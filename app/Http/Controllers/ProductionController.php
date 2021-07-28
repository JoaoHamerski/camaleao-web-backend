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
        $commissions = CommissionUser::orderBy('confirmed_at', 'desc');

        if ($request->filled('filtro') && $request->filtro == 'pendentes') {
            $commissions->whereNull('confirmed_at');
        } else {
            $commissions->whereNotNull('confirmed_at');
        }

        return view('production.index-admin', [
            'commissions' => $commissions->paginate(10)->appends($request->query())
        ]);
    }

    public function assignConfirmation(CommissionUser $commissionUser)
    {
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
            ->orderBy('created_at', 'desc')
            ->with('order')
            ->get();

        return response()->json([
            'commissions' => $commissions
        ], 200);
    }
}
