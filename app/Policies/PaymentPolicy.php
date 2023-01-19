<?php

namespace App\Policies;

use App\Models\User;
use App\Util\Helper;
use App\Models\Entry;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Payment $payment)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, array $injected)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payment  $payment
     * @param   array     $injected
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Payment $payment, array $injected)
    {
        if (
            $payment->is_confirmed !== null
            && !$user->hasRole('gerencia')
        ) {
            return false;
        }

        if (!$this->allowUpdateFromEntry($payment, $injected['bank_uid'])) {
            return false;
        }

        return strval($payment->order_id) === strval($injected['order_id']);
    }

    private function allowUpdateFromEntry($payment, $bankUid)
    {
        $entry = null;

        if (empty($bankUid)) {
            return true;
        }

        $entry = Entry::where('bank_uid', $bankUid)->first();

        if (!$entry) {
            return false;
        }

        return +$entry->value === +$payment->value;
    }

    public function assign(User $user, array $injected)
    {
        $confirmation = $injected['confirmation'];

        if ($confirmation === false) {
            return $user->hasRole(['gerencia', 'atendimento']);
        }

        return $user->hasRole('gerencia');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Payment $payment)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Payment $payment)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Payment $payment)
    {
        //
    }
}
