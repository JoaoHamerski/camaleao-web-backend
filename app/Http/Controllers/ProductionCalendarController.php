<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;

class ProductionCalendarController extends Controller
{
    public function index()
    {
        return view('production-calendar.index');
    }

    public function ordersByWeek(Request $request)
    {
        $orders = Order::query();

        if ($request->filled('startWeek')) {
            $startWeekAux = Carbon::createFromFormat('d/m/Y', $request->startWeek);
            $startWeek = $startWeekAux->toDateString();
            $endWeek = $startWeekAux->clone()->addDays(5)->toDateString();

            $orders->whereRaw(
                "production_date >= ? AND production_date <= ?",
                [$startWeek, $endWeek]
            );
        }

        $orders = $orders->get()->groupBy('production_date');
        $dates = [];

        for ($i = 0; $i < 6; $i++) {
            $date = $startWeekAux->clone()->addDay($i);

            if (!empty($orders[$date->toDateString()])) {
                $dates[$date->toDateString()] = $orders[$date->toDateString()];
            } else {
                $dates[$date->toDateString()] = [];
            }
        }

        return response()->json([
            'dates' => $dates
        ]);
    }
}
