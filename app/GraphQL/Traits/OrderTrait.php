<?php

namespace App\GraphQL\Traits;

use App\Util\Mask;
use App\Models\User;
use App\Models\Order;
use App\Models\AppConfig;
use App\Util\Formatter;
use App\Util\FileHelper;
use App\Models\Commission;
use App\Models\ClothingType;
use App\Models\ClothMatch;
use App\Models\ClothSize;
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

        $data['clothes'] = $this->getFormattedClothes($data);

        if (!$order) {
            unset($data['clothing_types']);
        }

        return $data;
    }

    public function getFormattedClothes($data)
    {
        return array_map(function ($cloth) {
            if ($cloth['individual_names']) {
                $cloth['items'] = $this->formatItemsIndividual($cloth);
            }

            unset($cloth['items_individual']);

            return $cloth;
        }, $data['clothes']);
    }

    public function formatItemsIndividual($cloth)
    {
        $items = collect($cloth['items_individual']);
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

        $price = $this->evaluateClothesValue($data, $order);
        $quantity = $this->evaluateClothesQuantity($data, $order);

        if ($price) {
            $data['price'] = $price;
        }

        if ($quantity) {
            $data['quantity'] = $quantity;
        }

        return $data;
    }

    private function findClothMatch($clothData)
    {
        return ClothMatch::where('model_id', $clothData['model_id'])
            ->where('material_id', $clothData['material_id'])
            ->where('neck_type_id', $clothData['neck_type_id'])
            ->where('sleeve_type_id', $clothData['sleeve_type_id'])
            ->first();
    }

    private function getClothMatchValue($clothMatch, $quantity)
    {
        $values = $clothMatch->values;

        $value = $values->first(
            fn ($value) => ($value->start <= $quantity && $value->end >= $quantity)
                || !$value->end
        );

        return $value->value;
    }

    private function getClothQuantity($cloth)
    {
        return collect($cloth['items'])->sum('quantity');
    }

    private function getSizeValues($clothMatch, $cloth)
    {
        $sizes = collect($cloth['items']);

        return $sizes->reduce(function ($total, $size) use ($clothMatch) {
            $clothSize = $clothMatch->sizes()->find($size['size_id']);
            $sizeSum = bcmul($clothSize->pivot->value, $size['quantity'], 2);

            return bcadd($total, $sizeSum, 2);
        }, 0);
    }

    private function getClothValue($cloth)
    {
        $clothMatch = $this->findClothMatch($cloth);

        $quantity = $this->getClothQuantity($cloth);
        $value = $this->getClothMatchValue($clothMatch, $quantity);
        $sizeValues = $this->getSizeValues($clothMatch, $cloth);
        $totalCloth = bcmul($quantity, $value, 2);
        $total = bcadd($totalCloth, $sizeValues, 2);

        return $total;
    }

    private function getClothesValue($clothes)
    {
        return collect($clothes)->reduce(
            fn ($total, $cloth) => bcadd(
                $total,
                $this->getClothValue($cloth),
                2
            ),
            0
        );
    }

    private function evaluateTotalPrice($clothesValue, $data, $order = null)
    {
        if (
            $clothesValue <= 0
            && isset($data['discount'])
            && $order
        ) {
            return bcsub(
                $order->original_price,
                $data['discount'],
                2
            );
        }

        if ($order && floatval($clothesValue) === 0.0) {
            return null;
        }

        $price = bcsub($clothesValue, $data['discount'] ?? 0, 2);

        return bcadd($price, $data['shipping_value'] ?? 0, 2);
    }

    private function evaluateClothesValue($data, $order = null)
    {
        $clothesValue = $this->getClothesValue($data['clothes']);
        $total = $this->evaluateTotalPrice($clothesValue, $data, $order);

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

    private function getClothesQuantity($clothes)
    {
        return collect($clothes)->reduce(
            fn ($total, $cloth) => bcadd(
                $total,
                collect($cloth['items'])->sum('quantity')
            ),
            0
        );
    }

    private function evaluateClothesQuantity($data, Order $order = null)
    {
        $total = $this->getClothesQuantity($data['clothes']);

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
            'clothes' => ['required'],
            'clothes.*.individual_names' => ['required', 'boolean'],
            'clothes.*.model_id' => ['required', 'exists:models,id'],
            'clothes.*.material_id' => ['nullable', 'exists:materials,id'],
            'clothes.*.neck_type_id' => ['nullable', 'exists:neck_types,id'],
            'clothes.*.sleeve_type_id' => ['nullable', 'exists:sleeve_types,id'],
            'clothes.*.items' => ['sometimes', 'required', 'array'],
            'clothes.*.items_individual' => ['sometimes', 'required', 'array'],
            'clothes.*.items.*.quantity' => ['sometimes', 'required'],
            'clothes.*.items.*.size_id' => ['sometimes', 'required', 'exists:cloth_sizes,id'],
            'clothes.*.items_individual.*.size_id' => ['sometimes', 'required', 'exists:cloth_sizes,id']
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

    /**
     * Registra ou atualiza as comissões após o cadastro de um pedido.
     *
     * @param \App\Models\Order $order
     * @param bool $isUpdate
     * @return void
     */
    public function handleCommissions(Order $order, $isUpdate = false): void
    {
        $order = $order->fresh();

        $data = [
            'print_commission' => AppConfig::get('orders', 'print_commission'),
            'seam_commission' => $order->getCommissions()->toJson()
        ];

        if (!$isUpdate) {
            $this->storeCommissions($order, $data);
            return;
        }

        $this->updateCommissions($order, $data);
    }

    /**
     * Armazena as comissões, apenas para usuários da produção.
     *
     * @param \App\Models\Order $order
     * @param array $data
     * @return void
     */
    public function storeCommissions(Order $order, array $data): void
    {
        $this->storeCommissionOnProduction(
            $order->commissions()->create($data)
        );
    }

    /**
     * Atualiza as comissões, ou armazena, caso o pedido seja
     * pré-registado e sofra uma atualização e conclua seu registro.
     * Apenas atualiza as comissões se alguma quantidade for alterada,
     * pois as comissões são baseadas apenas na quantidade e não no valor
     * de cada camisa.
     *
     * @param \App\Models\Order $order
     * @param array $data
     * @return void
     */
    public function updateCommissions(Order $order, array $data): void
    {
        if (!$order->commission) {
            $this->storeCommissions($order, $data);
            return;
        }

        if (!$order->isQuantityChanged()) {
            return;
        }

        $commission = $order->commission;
        $commission->update($data);

        $this->updateCommissionOnProduction($commission);
    }

    /**
     * Armazena a comissão para o usuário.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Commission $commission
     * @return \App\Models\User
     */
    public function storeUserCommission(User $user, Commission $commission): User
    {
        $user->commissions()->syncWithoutDetaching([
            $commission->id => [
                'role_id' => $user->role->id,
                'commission_value' => $commission->getUserCommission($user)
            ]
        ]);

        return $user->fresh();
    }

    /**
     * Armazena comissões apenas para usuários da produção
     *
     * @param \App\Models\Commission $commission
     * @return void
     */
    public function storeCommissionOnProduction(Commission $commission): void
    {
        $users = User::production()->get();

        $users->each(function ($user) use ($commission) {
            $this->storeUserCommission($user, $commission);
        });
    }

    /**
     * Retorna a comissão com pivô commissions_users.
     * Se nao encontra a comissão é porque o usuário teve seu nível de
     * privilégio recém alterado para produção.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Commission $commission
     * @return \App\Models\Commission
     */
    public function getCommissionWithPivot(User $user, Commission $commission): Commission
    {
        $commissionWithPivot = $user->commissions()->find($commission->id);

        if (!$commissionWithPivot) {
            $user = $this->storeUserCommission($user, $commission);
            $commissionWithPivot = $user->commissions()->find($commission->id);
        }

        return $commissionWithPivot;
    }

    /**
     * Atualiza as comissões dos usuários da produção
     *
     * @param \App\Models\Commission $commission
     * @return void
     */
    public function updateCommissionOnProduction(Commission $commission): void
    {
        $users = User::production()->get();

        $users->each(function ($user) use ($commission) {
            $commissionWithPivot = $this->getCommissionWithPivot($user, $commission);

            $data['commission_value'] = $commissionWithPivot
                ->pivot
                ->commission
                ->getUserCommission($user);

            if ($commissionWithPivot->pivot->isConfirmed()) {
                $data['confirmed_at'] = null;
                $data['was_quantity_changed'] = true;
            }

            $user->commissions()->syncWithoutDetaching([
                $commission->id => $data
            ]);
        });
    }
}
