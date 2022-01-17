<?php namespace App\Http\Models\Modules\Seguimiento;

use Illuminate\Database\Eloquent\Model;

class SepCentro extends Model {
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sep_centro';
    protected $primaryKey = 'cen_codigo';
    
}
