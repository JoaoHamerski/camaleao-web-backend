<?php

namespace App\GraphQL\Mutations;

use App\Models\AppConfig;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class StatusUpdateConcludeWeeklyCalendar
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'status' => ['nullable'],
            'status.*' => ['nullable', 'exists:status,id']
        ])->validate();

        $data = [];

        foreach ($args as $key => $value) {
            $data[] = [
                'field' => $key,
                'status' => $value
            ];
        }

        AppConfig::set('status', 'conclude_status_map', $data);

        return AppConfig::get('status', 'conclude_status_map', true);
    }
}
