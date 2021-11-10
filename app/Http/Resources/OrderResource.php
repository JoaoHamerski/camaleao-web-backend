<?php

namespace App\Http\Resources;

use App\Http\Resources\PaymentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    private function getPayments(Request $request)
    {
        return $this->when(
            $request->payments === 'true',
            PaymentResource::collection(
                $this->payments()
                    ->orderBy('date', 'desc')
                    ->get()
            )
        );
    }

    private function getClient(Request $request)
    {
        return $this->when(
            $request->client === 'true',
            new ClientResource($this->client)
        );
    }

    private function getClothingTypes(Request $request)
    {
        return $this->when(
            $request->clothing_types === 'true',
            ClothingTypeResource::collection($this->clothingTypes)
        );
    }

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
            'code' => $this->code,
            'client' => $this->getClient($request),
            'status' => new StatusResource($this->status),
            'name' => $this->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'delivery_date' => $this->delivery_date,
            'production_date' => $this->production_date,
            'art_paths' => $this->art_paths,
            'size_paths' => $this->size_paths,
            'payment_voucher_paths' => $this->payment_voucher_paths,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'closed_at' => $this->closed_at,
            'discount' => $this->discount,
            'total_owing' => $this->getTotalOwing(),
            'total_paid' => $this->getTotalPaid(),
            'reminder' => $this->getReminder(),
            'state' => $this->getState(),
            'payments' => $this->getPayments($request),
            'clothing_types' => $this->getClothingTypes($request),
            'total_clothings_value' => $this->totalClothingsValue()
        ];
    }
}
