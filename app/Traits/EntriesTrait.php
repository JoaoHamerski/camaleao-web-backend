<?php

namespace App\Traits;

use App\Util\Helper;
use App\Models\Entry;
use App\Util\Formatter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

trait EntriesTrait
{
    public function isConfirmable($data): bool
    {
        if (Arr::get($data, 'untied')) {
            return false;
        }

        if (Auth::user()->hasRole('gerencia') && !$this->is_sponsor) {
            return true;
        }

        if (Helper::filled($data, 'bank_uid')) {
            return Entry::where('bank_uid', $data['bank_uid'])->exists();
        }

        return false;
    }

    public function isFromEntries($data)
    {
        return Entry::where('bank_uid', $data['bank_uid'])->exists();
    }

    public function isValidEntry($data)
    {
        $entry = Entry::where('bank_uid', $data['bank_uid'])->first();

        return abs($entry->value) === +$data['value']
            && Formatter::parseDate($entry->date) === $data['date'];
    }
}
