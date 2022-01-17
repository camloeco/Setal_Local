<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepRegionalTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_regional', function(Blueprint $table)
		{
			$table->string('reg_codigo', 4);
			$table->string('reg_nombre', 40);
			$table->timestamps();
                        
                        $table->primary('reg_codigo');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sep_regional');
	}

}
