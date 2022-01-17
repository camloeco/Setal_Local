<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepResultadoTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sep_resultado', function(Blueprint $table) {
            $table->increments('res_id');

            $table->string('res_nombre', 500);
            $table->string('com_codigo', 25);
            $table->string('act_version', 10);

            $table->timestamps();


//                        $table->foreign('com_id')
//                                ->references('com_id')
//                                ->on('sep_competencia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('sep_resultado');
    }

}
