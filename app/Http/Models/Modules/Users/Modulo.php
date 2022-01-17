<?php namespace App\Http\Models\Modules\Users;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model {



	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sep_modulos';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['id_modulo','nombre_modulo','descripcion_modulo','estado_modulo','nombre_carpeta_modulo'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
         * 
	 */
        
        /**/
        protected $primaryKey = 'id_modulo';
        
        
        
}
