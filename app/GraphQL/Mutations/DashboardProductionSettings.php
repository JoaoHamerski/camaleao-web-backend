<?php

namespace App\GraphQL\Mutations;

use App\Models\AppConfig;
use Illuminate\Support\Facades\Validator;

final class DashboardProductionSettings
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $this->validator($args)->validate();

        AppConfig::set('dashboard', 'production', json_encode($args));

        return true;
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'estampados_no_dia_id' => ['required', 'exists:status,id'],
            'costurados_no_dia_id' => ['required', 'exists:status,id'],
            'month_production_id' => ['required', 'exists:status,id'],
            'late_orders_id' => ['required', 'exists:status,id'],
            'waiting_for_withdrawal_id' => ['required', 'exists:status,id'],
            'delivered_id' => ['required', 'exists:status,id'],
        ]);
    }
}
