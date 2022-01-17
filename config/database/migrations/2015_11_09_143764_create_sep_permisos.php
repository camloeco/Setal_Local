<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepPermisos extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('sep_permisos', function(Blueprint $table)
            {
                $table->increments('id_permiso');
                $table->integer('id_rol');
                $table->integer('id_funcion');
               
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
            Schema::drop('sep_permisos');
	}

}
