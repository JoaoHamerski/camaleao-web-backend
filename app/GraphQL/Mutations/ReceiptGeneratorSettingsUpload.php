<?php

namespace App\GraphQL\Mutations;

use App\Util\Formatter;
use App\Util\FileHelper;
use App\Models\AppConfig;
use App\Util\Helper;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ReceiptGeneratorSettingsUpload
{
    static $FILE_FIELDS = [
        'logo',
        'signature_image'
    ];

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = $this->getFormattedData($args);
        $this->validator($data)->validate();
        $data = $this->uploadFiles($data);

        AppConfig::set(
            'app',
            'receipt_generator_settings',
            json_encode($data)
        );

        return AppConfig::get('app', 'receipt_generator_settings', true);
    }

    public function uploadFiles(array $data)
    {
        foreach (static::$FILE_FIELDS as $field) {
            if ($data[$field] instanceof UploadedFile) {
                $filename = Str::slug($field)
                    . '-'
                    . Str::random(20)
                    . '.'
                    . $data[$field]->extension();

                $data[$field]->storeAs(
                    'public/receipt_settings',
                    $filename
                );

                $data[$field] = $filename;
            }
        }

        return $data;
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'logo' => FileHelper::isBase64($data['logo']) || empty($data['logo'])
                ? ['required', 'file', 'mimetypes:image/*']
                : [],
            'header' => ['required', 'string'],
            'content' => ['required', 'string'],
            'date' => ['required', 'string'],
            'signature_image' => FileHelper::isBase64($data['signature_name']) || empty($data['signature_name'])
                ? ['required', 'file', 'mimetypes:image/*']
                : [],
            'signature_name' => ['required', 'string']
        ]);
    }

    public function getFormattedData(array $data)
    {
        $formatted = (new Formatter($data))
            ->base64ToUploadedFile(['logo', 'signature_image'])
            ->get();

        return $formatted;
    }
}
