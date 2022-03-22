<?php

namespace App\Extend;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Activitylog\ActivityLogger as SpatieActivityLogger;
use Spatie\Activitylog\Contracts\Activity as ActivityContract;

class ActivityLogger extends SpatieActivityLogger
{
    protected function replacePlaceholders(string $description, ActivityContract $activity): string
    {
        return preg_replace_callback('/:[a-z0-9._-]+/i', function ($match) use ($activity) {
            $match = $match[0];

            $attribute = Str::before(Str::after($match, ':'), '.');

            if ($attribute === 'attributes') {
                $key = Str::replace(':attributes.', '', $match);
                return $activity->properties['attributes'][$key] ?? $match;
            }

            if (!in_array($attribute, ['subject', 'causer', 'properties'])) {
                return $match;
            }

            $propertyName = substr($match, strpos($match, '.') + 1);
            $attributeValue = $activity->$attribute;

            if (is_null($attributeValue)) {
                return $match;
            }

            $attributeValue = $attributeValue->toArray();

            return Arr::get($attributeValue, $propertyName, $match);
        }, $description);
    }
}
