<?php

namespace App\GraphQL\Mutations;

use App\Models\Entry;
use Illuminate\Support\Facades\Validator;

class EntryCancel
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'uid' => ['required', 'exists:entries,bank_uid']
        ])->validate();

        $entry = Entry::where('bank_uid', $args['uid'])->first();

        $entry->update(['is_canceled' => true]);

        return $entry;
    }
}
