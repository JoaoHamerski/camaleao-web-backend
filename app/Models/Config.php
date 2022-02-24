<?php

namespace App\Models;

use App\GraphQL\Mutations\ConfigNew;
use App\GraphQL\Mutations\ConfigRemove;
use App\GraphQL\Mutations\ConfigSet;
use App\GraphQL\Queries\ConfigGet;
use Error;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Config extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'json'
    ];

    public static function new($name, $key = null, $value = null)
    {
        return (new ConfigNew)->__invoke(null, compact('name', 'key', 'value'));
    }

    public static function get($name, $key = null)
    {
        return (new ConfigGet)->__invoke(null, compact('name', 'key'));
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
