<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class OrderFilter
{
    protected $builder;
    protected $searchFields = [
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
    ];

    public function filter(Builder $query, $values)
    {
        $this->builder = $query;

        foreach ($values as $key => $value) {
            if (in_array($key, $this->searchFields)) {
                $this->search($key, $value);
                continue;
            }

            if (is_callable([$this, $key])) {
                $this->$key($value);
            }
        }
    }

    private function search($field, $value)
    {
        $this->builder->where($field, 'LIKE', '%' . $value . '%');
    }
}
