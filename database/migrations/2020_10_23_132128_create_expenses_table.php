<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_type_id')->nullable();
            $table->foreignId('expense_via_id')->nullable();
            $table->string('description')->nullable();
            $table->double('value', 8, 2)->nullable();
            $table->date('date')->nullable();
            $table->string('receipt_path')->nullable();
            $table->timestamps();

            $table->foreign('expense_type_id')
                ->references('id')
                ->on('expense_types')
                ->onDelete('set null');

            $table->foreign('expense_via_id')
                ->references('id')
                ->on('expense_vias')
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
        Schema::dropIfExists('expenses');
    }
}
