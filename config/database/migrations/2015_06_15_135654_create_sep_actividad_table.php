<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepActividadTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sep_actividad', function(Blueprint $table) {
            $table->increments('act_id');

            $table->string('act_descripcion', 2000);
            $table->string('prog_codigo', 10);
            $table->unsignedInteger('fas_id');


            $table->string('act_version', 10);

            $table->string('act_estado', 1);

            $table->timestamps();

//                        $table->foreign('prog_codigo')
//                                ->references('prog_codigo')
//                                ->on('sep_programa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('sep_actividad');
    }

}
