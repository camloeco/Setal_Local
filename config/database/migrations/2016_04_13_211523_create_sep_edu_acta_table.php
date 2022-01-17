<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepEduActaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_edu_acta', function(Blueprint $table)
		{
			$table->increments('edu_acta_id');
                        $table->unsignedInteger('edu_comite_id');
                        
                        $table->string('edu_acta_quorum',1000);
                        $table->string('edu_acta_descargos',2000);
                        $table->string('edu_acta_practicas',2000);
                        $table->string('edu_acta_existencia',2);
                        $table->string('edu_acta_constituye',2);
                        $table->string('edu_acta_normas',2500);
                        $table->string('edu_acta_autor',2);
                        $table->string('edu_acta_grado_resp',30);
                        $table->string('edu_acta_grado_falta',30);
                        $table->string('edu_acta_sancion',2);
			
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
		Schema::drop('sep_edu_acta');
	}

}
