<?php

namespace App\GraphQL\Queries;

use App\GraphQL\Mutations\BankCheckDuplicatedEntries;
use App\Models\BankEntry;
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
        $entriesQuery = BankCheckDuplicatedEntries::getEntriesQuery();

        $file = Storage::get($filepath);
        $entries = collect(json_decode($file));
        $entries = $entries->filter(fn ($entry) => $entry->isVisible);
        $entries->each(function ($entry) use ($entriesQuery) {
            $isDuplicated = $entriesQuery
                ->clone()
                ->where('bank_uid', $entry->bank_uid)
                ->exists();

            $entry->isDuplicated = $isDuplicated;
        });

        return $entries;
    }
}
