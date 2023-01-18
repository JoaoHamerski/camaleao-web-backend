<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Sector;
use App\Models\Status;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    public function viewSector(User $user, array $injected)
    {
        return in_array(
            $injected['sector_id'],
            $user->sectors->pluck('id')->toArray()
        );
    }

    public function stepOrderStatus(User $user, array $injected)
    {
        $status = Status::find($injected['status_id']);

        if (!$status) {
            return false;
        }

        if (!$this->isUserAllowedToStepStatus($user, $status)) {
            return false;
        }

        return true;
    }

    private function isUserAllowedToStepStatus($user, $status): bool
    {
        $sector = $status->sector;
        $sectorUsers = $sector ? $sector->users->pluck('id')->toArray() : [];

        $allowedUserIds = [
            ...$sectorUsers,
            ...Status::getPreviousStatus($status)->sector->users->pluck('id')->toArray()
        ];

        return in_array($user->id, $allowedUserIds)
            || $status->id === Status::getNextStatus($sector->status->last());
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return mixed
     */
    public function view(?User $user, Order $order, array $injected)
    {
        if (!isset($injected['client_id'])) {
            return true;
        }

        return strval($order->client->id) === strval($injected['client_id']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return mixed
     */
    public function update(?User $user, Order $order)
    {
        return !$order->isClosed();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return mixed
     */
    public function delete(User $user, Order $order)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return mixed
     */
    public function restore(User $user, Order $order)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return mixed
     */
    public function forceDelete(User $user, Order $order)
    {
        //
    }
}
