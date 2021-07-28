<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_id');
            $table->foreignId('user_id');
            $table->boolean('was_quantity_changed')->default(0);
            $table->timestamp('confirmed_at')->nullable();
            $table->decimal('commission_value')->nullable();
            $table->timestamps();

            $table->foreign('commission_id')
                ->references('id')
                ->on('commissions')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('commission_user');
    }
}
