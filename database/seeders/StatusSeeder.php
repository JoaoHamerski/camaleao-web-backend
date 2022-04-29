<?php

namespace Database\Seeders;

use App\Models\AppConfig;

class StatusSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AppConfig::set('app', 'status_available', [8, 10]);
        AppConfig::set('status', 'conclude_status_map', [
            [
                "field" => "print_date",
                "status" => [
                    "5",
                    "7",
                    "9",
                    "10",
                ],
            ],
            [
                "field" => "seam_date",
                "status" => [
                    "7",
                    "10",
                    "9",
                ],
            ],
            [
                "field" => "delivery_date",
                "status" => [
                    "10",
                ],
            ],
        ]);

        AppConfig::set('status', 'update_status_map', [
            [
                "field" => "print_date",
                "status_is" => [
                    "4",
                ],
                "update_to" => "5",
            ],
            [
                "field" => "seam_date",
                "status_is" => [
                    "5",
                ],
                "update_to" => "10",
            ],
            [
                "field" => "delivery_date",
                "status_is" => [],
                "update_to" => null,
            ],
        ]);
    }
}
