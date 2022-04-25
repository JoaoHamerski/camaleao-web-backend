<?php

namespace App\Models;

use App\GraphQL\Mutations\ConfigNew;
use App\GraphQL\Mutations\ConfigRemove;
use App\GraphQL\Mutations\ConfigSet;
use App\GraphQL\Queries\ConfigGet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppConfig extends Model
{
    use HasFactory;

    protected $table = 'configs';

    protected $fillable = [
        'name',
        'json'
    ];

    public static function get($name, $key = null, $encoded = false)
    {
        return (new ConfigGet)->__invoke(null, compact('name', 'key', 'encoded'));
    }

    public static function set($name, $key, $value)
    {
        return (new ConfigSet)->__invoke(null, compact('name', 'key', 'value'));
    }

    public static function remove($name, $key)
    {
        return (new ConfigRemove)->__invoke(null, compact('name', 'key'));
    }
}
