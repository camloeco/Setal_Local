<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepHorarioActividadTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_horario_actividad', function(Blueprint $table)
		{
			$table->increments('hoa_id');
                        
                        $table->integer('hoa_lunes');
                        $table->integer('hoa_martes');
                        $table->integer('hoa_miercoles');
                        $table->integer('hoa_jueves');
                        $table->integer('hoa_viernes');
                        $table->integer('hoa_sabado');
                        
                        // foreign planeacion ficha
                        $table->unsignedInteger('plf_id');
                        
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
		Schema::drop('sep_horario_actividad');
	}

}
