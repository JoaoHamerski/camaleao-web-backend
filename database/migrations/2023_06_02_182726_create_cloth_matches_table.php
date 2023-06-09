<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClothMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // SOFT DELETE ISSO
        Schema::create('cloth_matches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('model_id')
                ->nullable()
                ->constrained('models')
                ->nullOnDelete();

            $table->foreignId('material_id')
                ->nullable()
                ->constrained('materials')
                ->nullOnDelete();

            $table->foreignId('neck_type_id')
                ->nullable()
                ->constrained('neck_types')
                ->nullOnDelete();

            $table->foreignId('sleeve_type_id')
                ->nullable()
                ->constrained('sleeve_types')
                ->nullOnDelete();

            $table->decimal('unique_value')->nullable();

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
        Schema::dropIfExists('clothes_matches');
    }
}
