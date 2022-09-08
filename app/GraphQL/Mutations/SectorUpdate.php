<?php

namespace App\GraphQL\Mutations;

use App\Models\Sector;
use App\Models\Status;
use App\GraphQL\Traits\SectorTrait;

class SectorUpdate
{
    use SectorTrait;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->validator($args)->validate();

        $sector = Sector::find($args['id']);
        $sector->update(['name' => $args['name']]);

        if (isset($data['users'])) {
            $sector->users()->sync($data['users']);
        }

        Status::whereIn('id', $sector->status->pluck('id')->toArray())
            ->update(['sector_id' => null]);

        $sector->status()->saveMany(
            Status::whereIn('id', $data['status'])->get()
        );

        return $sector->fresh();
    }
}
