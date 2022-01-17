    <?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepEduLiteralTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_edu_literal', function(Blueprint $table)
		{
			$table->increments('lit_id');
			$table->string('lit_codigo',25);
                        $table->string('lit_descripcion', 1000);
                        $table->string('art_codigo', 25);
			
			
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
		Schema::drop('sep_edu_literal');
	}

}
