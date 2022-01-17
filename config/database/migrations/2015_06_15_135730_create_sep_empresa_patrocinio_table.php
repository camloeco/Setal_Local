<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepEmpresaPatrocinioTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_empresa_patrocinio', function(Blueprint $table)
		{
			$table->increments('emp_id');
                        
			$table->string('par_identificacion', 30);
			$table->string('emp_nombre', 60);
			$table->string('emp_direccion', 120);
			$table->string('emp_telefono', 20);
			$table->string('emp_correo', 60);
			$table->string('emp_contacto', 60);
			
                        $table->timestamps();
                        
//                        $table->foreign('par_identificacion')
//                                ->references('par_identificacion')
//                                ->on('sep_participante');
			
                        
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sep_empresa_patrocinio');
	}

}
