<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepParticipanteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_participante', function(Blueprint $table)
		{
			$table->string('par_identificacion', 30);
                        
                        $table->string('par_nombres', 60);
                        $table->string('par_apellidos', 60);
                        $table->string('par_direccion', 120);
                        $table->string('par_telefono', 30);
                        $table->string('par_correo', 90);
                        $table->unsignedInteger('est_apr_id')->nullable()->default(1);
                        $table->unsignedInteger('rol_id');
                        
			$table->timestamps();
                        
                        $table->primary('par_identificacion');
                        
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sep_participante');
	}

}
