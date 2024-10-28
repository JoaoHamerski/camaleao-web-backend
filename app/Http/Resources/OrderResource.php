<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return Arr::only(parent::toArray($request), [
            'id',
            'code',
            'client_id',
            'name',
            'quantity',
            'price',
            'delivery_date',
            'created_at',
            'discount',
            'original_price',
            'total_paid',
            'total_garments_value',
            'states'
        ]);
    }
}
