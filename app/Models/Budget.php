<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'client',
        'product',
        'date',
        'settings',
        'product_items'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getReceiptSettings()
    {
        $settings = json_decode(
            AppConfig::get('app', 'receipt_generator_settings', false)
        );

        if (!$settings) {
            return $settings;
        }

        $settings->logo = storage_path('app/public/budget_settings/' . $settings->logo);

        return $settings;
    }

    public static function getFormattedToPDF($budget)
    {
        $budget->product_items = json_decode($budget->product_items);

        $budget->client = "<b>$budget->client</b>";
        $budget->product = "<b>$budget->product</b>";

        $budget->settings = json_decode($budget->settings);
        $budget->settings->logo = storage_path('app/public/budget_settings/' . $budget->settings->logo);

        $budget->settings->content = str_replace('%cliente%', $budget->client, $budget->settings->content);
        $budget->settings->content = str_replace('%produto%', $budget->product, $budget->settings->content);

        $budget->date = Carbon::createFromFormat('Y-m-d', $budget->date)
            ->isoFormat('DD \d\e MMMM \d\e YYYY');

        $budget->settings->date = str_replace('%data%', "<b>$budget->date</b>", $budget->settings->date);

        return $budget;
    }
}
