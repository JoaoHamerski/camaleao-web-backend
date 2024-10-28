<?php

namespace App\Http\Resources;

use Arr;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return Arr::only(parent::toArray($request), ['id', 'name', 'phone', 'bonus']);
    }
}
