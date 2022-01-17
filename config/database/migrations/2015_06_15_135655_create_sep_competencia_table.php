    <?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepCompetenciaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_competencia', function(Blueprint $table)
		{
			$table->string('com_codigo',25)->primary();
			
                        $table->string('com_nombre', 500);
			$table->integer('com_horas');
			$table->string('prog_codigo', 10);
                        $table->string('act_version', 10);
			
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
		Schema::drop('sep_competencia');
	}

}
