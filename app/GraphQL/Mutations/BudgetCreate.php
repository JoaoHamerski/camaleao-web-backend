<?php

namespace App\GraphQL\Mutations;

use App\Models\AppConfig;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\Traits\BudgetTrait;

class BudgetCreate
{
    use BudgetTrait;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $this->validator($data)->validate();

        return Auth::user()->budgets()->create([
            'client' => $data['client'],
            'product' => $data['product'],
            'date' => $data['date'],
            'product_items' => json_encode($data['product_items']),
            'settings' => AppConfig::get('app', 'budget_generator_settings')
        ]);
    }
}
