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
            $table->timestamps();
        });

        DB::table('clothing_types')->insert([
            ['name' => 'Gola comum', 'key' => 'common_neck'],
            ['name' => 'Manga longa', 'key' => 'long_sleeve'],
            ['name' => 'Gola polo', 'key' => 'polo_neck'],
            ['name' => 'Camisa branca', 'white_shirt'],
            ['name' => 'Outros', 'key' => 'others'],
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
