<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepProyectoFicha extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_proyecto_ficha', function(Blueprint $table)
		{
			$table->increments('prof_id');
                        
                        // foreign tabla proyecto
			$table->unsignedInteger('pro_id');
                        
                        // foreign tabla ficha
			$table->unsignedInteger('fic_numero');
                        
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
		Schema::drop('sep_proyecto_ficha');
	}

}
