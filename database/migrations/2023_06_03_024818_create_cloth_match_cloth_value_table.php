<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClothMatchClothValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloth_match_cloth_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cloth_match_id')
                ->nullable()
                ->constrained('cloth_matches')
                ->cascadeOnDelete();

            $table->foreignId('cloth_value_id')
                ->nullable()
                ->constrained('cloth_values')
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
        Schema::dropIfExists('cloth_match_cloth_value');
    }
}
