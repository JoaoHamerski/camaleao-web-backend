<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('abbreviation', 3);
            $table->timestamps();
        });

        DB::table('states')->insert([
            ['name' => 'Acre', 'abbreviation' => 'AC'],
            ['name' => 'Alagoas', 'abbreviation' => 'AL'],
            ['name' => 'Amapá', 'abbreviation' => 'AP'],
            ['name' => 'Amazonas', 'abbreviation' => 'AM'],
            ['name' => 'Bahia', 'abbreviation' => 'BA'],
            ['name' => 'Ceará', 'abbreviation' => 'CE'],
            ['name' => 'Espírito Santo', 'abbreviation' => 'ES'],
            ['name' => 'Goiás', 'abbreviation' => 'GO'],
            ['name' => 'Maranhão', 'abbreviation' => 'MA'],
            ['name' => 'Mato Grosso', 'abbreviation' => 'MT'],
            ['name' => 'Mato Grosso do Sul', 'abbreviation' => 'MS'],
            ['name' => 'Minas Gerais', 'abbreviation' => 'MG'],
            ['name' => 'Pará', 'abbreviation' => 'PA'],
            ['name' => 'Paraíba', 'abbreviation' => 'PB'],
            ['name' => 'Paraná', 'abbreviation' => 'PR'],
            ['name' => 'Pernambuco', 'abbreviation' => 'PE'],
            ['name' => 'Piauí', 'abbreviation' => 'PI'],
            ['name' => 'Rio de Janeiro', 'abbreviation' => 'RJ'],
            ['name' => 'Rio Grande do Norte', 'abbreviation' => 'RN'],
            ['name' => 'Rio Grande do Sul', 'abbreviation' => 'RS'],
            ['name' => 'Rondônia', 'abbreviation' => 'RO'],
            ['name' => 'Roraima', 'abbreviation' => 'RR'],
            ['name' => 'Santa Catarina', 'abbreviation' => 'SC'],
            ['name' => 'São Paulo', 'abbreviation' => 'SP'],
            ['name' => 'Sergipe', 'abbreviation' => 'SE'],
            ['name' => 'Tocantins', 'abbreviation' => 'TO'],
            ['name' => 'Distrito Federal', 'abbreviation' => 'DF'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('states');
    }
}
