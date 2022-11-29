<?php

namespace App\GraphQL\Queries;

use App\GraphQL\Mutations\BankCheckDuplicatedEntries;
use App\Models\BankEntry;
use App\Models\Entry;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BankEntryLoad
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:bank_entries,id']
        ])->validate();

        $bankEntry = BankEntry::find($args['id']);
        $file = $this->getFormattedFile($bankEntry->path);

        return json_encode($file);
    }

    public function getFormattedFile($filepath)
    {
        $file = Storage::get($filepath);
        $entries = collect(json_decode($file));
        $entries = $entries->filter(fn ($fileEntry) => $fileEntry->isVisible);
        $entries->each(function ($fileEntry) {
            $entry = Entry::where('bank_uid', $fileEntry->bank_uid)->first();

            $fileEntry->isDuplicated = !$entry;
            $fileEntry->isCanceled = $entry ? $entry->is_canceled : false;
        });

        return $entries;
    }
}
