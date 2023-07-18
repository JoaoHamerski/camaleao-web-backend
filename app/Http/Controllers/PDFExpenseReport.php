<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Util\Mask;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class PDFExpenseReport extends PDFController
{
    public function __invoke(Request $request)
    {
        $start_date = $request->start_date;
        $final_date = $request->final_date ?? $start_date;
        $expenses = Expense::whereBetween('date', [$start_date, $final_date])
            ->join('expense_types', 'expenses.expense_type_id', '=', 'expense_types.id');

        $expensesByType = $expenses
            ->clone()
            ->selectRaw('SUM(value) AS total, expense_type_id, COUNT(*) AS quantity')
            ->groupBy('expense_type_id')
            ->orderBy('expense_types.name')
            ->get();

        $expenses = $expenses
            ->orderBy('date', 'ASC')
            ->orderBy('expense_types.name', 'ASC')
            ->get();

        $dates = $this->getBetweenDates($request->start_date, $request->final_date);

        $pdf = PDF::loadView('pdf.expenses.index', [
            'title' => 'Relatório de despesas',
            'subtitle' => $dates,
            'start_date' => $request->start_date,
            'final_date' => $request->final_date,
            'expensesByType' => $expensesByType,
            'expenses' => $expenses
        ]);

        $dates = str_replace('/', '-', $dates);

        return $pdf->stream("Relatóro de despesas - $dates.pdf");
    }

    public function getBetweenDates($start_date, $final_date)
    {
        if (!$final_date) {
            return Mask::date($start_date);
        }

        return Mask::date($start_date) . ' - ' . Mask::date($final_date);
    }
}
