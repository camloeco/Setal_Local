<?php namespace App\Http\Controllers\Modules\Seguimiento;

use App\Http\Controllers\Controller;
use \Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// Modelos del modulo reportes
use App\Http\Models\Modules\Seguimiento\SepParticipante;
use App\Http\Models\Modules\Seguimiento\SepEtapaPractica;
use App\Http\Models\Modules\Seguimiento\SepOpcionEtapa;
use App\Http\Models\Modules\Seguimiento\SepMatricula;
use App\Http\Models\Modules\Seguimiento\SepFicha;
use App\Http\Models\Modules\Seguimiento\SepPrograma;
use App\Http\Models\Modules\Seguimiento\SepActividad;
use App\Http\Models\Modules\Seguimiento\SepActividadResultado;
use App\Http\Models\Modules\Seguimiento\SepResultado;
use App\Http\Models\Modules\Users\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
//-- EXCEL
use App\Http\Controllers\Modules\Seguimiento\Classes\PHPExcel\PHPExcel_IOFactory;

class ReportesController extends Controller {
    private $ficha;

	public function __construct(){
		$this->middleware('auth');
		$this->middleware('control_roles');
	}
	
	public function getReporteseguimientoexcel(){
		$select = '
			select 	matricula.fic_numero, fic_fecha_inicio, fic_fecha_fin, 
					aprendiz.par_identificacion as aprDocumento,
					aprendiz.nombre as aprNombre, aprendiz.par_correo as aprCorreo,
					prog_nombre, niv_for_nombre, coordinador.nombre as cooNombre';
	
		$from = '
			from 	sep_matricula matricula, 
				(select par_identificacion, concat(par_nombres," ",par_apellidos) as nombre, par_correo
	    		from sep_participante) as aprendiz, 
	    		(select par_identificacion, concat(par_nombres," ",par_apellidos) as nombre
	    		from sep_participante) as coordinador, 
	    		(select prog_codigo, prog_nombre, niv_for_id
	    		from sep_programa) as programa,
	    		(select niv_for_id, niv_for_nombre
	    		from sep_nivel_formacion) as nivel,
	    		(select fic_numero, fic_fecha_inicio, fic_fecha_fin, prog_codigo, par_identificacion_coordinador
	    		from sep_ficha) as ficha';

		$where ='
			where 	matricula.par_identificacion = aprendiz.par_identificacion
			and 	matricula.fic_numero = ficha.fic_numero
			and 	ficha.prog_codigo = programa.prog_codigo
			and 	programa.niv_for_id = nivel.niv_for_id
			and 	ficha.par_identificacion_coordinador = coordinador.par_identificacion
			and 	est_id = 2';

		$order = ' order by cooNombre, prog_nombre, fic_numero, aprNombre';

		$sql = $select.$from.$where.$order;
		$aprendices = DB::select($sql);
		$datos = array();
        $caractereNoPremitidos = array("\r\n","\n","\r","&gt;","&lt;",'"',"'");
		foreach($aprendices as $apr){
			$datos[$apr->aprDocumento]['cooNombre'] = $apr->cooNombre;
			$datos[$apr->aprDocumento]['ficha'] = $apr->fic_numero;
			$datos[$apr->aprDocumento]['nivel'] = $apr->niv_for_nombre;
			$datos[$apr->aprDocumento]['prog_nombre'] = $apr->prog_nombre;
			$datos[$apr->aprDocumento]['fechaInicio'] = $apr->fic_fecha_inicio;
			$datos[$apr->aprDocumento]['fechaFin'] = $apr->fic_fecha_fin;
			$datos[$apr->aprDocumento]['fechaPorTiempo'] = date('Y-m-d', strtotime($apr->fic_fecha_fin.' + 2 year'));;
			$datos[$apr->aprDocumento]['aprDocumento'] = $apr->aprDocumento;
			$datos[$apr->aprDocumento]['aprNombre'] = $apr->aprNombre;
			$datos[$apr->aprDocumento]['aprCorreo'] = str_replace($caractereNoPremitidos,'',$apr->aprCorreo);
			$datos[$apr->aprDocumento]['alternativa'] = 'Sin registro';
			$datos[$apr->aprDocumento]['empresa'] = '';
			$datos[$apr->aprDocumento]['instructorDocumento'] = '';
			$datos[$apr->aprDocumento]['instructorNombre'] = '';
			for($i=1; $i<=15; $i++){
				$datos[$apr->aprDocumento][$i] = '';
			}
			$datos[$apr->aprDocumento]['obsLiderProductiva'] = '';
			$datos[$apr->aprDocumento]['obsInstructor'] = '';
		}

		$sql = '
			select 	par_identificacion_aprendiz as aprDocumento, seg_pro_nombre_empresa, ope_descripcion,
					par_identificacion_responsable, concat(par_nombres," ",par_apellidos) as instructorNombre,
					seg_pro_obs_lider_productiva as obsLiderProductiva, 
					seg_pro_obs_instructor_seguimiento as obsInstructor
			from 	sep_seguimiento_productiva pro, sep_participante ins, sep_opcion_etapa opc
			where 	pro.par_identificacion_responsable = ins.par_identificacion
			and 	pro.ope_id = opc.ope_id';
		$productiva = DB::select($sql);
		foreach($productiva as $pro){
			if(isset($datos[$pro->aprDocumento])){
				$datos[$pro->aprDocumento]['instructorNombre'] = $pro->instructorNombre;
				$datos[$pro->aprDocumento]['instructorDocumento'] = $pro->par_identificacion_responsable;
				$datos[$pro->aprDocumento]['empresa'] = str_replace($caractereNoPremitidos,'',$pro->seg_pro_nombre_empresa);
				$datos[$pro->aprDocumento]['alternativa'] = $pro->ope_descripcion;
				$datos[$pro->aprDocumento]['obsLiderProductiva'] = str_replace($caractereNoPremitidos,'',$pro->obsLiderProductiva);
				$datos[$pro->aprDocumento]['obsInstructor'] = str_replace($caractereNoPremitidos,'',$pro->obsInstructor);
			}
		}

		$sql = '
			select 	par_identificacion_aprendiz as aprDocumento, seg_bit_bitacora
			from 	sep_seguimiento_bitacora bita, sep_seguimiento_productiva pro
			where 	bita.seg_pro_id = pro.seg_pro_id';
		$bitacora = DB::select($sql);
		foreach($bitacora as $bit){
			if(isset($datos[$bit->aprDocumento])){
				$datos[$bit->aprDocumento][$bit->seg_bit_bitacora] = 'X';
			}
		}

		$sql = '
			select 	seg_vis_fecha, seg_vis_visita, par_identificacion_aprendiz as aprDocumento
			from 	sep_seguimiento_visita visita, sep_seguimiento_productiva pro
			where 	visita.seg_pro_id = pro.seg_pro_id';
		$visita = DB::select($sql);
		foreach($visita as $vis){
			if(isset($datos[$vis->aprDocumento])){
				$datos[$vis->aprDocumento][($vis->seg_vis_visita+12)] = $vis->seg_vis_fecha;
			}
		}

		$fecha_actual = date('Y-m-d');
		$hora_actual = date('h:i a');
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename=Etapa_practica_'.$fecha_actual.'.csv');
		header('Pragma: no-cache');
		header("Expires: 0");

		echo ";F reporte:;".$fecha_actual.";H reporte:;".$hora_actual."\n";
		echo "Coodinadora;Ficha;Nivel;Programa;";
		echo "Fecha inicio;Fecha fin;Por tiempo fecha fin;";
		echo "Doc. Aprendiz;Aprendiz;Correo;Alternativa;Empresa;";
		echo "Doc. Instructor; Instructor;";
		for($i=1; $i<=12; $i++){
			echo $i.";";
		}
		echo utf8_decode('Planeación').";Extraordinario;".utf8_decode('Evaluación').";";
		echo utf8_decode('Obs. líder productiva').";".utf8_decode('Obs. Instructor').";";

		echo "\n";
		if(count($datos) > 0){
			foreach($datos as $valor){
				foreach($valor as $valor1){
					echo utf8_decode($valor1).";";
				}
			    echo "\n";
			}
		}else{
			echo utf8_decode('No hay información.');
		}
	}
	
	// 2020-06-19 Reporte para los Instructores
	public function getInasistencia(){
		$sql = '
			select 	pla_fic_id,pla_fic.fic_numero,prog_nombre, pla_fra_descripcion
			from 	sep_planeacion_ficha pla_fic,sep_ficha fic,sep_programa pro, sep_planeacion_franja fra
			where 	pla_fic.fic_numero = fic.fic_numero  and  fic.prog_codigo = pro.prog_codigo  
            and 	fra.pla_fra_id = pla_fic.pla_fra_id
			and  	not fic.fic_numero in("Restriccion","Complementario")
			order 	by prog_nombre desc';
		$fichas = DB::select($sql);
		
		$fecha_actual = date('Y-m-d');
		$sql = '
			select 	pla_fec_tri_fec_inicio 
			from 	sep_planeacion_fecha_trimestre 
			where	pla_fec_tri_fec_inicio <= "'.$fecha_actual.'"
			order by pla_fec_tri_id desc limit 1';
		$trimestre_actual = DB::select($sql);
		if(count($trimestre_actual)>0){
			$trimestre_fecha_inicio = $trimestre_actual[0]->pla_fec_tri_fec_inicio;
		}
		
		return view('Modules.Seguimiento.Reportes.inasistencia', compact('fichas','trimestre_fecha_inicio'));
	}
	
	// 2020-06-19 Reporte para los Instructores
	public function postInasistencia(){
		extract($_POST);
		
		// Validar que existan las 3 variables que necesitamos en el proceso
		if(!isset($fecha_inicio) or !isset($fecha_fin) or !isset($pla_fic_id)){
			echo 'Los 3 campos son obligatorios.'; dd();
		}
		
		$anio = substr($fecha_inicio,0,4);
		$mes = substr($fecha_inicio,5,2);
		$dia = substr($fecha_inicio,8,2);
		// Anio mes y día sean númericos en la fecha inicio
		if(!is_numeric($anio) or !is_numeric($mes) or !is_numeric($dia)){
			echo 'Año, mes y día deben ser númericos'; dd();
		}
		
		$anio = substr($fecha_fin,0,4);
		$mes = substr($fecha_fin,5,2);
		$dia = substr($fecha_fin,8,2);
		// Anio mes y día sean númericos en la fecha fin
		if(!is_numeric($anio) or !is_numeric($mes) or !is_numeric($dia)){
			echo 'Año, mes y día deben ser númericos'; dd();
		}
			
		// Los id's de las fichas deben ser númericos
		foreach($pla_fic_id as $val){
			if(!is_numeric($val)){
				echo 'Todos los valores deben ser numericos.'; dd();
			}
		}
		
		// La fecha no debe ser menor de la fecha inicio del módulo de inasistencia 
		if($fecha_inicio < '2020-04-13'){
			echo 'La fecha inicio del reporte no debe ser menor a la fecha 2020-04-13.'; dd();
		}
		
		// La fecha inicio no debe ser mayor a la fecha fin 
		if($fecha_inicio > $fecha_fin){
			echo 'La fecha inicio no puede ser mayor a la fecha fin.'; dd();
		}
		
		$fecha_actual = date('Y-m-d');
		// Validar que la fecha inicio y fin no sean mayor a la fecha actual
		if($fecha_inicio > $fecha_actual){
			echo 'La fecha inicio no puede ser mayor a la fecha actual.'; dd();
		}
		
		if($fecha_fin > $fecha_actual){
			echo 'La fecha fin no puede ser mayor a la fecha actual.'; dd();
		}
		
		// Máximo de fichas 8
		$cantidad_fichas = count($pla_fic_id);
		if($cantidad_fichas > 8){
			echo 'La cantidad máxima de fichas es 8.'; dd();
		}
		
		// Inasistencia detalle
		$id_ficha = implode(',', $pla_fic_id);
		$sql = '
			select 	fic_numero, ina_fecha, ina.ina_instructor, ina_det.ina_det_aprendiz, ina_hora,
					concat(ins.par_nombres, " ", ins.par_apellidos) as nombreInstructor
			from 	sep_inasistencia ina, sep_inasistencia_detalle ina_det, sep_participante ins
			where 	ina.ina_id = ina_det.ina_id
			and 	ina.ina_instructor = ins.par_identificacion
			and		pla_fic_id in ('.$id_ficha.')
			and 	(ina_fecha >= "'.$fecha_inicio.'" and ina_fecha <= "'.$fecha_fin.'")
			and 	ina_det_estado = "1"
			order 	by fic_numero, ina_fecha desc';
		$inasistencia_sql = DB::select($sql);
		
		if(count($inasistencia_sql)>0){
			$datos = array();
			foreach($inasistencia_sql as $val){
				$datos['lista'][$val->fic_numero][$val->ina_fecha][$val->ina_instructor][$val->ina_hora]['inasistencia'][$val->ina_det_aprendiz] = '';
				$datos['instructor'][$val->ina_instructor] = $val->nombreInstructor;
			}
			
			// Consultamos los aprendices de cada ficha seleccionada
			// que están en formación o inducción
			$sql = '
				select 	pla_fic.fic_numero, apr.par_identificacion, 
						par_nombres, par_apellidos, prog_nombre
				from 	sep_planeacion_ficha pla_fic, sep_ficha fic, 
						sep_matricula mat, sep_participante apr,
						sep_programa pro
				where 	pla_fic.fic_numero = fic.fic_numero
				and 	fic.prog_codigo = pro.prog_codigo
				and 	fic.fic_numero = mat.fic_numero
				and 	mat.par_identificacion = apr.par_identificacion
				and		pla_fic_id in ('.$id_ficha.')
				and 	est_id in(2,10)
				order 	by fic.fic_numero, apr.par_identificacion';
			$aprendices_sql = DB::select($sql);
			//dd($aprendices_sql);
			foreach($aprendices_sql as $val){
				$datos['aprendiz'][$val->fic_numero][$val->par_identificacion]['nombre'] = $val->par_nombres.' '.$val->par_apellidos;
				$datos['programa'][$val->fic_numero] = $val->prog_nombre;
			}
			
			// Llegadas tarde
			$sql = '
				select 	fic_numero, ina_fecha, ina_instructor, ina_hora, ina_ret_aprendiz
				from 	sep_inasistencia ina, sep_inasistencia_retardo ina_tar,
						sep_participante apr
				where 	ina.ina_id = ina_tar.ina_id
				and 	ina_tar.ina_ret_aprendiz = apr.par_identificacion
				and		pla_fic_id in ('.$id_ficha.')
				and 	(ina_fecha >= "'.$fecha_inicio.'" and ina_fecha <= "'.$fecha_fin.'")
				and 	ina_ret_estado = "1"';
			$retardo_sql = DB::select($sql);
			foreach($retardo_sql as $val){
				$datos['lista'][$val->fic_numero][$val->ina_fecha][$val->ina_instructor][$val->ina_hora]['retardo'][$val->ina_ret_aprendiz] = '';
			}
		}
		//dd($datos);
		$fecha_actual = date('Y-m-d');
		$hora_actual = date('h:i a');
		$identificacion = \Auth::user()->participante->par_identificacion;
		
		$sql = '
			insert into sep_utilizacion_reporte
			(uti_rep_id, par_identificacion, descripcion, fecha, hora) values
			(default, "'.$identificacion.'", "Inasistencia", "'.$fecha_actual.'", "'.$hora_actual.'" )';
		DB::insert($sql);
		
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename=Reporte_asistencia_'.$fecha_actual.'.csv');
		header('Pragma: no-cache');
		header("Expires: 0");
		
		echo ";Fec reporte:;".$fecha_actual.";Hor reporte:;".$hora_actual."\n";
		echo ";Fec inicio:;".$fecha_inicio.";Fec fin:;".$fecha_fin."\n";
		
		if(count($inasistencia_sql)>0){
			echo "Instructor(a);Ficha;Programa;Fecha;DocumentoAprendiz;NombreAprendiz;Estado\n";
			foreach($datos['aprendiz'] as $key1 => $val1){
				if(isset($datos['lista'][$key1])){
					foreach($datos['lista'][$key1] as $key2 => $val2){
						foreach($datos['aprendiz'][$key1] as $key3 => $val3){
							foreach($val2 as $key4 => $val4){
								echo utf8_decode($datos['instructor'][$key4]).";".$key1.";";
								echo utf8_decode($datos['programa'][$key1]).";".$key2.";";
								echo $key3.";".utf8_decode($val3['nombre']).";";
								foreach($val4 as $key5 => $val5){
									$entre = false;
									if(isset($val5['inasistencia'])){
										if(array_key_exists($key3, $val5['inasistencia'])){
											echo "No asistio;"; $entre = true;
										}
									}
									
									if(isset($val5['retardo'])){
										if(array_key_exists($key3, $val5['retardo'])){
											echo "Retardo;"; $entre = true;
										}
									}
									
									if(!$entre){
										echo "Asistio;";
									}
									echo "\n";
								}
							}
						}
					}
				}else{
					echo 'No tenemos registros de la ficha: '.$key1."\n";
				}
			}
		}else{
			echo "No se encontraron datos registrados.";
		}
	}
	
	// 2020-06-19
	public function seguridad($array){
		// Quitamos los simbolos no permitidos de cada variable recibida, 
		// para evitar ataques XSS e Inyección SQL
		$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','//','<','>','=',';',':','--');
		$array = str_replace($caractereNoPremitidos,'',$array);
		return	$array;
	}

	public function getIndex(){
		$ruta = getPathUploads()."/plantillas/prueba.xls";
		
		$list = array('xd1','xd2','xd3','xd4','xd5','xd6','xd7','xd8','xd9','xd10');
		$file = fopen($ruta,"wb");
		for($i=0; $i<5; $i++){
			fputcsv($file, $list, ";", ";");
		}
		fclose($file);
		
		$ruta1 = 'C:\xampp5\htdocs\prueba\seguimientopro/app/Http/Base/Php/PHPExcel/IOFactory.php';
		//leerExcelCsv($ruta,$ruta1);
		
		//return view("Modules.Seguimiento.Reportes.index");*/
	}
	
	public function postUpdatearea(){
		extract($_POST);
		
		$validarArea = DB::select('select are_id from sep_area where are_id = '.$valor.' limit 1');
		if(count($validarArea)>0){
			$sql = '
			update sep_instructor_coordinador
			set are_id = '.$valor.'
			where par_identificacion_instructor = "'.$par_identificacion.'"';
			DB::update($sql);
		}else{
			echo 'El área no existe en la base de datos';
		}
	}
	
    public function getReporteresultados(){
            
            //$instructores = SepParticipante::all()->where('rol_id',2)->lists('par_nombres','par_identificacion');
            $instructorMuestra = DB::select("select * from sep_participante where par_identificacion=" . \Auth::user()->participante->par_identificacion);
            
            $ficha = null;
            $instructoresA = DB::select("SELECT * FROM sep_participante WHERE rol_id = 2 order by par_nombres");
            $instructores = array();
            foreach ($instructoresA as $instructor) {
                $instructores[$instructor->par_identificacion] = $instructor->par_nombres . " " . $instructor->par_apellidos;
            }
            
            /*$sqlPlaneacionFicha="select * from sep_planeacion_ficha "
                    . "where par_identificacion='" . \Auth::user()->participante->par_identificacion ."'";*/
                    
            $sqlPlaneacionFicha="select * from sep_planeacion_ficha "
                    . "where fic_numero in (select fic_numero from sep_ficha where par_identificacion_coordinador='" . \Auth::user()->participante->par_identificacion ."')";
            
            $planeacionA=DB::select($sqlPlaneacionFicha);
            
            foreach ($planeacionA as $planeacion) {
                $planeaciones[$planeacion->plf_id]['plf_cantidad_horas'] = $planeacion->plf_cantidad_horas;
                $planeaciones[$planeacion->plf_id]['fic_numero'] = $planeacion->fic_numero;
                $planeaciones[$planeacion->plf_id]['plf_fecha_inicio'] = $planeacion->plf_fecha_inicio;
                $planeaciones[$planeacion->plf_id]['plf_fecha_fin'] = $planeacion->plf_fecha_fin;
                $planeaciones[$planeacion->plf_id]['plf_calificacion'] = $planeacion->plf_calificacion;
            
                $sqlResultado="select res_nombre,act_descripcion "
                    . "from sep_resultado r, sep_actividad a, sep_actividad_resultado ar "
                    . "where r.res_id=ar.res_id and a.act_id=ar.act_id and r.res_id=" . $planeacion->res_id;
            
                $resultadoA=DB::select($sqlResultado);
                
                $planeaciones[$planeacion->plf_id]['res_nombre'] = $resultadoA[0]->res_nombre;
                $planeaciones[$planeacion->plf_id]['act_descripcion'] = $resultadoA[0]->act_descripcion;
            
                $sqlFicha="select prog_nombre "
                    . "from sep_ficha f, sep_programa p "
                    . "where f.prog_codigo=p.prog_codigo and f.fic_numero=" . $planeacion->fic_numero;
            
                $fichaDbA=DB::select($sqlFicha);
                
                $planeaciones[$planeacion->plf_id]['prog_nombre'] = $fichaDbA[0]->prog_nombre;
            }
            
            return view("Modules.Seguimiento.Reportes.reporteinstructor", compact('planeaciones','instructorMuestra','ficha','instructores'));
	}    
    //public function getResultadosgeneral(){
            
            /*$instructorMuestra = DB::select("select * from sep_participante where par_identificacion=" . \Auth::user()->participante->par_identificacion);
            //$instructores = SepParticipante::all()->where('rol_id',2)->lists('par_nombres','par_identificacion');
            $fichaMuestra = DB::select("select * from sep_ficha where par_identificacion_coordinador=" . \Auth::user()->participante->par_identificacion);
            
            foreach ($fichaMuestra as $ficha) {
                $fichas[$ficha->fic_numero]['fic_numero'] = $ficha->fic_numero;
                $fichas[$ficha->fic_numero]['fic_fecha_inicio'] = $ficha->fic_fecha_inicio;
                $fichas[$ficha->fic_numero]['fic_fecha_fin'] = $ficha->fic_fecha_fin;
                
                $sqlPrograma="select prog_nombre "
                    . "from sep_programa "
                    . "where prog_codigo=" . $ficha->prog_codigo;
            
                $programa=DB::select($sqlPrograma);
                @$fichas[$ficha->fic_numero]['prog_nombre'] = $programa[0]->prog_nombre;
                
                $sqlInstructor="select par_nombres, par_apellidos "
                    . "from sep_participante "
                    . "where par_identificacion=" . $ficha->par_identificacion;
            
                $instructor=DB::select($sqlInstructor);
                @$fichas[$ficha->fic_numero]['ins_lider'] = $instructor[0]->par_nombres . " " . $instructor[0]->par_apellidos;
                
                $sqlResultado="select res_id,plf_calificacion "
                    . "from sep_planeacion_ficha "
                    . "where fic_numero=" . $ficha->fic_numero;
            
                $resultadoA=DB::select($sqlResultado);
                
                $resul = array();
                foreach ($resultadoA as $res) {
                    $resul[$res->res_id] = $res->plf_calificacion;
                }
                
                $sqlResTotales="select res_id,fas_id from sep_actividad_resultado "
                        . "where act_id in(select act_id from sep_actividad "
                        . "where act_version=( select act_version from sep_ficha "
                        . "where fic_numero=" . $ficha->fic_numero . ") and prog_codigo=" . $ficha->prog_codigo . ")";
                
                $resultadosTotalesA=DB::select($sqlResTotales);
                
                //contador total
                $contAnalisis=0;
                $contPlaneacion=0;
                $contEjecucion=0;
                $contEvaluacion=0;
                
                //contador evaluados
                $contAnalisisE=0;
                $contPlaneacionE=0;
                $contEjecucionE=0;
                $contEvaluacionE=0;
                
                foreach($resultadosTotalesA as $resTota){
                    switch($resTota->fas_id){
                        case 1:
                            $contAnalisis++;
                            if(isset($resul[$resTota->res_id]) && $resul[$resTota->res_id]=="SI"){
                                $contAnalisisE++;
                            }
                            break;
                        case 2:
                            $contPlaneacion++;
                            if(isset($resul[$resTota->res_id]) && $resul[$resTota->res_id]=="SI"){
                                $contPlaneacionE++;
                            }
                            break;
                        case 3:
                            $contEjecucion++;
                            if(isset($resul[$resTota->res_id]) && $resul[$resTota->res_id]=="SI"){
                                $contEjecucionE++;
                            }
                            break;
                        case 4:
                            $contEvaluacion++;
                            if(isset($resul[$resTota->res_id]) && $resul[$resTota->res_id]=="SI"){
                                $contEvaluacionE++;
                            }
                            break;
                    }
                    
                }
                
                if($contAnalisis==0){$contAnalisis=1;}
				if($contPlaneacion==0){$contPlaneacion=1;}
				if($contEjecucion==0){$contEjecucion=1;}
				if($contEvaluacion==0){$contEvaluacion=1;}
                
                $fichas[$ficha->fic_numero]['analisis'] = floor(($contAnalisisE*100)/$contAnalisis);
                $fichas[$ficha->fic_numero]['planeacion'] = floor(($contPlaneacionE*100)/$contPlaneacion);
                $fichas[$ficha->fic_numero]['ejecucion'] = floor(($contEjecucionE*100)/$contEjecucion);
                $fichas[$ficha->fic_numero]['evaluacion'] = floor(($contEvaluacionE*100)/$contEvaluacion);
                $fichas[$ficha->fic_numero]['general'] = floor((($contAnalisisE+$contEjecucionE+$contPlaneacionE+$contEvaluacionE)*100)/($contAnalisis+$contEjecucion+$contPlaneacion+$contEvaluacion));
                ---*/
                /*$planeaciones[$planeacion->plf_id]['fic_numero'] = $planeacion->fic_numero;
                $planeaciones[$planeacion->plf_id]['plf_fecha_inicio'] = $planeacion->plf_fecha_inicio;
                $planeaciones[$planeacion->plf_id]['plf_fecha_fin'] = $planeacion->plf_fecha_fin;
                $planeaciones[$planeacion->plf_id]['plf_calificacion'] = $planeacion->plf_calificacion;
            
                $sqlResultado="select res_nombre,act_descripcion "
                    . "from sep_resultado r, sep_actividad a, sep_actividad_resultado ar "
                    . "where r.res_id=ar.res_id and a.act_id=ar.act_id and r.res_id=" . $planeacion->res_id;
            
                $resultadoA=DB::select($sqlResultado);
                
                $planeaciones[$planeacion->plf_id]['res_nombre'] = $resultadoA[0]->res_nombre;
                $planeaciones[$planeacion->plf_id]['act_descripcion'] = $resultadoA[0]->act_descripcion;
            
                
            }*/
            
            
            /*$ficha = null;
            $instructoresA = DB::select("SELECT * FROM sep_participante WHERE rol_id = 2 order by par_nombres");
            $instructores = array();
            foreach ($instructoresA as $instructor) {
                $instructores[$instructor->par_identificacion] = $instructor->par_nombres . " " . $instructor->par_apellidos;
            }*/
            
            /*$sqlPlaneacionFicha="select * from sep_planeacion_ficha "
                    . "where par_identificacion=" . \Auth::user()->participante->par_identificacion;
            
            $planeacionA=DB::select($sqlPlaneacionFicha);
            */
            
            
            //return view("Modules.Seguimiento.Reportes.evaluacionresultados", compact('fichas','instructorMuestra'));
	//}
	public function getParticipantes(){
            return view("Modules.Seguimiento.Reportes.participantes");
	}
	public function getFichas(){
		return view("Modules.Seguimiento.Reportes.fichas");
	}
	public function getAprendiz(){
		return view("Modules.Seguimiento.Reportes.aprendiz");
	}
	public function postAprendiz(Request $request){
		$cedula = $request->input('cedula');
		//dd($request->input('cedula'));

		$etapaPractica = SepEtapaPractica::where('par_identificacion', $cedula)->get();
		
		if($etapaPractica){
			$etapaPractica = $etapaPractica->toArray();
		}
		
		$participante = SepParticipante::where('par_identificacion', $cedula)->first();
		
		if($participante){ 
			$participante = $participante->toArray();
		} // if
		
		if($etapaPractica && $participante){
			foreach($etapaPractica as $key=>$etapa){
				$opcionEtapa = SepOpcionEtapa::where('ope_id', $etapa['ope_id'])->first();
				$etapaPractica[$key]['ope_descripcion'] = $opcionEtapa->ope_descripcion;
				$etapaPractica[$key]['participante'] = $participante;
			} // foreach
		} // if
		else{
			$mensaje = "No se encontraron datos";
		}

		return view("Modules.Seguimiento.Reportes.aprendiz", compact('etapaPractica', 'mensaje'));
    }
	public function getFicha(){
		return view("Modules.Seguimiento.Reportes.ficha");
	}
    public function postFicha(Request $request){
		$this->ficha = $request->input('ficha');
		
		$etpPractica = SepEtapaPractica::select('ope_id', DB::raw('count(ope_id) as total'))
				->whereIn('par_identificacion', function($query)
				{
					$query->select('par_identificacion')
						  ->from('sep_matricula')
						  ->where('fic_numero', $this->ficha);
				})
				->groupBy('ope_id')
				->get()
				->toArray();
		
		$etapaPractica = Array();
		foreach($etpPractica as $etapa){
			$etapaPractica[$etapa['ope_id']] = $etapa['total'];
		} // foreach

		$opcionEtapa = SepOpcionEtapa::all()->toArray();
		
		$totalMatriculados = SepMatricula::where('fic_numero', $this->ficha)->count('mat_id');
		
		return view("Modules.Seguimiento.Reportes.ficha", compact('etapaPractica', 'opcionEtapa', 'totalMatriculados'));
		
	} // postFicha    
	public function getSabana(){
		return view("Modules.Seguimiento.Reportes.sabana");
	}// sabana
	public function postSabana(Request $request){
		
		//Se amplia el limite de memoria en ejecucion
		ini_set("memory_limit", "2048M");

		//Se elimina el tiempo limite para el proceso
		set_time_limit(0);
		//die(dirname(__FILE__).'/Classes/PHPExcel/IOFactory.php');
		//Se incluye la libreria para el archivo en zip
		require_once('zipArchive.lib.php');

		/** Incluir la ruta para las clases de manejo de excel* */
		//set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/Classes/');
		//die(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/Classes/');
		/** Clases necesarias para generar el excel */
		require_once('Classes/PHPExcel.php');
		
		
		$path = getPathUploads()."/plantillas";
		
		//Se captura el numero de ficha a generar
		$ficha = $request->input('ficha');
		
		/*
		if (is_dir("$path/$ficha")) {
			rmdir("$path/$ficha");
		} // if
		*/
		
		//Se genera la carpeta con el numero de la ficha
		mkdir("$path/$ficha", 0777);
		
		//Se captura el instructor y el programa de formacion de la ficha
		$resFicha = SepFicha::select('par_identificacion', 'prog_codigo')
				->where('fic_numero', $ficha)
				->get()
				->toArray();
		
		$this->prog_codigo = $resFicha[0]['prog_codigo'];
				
		//Se captura el nombre del programa de formacion
		$resPrograma = SepPrograma::select('prog_nombre')
				->where('prog_codigo', $resFicha[0]['prog_codigo'])
				->get()
				->toArray();
		
		//Se capturan los nombres y apellidos del instructor lider
		$resInstructor = SepParticipante::select('par_nombres','par_apellidos')
				->where('par_identificacion', $resFicha[0]['par_identificacion'])
				->get()
				->toArray();
		
		//Se capturan las identificaciones de los aprendices pertenecientes a la ficha
		$resMatricula = SepMatricula::select('par_identificacion')
				->where('fic_numero', $ficha)
				->get()
				->toArray();
		
					 
		//Se captura la ultima version de la matriz pedagogica del programa de formacion
		$resVersion = SepActividad::select(DB::raw('max(act_version) as version'))
				->where('prog_codigo', $resFicha[0]['prog_codigo'])
				->get()
				->toArray();
		
		//Ciclo que se repite por cada fase
		for ($fa = 1; $fa <= 4; $fa++) {
			if ($fa == 1) {
				$nomFase = "ANALISIS";
			} else if ($fa == 2) {
				$nomFase = "PLANEACION";
			} else if ($fa == 3) {
				$nomFase = "EJECUCION";
			} else if ($fa == 4) {
				$nomFase = "EVALUACION";
			}
			
			//Se crea el directorio con el nombre de cada fase, dentro del directorio de la ficha
			mkdir("$path/$ficha/$nomFase", 0700);

			//Se capturan los codigos de las actividades del programa por cada fase
			$resActividad = SepActividadResultado::select('act_id')
				->where('fas_id', $fa)
				->whereIn('act_id', function($query)
				{
					$query->select('act_id')
						  ->from('sep_actividad')
						  ->where('prog_codigo', $this->prog_codigo);
				})
				->groupBy('act_id')
				->get()
				->toArray();
			
			//Se capturan los codigos de los resultados con sus respectivas actividades del programa por cada fase
			$resFase = SepActividadResultado::select('act_id','res_id')
				->where('fas_id', $fa)
				->whereIn('act_id', function($query)
				{
					$query->select('act_id')
						  ->from('sep_actividad')
						  ->where('prog_codigo', $this->prog_codigo);
				})
				->get()
				->toArray();
			
			//Ciclo que se repite por cada aprendiz de la ficha
			//for($i=0;$i<COUNT($resMatricula);$i++)
			for ($i = 0; $i < 2; $i++) {
				
				//Se capturan los datos de cada aprendiz
				$resAprendiz = SepParticipante::select('par_nombres','par_apellidos')
					->where('par_identificacion', $resMatricula[$i]['par_identificacion'])
					->get()
					->toArray();
				
				//Se carga el archivo modelo de excel
				$objPHPexcel = PHPExcel_IOFactory::load($path.'/sabana.xlsx');

				//Se carga la hoja actual del documento cargado
				$objWorksheet = $objPHPexcel->getActiveSheet();

				//Se capturan las celdas del documento de acuerdo al nombre de cada una, y se les asigna el valor indicado
				$objWorksheet->getCell('programa')->setValue($resPrograma[0]['prog_nombre']);
				$objWorksheet->getCell('instructor')->setValue($resInstructor[0]['par_nombres'] . ' ' . $resInstructor[0]['par_apellidos']);
				$objWorksheet->getCell('ficha')->setValue($ficha);
				$objWorksheet->getCell('fase')->setValue($nomFase);
				$objWorksheet->getCell('aprendiz')->setValue($resAprendiz[0]['par_nombres'] . ' ' . $resAprendiz[0]['par_apellidos']);
				$objWorksheet->getCell('documento')->setValue($resMatricula[$i]['par_identificacion']);

				//Ciclo por cada actividad de la fase
				for ($acti = 1; $acti <= COUNT($resActividad); $acti++) {
					//Se captura el nombre de la actividad
					$resNomAct = SepActividad::select('act_descripcion')
						->where('act_id', $resActividad[$acti - 1]['act_id'])
						->get()
						->toArray();

					//Se asigna el nombre de la actividad en la celda indicada
					$objWorksheet->getCell('actividad' . $acti)->setValue($resNomAct[0]['act_descripcion']);
					$numeResul = 1;

					//Ciclo por cada resultado de las actividades en la fase
					for ($resu = 1; $resu <= COUNT($resFase); $resu++) {
						//Condicion para que unicamente muestre los resultados de la actividad actual
						if ($resFase[$resu - 1]['act_id'] == $resActividad[$acti - 1]['act_id']) {
							//Se captura el nombre del resultado de la actividad
							$resNomRes = SepResultado::select('res_nombre')
								->where('res_id', $resFase[$resu - 1]['res_id'])
								->get()
								->toArray();
							
							//Se asigna el nombre del resultado en la celda indicada
							$objWorksheet->getCell('resultado' . $acti . $numeResul)->setValue($resNomRes[0]['res_nombre']);
							$numeResul++;
						}
					}
				}

				//Se crea el archivo con el numero de identificacion del estudiante
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5');
				$objWriter->save("$path/$ficha/$nomFase/" . $resMatricula[$i]['par_identificacion'] . '.xls');
			}
		} // for

		// Cargando la hoja de c�lculo
		//Generando y descargo el zip
		$zip = new zipArchive1();

		$zip = comprime("$path/$ficha/", "$path/$ficha/", $zip);

		$pathSave = "$path/$ficha.zip";
		$zip->saveZip($pathSave);
		echo "<script type='text/javascript'>window.location.href='$path/$ficha.zip';</script>";
		
	}// sabana
	function comprime($ubicacion, $carpeta, $zip) {
		$dir = $ubicacion;
		$zip->addDir($carpeta);
		$directorio = opendir($ubicacion);
		while ($archivo = readdir($directorio)) {
			if (!is_dir("$dir/$archivo")) {
				$zip->addFile($dir . '/' . $archivo, "$carpeta/$archivo");
			} else {
				if ($archivo != "." && $archivo != "..") {
					$nuevaUbicacion = $ubicacion . "/" . $archivo . "/";
					comprime($nuevaUbicacion, $carpeta . $archivo . "/", $zip);
				}
			}
		}
		closedir($directorio);
		return $zip;
	} // comprime
	public function getReportepractica(){
		$page = Input::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;
		
		$sqlTotal = DB::select("
		SELECT COUNT(*) AS total 
		FROM sep_ficha");
		
		$sqlFicha = DB::select("
		SELECT 
			fic.fic_numero, niv_for.niv_for_id, niv_for_nombre, pro.prog_nombre, 
			fic_fecha_inicio,fic_fecha_fin, fic_localizacion, 
			CONCAT(par_nombres,' ',par_apellidos) AS nombre,
			IF(niv_for.niv_for_id = '1',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 821 DAY),
			IF(niv_for.niv_for_id = '2',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 912 DAY),
			IF(niv_for.niv_for_id = '3',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 1003 DAY),
			IF(niv_for.niv_for_id = '4',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 1277 DAY),'No se calculo la fecha')))) AS fecha_terminacion
		FROM 
			sep_ficha AS fic, sep_participante AS par, 
			sep_programa As pro, sep_nivel_formacion AS niv_for
		WHERE 
			fic.prog_codigo = pro.prog_codigo 
			AND fic.par_identificacion = par.par_identificacion 
			AND pro.niv_for_id = niv_for.niv_for_id");
		
		$instructores = DB::select("
		SELECT * 
		FROM sep_participante 
		WHERE rol_id=2");
		
		$sqlFicha = new LengthAwarePaginator(
                array_slice($sqlFicha, $offset, $perPage, true), count($sqlFicha), $perPage, $page);
        $sqlFicha->setPath("reportepractica");
		
		return view("Modules.Seguimiento.Reportes.consultaPractica",compact("sqlFicha","sqlTotal","offset","instructores"));
	}
	
	public function getConsultafiltro(){
		extract($_GET);
		/******
		*  nivelFormacion = 1 = Operarios
		*  nivelFormacion = 2 = Técnicos
		*  nivelFormacion = 4 = Tecnólogos
		******/
		$sqlFechaMenor = " TIMESTAMPDIFF(day,STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'),now())>=";
		$sqlFechaMayor = " TIMESTAMPDIFF(day,STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'),now())<=";
		$sqlNivelFormacion = " AND niv_for.niv_for_id = ";
		$and = " and ";
		$and2 = " and ";
		$rango = "";
		
		if($vIngresado == 1 || $vIngresado == 5 || $vIngresado == 9 || $vIngresado == 13 || $vIngresado == 16){
			/*--     Están en tiempo de práctica     --*/
			if($vIngresado == 1 ){
				$fechaMenor1 = 91; $fechaMayor1 = 182; $nivelFormacion1 = 1; 
				$fechaMenor2 = 182; $fechaMayor2 = 365; $nivelFormacion2 = 2;
				$fechaMenor3 = 547; $fechaMayor3 = 730; $nivelFormacion3 = 4;
			}
			/*--     Salen a práctica el próximo trimestre     --*/
			else if($vIngresado == 5){
				$fechaMenor1 = 0; $fechaMayor1 = 91; $nivelFormacion1 = 1;
				$fechaMenor2 = 91; $fechaMayor2 = 182; $nivelFormacion2 = 2;
				$fechaMenor3 = 456; $fechaMayor3 = 527; $nivelFormacion3 = 4;
			}
			/*--     Fecha terminación por tiempo ficha     --*/
			else if($vIngresado == 9 || $vIngresado == 16){
				$fechaMenor1 = ""; $fechaMayor1 = 821; $nivelFormacion1 = 1;
				$fechaMenor2 = ""; $fechaMayor2 = 912; $nivelFormacion2 = 2;
				$fechaMenor3 = ""; $fechaMayor3 = 1277; $nivelFormacion3 = 4;
				$sqlFechaMayor = "";
				$and = "";
				$rango = "
				and fic.fic_numero in (
					select fic.fic_numero
					from sep_ficha as fic,sep_matricula as mat,sep_participante as par
					where 
					fic.fic_numero = mat.fic_numero and 
					par.par_identificacion = mat.par_identificacion and 
					par.rol_id = 1 and
					mat.est_id in(2,6,8) 
					GROUP by mat.fic_numero
				) ";
			}
			/*--     Terminan práctica este trimestre     --*/
			else if($vIngresado == 13){
				$fechaMenor1 = 91; $fechaMayor1 = 182; $nivelFormacion1 = 1;
				$fechaMenor2 = 273; $fechaMayor2 = 365; $nivelFormacion2 = 2;
				$fechaMenor3 = 638; $fechaMayor3 = 730; $nivelFormacion3 = 4;
			}
			
			$sql = "
			SELECT 
				fic.fic_numero, niv_for.niv_for_id, niv_for_nombre, pro.prog_nombre, 
				fic_fecha_inicio,fic_fecha_fin, fic_localizacion, 
				CONCAT(par_nombres,' ',par_apellidos) AS nombre,
				IF(niv_for.niv_for_id = '1',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 821 DAY),
				IF(niv_for.niv_for_id = '2',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 912 DAY),
				IF(niv_for.niv_for_id = '3',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 1003 DAY),
				IF(niv_for.niv_for_id = '4',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 1277 DAY),'No se calculo la fecha')))) AS fecha_terminacion
			FROM 
				sep_ficha AS fic, sep_programa AS pro, sep_participante AS par, 
				sep_nivel_formacion AS niv_for
			WHERE 
				$sqlFechaMenor$fechaMenor1$and$sqlFechaMayor$fechaMayor1 
				and fic.prog_codigo = pro.prog_codigo 
				AND fic.par_identificacion = par.par_identificacion 
				AND pro.niv_for_id = niv_for.niv_for_id 
				AND niv_for.niv_for_id = $nivelFormacion1 $rango
				OR
				$sqlFechaMenor$fechaMenor2$and$sqlFechaMayor$fechaMayor2 
				and fic.prog_codigo = pro.prog_codigo 
				AND fic.par_identificacion = par.par_identificacion 
				AND pro.niv_for_id = niv_for.niv_for_id 
				AND niv_for.niv_for_id = $nivelFormacion2 $rango
				OR
				$sqlFechaMenor$fechaMenor3$and$sqlFechaMayor$fechaMayor3 
				and fic.prog_codigo = pro.prog_codigo 
				AND fic.par_identificacion = par.par_identificacion 
				AND pro.niv_for_id = niv_for.niv_for_id 
				AND niv_for.niv_for_id = $nivelFormacion3 $rango
				
				ORDER BY niv_for_nombre";
		}else{ 
			$sqlFechaMenor = ""; $sqlFechaMayor = ""; $sqlNivelFormacion = "";
			$fechaMenor = ""; $fechaMayor = ""; $nivelFormacion = "";
			$and = ""; $and2 = ""; 
			
			$sql = "
			SELECT 
				fic.fic_numero, niv_for.niv_for_id, niv_for_nombre, pro.prog_nombre, 
				fic_fecha_inicio,fic_fecha_fin, fic_localizacion, 
				CONCAT(par_nombres,' ',par_apellidos) AS nombre,
				IF(niv_for.niv_for_id = '1',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 821 DAY),
				IF(niv_for.niv_for_id = '2',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 912 DAY),
				IF(niv_for.niv_for_id = '3',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 1003 DAY),
				IF(niv_for.niv_for_id = '4',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 1277 DAY),'No se calculo la fecha')))) AS fecha_terminacion
			FROM 
				sep_ficha AS fic, sep_programa AS pro, sep_participante AS par, 
				sep_nivel_formacion AS niv_for
			WHERE 
				$sqlFechaMenor$fechaMenor$and$sqlFechaMayor$fechaMayor$and2 
				fic.prog_codigo = pro.prog_codigo 
				AND fic.par_identificacion = par.par_identificacion 
				AND pro.niv_for_id = niv_for.niv_for_id 
				$sqlNivelFormacion $nivelFormacion";
				
		}
		$fichas = DB::select($sql);
		
		return view("Modules.Seguimiento.Reportes.resultadoFiltro",compact("vIngresado","fichas"));
	}
	public function getInputtotal(){
		extract($_GET);
		/******
		*  nivelFormacion = 1 = Operarios
		*  nivelFormacion = 2 = Técnicos
		*  nivelFormacion = 4 = Tecnólogos
		******/
		$sqlFechaMenor = " TIMESTAMPDIFF(day,STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'),now())>=";
		$sqlFechaMayor = " TIMESTAMPDIFF(day,STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'),now())<=";
		$sqlNivelFormacion = " AND niv_for.niv_for_id = ";
		$and = " and ";
		$and2 = " and ";
		$rango = "";
		
		if($vIngresado == 1 || $vIngresado == 5 || $vIngresado == 9 || $vIngresado == 13 || $vIngresado == 16){
			/*--     Tiempo idoneo     --*/
			if($vIngresado == 1 ){
				$fechaMenor1 = 91; $fechaMayor1 = 182; $nivelFormacion1 = 1; 
				$fechaMenor2 = 182; $fechaMayor2 = 365; $nivelFormacion2 = 2;
				$fechaMenor3 = 547; $fechaMayor3 = 730; $nivelFormacion3 = 4;
			}
			/*--     Último trimestre esta practica     --*/
			else if($vIngresado == 5){
				$fechaMenor1 = 0; $fechaMayor1 = 91; $nivelFormacion1 = 1;
				$fechaMenor2 = 91; $fechaMayor2 = 182; $nivelFormacion2 = 2;
				$fechaMenor3 = 456; $fechaMayor3 = 527; $nivelFormacion3 = 4;
			}
			/*--     Tiempo máximo cumplido     --*/
			else if($vIngresado == 9 || $vIngresado == 16){
				$fechaMenor1 = ""; $fechaMayor1 = 821; $nivelFormacion1 = 1;
				$fechaMenor2 = ""; $fechaMayor2 = 912; $nivelFormacion2 = 2;
				$fechaMenor3 = ""; $fechaMayor3 = 1277; $nivelFormacion3 = 4;
				$sqlFechaMayor = "";
				$and = "";
				$rango = "
				and fic.fic_numero in (
					select fic.fic_numero
					from sep_ficha as fic,sep_matricula as mat,sep_participante as par
					where 
					fic.fic_numero = mat.fic_numero and 
					par.par_identificacion = mat.par_identificacion and 
					par.rol_id = 1 and
					mat.est_id in(2,6,8) 
					GROUP by mat.fic_numero
				) ";
			}
			/*--     Termina etapa productiva este trimestre     --*/
			else if($vIngresado == 13){
				$fechaMenor1 = 91; $fechaMayor1 = 182; $nivelFormacion1 = 1;
				$fechaMenor2 = 273; $fechaMayor2 = 365; $nivelFormacion2 = 2;
				$fechaMenor3 = 638; $fechaMayor3 = 730; $nivelFormacion3 = 4;
			}
			$sql = "
			SELECT 
				count(*) as total
			FROM 
				sep_ficha AS fic, sep_programa AS pro, sep_participante AS par, 
				sep_nivel_formacion AS niv_for
			WHERE 
				$sqlFechaMenor$fechaMenor1$and$sqlFechaMayor$fechaMayor1 
				and fic.prog_codigo = pro.prog_codigo 
				AND fic.par_identificacion = par.par_identificacion 
				AND pro.niv_for_id = niv_for.niv_for_id 
				AND niv_for.niv_for_id = $nivelFormacion1 $rango
				OR
				$sqlFechaMenor$fechaMenor2$and$sqlFechaMayor$fechaMayor2 
				and fic.prog_codigo = pro.prog_codigo 
				AND fic.par_identificacion = par.par_identificacion 
				AND pro.niv_for_id = niv_for.niv_for_id 
				AND niv_for.niv_for_id = $nivelFormacion2 $rango
				OR
				$sqlFechaMenor$fechaMenor3$and$sqlFechaMayor$fechaMayor3 
				and fic.prog_codigo = pro.prog_codigo 
				AND fic.par_identificacion = par.par_identificacion 
				AND pro.niv_for_id = niv_for.niv_for_id 
				AND niv_for.niv_for_id = $nivelFormacion3 $rango
				ORDER BY niv_for_nombre";
		}else{ 
			$sqlFechaMenor = ""; $sqlFechaMayor = ""; $sqlNivelFormacion = "";
			$fechaMenor = ""; $fechaMayor = ""; $nivelFormacion = "";
			$and = ""; $and2 = ""; 
				
			$sql = "
			SELECT 
				count(*) as total
			FROM 
				sep_ficha AS fic, sep_programa AS pro, sep_participante AS par, 
				sep_nivel_formacion AS niv_for
			WHERE 
				$sqlFechaMenor$fechaMenor$and$sqlFechaMayor$fechaMayor$and2 
				fic.prog_codigo = pro.prog_codigo 
				AND fic.par_identificacion = par.par_identificacion 
				AND pro.niv_for_id = niv_for.niv_for_id 
				$sqlNivelFormacion $nivelFormacion";
		}
		$fichas = DB::select($sql);
		
		foreach($fichas as $fic){
			echo $fic->total;
		}
	}
	public function getInputconsulta(){
		extract($_GET);
		
		if(is_numeric($vIngresado)){
			$sql = "
			SELECT 
				fic.fic_numero, niv_for.niv_for_id, niv_for_nombre, pro.prog_nombre, 
				fic_fecha_inicio,fic_fecha_fin, fic_localizacion, 
				CONCAT(par_nombres,' ',par_apellidos) AS nombre,
				IF(niv_for.niv_for_id = '1',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 821 DAY),
				IF(niv_for.niv_for_id = '2',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 912 DAY),
				IF(niv_for.niv_for_id = '3',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 1003 DAY),
				IF(niv_for.niv_for_id = '4',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 1277 DAY),'No se calculo la fecha')))) AS fecha_terminacion
			FROM 
				sep_ficha AS fic, sep_participante AS par, sep_programa As pro, 
				sep_nivel_formacion AS niv_for
			WHERE 
				fic.prog_codigo = pro.prog_codigo 
				AND fic.par_identificacion = par.par_identificacion
				AND pro.niv_for_id = niv_for.niv_for_id
				AND fic.fic_numero LIKE '$vIngresado%'";
		}else{
			$sql = "
			SELECT 
				fic.fic_numero, niv_for.niv_for_id, niv_for_nombre, pro.prog_nombre, 
				fic_fecha_inicio,fic_fecha_fin, fic_localizacion, 
				CONCAT(par_nombres,' ',par_apellidos) AS nombre,
				IF(niv_for.niv_for_id = '1',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 821 DAY),
				IF(niv_for.niv_for_id = '2',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 912 DAY),
				IF(niv_for.niv_for_id = '3',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 1003 DAY),
				IF(niv_for.niv_for_id = '4',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 1277 DAY),'No se calculo la fecha')))) AS fecha_terminacion
			FROM 
				sep_ficha AS fic, sep_participante AS par, sep_programa As pro, 
				sep_nivel_formacion AS niv_for
			WHERE 
				fic.prog_codigo = pro.prog_codigo 
				AND fic.par_identificacion = par.par_identificacion
				AND pro.niv_for_id = niv_for.niv_for_id
				AND pro.prog_nombre LIKE '$vIngresado%'";
		}
		$fichas = DB::select($sql);
		
		return view("Modules.Seguimiento.Reportes.resultadoFiltro",compact("fichas"));
	}
	public function getInputtotalconsulta(){
		extract($_GET);
		
		if(is_numeric($vIngresado)){
			$sqlFicha = DB::select("
			SELECT 
				COUNT(*) AS total
			FROM 
				sep_ficha AS fic, sep_participante AS par, sep_programa As pro, 
				sep_nivel_formacion AS niv_for
			WHERE 
				fic.prog_codigo = pro.prog_codigo 
				AND fic.par_identificacion = par.par_identificacion
				AND pro.niv_for_id = niv_for.niv_for_id
				AND fic.fic_numero LIKE '$vIngresado%'");
		}else{
			$sqlFicha = DB::select("
			SELECT 
				COUNT(*) AS total
			FROM 
				sep_ficha AS fic, sep_participante AS par, sep_programa As pro, 
				sep_nivel_formacion AS niv_for
			WHERE 
				fic.prog_codigo = pro.prog_codigo 
				AND fic.par_identificacion = par.par_identificacion
				AND pro.niv_for_id = niv_for.niv_for_id
				AND pro.prog_nombre LIKE '$vIngresado%'");
		}
		foreach($sqlFicha AS $total){
			echo $total->total;
		}
	}
	public function getSelinstructor(){
		extract($_GET);
		
		$sql="
		SELECT 
			fic.fic_numero, niv_for.niv_for_id, niv_for_nombre, pro.prog_nombre, 
			fic_fecha_inicio,fic_fecha_fin, fic_localizacion, 
			CONCAT(par_nombres,' ',par_apellidos) AS nombre,
			IF(niv_for.niv_for_id = '1',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 821 DAY),
			IF(niv_for.niv_for_id = '2',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 912 DAY),
			IF(niv_for.niv_for_id = '3',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 1003 DAY),
			IF(niv_for.niv_for_id = '4',ADDDATE(STR_TO_DATE(fic_fecha_inicio,'%m/%d/%Y'), INTERVAL 1277 DAY),'No se calculo la fecha')))) AS fecha_terminacion
		FROM 
			sep_ficha AS fic, sep_programa AS pro, sep_participante AS par, 
			sep_nivel_formacion AS niv_for
		WHERE 
			fic.prog_codigo = pro.prog_codigo 
			AND fic.par_identificacion = par.par_identificacion 
			AND pro.niv_for_id = niv_for.niv_for_id 
			AND fic.par_identificacion = '$vIngresado'";
		
		$fichas = DB::select($sql);
		
		return view("Modules.Seguimiento.Reportes.resultadoFiltro",compact("fichas"));
	}
	public function getSelinstructortotal(){
		extract($_GET);
		
		$consulta="
		SELECT 
			COUNT(*) AS total
		FROM 
			sep_ficha AS fic, sep_programa AS pro, sep_participante AS par, sep_nivel_formacion AS niv_for
		WHERE 
		fic.prog_codigo = pro.prog_codigo AND
		fic.par_identificacion = par.par_identificacion AND
		pro.niv_for_id = niv_for.niv_for_id AND
		fic.par_identificacion = '$vIngresado'";
		
		$sqlFicha = DB::select($consulta);
		
		foreach($sqlFicha AS $total){
			echo $total->total;
		}
	}
	public function getAprendicesficha(){
		extract($_GET);
		$aprendices = DB::select("
		SELECT 
			mat.par_identificacion,par_nombres,
			par_apellidos,par_telefono,par_correo,est_descripcion
		FROM 
			sep_matricula AS mat, sep_participante AS par,
			sep_estado AS est, sep_roles AS rol, sep_ficha AS fic
		WHERE 
			mat.par_identificacion = par.par_identificacion
			AND mat.est_id = est.est_id
			AND mat.fic_numero = fic.fic_numero
			AND par.rol_id = rol.id_rol
			AND par.rol_id = 1
			and est.est_id in(2,6,8)
			AND mat.fic_numero = $ficha");
		
		$arrOpcEtapa = array();
		
		foreach($aprendices as $apr){
			$sqlOpcEtapa = "
			SELECT 
				par_identificacion_aprendiz,ope_descripcion
			FROM 
				sep_seguimiento_productiva as seg_pro, sep_opcion_etapa as opc_eta
			where 
				seg_pro.ope_id = opc_eta.ope_id and
				seg_pro.par_identificacion_aprendiz = '$apr->par_identificacion'
				group by par_identificacion_aprendiz";
				
			$opcEtapa = DB::select($sqlOpcEtapa);
			
			foreach($opcEtapa as $opc){
				$arrOpcEtapa[$opc->par_identificacion_aprendiz] = $opc->ope_descripcion;
			}
		}
		
		$totalAprendices = DB::select("
		SELECT 
			COUNT(*) AS total
		FROM 
			sep_matricula AS mat, sep_participante AS par,
			sep_estado AS est, sep_roles AS rol, sep_ficha AS fic
		WHERE 
			mat.par_identificacion = par.par_identificacion
			AND mat.est_id = est.est_id
			AND mat.fic_numero = fic.fic_numero
			AND par.rol_id = rol.id_rol
			AND par.rol_id = 1
			AND mat.fic_numero = $ficha");
		
		$programa = DB::select("
			SELECT prog_nombre
			FROM sep_ficha AS fic, sep_programa AS pro
			WHERE fic.prog_codigo = pro.prog_codigo
			AND fic.fic_numero = $ficha");
		
		$estados = DB::select("SELECT * FROM sep_estado");
		
		$sqlAlternativa = "
		select *
		from 
			sep_etapa_practica as prac,sep_matricula as mat,
			sep_participante as par,sep_estado as est,sep_ficha as fic,sep_roles as rol
		where 
			par.par_identificacion = prac.par_identificacion
			and mat.par_identificacion = par.par_identificacion
			AND mat.est_id = est.est_id
			AND mat.fic_numero = fic.fic_numero
			AND par.rol_id = rol.id_rol
			AND par.rol_id = 1
			AND mat.fic_numero = $ficha";
			
		$alternativa = DB::select($sqlAlternativa);
		
		return view("Modules.Seguimiento.Reportes.ajaxAprendicesFicha",compact("arrOpcEtapa","alternativa","ficha","aprendices","programa","totalAprendices","estados"));
	}
	public function getAjaxaprendizestado(){
		extract($_GET);
		if($estado !=0){
			$aprendices = DB::select("
			SELECT 
				mat.par_identificacion,par_nombres,
				par_apellidos,par_telefono,par_correo,est_descripcion
			FROM 
				sep_matricula AS mat, sep_participante AS par,
				sep_estado AS est, sep_roles AS rol, sep_ficha AS fic
			WHERE 
				mat.par_identificacion = par.par_identificacion
				AND mat.est_id = est.est_id
				AND mat.fic_numero = fic.fic_numero
				AND par.rol_id = rol.id_rol
				AND par.rol_id = 1
				AND mat.est_id = $estado
				AND fic.fic_numero = $ficha");
		}else{
			$aprendices = DB::select("
			SELECT 
				mat.par_identificacion,par_nombres,
				par_apellidos,par_telefono,par_correo,est_descripcion
			FROM 
				sep_matricula AS mat, sep_participante AS par,
				sep_estado AS est, sep_roles AS rol, sep_ficha AS fic
			WHERE 
				mat.par_identificacion = par.par_identificacion
				AND mat.est_id = est.est_id
				AND mat.fic_numero = fic.fic_numero
				AND par.rol_id = rol.id_rol
				AND par.rol_id = 1
				AND fic.fic_numero = $ficha");
		}
		
		return view("Modules.Seguimiento.Reportes.ajaxaprendizestado",compact("aprendices"));
	}
	
	
	public function getCoordinador(Request $request){
		$sqlNombreProgramas="select prog_codigo,prog_nombre from sep_programa";
		//PROGRAMAS
		$NombresProgramas = DB::select($sqlNombreProgramas);
                $nombreProgramas= array();
                foreach($NombresProgramas as $nomProg){
                    $nombreProgramas[$nomProg->prog_codigo]=$nomProg->prog_nombre;
                }
                
                $sqlFichasProgramas="select fic_numero,prog_codigo from sep_ficha where prog_codigo in (select prog_codigo from sep_programa where niv_for_id not in (5,6)) group by prog_codigo,fic_numero";
                
		//FICHAS
		$FichasProgramas = DB::select($sqlFichasProgramas);
                $fichaPrograma= array();
                foreach($FichasProgramas as $ficProg){
                    $fichaPrograma[$ficProg->prog_codigo][$ficProg->fic_numero]=$ficProg->fic_numero;
                }
                //dd($fichaPrograma);
                $sqlCoordinadores="select * from sep_participante where rol_id=3";
		//COORDINADORES
		$coordinadores = DB::select($sqlCoordinadores);
		//dd($coordinadores);
		$fichasCoordinadores= array();
		$contCoordinador=0;
		
		//AQUI INICIA LA FECHA
		$fechaInicio="";
		$fechaFin="";
		
		$condiFecha="";
		
		if($fechaInicio!=""){
		    if($fechaFin!=""){
		        $condiFecha.=" and  between '$fechaInicio' and '$fechaFin'";
		    }
		    else{
		        $condiFecha.=" and  >= '$fechaInicio'";
		    }
		}
		else{
		    if($fechaFin!=""){
		        $condiFecha.=" and  <= '$fechaFin'";
		    }
		}
		
		foreach($coordinadores as $coordinador){
			$sqlFichasCoordinadores="select fic_numero,prog_codigo,fic_fecha_inicio "
					. "from sep_ficha "
					. "where par_identificacion_coordinador ='".$coordinador->par_identificacion . "' $condiFecha";
			$fichasCoordinadoresC = DB::select($sqlFichasCoordinadores);
			//FICHAS POR COORDINADOR
			$fichasCoordinadores[$coordinador->par_identificacion]=$fichasCoordinadoresC;
			$cedulaCoor[$contCoordinador]=$coordinador->par_identificacion;
			$contCoordinador++;
		}
		//dd($fichasCoordinadores);
		$etapaPractica = Array();
		$contCoordinador=0;
		foreach($fichasCoordinadores as $fichaCoordinador){
			foreach($fichaCoordinador as $ficCoor){
				$fic= $ficCoor->fic_numero;
				$this->ficha = $ficCoor->fic_numero;
		
				$sqlEtpPractica="select ope_id,count(ope_id) as total "
						. "from sep_etapa_practica "
						. "where par_identificacion in"
						. "(select par_identificacion "
						. "from sep_matricula "
						. "where fic_numero='$fic') group by ope_id";
				
                                /*$sqlEtpPractica="select ope_id,count(ope_id) as total "
						. "from sep_seguimiento_productiva "
						. "where par_identificacion_aprendiz in"
						. "(select par_identificacion "
						. "from sep_matricula "
						. "where fic_numero=$fic) group by ope_id";
				*/
				$etapPractica=DB::select($sqlEtpPractica);
				
				foreach($etapPractica as $etapa){
					//ETAPA PRACTICA POR FICHA
					$etapaPractica[$cedulaCoor[$contCoordinador]][$fic][$etapa->ope_id] = $etapa->total;
				}

				$totalMatriculados[$cedulaCoor[$contCoordinador]][$fic] = SepMatricula::where('fic_numero', $fic)->count('mat_id');

			}
			$contCoordinador++;
		}
		//dd($etapaPractica);
		$etapaPracticaProg = Array();
		for($i=0;$i<COUNT($cedulaCoor);$i++){
				
		//revisar
				$sqlEtpPractica="select ope_id, count(ope_id) as total, m.fic_numero, f.prog_codigo 
					from sep_etapa_practica s, sep_ficha f, sep_matricula m 
					where s.par_identificacion=m.par_identificacion 
					and m.fic_numero=f.fic_numero and s.par_identificacion in
					(select par_identificacion from sep_matricula 
					where fic_numero in (select fic_numero from sep_ficha where par_identificacion_coordinador=" . $cedulaCoor[$i] . ")) 
					group by prog_codigo,ope_id";
                               /* $sqlEtpPractica="select ope_id, count(ope_id) as total, m.fic_numero, f.prog_codigo 
					from sep_seguimiento_productiva s, sep_ficha f, sep_matricula m 
					where s.par_identificacion_aprendiz=m.par_identificacion 
					and m.fic_numero=f.fic_numero and s.par_identificacion_aprendiz in
					(select par_identificacion from sep_matricula 
					where fic_numero in (select fic_numero from sep_ficha where par_identificacion_coordinador=" . $cedulaCoor[$i] . ")) 
					group by prog_codigo,ope_id";	*/			
		//dd($sqlEtpPractica);
				$etapPractica=DB::select($sqlEtpPractica);
						
				foreach($etapPractica as $etapa){
					//ETAPA PRACTICA POR PROGRAMA
					$etapaPracticaProg[$cedulaCoor[$i]][$etapa->prog_codigo][$etapa->ope_id] = $etapa->total;
				}

			   // $totalMatriculados[$cedulaCoor[$i]][$fic] = SepMatricula::where('fic_numero', $fic)->count('mat_id');

		}
		//dd($etapaPracticaProg);
		$etapaPracticaCoor = Array();
		for($i=0;$i<COUNT($cedulaCoor);$i++){

                                $sqlEtpPractica="select ope_id, count(ope_id) as total, par_identificacion_coordinador 
					from sep_etapa_practica s, sep_ficha f, sep_matricula m 
					where s.par_identificacion=m.par_identificacion 
					and m.fic_numero=f.fic_numero and s.par_identificacion in(
					select par_identificacion 
					from sep_matricula 
					where fic_numero in (select fic_numero from sep_ficha where par_identificacion_coordinador=" . $cedulaCoor[$i] . ")) 
					group by par_identificacion_coordinador, ope_id";
				
				/*$sqlEtpPractica="select ope_id, count(ope_id) as total, par_identificacion_coordinador 
					from sep_seguimiento_productiva s, sep_ficha f, sep_matricula m 
					where s.par_identificacion_aprendiz=m.par_identificacion 
					and m.fic_numero=f.fic_numero and s.par_identificacion_aprendiz in(
					select par_identificacion 
					from sep_matricula 
					where fic_numero in (select fic_numero from sep_ficha where par_identificacion_coordinador=" . $cedulaCoor[$i] . ")) 
					group by par_identificacion_coordinador, ope_id";
				*/
                                 $etapPractica=DB::select($sqlEtpPractica);
                                 
						
				foreach($etapPractica as $etapa){
					//ETAPA PRACTICA POR PROGRAMA
					$etapaPracticaCoor[$cedulaCoor[$i]][$etapa->ope_id] = $etapa->total;
				}

			   // $totalMatriculados[$cedulaCoor[$i]][$fic] = SepMatricula::where('fic_numero', $fic)->count('mat_id');

		}
		
		//dd($etapaPracticaCoor);
		//$sqlFichasCoordinadores="select sep_participante from sep_participante where rol_id=3";
		//$fichasCoordinadores = DB::select($sqlFichasCoordinadores);
		
	   
		
		$opcionEtapa = SepOpcionEtapa::all()->toArray();
		
		
		
		return view("Modules.Seguimiento.Reportes.coordinacion", compact('etapaPractica', 'opcionEtapa', 'totalMatriculados','coordinadores','fichasCoordinadores','etapaPractica','etapaPracticaProg','etapaPracticaCoor','nombreProgramas','fichaPrograma'));
		
	}
	
	public function getReporteseguimiento(){
		extract($_GET);
        $limit = 10;
        $pagina = 1;
		$alternativas = DB::select("select * from sep_opcion_etapa");	
		$sqlAprendices = "
		select fic_numero,matricula.par_identificacion,par_nombres,par_apellidos,par_correo
		from sep_matricula as matricula,sep_participante as participante
		where matricula.par_identificacion = participante.par_identificacion 
		and participante.rol_id = 1 and est_id = 2 order by fic_numero limit ".$limit."";
		$aprendices = DB::select($sqlAprendices);
		$aprConAlternativa = DB::select("
		select par_identificacion_aprendiz,opcion.ope_id,ope_descripcion
		from sep_seguimiento_productiva as productiva, sep_opcion_etapa as opcion 
		where productiva.ope_id = opcion.ope_id and not opcion.ope_id = 11");
		$arrayAlternativa = array();
		foreach($aprConAlternativa as $apr){
			$arrayAlternativa[$apr->par_identificacion_aprendiz] = $apr->ope_descripcion;
		}
	    $sql="select count(matricula.par_identificacion) as total from sep_matricula as matricula,sep_participante as participante
		where matricula.par_identificacion = participante.par_identificacion 
		and participante.rol_id = 1 and est_id = 2 order by fic_numero";
		$sqlContador = DB::select($sql);
		$totAprendices = $sqlContador[0]->total;
        $cantidadPaginas = ceil($totAprendices/$limit);
        $contador = (($pagina-1)*$limit)+1;
		return view("Modules.Seguimiento.Reportes.repoteseguimiento",compact("alternativas","aprendices","arrayAlternativa","totAprendices",'cantidadPaginas','contador','pagina'));
	}
	public function getFiltros(){
	 	extract($_GET);
		$registroPorPagina = 10; 
	  	if(isset($pagina) && $pagina != 1){
			if($pagina == 2){
				$limit = "10,10";
			}else{  
			$hubicacionPagina = $registroPorPagina*($pagina-1);
			$limit = $hubicacionPagina.','.$registroPorPagina;
			}
		}else{
			$pagina = 1;
			$limit = "0,10";
		}
	  	$filtro=explode(",",$filtros);
	  	$consulta=$fecha=$array="";
	 	if($filtro[0] != "vacio" || $filtro[1] != "vacio" || $filtro[2] != "vacio"){	
			if ($filtro[0] != "vacio") {
				if ($filtro[0] == 1 || $filtro[0] == 3 || $filtro[0] == 6) {
					if ($filtro[0] == 6) {
						$visita = " in(1,3)";
					}else{
						$visita = " = ".$filtro[0];
					}
					$consulta = $consulta." and !exists (select * from sep_seguimiento_visita as vis
						where seg_vis_visita ".$visita." and seg_pro_id = pro.seg_pro_id)";
				}
				if ($filtro[0] == 2 || $filtro[0] == 5) {
					$bitacora = 1;
					if ($filtro[0] == 5) {
						$bitacora = 12;
					}
					$consulta = $consulta." and !exists (select * from  sep_seguimiento_bitacora bit
						where bit.seg_bit_bitacora = ".$bitacora." 
						and bit.seg_pro_id = pro.seg_pro_id)";
				}
				if ($filtro[0] == 4) {
					$fecha = " , pro.seg_pro_fecha_fin as fecha";
					$consulta = $consulta." having DATE_SUB(NOW(), INTERVAL 30 day) < fecha";
				}
			}
			if ($filtro[1] != "vacio") {
				$consulta=$consulta." and pro.ope_id = ".$filtro[1]."";
			}
			if ($filtro[2] != "vacio") {
				$consulta=$consulta." and pro.fic_numero like '%".$filtro[2]."%'";
			}
			$sqlAprendices="select pro.fic_numero, par.par_identificacion, par.par_nombres, par.par_apellidos,par.par_correo,opt.ope_descripcion ".$fecha." 
			from sep_seguimiento_productiva pro, sep_participante par , sep_opcion_etapa opt
			where pro.par_identificacion_aprendiz = par.par_identificacion 
			and par.rol_id = 1 and opt.ope_id = pro.ope_id ".$consulta." order by pro.fic_numero limit ".$limit."";			
			$sqlContador="select count(par.par_identificacion) as total 
			from sep_seguimiento_productiva pro, sep_participante par , sep_opcion_etapa opt
			where pro.par_identificacion_aprendiz = par.par_identificacion 
			and par.rol_id = 1 and opt.ope_id = pro.ope_id ".$consulta." order by pro.fic_numero";
		}else{
			$sqlAprendices = "
			select fic_numero,matricula.par_identificacion,par_nombres,par_apellidos,par_correo
			from sep_matricula as matricula,sep_participante as participante
			where matricula.par_identificacion = participante.par_identificacion 
			and participante.rol_id = 1 and est_id = 2 order by fic_numero limit ".$limit."";
			
			$sqlContador="select count(matricula.par_identificacion) as total from sep_matricula as matricula,sep_participante as participante
			where matricula.par_identificacion = participante.par_identificacion 
			and participante.rol_id = 1 and est_id = 2 order by fic_numero";

			$array="si";
		}
	
		if ($filtro[2] != "vacio" && $filtro[0] == "vacio" && $filtro[1] == "vacio") {
			$sqlAprendices="select matricula.fic_numero,matricula.par_identificacion,par_nombres,par_apellidos,par_correo
			from sep_matricula as matricula,sep_participante as participante
			where matricula.par_identificacion = participante.par_identificacion 
			and participante.rol_id = 1 and est_id = 2 
			and matricula.fic_numero =".$filtro[2]." order by matricula.par_identificacion asc limit ".$limit."";
		
			$sqlContador="select count(matricula.par_identificacion) as total
			from sep_matricula as matricula,sep_participante as participante
			where matricula.par_identificacion = participante.par_identificacion 
			and participante.rol_id = 1 and est_id = 2 and matricula.fic_numero = ".$filtro[2]." order by matricula.par_identificacion asc";
		    $array="si";
		}

		$aprConAlternativa = DB::select("
		select par_identificacion_aprendiz,opcion.ope_id,ope_descripcion
		from sep_seguimiento_productiva as productiva, sep_opcion_etapa as opcion 
		where productiva.ope_id = opcion.ope_id and not opcion.ope_id = 11");
		$arrayAlternativa = array();
		foreach($aprConAlternativa as $apr){
			$arrayAlternativa[$apr->par_identificacion_aprendiz] = $apr->ope_descripcion;
		}

		$aprendices = DB::select($sqlAprendices);
		$sqlContador = DB::select($sqlContador);
		$totAprendices = $sqlContador[0]->total;
        $cantidadPaginas = ceil($totAprendices/$registroPorPagina);
		$contador = $limit + 1;
		return view("Modules.Seguimiento.Reportes.reporte",compact("aprendices","totAprendices","arrayAlternativa","array","cantidadPaginas","contador","pagina"));
	}

/* filtrado de reporte viejo: 
    public function getAlternativa(){
		extract($_GET);
		
		if($vIngresado == ""){
			$sqlAprendices = "
				select 	fic_numero,par.par_identificacion, par.par_identificacion_actual,par_nombres,par_apellidos,par_correo,
					ifnull((
						select 	ope_descripcion 
						from 	sep_seguimiento_productiva productiva, sep_opcion_etapa opcion 
						where 	productiva.ope_id = opcion.ope_id 
						and		par_identificacion_aprendiz = mat.par_identificacion), 'Sin alternativa') as ope_descripcion
				from 	sep_matricula mat, sep_participante par
				where 	mat.par_identificacion = par.par_identificacion  
				and		mat.est_id = 2
				and 	par.rol_id = 1
				order by fic_numero";
		
			$aprendices = DB::select($sqlAprendices);
			$alternativa = "SI";
			return view("Modules.Seguimiento.Reportes.reporte",compact("alternativa","vIngresado","aprendices"));
		}else{
			if($vIngresado != 11){
				$sqlAprendices = "
					select 	mat.fic_numero,par.par_identificacion, par.par_identificacion_actual,par_nombres,par_apellidos,par_correo,ope_descripcion
					from 	sep_matricula mat, sep_participante par, sep_seguimiento_productiva pro, sep_opcion_etapa opcion
					where 	mat.par_identificacion = par.par_identificacion  
					and		mat.est_id = 2 
					and 	par.rol_id = 1
					and 	pro.par_identificacion_Aprendiz = mat.par_identificacion
					and 	pro.ope_id = opcion.ope_id 
					and 	pro.ope_id = $vIngresado
					order by mat.fic_numero";
			}else{
				$sqlAprendices = "
					select 	fic_numero,par.par_identificacion, par.par_identificacion_actual,par_nombres,par_apellidos,par_correo
					from 	sep_matricula mat, sep_participante par
					where 	mat.par_identificacion = par.par_identificacion  
					and		mat.est_id = 2
					and 	par.rol_id = 1
					and par.par_identificacion 
					not in (	select 	par_identificacion_aprendiz 
								from 	sep_seguimiento_productiva 
								where 	not ope_id = 11		)
					order by mat.fic_numero";
			}
			
			$aprendices = DB::select($sqlAprendices);
			$alternativa = "SI";
			return view("Modules.Seguimiento.Reportes.reporte",compact("alternativa","vIngresado","aprendices"));
		}
	}
	
	public function getReporte(){
		extract($_GET);
		
		$select = " 
			productiva.seg_pro_id,matricula.fic_numero, participante.par_identificacion_actual,participante. par_identificacion,
			par_nombres, par_apellidos, par_correo, seg_pro_fecha_ini, ope_descripcion ";
		
		$from = "
			sep_seguimiento_productiva as productiva,
			sep_participante as participante,
			sep_opcion_etapa as opcion,
            sep_matricula as matricula ";
		
		$where = " 
			productiva.par_identificacion_aprendiz = participante.par_identificacion and
			productiva.ope_id = opcion.ope_id and 
            matricula.par_identificacion= participante.par_identificacion and 
            matricula.est_id = 2 and
			not productiva.ope_id = 11 and ";
		
		if($vIngresado == 1){
			$sql = "
				select 	$select, date_add(seg_pro_fecha_ini, interval 15 day) as fechaSumada
				from 	$from
				where 	$where
						!exists (select * from  sep_seguimiento_bitacora where seg_bit_bitacora = 1 
						and 	seg_pro_id = productiva.seg_pro_id) 
						having 	curdate() > fechaSumada
						order by fic_numero";
		}else if($vIngresado == 2){
			$sql = "
				select 	$select, date_add(seg_pro_fecha_ini, interval 30 day) as fechaSumada
				from 	$from
				where 	$where
						!exists (select * from  sep_seguimiento_visita 
						where 	seg_vis_visita = 1 
						and 	seg_pro_id = productiva.seg_pro_id) 
						and		est_id = 2 
						and 	rol_id = 1
						having 	curdate() > fechaSumada
						order by fic_numero";
					
		}else if($vIngresado == 3){
			$sql = "
				select 	$select
				from 	$from
				where 	$where
						curdate() >= DATE_SUB(seg_pro_fecha_fin, INTERVAL 30 day) 
						and curdate() <= seg_pro_fecha_fin
						order by fic_numero";
		}else if($vIngresado == 4){
			$sql = "
				select	productiva.seg_pro_id,matricula.fic_numero, participante.par_identificacion_actual,participante.par_identificacion,
						par_nombres, par_apellidos, par_correo, seg_pro_fecha_ini, ope_descripcion,
						(if(floor((timestampdiff(day,seg_pro_fecha_ini,curdate()))/15) > 12,'12',floor((timestampdiff(DAY,seg_pro_fecha_ini,curdate()))/15))) AS nBitacoras,
						(select count(*) from sep_seguimiento_bitacora where seg_pro_id = productiva.seg_pro_id) as totBitacoras
				from 	sep_seguimiento_productiva as productiva,
						sep_participante as participante,
						sep_opcion_etapa as opcion,
						sep_matricula as matricula
				where	productiva.par_identificacion_aprendiz = participante.par_identificacion 
				and 	productiva.ope_id = opcion.ope_id 
				and 	matricula.par_identificacion = participante.par_identificacion 
				and		matricula.est_id = 2
				and	not productiva.ope_id = 11
				having 	totBitacoras < nBitacoras
				order by fic_numero";
		}else if($vIngresado == 5){
			$sql = "
				select 	$select, date_add(seg_pro_fecha_fin, interval 8 day) as fechaSumada 
				from 	$from 
				where 	$where
						!exists (select * from sep_seguimiento_visita as vis
						where 	seg_vis_visita = 3 and 
								seg_pro_id = productiva.seg_pro_id)
								having curdate() > fechaSumada
								order by fic_numero";
		}else if($vIngresado == 6){
			$sql = "
				select 	$select, date_add(seg_pro_fecha_fin, interval 8 day) as fechaSumada 
				from 	$from 
				where 	$where
					!exists (select * from sep_seguimiento_visita as vis
					where seg_vis_visita in(1,3) 
					and seg_pro_id = productiva.seg_pro_id)
					having curdate() > fechaSumada
					order by fic_numero";
		}else{
			$sqlAprendices = "
				select 	fic_numero,par.par_identificacion,par.par_identificacion_actual,par_nombres,par_apellidos,par_correo,
					ifnull((
						select 	ope_descripcion 
						from 	sep_seguimiento_productiva productiva, sep_opcion_etapa opcion 
						where 	productiva.ope_id = opcion.ope_id 
						and		par_identificacion_aprendiz = mat.par_identificacion), 'Sin alternativa') as ope_descripcion
				from 	sep_matricula mat, sep_participante par
				where 	mat.par_identificacion = par.par_identificacion  
				and		mat.est_id = 2
				and 	par.rol_id = 1
				order by fic_numero";
		
			$aprendices = DB::select($sqlAprendices);
			$alternativa = "SI";
			return view("Modules.Seguimiento.Reportes.reporte",compact("alternativa","vIngresado","aprendices"));
		}
		
		if($vIngresado <> ""){
			$aprendices = DB::select($sql);
			return view("Modules.Seguimiento.Reportes.reporte",compact("aprendices"));
		}
	}*/

	
		
	public function getModal(){
		$id=$_GET['id'];			
		$nombre=$_GET['nombre'];			
		$apellido=$_GET['apellido'];			
        
		$alternativasA = DB::select("select * from sep_opcion_etapa order by ope_id");

		$alternativas = array();
		
		foreach ($alternativasA as $alternativa) {
			$alternativas[$alternativa->ope_id] = $alternativa->ope_descripcion;
		}
		
		$seguiProductiva = DB::select("select * from sep_seguimiento_productiva where par_identificacion_aprendiz='$id'");
		
		$bitacoras = array();
		$visitas = array();
		
		$seg_pro_nombre_empresa = "";
		$seg_pro_fecha_ini = "";
		$seg_pro_fecha_fin = "";
		$ope_id = "";
		$seg_pro_obs_lider_productiva = "";
		$seg_pro_obs_instructor_seguimiento = "";
		$observaciones_instructor = "";
		$observaciones_lider = "";
		$disabled = "";
		$readonly = "";
		
		if(count($seguiProductiva)>0){
			$seguiBitacora = DB::select("select * from sep_seguimiento_bitacora where seg_pro_id=" . $seguiProductiva[0]->seg_pro_id);
			
			$seguiVisita = DB::select("select * from sep_seguimiento_visita where seg_pro_id=" . $seguiProductiva[0]->seg_pro_id . " order by seg_vis_visita");
			//dd($seguiVisita);
			foreach ($seguiBitacora as $bitacora) {
				$bitacoras[$bitacora->seg_bit_id] = $bitacora->seg_bit_bitacora;
			}

			foreach ($seguiVisita as $visita) {
				$visitas[$visita->seg_vis_id] = $visita->seg_vis_visita;
				$fechas[$visita->seg_vis_id] = $visita->seg_vis_fecha;
			}
			
			foreach ($seguiProductiva as $datos) {
				$seg_pro_nombre_empresa = $datos->seg_pro_nombre_empresa;
				$seg_pro_fecha_ini = $datos->seg_pro_fecha_ini;
				$seg_pro_fecha_fin = $datos->seg_pro_fecha_fin;
				$ope_id = $datos->ope_id;
				$seg_pro_obs_lider_productiva = $datos->seg_pro_obs_lider_productiva;
				$seg_pro_obs_instructor_seguimiento = $datos->seg_pro_obs_instructor_seguimiento;
			}
		}
		$rol = DB::select("select rol_id as rol from sep_participante where par_identificacion = ". \Auth::user()->par_identificacion ."");
		
		if($rol[0]->rol == 2){
			$observaciones_lider = "readonly";
		}else{
			$disabled = "disabled";
			$readonly = "readonly";
		}
		
		return view("Modules.Seguimiento.Reportes.modales",compact("observaciones_lider","fechas","rol","seguiVisita","readonly","disabled","seg_pro_obs_instructor_seguimiento","ope_id","seg_pro_fecha_fin","seg_pro_fecha_ini","seg_pro_nombre_empresa","seg_pro_obs_lider_productiva","alternativas","id","nombre","apellido","seguiProductiva","visitas","bitacoras"));
	}
	
		
	

    /*
	public function getHorasinstructor(){
		extract($_GET);
		//dd($_GET);
		if(isset($pla_fec_tri_id)){
			$sqlArea = '';
			if($are_id != ''){  
				$sqlArea = 'and ins_coo.are_id = '.$are_id;  
			}
			$concatenar_pla_fec_tri_id = implode(',', $pla_fec_tri_id);
			if(isset($par_identificacion_coordinador) and $par_identificacion_coordinador != ''){
				$sqlCoordinador = '';
				if($par_identificacion_coordinador != 'todas'){
					$sqlCoordinador = 'and par_identificacion_coordinador = "'.$par_identificacion_coordinador.'"';
				}
				$sql = '
					select 	par_identificacion_coordinador, 
							concat(substring_index(coordinador.par_nombres," ",1)," ",substring_index(coordinador.par_apellidos," ",1)) as nombreCoordinador,
							par_identificacion_instructor, 
							concat(instructor.par_nombres," ",instructor.par_apellidos) as nombreInstructor,
							instructor.par_horas_semanales, ins_coo.are_id, are_descripcion
					from 	sep_instructor_coordinador ins_coo, sep_participante coordinador, sep_participante instructor, sep_area area, users user
					where 	ins_coo.par_identificacion_coordinador = coordinador.par_identificacion
					and 	ins_coo.par_identificacion_instructor = instructor.par_identificacion '.$sqlCoordinador.'
					and 	ins_coo.are_id = area.are_id '.$sqlArea.'
					and 	instructor.par_identificacion = user.par_identificacion
					and 	instructor.rol_id = 2
					and 	user.estado = "1"
					order 	by nombreInstructor';
				$instructores_coordinacion = DB::select($sql);

				$sql = '
					select 	par_horas_semanales, par.par_identificacion, sum(pla_fic_det_hor_totales) as horasAsignadas, 
							pla_fic_det_fec_fin, par_identificacion_coordinador, pla_trimestre_numero_year
					from 	sep_planeacion_ficha_detalle p_f_d, sep_participante par, sep_instructor_coordinador ins_coo
					where 	p_f_d.par_id_instructor = par.par_identificacion
					and 	ins_coo.par_identificacion_instructor = par.par_identificacion
					and 	not pla_tip_id = 5 '.$sqlCoordinador.' '.$sqlArea.'
					and 	pla_trimestre_numero_year in ('.$concatenar_pla_fec_tri_id.')
					group 	by p_f_d.par_id_instructor, pla_fic_det_fec_fin';
				$horas_instructores_coordinacion = DB::select($sql);
			}else{
				$concatenar_instructores = $this->concatenarString($par_identificacion);
				//dd($concatenar_instructores);
				$sql = '
					select 	par_identificacion_coordinador, 
							concat(substring_index(coordinador.par_nombres," ",1)," ",substring_index(coordinador.par_apellidos," ",1)) as nombreCoordinador,
							par_identificacion_instructor, 
							concat(instructor.par_nombres," ",instructor.par_apellidos) as nombreInstructor,
							instructor.par_horas_semanales, ins_coo.are_id, are_descripcion
					from 	sep_instructor_coordinador ins_coo, sep_participante coordinador, sep_participante instructor, sep_area area, users user
					where 	ins_coo.par_identificacion_coordinador = coordinador.par_identificacion
					and 	ins_coo.par_identificacion_instructor = instructor.par_identificacion 
					and 	instructor.par_identificacion '.$concatenar_instructores.'
					and 	ins_coo.are_id = area.are_id
					and 	instructor.par_identificacion = user.par_identificacion
					and 	instructor.rol_id = 2
					and 	user.estado = "1"
					order 	by nombreInstructor';
				$instructores_coordinacion = DB::select($sql);

				$sql = '
					select 	par_horas_semanales, par.par_identificacion, sum(pla_fic_det_hor_totales) as horasAsignadas, 
							pla_fic_det_fec_fin, par_identificacion_coordinador, pla_trimestre_numero_year
					from 	sep_planeacion_ficha_detalle p_f_d, sep_participante par, sep_instructor_coordinador ins_coo
					where 	p_f_d.par_id_instructor = par.par_identificacion
					and 	ins_coo.par_identificacion_instructor = par.par_identificacion
					and 	par.par_identificacion '.$concatenar_instructores.'
					and 	not pla_tip_id = 5
					and 	pla_trimestre_numero_year in ('.$concatenar_pla_fec_tri_id.')
					group 	by p_f_d.par_id_instructor, pla_fic_det_fec_fin';
				$horas_instructores_coordinacion = DB::select($sql);

				//dd($horas_instructores_coordinacion);
			}

			$horasInstructores = array();
			foreach($instructores_coordinacion as $ins){
				$horasInstructores[$ins->par_identificacion_coordinador]['nombre'] = $ins->nombreCoordinador;
				$horasInstructores[$ins->par_identificacion_coordinador]['instructor'][$ins->par_identificacion_instructor]['nombre'] = $ins->nombreInstructor;
				$horasInstructores[$ins->par_identificacion_coordinador]['instructor'][$ins->par_identificacion_instructor]['are_id'] = $ins->are_id;
				$horasInstructores[$ins->par_identificacion_coordinador]['instructor'][$ins->par_identificacion_instructor]['are_descripcion'] = $ins->are_descripcion;
				$horasInstructores[$ins->par_identificacion_coordinador]['instructor'][$ins->par_identificacion_instructor]['horas_semanales'] = $ins->par_horas_semanales;
				if(!isset($horasInstructores[$ins->par_identificacion_coordinador]['cantidadContratistas'])){
					if($ins->par_horas_semanales == 32){
						$horasInstructores[$ins->par_identificacion_coordinador]['cantidadFuncionarios'] = 1;
						$horasInstructores[$ins->par_identificacion_coordinador]['cantidadContratistas'] = 0;
					}else{
						$horasInstructores[$ins->par_identificacion_coordinador]['cantidadContratistas'] = 1;
						$horasInstructores[$ins->par_identificacion_coordinador]['cantidadFuncionarios'] = 0;
					}
				}else{
					if($ins->par_horas_semanales == 32){
						$horasInstructores[$ins->par_identificacion_coordinador]['cantidadFuncionarios']++;
					}else{
						$horasInstructores[$ins->par_identificacion_coordinador]['cantidadContratistas']++;
					}
				}
			}
			
			// Asignamos las horas de cada instructor para cada trimestre seleccionado
			foreach($pla_fec_tri_id as $trimestre){
				foreach($horasInstructores as $keyCoordinador => $coordinador){
					foreach($coordinador['instructor'] as $keyInstructor => $instructor){
						$horasInstructores[$keyCoordinador]['instructor'][$keyInstructor]['trimestres'][$trimestre]['horasAsignadas'] = 0;
						$horasInstructores[$keyCoordinador]['instructor'][$keyInstructor]['trimestres'][$trimestre]['horasDisponibles'] = $instructor['horas_semanales'];
					}
				}
			}
			
			// Asignamos las horas que tienen los instructores programadas en los horarios
			foreach($horas_instructores_coordinacion as $ins){
				$horasLibres = $ins->par_horas_semanales - $ins->horasAsignadas;
				$horasInstructores[$ins->par_identificacion_coordinador]['instructor'][$ins->par_identificacion]['trimestres'][$ins->pla_trimestre_numero_year]['horasAsignadas'] = $ins->horasAsignadas;
				$horasInstructores[$ins->par_identificacion_coordinador]['instructor'][$ins->par_identificacion]['trimestres'][$ins->pla_trimestre_numero_year]['horasDisponibles'] = $horasLibres;
			}

			foreach($horasInstructores as $keyCoordinador => $coordinador){
				foreach($coordinador['instructor'] as $instructor){
					foreach($instructor['trimestres'] as $key => $trimestre){
						if(!isset($horasInstructores[$keyCoordinador]['trimestres'][$key]['horasAsignadas'])){
							$horasInstructores[$keyCoordinador]['trimestres'][$key]['horasAsignadas'] = $trimestre['horasAsignadas'];
							$horasInstructores[$keyCoordinador]['trimestres'][$key]['horasDisponibles'] = $trimestre['horasDisponibles'];
						}else{
							$horasInstructores[$keyCoordinador]['trimestres'][$key]['horasAsignadas'] += $trimestre['horasAsignadas'];
							$horasInstructores[$keyCoordinador]['trimestres'][$key]['horasDisponibles'] += $trimestre['horasDisponibles'];
						}
					}
				}
			}
			$cantidad_trimestres = count($pla_fec_tri_id);
			$concatenar_trimestres = $this->concatenar($pla_fec_tri_id);
			$fechas = DB::select('select * from sep_planeacion_fecha_trimestre where pla_fec_tri_id '.$concatenar_trimestres);
		}
		
		$coordinadores = DB::select('select par_identificacion,par_nombres,par_apellidos from sep_participante where rol_id = 3 and not par_identificacion = "16759526" order by par_nombres');
		$areas = DB::select('select * from sep_area');
		$rol = \Auth::user()->participante->rol_id;
		$trimestres = DB::select('select pla_fec_tri_id, pla_fec_tri_year, pla_fec_tri_trimestre, pla_fec_tri_fec_inicio, pla_fec_tri_fec_fin from sep_planeacion_fecha_trimestre');
		$arrayTrimestres = array();
		foreach($trimestres as $key => $val){
			$arrayTrimestres[$val->pla_fec_tri_id]['year'] = $val->pla_fec_tri_year;
			$arrayTrimestres[$val->pla_fec_tri_id]['numero'] = $val->pla_fec_tri_trimestre;
		}
	    //dd($horasInstructores);
		$instructores_db = DB::select('select par_identificacion,concat(par_nombres," ",par_apellidos) as nombre from sep_participante where rol_id = 2 order by par_nombres');
		return view('Modules.Seguimiento.Reportes.horasInstructor', compact('arrayTrimestres','horasInstructores','totalContratistasFuncionarios','are_id','areas','par_identificacion_coordinador','rol','coordinadores','pla_fec_tri_id','par_identificacion','fechas','cantidad_trimestres','trimestres','instructores','instructores_db','instructorHoras'));
	}
	*/
	
	public function getHorasinstructor(){
		$_GET = $this->seguridad($_GET);
		extract($_GET);

		if(isset($anio)){
			// Si selecciona al Coordinador consulte
			$concatenar_coordinador = '';
			$concatenar_instructor = '';
			$concatenar_from_sql = ' , users u';
			$concatenar_where_sql = ' and u.par_identificacion = instructor.par_identificacion and u.estado = "1" and instructor.rol_id = "2"';
			if($par_identificacion_coordinador != ''){
				if($par_identificacion_coordinador != 'todas'){
					$concatenar_coordinador = 'and par_identificacion_coordinador = "'.$par_identificacion_coordinador.'"';
				}
			}else{
				$concatenar_instructor = ' and instructor.par_identificacion in ('.(implode(',', $par_identificacion)).')';
				$concatenar_from_sql = '';
				$concatenar_where_sql = '';
			}

			$sql = '
				select 	par_identificacion_coordinador,
						concat(substring_index(coordinador.par_nombres," ",1)," ",substring_index(coordinador.par_apellidos," ",1)) as nombreCoordinador,
						par_identificacion_instructor,
						concat(instructor.par_nombres," ",instructor.par_apellidos) as nombreInstructor,
						instructor.par_horas_semanales
				from 	sep_instructor_coordinador ins_coo, sep_participante coordinador,
						sep_participante instructor '.$concatenar_from_sql.'
				where 	ins_coo.par_identificacion_coordinador = coordinador.par_identificacion
				and 	ins_coo.par_identificacion_instructor = instructor.par_identificacion
						'.$concatenar_coordinador.'  '.$concatenar_instructor.' '.$concatenar_where_sql.'
				order 	by nombreInstructor';
			$instructores_coordinacion = DB::select($sql);
			//echo '<pre>'.$sql; print_r($instructores_coordinacion); dd();
			if(count($instructores_coordinacion)>0){
				foreach($instructores_coordinacion as $val){
					$id[] = $val->par_identificacion_instructor;
				}
				$concatenar_instructor = ' and par.par_identificacion in ('.(implode(',', $id)).')';
			
				$fechas = [];
				foreach($anio as $year){
					$fecha_anio_mes = explode('-', $year);
					$fecha_inicio = $fecha_anio_mes[0].'-'.$fecha_anio_mes[1].'-01';
					$fecha_fin = date("Y-m-t", strtotime($fecha_inicio));
					$dias_mes[$fecha_fin] = '';

					foreach($instructores_coordinacion as $ins){
						$nombres[$ins->par_identificacion_coordinador] = $ins->nombreCoordinador;
						$nombres[$ins->par_identificacion_instructor] = $ins->nombreInstructor;
						$horasInstructores[$ins->par_identificacion_coordinador][$ins->par_identificacion_instructor][$fecha_inicio]['fecha_fin'] = $fecha_fin;
						$horasInstructores[$ins->par_identificacion_coordinador][$ins->par_identificacion_instructor][$fecha_inicio]['horas_semanales'] = $ins->par_horas_semanales * 4;
						$horasInstructores[$ins->par_identificacion_coordinador][$ins->par_identificacion_instructor][$fecha_inicio]['horas_formacion_directa'] = 0;
						$horasInstructores[$ins->par_identificacion_coordinador][$ins->par_identificacion_instructor][$fecha_inicio]['horas_formacion_complementario'] = 0;
						$horasInstructores[$ins->par_identificacion_coordinador][$ins->par_identificacion_instructor][$fecha_inicio]['horas_restriccion'] = 0;
						$horasInstructores[$ins->par_identificacion_coordinador][$ins->par_identificacion_instructor][$fecha_inicio]['horas_etapa_práctica'] = 0;
						$horasInstructores[$ins->par_identificacion_coordinador][$ins->par_identificacion_instructor][$fecha_inicio]['horas_totales'] = 0;
						
						/*if(!isset($horasInstructores[$ins->par_identificacion_coordinador]['cantidadContratistas'])){
							if($ins->par_horas_semanales == 32){
								$horasInstructores[$ins->par_identificacion_coordinador]['cantidadFuncionarios'] = 1;
								$horasInstructores[$ins->par_identificacion_coordinador]['cantidadContratistas'] = 0;
							}else{
								$horasInstructores[$ins->par_identificacion_coordinador]['cantidadContratistas'] = 1;
								$horasInstructores[$ins->par_identificacion_coordinador]['cantidadFuncionarios'] = 0;
							}
						}else{
							if($ins->par_horas_semanales == 32){
								$horasInstructores[$ins->par_identificacion_coordinador]['cantidadFuncionarios']++;
							}else{
								$horasInstructores[$ins->par_identificacion_coordinador]['cantidadContratistas']++;
							}
						}*/
					}
					//dd($horasInstructores);
					$sql = '
						select 	*, par.par_horas_semanales, par.par_identificacion,
								pla_fic_det_fec_fin, par_identificacion_coordinador, pla_trimestre_numero_year,
								concat(substring_index(coordinador.par_nombres," ",1)," ",substring_index(coordinador.par_apellidos," ",1)) as nombreCoordinador,
								concat(par.par_nombres," ",par.par_apellidos) as nombreInstructor
						from 	sep_planeacion_ficha_detalle p_f_d, sep_participante par,
								sep_instructor_coordinador ins_coo, sep_participante coordinador
						where 	p_f_d.par_id_instructor = par.par_identificacion
						and 	ins_coo.par_identificacion_instructor = par.par_identificacion
						and 	ins_coo.par_identificacion_coordinador = coordinador.par_identificacion
						and		((pla_fic_det_fec_inicio < "'.$fecha_inicio.'" and (pla_fic_det_fec_fin > "'.$fecha_inicio.'" and pla_fic_det_fec_fin <= "'.$fecha_fin.'"))
							or 	((pla_fic_det_fec_inicio >= "'.$fecha_inicio.'" and pla_fic_det_fec_inicio < "'.$fecha_fin.'") and pla_fic_det_fec_fin > "'.$fecha_fin.'")
							or 	(pla_fic_det_fec_inicio < "'.$fecha_inicio.'" and pla_fic_det_fec_fin > "'.$fecha_fin.'")
							or 	(pla_fic_det_fec_inicio >= "'.$fecha_inicio.'" and pla_fic_det_fec_fin <= "'.$fecha_fin.'"))
							'.$concatenar_instructor.' '.$concatenar_coordinador.'
						and 	not pla_tip_id = 5';
					$horas_instructores_coordinacion = DB::select($sql);

					foreach($horas_instructores_coordinacion as $ins){
						if($ins->pla_fic_det_fec_inicio < $fecha_inicio and $ins->pla_fic_det_fec_fin > $fecha_fin){
							$fecha_inicio_copia = $fecha_inicio;
							$fecha_fin_copia = $fecha_fin;
						}else if($ins->pla_fic_det_fec_inicio < $fecha_inicio and $ins->pla_fic_det_fec_fin <= $fecha_fin){
							$fecha_inicio_copia = $fecha_inicio;
							$fecha_fin_copia = $ins->pla_fic_det_fec_fin;
						}else if($ins->pla_fic_det_fec_inicio >= $fecha_inicio and $ins->pla_fic_det_fec_fin > $fecha_fin){
							$fecha_inicio_copia = $ins->pla_fic_det_fec_inicio;
							$fecha_fin_copia = $fecha_fin;
						}else{
							$fecha_inicio_copia = $ins->pla_fic_det_fec_inicio;
							$fecha_fin_copia = $ins->pla_fic_det_fec_fin;
						}

						//echo $fecha_inicio_copia.' '.$fecha_fin_copia.'<br>';
						$contador_dias = 0;
						while($fecha_inicio_copia <= $fecha_fin_copia){
							$dia = date('N', strtotime($fecha_inicio_copia));
							if($dia == $ins->pla_dia_id){
								$contador_dias++;
							}
							$fecha_inicio_copia = date('Y-m-d', strtotime($fecha_inicio_copia.' + 1 day'));
						}

						$horas_por_dias = $contador_dias * $ins->pla_fic_det_hor_totales;

						//echo $ins->pla_fic_det_fec_inicio.' '.$ins->pla_fic_det_fec_fin.'<br>';

						if($ins->pla_tip_id == 1 or $ins->pla_tip_id == 2 or $ins->pla_tip_id == 7){
							$horasInstructores[$ins->par_identificacion_coordinador][$ins->par_identificacion_instructor][$fecha_inicio]['horas_formacion_directa'] += $horas_por_dias;
						}else if($ins->pla_tip_id == 3){
							$horasInstructores[$ins->par_identificacion_coordinador][$ins->par_identificacion_instructor][$fecha_inicio]['horas_formacion_complementario'] += $horas_por_dias;
						}else if($ins->pla_tip_id == 4){
							$horasInstructores[$ins->par_identificacion_coordinador][$ins->par_identificacion_instructor][$fecha_inicio]['horas_restriccion'] += $horas_por_dias;
						}else if($ins->pla_tip_id == 6){
							$horasInstructores[$ins->par_identificacion_coordinador][$ins->par_identificacion_instructor][$fecha_inicio]['horas_etapa_práctica'] += $horas_por_dias;
						}
						$horasInstructores[$ins->par_identificacion_coordinador][$ins->par_identificacion_instructor][$fecha_inicio]['horas_totales'] += $horas_por_dias;
					}
				}
			}
			//echo '<pre>'.$sql; print_r($horasInstructores); dd();

			//echo '<pre>'; print_r($dias_mes); dd();
		}

		$coordinadores = DB::select('select par_identificacion,par_nombres,par_apellidos from sep_participante where rol_id = 3 and not par_identificacion = "16759526" order by par_nombres');
		$areas = DB::select('select * from sep_area');
		$rol = \Auth::user()->participante->rol_id;
		$trimestres = DB::select('select pla_fec_tri_id, pla_fec_tri_year, pla_fec_tri_trimestre, pla_fec_tri_fec_inicio, pla_fec_tri_fec_fin from sep_planeacion_fecha_trimestre');
		$arrayTrimestres = array();
		foreach($trimestres as $key => $val){
			$arrayTrimestres[$val->pla_fec_tri_id]['year'] = $val->pla_fec_tri_year;
			$arrayTrimestres[$val->pla_fec_tri_id]['numero'] = $val->pla_fec_tri_trimestre;
		}
		//dd($horasInstructores);
		$anio_actual = date('Y');
		$mes_actual = date('m');
		$meses = [
			1=>'enero', 'febrero', 'marzo', 'abril',
			'mayo', 'junio', 'julio', 'agosto',
			'septiembre', 'octubre', 'noviembre', 'diciembre'
		];

		$sql = '
			select 	par_identificacion,concat(par_nombres," ",par_apellidos) as nombre
			from 	sep_participante
			where 	rol_id = 2 order by par_nombres';
		$instructores_db = DB::select($sql);
		return view('Modules.Seguimiento.Reportes.horasInstructor', compact('anio', 'nombres', 'dias_mes', 'mes_actual', 'meses', 'anio_actual', 'arrayTrimestres','horasInstructores','totalContratistasFuncionarios','are_id','areas','par_identificacion_coordinador','rol','coordinadores','pla_fec_tri_id','par_identificacion','fechas','cantidad_trimestres','trimestres','instructores','instructores_db','instructorHoras'));
	}

	public function concatenar($array){
		$concatenar = ' in (';
		foreach($array as $val){ $concatenar .= $val.','; }
		$concatenar = substr($concatenar,0,-1);
		$concatenar .= ')';

		return $concatenar;
	}

	public function concatenarString($array){
		$concatenar = ' in (';
		foreach($array as $val){ $concatenar .= "'$val',"; }
		$concatenar = substr($concatenar,0,-1);
		$concatenar .= ')';

		return $concatenar;
	}
}
