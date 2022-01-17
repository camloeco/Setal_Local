<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepProyectoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_proyecto', function(Blueprint $table)
		{
			$table->increments('pro_id');
			
                        $table->string('pro_nombre', 250);
                        $table->binary('pro_problema');
                        $table->binary('pro_justificacion');
                        $table->string('pro_obj_general', 200);
                        $table->binary('pro_obj_especifico');
                        
			$table->timestamps();
                        
//                        $table->foreign('fic_numero')
//                                ->references('fic_numero')
//                                ->on('sep_ficha');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sep_proyecto');
	}

}
