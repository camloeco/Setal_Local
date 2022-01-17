<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepCentroTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_centro', function(Blueprint $table)
		{
			$table->string('cen_codigo', 5);
                        
			$table->string('cen_nombre', 200);
			$table->string('cen_sigla', 10);
			$table->string('reg_codigo', 4);
                        
			$table->timestamps();
                        
                        $table->primary('cen_codigo');
                        
//                        $table->foreign('reg_codigo')
//                                ->references('reg_codigo')
//                                ->on('sep_regional');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sep_centro');
	}

}
