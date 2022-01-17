<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepEtapaPracticaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_etapa_practica', function(Blueprint $table)
		{
			$table->increments('etp_id');
			
                        $table->string('par_identificacion', 30 );
                        $table->unsignedInteger('ope_id');
                        $table->string('etp_fecha_registro', 10);
			
                        $table->timestamps();
                        
//                        $table->foreign('par_identificacion')
//                                ->references('par_identificacion')
//                                ->on('sep_participante');
//                        
//                        $table->foreign('ope_id')
//                                ->references('ope_id')
//                                ->on('sep_opcion_etapa');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sep_etapa_practica');
	}

}
