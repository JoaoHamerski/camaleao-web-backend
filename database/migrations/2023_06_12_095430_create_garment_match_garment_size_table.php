<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGarmentMatchGarmentSizeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Armazena os tamanhos da combinação,
         * com o seu respectivo valor adicional por peça
         */
        Schema::create('garment_match_garment_size', function (Blueprint $table) {
            $table->id();

            $table->foreignId('garment_match_id')
                ->constrained('garment_matches')
                ->cascadeOnDelete();

            $table->foreignId('garment_size_id')
                ->nullable()
                ->constrained('garment_sizes')
                ->nullOnDelete();

            // Valor do tamanho para a combinação específica criada.
            $table->decimal('value')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('garment_match_garment_size');
    }
}
