<?php

namespace Database\Seeders;

use App\Models\Status;
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
        $this->populateStatusTable();
        $this->setAvailableStatus();
        $this->setConcludedStatus();
        $this->setUpdateStatusMap();
    }

    private function populateStatusTable()
    {
        $status = [
            ['id' => 22, 'text' => 'Cadastrado', 'sector_id' => 1],
            ['text' => 'Analisado', 'sector_id' => 2],
            ['text' => 'Exportado', 'sector_id' => 3],
            ['text' => 'Impresso', 'sector_id' => 4],
            ['text' => 'Estampado', 'sector_id' => 5],
            ['text' => 'Costurado e Embalado', 'sector_id' => 6],
            ['text' => 'Disponível para retirada', 'sector_id' => null],
            ['text' => 'Entregue com pagamento pendente', 'sector_id' => null],
            ['text' => 'Entregue', 'sector_id' => null],
            ['text' => 'Enviado', 'sector_id' => null],
            ['text' => 'Concluído pelo sistema', 'sector_id' => null],
        ];

        Status::factory()
            ->count(count($status))
            ->sequence(fn ($sequence) => [
                'order' => $sequence->index,
                'text' => $status[$sequence->index]['text'],
                'sector_id' => $status[$sequence->index]['sector_id']
            ])
            ->create();
    }

    private function setAvailableStatus()
    {
        AppConfig::set('app', 'status_available', [8, 10]);
    }

    private function setConcludedStatus()
    {
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
    }

    private function setUpdateStatusMap()
    {
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
