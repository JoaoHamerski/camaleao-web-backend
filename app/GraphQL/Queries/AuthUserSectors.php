<?php

namespace App\GraphQL\Queries;

use App\Models\Order;
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
            ->ordered()
            ->map(fn ($sector) => [
                'orders_count' => Order::getBySector($sector)->count(),
                'sector' => $sector,
                'next_status' => Status::getNextStatus($sector->status->last())
            ]);
    }
}
