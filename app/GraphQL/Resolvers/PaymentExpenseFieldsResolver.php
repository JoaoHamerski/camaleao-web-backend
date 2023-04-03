<?php

namespace App\GraphQL\Resolvers;

use App\Models\Via;
use App\Models\User;
use App\Models\Order;
use App\Models\Client;
use App\Util\FileHelper;
use App\Models\ExpenseType;
use App\Models\ProductType;

class PaymentExpenseFieldsResolver
{

    /**
     * Resolve o campo order da query
     *
     * @param $rootValue
     * @return \App\Models\Order|null
     */
    public function order($rootValue)
    {
        $orderId = $rootValue->order_id;

        if (intval($orderId) !== -1) {
            return Order::find($orderId);
        }

        return null;
    }

    /**
     * Resolve o campo user da query
     *
     * @param $rootValue
     * @return \App\Models\User|null
     */
    public function user($rootValue)
    {
        $userId = $rootValue->user_id;

        if ($userId) {
            return User::find($userId);
        }

        return null;
    }

    public function via($rootValue)
    {
        $viaId = $rootValue->via_id;

        return Via::find($viaId);
    }

    public function type($rootValue)
    {
        $typeId = $rootValue->type_id;

        if ($typeId) {
            return ExpenseType::find($typeId);
        }

        return null;
    }

    /**
     * Verifica se o tipo os dados passados é de despesa.
     * Só é de despesa quando possui um usuário na sua relação.
     *
     * @param $rootValue
     * @return bool
     */
    public function isExpense($rootValue)
    {
        return intval($rootValue->order_id) === -1;
    }

    public function receiptPath($rootValue)
    {
        $receiptPath = $rootValue->receipt_path;

        if (!$receiptPath) {
            return null;
        }

        return FileHelper::getFilesURL($receiptPath, 'receipt_path');
    }

    public function paymentVoucherPaths($rootValue)
    {
        $paymentVoucherPaths = $rootValue->payment_voucher_paths;

        if (!$paymentVoucherPaths) {
            return null;
        }

        return FileHelper::getFilesURL($paymentVoucherPaths, 'payment_voucher_paths');
    }

    public function productType($rootValue)
    {
        $productTypeId = $rootValue->product_type_id;

        if ($productTypeId) {
            return ProductType::find($productTypeId);
        }

        return null;
    }

    public function employee($rootValue)
    {
        $employeeId = $rootValue->employee_id;

        if ($employeeId) {
            return User::find($employeeId);
        }

        return null;
    }

    public function client($rootValue)
    {
        $clientId = $rootValue->sponsorship_client_id;

        if ($clientId) {
            return Client::find($clientId);
        }

        return null;
    }
}
