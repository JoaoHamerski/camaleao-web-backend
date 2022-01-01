<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    private function getOrders(Request $request)
    {
        $orders = $this->orders();

        if ($request->filled('code')) {
            $orders->where('code', 'like', '%' . $request->code . '%');
        }

        return $this->when(
            $request->orders === 'true',
            OrderResource::collection(
                $orders->latest()->get()
            )
        );
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'city' => new CityResource($this->city),
            'branch' => new BranchResource($this->branch),
            'shipping_company' => new ShippingCompanyResource($this->shippingCompany),
            'total_owing' => $this->getTotalOwing(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'orders' => $this->getOrders($request)
        ];
    }
}
