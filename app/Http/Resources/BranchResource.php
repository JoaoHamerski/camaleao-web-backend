<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'id' => $this->id,
            'city' => new CityResource($this->city),
            'cities' => CityResource::collection($this->cities),
            'created_at' => $this->created_at,
            'shipping_company' => new ShippingCompanyResource($this->shippingCompany)
        ];
    }
}
