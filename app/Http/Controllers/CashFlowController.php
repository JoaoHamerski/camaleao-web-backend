<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Payment;
use App\Util\Formatter;
use Illuminate\Http\Request;
use App\Queries\OrdersRequest;
use App\Util\CollectionHelper;
use App\Queries\ExpensesRequest;
use App\Queries\PaymentsRequest;
use App\Http\Resources\ExpenseResource;
use App\Http\Resources\PaymentResource;
use Illuminate\Support\Facades\Validator;

class CashFlowController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::query();
        $payments = Payment::where('is_confirmed', true);
        $data = [];

        if ($request->hasAny(['start_date', 'end_date'])) {
            $data = $this->requestQuery($request, $data);
        } else {
            $data['entries'] = $this->getEntries($expenses, $payments);
        }

        $data['entries'] = CollectionHelper::paginate($data['entries'], 10);

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function getEntries($expenses, $payments)
    {
        $payments = PaymentResource::collection($payments->get());
        $expenses = ExpenseResource::collection($expenses->get());

        return $payments->concat($expenses)
            ->sortByDesc('created_at')
            ->values();
    }

    public function requestQuery(Request $request, $data)
    {
        $payments = Payment::where('is_confirmed', true);

        $data = Formatter::parse($request->all(), [
            'parseDate' => ['start_date', 'end_date']
        ]);

        Validator::make($data, [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after:start_date']
        ])->validate();

        $data['start_date'] = Carbon::createFromFormat(
            'Y-m-d',
            $data['start_date']
        )->toDateString();

        $data['end_date'] = $request->filled('end_date')
            ? Carbon::createFromFormat('Y-m-d', $data['end_date'])
            : (new Carbon($data['start_date']))->addDays(1);

        $data['end_date'] = $data['end_date']->toDateString();

        $expenses = ExpensesRequest::query($request, ['data' => $data]);
        $payments = PaymentsRequest::query($request, ['data' => $data, 'query' => $payments]);

        $ordersCreatedQuery = OrdersRequest::query($request, ['data' => $data]);
        $ordersClosedQuery = OrdersRequest::query(
            $request,
            ['data' => $data, 'merge' => ['order' => 'is_closed']]
        );

        $ordersUniqueQuery = OrdersRequest::query(
            $request,
            ['data' => $data, 'merge' => ['order' => 'unique']]
        );

        $data['orders']['created'] = $ordersCreatedQuery->count();
        $data['orders']['created_quantity'] = $ordersCreatedQuery->sum('quantity');

        $data['orders']['closed'] = $ordersClosedQuery->count();
        $data['orders']['closed_quantity'] = $ordersClosedQuery->sum('quantity');

        $data['orders']['unique'] = $ordersUniqueQuery->count();
        $data['orders']['unique_quantity'] = $ordersUniqueQuery->sum('quantity');

        $data['balance'] = bcsub(
            $payments->sum('value'),
            $expenses->sum('value'),
            2
        );

        $data['entries'] = $this->getEntries($expenses, $payments);

        return $data;
    }
}
