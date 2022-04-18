<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->timestamps();
        });

        DB::table('status')->insert([
            ['id' => 1, 'text' => 'Arte pendente'],
            ['id' => 3, 'text' => 'Pagamento pendente'],
            ['id' => 4, 'text' => 'Produção'],
            ['id' => 5, 'text' => 'Costura'],
            ['id' => 7, 'text' => 'Problemas na produção'],
            ['id' => 8, 'text' => 'Disponível para retirada'],
            ['id' => 9, 'text' => 'Entregue com pagamento pendente'],
            ['id' => 10, 'text' => 'Entregue'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status');
    }
}
