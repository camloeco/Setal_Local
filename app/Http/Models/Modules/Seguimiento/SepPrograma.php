<?php namespace App\Http\Models\Modules\Seguimiento;

use Illuminate\Database\Eloquent\Model;

class SepPrograma extends Model {
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sep_programa';
    protected $primaryKey = 'prog_codigo';

    public function competencias()
    {
        return $this->hasMany('App\Http\Models\Modules\Seguimiento\SepCompetencia', 'prog_codigo');
    }

}
