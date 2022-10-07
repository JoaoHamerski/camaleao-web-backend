<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'client',
        'product',
        'date',
        'value',
        'filename',
        'has_signature',
        'settings'
    ];

    protected static function booted()
    {
        static::deleting(function ($receipt) {
            @unlink(storage_path('app/receipts/' . $receipt->filename));
        });
    }

    public static function getReceiptSettings()
    {
        $receiptSettings = json_decode(
            AppConfig::get('app', 'receipt_generator_settings', false)
        );

        if (!$receiptSettings) {
            return $receiptSettings;
        }

        return $receiptSettings;
    }

    public function getFilepathAttribute()
    {
        return storage_path("app/receipts/$this->filename");
    }
}
