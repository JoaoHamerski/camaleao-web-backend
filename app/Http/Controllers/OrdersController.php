<?php

namespace App\Http\Controllers;

use App\Util\Mask;
use \Carbon\Carbon;
use App\Models\Via;
use ErrorException;
use App\Models\City;
use App\Models\Role;
use App\Models\User;
use App\Util\Helper;
use App\Models\Order;
use App\Models\Client;
use App\Models\Config;
use App\Models\Status;
use App\Util\Validate;
use App\Util\Formatter;
use App\Models\Commission;
use App\Traits\FileManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\ClothingType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PaymentResource;
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

    public function json(Client $client = null, Order $order)
    {
        if ($client) {
            $this->authorize('view', [$order, $client->id]);
        }

        $paths = [
            'art_paths',
            'size_paths',
            'payment_voucher_paths'
        ];

        $orderClothingTypes = $order->clothingTypes;
        $jsonOrder = $order;

        foreach ($orderClothingTypes as $type) {
            $jsonOrder['value_' . $type->key] = $type->pivot->value;
            $jsonOrder['quantity_' . $type->key] = $type->pivot->quantity;
        }

        foreach ($paths as $path) {
            if (!empty($order[$path])) {
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

    public function isCommissionConfirmed(User $user, $commissionId)
    {
        $commission = $user->commissions()->find($commissionId);

        if (!$commission) {
            return null;
        }

        return !!$commission->pivot->confirmed_at;
    }

    public function storeUserCommissions(Commission $commission, $wasQuantityChanged = false)
    {
        $users = User::production()->get();

        foreach ($users as $user) {
            $data = [];
            $commissionWithPivot = $user->commissions()->find($commission->id);

            if (!$commissionWithPivot) {
                $data['commission_value'] = $commission->getUserCommission($user);
                $data['role_id'] = $user->role_id;
            } else {
                $data['commission_value'] = Role::find($commissionWithPivot->pivot->role_id)->name == 'Costura'
                    ? $commission->getSeamTotalCommission()
                    : $commission->getPrintTotalCommission();
            }

            if ($this->isCommissionConfirmed($user, $commission->id) && $wasQuantityChanged) {
                $data['confirmed_at'] = null;
                $data['was_quantity_changed'] = true;
            }

            $user->commissions()->syncWithoutDetaching([
                $commission->id => $data,
            ]);
        }
    }

    public function storeCommissions(Order $order, $isUpdate = false)
    {
        $data = [
            'print_commission' => Config::get('app', 'order_commission'),
            'seam_commission' => $order->getCommissions()->toJson()
        ];

        $isQuantityChanged = false;

        if (!$isUpdate) {
            $commission = $order->commissions()->create($data);
        } else {
            if (!$order->commission) {
                $commission = $order->commissions()->create($data);
            } else {
                $commission = Commission::where('order_id', $order->id)->first();
                $commission->update($data);
            }

            try {
                $isQuantityChanged = $order->isQuantityChanged();
            } catch (ErrorException $error) {
                $isQuantityChanged = false;
            }
        }


        $this->storeUserCommissions(
            $commission->fresh(),
            $isQuantityChanged
        );
    }

    public function show(Client $client = null, Order $order)
    {
        return new OrderResource($order);
    }

    public function edit(Client $client = null, Order $order)
    {
        if ($client) {
            $this->authorize('update', [$order, $client->id]);
        }

        return view('orders.edit', [
            'client' => $client,
            'order' => $order
        ]);
    }

    public function destroy(Client $client = null, Order $order)
    {
        if ($client) {
            $this->authorize('view', [$order, $client->id]);
        }

        $order->delete();

        return response()->json([
            'message' => 'success',
            'redirect' => $client ? $client->path() : route('orders.index')
        ], 200);
    }

    public function store(Client $client, Request $request)
    {
        $data = $this->getFormattedData($request->all());
        $this->validator($data)->validate();

        $data = array_merge($data, $this->uploadAllBase64Files($data));

        $order = $client->orders()->create(
            Arr::except($data, $this->exceptKeysToStore())
        );

        $order->clothingTypes()->attach(
            $this->getFilledClothingTypes($data)
        );

        if (!$order->isPreRegistered()) {
            $this->storeCommissions($order);
        }

        if (!empty($data['down_payment']) && !empty($data['payment_via_id'])) {
            $order->createDownPayment(
                $data['down_payment'],
                $data['payment_via_id']
            );
        }

        return response('', 200);
    }

    public function update(Client $client = null, Order $order, Request $request)
    {
        if ($client) {
            $this->authorize('update', [$order, $client->id]);
        }

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

        if (!$order->isPreRegistered()) {
            $this->storeCommissions($order, true);
        }

        return response()->json([
            'message' => 'success',
            'redirect' => $order->fresh()->path()
        ], 200);
    }

    private function getFilledClothingTypes(array $data)
    {
        $filled = [];

        foreach ($this->clothingTypes as $type) {
            $quantity = $data['quantity_' . $type->key];
            $value = $data['value_' . $type->key];

            if (!empty($quantity) && !empty($value)) {
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
        $keys[] = 'client';

        return $keys;
    }

    public function getOrderCommission()
    {
        return response()->json([
            'commission' => Config::get('app', 'order_commission')
        ], 200);
    }

    public function changeOrderCommission(Request $request)
    {
        if ($request->filled('value')) {
            $data['value'] = Formatter::parseCurrencyBRL($request->value);
        }

        Validator::make($data, [
            'value' => ['required', 'numeric']
        ])->validate();

        Config::set('app', 'order_commission', $data['value']);

        return response()->json([], 204);
    }

    private function evaluateTotalQuantity(array $data)
    {
        $INITIAL_VALUE = 0;

        $total = $this->clothingTypes->reduce(
            function ($total, $type) use ($data) {
                $value = $data["value_$type->key"];
                $quantity = $data["quantity_$type->key"];

                if (!empty($value)) {
                    return bcadd($total, $quantity);
                }

                return $total;
            },
            $INITIAL_VALUE
        );


        return $total;
    }

    private function evaluateTotalValue(array $data)
    {
        $INITIAL_VALUE = 0;

        $total = $this->clothingTypes->reduce(
            function ($total, $type) use ($data) {
                $quantity = $data["quantity_$type->key"];
                $value = $data["value_$type->key"];

                if (!empty($quantity)) {
                    $typeTotal = bcmul($quantity, $value, 2);

                    return bcadd($total, $typeTotal, 2);
                }

                return $total;
            },
            $INITIAL_VALUE
        );

        return bcsub($total, $data['discount'] ?? 0, 2);
    }

    private function validator(array $data, $order = null)
    {
        $totalPrice = bcadd($data['price'], $data['discount'] ?? 0, 2);

        $fields = [
            'name' => ['nullable', 'max:50'],
            'code' => [
                'required', $order
                    ? Rule::unique('orders')->ignore($data['code'], 'code')
                    : Rule::unique('orders')
            ],
            'discount' => [
                'sometimes',
                'nullable',
                'numeric',
                'gt:0.00',
                "lt:$totalPrice",
            ],
            'price' => ['required', 'numeric'],
            'delivery_date' => ['nullable', 'date_format:Y-m-d'],
            'production_date' => ['nullable', 'date_format:Y-m-d'],
            'down_payment' => ['sometimes', 'max_currency:' . $data['price']],
            'payment_via_id' => [
                'sometimes',
                'nullable',
                'required_with:down_payment',
                'exists:vias,id'
            ],
            'art_paths.*' => ['file', 'max:1024'],
            'size_paths.*' => ['file', 'max:1024'],
            'payment_voucher_paths.*' => ['file', 'max:1024'],
        ];

        foreach ($this->clothingTypes as $type) {
            $fields['value_' . $type->key] = ['nullable', 'numeric', 'max:999999'];
            $fields['quantity_' . $type->key] = ['nullable', 'integer', 'max:9999'];
        }

        if ($order && $order->client === null) {
            $fields['client_id'] = ['required', 'exists:clients,id'];
        }

        if (!$order && $data['price'] == 0) {
            $data['price'] = '';
        }

        if ($order) {
            $fields['price'][] = 'min_currency:' . $order->getTotalPayments();
        }

        return Validator::make($data, $fields, $this->errorMessages($data));
    }

    private function errorMessages($data)
    {
        $totalPrice = bcadd($data['price'], $data['discount'] ?? 0, 2);

        return [
            'art_paths.*.max.file' => __('general.validation.file_min', ['max' => '1MB']),
            'discount.lt' => __(
                'general.validation.orders.discount_lt',
                ['total_price' => Mask::money($totalPrice)]
            ),
            'discount.gt' => __('general.validation.orders.discount_gt'),
            'down_payment.max_currency' => __(
                'general.validation.orders.down_payment_max_currency',
                ['final_value' => Mask::money($data['price'])]
            ),
            'size_paths.*.max' => __('general.validation.file_min', ['max' => '1MB']),
            'payment_via_id.required_with' => __('general.validation.orders.payment_via_id_required_with'),
            'payment_voucher_paths.*.max' => __('general.validation.file_min', ['max' => '1MB']),
            'price.min_currency' => __('general.validation.orders.price_min_currency'),
            'price.required' => __('general.validation.orders.price_required'),
        ];
    }

    private function getFormattedData(array $data)
    {
        $data['price'] = null;
        $data['quantity'] = null;

        $data = Formatter::parse($data, [
            'parseCurrencyBRL' => [
                'down_payment',
                'value_',
                'discount'
            ],
            'parseDate' => [
                'delivery_date',
                'production_date'
            ],
            'base64toUploadedFile' => [
                'art_paths',
                'size_paths',
                'payment_voucher_paths'
            ]
        ]);

        if (strlen($data['discount']) === 0) {
            unset($data['discount']);
        }

        if (isset($data['client'])) {
            $data['client_id'] = $data['client']['id'];
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

        $orders = $this->getRequestQuery($request, true);

        $pdf = PDF::loadView('orders.pdf.report', [
            'orders' => $orders->with('client')->get(),
            'request' => $request
        ]);


        return $pdf->stream('pedido.pdf');
    }

    public function getRequestQuery($request, $isPDF = false)
    {
        $orders = Order::query();

        if ($isPDF) {
            $orders = Order::whereNotNull('quantity');
            $orders = Order::whereNotNull('client_id');
        }

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

        $orders = Order::preRegistered()->where('production_date', $date->toDateString());

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

    public function showFile(Client $client = null, Order $order, Request $request)
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
