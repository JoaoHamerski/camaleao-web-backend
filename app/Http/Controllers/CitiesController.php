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
    
    public function cities()
    {
        $cities = City::with('state')->orderBy('name')->get();

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
        Validator::make($request->all(), [
            'city_id' => ['required', 'exists:cities,id']
        ])->validate();

        Client::where('city_id', $city->id)
            ->update(['city_id' => $request->city_id]);
        
        $city->delete();

        return response()->json([], 200);
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'max:100'],
            'state_id' => ['nullable', 'exists:states,id']
        ]);
    }
}
