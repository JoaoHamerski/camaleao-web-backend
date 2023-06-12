<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGarmentMatchGarmentValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Pivô para armazenar os intervalos de valores
         * de uma combinação de vestuário.
         */
        Schema::create('garment_match_garment_value', function (Blueprint $table) {
            $table->id();

            $table->foreignId('garment_match_id')
                ->nullable()
                ->constrained('garment_matches')
                ->cascadeOnDelete();

            $table->foreignId('garment_value_id')
                ->nullable()
                ->constrained('garment_values')
                ->cascadeOnDelete();

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
        Schema::dropIfExists('garment_match_garment_value');
    }
}
