<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepEduActaNovedadTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_edu_acta_novedad', function(Blueprint $table)
		{
			$table->increments('edu_acta_novedad_id');
                        $table->unsignedInteger('edu_acta_id');
                        $table->unsignedInteger('edu_novedad_id');
                        $table->string('par_identificacion',30);
                        
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
		Schema::drop('sep_edu_acta_novedad');
	}

}