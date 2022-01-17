    <?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSepEduFaltaLitTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sep_edu_falta_lit', function(Blueprint $table)
		{
			$table->increments('fali_id');
			
                        $table->unsignedInteger('edu_falta_id');
                        $table->unsignedInteger('lit_id');
			
			
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
		Schema::drop('sep_edu_falta_lit');
	}

}
