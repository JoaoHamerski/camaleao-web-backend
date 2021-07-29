<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use Illuminate\Http\Request;
use App\Models\CommissionUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class ProductionController extends Controller
{
    public function index()
    {
        return view('production.index');
    }

    public function indexAdmin(Request $request)
    {
        $commissions = CommissionUser::join(
            'commissions',
            'commission_user.commission_id',
            '=',
            'commissions.id'
        )->orderBy('commissions.created_at', 'desc');

        if ($request->filled('filtro')) {
            if ($request->filtro == 'pendentes') {
                $commissions->whereNull('confirmed_at');
            } elseif ($request->filtro == 'confirmados') {
                $commissions->whereNotNull('confirmed_at');
            }
        }
        if ($request->filled('codigo')) {
            $code = $request->codigo;

            $commissions->whereHas('commission.order', function ($query) use ($code) {
                $query->where('code', 'like', '%' . $code . '%');
            });
        }

        return view('production.index-admin', [
            'commissions' => $commissions
                ->paginate(10)
                ->appends($request->query())
        ]);
    }

    public function assignConfirmation(CommissionUser $commissionUser)
    {
        if ($commissionUser->user_id !== Auth::id()) {
            abort(403);
        }
        
        $commissionUser->update([
            'confirmed_at' => now(),
            'was_quantity_changed' => false
        ]);

        return response()->json([], 204);
    }
    
    public function calculateMonthCommission(Request $request)
    {
        if (Auth::user()->hasRole('gerencia')) {
            $commissions = CommissionUser::where('role_id', $request->user_role);
        } else {
            $commissions = CommissionUser::where('user_id', Auth::id());
        }

        $selectedMonth = Carbon::createFromDate(
            Carbon::now()->year,
            $request->month,
            1
        );

        if ($selectedMonth->greaterThan(Carbon::now())) {
            $selectedMonth->subYear(1);
        }

        $commissions->where(function ($query) use ($selectedMonth) {
            $query->whereNotNull('confirmed_at');
            $query->whereRaw(
                "(confirmed_at >= ? AND confirmed_at <= ?)",
                [
                    $selectedMonth->startOfMonth()->toDateString(),
                    $selectedMonth->endOfMonth()->toDateString()
                ]
            );
        });
        $totalValue = $commissions->sum('commission_value');
        
        return response()->json([
            'has_commission' => !! $commissions->count(),
            'commission' => $totalValue,
            'year' => $selectedMonth->year,
            'month' => $selectedMonth->month
        ], 200);
    }

    public function getCommissions()
    {
        $commissions = Auth::user()
            ->commissions()
            ->orderByPivot('was_quantity_changed', 'desc')
            ->orderBy('confirmed_at', 'asc')
            ->orderBy('created_at', 'desc')
            ->with('order');

        return response()->json([
            'commissions' => $commissions->paginate(10)
        ], 200);
    }
}
