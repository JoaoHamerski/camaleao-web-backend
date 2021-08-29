<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Traits\FileManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductionCalendarController extends Controller
{
    use FileManager;

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

    public function storeOrder(Request $request)
    {
        $this->validator(
            $data = $this->getFormattedData($request->all())
        )->validate();

        $path = $this->storeFile(
            $this->base64ToUploadedFile($data['imagePath']),
            'public/imagens_da_arte'
        );

        $path = explode('/', $path)[2];


        $order = Order::create([
            'production_date' => $data['production_date'],
            'art_paths' =>  json_encode([$path]),
        ]);

        if (!empty($data['reminder'])) {
            $order->notes()->create([
                'is_reminder' => true,
                'text' => $data['reminder']
            ]);
        }

        return response()->json(['order' => $order], 200);
    }

    public function getFormattedData(array $data)
    {
        $data['isNotCreated'] = $data['isNotCreated'] === true
            ? 'true'
            : false;

        unset($data['id']);

        return $data;
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'production_date' => 'required',
            'imagePath' => 'required',
            'isNotCreated' => 'required|in:true'
        ]);
    }
}
