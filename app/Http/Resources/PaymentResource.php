<?php

namespace App\Http\Resources;

use App\Util\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    private function getOrder(Request $request)
    {
        return $this->when(
            Helper::parseBool($request->order),
            new OrderResource($this->order)
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
            'order' => $this->getOrder($request),
            'payment_via' => new ViaResource($this->via),
            'value' => $this->value,
            'date' => $this->date,
            'note' => $this->note,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'confirmed_at' => $this->confirmed_at,
            'is_confirmed' => $this->is_confirmed
        ];
    }
}
