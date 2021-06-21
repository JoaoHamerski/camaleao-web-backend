<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivitiesController extends Controller
{
    public function index(Request $request)
    {
        $activities = Activity::query();

        if ($request->filled('usuario')) {
            $activities->where('causer_id', $request->usuario);
        }

        if ($request->filled('entidade')) {
            $activities->where('subject_type', $request->entidade);
        }

        if ($request->filled('data')) {
            $data = \Carbon\Carbon::createFromFormat('d/m/Y', $request->data);

            $activities->whereDate('created_at', $data);
        }

        return view('activities.index', [
            'activities' => $activities->latest()->paginate(10)->appends($request->query()),
            'entities' => Activity::all()->pluck('subject_type')->unique(),
            'users' => User::all()
        ]);
    }
}
