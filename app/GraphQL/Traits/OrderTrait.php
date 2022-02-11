<?php

namespace App\GraphQL\Traits;

use ErrorException;
use App\Util\Mask;
use App\Models\Role;
use App\Models\User;
use App\Models\Order;
use App\Models\Config;
use App\Util\Formatter;
use App\Util\FileHelper;
use App\Models\Commission;
use App\Models\ClothingType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use function Ramsey\Uuid\v1;

trait OrderTrait
{
    /**
     * Campos da tabela 'orders' que armazenam o nome dos arquivos.
     */
    protected $FILE_FIELDS = [
        'art_paths',
        'size_paths',
        'payment_voucher_paths'
    ];

    protected $clothingTypes;

    public function __construct()
    {
        $this->clothingTypes = ClothingType::where('is_hidden', 0)
            ->orderBy('order', 'asc')
            ->get();
    }

    private function getFormattedData(array $data, $order = null)
    {
        $data = (new Formatter($data))
            ->currencyBRL([
                'down_payment',
                'discount',
                'clothing_types.*.value'
            ])
            ->date([
                'delivery_date',
                'production_date'
            ])
            ->base64ToUploadedFile([
                'art_paths.*',
                'size_paths.*',
                'payment_voucher_paths.*'
            ])
            ->get();

        $data = $this->evaluateOrderAttributes($data, $order);

        return $data;
    }

    private function evaluateOrderAttributes($data, Order $order = null)
    {
        $price = $this->evaluatePrice($data, $order);
        $quantity = $this->evaluateQuantity($data, $order);

        if ($price) {
            $data['price'] = $price;
        }

        if ($quantity) {
            $data['quantity'] = $quantity;
        }

        return $data;
    }

    private function getOriginalPrice($data, Order $order = null)
    {
        if (isset($data['price']) && isset($data['discount'])) {
            return bcadd($data['price'], $data['discount'], 2);
        }

        if ($order) {
            return $order->original_price;
        }

        return null;
    }

    /**
     * Sempre que o preço for informado,
     * o pagamento de entrada não pode ser maior que ele.
     *
     */
    private function getDownPaymentRule($price)
    {
        return $price
            ? "max_currency:$price"
            : null;
    }

    /**
     * Caso $order seja null, está criando o pedido,
     * então retorna "required" rule para o attributo.
     *
     * @param \App\Models\Order $order
     * @return string
     */
    private function getRequiredRule(Order $order = null)
    {
        return $order ? 'nullable' : 'required';
    }

    /**
     * @param string|null $exceptId
     */
    private function getUniqueRule($exceptId)
    {
        return $exceptId
            ? Rule::unique('orders')->ignore($exceptId, 'id')
            : Rule::unique('orders');
    }

    private function rules($data, Order $order = null)
    {
        $originalPrice = $this->getOriginalPrice($data, $order);

        $rules = [
            'client_id' => [
                'sometimes',
                'required',
                'exists:clients,id'
            ],
            'name' => ['nullable', 'max:90'],
            'code' => [
                $this->getRequiredRule($order),
                $this->getUniqueRule($data['id'] ?? null)
            ],
            'discount' => [
                'sometimes',
                'nullable',
                'numeric',
                'gt:-0.01',
                "lt:$originalPrice",
            ],
            'price' => [
                $this->getRequiredRule($order),
                'numeric',
                'required_with:discount'
            ],
            'delivery_date' => ['nullable', 'date_format:Y-m-d'],
            'production_date' => ['nullable', 'date_format:Y-m-d'],
            'down_payment' => [
                'sometimes',
                $this->getDownPaymentRule($data['price'] ?? null)
            ],
            'status_id' => [
                'sometimes',
                'required',
                'exists:status,id'
            ],
            'payment_via_id' => [
                'nullable',
                'required_with:down_payment',
                'exists:vias,id'
            ],
            'art_paths.*' => ['nullable', 'file', 'max:1024'],
            'size_paths.*' => ['nullable', 'file', 'max:1024'],
            'payment_voucher_paths.*' => ['nullable', 'file', 'max:1024'],
            'clothing_types.*.value' => ['nullable', 'numeric', 'max:999999'],
            'clothing_types.*.quantity' => ['nullable', 'integer', 'max:9999'],
        ];

        if ($order) {
            $rules['price'][] = 'min_currency:' . $order->total_paid;
        }

        return $rules;
    }

    private function validator(array $data, Order $order = null)
    {
        $data = FileHelper::getOnlyUploadedFileInstances($data, $this->FILE_FIELDS);

        return Validator::make(
            $data,
            $this->rules($data, $order),
            $this->errorMessages($data)
        );
    }

    private function getFilledClothingTypes(array $data)
    {
        $filled = [];

        $uploadedClothingTypes = $data['clothing_types'];
        $clothingsSearch = array_column($uploadedClothingTypes, 'key');

        foreach ($this->clothingTypes as $type) {
            $index = array_search($type->key, $clothingsSearch);

            if ($index === false) {
                continue;
            }

            $currentType = $uploadedClothingTypes[$index];

            if (
                !empty($currentType['quantity'])
                && !empty($currentType['value'])
            ) {
                $filled[$type->id] = [
                    'quantity' => $currentType['quantity'],
                    'value' => $currentType['value']
                ];
            }
        }

        return $filled;
    }

    private function errorMessages($data)
    {
        $originalPrice = $this->getOriginalPrice($data);
        $price = isset($data['price']) ? $data['price'] : null;

        return [
            'art_paths.*.max.file' => __('general.validation.file_min', ['max' => '1MB']),
            'discount.lt' => __(
                'general.validation.orders.discount_lt',
                ['total_price' => Mask::money($originalPrice)]
            ),
            'discount.gt' => __('general.validation.orders.discount_gt'),
            'down_payment.max_currency' => __(
                'general.validation.orders.down_payment_max_currency',
                ['final_value' => Mask::money($price)]
            ),
            'size_paths.*.max' => __('general.validation.file_min', ['max' => '1MB']),
            'payment_via_id.required_with' => __('general.validation.orders.payment_via_id_required_with'),
            'payment_voucher_paths.*.max' => __('general.validation.file_min', ['max' => '1MB']),
            'price.min_currency' => __('general.validation.orders.price_min_currency'),
            'price.required' => __('general.validation.orders.price_required'),
        ];
    }

    private function evaluateClothingTypesQuantity($data)
    {
        $INITIAL_VALUE = 0;
        $clothingTypes = [];

        if (!isset($data['clothing_types'])) {
            return null;
        }

        $clothingTypes = collect($data['clothing_types']);

        return $clothingTypes->reduce(
            function ($total, $type) {
                $value = $type["value"];
                $quantity = $type["quantity"];

                if (!empty($value)) {
                    return bcadd($total, $quantity);
                }

                return $total;
            },
            $INITIAL_VALUE
        );
    }

    /**
     * Calcula a quantidade total dos tipos de roupas informados.
     *
     * @param array $data
     * @return int|null
     */
    private function evaluateQuantity(array $data, Order $order = null)
    {

        $total = $this->evaluateClothingTypesQuantity($data);

        if ($total === 0 && $order && $order->isPreRegistered()) {
            return null;
        }

        return $total;
    }

    /**
     * Calcula o valor total dos tipos de roupas informados.
     *
     * @param array $data
     * @return float|null
     */
    private function evaluateClothingTypesValue($data)
    {
        $INITIAL_VALUE = 0;
        $clothingTypes = [];

        if (!isset($data['clothing_types'])) {
            return null;
        }

        $clothingTypes = collect($data['clothing_types']);

        return $clothingTypes->reduce(
            function ($total, $type) {
                $value = $type["value"];
                $quantity = $type["quantity"];

                if (!empty($quantity)) {
                    $typeTotal = bcmul($quantity, $value, 2);

                    return bcadd($total, $typeTotal, 2);
                }

                return $total;
            },
            $INITIAL_VALUE
        );
    }

    /**
     * Calcula o valor total do produto cadastrado.
     *
     * @param float $clothingTypesValue
     * @param array $data
     * @param \App\Models\Order|null $order
     * @return float|null
     */
    private function evaluateTotalPrice($clothingTypesValue, $data, $order)
    {
        if (
            $clothingTypesValue <= 0
            && isset($data['discount'])
            && $order
        ) {
            return bcsub(
                $order->original_price,
                $data['discount'],
                2
            );
        }

        if ($order && floatval($clothingTypesValue) === 0.0) {
            return null;
        }

        return bcsub($clothingTypesValue, $data['discount'], 2);
    }

    /**
     * Calcula o preço final do produto cadastrado.
     *
     * @param array $data,
     * @param App\Models\Order|null $order
     * @return float|null
     */
    private function evaluatePrice(array $data, Order $order = null)
    {
        $clothingTypesValue = $this->evaluateClothingTypesValue($data);

        $total = $this->evaluateTotalPrice($clothingTypesValue, $data, $order);

        if (floatval($total) === 0.0 && $order) {
            return $order->original_price;
        }

        return $total;
    }

    /**
     * Faz o upload dos arquivos e retorna o nome
     * em um array associativo com o nome dos arquivos
     * upados em json
     *
     * @param array $data Dados enviados do formulários
     * @param \App\Models\Order $order
     *
     * @return array
     */
    public function handleFilesUpload(array $data, Order $order = null)
    {
        foreach ($this->FILE_FIELDS as $field) {
            if ($order) {
                $storedFiles = FileHelper::getFilesFromField($order[$field]);

                if (isset($data[$field])) {
                    $this->deleteRemovedFiles(
                        $storedFiles,
                        $data[$field],
                        $field
                    );
                }
            }


            if (isset($data[$field])) {
                $storedFiles = $this->storeFiles($data[$field], $field, $order);

                $data[$field] = $storedFiles;
            }
        }

        return $data;
    }

    public function canUploadFileToField($file, $field, $order = null)
    {
        if ($order) {
            $orderFields = array_map(
                fn ($file) => FileHelper::getFilenameFromUrl($file),
                $order->{$field}
            );

            return !in_array($file, $orderFields) && !empty($file);
        }

        return !empty($file);
    }

    public function storeFiles($files, $field, $order = null)
    {
        $paths = [];

        foreach ($files as $key => $file) {
            if ($this->canUploadFileToField($file, $field, $order)) {
                $paths[] = FileHelper::uploadFileToField($file, $field, $key);
                continue;
            }

            $paths[] = $file;
        }

        return json_encode($paths);
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
}