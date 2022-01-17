<?php namespace App\Http\Models\Modules\Users;

use Illuminate\Database\Eloquent\Model;

class Funcion extends Model {



	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sep_funciones';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['id_funcion','nombre_funcion','id_controlador','descripcion_funcion'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
         * 
	 */
        
        /**/
        protected $primaryKey = 'id_funcion';
        
        
        
}
