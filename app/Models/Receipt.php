<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'client',
        'product',
        'date',
        'value',
        'filename',
        'has_signature'
    ];

    public static function getReceiptSettings()
    {
        $receiptSettings = json_decode(
            AppConfig::get('app', 'receipt_generator_settings', false)
        );

        if (!$receiptSettings) {
            return $receiptSettings;
        }

        $receiptSettings->logo = str_replace(
            'public/',
            'app/public/',
            $receiptSettings->logo
        );

        $receiptSettings->signature_image = str_replace(
            'public/',
            'app/public/',
            $receiptSettings->signature_image
        );

        return $receiptSettings;
    }

    public function getFilepathAttribute()
    {
        return storage_path("app/receipts/$this->filename");
    }
}
