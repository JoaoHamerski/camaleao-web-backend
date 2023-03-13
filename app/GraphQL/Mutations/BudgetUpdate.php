<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\Traits\BudgetTrait;
use App\Models\Budget;

class BudgetUpdate
{
    use BudgetTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $this->validator($data, $isUpdate = true)->validate();

        $budget = Budget::find($args['id']);
        $budget->update(
            array_merge(
                $data,
                ['product_items' => json_encode($data['product_items'])]
            )
        );

        return $budget;
    }
}
