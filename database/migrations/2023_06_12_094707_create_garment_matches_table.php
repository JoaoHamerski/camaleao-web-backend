<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGarmentMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Armazena uma combinação de vestuários.
         * Caso o valor o valor seja único, usa-se o campo "unique_value",
         * Caso o valor seja baseado em 1 ou muitos intervalos de quantidades, usa-se o pivô "garment_match_garment_value"
         */
        Schema::create('garment_matches', function (Blueprint $table) {
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

            $table->unique(
                ['model_id', 'material_id', 'neck_type_id', 'sleeve_type_id'],
                'unique_match'
            );

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
        Schema::dropIfExists('garment_matches');
    }
}
