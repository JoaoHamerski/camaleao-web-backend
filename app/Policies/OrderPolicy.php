<?php

namespace App\Policies;

use App\Models\Order;
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
        $order = Order::find($injected['order_id']);
        $status = Status::find($injected['status_id']);
        $sector = $order->getSectorWithRematchedStatus();
        $userSectors = $user->sectors->pluck('id')->toArray();

        if (!$order || !$sector) {
            return false;
        }

        if (!in_array($sector->id, $userSectors)) {
            return false;
        }

        return in_array(
            $status->id,
            $sector->status->pluck('id')->toArray()
        );
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
