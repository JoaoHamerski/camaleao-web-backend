<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClothClothSizeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloth_cloth_size', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cloth_id')
                ->constrained('clothes')
                ->cascadeOnDelete();

            $table->foreignId('size_id')
                ->nullable()
                ->constrained('cloth_sizes')
                ->nullOnDelete();

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
        Schema::dropIfExists('cloth_cloth_size');
    }
}
