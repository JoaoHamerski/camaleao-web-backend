<?php

namespace App\Queries;

use Carbon\Carbon;
use App\Util\Helper;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpensesRequest
{
    public static function query(Request $request, array $options = [])
    {
        $request = $request->duplicate();

        $data = $options['data'] ?? [];
        $merge = $options['merge'] ?? [];
        $expenses = $options['query'] ?? Expense::query();
        $isPDF = $options['isPDF'] ?? false;

        if (!empty($merge)) {
            $request->merge($merge);
        }

        if ($request->has('search')) {
            $expenses->where('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('start_date') || $request->filled(['start_date', 'end_date'])) {
            $expenses->whereBetween('date', [
                $data['start_date'] ?? $request->start_date,
                $data['end_date'] ?? $request->end_date
            ]);
        }

        if ($request->filled('description')) {
            $expenses->where('description', 'like', '%' . $request->description . '%');
        }

        if (Auth::user()->hasRole('gerencia')) {
            $expenses->latest();
        }

        if (Auth::user()->hasRole('atendimento')) {
            $expenses->where('user_id', Auth::user()->id)->latest();
        }

        if (Helper::parseBool($request->order_date)) {
            $expenses->ordeBy('date', 'desc');
        }

        return $expenses;
    }
}
