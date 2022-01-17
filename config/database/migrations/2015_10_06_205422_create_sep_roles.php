<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepRoles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_roles', function(Blueprint $table)
		{
                    $table->integer('id_rol')->primary();
                    $table->string('nombre_rol',50);
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
		Schema::drop('sep_roles');
	}

}
