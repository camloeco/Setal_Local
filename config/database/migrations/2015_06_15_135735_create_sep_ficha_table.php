<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepFichaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_ficha', function(Blueprint $table)
		{
			$table->string('fic_numero', 10);
                        
			$table->string('prog_codigo', 10);
			$table->string('cen_codigo', 5);
			$table->string('fic_fecha_inicio', 10);
			$table->string('fic_fecha_fin', 10);
			$table->string('par_identificacion', 15);
			$table->string('fic_estado', 1);
			$table->string('fic_localizacion', 200);
			$table->string('fic_version_matriz',10);
                        $table->string('act_version',10);
                        $table->string('fic_proyecto',100);
                        
			$table->timestamps();
                        
                        $table->primary('fic_numero');
                        
//                        $table->foreign('prog_codigo')
//                                ->references('prog_codigo')
//                                ->on('sep_programa');
//                        
//                        $table->foreign('cen_codigo')
//                                ->references('cen_codigo')
//                                ->on('sep_centro');
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
		Schema::drop('sep_ficha');
	}

}
