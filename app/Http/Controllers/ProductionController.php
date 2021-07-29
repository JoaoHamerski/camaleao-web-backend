<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use Illuminate\Http\Request;
use App\Models\CommissionUser;
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
