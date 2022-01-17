<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepControladores extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_controladores', function(Blueprint $table)
		{
                    $table->integer('id_controlador');
                    $table->primary('id_controlador');
                    $table->string('nombre_controlador');
                    $table->string('display_controlador');
                    $table->string('descripcion_controlador');
                    $table->integer('id_modulo');
                    
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
                Schema::drop('sep_controladores');
	}

}
