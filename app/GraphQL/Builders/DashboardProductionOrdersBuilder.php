<?php

namespace App\GraphQL\Builders;

use App\GraphQL\Queries\DashboardProduction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class DashboardProductionOrdersBuilder
{
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'production_date' => ['nullable', 'date_format:Y-m']
        ])->validate();

        $types = [
            'estampados' => fn () => $this->getEstampadosNoDiaOrders(),
            'costurados' => fn () => $this->getCosturadosNoDiaOrders(),
            'month_production' => fn () => $this->getMonthProductionOrders($args['production_date']),
            'late_orders' => fn () => $this->getLateOrders(),
            'waiting_for_withdrawal_orders' => fn () => $this->getWaitingForWithdrawalOrders()
        ];

        return $types[$args['type']]();
    }

    public function getEstampadosNoDiaOrders()
    {
        $query = DashboardProduction::query();

        DashboardProduction::confirmedStatusQuery($query, 'estampados');

        return DashboardProduction::queryPeriods($query)['day']->orderBy('created_at', 'desc');
    }

    public function getCosturadosNoDiaOrders()
    {
        $query = DashboardProduction::query();

        DashboardProduction::confirmedStatusQuery($query, 'costurados');

        return DashboardProduction::queryPeriods($query)['day']->orderBy('created_at', 'desc');
    }

    public function getMonthProductionOrders($productionDate)
    {
        $query = DashboardProduction::query();

        return DashboardProduction::monthProductionQuery($query, $productionDate)->orderBy('created_at', 'desc');
    }

    public function getLateOrders()
    {
        return DashboardProduction::lateOrdersQuery()->orderBy('delivery_date', 'asc');
    }

    public function getWaitingForWithdrawalOrders()
    {
        return DashboardProduction::waitingForWithdrawalQuery()->orderBy('created_at', 'desc');
    }
}
