<?php namespace App\Http\Models\Modules\Users;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserProfiles extends Model {
        
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_profiles';

    public function getAgeAttribute(){
        return Carbon::parse($this->birthdate)->age;
    } // getAgeAttribute
    
}
