<?php

namespace App\GraphQL\Mutations;

use App\Models\Order;
use App\Models\Status;
use App\Util\Helper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderUpdateStatus
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:orders,id'],
            'status_id' => ['required', 'exists:status,id'],
            'override_option' => ['nullable', Rule::in(['update', 'keep'])]
        ])->validate();


        $order = Order::find($args['id']);

        if (!Helper::filled($args, 'override_option')) {
            $this->checkIfChangedStatusHasDates($args['status_id'], $order);
        }

        $order->update(['status_id' => $args['status_id']]);

        $this->syncStatus($order, $args['override_option']);

        return $order;
    }

    public function checkIfChangedStatusHasDates($statusToUpdate, $order)
    {
        $statusToUpdate = Status::find($statusToUpdate);
        $orderStatus = $order->status;

        if ($statusToUpdate->order <= $orderStatus->order) {
            return;
        }

        $statusToCheck = Status::getStatusBetween($orderStatus, $statusToUpdate);
        $statusToCheck->push($statusToUpdate);

        $statusWithPivot = $order->linkedStatus->filter(
            fn ($sWithPivot) => $statusToCheck->contains(fn ($sToCheck) => $sWithPivot->id === $sToCheck->id)
        );

        $hasDate = $statusWithPivot->some(
            fn ($status) => $status->pivot->confirmed_at
        );

        if ($hasDate) {
            throw ValidationException::withMessages([
                'override_dates' => json_encode($statusWithPivot->whereNotNull('pivot.confirmed_at'))
            ]);
        }
    }

    public function syncStatus($order, $overrideOption)
    {
        $order->refresh();

        $linkedStatus = $order->linkedStatus;
        $orderStatus = $order->status;
        $updateConfirmedDate = $overrideOption === null || $overrideOption === 'update';

        $linkedStatus->each(function ($linkedS) use ($orderStatus, $order, $updateConfirmedDate) {
            if ($orderStatus->order >= $linkedS->order) {
                if (!$linkedS->pivot->is_confirmed) {
                    $order->confirmLinkedStatus($linkedS, $updateConfirmedDate);
                }
            }

            if ($orderStatus->order < $linkedS->order) {
                if ($linkedS->pivot->is_confirmed) {
                    $order->cancelLinkedStatus($linkedS);
                }
            }
        });
    }
}
