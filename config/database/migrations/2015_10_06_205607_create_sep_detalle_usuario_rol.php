<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepDetalleUsuarioRol extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_detalle_usuario_rol', function(Blueprint $table)
		{
                    $table->integer('id_rol');
                    $table->string('id_usuario',30);
                    $table->primary(['id_rol', 'id_usuario']);
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
		Schema::drop('sep_detalle_usuario_rol');
	}

}
