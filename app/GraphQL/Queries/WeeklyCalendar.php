<?php

namespace App\GraphQL\Queries;

use Carbon\Carbon;
use App\Models\Order;
use App\Util\Formatter;
use App\Models\AppConfig;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class WeeklyCalendar
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $this->validator($data)->validate();

        $date = Carbon::createFromFormat('Y-m-d', $data['date']);
        $startOfWeek = $date->clone()->startOf('week');
        $endOfWeek = $date->clone()->endOf('week');

        $orders = $this->getOrdersBetweenDates($startOfWeek, $endOfWeek, $data['field']);

        return $this->getPopulatedWeekDays($orders, $startOfWeek);
    }

    public function getOrdersBetweenDates($startOfWeek, $endOfWeek, $field)
    {
        $UPDATE_STATUS_MAP = collect(AppConfig::get('status', 'update_status_map'));
        $CONCLUDE_STATUS_MAP = collect(AppConfig::get('status', 'conclude_status_map'));

        $STATUS_IS_CONCLUDED_FROM_FIELD = $CONCLUDE_STATUS_MAP
            ->firstWhere('field', '==', $field)['status'];

        $STATUS_CAN_BE_CONCLUDED_FROM_FIELD =  $UPDATE_STATUS_MAP
            ->firstWhere('field', '==', $field)['status_is'] ?? [];

        $query = Order::whereBetween($field, [
            $startOfWeek->toDateString(),
            $endOfWeek->toDateString()
        ]);

        if ($field === 'print_date') {
            $query->orderByRaw('ISNULL(`order`), `order` ASC');
        }

        $query->orderBy('created_at', 'DESC');

        $orders = $query->get()->map(
            fn (Order $order) => $order
                ->isConcluded($STATUS_IS_CONCLUDED_FROM_FIELD)
                ->canBeConcluded($STATUS_CAN_BE_CONCLUDED_FROM_FIELD)
        );

        return $orders->groupBy($field);
    }

    public function getPopulatedWeekDays($orders, $startOfWeek)
    {
        $daysOfWeek = [];

        for ($i = 0; $i < 6; $i++) {
            $date = $startOfWeek->clone()->addDays($i);
            $ordersOfDate = collect($orders->get($date->toDateString())) ?? [];

            $daysOfWeek[] = [
                'date' => $date->toDateString(),
                'orders' => $ordersOfDate,
                'total_quantity' => $ordersOfDate->sum('quantity')
            ];
        }

        return $daysOfWeek;
    }

    public function getFormattedData(array $data)
    {
        return (new Formatter($data))
            ->date('date')
            ->get();
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'date' => ['required', 'date'],
            'field' => ['required', Rule::in([
                'print_date',
                'seam_date',
                'delivery_date'
            ])]
        ]);
    }
}
