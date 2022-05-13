<?php

namespace App\GraphQL\Mutations;

use App\Models\UpdateReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UpdateReportReadByUser
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $report = UpdateReport::latest()->first();
        $users = json_decode($report->read_by_user_ids);
        $users[] = Auth::id();


        $report->update([
            'read_by_user_ids' => json_encode($users)
        ]);

        return $report;
    }
}
