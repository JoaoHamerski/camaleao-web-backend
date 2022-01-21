<?php

namespace App\Util;

use Exception;
use Carbon\Carbon;
use App\Util\Helper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
    /**
     * Mapeamento dos campos do banco de dados que armazenam arquivos
     * para o nome dos diretórios em "storage/public"
     */
    protected static $FIELDS_MAP = [
        'art_paths' => 'imagens_da_arte',
        'size_paths' => 'imagens_do_tamanho',
        'payment_voucher_paths' => 'comprovantes',
        'receipt_path' => 'comprovantes_vias'
    ];

    public static function getFilesURL($files, string $field)
    {
        $baseFileURL = URL::to('/storage/' . self::$FIELDS_MAP[$field]);
        $decodedFiles = json_decode($files);

        if (!Helper::isValidJson($files) && empty($files)) {
            return null;
        }

        if (is_array($decodedFiles)) {
            return array_map(function ($file) use ($baseFileURL) {
                return $baseFileURL . '/' . $file;
            }, $decodedFiles);
        }

        return $baseFileURL . '/' . $files;
    }
    /**
     * Mapeamento do nome dos campos para nome dos diretórios
     * que são armazenados os arquivos do sistema.
     */
    protected static $FIELDS_FOLDER_MAP = [
        'art_paths' => 'imagens_da_arte',
        'size_paths' => 'imagens_do_tamanho',
        'payment_voucher_paths' => 'comprovantes',
        'receipt_path' => 'comprovantes_vias'
    ];

    public static function isBase64($data)
    {
        @list(, $base64) = explode(',', $data);

        return base64_encode(base64_decode($base64, true)) === $base64;
    }

    public static function getOnlyUploadedFileInstances($data, $keys)
    {
        if (!is_array($keys)) {
            $data[$keys] = Helper::filterInstanceOf(
                $data[$keys],
                UploadedFile::class
            );

            return $data;
        }

        foreach ($keys as $key) {
            $data[$key] = Helper::filterInstanceOf(
                $data[$key],
                UploadedFile::class
            );
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

        if ($file instanceof UploadedFile) {
            $uploadedFile = self::storeFile($file, $path, $key);
        }

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
