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
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

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
                'seam_date',
                'print_date',
                'delivery_date',
            ])
            ->base64ToUploadedFile([
                'art_paths.*',
                'size_paths.*',
                'payment_voucher_paths.*'
            ])
            ->get();

        $data = $this->evaluateOrderAttributes($data, $order);

        if ($order && $order->client_id) {
            unset($data['client_id']);
        }

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
                Rule::requiredIf(fn () => $order && !$order->client_id),
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
            'seam_date' => ['required', 'date_format:Y-m-d'],
            'print_date' => ['required', 'date_format:Y-m-d'],
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
                ['total_price' => Mask::currencyBRL($originalPrice)]
            ),
            'discount.gt' => __('general.validation.orders.discount_gt'),
            'down_payment.max_currency' => __(
                'general.validation.orders.down_payment_max_currency',
                ['final_value' => Mask::currencyBRL($price)]
            ),
            'size_paths.*.max' => __('general.validation.file_min', ['max' => '1MB']),
            'payment_via_id.required_with' => __('general.validation.orders.payment_via_id_required_with'),
            'payment_voucher_paths.*.max' => __('general.validation.file_min', ['max' => '1MB']),
            'price.min_currency' => __('general.validation.orders.price_min_currency'),
            'price.required' => __('general.validation.orders.price_required'),
            'print_date.required' => 'Por favor, informe a data de estampa',
            'seam_date.required' => 'Por favor, informe a data de costura',
            'delivery_date.required' => 'Por favor, informe a data de entrega',
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

        return bcsub($clothingTypesValue, $data['discount'] ?? 0, 2);
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
