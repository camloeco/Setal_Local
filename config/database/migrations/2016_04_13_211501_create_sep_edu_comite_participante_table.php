<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepEduComiteParticipanteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_edu_com_par', function(Blueprint $table)
		{
			$table->increments('edu_com_par_id');
                        $table->unsignedInteger('edu_comite_id');
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
		Schema::drop('sep_edu_com_par');
	}

}
