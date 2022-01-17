<?php namespace App\Http\Models\Modules\Users;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];
        
        public function profile(){
            return $this->hasOne('App\Http\Models\Modules\Users\UserProfiles');
        }
        
        public function getFullNameAttribute(){
            return $this->first_name. " " . $this->last_name; 
        }
        
        public function participante(){
            return $this->hasOne('App\Http\Models\Modules\Seguimiento\SepParticipante', 'par_identificacion', 'par_identificacion');
        }


}
