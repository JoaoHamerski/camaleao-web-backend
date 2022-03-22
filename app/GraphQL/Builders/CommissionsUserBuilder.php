<?php

namespace App\GraphQL\Builders;

use App\GraphQL\Exceptions\UnprocessableException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class CommissionsUserBuilder
{
    public function __invoke()
    {
        if (Auth::user()->hasRole(['estampa', 'costura'])) {
            return Auth::user()
                ->commissions()
                ->orderByPivot('was_quantity_changed', 'desc')
                ->orderByRaw('-confirmed_at ASC')
                ->orderBy('created_at', 'desc');
        }

        throw new UnprocessableException(
            'Erro ao retornar as comissões',
            'Você precisa ser um usuário de produção para ter comissões.'
        );
    }
}
