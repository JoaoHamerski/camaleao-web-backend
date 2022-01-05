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
use App\Util\FileHelper;
use Illuminate\Support\Arr;
use App\Models\ClothingType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade as PDF;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Queries\OrdersRequest;

class OrdersController extends Controller
{
    protected $clothingTypes = [];

    /**
     * Campos da tabela 'orders' que armazenam o nome dos arquivos.
     */
    protected $FILE_FIELDS = [
        'art_paths',
        'size_paths',
        'payment_voucher_paths'
    ];

    public function __construct()
    {
        $this->clothingTypes = ClothingType::where('is_hidden', 0)
            ->orderBy('order', 'asc')
            ->get();
    }

    public function index(Request $request)
    {
        $orders = OrdersRequest::query($request);

        return OrderResource::collection($orders->paginate(10));
    }

    public function show(Client $client = null, Order $order)
    {
        if ($client) {
            $this->authorize('view', [$order, $client->id]);
        }

        return new OrderResource($order);
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

    public function destroy(Client $client = null, Order $order)
    {
        if ($client) {
            $this->authorize('view', [$order, $client->id]);
        }

        $order->delete();

        return response('', 200);
    }

    public function store(Client $client, Request $request)
    {
        $data = $this->getFormattedData($request->all());

        $this->validator($data)->validate();

        $data = $this->uploadFiles($data);

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
        $data = $this->getFormattedData($request->all());

        $this->validator($data, $order)->validate();

        $data = $this->uploadFiles($data, $order);

        $order->update(Arr::except($data, $this->exceptKeysToStore()));

        $order->clothingTypes()->sync(
            $this->getFilledClothingTypes($data)
        );

        if (!$order->isPreRegistered()) {
            $this->storeCommissions($order, true);
        }

        return response('', 200);
    }

    public function deleteRemovedFiles(array $storedFiles, array $uploadedFiles, $field)
    {
        $uploadedFiles = array_map(
            fn ($file) => FileHelper::isBase64($file)
                ? $file
                : FileHelper::getFilename($file),
            $uploadedFiles
        );

        $removedFiles = array_diff($storedFiles, $uploadedFiles);

        FileHelper::deleteFiles($removedFiles, $field);
    }

    /**
     * Faz o upload dos arquivos e retorna o nome
     * em um array associativo com o nome dos arquivos
     * upados em json
     *
     * @param array $data Dados enviados do formulários
     * @param App\Models\Order $order
     *
     * @return array
     */
    public function uploadFiles(array $data, Order $order = null)
    {
        foreach ($this->FILE_FIELDS as $field) {
            if ($order) {
                $storedFiles = json_decode($order[$field]) ?? [];

                $this->deleteRemovedFiles(
                    $storedFiles,
                    $data[$field],
                    $field
                );
            }

            $files = FileHelper::uploadFilesToField($data[$field], $field);

            $data[$field] = json_encode($files);
        }

        return $data;
    }

    private function validator(array $data, $order = null)
    {
        $data = FileHelper::getOnlyUploadedFileInstances($data, $this->FILE_FIELDS);

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
                'gt:-0.01',
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
            $fields['price'][] = 'min_currency:' . $order->getTotalPaid();
        }

        return Validator::make($data, $fields, $this->errorMessages($data));
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

        $keys = array_merge($keys, [
            'down_payment',
            'payment_via_id',
            'client'
        ]);

        return $keys;
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

        if (!strlen($data['discount'])) {
            unset($data['discount']);
        }

        if (isset($data['client'])) {
            $data['client_id'] = $data['client']['id'];
        }

        $data['price'] = $this->evaluateTotalValue($data);
        $data['quantity'] = $this->evaluateTotalQuantity($data);

        return $data;
    }

    public function generateOrderReport(Client $client, Order $order)
    {
        $this->authorize('view', [$order, $client->id]);

        $pdf = PDF::loadView('pdf.order', compact('client', 'order'));

        return $pdf->stream('pedido-' . $order->code . '.pdf');
    }

    public function generateGeneralOrderReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city' => ['nullable', 'exists:cities,id'],
            'status' => ['nullable', 'exists:status,id'],
            'closing_date' => ['nullable', 'date_format:d/m/Y'],
            'delivery_date' => ['nullable', 'date_format:d/m/Y']
        ]);

        if ($validator->fails()) {
            abort(422);
        }

        $orders = OrdersRequest::query($request, null, true);

        $data = [
            'city' => $request->filled('city') ? City::find($request->city) : null,
            'status' => $request->filled('status') ? Status::find($request->status) : null,
            'delivery_date' => $request->delivery_date,
            'closing_date' => $request->closing_date,
        ];

        $pdf = PDF::loadView('pdf.orders-general', [
            'orders' => OrderResource::collection($orders->get()),
            'data' => $data
        ]);

        return $pdf->stream('relatorio-de-pedidos.pdf');
    }

    public function generateReportProductionDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'production_date' => ['required', 'date_format:d/m/Y']
        ]);

        if ($validator->fails()) {
            abort(422);
        }

        $orders = OrdersRequest::query($request, null, true);

        $data = [
            'total_quantity' => $orders->sum('quantity'),
            'date' => $request->production_date
        ];

        $pdf = PDF::loadView('pdf.orders-production-date', [
            'orders' => OrderResource::collection($orders->get()),
            'data' => $data
        ]);

        return $pdf->stream(
            'pedido-por-data (' . Helper::date($data['date'], '%d-%m-%Y') . ').pdf'
        );
    }

    public function toggleOrder(Client $client, Order $order, Request $request)
    {
        $this->authorize('toggleOrder', [$order, $client->id]);

        $order->update([
            'closed_at' => !$order->closed_at
                ? Carbon::now()->toDateString()
                : null
        ]);

        return response('', 200);
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
