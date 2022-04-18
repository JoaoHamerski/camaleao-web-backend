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
            'password.required' => 'Por favor, informe sua senha.',
            'password.current_password' => 'A sua senha não confere.',
            'replace_status_id.required' => 'Por favor, selecione um status para substituir.'
        ];
    }
}
