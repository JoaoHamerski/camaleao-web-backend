<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClothingTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clothing_types', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->boolean('is_hidden')->default(false);
            $table->integer('order')->nullable();
            $table->timestamps();
        });

        DB::table('clothing_types')->insert([
            ['name' => 'Gola comum', 'key' => 'common_neck', 'order' => 0],
            ['name' => 'Manga longa', 'key' => 'long_sleeve', 'order' => 1],
            ['name' => 'Gola polo', 'key' => 'polo_neck', 'order' => 2],
            ['name' => 'Camisa branca', 'key' => 'white_shirt', 'order' => 3],
            ['name' => 'Outros', 'key' => 'others', 'order' => 4],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clothing_types');
    }
}
