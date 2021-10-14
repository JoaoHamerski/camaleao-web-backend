<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            'id' => $this->id,
            'code' => $this->code,
            'client' => new ClientResource($this->client),
            'status' => new StatusResource($this->status),
            'name' => $this->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'delivery_date' => $this->delivery_date,
            'production_date' => $this->production_date,
            'art_paths' => $this->art_paths,
            'size_paths' => $this->size_paths,
            'payment_voucher_paths' => $this->payment_voucher_paths,
            'costureira_valor' => $this->costureira_valor,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'closed_at' => $this->closed_at,
            'discount' => $this->closed_at,
            'total_owing' => $this->total_owing,
            'total_paid' => $this->total_paid,
            'reminder' => $this->reminder,
            'state' => $this->state
        ];
    }
}
