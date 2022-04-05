<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommissionConfirmProduction
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:commissions,id']
        ])->validate();

        $commissions = Auth::user()->commissions();

        $commissions->updateExistingPivot($args['id'], [
            'confirmed_at' => now(),
            'was_quantity_changed' => false
        ]);

        return $commissions->find($args['id']);
    }
}
