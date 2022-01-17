<?php namespace App\Http\Models\Modules\Seguimiento;

use Illuminate\Database\Eloquent\Model;

class SepParticipante extends Model {
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sep_participante';
    protected $primaryKey = 'par_identificacion';
    
    public function matricula(){
        return $this->hasMany('App\Http\Models\Modules\Seguimiento\SepMatricula','par_identificacion');
    } 

}
