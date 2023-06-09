<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClothMatchClothSizeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloth_match_cloth_size', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cloth_match_id')
                ->nullable()
                ->constrained('cloth_matches')
                ->nullOnDelete();

            $table->foreignId('cloth_size_id')
                ->nullable()
                ->constrained('cloth_sizes')
                ->nullOnDelete();

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
        Schema::dropIfExists('cloth_match_cloth_size');
    }
}
