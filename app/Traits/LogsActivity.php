<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;

trait LogsActivity
{
    use SpatieLogsActivity;

    public function getDescriptionLog($type, $causer, $subject, $text)
    {
        $causerProp = str_replace(':causer.', '', $causer);
        $subjectProp = str_replace(':subject.', '', $subject);

        return json_encode(compact(
            'type',
            'causer',
            'causerProp',
            'subject',
            'subjectProp',
            'text'
        ));
    }
}
