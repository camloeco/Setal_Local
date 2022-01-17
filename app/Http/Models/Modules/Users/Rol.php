<?php namespace App\Http\Models\Modules\Users;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model {



	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sep_roles';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['id_rol','nombre_rol','id_cliente'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
         * 
	 */
        
        /**/
        protected $primaryKey = 'id_rol';
        
        
        
}
