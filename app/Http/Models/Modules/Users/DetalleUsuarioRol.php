<?php namespace App\Http\Models\Modules\Users;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class DetalleUsuarioRol extends Model {

        /**/
        //protected $primaryKey = ['id_rol','id_usuario'];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sep_detalle_usuario_rol';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['id_rol','id_usuario'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
         * 
	 */
               
}
