<?php

namespace App\GraphQL\Mutations;

use App\Models\Model;
use Illuminate\Support\Facades\Validator;

final class ModelUpdate
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:models'],
            'name' => ['required']
        ])->validate();

        $model = Model::find($args['id']);
        $model->update($args);

        return $model;
    }
}
