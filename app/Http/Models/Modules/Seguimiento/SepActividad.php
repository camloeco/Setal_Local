<?php namespace App\Http\Models\Modules\Seguimiento;

use Illuminate\Database\Eloquent\Model;

class SepActividad extends Model {
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sep_actividad';
    protected $primaryKey = 'act_id';
    
    public function resultado()
    {
        // Modelo de la relacion, tabla intermedia, pk de la tabla a la que se realiza la consulta, pk de la otra tabla
        return $this->belongsToMany('App\Http\Models\Modules\Seguimiento\SepResultado','sep_actividad_resultado','act_id', 'res_id');
    }
    
}
