<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CitiesController extends Controller
{
    public function index()
    {
        return view('cities.index');
    }

    public function show(City $city)
    {
        return view('cities.show', [
            'city' => $city,
            'clients' => $city->clients()->paginate(10)
        ]);
    }

    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        City::create([
            'name' => $request->name,
            'state_id' => $request->state_id
        ]);

        return response()->json([], 201);
    }
    
    public function list(Request $request)
    {
        $cities = City::with(['state', 'branch'])
            ->orderBy('name');

        if ($request->has('page')) {
            $cities = $cities->paginate(10);
        } else {
            $cities = $cities->get();
        }

        if ($request->filled('only_names') && $request->only_names) {
            $cities = $cities->makeHidden([
                'branch_id',
                'state_id',
                'state',
                'branch',
                'created_at',
                'updated_at'
            ]);
        }
        
        return response()->json(['cities' => $cities], 200);
    }

    public function states()
    {
        $states = State::orderBy('name')->get();

        return response()->json(['states' => $states], 200);
    }

    public function patch(Request $request, City $city)
    {
        $this->validator($data = $request->all())
            ->validate();

        $city->update([
            'name' => $data['name'],
            'state_id' => $data['state_id']
        ]);

        return response()->json([], 200);
    }

    public function patchMany(Request $request)
    {
        Validator::make($request->all(), [
            'state_id' => ['required', 'exists:states,id']
        ])->validate();

        City::whereIn('id', $request->cities_ids)->update([
            'state_id' => $request->state_id
        ]);

        return response()->json([], 200);
    }

    public function replace(Request $request, City $city)
    {
        Validator::make($data = $this->getFormattedData($request->all()), [
            'city_id' => [
                'required',
                'exists:cities,id',
                'not_in:' . $city->id
            ]
        ])->validate();

        Client::where('city_id', $city->id)
            ->update(['city_id' => $data['city_id']]);
        
        $city->delete();

        return response()->json([], 200);
    }

    public function getFormattedData(array $data)
    {
        if (isset($data['city_id'])) {
            $data['city_id'] = $data['city_id']['id'];
        }

        return $data;
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'max:100'],
            'state_id' => ['nullable', 'exists:states,id']
        ]);
    }
}
