<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Models\Via;
use App\Models\City;
use App\Util\Helper;
use App\Models\Order;
use App\Models\Client;
use App\Models\Status;
use App\Util\Validate;
use App\Util\Sanitizer;
use App\Traits\FileManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\ClothingType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{
    use FileManager;

    protected $clothingTypes = [];

    public function __construct()
    {
        $this->clothingTypes = ClothingType::where('is_hidden', 0)
            ->orderBy('order', 'asc')
            ->get();
    }

    public function json(Client $client, Order $order)
    {
        $this->authorize('view', [$order, $client->id]);

        $paths = [
            'art_paths',
            'size_paths',
            'payment_voucher_paths'
        ];

        $orderClothingTypes = $order->clothingTypes;
        $jsonOrder = $order->replicate();

        foreach ($orderClothingTypes as $type) {
            $jsonOrder['value_' . $type->key] = $type->pivot->value;
            $jsonOrder['quantity_' . $type->key] = $type->pivot->quantity;
        }

        foreach ($paths as $path) {
            if (! empty($order[$path])) {
                $files = [];

                foreach ($order->getPaths($path, true) as $index => $filepath) {
                    $files[] = 'data:'
                    . Storage::mimeType($filepath)
                    . ';base64,'
                    . base64_encode(Storage::get($filepath));
                }

                $jsonOrder[$path] = $files;
            }
        }

        return response()->json([
            'order' => $jsonOrder
        ], 200);
    }
    
    public function list(Request $request, Client $client)
    {
        $orders = Client::find($client->id)
            ->orders()
            ->whereNull('closed_at')
            ->orderBy('created_at', 'asc')
            ->get();
        
        $orders = $orders->filter(function ($order) {
            return $order->getTotalOwing() > 0;
        });

        return response()->json([
            'orders' => $orders
        ], 200);
    }

    public function index(Request $request)
    {
        $orders = $this->getRequestQuery($request);

        if (!$request->filled('ordem') && !$request->filled('codigo')) {
            $orders->whereNull('closed_at');
        }

        return view('orders.index', [
            'preRegisteredCount' => Order::preRegistered()->count(),
            'orders' => $orders->paginate(10)->appends($request->query()),
            'cities' => City::orderBy('name')->get(),
            'status' => Status::all()
        ]);
    }

    public function create(Client $client)
    {
        return view('orders.create', [
            'client' => $client,
            'vias' => Via::all()
        ]);
    }

    public function show(Client $client, Order $order)
    {
        $this->authorize('view', [$order, $client->id]);

        return view('orders.show', [
            'client' => $client,
            'order' => $order,
            'payments' => $order->payments()->orderBy('created_at', 'desc')->get(),
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
        $this->validator(
            $data = $this->getFormattedData($request->all())
        )->validate();

        $data = array_merge($data, $this->uploadAllBase64Files($data));
        
        $order = $client->orders()->create(
            Arr::except($data, $this->exceptKeysToStore())
        );

        $order->clothingTypes()->attach(
            $this->getFilledClothingTypes($data)
        );

        if (! empty($data['down_payment']) && ! empty($data['payment_via_id'])) {
            $order->createDownPayment(
                $data['down_payment'],
                $data['payment_via_id']
            );
        }

        return response()->json([
            'message' => 'success',
            'redirect' => $order->path()
        ], 200);
    }

    private function getFilledClothingTypes(array $data)
    {
        $filled = [];

        foreach ($this->clothingTypes as $type) {
            $quantity = $data['quantity_' . $type->key];
            $value = $data['value_' . $type->key];

            if (! empty($quantity) && ! empty($value)) {
                $filled[$type->id] = [
                    'quantity' => $quantity,
                    'value' => $value
                ];
            }
        }

        return $filled;
    }

    private function exceptKeysToStore()
    {
        $keys = [];

        foreach ($this->clothingTypes as $type) {
            $keys[] = 'quantity_' . $type->key;
            $keys[] = 'value_' . $type->key;
        }

        $keys[] = 'down_payment';
        $keys[] = 'payment_via_id';

        return $keys;
    }

    public function update(Client $client, Order $order, Request $request)
    {
        $this->authorize('update', [$order, $client->id]);

        $this->validator(
            $data = $this->getFormattedData($request->all(), true),
            $order
        )->validate();


        $this->deleteField($order, ['art_paths', 'size_paths', 'payment_voucher_paths']);

        $data = array_merge($data, $this->uploadAllBase64Files($data));

        $order->update(Arr::except($data, $this->exceptKeysToStore()));

        $order->clothingTypes()->sync(
            $this->getFilledClothingTypes($data)
        );

        return response()->json([
            'message' => 'success',
            'redirect' => $order->path()
        ], 200);
    }
    
    private function evaluateTotalQuantity(array $data)
    {
        $total = 0;

        foreach ($this->clothingTypes as $type) {
            if (! empty($data['value_' . $type->key])) {
                $total = bcadd($total, $data['quantity_' . $type->key]);
            }
        }

        return $total;
    }

    private function evaluateTotalValue(array $data)
    {
        $total = 0;

        foreach ($this->clothingTypes as $type) {
            if (! empty($data['quantity_' . $type->key])) {
                $mul = bcmul(
                    $data['quantity_' . $type->key],
                    $data['value_' . $type->key],
                    2
                );
    
                $total = bcadd($total, $mul, 2);
            }
        }

        return bcsub($total, $data['discount'] ?? 0, 2);
    }

    private function validator(array $data, $order = null)
    {
        $fields = [
            'name' => ['nullable', 'max:50'],
            'code' => [
                'required', $order
                    ? Rule::unique('orders')->ignore($data['code'], 'code')
                    : Rule::unique('orders')
            ],
            'discount' => ['nullable', 'numeric', 'lte:price'],
            'price' => [
                'required',
                'numeric',
                'min_double:' . ($order
                    ? $order->getTotalPayments()
                    : '0.01')
            ],
            'delivery_date' => ['nullable', 'date_format:Y-m-d'],
            'production_date' => ['nullable', 'date_format:Y-m-d'],
            'down_payment' => ['sometimes', 'max_double:price'],
            'payment_via_id' => ['sometimes', 'nullable', 'required_with:down_payment', 'exists:vias,id'],
        ];

        foreach ($this->clothingTypes as $type) {
            $fields['value_' . $type->key] = ['nullable', 'numeric', 'max:999999'];
            $fields['quantity_' . $type->key] = ['nullable', 'integer', 'max:9999'];
        }

        return Validator::make($data, $fields, $this->errorMessages());
    }

    private function errorMessages()
    {
        return [
            'discount.lte' => 'O desconto não pode ser maior que o preço total'
        ];
    }

    private function getFormattedData(array $data, $isUpdate = false)
    {
        $data['price'] = null;
        $data['quantity'] = null;

        if (empty($data['discount'])) {
            $data['discount'] = 0.00;
        }
        
        foreach ($data as $key => $field) {
            if (Str::contains($key, ['down_payment', 'value_', 'discount']) && ! empty($field)) {
                $data[$key] = Sanitizer::money($data[$key]);
            }

            if (Str::contains($key, ['delivery_date', 'production_date'])) {
                if (Validate::isDate($field)) {
                    $data[$key] = Carbon::createFromFormat(
                        'd/m/Y',
                        $data[$key]
                    )->toDateString();
                }
            }
            
            if (Str::contains($key, ['art_paths', 'size_paths', 'payment_voucher_paths'])) {
                foreach ($field as $index => $file) {
                    $base64 = $file['base64'];

                    if (! empty($base64)) {
                        $data[$key][$index] = $this->base64ToUploadedFile($base64);
                    }
                }
            }
        }

        $data['price'] = $this->evaluateTotalValue($data);
        $data['quantity'] = $this->evaluateTotalQuantity($data);

        return $data;
    }

    public function generateOrderPDF(Client $client, Order $order)
    {
        $this->authorize('view', [$order, $client->id]);

        $pdf = PDF::loadView('orders.pdf.order', compact('client', 'order'));

        return $pdf->stream('pedido-' . $order->code . '.pdf');
    }

    public function generateReport(Request $request)
    {
        if ($request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'cidade' => ['nullable', 'exists:cities,name'],
                'status' => 'nullable|exists:status,id',
                'data_de_fechamento' => 'nullable|date_format:d/m/Y'
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

        $pdf = PDF::loadView('orders.pdf.report', [
            'orders' => $orders->with('client')->get(),
            'request' => $request
        ]);


        return $pdf->stream('pedido.pdf');
    }

    public function getRequestQuery($request)
    {
        $orders = Order::query();

        if ($request->filled('cidade')) {
            $orders->whereHas('client.city', function ($query) use ($request) {
                $query->where('name', $request->cidade);
            });
        }

        if ($request->em_aberto == 'em_aberto') {
            $orders->whereNull('closed_at');
        }

        if ($request->filled('status') && Status::where('id', $request->status)->exists()) {
            $orders->where('status_id', $request->status);
        }

        if ($request->filled('codigo')) {
            $orders->where('code', 'like', '%' . $request->codigo . '%');
        }

        if ($request->filled('ordem')) {
            if ($request->ordem == 'mais_recente') {
                $orders->latest();
            }
        }

        if ($request->filled('ordem') && $request->ordem == 'data_de_entrega') {
            $orders->whereNull('closed_at');
            $orders->orderByRaw('CASE WHEN delivery_date IS NULL THEN 1 ELSE 0 END, delivery_date');
        }

        if ($request->filled('data_de_fechamento')) {
            $orders->whereDate(
                'closed_at',
                Carbon::createFromFormat('d/m/Y', $request->data_de_fechamento)->toDateString()
            );
        }

        if ($request->filled('data_de_entrega')) {
            $orders->whereDate(
                'delivery_date',
                Carbon::createFromFormat('d/m/Y', $request->data_de_entrega)->toDateString()
            );
        }

        if ($request->filled('filtro') && $request->filtro == 'pre-registro') {
            $orders->preRegistered();
        }

        return $orders;
    }

    public function generateReportProductionDate(Request $request)
    {
        if (Validate::isDate($date = $request->data_de_producao)) {
            $date = Carbon::createFromFormat('d/m/Y', $date);
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
            $orders->whereNull('closed_at');
        }

        $pdf = PDF::loadView('orders.pdf.report-production-date', [
            'orders' => $orders->get(),
            'date' => $date,
            'totalQuantity' => $orders->sum('quantity')
        ]);

        return $pdf->stream('pedido-por-data (' . Helper::date($date, '%d-%m-%Y') . ').pdf');
    }

    public function toggleOrder(Client $client, Order $order, Request $request)
    {
        $this->authorize('toggleOrder', [$order, $client->id]);

        if ($order->closed_at) {
            $order->closed_at = null;
        } else {
            $order->closed_at = Carbon::now()->toDateString();
        }

        $order->save();

        return redirect($order->path());
    }

    public function deleteFile(Client $client, Order $order, Request $request)
    {
        $this->authorize('view', [$order, $client->id]);

        if (Storage::delete($filepath = $this->getPathToDelete($request->filepath))) {
            $paths = json_decode($order->{$this->getField($filepath)});

            foreach ($paths as $key => $path) {
                if (Str::contains($filepath, $path)) {
                    unset($paths[$key]);
                }
            }

            $order->{$this->getField($filepath)} = !empty($paths) ? array_values($paths) : null;
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
                    'view' => view('orders.partials.file-viewer', [
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
}
