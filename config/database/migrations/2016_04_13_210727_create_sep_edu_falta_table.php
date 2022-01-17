<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepEduFaltaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_edu_falta', function(Blueprint $table)
		{
			$table->increments('edu_falta_id');
			$table->string('edu_falta_descripcion',5000);
			$table->string('edu_falta_evidencia',5000);
			$table->string('par_identificacion',30);
			$table->string('par_identificacion_coordinador',30);
			$table->string('edu_falta_fecha',10);
			$table->string('edu_falta_calificacion',20);
                        
                        $table->unsignedInteger('edu_tipo_falta_id');
                        $table->unsignedInteger('edu_est_id');
                        
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
		Schema::drop('sep_edu_falta');
	}

}