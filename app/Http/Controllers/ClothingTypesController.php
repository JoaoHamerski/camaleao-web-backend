<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClothingTypeResource;
use App\Util\Formatter;
use Illuminate\Support\Str;
use App\Models\ClothingType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ClothingTypesController extends Controller
{
    public function index(Request $request)
    {
        $clothingTypes = ClothingType::query();

        if ($request->filled('hidden')) {
            $isHidden = $request->hidden === 'true' ? 1 : 0;

            $clothingTypes = ClothingType::where('is_hidden', $isHidden);
        }

        return ClothingTypeResource::collection(
            $clothingTypes->orderBy('order')->get()
        );
    }

    public function store(Request $request)
    {
        $this->validator(
            $data = $this->getFormattedData($request->all())
        )->validate();

        ClothingType::create([
            'name' => $data['name'],
            'key' => $data['key']
        ]);

        return response()->json([], 201);
    }

    public function update(Request $request, ClothingType $clothingType)
    {
        Validator::make($request->all(), [
            'name' => [
                'required',
                'min:3',
                'max:30',
                Rule::unique('clothing_types')->ignore($clothingType->id)
            ],
        ])->validate();

        $clothingType->update([
            'name' => $request->name
        ]);

        return response()->json([], 204);
    }

    public function changeComission(Request $request, ClothingType $clothingType)
    {
        $data = [];
        if ($request->filled('value')) {
            $data['value'] = Formatter::parseCurrencyBRL($request->value);
        }

        Validator::make($data, [
            'value' => ['required', 'numeric']
        ])->validate();


        $clothingType->update([
            'commission' => $data['value']
        ]);

        return response()->json([], 200);
    }

    public function toggleHide(ClothingType $clothingType)
    {
        $clothingType->is_hidden = !$clothingType->is_hidden;
        $clothingType->save();

        return response()->json([], 204);
    }

    private function getFormattedData(array $data)
    {
        if (isset($data['name']) && !empty($data['name'])) {
            $data['key'] = Str::slug($data['name']);
            $data['key'] = str_replace('-', '_', $data['key']);
        }

        return $data;
    }

    private function validator(array $data)
    {
        return Validator::make($data, [
            'name' => [
                'required',
                'min:3',
                'max:30',
                'unique:clothing_types,name'
            ],
            'key' => 'unique:clothing_types,key'
        ], $this->errorMessages());
    }

    private function errorMessages()
    {
        return [
            'name.max' => 'O nome deve ter menos de :max caracteres.',
            'name.min' => 'O nome deve ter mais de :min caracteres.',
            'name.unique' => 'Esse nome já está cadastrado.',
            'key.unique' => 'Ocorreu um erro no cadastro, por favor, tente outro nome.'
        ];
    }

    public function updateOrder(Request $request)
    {
        Validator::make($request->all(), [
            'newIndex' => ['required', 'numeric'],
            'oldIndex' => ['required', 'numeric']
        ])->validate();

        $clothingOld = ClothingType::where('order', $request->oldIndex)
            ->first();

        $clothingNew = ClothingType::where('order', $request->newIndex)
            ->first();

        $clothingOld->order = $request->newIndex;
        $clothingNew->order = $request->oldIndex;

        $clothingOld->save();
        $clothingNew->save();

        return response()->json([], 200);
    }

    public function list(Request $request)
    {
    }
}
