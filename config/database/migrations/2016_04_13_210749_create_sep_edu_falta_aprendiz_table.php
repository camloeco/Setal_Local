<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepEduFaltaAprendizTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_edu_falta_apr', function(Blueprint $table)
		{
			$table->increments('edu_falta_apr_id');
			$table->string('par_identificacion',30);
                        $table->unsignedInteger('edu_falta_id');
                        
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
		Schema::drop('sep_edu_falta_apr');
	}

}
