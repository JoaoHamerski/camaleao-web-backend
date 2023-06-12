<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGarmentValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Armazena os intervalos de valores dos vestuários.
         */
        Schema::create('garment_values', function (Blueprint $table) {
            $table->id();
            $table->decimal('value')->nullable();
            $table->unsignedSmallInteger('start')->nullable();
            $table->unsignedSmallInteger('end')->nullable();
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
        Schema::dropIfExists('garment_values');
    }
}
