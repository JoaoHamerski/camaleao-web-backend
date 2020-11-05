<?php

namespace App\Http\Controllers;

use App\Util\Helper;
use App\Models\Order;
use App\Models\Status;
use App\Util\Validate;
use App\Models\Client;
use App\Util\Sanitizer;
use App\Models\Via;
use App\Traits\FileManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{
    use FileManager;

    public function index(Request $request)
    {
        $orders = $this->getRequestQuery($request);

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
            'status' => Status::all(),
            'vias' => Via::all()
        ]);
    }

    public function edit(Client $client, Order $order) 
    {
        $this->authorize('update', [$order, $client->id]);

        return view('orders.edit', compact('client', 'order'));
    }

    public function destroy(Client $client, Order $order)
    {
        $this->authorize('view', [$order, $client->id]);

        $order->delete();

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

        $order = $client->orders()->create(\Arr::except($data, ['down_payment']));

        if (! empty($data['down_payment'])) {
            $order->createDownPayment($data['down_payment']);
        }

        return response()->json([
            'message' => 'success',
            'redirect' => $order->path()
        ], 200);
    }

    public function patch(Client $client, Order $order, Request $request)
    {  
        $this->authorize('update', [$order, $client->id]);

        $validator = $this->validator(
            $data = $this->getFormattedData($request->all()),
            true
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
            'code' => $data['code'],
            'name' => $data['name'],
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
                'cidade' => ['nullable', Rule::in($cities)],
                'status' => 'nullable|exists:status,id'
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

        $orders = $this->getRequestQuery($request);

        $pdf = \PDF::loadView('orders.pdf.report', [
            'orders' => $orders->with('client')->get(),
            'city' => $city ?? null,
            'status' => Status::find($request->status) ?? null,
            'only_open' => $request->only_open
        ]);

        $filename = 'Pedidos';
        $filename .= isset($city) ? " - $city" : '';
        $filename .= isset($status) ? " - $status->text" : '';

        return $pdf->stream($filename . '.pdf');
    }

    public function getRequestQuery($request) 
    {
        $orders = Order::query();

        if (($city = $request->cidade) != null) {
            $orders->whereHas('client', function ($query) use ($city) {
                $query->where('city', $city);
            });
        }

        if ($request->em_aberto == 'em_aberto') {
            $orders->where('is_closed', '0');
        }

        if ($request->status != null && Status::where('id', $request->status)->exists()) {
            $orders->where('status_id', $request->status);
        }

        if ($request->has('codigo') && ! empty($request->codigo)) {
            $orders->where('code', 'like', '%' . $request->codigo . '%');
        }

        if ($request->has('ordem')) {
            if ($request->ordem == 'mais_recente') 
                $orders->latest();
        }

        return $orders;
    }

    public function generateReportProductionDate(Request $request)
    {
        if (Validate::isDate($date = $request->data_de_producao)) {
            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $date);
        }       

        if ($request->wantsJson()) {
            $validator = Validator::make(['data_de_producao' => $date], [
                'data_de_producao' => 'required|date'
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

        if ($request->has('em_aberto') && $request->em_aberto == 'em_aberto') {
            $orders->where('is_closed', '0');
        }

        $pdf = \PDF::loadView('orders.pdf.report-production-date', [
            'orders' => $orders->get(),
            'date' => $date,
            'totalQuantity' => $orders->sum('quantity')
        ]);

        return $pdf->stream('pedido-por-data('. Helper::date($date, '%d-%m-%Y') . ').pdf');
    }

    public function toggleOrder(Client $client, Order $order, Request $request)
    {
        $this->authorize('view', [$order, $client->id]);
        
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
        
        if (\Storage::delete($filepath = $this->getPathToDelete($request->filepath))) {
            $paths = json_decode($order->{$this->getField($filepath)});

            foreach ($paths as $key => $path) {
                if (\Str::contains($filepath, $path))
                    unset($paths[$key]);
            }

            $order->{$this->getField($filepath)} = ! empty($paths) ? array_values($paths) : null;
            $order->save();

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
                        'paths' => $order->getPaths($option . '_paths'),
                        'option' => $option
                    ])->render()
                ], 200);
            }
        }

        return response()->json([
            'message' => 'error'
        ], 422);
    }

    private function validator(array $data, $isUpdate = false) 
    {
        return Validator::make($data, [
            'name' => 'nullable|max:255',
            'code' => [
                'required', $isUpdate 
                ? Rule::unique('orders')->ignore($data['code'], 'code')
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
