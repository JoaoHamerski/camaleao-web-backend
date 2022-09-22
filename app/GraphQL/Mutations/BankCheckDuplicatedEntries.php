<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\DB;

class BankCheckDuplicatedEntries
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $entriesQuery = static::getEntriesQuery();

        $ids = collect($args['bank_uid']);

        $duplicatedIds = $ids->filter(
            fn ($id) => $entriesQuery->clone()->where('bank_uid', $id)->exists()
        );

        return $duplicatedIds;
    }

    public static function getEntriesQuery()
    {
        $payments = DB::table('payments')
            ->whereNotNull('bank_uid')
            ->select(['id', 'bank_uid']);

        $expenses = DB::table('expenses')
            ->whereNotNull('bank_uid')
            ->select(['id', 'bank_uid']);

        $merged = $payments->unionAll($expenses);

        return DB::table(
            DB::raw("({$merged->toSql()}) AS merged")
        )->mergeBindings($merged);
    }
}
