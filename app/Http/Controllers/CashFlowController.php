<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Expense;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Util\CollectionHelper;

class CashFlowController extends Controller
{
    public function index(Request $request)
    {
    	$expenses = Expense::query();
    	$payments = Payment::query();

    	if ($request->hasAny(['dia_inicial', 'dia_final'])) {
    		$validator = Validator::make($request->all(), [
	    		'dia_inicial' => 'required|date_format:d/m/Y',
	    		'dia_final' => 'nullable|date_format:d/m/Y|after:dia_inicial'
	    	]);

	    	if (! $request->wantsJson()) {
	    		if ($validator->fails()) {
	    			abort(422);
	    		}
	    	} else {
	    		if ($validator->fails()) {
	    			return response()->json([
	    				'message' => 'error',
	    				'errors' => $validator->errors()
	    			], 422);
	    		}
	    	}

	    	$start_date = Carbon::createFromFormat('d/m/Y', $request->dia_inicial)->subDays(1);

   		    $end_date = ! empty($request->dia_final) 
	            ? Carbon::createFromFormat('d/m/Y', $request->dia_final)
	            : (new Carbon($start_date))->addDays(1);

	    	$expenses->whereBetween('date', [$start_date, $end_date]);
	    	$payments->whereBetween('date', [$start_date, $end_date]);
    	}


    	if ($request->has('descricao')) {
    		$payments->whereHas('order', function($query) use ($request) {
    			$query->where('name', 'like', '%' . $request->descricao . '%');
    		});

    		$expenses->where('description', 'like', '%' . $request->descricao . '%');
    	}

    	$entries = $expenses->get()
    		->concat($payments->get());

    	return view('cash-flow.index', [
    		'balance' => bcsub($payments->sum('value'), $expenses->sum('value'), 2),
    		'entries' => CollectionHelper::paginate(
    				$entries->sortByDesc('date'), 10
    			)->appends($request->query())
    	]);
    }

    public function getDetails(Request $request)
    {
    	$entity = $request->entity == 'expense' 
    		? Expense::find($request->id)
    		: Payment::find($request->id);

		$data = $entity instanceof Expense 
			? ['expense' => $entity]
			: ['payment' => $entity];

		return response()->json([
			'message' => 'success',
			'view' => $entity instanceof Expense 
				? view('cash-flow._expense-details', $data)->render()
				: view('cash-flow._payment-details', $data)->render()
		], 200);

    }
}
