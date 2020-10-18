<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignId('client_id');
            $table->foreignId('status_id')->nullable();
            $table->integer('quantity');
            $table->double('price', 9, 2);
            $table->date('delivery_date')->nullable();
            $table->date('production_date')->nullable();
            $table->longText('art_paths')->nullable();
            $table->longText('size_paths')->nullable();
            $table->longText('payment_voucher_paths')->nullable();
            $table->string('costureira_valor')->nullable();
            $table->boolean('is_closed')->default(0);


            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');

            $table->foreign('status_id')
                ->references('id')
                ->on('status')
                ->onDelete('set null');

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
        Schema::dropIfExists('orders');
    }
}
