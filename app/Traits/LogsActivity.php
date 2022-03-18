<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Extend\ActivityLogger;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;

trait LogsActivity
{
    use SpatieLogsActivity;

    public static $CREATE_TYPE = 'created';
    public static $UPDATE_TYPE = 'updated';
    public static $DELETE_TYPE = 'deleted';

    public function getDescriptionForEvent(string $eventName): string
    {
        $events = [
            'created' => $this->getCreatedLog(),
            'updated' => $this->getUpdatedLog(),
            'deleted' => $this->getDeletedLog()
        ];

        foreach ($events as $key => $event) {
            if ($key === $eventName) {
                return $event;
            }
        }
    }

    public function getCreatedLog(): string
    {
        return 'created';
    }

    public function getUpdatedLog(): string
    {
        return 'updated';
    }

    public function getDeletedLog(): string
    {
        return 'deleted';
    }

    protected function replaceProps($item, string $search)
    {
        $prop = str_replace($search, '', $item);
        return [$prop => $item];
    }

    public function getDescriptionLog(
        string $type,
        string $placeholderText,
        array $replaceCauser = [],
        array $replaceSubject = [],
        array $replaceAttributes = []
    ): string {
        $causerProps = [];
        $subjectProps = [];
        $attributesProps = [];

        $description = Str::replaceArray(':causer', $replaceCauser, $placeholderText);
        $description = Str::replaceArray(':subject', $replaceSubject, $description);
        $description = Str::replaceArray(':attribute', $replaceAttributes, $description);

        if ($replaceCauser) {
            $causerProps = array_map(
                fn ($item) => $this->replaceProps($item, ':causer.'),
                $replaceCauser
            );
            $causerProps = array_merge(...$causerProps);
        }

        if ($replaceSubject) {
            $subjectProps = array_map(
                fn ($item) => $this->replaceProps($item, ':subject.'),
                $replaceSubject
            );
            $subjectProps = array_merge(...$subjectProps);
        }

        if ($replaceAttributes) {
            $attributesProps = array_map(
                fn ($item) => $this->replaceProps($item, ':attributes.'),
                $replaceAttributes
            );
            $attributesProps = array_merge(...$attributesProps);
        }

        return json_encode(compact(
            'type',
            'description',
            'placeholderText',
            'causerProps',
            'subjectProps',
            'attributesProps'
        ));
    }

    protected static function bootLogsActivity()
    {
        static::eventsToBeRecorded()->each(function ($eventName) {
            return static::$eventName(function (Model $model) use ($eventName) {
                if (!$model->shouldLogEvent($eventName)) {
                    return;
                }

                $description = $model->getDescriptionForEvent($eventName);

                $logName = $model->getLogNameToUse($eventName);

                if ($description == '') {
                    return;
                }

                $attrs = $model->attributeValuesToBeLogged($eventName);

                if ($model->isLogEmpty($attrs) && !$model->shouldSubmitEmptyLogs()) {
                    return;
                }

                $logger = app(ActivityLogger::class)
                    ->useLog($logName)
                    ->performedOn($model)
                    ->withProperties($attrs);

                if (method_exists($model, 'tapActivity')) {
                    $logger->tap([$model, 'tapActivity'], $eventName);
                }

                $logger->log($description);
            });
        });
    }

    public function attributesToBeLogged(): array
    {
        $attributes = [];

        if (isset(static::$logFillable) && static::$logFillable) {
            $attributes = array_merge($attributes, $this->getFillable());
        }

        if ($this->shouldLogUnguarded()) {
            $attributes = array_merge($attributes, array_diff(array_keys($this->getAttributes()), $this->getGuarded()));
        }

        if (isset(static::$logAttributes) && is_array(static::$logAttributes)) {
            $attributes = array_merge($attributes, array_diff(static::$logAttributes, ['*']));

            if (in_array('*', static::$logAttributes)) {
                $attributes = array_merge($attributes, array_keys($this->getAttributes()));
            }
        }

        if (isset(static::$logAttributesToIgnore) && is_array(static::$logAttributesToIgnore)) {
            $attributes = array_diff($attributes, static::$logAttributesToIgnore);
        }

        if (isset(static::$logAlways) && is_array(static::$logAlways)) {
            $attributes = array_merge($attributes, static::$logAlways);
        }

        return $attributes;
    }

    public function attributeValuesToBeLogged(string $processingEvent): array
    {
        if (!count($this->attributesToBeLogged())) {
            return [];
        }

        $properties['attributes'] = static::logChanges(
            $processingEvent == 'retrieved'
                ? $this
                : ($this->exists
                    ? $this->fresh() ?? $this
                    : $this
                )
        );

        $attributes = $properties['attributes'];

        if (static::eventsToBeRecorded()->contains('updated') && $processingEvent == 'updated') {
            $nullProperties = array_fill_keys(array_keys($properties['attributes']), null);

            $properties['old'] = array_merge($nullProperties, $this->oldAttributes);

            $this->oldAttributes = [];
        }

        if ($this->shouldLogOnlyDirty() && isset($properties['old'])) {
            $properties['attributes'] = array_udiff_assoc(
                $properties['attributes'],
                $properties['old'],
                function ($new, $old) {
                    if ($old === null || $new === null) {
                        return $new === $old ? 0 : 1;
                    }

                    return $new <=> $old;
                }
            );
            $properties['old'] = collect($properties['old'])
                ->only(array_keys($properties['attributes']))
                ->all();
        }

        if ($this->hasAlwaysLogAttributes()) {
            $properties['attributes'] = array_merge(
                $properties['attributes'],
                Arr::only($attributes, static::$logAlways)
            );
        }

        return $properties;
    }

    public function hasAlwaysLogAttributes()
    {
        return isset(static::$logAlways)
            && is_array(static::$logAlways)
            && count(static::$logAlways);
    }
}
