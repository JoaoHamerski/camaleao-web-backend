<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\Traits\SectorTrait;
use App\Models\Sector;
use App\Models\Status;

class SectorCreate
{
    use SectorTrait;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->validator($args)->validate();

        $sector = Sector::create([
            'name' => $data['name'],
            'alias' => $data['alias']
        ]);

        $sector->users()->attach($data['users']);

        Status::whereIn('id', $data['status'])
            ->update(['sector_id' => $sector->id]);

        return $sector;
    }
}
