<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStatusIdFromOrderStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_status', function (Blueprint $table) {
            $table->dropForeign(['status_id']);

            $table->foreign('status_id')
                ->references('id')
                ->on('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_status', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->foreign('status_id')
                ->references('id')
                ->on('status')
                ->onDelete('cascade');
        });
    }
}
