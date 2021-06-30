<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClothingTypeOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clothing_type_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->foreignId('clothing_types_id');
            $table->decimal('value');
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('clothing_types_id')
                ->references('id')
                ->on('clothing_types')
                ->onDelete('cascade');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clothing_type_order');
    }
}
