<?php namespace App\Http\Models\Modules\Users;

use Illuminate\Database\Eloquent\Model;

class Controladores extends Model {



	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sep_controladores';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['id_controlador','nombre_controlador','descripcion_controlador','id_modulo'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
         * 
	 */
        
        /**/
        protected $primaryKey = 'id_controlador';
        
        
        
}
