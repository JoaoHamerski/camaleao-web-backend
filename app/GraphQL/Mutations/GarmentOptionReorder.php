<?php

namespace App\GraphQL\Mutations;

use App\Models\GarmentSize;
use App\Models\Material;
use App\Models\Model;
use App\Models\NeckType;
use App\Models\SleeveType;
use Illuminate\Support\Facades\Validator;

final class GarmentOptionReorder
{
    protected static $FIELDS_MAP_MODEL = [
        'model' => Model::class,
        'material' => Material::class,
        'sleeve_type' => SleeveType::class,
        'neck_type' => NeckType::class,
        'garment_size' => GarmentSize::class
    ];

    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $field = $args['option'] . 's';

        Validator::make($args, [
            'items.*.id' => ['required', "exists:$field,id"]
        ])->validate();

        foreach ($args['items'] as $item) {
            $model = static::$FIELDS_MAP_MODEL[$args['option']];
            $model::find($item['id'])->update(['order' => $item['order']]);
        }

        return true;
    }
}
