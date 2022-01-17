<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepOpcionEtapaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_opcion_etapa', function(Blueprint $table)
		{
			$table->increments('ope_id');
			
                        $table->string('ope_descripcion', 40);
			
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
		Schema::drop('sep_opcion_etapa');
	}

}
