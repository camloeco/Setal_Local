<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepEduComiteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_edu_comite', function(Blueprint $table)
		{
			$table->increments('edu_comite_id');
                        $table->unsignedInteger('edu_falta_id');
			$table->unsignedInteger('edu_est_id');
			$table->unsignedInteger('edu_tipo_com_id');
                        
                        $table->string('edu_comite_hora',10);
                        $table->string('edu_comite_fecha',10);
                        $table->string('edu_comite_direccion',30);
                        
                        
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
		Schema::drop('sep_edu_comite');
	}

}
