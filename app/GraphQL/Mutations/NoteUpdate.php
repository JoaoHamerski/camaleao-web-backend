<?php

namespace App\GraphQL\Mutations;

use App\Models\Note;
use Illuminate\Support\Facades\Validator;

class NoteUpdate
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:notes,id'],
            'text' => ['required']
        ])->validate();

        $note = Note::find($args['id']);
        $note->update($args);

        return $note;
    }
}
