<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class ClothingTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'value' => $this->pivot->value ?? new MissingValue,
            'quantity' => $this->pivot->quantity ?? new MissingValue,
            'total' => $this->pivot ? $this->totalValue() : new MissingValue,
            'commission' => $this->commission
        ];
    }
}
