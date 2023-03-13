<?php

namespace App\GraphQL\Queries;

use App\Models\Budget;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class BudgetGeneratePDF
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:budgets']
        ])->validate();

        $budget = Budget::find($args['id']);

        return URL::temporarySignedRoute(
            'pdf.budget',
            now()->addMinutes(5),
            compact('budget')
        );
    }
}
