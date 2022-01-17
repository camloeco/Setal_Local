<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepModulos extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_modulos', function(Blueprint $table)
		{
                    $table->integer('id_modulo')->primary();
                    $table->string('nombre_modulo',100);
                    $table->string('display_modulo',100);
                    $table->string('descripcion_modulo',255);
                    $table->integer('estado_modulo');
                    $table->string('nombre_carpeta_modulo',50);                   
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
		Schema::drop('sep_modulos');
	}

}
