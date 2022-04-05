<?php

namespace App\GraphQL\Mutations;

use App\Models\City;
use App\Models\Branch;
use App\GraphQL\Traits\BranchTrait;
use App\Util\Helper;
use Illuminate\Support\Facades\Auth;

class BranchUpdate
{
    use BranchTrait;

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $this->validator($args)->validate();

        $branch = Branch::find($args['id']);
        $oldBranch = Branch::with('cities', 'city', 'shippingCompany')->find($branch->id);

        activity()->withoutLogs(function () use ($branch, $args) {
            $branch->update($args);
            $branch
                ->cities()
                ->update(['branch_id' => null]);

            $branch
                ->cities()
                ->saveMany(
                    array_map(
                        fn ($id) => City::find($id),
                        $args['cities_id']
                    )
                );
        });

        $this->logBranchUpdate($branch, $oldBranch);

        return $branch;
    }

    public function logBranchUpdate(Branch $branch, Branch $oldBranch)
    {
        $description = [
            'type' => 'updated',
            'text' => Auth::user()->name . 'alterou os dados da filial ' . $branch->city->name,
            'placeholderText' => ':causer alterou os dados da filial :attribute',
            'causerProps' => ['name' => Auth::user()->name],
            'subjectProps' => [],
            'attributesProps' => [
                'city.name' => $branch->city->name
            ]
        ];

        $attributes = [
            'shippingCompany.name' => $branch->shippingCompany->name,
            'city.name' => $branch->city->name,
            'cities' => $branch->cities->pluck('name')
        ];

        $old = $this->recordOldAttributes($attributes, $branch, $oldBranch);

        $properties = [
            'attributes' => $attributes,
            'old' => $old
        ];

        activity('branches')
            ->causedBy(Auth::user())
            ->performedOn($branch)
            ->withProperties($properties)
            ->log(json_encode($description));
    }

    public function recordOldAttributes($attributes, $branch, $oldBranch)
    {
        unset($attributes['cities']);
        $keys = array_keys($attributes);

        $old = [];

        foreach ($keys as $key) {
            if (data_get($branch, $key) !== data_get($oldBranch, $key)) {
                $old[$key] = data_get($oldBranch, $key);
            }
        }

        $old = $this->recordCitiesChanges($branch, $oldBranch, $old);

        return $old;
    }

    public function recordCitiesChanges($branch, $oldBranch, $old)
    {
        $citiesDiff = Helper::arrayFullDiff(
            $branch->cities->pluck('name'),
            $oldBranch->cities->pluck('name')
        );

        if (count($citiesDiff)) {
            $old['cities'] = $oldBranch->cities->pluck('name');
        }

        return $old;
    }
}
