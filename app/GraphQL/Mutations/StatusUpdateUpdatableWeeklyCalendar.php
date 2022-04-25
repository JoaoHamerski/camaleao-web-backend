<?php

namespace App\GraphQL\Mutations;

use App\Models\AppConfig;
use Illuminate\Support\Facades\Validator;

class StatusUpdateUpdatableWeeklyCalendar
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'print_date.status' => ['nullable'],
            'print_date.status.*' => ['exists:status,id'],
            'print_date.update_to' => ['exists:status,id'],
            'seam_date' => ['nullable'],
            'seam_date.status.*' => ['exists:status,id'],
            'seam_date.update_to' => ['exists:status,id'],
            'delivery_date' => ['nullable'],
            'delivery_date.status.*' => ['exists:status,id'],
            'delivery_date.update_to' => ['exists:status,id'],
        ])->validate();

        $data = [];

        foreach ($args as $key => $value) {
            $data[] = [
                'field' => $key,
                'status_is' => $value['status'],
                'update_to' => $value['update_to']
            ];
        }

        AppConfig::set('status', 'update_status_map', $data);

        return AppConfig::get('status', 'update_status_map', true);
    }
}
