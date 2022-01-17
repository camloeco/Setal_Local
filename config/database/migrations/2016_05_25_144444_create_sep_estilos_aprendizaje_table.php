    <?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepEstilosAprendizajeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_estilos_aprendizaje', function(Blueprint $table)
		{
			$table->increments('est_apr_id');
                        $table->string('est_apr_descripcion',25);
                        
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
	public function down()
	{
		Schema::drop('sep_estilos_aprendizaje');
	}

}
