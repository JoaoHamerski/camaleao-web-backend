<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    private $filepath;

    public function __construct()
    {

    }

    private function latestBackupFilepath()
    {
        $backupFolder = str_replace(' ', '-', config('app.name'));
        $filepaths  = glob(storage_path('app/backup/' . $backupFolder . '/*'));
        $filepath = array_values(array_slice($filepaths, -1))[0];
        $filename = basename($filepath);

        return $backupFolder . '/' . $filename;
    }

    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('', 'KB', 'MB', 'GB', 'TB');

        $result = round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];

        return str_replace('.', ',', $result);
    }

    public function index()
    {
        $size = Storage::disk('backup')->size(
            $this->latestBackupFilepath()
        );

        $lastModified = Storage::disk('backup')->lastModified(
            $this->latestBackupFilepath()
        );

        return view('backup.index', [
            'size' => $this->formatBytes($size),
            'lastModified' => $lastModified
        ]);
    }

    public function download()
    {
        return Storage::disk('backup')->download(
            $this->latestBackupFilepath()
        );
    }
}
