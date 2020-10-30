<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameExpenseViasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenses', function(Blueprint $table) {
            $table->dropForeign(['expense_via_id']);
        });

        Schema::rename('expense_vias', 'vias');

        Schema::table('expenses', function(Blueprint $table) {
            $table->foreign('expense_via_id')
                ->references('id')
                ->on('vias')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vias');
    }
}
