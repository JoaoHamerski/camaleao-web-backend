<?php

namespace App\GraphQL\Traits;

use App\Util\Mask;
use App\Models\Order;
use App\Util\Formatter;
use App\Util\FileHelper;
use App\Models\ClothingType;
use App\Models\GarmentMatch;
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

    public function syncItems($input, $order, $isUpdate = false)
    {
        $inputGarments = collect($input['garments']);

        if ($isUpdate) {
            $order->garments()->delete();
        }

        $inputGarments->each(function ($inputGarment) use ($order) {
            $match = $this->findGarmentMatch($inputGarment);
            $items = $inputGarment['items'];

            $garment = $order->garments()
                ->create([
                    'garment_match_id' => $match->id,
                    'individual_names' => data_get($inputGarment, 'items_individual')
                ]);

            $this->syncGarmentSizes($garment, $items);
        });
    }

    public function syncGarmentSizes($garment, $sizes)
    {
        foreach ($sizes as $size) {
            $garment->sizes()->attach([
                $size['size_id'] => ['quantity' => $size['quantity']]
            ]);
        }
    }

    private function getFormattedData(array $data, $order = null)
    {
        $data = (new Formatter($data))
            ->currencyBRL([
                'down_payment',
                'discount',
                'clothing_types.*.value',
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

        $data['garments'] = $this->getFormattedGarments($data);

        if (!$order || !$order->clothingTypes->count()) {
            unset($data['clothing_types']);
        }

        return $data;
    }

    public function getFormattedGarments($data)
    {
        return array_map(function ($garment) {
            if ($garment['individual_names']) {
                $garment['items'] = $this->formatItemsIndividual($garment);
                $garment['items_individual'] = json_encode($garment['items_individual']);
            } else {
                unset($garment['items_individual']);
            }

            return $garment;
        }, $data['garments']);
    }

    public function formatItemsIndividual($garment)
    {
        $items = collect($garment['items_individual']);
        $grouped = $items->groupBy('size_id');

        return $grouped->map(fn ($group, $id) => [
            'quantity' => $group->count(),
            'size_id' => $id
        ])->values()->toArray();
    }

    private function evaluateOrderAttributes($data, Order $order = null)
    {
        if ($order && $order->clothingTypes->count()) {
            return $this->ctEvaluateOrderAttributes($data, $order);
        }

        $price = $this->evaluateGarmentsValue($data, $order);
        $quantity = $this->evaluateGarmentsQuantity($data, $order);

        if ($price) {
            $data['price'] = $price;
        }

        if ($quantity) {
            $data['quantity'] = $quantity;
        }

        return $data;
    }

    private function findGarmentMatch($garmentData)
    {
        return GarmentMatch::where('model_id', $garmentData['model_id'])
            ->where('material_id', $garmentData['material_id'])
            ->where('neck_type_id', $garmentData['neck_type_id'])
            ->where('sleeve_type_id', $garmentData['sleeve_type_id'])
            ->first();
    }

    private function getGarmentMatchValue($garmentMatch, $quantity)
    {
        $values = $garmentMatch->values;

        if ($garmentMatch->unique_value) {
            return $garmentMatch->unique_value;
        }

        $value = $values->first(
            fn ($value) => ($value->start <= $quantity && $value->end >= $quantity)
                || !$value->end
        );

        return $value->value;
    }

    private function getGarmentQuantity($garment)
    {
        return collect($garment['items'])->sum('quantity');
    }

    private function getSizeValues($garmentMatch, $garment)
    {
        $sizes = collect($garment['items']);

        return $sizes->reduce(function ($total, $size) use ($garmentMatch) {
            $garmentSize = $garmentMatch->sizes()->find($size['size_id']);
            $sizeSum = bcmul($garmentSize->pivot->value, $size['quantity'], 2);

            return bcadd($total, $sizeSum, 2);
        }, 0);
    }

    private function getGarmentValue($garment)
    {
        $garmentMatch = $this->findGarmentMatch($garment);

        $quantity = $this->getGarmentQuantity($garment);
        $value = $this->getGarmentMatchValue($garmentMatch, $quantity);
        $sizeValues = $this->getSizeValues($garmentMatch, $garment);
        $totalGarment = bcmul($quantity, $value, 2);
        $total = bcadd($totalGarment, $sizeValues, 2);

        return $total;
    }

    private function getGarmentsValue($garments)
    {
        return collect($garments)->reduce(
            fn ($total, $garment) => bcadd(
                $total,
                $this->getGarmentValue($garment),
                2
            ),
            0
        );
    }

    private function evaluateTotalPrice($garmentsValue, $data, $order = null)
    {
        if (
            $garmentsValue <= 0
            && isset($data['discount'])
            && $order
        ) {
            return bcsub(
                $order->original_price,
                $data['discount'],
                2
            );
        }

        if ($order && floatval($garmentsValue) === 0.0) {
            return null;
        }

        $price = bcsub($garmentsValue, $data['discount'] ?? 0, 2);

        return bcadd($price, $data['shipping_value'] ?? 0, 2);
    }

    private function evaluateGarmentsValue($data, $order = null)
    {
        $garmentsValue = $this->getGarmentsValue($data['garments']);
        $total = $this->evaluateTotalPrice($garmentsValue, $data, $order);

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

    private function getGarmentsQuantity($garments)
    {
        return collect($garments)->reduce(
            fn ($total, $garment) => bcadd(
                $total,
                collect($garment['items'])->sum('quantity')
            ),
            0
        );
    }

    private function evaluateGarmentsQuantity($data, Order $order = null)
    {
        $total = $this->getGarmentsQuantity($data['garments']);

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

    private function rules($data, Order $order = null)
    {
        $originalPrice = $this->getOriginalPrice($data, $order);

        $rules = [
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
            'delivery_date' => ['required', 'date_format:Y-m-d'],
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
            'garments' => ['required'],
            'garments.*.individual_names' => ['required', 'boolean'],
            'garments.*.model_id' => ['required', 'exists:models,id'],
            'garments.*.material_id' => ['nullable', 'exists:materials,id'],
            'garments.*.neck_type_id' => ['nullable', 'exists:neck_types,id'],
            'garments.*.sleeve_type_id' => ['nullable', 'exists:sleeve_types,id'],
            'garments.*.items' => ['sometimes', 'required', 'array'],
            'garments.*.items_individual' => ['sometimes', 'required', 'string'],
            'garments.*.items.*.quantity' => ['sometimes', 'required'],
            'garments.*.items.*.size_id' => ['sometimes', 'required', 'exists:garment_sizes,id'],
            'garments.*.items_individual.*.size_id' => ['sometimes', 'required', 'exists:garment_sizes,id']
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

    private function errorMessages($data)
    {
        $originalPrice = $this->getOriginalPrice($data);
        $price = isset($data['price']) ? $data['price'] : null;

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
            'print_date.required' => __('validation.rules.required'),
            'print_date.date_format' =>  __('validation.rules.date'),
            'seam_date.required' => __('validation.rules.required'),
            'seam_date.date_format' =>  __('validation.rules.date'),
            'delivery_date.required' => __('validation.rules.required'),
            'delivery_date.date_format' =>  __('validation.rules.date'),
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
