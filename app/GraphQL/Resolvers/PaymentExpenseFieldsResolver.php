<?php

namespace App\GraphQL\Resolvers;

use App\Models\Via;
use App\Models\User;
use App\Models\Order;
use App\Util\FileHelper;
use App\Models\ExpenseType;

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

        if ($orderId) {
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
        return !(!!$rootValue->order_id);
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
}
