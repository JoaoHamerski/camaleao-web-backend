<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ClientFilter
{
    protected $builder;
    protected $searchFields = [
        'name',
        'phone',
        'bonus'
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
