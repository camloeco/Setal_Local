<?php namespace App\Http\Models\Modules\Seguimiento;

use Illuminate\Database\Eloquent\Model;

class SepCompetencia extends Model {
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sep_competencia';
    protected $primaryKey = 'com_codigo';
    
    public function programa()
    {
        return $this->belongsTo('App\Http\Models\Modules\Seguimiento\SepPrograma', 'prog_codigo');
    }
    
    public function resultados()
    {
        return $this->hasMany('App\Http\Models\Modules\Seguimiento\SepResultado', 'com_codigo');
    }

}
