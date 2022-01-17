<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepFunciones extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */ 
	public function up()
	{
		Schema::create('sep_funciones', function(Blueprint $table)
		{
                    $table->integer('id_funcion');
                    $table->primary('id_funcion');
                    $table->string('nombre_funcion');
                    $table->string('display_funcion');
                    $table->integer('id_controlador');
                    $table->string('descripcion_funcion');                   
                    
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
                Schema::drop('sep_funciones');
	}


}
