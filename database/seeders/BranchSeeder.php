<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Branch;
use App\Models\ShippingCompany;

class BranchSeeder extends BaseSeeder
{
    use ExecuteByProbabilitiesTrait;

    protected static $methodsByProbability = [
        ['hasManyCities', 'chance' => 95],
        ['belongsToCity', 'chance' => 100],
        ['belongsToShippingCompany', 'chance' => 85],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $BRANCHES_QUANTITY = 5;

        Branch::factory()
            ->count($BRANCHES_QUANTITY)
            ->create()
            ->each(function (Branch $branch) {
                $this->executeMethodsByProbability($branch);
            });
    }

    protected function hasManyCities(Branch $branch)
    {
        $cityIds = City::all()->pluck('id');
        $cityIdsToExclude = City::whereNotNull('branch_id')->pluck('id');
        $cityIdsAvailable = $cityIds->diff($cityIdsToExclude)
            ->take($this->faker->numberBetween(1, 5));

        if ($cityIdsAvailable->isEmpty()) {
            return;
        }

        City::whereIn('id', $cityIdsAvailable)->update([
            'branch_id' => $branch->id
        ]);
    }

    protected function belongsToCity(Branch $branch)
    {
        $branch->update([
            'city_id' => City::inRandomOrder()->first()->id
        ]);
    }

    protected function belongsToShippingCompany(Branch $branch)
    {
        $branch->update([
            'shipping_company_id' => ShippingCompany::inRandomOrder()->first()->id
        ]);
    }
}
