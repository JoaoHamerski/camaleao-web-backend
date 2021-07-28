<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function set($name, $key, $value)
    {
        $config = Config::where('name', $name)->first();
        $json = json_decode($config->json);

        $collection = collect($json);
        $collection = $collection->merge([$key => $value]);

        $config->update(['json' => $collection->toJson()]);

        return response()->json([], 204);
    }

    public static function get($name, $key)
    {
        $config = Config::where('name', $name)->first();
        $json = json_decode($config->json);

        return $json->{$key} ?? null;
    }
}
