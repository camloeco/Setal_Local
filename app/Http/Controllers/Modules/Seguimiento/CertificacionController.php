<?php 
namespace App\Http\Controllers\Modules\Seguimiento;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class CertificacionController extends Controller {
	public function __construct(){
		$this->middleware('auth');
		$this->middleware('control_roles');
	}

	public function getReporte(){
		return view('Modules.Seguimiento.Certificacion.reporte');
	}

	public function getIndex(){
		$sql = '
			select *
			from 	sep_participante par, sep_matricula mat
			where 	par.par_identificacion = mat.par_identificacion
			and 	rol_id = 1 order by par_nombres limit 1000,10 ';
		$aprendices = DB::select($sql);
		return view('Modules.Seguimiento.Certificacion.index', compact('aprendices'));
	}

	public function getDescargar(){
		$ruta = public_path() . '/Modules/Seguimiento/Educativa/certificacion/paz.xlsx';
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        return response()->download($ruta);
	}

	public function seguridad($array){
		// Quitamos los simbolos no permitidos de cada variable recibida, 
		// para evitar ataques XSS e Inyección SQL
		$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';',':','--');
		$array = str_replace($caractereNoPremitidos,'',$array);
		return	$array;
	}
}