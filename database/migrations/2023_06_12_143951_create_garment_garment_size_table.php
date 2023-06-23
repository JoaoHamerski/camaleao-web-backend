<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGarmentGarmentSizeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Pivô para armazenar os tamanhos cadastrados da roupa,
         * diferente de "garment_match_garment_size" que armazena
         * os tamanhos da combinação e seu valor adicional fixo.
         */
        Schema::create('garment_garment_size', function (Blueprint $table) {
            $table->id();

            $table->foreignId('garment_id')
                ->nullable()
                ->constrained('garments')
                ->nullOnDelete();

            $table->foreignId('garment_size_id')
                ->nullable()
                ->constrained('garment_sizes')
                ->nullOnDelete();

            // Quantidade de roupas
            $table->unsignedSmallInteger('quantity');

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
        Schema::dropIfExists('garment_garment_size');
    }
}
