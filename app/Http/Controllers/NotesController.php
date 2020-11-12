<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotesController extends Controller
{
    public function store(Client $client, Order $order, Request $request)
    {
    	$validator = Validator::make($request->all(), [
    		'order_note' => 'required'
    	]);

    	if ($validator->fails()) {
    		return response()->json([
    			'message' => 'error',
    			'errors' => $validator->errors()
    		], 422);
    	}

    	$note = $order->notes()->create([
    		'text' => $request->order_note
    	]);

    	return response()->json([
    		'message' => 'success',
    		'noteListItem' => view('orders.partials.note-list-item', compact('note'))->render(),
            'countNotes' => $order->notes->count()
    	], 200);
    }

    public function destroy(Client $client, Order $order, Note $note)
    {
        $note->delete();

        return response()->json([
            'message' => 'success',
            'countNotes' => $order->notes->count()
        ], 200);
    }
}
