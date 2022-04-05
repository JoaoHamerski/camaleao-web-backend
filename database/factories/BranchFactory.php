<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\City;
use App\Models\ShippingCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    use FactoryByProbabilitiesTrait;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Branch::class;

    protected static $methodsByProbability = [
        ['hasManyCities', 'chance' => 95],
        ['belongsToCity', 'chance' => 100],
        ['belongsToShippingCompany', 'chance' => 85],
    ];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Branch $branch) {
            $this->executeMethodsByProbability($branch);
        });
    }

    protected function hasManyCities(Branch $branch)
    {
        $cityIds = City::all()->pluck('id');
        $cityIdsToExclude = City::whereNotNull('branch_id')->pluck('id');
        $cityIdsAvaliable = $cityIds->diff($cityIdsToExclude)
            ->take($this->faker->numberBetween(1, 5));

        if ($cityIdsAvaliable->isEmpty()) {
            return;
        }

        City::whereIn('id', $cityIdsAvaliable)->update([
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
