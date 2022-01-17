<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepPlaneacionFichaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_planeacion_ficha', function(Blueprint $table)
		{
			$table->increments('plf_id');
                        
                        // foreign tabla ficha
			$table->unsignedInteger('fic_numero');
                        
                        // foreign tabla actividad
			$table->unsignedInteger('act_id');
                        
                        $table->string('plf_fecha_inicio',10);
                        $table->string('plf_fecha_fin',10);
                        
                        $table->unsignedInteger('plf_calificacion');
                        
                        // foreign tabla participantes
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
		Schema::drop('sep_planeacion_ficha');
	}

}
