<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\Traits\GarmentMatchTrait;
use App\Models\GarmentMatch;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

final class GarmentMatchUpdate
{
    use GarmentMatchTrait;
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, ['id' => ['required', 'exists:garment_matches']])
            ->validate();

        $oldMatch = GarmentMatch::find($args['id']);

        $match = (new GarmentMatchCreate)->__invoke($_, $args);
        $match->update(['created_at' => $oldMatch->created_at]);

        if ($oldMatch) {
            $this->deleteOldMatch($oldMatch);
        }

        return $match;
    }

    public function deleteOldMatch($match)
    {
        if (!$match->garments()->count()) {
            $match->forceDelete();
            return;
        }

        $match->delete();
    }
}
