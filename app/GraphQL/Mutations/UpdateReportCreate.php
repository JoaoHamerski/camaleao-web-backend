<?php

namespace App\GraphQL\Mutations;

use App\Models\UpdateReport;
use Illuminate\Support\Facades\Validator;

class UpdateReportCreate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'description' => ['required']
        ])->validate();

        $report = UpdateReport::create([
            'description' => $args['description']
        ]);

        return $report;
    }
}
