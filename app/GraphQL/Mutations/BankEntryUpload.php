<?php

namespace App\GraphQL\Mutations;

use App\Models\Entry;
use App\Models\BankEntry;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BankEntryUpload
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->validator($args)->validate();
        $filepath = '/bank-entries/' . $data['filename'] . '.json';

        $this->insertEntries($data['json_file'], $data['replace']);
        Storage::put($filepath, $data['json_file']);

        return BankEntry::updateOrCreate([
            'filename' => $data['filename'],
            'path' => $filepath
        ]);
    }

    public function shouldReplaceEntry($entry): bool
    {
        return !Entry::where('bank_uid', $entry['bank_uid'])->exists();
    }

    private function insertEntries($file, bool $isReplace)
    {
        $fields = ['bank_uid', 'value', 'description', 'date', 'via_id'];
        $entries = collect(json_decode($file, true));
        $entries = $entries->map(
            fn ($entry) => Arr::only($entry, $fields)
        );

        if ($isReplace) {
            $entries = $entries->filter(
                fn ($entry) => $this->shouldReplaceEntry($entry)
            );
        }

        $entries = $entries->toArray();

        Validator::make($entries, [
            '*.date' => 'date_format:d/m/Y'
        ])->validate();

        Entry::upsert(
            $entries,
            'bank_uid',
            $fields
        );
    }

    public function validator(array $data)
    {
        $data['filename'] = $this->getFilenameWithoutExtension($data['filename']);

        return Validator::make($data, [
            'replace' => ['required', 'boolean'],
            'filename' => [
                'required',
                !$data['replace']
                    ? 'unique:bank_entries'
                    : null
            ],
            'json_file' => ['required', 'json']
        ], [
            'filename.unique' => 'unique'
        ]);
    }

    public function getFilenameWithoutExtension(string $filename)
    {
        $indexOfExtension = strrpos($filename, '.');

        return substr($filename, 0, $indexOfExtension);
    }
}
