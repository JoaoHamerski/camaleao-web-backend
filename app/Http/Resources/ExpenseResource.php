<?php

namespace App\Http\Resources;

use App\Util\FileHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            'user' => new UserResource($this->user),
            'type' => new ExpenseTypeResource($this->type),
            'via' => new ViaResource($this->via),
            'description' => $this->description,
            'value' => $this->value,
            'date' => $this->date,
            'receipt_path' => FileHelper::getFilesURL($this->receipt_path, 'receipt_path'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
