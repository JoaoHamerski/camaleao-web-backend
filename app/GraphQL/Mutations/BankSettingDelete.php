<?php

namespace App\GraphQL\Mutations;

use App\Models\BankSetting;
use Illuminate\Support\Facades\Validator;

class BankSettingDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:bank_settings,id']
        ])->validate();

        $bankSetting = BankSetting::find($args['id']);
        $bankSetting->delete();

        return $bankSetting;
    }
}
