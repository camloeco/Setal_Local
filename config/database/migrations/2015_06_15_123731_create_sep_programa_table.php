<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepProgramaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_programa', function(Blueprint $table)
		{
			$table->string('prog_codigo', 10);
			
                        $table->string('prog_nombre', 250);
                        $table->string('prog_pdf', 100);
                        $table->string('prog_matriz_excel', 100);
			
                        $table->timestamps();
                        
                        $table->primary('prog_codigo');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sep_programa');
	}

}
