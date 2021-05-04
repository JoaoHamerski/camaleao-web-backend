<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function download()
    {
        $url = glob(storage_path('app/backup/' . config('app.name') . '/*'))[0];
        $filename = basename($url);

        return Storage::disk('backup')->download(
            config('app.name') . '/' . $filename,
            'backup-' . $filename
        );
    }
}
