    <?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepEduArticuloTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_edu_articulo', function(Blueprint $table)
		{
			$table->string('art_codigo',25)->primary();
			
                        $table->string('art_descripcion', 1000);
                        $table->string('cap_codigo', 25);
			
			
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
		Schema::drop('sep_edu_articulo');
	}

}
