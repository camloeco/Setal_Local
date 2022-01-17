<?php namespace App\Http\Models\Modules\Seguimiento;

use Illuminate\Database\Eloquent\Model;

class SepFicha extends Model {
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sep_ficha';
    protected $primaryKey = 'fic_numero';
    
}
