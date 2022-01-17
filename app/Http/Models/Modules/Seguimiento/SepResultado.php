<?php namespace App\Http\Models\Modules\Seguimiento;

use Illuminate\Database\Eloquent\Model;

class SepResultado extends Model {
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sep_resultado';
    protected $primaryKey = 'res_id';
    
    public function competencia()
    {
        return $this->belongsTo('App\Http\Models\Modules\Seguimiento\SepCompetencia', 'com_codigo');
    }
    
    public function actividades()
    {
        // Modelo de la relacion, tabla intermedia, pk de la tabla a la que se realiza la consulta, pk de la otra tabla
        return $this->belongsToMany('App\Http\Models\Modules\Seguimiento\SepActividad','sep_actividad_resultado','res_id', 'act_id');
    }

}
