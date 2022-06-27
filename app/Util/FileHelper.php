<?php

namespace App\Util;

use Exception;
use Carbon\Carbon;
use App\Util\Helper;
use Error;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class FileHelper
{

    /**
     * Mapeamento dos campos do banco de dados que armazenam arquivos
     * para o nome dos diretórios em "storage/public"
     */
    protected static $FIELDS_FOLDER_MAP = [
        'art_paths' => 'imagens_da_arte',
        'size_paths' => 'imagens_do_tamanho',
        'payment_voucher_paths' => 'comprovantes',
        'receipt_path' => 'comprovante_vias'
    ];

    /**
     * Retorna o nome do arquivo caso seja uma URL.
     *
     * @param string $value
     * @return string
     */
    public static function getFilenameFromUrl(string $url): string
    {
        return Helper::getLastArrayEl(explode('/', $url));
    }

    public static function imageToBase64(string $path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    /**
     * Retorna apenas o nome do(s) arquivo(s) do valor passado
     *
     * @param array|string $value Valor do campo
     * @return mixed
     */
    public static function getFilesFromField($value)
    {
        if (empty($value)) {
            return [];
        }

        if (is_string($value)) {
            $decodedValue = json_decode($value);

            $value = $decodedValue ?? $value;
        }


        if (is_array($value)) {
            return array_map(function ($item) {
                if (Str::contains($item, '/')) {
                    return self::getFilenameFromUrl($item);
                }

                return $item;
            }, $value);
        }

        if (Str::contains($value, '/')) {
            return self::getFilenameFromUrl($value);
        }

        return $value;
    }

    public static function getFilesURL($files, string $field)
    {
        $baseFileURL = URL::to('/storage/' . self::$FIELDS_FOLDER_MAP[$field]);
        $decodedFiles = json_decode($files);

        if (!Helper::isValidJson($files) && empty($files)) {
            return [];
        }

        if (is_array($decodedFiles)) {
            return array_map(
                fn ($fileURL) => Str::startsWith($fileURL, 'http')
                    ? $fileURL
                    : $baseFileURL . '/' . $fileURL,
                $decodedFiles
            );
        }

        return $baseFileURL . '/' . $files;
    }

    public static function getSecureFilesURL($files, $field)
    {
        $decodedFiles = json_decode($files);

        if (empty($files)) {
            return [];
        }

        return array_map(
            fn ($item) => URL::temporarySignedRoute(
                'images.show',
                now()->addSeconds(10),
                [
                    'filename' => $item,
                    'field' => $field
                ]
            ),
            $decodedFiles
        );
    }

    public static function isBase64($data)
    {
        @list(, $base64) = explode(',', $data);

        return base64_encode(base64_decode($base64, true)) === $base64;
    }

    public static function getOnlyUploadedFileInstances($data, $keys)
    {
        if (!is_array($keys) && isset($data[$keys])) {
            $data[$keys] = Helper::filterInstanceOf(
                $data[$keys],
                UploadedFile::class
            );

            return $data;
        }

        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $data[$key] = Helper::filterInstanceOf(
                    $data[$key],
                    UploadedFile::class
                );
            }
        }

        return $data;
    }

    public static function removeUploadedFileInstances(array $array, $keys)
    {
        if (!is_array($keys)) {
            return Helper::filterInstanceOf(
                $array[$keys],
                UploadedFile::class,
                false
            );
        }

        foreach ($keys as $key) {
            $array[$key] = Helper::filterInstanceOf(
                $array[$key],
                UploadedFile::class,
                false
            );
        }

        return $array;
    }

    public static function getFilepath($field, $removePublic = false)
    {
        return $removePublic
            ? static::$FIELDS_FOLDER_MAP[$field]
            : 'public/' . static::$FIELDS_FOLDER_MAP[$field];

        return null;
    }

    public static function getFilename(string $filename)
    {
        $filename = explode('/', $filename);

        if (count($filename) === 1) {
            return $filename[0];
        }

        return Helper::lastElement($filename);
    }

    /**
     * Cria um novo nome para o arquivo a ser armazenado.
     *
     * @param $file
     * @param $key
     *
     * @return string $filename
     */
    public static function generateFilename($file, $key = null)
    {
        $filename = '';
        $filename .= Carbon::now();
        $filename .= $key ? " ($key)" : '';
        $filename .= '.' . $file->extension();

        return $filename;
    }

    public static function storeFile($file, $path, $key = null)
    {
        return $file->storeAs(
            $path,
            self::generateFilename($file, $key)
        );
    }

    public static function uploadFileToField($file, $field, $key = 1)
    {
        $uploadedFile = $file;
        $path = self::getFilepath($field);

        if (!($file instanceof UploadedFile)) {
            throw new Error('O arquivo precisa ser uma instância de UploadedFile', 500);
        }

        $uploadedFile = self::storeFile($file, $path, $key);

        return self::getFilename($uploadedFile);
    }

    /**
     * Faz o upload de arquivos de um determinado campo pré-definido.
     *
     * @param $files
     * @param $path
     *
     * @return string $paths;
     */
    public static function uploadFilesToField($files, $field)
    {
        $paths = [];

        if (self::getFilepath($field) === null) {
            throw new Exception("There is no folder registered on referenced field.", 500);
        }

        foreach ($files as $key => $file) {
            $paths[] = self::uploadFileToField($file, $field, $key);
        }

        return $paths;
    }

    public static function deleteFile(string $file, string $field = null)
    {
        if (!$field) {
            return Storage::delete($file);
        }

        return Storage::delete(
            self::getFilepath($field) . '/' . self::getFilename($file)
        );
    }

    public static function deleteFiles(array $files, string $field = null)
    {
        if (empty($files)) {
            return;
        }

        if (is_array($files)) {
            foreach ($files as $file) {
                self::deleteFile($file, $field);
            }

            return;
        }

        Storage::delete($files, $field);
    }
}
