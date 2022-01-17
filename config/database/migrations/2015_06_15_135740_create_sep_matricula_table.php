<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepMatriculaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_matricula', function(Blueprint $table)
		{
			$table->increments('mat_id');
                        
			$table->string('fic_numero', 10);
			$table->string('par_identificacion', 30);
                        
			$table->unsignedInteger('est_id');
                        $table->string('mat_fecha_fin_practica', 10);
                        
			$table->timestamps();
                        
//                        $table->foreign('par_identificacion')
//                                ->references('par_identificacion')
//                                ->on('sep_participante');
//                        
//                        $table->foreign('fic_numero')
//                                ->references('fic_numero')
//                                ->on('sep_ficha');
//                        
//                        $table->foreign('est_id')
//                                ->references('est_id')
//                                ->on('sep_estado');
                        
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sep_matricula');
	}

}
