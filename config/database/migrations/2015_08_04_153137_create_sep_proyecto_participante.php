<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepProyectoParticipante extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_proyecto_participante', function(Blueprint $table)
		{
			$table->increments('prop_id');
                        
                        // foreign tabla proyecto
			$table->unsignedInteger('pro_id');
                        
                        // foreign tabla participante
			$table->unsignedInteger('par_identificacion');
                        
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
		Schema::drop('sep_proyecto_participante');
	}

}
