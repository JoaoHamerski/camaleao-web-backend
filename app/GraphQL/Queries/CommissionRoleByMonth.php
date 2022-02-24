<?php

namespace App\GraphQL\Queries;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\CommissionUser;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommissionRoleByMonth
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $this->validator($args)->validate();

        @list($month, $year) = explode('/', $args['month']);
        $selectedMonth = Carbon::createFromDate($year, $month, 1);
        $commissions = $this->getCommissionsQuery($args);

        $commissions->where(function ($query) use ($selectedMonth) {
            $query->whereNotNull('confirmed_at');
            $query->whereBetween('confirmed_at', [
                $selectedMonth->startOfMonth()->toDateString(),
                $selectedMonth->endOfMonth()->toDateString(),
            ]);
        });

        $value = $commissions->sum('commission_value');
        $role = $this->getRole($args);

        return compact('month', 'year', 'value', 'role');
    }

    /**
     * Se a query for feito por alguém da gerencia,
     * buscar comissões da regra informada, senão
     * do usuário informado.
     */
    public function getCommissionsQuery($data)
    {
        if (Auth::user()->hasRole('gerencia')) {
            return CommissionUser::where('role_id', $data['role_id']);
        }

        return CommissionUser::where('user_id', Auth::id());
    }

    public function getRole($data)
    {
        if (isset($data['role_id'])) {
            return Role::find($data['role_id']);
        }

        return Auth::user()->role;
    }

    public function validator($data)
    {
        $VALID_RULES = [4, 5];

        return Validator::make($data, [
            'role_id' => [
                Rule::requiredIf(fn () => Auth::user()->hasRole('gerencia')),
                'exists:roles,id',
                Rule::in($VALID_RULES)
            ],
            'month' => ['required', 'date_format:m/Y']
        ], $this->errorMessages());
    }

    public function errorMessages()
    {
        return [
            'role_id.required' => __('general.validation.commissions.role_id_required'),
            'month.required' => __('general.validation.commissions.month_required'),
            'month.date_format' => __('general.validation.commissions.month_date_format')
        ];
    }
}
