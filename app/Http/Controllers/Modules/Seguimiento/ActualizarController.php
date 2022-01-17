<?php namespace App\Http\Controllers\Modules\Seguimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\Modules\Seguimiento\SepFicha;
use App\Http\Models\Modules\Seguimiento\SepActividad;
use App\Http\Models\Modules\Seguimiento\SepParticipante;
use DB;

class ActualizarController extends Controller {
	public function __construct(){
		$this->middleware('auth');
		$this->middleware('control_roles');
	}
	
	public function getImportar(){
	    // Registrar actividades del programa
	    
	    //Resultados Transversal
	    /*$sql = "select fas_id, com_descripcion, res_descripcion, act_descripcion, pla_can_hor_presenciales, pla_tip_id 
	    from sep_plantilla pla, sep_plantilla_detalle pla_det 
	    where pla.pla_id = pla_det.pla_id and pla.prog_codigo = 223310 and pla_det.fas_id = 5";
	    $datosPlantilla = DB::select($sql);*/
	    
	    // Resultados completos	
	    /*
		$sql = '
			select 	fas_id, com_descripcion, res_descripcion,
					act_descripcion, pla_can_hor_presenciales, pla_tip_id
			from 	sep_plantilla pla, sep_plantilla_detalle pla_det
			where 	pla.pla_id = pla_det.pla_id
			and 	pla.prog_codigo = 228123';
		$datosPlantilla = DB::select($sql);*/
		
		/*$pla_fic_id = 878;
		if(count($datosPlantilla) > 0){
			foreach($datosPlantilla as $val){
				$fase = $val->fas_id;
				$competencia = $val->com_descripcion;
				$resultado = $val->res_descripcion;
				$actividad = $val->act_descripcion;
				$totalHorasAct = $val->pla_can_hor_presenciales;
				$tipo = $val->pla_tip_id;

				$sql = '
					insert into sep_planeacion_ficha_actividades(
						pla_fic_act_id, pla_fic_id, fas_id, 
						pla_fic_act_competencia, pla_fic_act_resultado, 
						pla_fic_act_actividad, pla_fic_act_horas, pla_tip_id
					) values (
						default, '.$pla_fic_id.', "'.$fase.'",
						"'.$competencia.'", "'.$resultado.'",
						"'.$actividad.'","'.$totalHorasAct.'", '.$tipo.')';
				DB::insert($sql);
			}
		}*/
		return view("Modules.Seguimiento.Actualizar.indexImportar");
	}
	
	public function postImportar(Request $request){
		// ¿Se ha cargado el archivo CSV?
		if($request->hasFile('archivoCsv')) {
			$archivo = $request->file('archivoCsv');
			// ¿El archivo cumple con el formato esperado - EXCEL (xls, xlsx) ?
			if($archivo->getClientOriginalExtension() == 'xls' || $archivo->getClientOriginalExtension() == 'xlsx') {
				$filename = time() . '-' . $archivo->getClientOriginalName();
				// Configuracion del directorio multimedia
				$pathCsv = getPathUploads() . '/CSV/Horario';
				// Se mueve el archivo CSV al directorio multimedia
				$archivo->move($pathCsv, $filename);
				// Convertir archivo CSV a un arreglo
				$registros = $this->actualizar($pathCsv, $filename);
				dd();
				$pla_fic_id = $this->crearHorario($registros);
				return redirect(url('seguimiento/horario/index?pla_fec_tri_id=todos&pla_fic_id%5B%5D='.$pla_fic_id));
			}else{
				$mensaje['formato'] = 'El archivo no cumple con el formato esperado - CSV, por favor cargar un formato valido';
			} 
		}else{
			$mensaje['archivo'] = 'No se adjunto ning&uacute;n archivo';
		}

		return view('Modules.Seguimiento.Plantilla.importar', compact('mensaje'));
	}

	public function actualizar($path, $filename){
		$objReader = new \PHPExcel_Reader_Excel2007();
		$objPHPExcel = $objReader->load($path . "/" . $filename);
		$objPHPExcel->setActiveSheetIndex(0);
		dd();
		/*$fichas = array(
			860 => ['nivel'=> '2', 'dc' => 'Nuevo'],
			855 => ['nivel'=> '4', 'dc' => 'Nuevo'],
			813 => ['nivel'=> '4', 'dc' => 'Nuevo'],
			788 => ['nivel'=> '4', 'dc' => 'Nuevo'],
			861 => ['nivel'=> '4', 'dc' => 'Nuevo'],
			859 => ['nivel'=> '4', 'dc' => 'Viejo'],
			811 => ['nivel'=> '4', 'dc' => 'Viejo'],
			823 => ['nivel'=> '2', 'dc' => 'Viejo'],
			812 => ['nivel'=> '2', 'dc' => 'Viejo'],
			857 => ['nivel'=> '4', 'dc' => 'Viejo']
		);*/
		
		// Jorge
		$fichas = array(
			810 => ['nivel' => '4', 'dc' => 'Viejo'],
			816 => ['nivel' => '4', 'dc' => 'Viejo'],
			828 => ['nivel' => '2', 'dc' => 'Viejo'],
			829 => ['nivel' => '2', 'dc' => 'Viejo'],
			830 => ['nivel' => '2', 'dc' => 'Viejo']
		);

		foreach($fichas as $pla_fic_id => $fic){
			$nivel = $fic['nivel'];
			$dc = $fic['dc'];

			$sql = '
				select 	tip.tra_tip_id, tra_tip_descripcion, tra_com_descripcion,
						tra_res_descripcion, tra_act_descripcion, niv_for_id,
						tra_act_horas, numero_trimestre_inicio
				from 	sep_transversal_nivel_formacion niv_for, sep_transversal_tipo tip,
						sep_transversal_actividad act
				where 	niv_for.tra_tip_id = tip.tra_tip_id
				and 	niv_for.tra_tip_id = act.tra_tip_id
				and 	act.dc_tipo = "'.$dc.'"
				and 	niv_for.dc_tipo = "'.$dc.'"
				and 	niv_for.niv_for_id = '.$nivel.'
				order by tra_tip_id asc';
			$actividades_transversal = DB::select($sql);
			echo $sql; echo '<br><br>';
			foreach($actividades_transversal as $key => $val){
				$competencia = ucfirst(mb_strtolower($val->tra_com_descripcion, 'UTF-8'));
				$resultado = ucfirst(mb_strtolower($val->tra_res_descripcion, 'UTF-8'));
				$actividad = '('.$val->tra_tip_descripcion.') - '.ucfirst(mb_strtolower($val->tra_act_descripcion, 'UTF-8'));
				$act_horas = $val->tra_act_horas;

				$sql = '
					insert into sep_planeacion_ficha_actividades(
						pla_fic_act_id, pla_fic_act_competencia, pla_fic_act_resultado,
						pla_fic_act_actividad, pla_fic_act_horas, pla_fic_id,
						par_id_instructor, pla_tip_id, pla_trimestre_numero,
						fas_id, fecha_inicio, fecha_fin
					) values (
						default, "'.$competencia.'", "'.$resultado.'",
						"'.$actividad.'","'.$act_horas.'", '.$pla_fic_id.',
						"1111111111", 7, null,
						5, null, null)';
				DB::insert($sql);
			}
		}
		
		dd();
		
		$fila = 2;
		$ficha = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
		$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';',':','--','Ã');
		while(trim($ficha) != "") {
			$fechaInicio = (String) $objPHPExcel->getActiveSheet()->getCell('B' . $fila)->getFormattedValue();
			$fechaFin = (String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila)->getFormattedValue();
			echo ' '.$ficha.' '.$fechaInicio.' - '.$fechaFin.' -- ';
			if(strlen($fechaInicio) == 10){
				$anio = substr($fechaInicio, 6, 4);
				$mes = substr($fechaInicio, 3, 2);
				$dia = substr($fechaInicio, 0, 2);
				$fechaInicio = $anio.'-'.$mes.'-'.$dia;

				$anio = substr($fechaFin, 6, 4);
				$mes = substr($fechaFin, 3, 2);
				$dia = substr($fechaFin, 0, 2);
				$fechaFin = $anio.'-'.$mes.'-'.$dia;
			}else{
				$anio = substr($fechaInicio, 6, 2);
				$mes = substr($fechaInicio, 0, 2);
				$dia = substr($fechaInicio, 3, 2);
				$fechaInicio = '20'.$anio.'-'.$mes.'-'.$dia;

				$anio = substr($fechaFin, 6, 2);
				$mes = substr($fechaFin, 0, 2);
				$dia = substr($fechaFin, 3, 2);
				$fechaFin = '20'.$anio.'-'.$mes.'-'.$dia;
			}
			//echo ' '.$ficha.' '.$fechaInicio.' - '.$fechaFin.'<br>';
			$sql = '
				update 	sep_ficha
				set 	fic_fecha_inicio = "'.$fechaInicio.'", fic_fecha_fin = "'.$fechaFin.'"
				where 	fic_numero = "'.$ficha.'"';
		//	DB::update($sql);
			$fila++;
			$ficha = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
		}
		dd();

		/*$fila = 2;
		$registro = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
		$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';',':','--','Ã');
		while(trim($registro) != "") {
			$descripcion = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
			$aforo = $objPHPExcel->getActiveSheet()->getCell('B' . $fila);
			$sql = '
				insert into sep_ingreso_ambiente (id, descripcion, aforo)
				values
				(default, "'.$descripcion.'", '.$aforo.')';
			DB::insert($sql);

			$fila++;
			$registro = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
		}*/

		//dd();

		/*$existe = array();
		$fila = 3;
		$registro = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
		$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';',':','--','Ã');
		while(trim($registro) != "") {
			$linea = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
			if($linea == "Transversalidad"){
				$linea = '16686574';
			}else if($linea == "Vestuario Inteligente"){
				$linea = '67020609';
			}else if($linea == "Comunicación Digital"){
				$linea = '30310315';
			}else if($linea == "Diseño Mecatrónico"){
				$linea = '16266427';
			}
			
			$programa = (String) $objPHPExcel->getActiveSheet()->getCell('B' . $fila);
			$ficha = (String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila);

			if(!in_array($ficha, $existe)){
				$sql = '
					insert into sep_ingreso_habilitar_ficha (id, coordinador, programa, ficha)
					values
					(default, "'.$linea.'", "'.$programa.'", "'.$ficha.'")';
				DB::insert($sql);
				$existe[] = $ficha;
			}
			$fila++;
			$registro = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
		}




		dd($existe);*/

		//dd($filename);
		/*$ficha = '2056968';
		$programa = 122320;
		$pla_fic_id = DB::select('select pla_fic_id from sep_planeacion_ficha where fic_numero = "'.$ficha.'"');
		echo$pla_fic_id = $pla_fic_id[0]->pla_fic_id;
		DB::delete('delete from sep_planeacion_ficha_actividades where pla_fic_id = '.$pla_fic_id);
		
		$pla_fic_id = DB::select('select pla_fic_id from sep_planeacion_ficha where fic_numero = "'.$ficha.'"');*/
		
		/*$sql = '
			select pla_fic_id, fic.fic_numero
			from sep_planeacion_ficha pla_fic, sep_ficha fic
			where pla_fic.fic_numero = fic.fic_numero
			and prog_codigo in(122330,524149,522202,228101,513101,524109,922606)';
		$fichas = DB::select($sql);*/
		//dd($fichas);
		/*foreach($fichas as $val){
			DB::update('update sep_planeacion_ficha_actividades set pla_trimestre_numero = "0" where pla_fic_id = '.$val->pla_fic_id);
		}
		dd();*/
		//$ficha = (String) $objPHPExcel->getActiveSheet()->getCell('D5');
		/*$ficha = '2141699';*/
		$programa = (String) $objPHPExcel->getActiveSheet()->getCell('D4');
		$pla_fic_id = 778;
		$sql = '
			select 	pla_fic_id, fic_numero
			from 	sep_planeacion_ficha
			where 	pla_fic_id = '.$pla_fic_id;
		$fichas = DB::select($sql);
		//dd($fichas);
		if(count($fichas) == 0){
			dd('Ficha no existe');
		}

		/*echo "<pre>";
		print_r($fichas);
		dd($fichas);*/
		
		/*foreach($fichas as $val){
			DB::delete('delete from sep_planeacion_ficha_actividades where pla_fic_id = '.$val->pla_fic_id);
		}*/
		//dd($fichas);
		$sql = '
			delete 	from sep_planeacion_ficha_actividades 
			where 	pla_tip_id = 2 and pla_fic_id = '.$pla_fic_id;
		//DB::delete($sql);
		$tipo = array(0=>1,1=>2,2=>2,3=>2,4=>2,5=>7,6=>6);
		$fila = 14;
		$registro = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
		$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';',':','--','Ã');
		while(trim($registro) != "") {
			foreach($fichas as $val){
				$pla_fic_id = $val->pla_fic_id;
				$competencia = (String) $objPHPExcel->getActiveSheet()->getCell('B' . $fila);
				$resultado = (String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila);
				$actividad = (String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila);
				$act_horas = (String) $objPHPExcel->getActiveSheet()->getCell('F' . $fila)->getCalculatedValue();
				
				$fase = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
				//$competencia = str_replace($caractereNoPremitidos, '', utf8_decode($competencia));
				//$resultado = utf8_decode($resultado);

				$competencia = str_replace($caractereNoPremitidos,'',$competencia);
				$resultado = str_replace($caractereNoPremitidos,'',$resultado);
				$actividad = str_replace($caractereNoPremitidos,'',$actividad);

				$competencia = utf8_encode(mb_convert_encoding($competencia,'HTML-ENTITIES','UTF-8'));
				$resultado = utf8_encode(mb_convert_encoding($resultado,'HTML-ENTITIES','UTF-8'));
				$actividad = utf8_encode(mb_convert_encoding($actividad,'HTML-ENTITIES','UTF-8'));
				/*echo $resultado;
				echo '<br>';*/
				$tipo_materia = $tipo[$fase];
				if($fase == 0 or $fase == 5 or $fase == 6){
					$fase = 5;
				}
								
				$sql = '
					insert into sep_planeacion_ficha_actividades (
						pla_fic_act_id,pla_fic_act_competencia,pla_fic_act_resultado,pla_fic_act_actividad,
						pla_fic_act_horas,pla_fic_id,par_id_instructor,pla_tip_id,pla_trimestre_numero,pla_trimestre_year,fas_id
					) values (
						default,"'.$competencia.'","'.$resultado.'","'.$actividad.'",
						"'.$act_horas.'",'.$pla_fic_id.',null,'.$tipo_materia.',null,null,'.$fase.')';
				//DB::insert($sql);
			}
			$fila++;
			$registro = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
		}
		dd('xd');
	}
}