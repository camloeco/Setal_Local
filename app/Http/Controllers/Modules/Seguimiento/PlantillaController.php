<?php

namespace App\Http\Controllers\Modules\Seguimiento;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class PlantillaController extends Controller {
	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('control_roles');
	}

	public function getImportar(){
		return view("Modules.Seguimiento.Plantilla.indexImportar");
	}

	public function postImportar(Request $request){
		// ¿Se ha cargado el archivo CSV?
        if ($request->hasFile('archivoCsv')) {
            $archivo = $request->file('archivoCsv');
            // ¿El archivo cumple con el formato esperado - EXCEL (xls, xlsx) ?
            if ($archivo->getClientOriginalExtension() == "xlsx") {
                $filename = time() . '-' . $archivo->getClientOriginalName();
                // Configuracion del directorio multimedia
                $path = getPathUploads() . "/CSV/Participante";
                // Se mueve el archivo Excel al directorio multimedia
                $archivo->move($path, $filename);
                // Convertir archivo XLSX a un arreglo
				$registros = $this->leerExcel($path, $filename);
				$mensaje['exito'] = "El archivo se cargo exitosamente";
            }else {
                $mensaje['formato'] = "El archivo no cumple con el formato esperado - xlsx(Libro de excel), por favor cargar un formato valido";
            }
        }
        else {
            $mensaje['archivo'] = "No se adjunto ning&uacute;n archivo";
        }
        return view("Modules.Seguimiento.Plantilla.indexImportar", compact("mensaje"));
	}

	public function leerExcel($path, $filename){
		$horario = array();

		$objReader = new \PHPExcel_Reader_Excel2007();
		$objPHPExcel = $objReader->load($path . "/" . $filename);
		$objPHPExcel->setActiveSheetIndex(0);

		$prog_codigo = (String) $objPHPExcel->getActiveSheet()->getCell('D4');
		$pla_version = (String) $objPHPExcel->getActiveSheet()->getCell('F4');
		$pla_fra_id = (String) $objPHPExcel->getActiveSheet()->getCell('D11');
		if($pla_fra_id == 'Mañana' or $pla_fra_id == 'Tarde' or $pla_fra_id == ''){
			$pla_fra_id = 1;
			$franja = '1,2';
		}else{
			$pla_fra_id = 3;
			$franja = '3';
		}

		if($pla_version == ''){
			dd('La celda F4 debe contener la versión del programa no puede estas vacia y debe ser numerica');
		}

		$sql = '
			select 	pla_id
			from 	sep_plantilla
			where 	prog_codigo = '.$prog_codigo.'
			and 	pla_version = "'.$pla_version.'"
			and 	pla_fra_id = '.$pla_fra_id.' limit 1';
		$validar = DB::select($sql);
		if(count($validar)>0){
			dd('El programa '.$prog_codigo.' en la versión '.$pla_version.' ya existe');
		}

		$par_identificacion = \Auth::user()->participante->par_identificacion;

		$sql = '
			insert into sep_plantilla
			(pla_id, prog_codigo, pla_version, pla_fra_id, pla_fecha_creacion, pla_usuario_creo, pla_estado)
			values
			(default, '.$prog_codigo.', '.$pla_version.','.$pla_fra_id.', default, '.$par_identificacion.',"1")';
		DB::insert($sql);
		$pla_id = DB::getPdo()->lastInsertId();

		$tipo = array(0=>1, 1=>2, 2=>2, 3=>2, 4=>2, 5=>7, 6=>6);
		$fila = 14;
		$registro = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
		$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';',':','--');
		while(trim($registro) != "") {
			$fase = (int) $objPHPExcel->getActiveSheet()->getCell('A' . $fila)->getCalculatedValue();
			$competencia = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('B' . $fila));
			$resultado = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila));
			$actividad = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila));
			$hor_totales = (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila)->getCalculatedValue();
			$hor_presenciales = (String) $objPHPExcel->getActiveSheet()->getCell('F' . $fila)->getCalculatedValue();
			$hor_autonomas = (String) $objPHPExcel->getActiveSheet()->getCell('G' . $fila)->getCalculatedValue();

			$tipo_materia = $tipo[$fase];
			if($fase == 0 or $fase == 5 or $fase == 6){
				$fase = 5;
			}

			$horario['fase'][] = $fase;
			$horario['competencia'][] = $competencia;
			$horario['resultado'][] = $resultado;
			$horario['actividad'][] = $actividad;
			$horario['hor_presenciales'][] = $hor_presenciales;
			$horario['pla_tip_id'][] = $tipo_materia;

			$sql = '
				insert into 	sep_plantilla_detalle
					(pla_det_id, pla_id, fas_id, com_descripcion,
					act_descripcion, res_descripcion, pla_can_hor_total, pla_can_hor_presenciales, 
					pla_can_hor_autonomas, pla_det_estado)
				values
					(default, '.$pla_id.', '.$fase.', "'.$competencia.'", 
					"'.$actividad.'", "'.$resultado.'", '.$hor_totales.', '.$hor_presenciales.', 
					'.$hor_autonomas.', "1")';
			DB::insert($sql);

			$fila++;
			$registro = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
		}

		$sql = '
			select 	pla_fic.pla_fic_id, fic.fic_numero,
					(select count(pla_fic_act_id)
					from 	sep_planeacion_ficha_actividades pla_fic_act
					where 	pla_fic_act.pla_fic_id = pla_fic.pla_fic_id) as total
			from 	sep_planeacion_ficha pla_fic, sep_ficha fic
			where 	fic.fic_numero = pla_fic.fic_numero
			and		fic.prog_codigo ='.$prog_codigo.'
			and 	pla_fra_id in ('.$franja.')
			group by pla_fic.pla_fic_id
			having total <= 5';
		$grupos_sin_actividades = DB::select($sql);

		if(count($grupos_sin_actividades)>0){
			foreach($grupos_sin_actividades as $val){
				$pla_fic_id = $val->pla_fic_id;
				foreach($horario['fase'] as $key1 => $val1){
					$fase = $val1;
					$competencia = $horario['competencia'][$key1];
					$actividad = $horario['actividad'][$key1];
					$resultado = $horario['resultado'][$key1];
					$hor_presenciales = $horario['hor_presenciales'][$key1];
					$pla_tip_id = $horario['pla_tip_id'][$key1];

					$sql = '
						insert into		sep_planeacion_ficha_actividades
							(pla_fic_act_id, pla_fic_act_competencia, pla_fic_act_resultado,
							pla_fic_act_actividad, pla_fic_act_horas, pla_fic_id,
							par_id_instructor, pla_tip_id, pla_trimestre_numero,
							pla_trimestre_year, fas_id)
						values
							(default, "'.$competencia.'", "'.$resultado.'",
							"'.$actividad.'","'.$hor_presenciales.'", '.$pla_fic_id.',
							null, '.$pla_tip_id.', null,
							null, '.$fase.')';
					DB::insert($sql);
				}
			}
		}
	}

	public function importArrayBD($registros) {
		$prog_codigo = $registros['prog_codigo'];
		$sqlMaxVersionPrograma = "
			select 	max(pla_version) as ultimaVersion
			from 	sep_plantilla
			where 	prog_codigo = '$prog_codigo'";
		$maxVersionPrograma = DB::select($sqlMaxVersionPrograma);

		if($maxVersionPrograma[0]->ultimaVersion == null){
			$version = '1';
		}else{
			$version = $maxVersionPrograma[0]->ultimaVersion+1;
		}

		$sqlInsPlantilla = "insert into sep_plantilla (prog_codigo,pla_version) values('$prog_codigo',$version)";
		DB::insert($sqlInsPlantilla);

		$pla_id = DB::getPdo()->lastInsertId();

		foreach($registros as $key => $valor){
			if($key != "prog_codigo"){
				$sqlPlaDetalle = "
					insert into sep_plantilla_detalle
					(pla_id, fas_id, com_descripcion, res_descripcion, act_descripcion, pla_can_hor_total, pla_can_hor_presenciales, pla_can_hor_autonomas) 
					values
					($pla_id, ". $registros[$key][0] .", '". $registros[$key][1] ."', '". $registros[$key][2] ."', '". $registros[$key][3] ."', ". $registros[$key][4] .", ". $registros[$key][5] .", ". $registros[$key][6] .")";
				DB::insert($sqlPlaDetalle);
			}
		}
	}

	public function seguridad($array){
		// Quitamos los simbolos no permitidos de cada variable recibida,
		// para evitar ataques XSS e Inyección SQL
		$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';',':','--');
		$array = str_replace($caractereNoPremitidos,'',$array);
		return	$array;
	}
}