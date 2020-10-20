<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Status;
use App\Util\Validate;
use App\Models\Client;
use App\Util\Sanitizer;
use App\Util\Helper;
use App\Traits\FileManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{
    use FileManager;

    public function index(Request $request)
    {
        $orders = Order::query();

        if ($request->has('codigo') && ! empty($request->codigo)) {
            $orders->where('code', 'like', '%' . $request->codigo . '%');
        }

        return view('orders.index', [
            'orders' => $orders->latest()->paginate(10)->appends($request->query()),
            'cities' => Client::all()->pluck('city')->unique()->sort(),
            'status' => Status::all()
        ]);
    }

    public function create(Client $client) 
    {
    	return view('orders.create', compact('client'));
    }

    public function show(Client $client, Order $order) 
    {
        $this->authorize('view', [$order, $client->id]);

        return view('orders.show', [
            'client' => $client,
            'order' => $order,
            'status' => Status::all()
        ]);
    }

    public function edit(Client $client, Order $order) 
    {
        $this->authorize('view', [$order, $client->id]);

        if ($order->is_closed)
            return abort(403);

        if (! $client->orders()->where('id', $order->id)->exists()) {
            abort(403);
        }
        
        return view('orders.edit', compact('client', 'order'));
    }

    public function destroy(Client $client, Order $order)
    {
        $order->delete();

        $this->deleteFiles($order, [
            'art_paths', 'size_paths', 'payment_voucher_paths'
        ]);

        return response()->json([
            'message' => 'success',
            'redirect' => $client->path()
        ], 200);
    }

    public function store(Client $client, Request $request) 
    {
        $validator = $this->validator(
            $data = $this->getFormattedData($request->all())
        );

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'errors' => $validator->errors()
             ], 422);
        }

        $data = array_merge($data, $this->uploadAllFiles($request));
        $data['status_id'] = Status::first()->id;

        $order = $client->orders()->create(\Arr::except($data, ['down_payment']));

        if (! empty($data['down_payment'])) {
            $order->payments()->create([
                'value' => $data['down_payment'],
                'date' => \Carbon\Carbon::now(),
                'note' => 'Pagamento de entrada'
            ]);
        }

        return response()->json([
            'message' => 'success',
            'redirect' => $order->path()
        ], 200);
    }

    public function patch(Client $client, Order $order, Request $request)
    {  
        $this->authorize('view', [$order, $client->id]);

        $validator = $this->validator(
            $data = $this->getFormattedData($request->all()),
            $request->code
        );

        if ($validator->fails()) {  
            return response()->json([
                'message' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        foreach(['art_paths', 'size_paths', 'payment_voucher_paths'] as $field) { 
            if ($request->hasFile($field)) {
                $order->{$field} = $this->appendOrInsertFiles(
                    $request->only([$field]), 
                    $field, 
                    $order
                );
            }
        }

        $order->save();

        $order->update([
            'quantity' => $data['quantity'],
            'price' => $data['price'],
            'delivery_date' => $data['delivery_date'],
            'production_date' => $data['production_date']
        ]);

        return response()->json([
            'message' => 'success',
            'redirect' => $order->path()
        ], 200);
    }

    public function generateOrderPDF(Client $client, Order $order)
    {
        $this->authorize('view', [$order, $client->id]);

        $pdf = \PDF::loadView('orders.pdf.order', compact('client', 'order'));

        return $pdf->stream('pedido(' . $order->code . ').pdf');
    }

    public function generateReport(Request $request)
    {   
        if ($request->wantsJson()) {
            $cities = Client::all()->pluck('city')->unique()->all();

            $validator = Validator::make($request->all(), [ 
                'city' => ['nullable', Rule::in($cities)] 
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            return response()->json([
                'message' => 'success'
            ], 200);
        }

        $orders = Order::query();

        if ($request->city != null) {
            $city = $request->city;
            $orders->whereHas('client', function ($query) use ($request) {
                $query->where('city', $request->city);
            });
        }

        if ($request->only_open == 'only_open') {
            $orders->where('is_closed', '0');
        }

        if ($request->status != null && Status::where('id', $request->status)->exists()) {
            $status = Status::find($request->status);
            $orders->where('status_id', $request->status);
        }

        $pdf = \PDF::loadView('orders.pdf.report', [
            'orders' => $orders->with('client')->latest()->get(),
            'city' => $city ?? null,
            'status' => $status ?? null,
            'only_open' => $request->only_open
        ]);

        $filename = 'Pedidos';
        $filename .= isset($city) ? " - $city" : '';
        $filename .= isset($status) ? " - $status->text" : '';

        return $pdf->stream($filename . '.pdf');
    }

    public function generateReportProductionDate(Request $request)
    {
        $date = $request->date;

        if (Validate::isDate($date)) {
            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date);
        }       

        if ($request->wantsJson()) {
            $validator = Validator::make(['date' => $date], [
                'date' => 'required|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            return response()->json([
                'message' => 'success'
            ], 200);
        }

        $orders = Order::where('production_date', $date->toDateString());

        $pdf = \PDF::loadView('orders.pdf.report-production-date', [
            'orders' => $orders->get(),
            'date' => $date,
            'totalQuantity' => $orders->sum('quantity')
        ]);

        return $pdf->stream('pedido-por-data('. Helper::date($date, '%d-%m-%Y') . ').pdf');
    }

    public function toggleOrder(Client $client, Order $order, Request $request)
    {
        if ($order->is_closed) 
            $order->is_closed = 0;
        else 
            $order->is_closed = 1;

        $order->save();

        return redirect($order->path());
    }  

    public function deleteFile(Client $client, Order $order, Request $request)
    {
        $this->authorize('view', [$order, $client->id]);
        
        $filepath = $this->getPathToDelete($request->filepath); 

        if (\Storage::delete($filepath)) {
            foreach(['art_paths', 'size_paths', 'payment_voucher_paths'] as $field) {
                if (\Str::contains($filepath, $this->getFilepath($field, true))) {
                    $paths = json_decode($order->{$field});

                    foreach ($paths as $key => $path) {
                        if (\Str::contains($filepath, $path))
                            unset($paths[$key]);
                    }

                    $order->{$field} = ! empty($paths) ? array_values($paths) : null;
                    $order->save();
                }
            }

            return response()->json([
                'message' => 'success'
            ], 200);
        }

        return response()->json([
            'message' => 'error'
        ], 422);
    }

    public function showFile(Client $client, Order $order, Request $request) 
    {
        foreach (['art', 'size', 'payment_voucher'] as $option) {
            if ($request->option == $option) {
                return response()->json([
                    'message' => 'success',
                    'view' => view('orders.file-viewer', [
                        'paths' => $order->getPaths($option . '_paths')
                    ])->render()
                ], 200);
            }
        }

        return response()->json([
            'message' => 'error'
        ], 422);
    }

    private function validator(array $data, $codeExcept = null) 
    {
        return Validator::make($data, [
            'code' => [
                'required', $codeExcept 
                ? Rule::unique('orders')->ignore($codeExcept, 'code')
                : Rule::unique('orders')
            ],
            'quantity' => 'required',
            'price' => 'required',
            'delivery_date' => 'nullable|date_format:Y-m-d',
            'production_date' => 'nullable|date_format:Y-m-d',
            'down_payment' => 'sometimes|max_double:' . $data['price'],
            'art_paths.*' => 'nullable|image',
            'size_paths.*' => 'nullable|image'
        ]);
    }

    private function getFormattedData(array $data) 
    {
        foreach ($data as $key => $field) {
            if (\Str::contains($key, ['price', 'down_payment']) && ! empty($field))
                $data[$key] = Sanitizer::money($data[$key]);

            if (\Str::contains($key, ['delivery_date', 'production_date'])) {
                if (Validate::isDate($field)) {
                    $data[$key] = \Carbon\Carbon::createFromFormat('d/m/Y', $data[$key])->toDateString();
                }
            }
        }

        return $data;
    }
}
