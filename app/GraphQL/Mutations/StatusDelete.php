<?php

namespace App\GraphQL\Mutations;

use App\Models\Status;
use Illuminate\Support\Facades\Validator;

class StatusDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:status,id'],
            'replace_status_id' => ['required', 'exists:status,id'],
            'password' => ['required', 'current_password']
        ], $this->errorMessages())->validate();

        $status = Status::find($args['id']);
        $statusToReplace = Status::find($args['replace_status_id']);

        activity()->withoutLogs(function () use ($status, $statusToReplace) {
            $status->orders()->update([
                'status_id' => $statusToReplace->id
            ]);
        });

        $status->delete();

        return $status;
    }

    private function errorMessages()
    {
        return [
            'password.required' => __('validation.rules.required'),
            'password.current_password' => __('validation.rules.current_password'),
            'replace_status_id.required' => __('validation.rules.required_list', [
                'pronoun' => 'um',
                'attribute' => 'status para substituir'
            ])
        ];
    }
}
