<?php

namespace App\GraphQL\Mutations;

use App\Models\Note;
use Illuminate\Support\Facades\Validator;

class NoteDelete
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        Validator::make($args, [
            'id' => ['required', 'exists:notes,id']
        ])->validate();

        $note = Note::find($args['id']);
        $note->delete();

        return $note;
    }
}
