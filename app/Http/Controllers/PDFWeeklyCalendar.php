<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Status;
use App\Traits\QueryOrderTrait;
use App\Util\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Str;

class PDFWeeklyCalendar extends PDFController
{
    use QueryOrderTrait;

    public function __invoke(Request $request)
    {
        Validator::make($request->all(), [
            'field' => ['required', Rule::in([
                'seam_date',
                'print_date',
                'delivery_date'
            ])],
            'status_id' => ['nullable', 'exists:status,id'],
            'date' => ['required', 'date']
        ])->validate();

        $request->merge([$request->field => $request->date]);
        $date = Carbon::createFromFormat('Y-m-d', $request->date);

        $orders = Order::query();
        $orders = $this->queryOrders($orders, $request->all(), $request);

        if ($request->field === 'print_date') {
            $orders->orderBy('order', 'ASC');
            $orders->orderBy('created_at', 'DESC');
        }

        $pdf = PDF::loadView('pdf.weekly-calendar.index', [
            'title' =>  $this->getTitleForOrdersWeeklyCalendar($date, $request->field),
            'subtitle' => $this->getSubtitleForOrdersWeeklyCalendar($orders, $request),
            'orders' => $orders->get(),
        ]);

        return $pdf->stream(
            $this->getFilenameForOrdersWeeklyCalendar(
                $date,
                $request->field
            )
        );
    }

    private function getFilenameForOrdersWeeklyCalendar(Carbon $date, $field)
    {
        $type = Str::lower(static::$FIELD_TYPES[$field]);
        $date = $date->format('d-m-Y');

        return "calendario-semanal-$type-$date.pdf";
    }

    private function getSubtitleForOrdersWeeklyCalendar($orders, $request)
    {
        $status = null;
        $shirtPiecesText = Str::upper(
            Helper::plural($orders->sum('quantity'), 'f', 'peça')
        );

        if ($request->filled('status_id')) {
            $status = Status::find($request->status_id);

            return "$status->text - $shirtPiecesText";
        }

        return $shirtPiecesText;
    }

    private function getTitleForOrdersWeeklyCalendar(Carbon $date, $field)
    {
        $date = $date->isoFormat('DD [de] MMMM');
        $type = static::$FIELD_TYPES[$field];

        return  "$type - $date";
    }
}
