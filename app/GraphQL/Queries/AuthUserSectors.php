<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
use App\Models\Sector;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;

class AuthUserSectors
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return Auth::user()
            ->sectors()
            ->join('status', 'sectors.id', '=', 'status.sector_id')
            ->select('sectors.*')
            ->orderBy('status.order')
            ->groupBy('status.sector_id')
            ->get()
            ->map(fn ($sector) => [
                'orders_count' => Order::getBySector($sector)->count(),
                'sector' => $sector
            ]);
    }
}
