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
            ['text' => 'Atendimento inicial', 'sector_id' => 1],
            ['text' => 'Arte pendente', 'sector_id' => 2],
            ['text' => 'Arte em análise', 'sector_id' => 1],
            ['text' => 'Atendimento em espera', 'sector_id' => 1],
            ['text' => 'Tamanhos pendentes', 'sector_id' => 3],
            ['text' => 'Pagamento pendente', 'sector_id' => 3],
            ['text' => 'Exportação pendente', 'sector_id' => 4],
            ['text' => 'Impressão', 'sector_id' => 5],
            ['text' => 'Corte', 'sector_id' => 5],
            ['text' => 'Estampa', 'sector_id' => 5],
            ['text' => 'Em análise na gerencia', 'sector_id' => 8],
            ['text' => 'Costura', 'sector_id' => 6],
            ['text' => 'Estampa incompleta', 'sector_id' => 6],
            ['text' => 'Disponível para retirada', 'sector_id' => 7],
            ['text' => 'Entregue com pagamento pendente', 'sector_id' => 7],
            ['text' => 'Entregue', 'sector_id' => 7],
            ['text' => 'Prontas c/ pendencia', 'sector_id' => 7],
            ['text' => 'Enviado', 'sector_id' => 7],
            ['text' => 'Pedido cancelado', 'sector_id' => null],
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
