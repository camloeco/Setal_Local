<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepActividadResultadoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_actividad_resultado', function(Blueprint $table)
		{
			$table->increments('acr_id');
                        
                        $table->unsignedInteger('act_id');
                        $table->unsignedInteger('res_id');
                        $table->unsignedInteger('fas_id');
                        $table->unsignedInteger('acr_duracion');
                        
			$table->timestamps();
                        
//                        $table->foreign('act_id')
//                                ->references('act_id')
//                                ->on('sep_actividad');
//                        
//                        $table->foreign('res_id')
//                                ->references('res_id')
//                                ->on('sep_resultado');
//                        
//                        $table->foreign('par_identificacion')
//                                ->references('par_identificacion')
//                                ->on('sep_participante');
                        
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sep_actividad_resultado');
	}

}
