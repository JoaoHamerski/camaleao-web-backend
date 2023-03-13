<?php

namespace App\GraphQL\Traits;

use App\Util\Formatter;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

trait BudgetTrait
{
    public function validator(array $data, $isUpdate = false)
    {
        return Validator::make($data, [
            'id' => $isUpdate ? ['required', 'exists:budgets'] : [''],
            'client' => ['required'],
            'product' => ['required'],
            'date' => ['required', 'date'],
            'product_items' => ['required', 'array'],
            'product_items.*.*' => ['required'],
            'product_items.*.unity' => [Rule::in(['un', 'pc', 'pct', 'cx', 'm'])]
        ]);
    }

    public function getFormattedData(array $data)
    {
        return (new Formatter($data))
            ->currencyBRL('product_items.*.value')
            ->date('date')
            ->get();
    }
}
