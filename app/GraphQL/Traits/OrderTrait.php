<?php

namespace App\GraphQL\Traits;

use App\Util\Mask;
use App\Models\Order;
use App\Util\Formatter;
use App\Util\FileHelper;
use App\Models\ClothingType;
use App\Models\GarmentMatch;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

trait OrderTrait
{
    use OrderLegacyTrait;

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

    public function registerProducts($input, $order, $isUpdate = false)
    {
        $products = collect($input['product_items']);

        if ($isUpdate) {
            $order->products()->delete();
        }

        $products->each(function ($product) use ($order) {
            $order->products()
                ->create([
                    'description' => $product['description'],
                    'value' => $product['value'],
                    'quantity' => $product['quantity'],
                    'unity' => $product['unity']
                ]);
        });
    }

    private function getFormattedData(array $data, $order = null)
    {
        $data = (new Formatter($data))
            ->currencyBRL([
                'down_payment',
                'discount',
                'product_items.*.value',
                'shipping_value'
            ])
            ->date([
                'delivery_date',
            ])
            ->base64ToUploadedFile([
                'art_paths.*',
                'size_paths.*',
                'payment_voucher_paths.*'
            ])->get();

        return $data;
    }

    private function evaluateOrderAttributes($data, Order $order = null)
    {
        $price = $this->evaluateProductsValue($data, $order);
        $quantity = $this->evaluateProductsQuantity($data, $order);

        if ($price) {
            $data['price'] = $price;
        }

        if ($quantity) {
            $data['quantity'] = $quantity;
        }

        return $data;
    }

    private function getProductValue($product)
    {
        return bcmul($product['value'], $product['quantity'], 2);
    }

    private function getProductsValue($products)
    {
        return collect($products)->reduce(
            fn ($total, $product) => bcadd(
                $total,
                $this->getProductValue($product),
                2
            ),
            0
        );
    }

    private function evaluateTotalPrice($productsValue, $data, $order = null)
    {
        if (
            $productsValue <= 0
            && isset($data['discount'])
            && $order
        ) {
            return bcsub(
                $order->original_price,
                $data['discount'],
                2
            );
        }

        if ($order && floatval($productsValue) === 0.0) {
            return null;
        }

        $price = bcsub($productsValue, $data['discount'] ?? 0, 2);

        return bcadd($price, $data['shipping_value'] ?? 0, 2);
    }

    private function evaluateProductsValue($data, $order = null)
    {
        $productsValue = $this->getProductsValue($data['product_items']);
        $total = $this->evaluateTotalPrice($productsValue, $data, $order);

        if (floatval($total) === 0.0 && $order && $order->isPreRegistered()) {
            return null;
        }

        if (floatval($total) === 0.0 && $order) {
            return $order->original_price;
        }

        if (floatval($total) === 0.0) {
            return null;
        }

        return $total;
    }

    private function getProductsQuantity($products)
    {
        return collect($products)->reduce(
            fn ($total, $product) => bcadd(
                $total,
                $product['quantity']
            ),
            0
        );
    }

    private function evaluateProductsQuantity($data, Order $order = null)
    {
        $total = $this->getProductsQuantity($data['product_items']);

        if ($total === 0 && $order && $order->isPreRegistered()) {
            return null;
        }

        return $total;
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
            ? "max:$price"
            : null;
    }

    /**
     * Caso $order seja null, está criando o pedido,
     * então retorna "required" rule para o atributo.
     *
     * @param \App\Models\Order $order
     * @return string
     */
    private function getRequiredRule(Order $order = null, $field = null)
    {
        if ($order && $field) {
            return $order->{$field} ? 'nullable' : 'required';
        }

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

    private function getGeneralRules($data, $order = null)
    {
        $originalPrice = $this->getOriginalPrice($data, $order);
        $deliveryDateMax = Carbon::now()->addMonth()->toDateString();

        $rules[] = [
            'client_id' => [
                'nullable',
                'exists:clients,id'
            ],
            'name' => ['nullable', 'max:90'],
            'code' => [
                $this->getRequiredRule($order, 'code'),
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
                $this->getRequiredRule($order, 'price'),
                'numeric',
                'required_with:discount'
            ],
            'delivery_date' => ['required', 'date_format:Y-m-d', "before_or_equal:$deliveryDateMax"],
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
        ];

        if ($order) {
            $rules['price'][] = 'min_currency:' . $order->total_paid;
        }

        return Arr::collapse($rules);
    }

    private function getClothingTypesRules($data, $order = null)
    {
        return [
            'clothing_types.*.value' => ['nullable', 'numeric', 'max:999999'],
            'clothing_types.*.quantity' => ['nullable', 'integer', 'max:9999'],
        ];
    }

    private function getProductsRules($data, $order = null)
    {
        if ($order && $order->clothingTypes()->count()) {
            return [];
        }

        return [
            'product_items' => ['required'],
            'product_items.*.description' => ['required'],
            'product_items.*.unity' => [
                'required',
                Rule::in(['un', 'pc', 'pct', 'cx', 'm'])
            ],
            'product_items.*.quantity' => ['required'],
            'product_items.*.value' => ['required'],
        ];
    }

    private function rules($data, Order $order = null)
    {
        $rules[] = $this->getGeneralRules($data, $order);
        $rules[] = $this->getProductsRules($data, $order);

        return Arr::collapse($rules);
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

    private function errorMessages($data)
    {
        $originalPrice = $this->getOriginalPrice($data);
        $price = isset($data['price']) ? $data['price'] : null;
        $deliveryDateMaxFormatted = Carbon::now()->addMonth()->format('d/m/Y');

        return [
            'code.required' => __('validation.rules.required'),
            'code.unique' => __('validation.rules.unique', ['pronoun' => 'Este']),
            'discount.lt' => __(
                'validation.rules.lt',
                ['subject' => 'o preço final']
            ) . ' (' . Mask::currencyBRL($originalPrice) . ')',
            'discount.gt' => __('validation.rules.gt', ['subject' => 'R$ 0,00']),
            'down_payment.max' => __(
                'validation.rules.max',
                ['subject' => 'ao valor total']
            ) . ' (' . Mask::currencyBRL($price) . ')',
            'art_paths.*.max' => __('validation.rules.max_file', ['max' => '1MB']),
            'size_paths.*.max' => __('validation.rules.max_file', ['max' => '1MB']),
            'payment_voucher_paths.*.max' => __('validation.rules.max_file', ['max' => '1MB']),
            'payment_via_id.required_with' => __('validation.custom.orders.payment_via_id|required_with'),
            'price.min_currency' => __('validation.custom.orders.price|min_currency'),
            'price.required' => __('validation.custom.orders.price|required'),
            'delivery_date.required' => __('validation.rules.required'),
            'delivery_date.date_format' =>  __('validation.rules.date'),
            'garments.*.items.*.size_id.required' => 'Tamanho é obrigatório',
            'garments.*.items.*.quantity.required' => 'Qtd. é obrigatória',
            'garments.*.match_id.required' => 'Selecione uma combinação válida.',
            'delivery_date.before_or_equal' => 'A data de entrega deve ser anterior ou igual a ' . $deliveryDateMaxFormatted
        ];
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

    /**
     * Verifica se é possível fazer o upload
     * do arquivo no campo especificado.
     *
     * @param $file
     * @param string $field
     * @param \App\Models\Order|null $order
     * @return boolean
     */
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

    /**
     * Armazena os arquivos no campo especificado.
     *
     * @param array $files
     * @param string $field
     * @param \App\Models\Order|null $order
     * @return string
     */
    public function storeFiles(array $files, string $field, $order = null)
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

    /**
     * Deleta os arquivos removidos no front-end.
     *
     * @param array $storedFiles Arquivos atualmente armazenados
     * @param array $uploadedFiles Arquivos que foram feitos o upload
     * @param string $field
     * @return void
     */
    public function deleteRemovedFiles(array $storedFiles, array $uploadedFiles, string $field): void
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
}
