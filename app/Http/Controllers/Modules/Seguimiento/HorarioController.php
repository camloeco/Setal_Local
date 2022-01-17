<?php

namespace App\Http\Controllers\Modules\Seguimiento;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\Modules\Seguimiento\SepFicha;
use App\Http\Models\Modules\Seguimiento\SepActividad;
use App\Http\Models\Modules\Seguimiento\SepParticipante;
use DB;

class HorarioController extends Controller {
	public function __construct(){
		$this->middleware('auth');
		$this->middleware('control_roles');
	}
	
	public function postModificarfranja(){
		$valor = $_POST['valor'];
		$id = $_POST['id'];
		$rol = \Auth::user()->participante->rol_id;
		$permisoRoles = array(0, 8);

		if(!in_array($rol, $permisoRoles)){
			echo 'El rol actual no está autorizado para ejecutar la función.';
		}else if(!is_numeric($valor)){
			echo 'El valor debe ser númerico';
		}else if($valor < 1 or $valor > 4){
			echo 'El valor debe estar entre 1 y 4';
		}else if(!is_numeric($id)){
			echo 'El Id debe ser númerico';
		}else{
			DB::update('update sep_planeacion_ficha set pla_fra_id = '.$valor.' where pla_fic_id = '.$id);
			echo 'Modificación exitosa. Actualice la página.';
		}
	}
	
	public function getTransversaljornada(){
		$jornadas = array(
			'Mañana' => array('inicio'=>'06:00','fin'=>'12:00'),
			'Tarde' => array('inicio'=>'12:00','fin'=>'18:00'),
			'Tarde - nocturna' => array('inicio'=>'16:00','fin'=>'22:00'),
			'Nocturna' => array('inicio'=>'18:00','fin'=>'22:00'),
		);

		$sql = '
			select 	par.par_identificacion, par_nombres, par_apellidos
			from 	sep_participante par, users user, sep_transversal_instructor tra_ins
			where 	par.par_identificacion = tra_ins.par_id_instructor
			and		par.par_identificacion = user.par_identificacion
			and		par.rol_id = 2
			and 	user.estado = "1"
			group by par.par_identificacion
			order by par_nombres';
		$instructor = DB::select($sql);

		$sql = 'select 	* from 	sep_transversal_jornada';
		$instructor_jornada = DB::select($sql);
		$jornadas_seleccionadas = array();
		foreach($instructor_jornada as $val){
			$jornadas_seleccionadas[$val->jornada][] = $val->par_id_instructor;
		}
		//dd($jornadas_seleccionadas);
		return view('Modules.Seguimiento.Horario.transversalJornada', compact('jornadas_seleccionadas', 'instructor_jornada', 'jornadas', 'instructor'));
	}

	public function postTransversaljornada(){
		extract($_POST);

		if(isset($par_identificacion)){
			$jornadas = array(
				'Mañana' => array('inicio'=>'06:00','fin'=>'12:00'),
				'Tarde' => array('inicio'=>'12:00','fin'=>'18:00'),
				'Tarde - nocturna' => array('inicio'=>'16:00','fin'=>'22:00'),
				'Nocturna' => array('inicio'=>'18:00','fin'=>'22:00'),
			);
			$sql = 'delete from sep_transversal_jornada';
			DB::delete($sql);
			foreach($par_identificacion as $key => $par){
				foreach($par as $val){
					$sql = '
						insert into	sep_transversal_jornada
						(tra_jor_id, par_id_instructor, jornada, jornada_inicio, jornada_fin)
						values
						(default, "'.$val.'", "'.$key.'", "'.$jornadas[$key]['inicio'].'", "'.$jornadas[$key]['fin'].'")';
					DB::insert($sql);
				}
			}
		}

		session()->put('mensaje','yes');
		return redirect(url('seguimiento/horario/transversaljornada'));
	}
	
    public function getTransversalactividad(){
		$transversal = DB::select('select * from sep_transversal_tipo order by tra_tip_descripcion asc');
		$actividadAsignada = array();
		$disenoCurricular = array('Viejo', 'Nuevo');
		foreach($disenoCurricular as $dis){
			foreach($transversal as $tra){
				$actividadAsignada[$tra->tra_tip_id][$dis]['competencia'] = '';
				$actividadAsignada[$tra->tra_tip_id][$dis]['resultado'] = '';
				$actividadAsignada[$tra->tra_tip_id][$dis]['actividad'] = '';
				$actividadAsignada[$tra->tra_tip_id][$dis]['hora'] = '';
				$actividadAsignada[$tra->tra_tip_id][$dis]['descripcion'] = $tra->tra_tip_descripcion;
			}
		}

		
		$actividad = DB::select('select * from sep_transversal_actividad');
		foreach($actividad as $act){
			$actividadAsignada[$act->tra_tip_id][$act->dc_tipo]['competencia'][] = $act->tra_com_descripcion;
			$actividadAsignada[$act->tra_tip_id][$act->dc_tipo]['resultado'][] = $act->tra_res_descripcion;
			$actividadAsignada[$act->tra_tip_id][$act->dc_tipo]['actividad'][] = $act->tra_act_descripcion;
			$actividadAsignada[$act->tra_tip_id][$act->dc_tipo]['hora'][] = $act->tra_act_horas;
		}

		return view('Modules.Seguimiento.Horario.transversalActividad', compact('disenoCurricular','actividadAsignada'));
	}
	
    public function postTransversalactividad(){
		extract($_POST);

		$rol = \Auth::user()->participante->rol_id;
		if($rol == 8 or $rol == 0){
			if(isset($competencia)){
				$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';',':','--');
				DB::delete('delete from sep_transversal_actividad');
				foreach($competencia as $key1 => $val1){
					foreach($val1 as $key2 => $val2){
						foreach($val2 as $key3 => $val3){
							$com = str_replace($caractereNoPremitidos, '', trim($val3));
							$res = str_replace($caractereNoPremitidos, '', trim($resultado[$key1][$key2][$key3]));
							$act = str_replace($caractereNoPremitidos, '', trim($actividad[$key1][$key2][$key3]));
							$hor = str_replace($caractereNoPremitidos, '', trim($hora[$key1][$key2][$key3]));

							if($com != '' and $res != '' and $act != '' and $hor != ''){
								$sql = '
									insert into sep_transversal_actividad
									(tra_act_id, tra_com_descripcion, tra_res_descripcion, tra_act_descripcion, tra_act_horas, tra_tip_id, dc_tipo)
									values
									(default, "'.$com.'", "'.$res.'", "'.$act.'", '.$hor.', '.$key1.', "'.$key2.'")';
								DB::insert($sql);
							}
						}
					}
				}
			}
		}

		session()->put('mensaje','yes');
		return redirect(url('seguimiento/horario/transversalactividad'));
	}
	
	public function getTransversalhora(){
		$transversal = DB::select('select * from sep_transversal_tipo order by tra_tip_descripcion asc');
		$disenoCurricular = array('Viejo', 'Nuevo');
		$sql = '
			select 	*
			from 	sep_transversal_hora th, sep_nivel_formacion nf, sep_transversal_tipo tt
			where 	th.tra_tip_id = tt.tra_tip_id
			and 	th.niv_for_id = nf.niv_for_id
			and 	nf.niv_for_id in (1, 2, 4)';
		$asignado = DB::select($sql);

		$horasAsignadas = array();
		foreach($asignado as $asi){
			$horasAsignadas[$asi->niv_for_id][$asi->tra_tip_id][$asi->dc_tipo] = $asi->tra_hor_can_hora;
		}

		return view('Modules.Seguimiento.Horario.transversalHora', compact('horasAsignadas', 'transversal', 'disenoCurricular'));
	}

	public function postTransversalhora(){
		extract($_POST);
		
		$rol = \Auth::user()->participante->rol_id;
		if($rol == 8 or $rol == 0){
    		if(isset($_POST[1]) and ($_POST[2]) and ($_POST[4])){
    			DB::delete('delete from sep_transversal_hora');
    			unset($_POST['_token']);
    			foreach($_POST as $key1 => $val1){
    				foreach($val1 as $key2 => $val2){
    					foreach($val2 as $key3 => $val3){
    						if(is_numeric($val3)){
    							$sql = '
    								insert into sep_transversal_hora
    								(tra_hor_id, tra_tip_id, dc_tipo, niv_for_id, tra_hor_can_hora)
    								values
    								(default, '.$key2.', "'.$key3.'", '.$key1.', '.$val3.')';
    							DB::insert($sql);
    						}
    					}
    				}
    			}
    		}
		}

		session()->put('mensaje','yes');
		return redirect(url('seguimiento/horario/transversalhora'));
	}
	
	public function getTransversalasignar(){
		$transversal = DB::select('select * from sep_transversal_tipo order by tra_tip_descripcion asc');
		$transversalSeleccionada = DB::select('select * from sep_transversal_instructor');
		$ambienteSeleccionada = DB::select('select * from sep_transversal_ambiente');

		$transversalInstructor = array();
		foreach($transversalSeleccionada as $tra){
			$transversalInstructor[$tra->tra_tip_id][] = $tra->par_id_instructor;
		}

		$sql = '
			select 	par.par_identificacion, par_nombres, par_apellidos
			from 	sep_participante par, users user
			where 	par.par_identificacion = user.par_identificacion
			and		par.rol_id = 2
			and 	user.estado = "1" order by par_nombres';
		$instructor = DB::select($sql);

		$transversalAmbiente = array();
		foreach($ambienteSeleccionada as $tra){
			$transversalAmbiente[$tra->tra_tip_id][] = $tra->pla_amb_id;
		}

		$sql = '
			select 	*
			from 	sep_planeacion_ambiente
			where 	pla_amb_estado = "Activo" order by pla_amb_descripcion asc';
		$ambiente = DB::select($sql);

		return view('Modules.Seguimiento.Horario.transversalAsignar', compact('ambiente', 'transversalAmbiente', 'transversalInstructor', 'transversal', 'instructor'));
	}

	public function postTransversalasignar(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

		if(isset($par_identificacion)){
			DB::delete('delete from sep_transversal_instructor');
			foreach($par_identificacion as $key1 => $participante){
				foreach($participante as $key2 => $par){
					if($par != '' and is_numeric($par)){
						$sql = '
							insert into sep_transversal_instructor
							(tra_ins_id, tra_tip_id, par_id_instructor, tra_ins_prioridad)
							values
							(default, '.$key1.', "'.$par.'", '.($key2+1).')';
						DB::insert($sql);
					}
				}
			}
		}

		if(isset($pla_amb_id)){
			DB::delete('delete from sep_transversal_ambiente');
			foreach($pla_amb_id as $key1 => $ambiente){
				foreach($ambiente as $key2 => $amb){
					if($amb != '' and is_numeric($amb)){
						$sql = '
							insert into sep_transversal_ambiente
							(tra_amb_id, tra_tip_id, pla_amb_id)
							values
							(default, '.$key1.', "'.$amb.'")';
						DB::insert($sql);
					}
				}
			}
		}

		session()->put('mensaje','yes');

		return redirect(url('seguimiento/horario/transversalasignar'));
	}
	
	public function getTransversalnivel(){
		$nivel = array(1 => 'Operario', 2 => 'Técnico', 4 => 'Tecnólogo');
		$transversal = DB::select('select * from sep_transversal_tipo order by tra_tip_descripcion asc');
		$arrayTransersal = array();
		foreach($transversal as $tra){
			$arrayTransersal[$tra->tra_tip_id] = $tra->tra_tip_descripcion;
		}

		$sql = '
			select 	*
			from 	sep_transversal_nivel_formacion tnf, sep_nivel_formacion nf, sep_transversal_tipo tt
			where 	tnf.tra_tip_id = tt.tra_tip_id
			and 	tnf.niv_for_id = nf.niv_for_id
			and 	nf.niv_for_id in (1, 2, 4) order by numero_trimestre_inicio asc';
		$asignado = DB::select($sql);

		$trimestrePorNivel[1] = 2;
		$trimestrePorNivel[2] = 3;
		$trimestrePorNivel[4] = 7;
		$disenoCurricular = array('Viejo', 'Nuevo');
		$arrayAsignado = array();
		foreach($disenoCurricular as $key => $dis){
			foreach($nivel as $key2 => $niv){
				$arrayAsignado[$key2][$dis]['nivel'] =  $niv;
				$arrayAsignado[$key2]['cantidadTrimestres'] =  $trimestrePorNivel[$key2];
				for($i=1; $i<=$trimestrePorNivel[$key2]; $i++){
					$arrayAsignado[$key2][$dis]['trimestre'][$i] =  '';
				}
			}
		}

		foreach($asignado as $asi){
			$arrayAsignado[$asi->niv_for_id][$asi->dc_tipo]['nivel'] = $asi->niv_for_nombre;
			$arrayAsignado[$asi->niv_for_id][$asi->dc_tipo]['trimestre'][$asi->numero_trimestre_inicio][] = $asi->tra_tip_id;
			$arrayAsignado[$asi->niv_for_id][$asi->dc_tipo]['transversalProgramada'][] = $asi->tra_tip_id;
		}
        //dd($arrayAsignado);
		return view('Modules.Seguimiento.Horario.transversalNivel',compact('arrayTransersal', 'arrayAsignado', 'arrayTrimestresPorNivelDeFormacion'));
	}

	public function postTransversalnivel(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

        if(isset($transversal)){
    		$nivelFormacion = array('Operario' => 1, 'Técnico' => 2, 'Tecnólogo' => 4);
    		DB::delete('delete from sep_transversal_nivel_formacion');
    		foreach($transversal as $key => $tra){
    			$sql = '
    				insert into sep_transversal_nivel_formacion
    				(tra_niv_for, tra_tip_id, dc_tipo, niv_for_id, numero_trimestre_inicio)
    				values
    				(default, '.$transversal[$key].', "'.$disenoCurricular[$key].'", "'.$nivelFormacion[$nivel[$key]].'", '.$trimestre[$key].')';
    			DB::insert($sql);
    		}
        }

		session()->put('mensaje','yes');
		return redirect(url('seguimiento/horario/transversalnivel'));
	}
	
	public function postAsignaractividad(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);
		
		if($valor == ''){
			$sql = '
				update 	sep_planeacion_ficha 
				set 	pla_ins_lider = ""
				where 	fic_numero = "'.$ficha.'"';
			DB::select($sql);
			echo 'El cambio se realizo exitosamente.';
		}else{
			$sql = '
				select 	fic_numero 
				from 	sep_planeacion_ficha 
				where 	fic_numero = "'.$ficha.'" limit 1';
			$validarFicha = DB::select($sql);
			
			if(count($validarFicha)>0){
				$sql = '
					select 	par_identificacion 
					from 	sep_participante 
					where 	par_identificacion = "'.$valor.'" 
					and 	rol_id = 2 limit 1';
				$validarInstructor = DB::select($sql);
				
				if(count($validarInstructor)>0){
					$sql = '
						update 	sep_planeacion_ficha 
						set 	pla_ins_lider = "'.$valor.'"
						where 	fic_numero = "'.$ficha.'"';
					DB::select($sql);
					echo 'El cambio se realizo exitosamente.';
				}else{
					echo 'El número del documento no existe en la base de datos o la persona no tiene rol de Instructor';
				}
			}else{
				echo 'El número de ficha no existe en nuestra base de datos.';
			}
		}
	}
	
	public function getActividad(){
		$par_identificacion = \Auth::user()->participante->par_identificacion;
		
		$sql = '
			select 	act_ins_id,fic.fic_numero, prog_nombre,resultado,actividad,observaciones,
					id_herramienta,otra_herramienta,explicacion
			from	sep_actividad_instructor act_ins,sep_ficha fic,
					sep_programa pro
			where 	act_ins.ficha = fic.fic_numero
			and 	fic.prog_codigo = pro.prog_codigo
			and 	id_instructor = "'.$par_identificacion.'"';
		$fichas = DB::select($sql);
		
		$actividadesCargadas = array();
		foreach($fichas as $val){
			$actividadesCargadas[$val->act_ins_id]['id_herramienta'] = $val->id_herramienta;
			$actividadesCargadas[$val->act_ins_id]['otra_herramienta'] = $val->otra_herramienta;
			$actividadesCargadas[$val->act_ins_id]['explicacion'] = $val->explicacion;
			$actividadesCargadas[$val->act_ins_id]['observaciones'] = $val->observaciones;
		}
		//dd($actividadesCargadas);
		$sql = '
			select * 
			from 	sep_actividad_herramienta';
		$herramienta = DB::select($sql);
		//echo session()->get('exito');
		return view('Modules.Seguimiento.Horario.actividad',compact('actividadesCargadas','fichas','herramienta'));
	}
	
	public function postActividadinstructor(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);
		//dd($_POST);
		
		$sql = '
			select * 
			from 	sep_actividad_herramienta';
		$herramientaSQL = DB::select($sql);
		
		$herramientaArray = array();
		foreach($herramientaSQL as $val){
			$herramientaArray[] = $val->act_her_id;
		}
		
		$par_identificacion = \Auth::user()->participante->par_identificacion;
		foreach($act_ins_id as $key => $val){
			if(!is_numeric($val)){
				dd('id debe ser solo númerico');
			}
			$sql = '
				select 	ficha
				from 	sep_actividad_instructor
				where 	id_instructor = "'.$par_identificacion.'"
				and 	act_ins_id = '.$val.' limit 1';
			$validar = DB::select($sql);
			if(count($validar)==0){
				dd('resultado '.$val.' no pertenece al instructor');
			}
			
			if($herramienta[$key] == '' or !in_array($herramienta[$key], $herramientaArray)){
				dd('Valor de campo herramienta está vacio o no existe el tipo de herramienta seleccionada');
			}
		}
		
		foreach($herramienta as $key => $val){
			if(isset($observaciones[$key])){
				$observaciones1 = substr($observaciones[$key],0,500);
			}else{
				$observaciones1 = '';
			}
			if($val == 4){
				$otra_herramienta = substr($otraHerramienta[$key],0,100);
				$sql = '
				update 	sep_actividad_instructor
				set 	id_herramienta = '.$val.', otra_herramienta = "'.$otra_herramienta.'",
						explicacion = "", at_update = default, observaciones = "'.$observaciones1.'"
				where 	act_ins_id = '.$act_ins_id[$key];
			}else if($val == 5){
				$explicacion1 = substr($explicacion[$key],0,500);
				$sql = '
				update 	sep_actividad_instructor
				set 	id_herramienta = '.$val.',explicacion = "'.$explicacion1.'",
						otra_herramienta = "", at_update = default, observaciones = "'.$observaciones1.'"
				where 	act_ins_id = '.$act_ins_id[$key];
			}else{
				$sql = '
				update 	sep_actividad_instructor
				set 	id_herramienta = '.$val.', explicacion = "",
						otra_herramienta = "", at_update = default, observaciones = "'.$observaciones1.'"
				where 	act_ins_id = '.$act_ins_id[$key];
			}
			DB::update($sql);
		}
		session()->put('exito','yes');
		return redirect(url('seguimiento/horario/actividad'));
	}
	
	public function seguridad($array){
		// Quitamos los simbolos no permitidos de cada variable recibida, 
		// para evitar ataques XSS e Inyección SQL
		$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';',':','--');
		$array = str_replace($caractereNoPremitidos,'',$array);
		return	$array;
	}
	
	public function postModificarnumeroprograma(){
		$valor = $_POST['valor'];
		$id = $_POST['id'];
		if(!is_numeric($id)){
			dd('El Id debe ser númerico');
		}
		DB::update('update sep_planeacion_ficha set pla_fic_consecutivo_ficha = "'.$valor.'" where pla_fic_id = '.$id);
	}

	public function postModificartipooferta(){
		$valor = $_POST['valor'];
		$id = $_POST['id'];
		if(!is_numeric($id)){
			dd('El Id debe ser númerico');
		}
		DB::update('update sep_planeacion_ficha set pla_tip_ofe_id = '.$valor.' where pla_fic_id = '.$id);
	}
	
	public function getHorarioauditorio(){
		// Declaracion de fechas
		$fechaActual = date('Y-m-d');
		$semanaActaul = date('W');
		$nombreDia = date('l');
		
		// Validar si hoy es lunes
		if($nombreDia == 'Monday'){
			$lunesActual = $fechaActual;
		}else{
			$lunesActual = date('Y-m-d', strtotime($fechaActual.' last monday'));
		}	

		// Validar si hoy es domindo
		if($nombreDia == 'Sunday'){
			$domingoActual = $fechaActual;
		}else{
			$domingoActual = date('Y-m-d', strtotime($fechaActual.' next Sunday'));
		}
		

		/*echo $lunesActual."<br>";
		echo $sabadoActual."<br>";
		echo $nombreDia."<br>";*/
		return view('Modules.Seguimiento.Horario.horarioAuditorio', compact('lunesActual','domingoActual'));
	}
	
	public function getEtapapractica(){
		$ambientes = DB::select('select * from sep_planeacion_ambiente where not pla_amb_tipo = "Restriccion" and not pla_amb_id = 88 order by pla_amb_descripcion');
		$trimestres = DB::select('select * from sep_planeacion_fecha_trimestre');
		$instructores = DB::select('select * from sep_participante where rol_id = 2 order by par_nombres');
		$dias = array(1=>'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
		$sqlFichas = '
			select 	fic.fic_numero,prog_nombre,pla_fic_id
			from   	sep_ficha fic, sep_planeacion_ficha pla_fic, sep_programa pro
			where  	fic.fic_numero = pla_fic.fic_numero  and  fic.prog_codigo = pro.prog_codigo
			and		not fic.fic_numero in("Complementario", "Restriccion")
			order  	by prog_nombre';
		$fichas = DB::select($sqlFichas);
		$arrayTrimestres = array();
		foreach($trimestres as $key => $val){
			$arrayTrimestres[$val->pla_fec_tri_id]['fechaInicio'] = $val->pla_fec_tri_fec_inicio;
			$arrayTrimestres[$val->pla_fec_tri_id]['fechaFin'] = $val->pla_fec_tri_fec_fin;
		}

		return view('Modules.Seguimiento.Horario.etapaPractica', compact('fichas','trimestres','ambientes','instructores','dias'));
	}

	public function postEtapapractica(){
		extract($_POST);
		
		$mensaje = '';
		$errores['error'] = 0;
		$todosLosTrimestres = DB::select('select pla_fec_tri_id, pla_fec_tri_year, pla_fec_tri_trimestre, pla_fec_tri_fec_inicio, pla_fec_tri_fec_fin from sep_planeacion_fecha_trimestre');
		$trimestres = DB::select('select pla_fec_tri_id, pla_fec_tri_year, pla_fec_tri_trimestre, pla_fec_tri_fec_inicio, pla_fec_tri_fec_fin from sep_planeacion_fecha_trimestre where pla_fec_tri_id ='.$pla_fec_tri_id.' limit 1');
		$fechaInicio = $trimestres[0]->pla_fec_tri_fec_inicio;
		$fechaFin = $trimestres[0]->pla_fec_tri_fec_fin;
		
		if($hora_inicio < $hora_fin){
			if($pla_fic_id != 1){
				$validarGrupo = $this->validarGrupo($pla_fic_id, $fechaInicio, $fechaFin, $hora_inicio, $hora_fin, $dia_id);
				if($validarGrupo == 0){
					$mensaje = '- El grupo no esta disponible.<br>';
					$errores['error'] = 1;
				}

				$nivel_formacion = DB::select('select niv_for_id from sep_planeacion_ficha pla_fic, sep_ficha ficha, sep_programa pro where pla_fic_id = '.$pla_fic_id.' and pla_fic.fic_numero = ficha.fic_numero and ficha.prog_codigo = pro.prog_codigo');
				$nivel_formacion = $nivel_formacion[0]->niv_for_id;

				$etapaProductiva = 2;
				if($nivel_formacion == 1){
					$etapaProductiva = 1;
				}

				$ficha = DB::select('select pla_fic_fec_ini_induccion,pla_fic_fec_fin_lectiva from sep_planeacion_ficha where pla_fic_id = '.$pla_fic_id);
				$inicioLectiva = $ficha[0]->pla_fic_fec_ini_induccion;
				$finLectiva = $ficha[0]->pla_fic_fec_fin_lectiva;
				
				$sql = 'select pla_fec_tri_id from sep_planeacion_fecha_trimestre where pla_fec_tri_fec_inicio ="'.$inicioLectiva.'" limit 1';
				$pla_fec_tri_id_inicia_lectiva = DB::select('select pla_fec_tri_id from sep_planeacion_fecha_trimestre where pla_fec_tri_fec_inicio ="'.$inicioLectiva.'" limit 1');
				$pla_fec_tri_id_inicia_lectiva = $pla_fec_tri_id_inicia_lectiva[0]->pla_fec_tri_id;
				
				
                //dd('select pla_fec_tri_id from sep_planeacion_fecha_trimestre where pla_fec_tri_fec_fin ="'.$finLectiva.'" limit 1');
                $sql = 'select pla_fec_tri_id from sep_planeacion_fecha_trimestre where pla_fec_tri_fec_fin ="'.$finLectiva.'" limit 1';
				$pla_fec_tri_id_termina_lectiva = DB::select($sql);
				//dd($sql);
				$pla_fec_tri_id_termina_lectiva = $pla_fec_tri_id_termina_lectiva[0]->pla_fec_tri_id;

				$idFechaInicio = $pla_fec_tri_id_termina_lectiva+1;
				$idFechaFin = $pla_fec_tri_id_termina_lectiva+$etapaProductiva;

				if($pla_fec_tri_id < $idFechaInicio or $pla_fec_tri_id > $idFechaFin){
					$fInicio = $todosLosTrimestres[$idFechaInicio-1]->pla_fec_tri_fec_inicio;
					$fFin = $todosLosTrimestres[$idFechaFin-1]->pla_fec_tri_fec_fin;
					$mensaje .= '- Debe seleccionar un trimestre que este en el rango de etapa práctica de la ficha, desde <strong style="color:red;">'.$fInicio.'</strong> hasta <strong style="color:red;">'.$fFin.'</strong>.<br>';
					$errores['error'] = 1;
				}
			}
			
			$validarInstructor = $this->validarDisponibilidad('instructor',$par_identificacion,$fechaFin,$hora_inicio,$hora_fin,$dia_id);
			if($validarInstructor == 0){
				$mensaje .= '- El instructor no esta disponible.<br>';
				$errores['error'] = 1;
			}else if($validarInstructor == 3){
				$mensaje .= '- El instructor sobre pasa sus horas semanales.<br>';
				$errores['error'] = 1;
			}

			$amb_id = 72;
		}else{
			$mensaje .= '- La hora de inicio debe ser menor a la hora de fin.<br>';
			$errores['error'] = 1;
		}

		$validar = DB::select('select pla_fic_det_fec_fin from sep_planeacion_ficha_detalle where pla_fic_id = '.$pla_fic_id.' and pla_fic_det_fec_fin = "'.$fechaFin.'" and pla_tip_id = 6 limit 1');
		if(count($validar)>0){
			$mensaje .= '- La ficha ya tiene etapa práctica programada.<br>';
			$errores['error'] = 1;
		}

		if($errores['error'] == 0){
			$mensaje .= '- La etapa práctica se registro exitosamente.<br>';
			$horasTotales = $hora_fin-$hora_inicio;

			$pla_trimestre_numero_ficha = 1;
			if(isset($pla_fec_tri_id_inicia_lectiva)){
				$pla_trimestre_numero_ficha = ($pla_fec_tri_id-$pla_fec_tri_id_inicia_lectiva)+1;
			}

			$sep_planeacion_ficha_detalle = '
				insert into	sep_planeacion_ficha_detalle (
					pla_fic_det_id,pla_fic_id,pla_fic_det_fec_inicio,pla_fic_det_fec_fin,
					pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_fic_det_hor_totales,par_id_instructor,
					pla_dia_id,pla_amb_id,pla_trimestre_numero_ficha,pla_tip_id,pla_trimestre_numero_year
				) values	(
					default,'.$pla_fic_id.',"'.$fechaInicio.'","'.$fechaFin.'",
					"'.$hora_inicio.'","'.$hora_fin.'","'.$horasTotales.'","'.$par_identificacion.'", 
					'.$dia_id.','.$amb_id.','.$pla_trimestre_numero_ficha.',6,'.$pla_fec_tri_id.')';
			DB::beginTransaction();
			DB::insert($sep_planeacion_ficha_detalle);
			DB::commit();

			$competencia = 'RESULTADOS DE APRENDIZAJE ETAPA PRÁCTICA';
			$resultado = 'APLICAR EN LA RESOLUCIÓN DE PROBLEMAS REALES DEL SECTOR PRODUCTIVO, LOS CONOCIMIENTOS,HABILIDADES Y DESTREZAS PERTINENTES A LAS COMPETENCIAS DEL PROGRAMA DE FORMACIÓN ASUMIENDO ESTRATEGIAS Y METODOLOGÍAS DE AUTOGESTIÓN';
			$actividad = 'RESULTADOS DE APRENDIZAJE ETAPA PRÁCTICA';
			$act_horas = 440;

			$sql = '
				insert into sep_planeacion_ficha_actividades (
					pla_fic_act_id,pla_fic_act_competencia,pla_fic_act_resultado,pla_fic_act_actividad,
					pla_fic_act_horas,pla_fic_id,par_id_instructor,pla_tip_id,pla_trimestre_numero,fas_id
				) values (
					default,"'.$competencia.'","'.$resultado.'","'.$actividad.'",
					"'.$act_horas.'",'.$pla_fic_id.',"'.$par_identificacion.'",6,'.$pla_trimestre_numero_ficha.',5)';
			DB::insert($sql);
		}
		echo $mensaje;
	}
	
	public function getModificaractividades(){
		extract($_GET);
		
		$rol = \Auth::user()->participante->rol_id;
		$pla_tip_id = '';
		if($rol == 2){
			$pla_tip_id = ' and pla_tip_id = 2 ';
		}

		$fases = array(1=>'Análisis','Planeación','Ejecución','Evaluación', '-','-','-','Induccion','Identificación','Análisis','Diseño','Desarrollo','Implantacón');

	    $sql = '
			select 	par.par_identificacion, par.par_nombres, par.par_apellidos, usu.estado 
			from sep_participante par
			left join users usu on usu.par_identificacion =  par.par_identificacion
			where 	par.rol_id = 2 
			and usu.estado = "1"  
			order by par.par_nombres';
		$instructores = DB::select($sql);
		$instructoresArray = [];
		foreach($instructores as $ins){
			$instructoresArray[$ins->par_identificacion] = $ins->par_nombres.' '.$ins->par_apellidos;
		}
		
		if($pla_fic_id == 955){
			$sql = '
			select 	*
			from 	sep_planeacion_ficha_actividades
			where 	pla_fic_id = 955
			and     not pla_tip_id = 3
			order by pla_fic_act_id asc';
		}else{
			$sql = '
			select 	*
			from 	sep_planeacion_ficha_actividades
			where 	pla_fic_id = '.$pla_fic_id.' '.$pla_tip_id.'
			and     not pla_tip_id = 3
			order by fas_id, pla_fic_act_id asc';
		}

		$actividades = DB::select($sql);
		
		$sql = '
			select 	fecha_inicio, fecha_fin, trimestre_numero
			from 	sep_planeacion_ficha_trimestre
			where 	pla_fic_id = '.$pla_fic_id;
			
		$ficha = DB::select($sql);

		$fechaActual = date('Y-m-d');
		$fechaActual = date('Y-m-d', strtotime($fechaActual.' + 1 month'));
		$trimestre_ficha = array();
		foreach($ficha as $fic){
			if($fechaActual < $fic->fecha_inicio){
				//break;
			}
			$trimestre_ficha['fecha_inicio'][] = $fic->fecha_inicio;
			$trimestre_ficha['fecha_fin'][] = $fic->fecha_fin;
			$trimestre_ficha['trimestre_numero'][] = $fic->trimestre_numero;
			
		}
		
		$sql = '
			select 	sum(pla_fic_act_horas) as total, pla_fic_can_trimestre
			from 	sep_planeacion_ficha_actividades pla_fic_act, 
					sep_planeacion_ficha pla_fic
			where 	pla_fic_act.pla_fic_id = pla_fic.pla_fic_id
            and		pla_fic_act.pla_fic_id = '.$pla_fic_id.' '.$pla_tip_id.'
			and     not pla_tip_id = 3
			and 	fas_id in(1,2,3,4,7,8,9,10,11,12)';
		$horasNivelPrograma = DB::select($sql);
		$horasTotalesPrograma = $horasNivelPrograma[0]->total;
		$cantidadTrimestres = $horasNivelPrograma[0]->pla_fic_can_trimestre;
		$horasPorTrimestre = (ceil($horasTotalesPrograma / $cantidadTrimestres)+50);
		$trimestreActual = count($trimestre_ficha['trimestre_numero']);
		$habilitarHasta = $horasPorTrimestre * $trimestreActual;
		$habilitarDesde = $horasPorTrimestre * ($trimestreActual - 1);
		
		return view('Modules.Seguimiento.Horario.modificarActividades', compact('instructoresArray', 'habilitarDesde', 'habilitarHasta', 'trimestre_ficha', 'diferencia','trimestresFicha','instructores', 'actividades', 'fases'));
	}

	public function postModificaractividades(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);
		
		DB::beginTransaction();
		foreach($pla_fic_act_id as $key => $id){
		    if(is_numeric($id)){
    		    if($par_id_instructor[$key] == 'noAsignar' or $pla_fec_tri_id[$key] == 0){
    				$sql = 'update sep_planeacion_ficha_actividades set par_id_instructor = null, pla_trimestre_numero = null, pla_trimestre_year = null where pla_fic_act_id = '.$id;
    		    }else{
    				
					$sql = '
    					update 	sep_planeacion_ficha_actividades
    					set 	par_id_instructor = "'.$par_id_instructor[$key].'",
    							fecha_inicio = "'.$pla_fec_tri_id[$key].'"
						where 	pla_fic_act_id = '.$id;
    			}
    			DB::update($sql);
		    }
		}
		DB::commit();
		echo 'Los cambios se realizarón exitosamente.';
	}
	
	
	
	public function getActualizarficha(){
		$sql = '
			select 	fic.fic_numero,prog_nombre,pla_fra_descripcion 
			from 	sep_planeacion_ficha pla_fic,sep_programa pro, sep_ficha fic,sep_planeacion_franja pla_fra
			where 	fic.fic_numero like "N%" and pla_fic.fic_numero = fic.fic_numero 
			and 	fic.prog_codigo = pro.prog_codigo and pla_fic.pla_fra_id = pla_fra.pla_fra_id
			order 	by prog_nombre';
		$fichas = DB::select($sql);

		$sql = '
			select 	fic_numero, prog_nombre 
			from 	sep_ficha f, sep_programa pro
			where 	not fic_numero in(
						select 	fic.fic_numero
						from 	sep_planeacion_ficha pla_fic,sep_ficha fic
						where 	pla_fic.fic_numero = fic.fic_numero) 
			and 	f.prog_codigo = pro.prog_codigo
			and 	not fic_numero like "N%"
			order 	by prog_nombre';
		$fichasSinHorario = DB::select($sql);
		return view('Modules.Seguimiento.Horario.actualizarFicha', compact('fichas','fichasSinHorario'));
	}

	public function postActualizarficha(){
		extract($_POST);
		$notificaciones = '';
		
		if(!is_numeric($fic_numero_nueva)){ $notificaciones = '- El campo número de ficha en Sofia Plus no es un número<br>'; }
		$sql = 'select prog_codigo from sep_planeacion_ficha pla_fic,sep_ficha fic where fic.fic_numero = "'.$fic_numero_vieja.'" and fic.fic_numero = pla_fic.fic_numero limit 1';
		$programaFichaVieja = DB::select($sql);
		if(!isset($programaFichaVieja[0]->prog_codigo)){ 
			$notificaciones = '- El valor ingresado en el campo "Grupos sin asignación de ficha" no existe o ya fue asignada.<br>'; 
		}else{
			$programaFichaVieja = $programaFichaVieja[0]->prog_codigo;
		}
		
		$sql = 'select prog_codigo from sep_ficha fic where fic_numero = "'.$fic_numero_nueva.'" limit 1';
		$programaFichaNueva = DB::select($sql);
		if(!isset($programaFichaNueva[0]->prog_codigo)){ 
			$notificaciones = '- El valor ingresado en el campo "Fichas sin horario" no existe.<br>'; 
		}else{
			$programaFichaNueva = $programaFichaNueva[0]->prog_codigo;
		}

		if($programaFichaVieja != $programaFichaNueva){ $notificaciones = '- Los códigos de los programas de formación no son iguales.<br>'; }

		$validar = DB::select('select fic_numero from sep_planeacion_ficha where fic_numero = "'.$fic_numero_nueva.'" limit 1');
		if(count($validar)>0){ $notificaciones = '- El número de ficha por el cual quiere actualizar ya ha sido actualizado.<br>'; }

		if($notificaciones == ''){
			DB::update('update sep_planeacion_ficha set fic_numero = "'.$fic_numero_nueva.'" where fic_numero = "'.$fic_numero_vieja.'"');
			$notificaciones = '- La ficha se ha actualizado exitosamente.';
		}

		echo $notificaciones;
	}

	public function getActividadesinstructor(){
		$rol = \Auth::user()->participante->rol_id;
		if($rol != 1){
			extract($_GET);
			$sql = '
				select 	pla_fic_act_competencia, pla_fic_act_resultado,pla_fic_act_actividad, pla_fic_act_horas, fas_id
				from 	sep_planeacion_ficha_actividades act, sep_participante par
				where 	act.par_id_instructor = par.par_identificacion
				and 	act.par_id_instructor = "'.$cc.'" and  pla_fic_id = '.$pla_fic_id.'
				and 	fecha_inicio = "'.$fecha_inicio.'"';
			$actividades = DB::select($sql);
			$fase = array(1=>'Análisis','Planeación','Ejecución','Evaluación','-');
			return view("Modules.Seguimiento.Horario.actividadesInstructor",compact('fase','actividades'));
		}else{
			echo "No tienes los permisos para ingresar a está función";
		}
	}

	public function getComplementario(){
		$sql = '
			select fic.fic_numero, prog_nombre, pla_fic_id
			from   sep_ficha fic, sep_planeacion_ficha pla_fic, sep_programa pro
			where  fic.fic_numero = pla_fic.fic_numero  and  fic.prog_codigo = pro.prog_codigo
			order  by prog_nombre, fic.fic_numero';
		$fichas = DB::select($sql);

		$sql = '
			select 	pla_amb_id, pla_amb_descripcion
			from 	sep_planeacion_ambiente
			where 	not pla_amb_tipo = "Restriccion"
			and 	not pla_amb_id = 88
			and 	pla_amb_estado = "Activo" order by pla_amb_descripcion';
		$ambientes = DB::select($sql);

		$sql = '
			select 	par.par_identificacion, par_nombres, par_apellidos
			from 	sep_participante par, users u
			where 	par.par_identificacion = u.par_identificacion
			and		rol_id = 2 and not par.par_identificacion = "0"
			and 	estado = "1"
			order by par_nombres';
		$instructores = DB::select($sql);

		$dias = array(1=>'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
		return view('Modules.Seguimiento.Horario.complementario',compact('fichas', 'ambientes', 'instructores', 'dias'));
	}

	public function postComplementario(){
		extract($_POST);
		
	    $pla_fic_id=1;
	    $pla_trimestre_numero_ficha=1;

		if(isset($tipo_registro)){
			$pla_fic_id = $fic_id;
			$amb_id = $com_pla_amb_id;
			$par_identificacion = $com_par_identificacion;
			$hora_inicio = $com_hora_inicio;
			$hora_fin = $com_hora_fin;
			$fecha_inicio = $fechaInicio;
			$fecha_fin = $fechaFin;
			$dia_id = $com_dia;
			
			$sql="select trimestre_numero 
			from sep_planeacion_ficha_trimestre
			where pla_fic_id=$pla_fic_id 
			and fecha_inicio='$fecha_inicio' 
			and fecha_fin='$fecha_fin'";

			$res=DB::select($sql);

			$pla_trimestre_numero_ficha= $res[0]->trimestre_numero;
		}
		
		$sql = "select TIMESTAMPDIFF(week,'$fecha_inicio','$fecha_fin') as diferencia"; 
		$diferencia_entre_fechas = DB::select($sql);

		$pla_fic_act_horas = ($hora_fin - $hora_inicio) * ($diferencia_entre_fechas[0]->diferencia + 1);
           	
		
		$errores['error'] = 0;

		if($hora_inicio < $hora_fin){
			if($pla_fic_id != 1){
				$validarGrupo = $this->validarGrupo($pla_fic_id, $fecha_inicio, $fecha_fin, $hora_inicio, $hora_fin, $dia_id);
				if($validarGrupo == 0){
					$errores['mensaje'][] = '- El grupo no esta disponible.<br>';
					$errores['error'] = 1;
				}

				$sql = '
					select 	pla_fic_fec_ini_induccion, pla_fic_fec_fin_lectiva
					from 	sep_planeacion_ficha
					where 	pla_fic_id = '.$pla_fic_id;
				$ficha = DB::select($sql);
				$fecha_inicio_lectiva = $ficha[0]->pla_fic_fec_ini_induccion;
				$fecha_fin_lectiva = $ficha[0]->pla_fic_fec_fin_lectiva;

				/*if($fecha_inicio < $fecha_inicio_lectiva or $fecha_fin > $fecha_fin_lectiva){
					$errores['mensaje'][] = '- Debe seleccionar un trimestre que este en el rango de etapa lectiva de la ficha, desde <strong>'.$inicioLectiva.'</strong> hasta <strong>'.$finLectiva.'</strong>.<br>';
					$errores['error'] = 1;
				}*/
			}
			//dd($_POST);
			$validarInstructor = $this->validarDisponibilidad('instructor', $par_identificacion, $fecha_inicio, $fecha_fin ,$hora_inicio, $hora_fin, $dia_id);
			if($validarInstructor == 0){
				$errores['mensaje'][] = '- El instructor no esta disponible.<br>';
				$errores['error'] = 1;
			}else if($validarInstructor == 3){
				$errores['mensaje'][] = '- El instructor sobre pasa sus horas semanales.<br>';
				$errores['error'] = 1;
			}

			if($amb_id != 88 and $amb_id != 123){
				$validarAmbiente = $this->validarDisponibilidad('ambiente', $amb_id, $fecha_inicio, $fecha_fin, $hora_inicio, $hora_fin, $dia_id);
				if($validarAmbiente == 0){
					$errores['mensaje'][] = '- El ambiente no esta disponible.<br>';
					$errores['error'] = 1;
				}
			}
		}else{
			$errores['mensaje'][] = '- La hora de inicio debe ser menor a la hora de fin.<br>';
			$errores['error'] = 1;
		}

		$validar_fecha_inicio = $fecha_inicio;
		$dia = date("N", strtotime($validar_fecha_inicio));
		if($dia != 1){
			$errores['mensaje'][] = 'El día de la <strong>fecha inicio</strong> debe ser lunes.<br>';
			$errores['error'] = 1;
		}

		$validar_fecha_fin = $fecha_fin;
		$dia = date("N", strtotime($validar_fecha_fin));
		if($dia != 6){
			$errores['mensaje'][] = 'El día de la <strong>fecha fin</strong> debe ser sábado.<br>';
			$errores['error'] = 1;
		}

		if($fecha_inicio > $fecha_fin){
			$errores['mensaje'][] = 'La <strong>fecha inicio</strong> debe ser menor a la <strong>fecha fin</strong>.';
			$errores['error'] = 1;
		}

		if($errores['error'] == 0){
			$errores['mensaje'][] = '- El complementario se registro exitosamente.';
			$horasTotales = $hora_fin - $hora_inicio;

			$sep_planeacion_ficha_detalle = '
				insert into	sep_planeacion_ficha_detalle (
					pla_fic_det_id,pla_fic_id,pla_fic_det_fec_inicio,pla_fic_det_fec_fin,
					pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_fic_det_hor_totales,par_id_instructor,
					pla_dia_id,pla_amb_id,pla_trimestre_numero_ficha,pla_tip_id,pla_trimestre_numero_year
				) values (
					default,'.$pla_fic_id.', "'.$fecha_inicio.'", "'.$fecha_fin.'",
					"'.$hora_inicio.'", "'.$hora_fin.'", "'.$horasTotales.'", "'.$par_identificacion.'",
					'.$dia_id.', '.$amb_id.','.$pla_trimestre_numero_ficha.', 3, 1)';
			DB::beginTransaction();
			DB::insert($sep_planeacion_ficha_detalle);
			DB::commit();

			$sql = '
				insert into sep_planeacion_ficha_actividades(
					pla_fic_act_id, pla_fic_act_competencia, pla_fic_act_resultado,
					pla_fic_act_actividad, pla_fic_act_horas, pla_fic_id,
					par_id_instructor, pla_tip_id, pla_trimestre_numero,
					fas_id, fecha_inicio, fecha_fin
				) values (
					default, "Complementario", "Complementario",
					"'.$descripcion_complementario.'","'.$pla_fic_act_horas.'", '.$pla_fic_id.',
					"'.$par_identificacion.'", 3, 1,
					5, "'.$fecha_inicio.'", "'.$fecha_fin.'")';
			DB::insert($sql);
		}

        if(isset($tipo_registro)){
			return $errores;
		}else{
			echo json_encode($errores);
		}
	}

	public function validarGrupo($pla_fic_id, $fechaInicio, $fechaFin, $hora_inicio, $hora_fin, $dia){
		$sql = '
			select 	pla_dia_id
			from 		sep_planeacion_ficha_detalle
			where 	((pla_fic_det_hor_inicio < '.$hora_inicio.' and (pla_fic_det_hor_fin > '.$hora_inicio.' and pla_fic_det_hor_fin <= '.$hora_fin.'))
				or		((pla_fic_det_hor_inicio >= '.$hora_inicio.' and  pla_fic_det_hor_inicio < '.$hora_fin.') and pla_fic_det_hor_fin > '.$hora_fin.')
				or		(pla_fic_det_hor_inicio >= '.$hora_inicio.' and pla_fic_det_hor_fin <= '.$hora_fin.')
				or		(pla_fic_det_hor_inicio < '.$hora_inicio.' and pla_fic_det_hor_fin > '.$hora_fin.'))
			and 	((pla_fic_det_fec_inicio < "'.$fechaInicio.'" and (pla_fic_det_fec_fin > "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))
				or 		((pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_inicio < "'.$fechaFin.'") and pla_fic_det_fec_fin > "'.$fechaFin.'")
				or 		(pla_fic_det_fec_inicio < "'.$fechaInicio.'" and pla_fic_det_fec_fin > "'.$fechaFin.'")
				or 		(pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))
			and  pla_fic_id = '.$pla_fic_id.'
			and			pla_dia_id = '.$dia.' limit 1';
		$validar = DB::select($sql);
		
		$respuesta = 1;
		if(count($validar)>0){
			$respuesta = 0;
		}

		return $respuesta;
	}

	public function validarDisponibilidad($validar, $valor, $fechaInicio, $fechaFin, $hora_inicio, $hora_fin, $dia){
		$respuesta = 1;
		if($validar == 'instructor'){
			$concatenar = ' and	par_id_instructor = "'.$valor.'"';
			$sql = '
				select 	sum(pla_fic_det_hor_totales) as total
				from 	sep_planeacion_ficha_detalle
				where 	pla_tip_id != 5
				and		((pla_fic_det_fec_inicio < "'.$fechaInicio.'" and (pla_fic_det_fec_fin > "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))
					or 	((pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_inicio < "'.$fechaFin.'") and pla_fic_det_fec_fin > "'.$fechaFin.'")
					or 	(pla_fic_det_fec_inicio < "'.$fechaInicio.'" and pla_fic_det_fec_fin > "'.$fechaFin.'")
					or 	(pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))
				and 	par_id_instructor = "'.$valor.'"';
			$validarHoras = DB::select($sql);
			$calculo = $hora_fin - $hora_inicio;
			//$calcularSiPuede = $validarHoras[0]->total+$hora_fin;
			$calcularSiPuede = $validarHoras[0]->total+($hora_fin-$hora_inicio);

			$sql = '
				select 	par_horas_semanales
				from 	sep_participante
				where 	par_identificacion = "'.$valor.'" limit 1';
			$horasSemanales = DB::select($sql);
			$horasSemanales = $horasSemanales[0]->par_horas_semanales;
			if($calcularSiPuede > $horasSemanales){
				return 3;
			}
		}
		
		if($validar == 'ambiente'){
			$concatenar = ' and	pla_amb_id = '.$valor;
		}

		$sql = '
			select 	pla_dia_id
			from 	sep_planeacion_ficha_detalle
			where 	((pla_fic_det_hor_inicio < '.$hora_inicio.' and (pla_fic_det_hor_fin > '.$hora_inicio.' and pla_fic_det_hor_fin <= '.$hora_fin.'))
				or	((pla_fic_det_hor_inicio >= '.$hora_inicio.' and  pla_fic_det_hor_inicio < '.$hora_fin.') and pla_fic_det_hor_fin > '.$hora_fin.')
				or	(pla_fic_det_hor_inicio >= '.$hora_inicio.' and pla_fic_det_hor_fin <= '.$hora_fin.')
				or 	(pla_fic_det_hor_inicio < '.$hora_inicio.' and pla_fic_det_hor_fin > '.$hora_fin.'))
			and		((pla_fic_det_fec_inicio < "'.$fechaInicio.'" and (pla_fic_det_fec_fin > "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))
				or 	((pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_inicio < "'.$fechaFin.'") and pla_fic_det_fec_fin > "'.$fechaFin.'")
				or 	(pla_fic_det_fec_inicio < "'.$fechaInicio.'" and pla_fic_det_fec_fin > "'.$fechaFin.'")
				or 	(pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'")) '.$concatenar.'
			and		pla_dia_id = '.$dia.' limit 1';
		$validar = DB::select($sql);

		if(count($validar)>0){
			$respuesta = 0;
		}

		return $respuesta;
	}

	public function getAmbientecreate(){
		$coordinadores = DB::select('select par_identificacion, concat(par_nombres ," ", par_apellidos) as nombre_coordinador from sep_participante where rol_id = 3');
		return view('Modules.Seguimiento.Horario.ambienteCreate', compact('coordinadores'));
	}
	
	public function postAmbientecreate(){
	    $_POST = $this->seguridad($_POST);
		extract($_POST);
		$sql = '
			insert into sep_planeacion_ambiente
			values(default, "'.$pla_amb_descripcion.'", default, "'.$pla_amb_tipo.'", "'.$par_id_coordinador.'", "'.$pla_amb_suma_horas.'", default)';
		DB::insert($sql);
		return redirect(url('seguimiento/horario/ambiente'));
	}
	
	public function getAmbiente(){
	    $rol = \Auth::user()->participante->rol_id;
		$ambientes = DB::select('select pla_amb_id, pla_amb_descripcion, pla_amb_tipo, pla_amb_suma_horas, pla_amb_estado, par_id_coordinador from sep_planeacion_ambiente where not pla_amb_id = 72 order by pla_amb_tipo, pla_amb_descripcion');
		$coordinadores = DB::select('select par_identificacion, concat(par_nombres ," ", par_apellidos) as nombre_coordinador from sep_participante where rol_id = 3');
		$arrayAmbientes = array();
		foreach($ambientes as $val){
			$arrayAmbientes[$val->pla_amb_id]['pla_amb_descripcion'] = $val->pla_amb_descripcion;
			$arrayAmbientes[$val->pla_amb_id]['pla_amb_tipo'] = $val->pla_amb_tipo;
			$arrayAmbientes[$val->pla_amb_id]['pla_amb_suma_horas'] = $val->pla_amb_suma_horas;
			$arrayAmbientes[$val->pla_amb_id]['pla_amb_estado'] = $val->pla_amb_estado;
		}
		session()->put('ambientes', $arrayAmbientes);
		return view('Modules.Seguimiento.Horario.ambiente', compact('ambientes', 'rol'));
	}
	
    public function getAmbientemodalmodificar(){
		extract($_GET);
		$ambientes = session()->get('ambientes');
		return view('Modules.Seguimiento.Horario.ambienteModalModificar', compact('ambienteId', 'ambientes'));
	}
	
	public function postAmbientemodalmodificar(){
		$rol = \Auth::user()->participante->rol_id;
		if($rol == 8 or $rol == 0){
		    $_POST = $this->seguridad($_POST);
		    extract($_POST);
		    if(!is_numeric($pla_amb_id)){
		    	dd('Id debe ser numérico');
		    }
		    $sql = '
    			update 	sep_planeacion_ambiente 
    			set 		pla_amb_descripcion = "'.$pla_amb_descripcion.'",
    							pla_amb_tipo = "'.$pla_amb_tipo.'",
    							pla_amb_suma_horas = "'.$pla_amb_suma_horas.'",
    							pla_amb_estado = "'.$pla_amb_estado.'"
    			where 	pla_amb_id = '.$pla_amb_id;
		    DB::update($sql);
		}
	}
	
	public function getHorarioaprendiz(){
		$rol = \Auth::user()->participante->rol_id;
		$identificacion = \Auth::user()->par_identificacion;
		$ficha = DB::select('select max(fic_numero) as fic_numero from sep_matricula where par_identificacion = "'.$identificacion.'" limit 1');
		
		if($ficha[0]->fic_numero != null){
			$ficha = $ficha[0]->fic_numero;
			$trimestres = DB::select('select pla_fec_tri_id, pla_fec_tri_year, pla_fec_tri_trimestre, pla_fec_tri_fec_inicio, pla_fec_tri_fec_fin from sep_planeacion_fecha_trimestre');
			$sql = '
				select 	pla_fic_id, p_f.fic_numero, pla_tip_ofe_descripcion, pla_fic_can_trimestre, 
								pla_fic_fec_creacion, prog_nombre, pla_fra_descripcion, 
								substring_index(par.par_nombres," ",1) as par_nombres, 
								substring_index(par.par_apellidos," ",1) as par_apellidos, 
								substring_index(niv_for_nombre," ",1) as niv_for_nombre
				from 		sep_planeacion_ficha p_f, sep_planeacion_tipo_oferta p_t_o, sep_participante par, 
								sep_planeacion_franja p_fra, sep_programa pro, sep_ficha fic, sep_nivel_formacion niv
				where 	p_f.pla_tip_ofe_id = p_t_o.pla_tip_ofe_id
					and 	p_f.pla_fra_id = p_fra.pla_fra_id
					and 	p_f.fic_numero = fic.fic_numero
					and 	fic.prog_codigo = pro.prog_codigo
					and 	pro.niv_for_id = niv.niv_for_id
					and 	p_f.pla_fic_usu_creador = par.par_identificacion 
					and 	p_f.fic_numero = '.$ficha.'
				order 	by pla_fic_id desc';
			$horarios = DB::select($sql);
			
			if(count($horarios)>0){
				$sql ='
					select 	p_f.pla_fic_id, pla_fic_det_id, fic_numero, pla_trimestre_numero,
									par.par_identificacion, substring_index(par_nombres," ",1) as nombre, 
									substring_index(par_apellidos," ",1) as apellido, pla_dia_id, 
									par.par_identificacion, pla_fic_det_hor_inicio, pla_fic_det_hor_fin, 
									pla_fic_det_hor_totales, pla_amb_descripcion, amb.pla_amb_id,
									pla_fic_det_fec_inicio, pla_fic_det_fec_fin
					from 		sep_planeacion_ficha p_f, sep_planeacion_ficha_detalle p_f_d, 
									sep_participante par, sep_planeacion_ambiente amb
					where 	p_f.pla_fic_id = p_f_d.pla_fic_id
						and 	p_f_d.par_id_instructor = par.par_identificacion
						and 	p_f_d.pla_amb_id = amb.pla_amb_id
						and 	p_f.fic_numero = '.$ficha.'
						and 	pla_tip_id = 2 
					order 	by p_f.pla_fic_id, pla_trimestre_numero, pla_dia_id';
				$horarios_detalle = DB::select($sql);
				//dd($horarios_detalle);
				$programacion = array();
				foreach($horarios_detalle as $key => $val){
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero]["fechas_inicio"] = $val->pla_fic_det_fec_inicio;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero]["fechas_fin"] = $val->pla_fic_det_fec_fin;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero]["hora_inicio"][] = $val->pla_fic_det_hor_inicio;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero]["hora_fin"][] = $val->pla_fic_det_hor_fin;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero]["horas_totales"][] = $val->pla_fic_det_hor_totales;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero]["dia_id"][] = $val->pla_dia_id;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero]["amb_id"][] = $val->pla_amb_id;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero]["amb_descripcion"][] = $val->pla_amb_descripcion;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero]["instructor_cedula"][] = $val->par_identificacion;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero]["instructor_nombre"][] = $val->nombre." ".$val->apellido;
				}

				$diaOrtografia = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
				$faseOrtografia = array(1 => "Análisis", "Planeación", "Ejecución", "Evaluación");
			}else{
				$mensaje = 'El horario se su ficha no ha sido cargado actualmente.';
				return view('Modules.Seguimiento.Horario.errores', compact('mensaje'));
			}
		}else{
			$mensaje = 'Usted no esta matriculado en ninguna ficha.';
			return view('Modules.Seguimiento.Horario.errores', compact('mensaje'));
		}
		return view("Modules.Seguimiento.Horario.horarioAprendiz",compact("rol","horarios_detalle","pla_fic_id","pla_fec_tri_id","trimestre","trimestres","fichas","arrayErrores","diaOrtografia", "horarios", "programacion", "faseOrtografia"));
	}
	
	public function postGuardarcambios(){
	    $_POST = $this->seguridad($_POST);
		extract($_POST);
		//dd($_POST);
		$notificaciones['errores'] = 0;

		foreach($dia as $key => $val){
			$horaInicio = $hora_inicio[$key];
			$horaFin = $hora_fin[$key];
			$planeacion_fic_det_id = $pla_fic_det_id[$key];
			$inicio_fecha = $fecha_inicio[$key];
			$fin_fecha = $fecha_fin[$key];

          	if($horaInicio < $horaFin){
    			$sql = '
    				select 	pla_tip_id
    				from 	sep_planeacion_ficha_detalle
    				where 	((pla_fic_det_hor_inicio < '.$horaInicio.' and (pla_fic_det_hor_fin > '.$horaInicio.' and pla_fic_det_hor_fin <= '.$horaFin.'))
						or	((pla_fic_det_hor_inicio >= '.$horaInicio.' and  pla_fic_det_hor_inicio < '.$horaFin.') and pla_fic_det_hor_fin > '.$horaFin.')
						or	(pla_fic_det_hor_inicio >= '.$horaInicio.' and pla_fic_det_hor_fin <= '.$horaFin.')
						or	(pla_fic_det_hor_inicio < '.$horaInicio.' and pla_fic_det_hor_fin > '.$horaFin.'))
					and		((pla_fic_det_fec_inicio < "'.$inicio_fecha.'" and (pla_fic_det_fec_fin > "'.$inicio_fecha.'" and pla_fic_det_fec_fin <= "'.$fin_fecha.'"))
						or 	((pla_fic_det_fec_inicio >= "'.$inicio_fecha.'" and pla_fic_det_fec_inicio < "'.$fin_fecha.'") and pla_fic_det_fec_fin > "'.$fin_fecha.'")
						or 	(pla_fic_det_fec_inicio < "'.$inicio_fecha.'" and pla_fic_det_fec_fin > "'.$fin_fecha.'")
						or 	(pla_fic_det_fec_inicio >= "'.$inicio_fecha.'" and pla_fic_det_fec_fin <= "'.$fin_fecha.'"))
    				and		pla_dia_id = '.$val.'
    				and 	not pla_fic_det_id = '.$planeacion_fic_det_id.'
    				and 	par_id_instructor = "'.$par_identificacion.'" limit 1';
    			$validar = DB::select($sql);
    			//dd($_POST);
    			if(count($validar)==0){
					if($pla_amb_id[$key] == 73){
						$horasSemanales = 2;
						$validarHorasColocar = 1;
						$horasTotales = $horaFin - $horaInicio;
					}else{
						$sql = '
							select 	sum(pla_fic_det_hor_totales) as totales
							from 	sep_planeacion_ficha_detalle
							where	((pla_fic_det_fec_inicio < "'.$inicio_fecha.'" and (pla_fic_det_fec_fin > "'.$inicio_fecha.'" and pla_fic_det_fec_fin <= "'.$fin_fecha.'"))
								or 	((pla_fic_det_fec_inicio >= "'.$inicio_fecha.'" and pla_fic_det_fec_inicio < "'.$fin_fecha.'") and pla_fic_det_fec_fin > "'.$fin_fecha.'")
								or 	(pla_fic_det_fec_inicio < "'.$inicio_fecha.'" and pla_fic_det_fec_fin > "'.$fin_fecha.'")
								or 	(pla_fic_det_fec_inicio >= "'.$inicio_fecha.'" and pla_fic_det_fec_fin <= "'.$fin_fecha.'"))
							and not pla_fic_det_id = '.$planeacion_fic_det_id.'
							and		par_id_instructor = "'.$par_identificacion.'" and pla_tip_id != 5';
						$horasProgramadas = DB::select($sql);
						$horasProgramadas = $horasProgramadas[0]->totales;

						$sql = '
							select 	par_horas_semanales
							from 	sep_participante
							where 	par_identificacion = "'.$par_identificacion.'" limit 1';
						$horasSemanales = DB::select($sql);
						$horasSemanales = $horasSemanales[0]->par_horas_semanales;
		
						$horasTotales = $horaFin - $horaInicio;
						$validarHorasColocar = $horasProgramadas + $horasTotales;
					}
					
    				if($validarHorasColocar <= $horasSemanales){
						$sql = '
							select 	pla_amb_suma_horas
							from 	sep_planeacion_ambiente
							where 	pla_amb_id = '.$pla_amb_id[$key].' limit 1';
						$validar_tipo_ambiente = DB::select($sql);

						if(count($validar_tipo_ambiente)>0){
							if($pla_amb_id[$key] == 88){
								$pla_tip_id = 3;
							}else{
								$suma_horas = $validar_tipo_ambiente[0]->pla_amb_suma_horas;
								if($suma_horas == 'SI'){
									$pla_tip_id = 4;
								}else{
									$pla_tip_id = 5;
								}
							}
							
							DB::beginTransaction();
							$sql = '
								update 	sep_planeacion_ficha_detalle
								set  	pla_fic_det_hor_inicio = '.$horaInicio.', pla_fic_det_hor_fin = '.$horaFin.',
										pla_dia_id = '.$val.', pla_fic_det_hor_totales = '.$horasTotales.',
										pla_amb_id = '.$pla_amb_id[$key].', pla_tip_id = '.$pla_tip_id.'
								where 	pla_fic_det_id = '.$planeacion_fic_det_id;
							DB::update($sql);
							DB::commit();
							$notificaciones['mensaje'][] = 'La modificación # <strong>'.($key+1).'</strong> <strong style="color:green;">SI</strong> se logro exitosamente.<br>';
						}else{
							$notificaciones['mensaje'][] = 'La modificación # <strong>'.($key+1).'</strong> <strong style="color:danger;">SI</strong> No se logro porque el ambiente no existe.<br>';
						}
    				}else{
    					$notificaciones['errores'] = 1;
    					$notificaciones['mensaje'][] = 'La modificación # <strong>'.($key+1).'</strong> <strong style="color:red;">NO</strong> se logro porque sobre pasa las <strong>'.$horasSemanales.'</strong> horas semanales del instructor.<br>';
    					$errores = true;
    				}
    			}else{
    				$notificaciones['errores'] = 1;
    				$notificaciones['mensaje'][] = 'La modificación # <strong>'.($key+1).'</strong> <strong style="color:red;">NO</strong> se logro porque el instructor está ocupado.<br>';
    			}
            }else{
                $notificaciones['errores'] = 1;
				$notificaciones['mensaje'][] = 'La modificación # <strong>'.($key+1).'</strong> <strong style="color:red;">NO</strong> se logro porque la hora inicio debe ser menor o diferente a la hora fin.<br>';
            }
		}
		
		echo json_encode($notificaciones);
	}
	
	public function postGuardarcambioscomplementario(){
	    $_POST = $this->seguridad($_POST);
		extract($_POST);
		$notificaciones['errores'] = 0;

		foreach($dia as $key => $val){
			$horaInicio = $hora_inicio[$key];
			$horaFin = $hora_fin[$key];
			$inicio_fecha = $fecha_inicio[$key];
			$fin_fecha = $fecha_fin[$key];
			$planeacion_fic_det_id = $pla_fic_det_id[$key];
          	if($horaInicio < $horaFin){
    			$sql = '
    				select 	pla_tip_id
    				from 		sep_planeacion_ficha_detalle
    				where 	((pla_fic_det_hor_inicio < '.$horaInicio.' and (pla_fic_det_hor_fin > '.$horaInicio.' and pla_fic_det_hor_fin <= '.$horaFin.'))
    				or	    	((pla_fic_det_hor_inicio >= '.$horaInicio.' and  pla_fic_det_hor_inicio < '.$horaFin.') and pla_fic_det_hor_fin > '.$horaFin.')
    				or	    	(pla_fic_det_hor_inicio >= '.$horaInicio.' and pla_fic_det_hor_fin <= '.$horaFin.')
    				or 	    	(pla_fic_det_hor_inicio < '.$horaInicio.' and pla_fic_det_hor_fin > '.$horaFin.'))
					and		((pla_fic_det_fec_inicio < "'.$inicio_fecha.'" and (pla_fic_det_fec_fin > "'.$inicio_fecha.'" and pla_fic_det_fec_fin <= "'.$fin_fecha.'"))
						or 	((pla_fic_det_fec_inicio >= "'.$inicio_fecha.'" and pla_fic_det_fec_inicio < "'.$fin_fecha.'") and pla_fic_det_fec_fin > "'.$fin_fecha.'")
						or 	(pla_fic_det_fec_inicio < "'.$inicio_fecha.'" and pla_fic_det_fec_fin > "'.$fin_fecha.'")
						or 	(pla_fic_det_fec_inicio >= "'.$inicio_fecha.'" and pla_fic_det_fec_fin <= "'.$fin_fecha.'"))
    				and		pla_dia_id = '.$val.'
    				and 	not pla_fic_det_id = '.$planeacion_fic_det_id.'
    				and 	par_id_instructor = "'.$par_identificacion.'" limit 1';
    			$validar = DB::select($sql);
    			//dd($_POST);
    			if(count($validar)==0){
							$sql = '
								select 	sum(pla_fic_det_hor_totales) as totales
								from 	sep_planeacion_ficha_detalle
								where	((pla_fic_det_fec_inicio < "'.$inicio_fecha.'" and (pla_fic_det_fec_fin > "'.$inicio_fecha.'" and pla_fic_det_fec_fin <= "'.$fin_fecha.'"))
									or 	((pla_fic_det_fec_inicio >= "'.$inicio_fecha.'" and pla_fic_det_fec_inicio < "'.$fin_fecha.'") and pla_fic_det_fec_fin > "'.$fin_fecha.'")
									or 	(pla_fic_det_fec_inicio < "'.$inicio_fecha.'" and pla_fic_det_fec_fin > "'.$fin_fecha.'")
									or 	(pla_fic_det_fec_inicio >= "'.$inicio_fecha.'" and pla_fic_det_fec_fin <= "'.$fin_fecha.'"))
								and not pla_fic_det_id = '.$planeacion_fic_det_id.'
								and			par_id_instructor = "'.$par_identificacion.'" and pla_tip_id != 5';
							$horasProgramadas = DB::select($sql);
							$horasProgramadas = $horasProgramadas[0]->totales;
							$horasSemanales = DB::select('select par_horas_semanales from sep_participante where par_identificacion = "'.$par_identificacion.'" limit 1');
							$horasSemanales = $horasSemanales[0]->par_horas_semanales;
			
							$horasTotales = $horaFin - $horaInicio;
							$validarHorasColocar = $horasProgramadas + $horasTotales;
    				if($validarHorasColocar <= $horasSemanales){
							$validarAmbiente = array();
							if($pla_amb_id[$key] != 88 and $pla_amb_id[$key] != 123){
								$sql = '
									select 	pla_dia_id
									from 	sep_planeacion_ficha_detalle
									where 	((pla_fic_det_hor_inicio < '.$horaInicio.' and (pla_fic_det_hor_fin > '.$horaInicio.' and pla_fic_det_hor_fin <= '.$horaFin.'))
										or	((pla_fic_det_hor_inicio >= '.$horaInicio.' and  pla_fic_det_hor_inicio < '.$horaFin.') and pla_fic_det_hor_fin > '.$horaFin.')
										or	(pla_fic_det_hor_inicio >= '.$horaInicio.' and pla_fic_det_hor_fin <= '.$horaFin.')
										or 	(pla_fic_det_hor_inicio < '.$horaInicio.' and pla_fic_det_hor_fin > '.$horaFin.'))
									and		((pla_fic_det_fec_inicio < "'.$inicio_fecha.'" and (pla_fic_det_fec_fin > "'.$inicio_fecha.'" and pla_fic_det_fec_fin <= "'.$fin_fecha.'"))
										or 	((pla_fic_det_fec_inicio >= "'.$inicio_fecha.'" and pla_fic_det_fec_inicio < "'.$fin_fecha.'") and pla_fic_det_fec_fin > "'.$fin_fecha.'")
										or 	(pla_fic_det_fec_inicio < "'.$inicio_fecha.'" and pla_fic_det_fec_fin > "'.$fin_fecha.'")
										or 	(pla_fic_det_fec_inicio >= "'.$inicio_fecha.'" and pla_fic_det_fec_fin <= "'.$fin_fecha.'"))
									and 	not pla_fic_det_id = '.$planeacion_fic_det_id.'
									and  	pla_amb_id = '.$pla_amb_id[$key].'
									and		pla_dia_id = '.$dia[$key].' limit 1';
								$validarAmbiente = DB::select($sql);
							}

							if(count($validarAmbiente)==0){
								DB::beginTransaction();
								DB::update('update  sep_planeacion_ficha_detalle  set  pla_dia_id = '.$val.',pla_fic_det_hor_inicio = '.$horaInicio.',pla_fic_det_hor_fin = '.$horaFin.',pla_fic_det_hor_totales = '.$horasTotales.', pla_amb_id = '.$pla_amb_id[$key].', pla_tip_id = 3 where pla_fic_det_id = '.$planeacion_fic_det_id);
								DB::commit();
								$notificaciones['mensaje'][] = 'La modificación # <strong>'.($key+1).'</strong> <strong style="color:green;">SI</strong> se logro exitosamente.<br>';
							}else{
								$notificaciones['errores'] = 1;
								$notificaciones['mensaje'][] = 'La modificación # <strong>'.($key+1).'</strong> <strong style="color:red;">NO</strong> el ambiente no esta disponible.<br>';
								$errores = true;
							}
						}else{
    					$notificaciones['errores'] = 1;
    					$notificaciones['mensaje'][] = 'La modificación # <strong>'.($key+1).'</strong> <strong style="color:red;">NO</strong> se logro porque sobre pasa las <strong>'.$horasSemanales.'</strong> horas semanales del instructor.<br>';
    					$errores = true;
    				}
    			}else{
    				$notificaciones['errores'] = 1;
    				$notificaciones['mensaje'][] = 'La modificación # <strong>'.($key+1).'</strong> <strong style="color:red;">NO</strong> se logro porque el instructor está ocupado.<br>';
    			}
            }else{
                $notificaciones['errores'] = 1;
				$notificaciones['mensaje'][] = 'La modificación # <strong>'.($key+1).'</strong> <strong style="color:red;">NO</strong> se logro porque la hora inicio debe ser menor o diferente a la hora fin.<br>';
            }
		}
		//dd($_POST);
		echo json_encode($notificaciones);
	}

	public function postEliminarrestriccion(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

		if(!is_numeric($id)){
			dd('El valor recibido no es númerico.');
		}
		DB::delete('delete from sep_planeacion_ficha_detalle where pla_fic_det_id = '.$id);
	}
	
	public function getModalrestriccion(){
		extract($_GET);

		$sql = '
			select 	pla_fic_det_fec_inicio, pla_fic_det_fec_fin, pla_dia_id,
					pla_fic_det_hor_inicio, pla_fic_det_hor_fin, pla_amb_id, pla_fic_det_id
			from 	sep_planeacion_ficha_detalle
			where 	par_id_instructor = "'.$cc.'"
			and		((pla_fic_det_fec_inicio < "'.$fecha_inicio.'" and (pla_fic_det_fec_fin > "'.$fecha_inicio.'" and pla_fic_det_fec_fin <= "'.$fecha_fin.'"))
				or 	((pla_fic_det_fec_inicio >= "'.$fecha_inicio.'" and pla_fic_det_fec_inicio < "'.$fecha_fin.'") and pla_fic_det_fec_fin > "'.$fecha_fin.'")
				or 	(pla_fic_det_fec_inicio < "'.$fecha_inicio.'" and pla_fic_det_fec_fin > "'.$fecha_fin.'")
				or 	(pla_fic_det_fec_inicio >= "'.$fecha_inicio.'" and pla_fic_det_fec_fin <= "'.$fecha_fin.'"))
			and 	pla_tip_id in(4,5)
			order by pla_dia_id, pla_fic_det_hor_inicio';
		$detalle = DB::select($sql);

		$sql = '
			select 	pla_amb_id,pla_amb_descripcion
			from 	sep_planeacion_ambiente
			where 	pla_amb_tipo = "Restriccion"
			and not pla_amb_id = 72
			and 	pla_amb_estado = "Activo"
			order by pla_amb_tipo,pla_amb_descripcion';
		$ambientes = DB::select($sql);

		$dias = array(1=>'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
		return view('Modules.Seguimiento.Horario.modalRestriccion', compact('detalle', 'dias', 'ambientes', 'cc'));
	}

	public function getModalcomplementario(){
		extract($_GET);
		$sql = '
			select * from sep_planeacion_ficha_detalle
			where 	((pla_fic_det_fec_inicio < "'.$fecha_inicio.'" and (pla_fic_det_fec_fin > "'.$fecha_inicio.'" and pla_fic_det_fec_fin <= "'.$fecha_fin.'"))
				or 	((pla_fic_det_fec_inicio >= "'.$fecha_inicio.'" and pla_fic_det_fec_inicio < "'.$fecha_fin.'") and pla_fic_det_fec_fin > "'.$fecha_fin.'")
				or 	(pla_fic_det_fec_inicio < "'.$fecha_inicio.'" and pla_fic_det_fec_fin > "'.$fecha_fin.'")
				or 	(pla_fic_det_fec_inicio >= "'.$fecha_inicio.'" and pla_fic_det_fec_fin <= "'.$fecha_fin.'"))
			and par_id_instructor = "'.$cc.'" and pla_tip_id = 3';
		$detalle = DB::select($sql);

		$sql = '
			select 	pla_amb_id,pla_amb_descripcion
			from 	sep_planeacion_ambiente
			where 	not pla_amb_tipo = "Restriccion"
			and not pla_amb_id in (72,88)
			and pla_amb_estado = "Activo" order by pla_amb_descripcion';
		$ambientes = DB::select($sql);
		$dias = array(1=>'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
		return view('Modules.Seguimiento.Horario.modalRestriccion', compact('detalle','dias','ambientes','cc'));
	}

	public function validar($jornadaInicio, $jornadaFin, $jornada, $variable, $fechaInicio, $fechaFin, $dia, $tipo, $pla_tip_id){
		$horariosLibres = array();
		$where = "";

		if($pla_tip_id == "induccion"){
			$pla_tip_id = 1;
		}else if($pla_tip_id == "trimestre"){
			$pla_tip_id = 2;
		}else if($pla_tip_id == "fase"){
			$pla_tip_id = 3;
		}else if($pla_tip_id == "practica"){
			$pla_tip_id = 4;
		}
		
		if($tipo == "ambiente"){
		    if($variable == 123){
				$horariosLibres[$jornada]["inicio"][] = $jornadaInicio;
				$horariosLibres[$jornada]["fin"][] = $jornadaFin;
				return $horariosLibres;
			}
			$where = "
					 	pla_amb_id =  $variable
				and 	pla_fic_det_hor_inicio < $jornadaFin  
				and 	pla_fic_det_hor_fin > $jornadaInicio "; 
		}else if($tipo == 'grupo'){
			$where = ' pla_fic_id = '.$variable.' ';
		}else if($tipo == "instructor"){
			$where = "
						par_id_instructor = '$variable'
				and 	pla_fic_det_hor_inicio < $jornadaFin
				and 	pla_fic_det_hor_fin > $jornadaInicio ";
		}

		

		$sql = "
			select 	pla_fic_det_id, par_id_instructor, pla_fic_det_hor_inicio, pla_fic_det_hor_fin
			from 	sep_planeacion_ficha_detalle
			where 	$where
			and	(
				(pla_fic_det_fec_inicio < '$fechaInicio' and (pla_fic_det_fec_fin > '$fechaInicio' and pla_fic_det_fec_fin <= '$fechaFin'))
			or 	((pla_fic_det_fec_inicio >= '$fechaInicio' and pla_fic_det_fec_inicio < '$fechaFin') and pla_fic_det_fec_fin > '$fechaFin')
			or 	(pla_fic_det_fec_inicio < '$fechaInicio' and pla_fic_det_fec_fin > '$fechaFin')
			or 	(pla_fic_det_fec_inicio >= '$fechaInicio' and pla_fic_det_fec_fin <= '$fechaFin')
			) 
			and		pla_dia_id = $dia
			order 	by pla_fic_det_hor_inicio asc";
		$franjasOcupadas = DB::select($sql);

		// Ojo tener en cuenta la jornada de Marlly
		if(count($franjasOcupadas) == 0){
			$horariosLibres[$jornada]["inicio"][] = $jornadaInicio;
			$horariosLibres[$jornada]["fin"][] = $jornadaFin;
		}else{
			foreach($franjasOcupadas as $index => $franja){
				$horIni = $franja->pla_fic_det_hor_inicio;
				$horFin = $franja->pla_fic_det_hor_fin;
				if($jornada != "noche" and $horIni < $jornadaInicio){
					$horIni = $jornadaInicio;
				}

				if(isset($horariosLibres[$jornada]["inicio"][0])){

					$anterior = count($horariosLibres[$jornada]["inicio"])-1;
					
					if($horIni == $horariosLibres[$jornada]["inicio"][$anterior]){
						$horariosLibres[$jornada]["inicio"][$anterior] = $horFin;
					}else{
						$horariosLibres[$jornada]["fin"][$anterior] = $horIni;
						if($horFin < $jornadaFin){
							$horariosLibres[$jornada]["inicio"][] = $horFin;
							$horariosLibres[$jornada]["fin"][] = $jornadaFin;
						}
					}
				}else{
					if($jornadaInicio == $horIni){
						if($jornadaFin != $horFin){
							$horariosLibres[$jornada]["inicio"][] = $horFin;
							$horariosLibres[$jornada]["fin"][] = $jornadaFin;
						}
					}else{
						//if($jornada != "Noche"){
							$horariosLibres[$jornada]["inicio"][] = $jornadaInicio;
							$horariosLibres[$jornada]["fin"][] = $horIni;

							if($horFin != $jornadaFin){
								$horariosLibres[$jornada]["inicio"][] = $horFin;
								$horariosLibres[$jornada]["fin"][] = $jornadaFin;
							}
						//}else{

						//}
					}
				}
			}
		}

		if(isset($horariosLibres[$jornada])){
			$anterior = count($horariosLibres[$jornada]["inicio"])-1;
			if($anterior == 0){
				if($horariosLibres[$jornada]["inicio"][$anterior] == $horariosLibres[$jornada]["fin"][$anterior]){
					unset($horariosLibres[$jornada]);
					unset($horariosLibres[$jornada]);
				}
			}else{
				if($horariosLibres[$jornada]["inicio"][$anterior] == $horariosLibres[$jornada]["fin"][$anterior]){
					unset($horariosLibres[$jornada]["inicio"][$anterior]);
					unset($horariosLibres[$jornada]["fin"][$anterior]);
				}
			}	
		}
		
		//dd($horariosLibres);
		/*echo "<pre>";
		print_r($sql);
		dd($horariosLibres);*/
		return $horariosLibres;
	}

	public function validarLosDosArrays($array1, $array2, $jornadaInicio, $jornadaFin, $jornada){
		$horariosLibres = array();
		$count1 = count($array1);
		$count2 = count($array2);

		if($count1 > $count2){
			$arrayMayor = $array1;
			$arrayMenor = $array2;
		}else if($count1 > $count2){
			$arrayMayor = $array2;
			$arrayMenor = $array1;
		}else{
			$arrayMayor = $array1;
			$arrayMenor = $array2;
		}
		
		foreach($arrayMenor[$jornada]["inicio"] as $key => $val){
			$horIniMenor = $arrayMenor[$jornada]["inicio"][$key];
			$horFinMenor = $arrayMenor[$jornada]["fin"][$key];
			foreach($arrayMayor[$jornada]["inicio"] as $key1 => $val1){
				$horIniMayor = $arrayMayor[$jornada]["inicio"][$key1];
				$horFinMayor = $arrayMayor[$jornada]["fin"][$key1];

				if(isset($horariosLibres[$jornada]["inicio"][0])){
					$anterior = count($horariosLibres[$jornada]["inicio"])-1;
					//echo $horariosLibres[$jornada]["inicio"][$anterior]." ".$horariosLibres[$jornada]["fin"][$anterior];
					if($horIniMayor > $horariosLibres[$jornada]["fin"][$anterior]){
						$horariosLibres[$jornada]["inicio"][] = $horIniMayor;
						$horariosLibres[$jornada]["fin"][] = $horFinMayor;
					}
				}else{
					if($horIniMenor == $horIniMayor){
						if($horFinMenor > $horFinMayor){
							$horariosLibres[$jornada]["inicio"][] = $horIniMenor;
							$horariosLibres[$jornada]["fin"][] = $horFinMayor;
						}else{
							$horariosLibres[$jornada]["inicio"][] = $horIniMenor;
							$horariosLibres[$jornada]["fin"][] = $horFinMenor;
						}
					}else if($horFinMenor == $horFinMayor){
						if($horIniMayor < $horIniMenor){
							$horariosLibres[$jornada]["inicio"][] = $horIniMenor;
							$horariosLibres[$jornada]["fin"][] = $horFinMenor;
						}else{
							$horariosLibres[$jornada]["inicio"][] = $horIniMayor;
							$horariosLibres[$jornada]["fin"][] = $horFinMenor;
						}
					}
				}
			}
		}

		return $horariosLibres;
	}

	public function crearHorario($array, $programaCodigo){
		extract($array);
		
		$sql = '
			select 	pla_fic_id
			from 	sep_planeacion_ficha
			where 	fic_numero = "'.$fic_numero.'" limit 1';
		$validarExistenciaHorario = DB::select($sql);
		if(count($validarExistenciaHorario)>0){
			dd('El horario de la ficha '.$fic_numero.' ya existe en nuestra base de datos.');
		}

		$horario = array();
		$errores = array();
		$dia = array(1 => "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");

		// Fechas etapa lectiva
		
		/*$fechasTrimestre = array();
		$fecha_copia = $fecha_inicio_lectiva;
		
		$inicio_anio = 5;
		$fecha_fin_anio = 51;
		$inicio_semana_confraternidad = 27;
		$semanas_que_no_cuentan = array(1, 2, 3, 4, 27, 28, 51, 52, 53);
		$duracion_trimestres_ficha = $trimestres_lectiva + $trimestres_productiva;
		$tipo_trimestre = 'lectiva';
		for($i=1; $i<=$duracion_trimestres_ficha; $i++){
			if($i > $trimestres_lectiva){
				$tipo_trimestre = 'productiva';
			}
			// Crear fecha inicio trimestre
			$validar_antes_fecha = date("W", strtotime($fecha_copia));
			if(!in_array($validar_antes_fecha, $semanas_que_no_cuentan)){
				$horario[$tipo_trimestre][$i]['fecha_inicio'][] = $fecha_copia;
			}
			// Crear los cortes entre fechas
			for($j=1; $j<=11; $j++){
				$fecha_copia = date('Y-m-d', strtotime($fecha_copia.' + 1 week'));
				$validar_fecha = date("W", strtotime($fecha_copia));
				if(in_array($validar_fecha, $semanas_que_no_cuentan)){
					$j--;
					if($validar_fecha == $fecha_fin_anio){
						$horario[$tipo_trimestre][$i]['fecha_fin'][] = date('Y-m-d', strtotime($fecha_copia.'last Saturday'));
					}else if($validar_fecha == $inicio_semana_confraternidad){
						$horario[$tipo_trimestre][$i]['fecha_fin'][] = date('Y-m-d', strtotime($fecha_copia.'last Saturday'));
					}
				}else{
					if($validar_fecha == $inicio_anio){
						$horario[$tipo_trimestre][$i]['fecha_inicio'][] = $fecha_copia;
					}else if($validar_fecha == ($inicio_semana_confraternidad+2)){
						$horario[$tipo_trimestre][$i]['fecha_inicio'][] = $fecha_copia;
					}
				}
			}

			// Crear fecha fin trimestre
			if(in_array($fecha_copia, $horario[$tipo_trimestre][$i]['fecha_inicio'])){
				$posicion = array_search($fecha_copia, $horario[$tipo_trimestre][$i]['fecha_inicio']);
				unset($horario[$tipo_trimestre][$i]['fecha_inicio'][$posicion]);
			}else{
				$horario[$tipo_trimestre][$i]['fecha_fin'][] = date('Y-m-d', strtotime($fecha_copia.'last Saturday'));
			}
		}*/

		$duracion_trimestres_ficha=$trimestres_lectiva+$trimestres_productiva;
		$sql="select pla_fec_tri_id, pla_fec_tri_fec_inicio as inicio , pla_fec_tri_fec_fin as fin
		      from sep_planeacion_fecha_trimestre
			  where pla_fec_tri_fec_inicio >= '".$fecha_inicio_lectiva."'
			  limit $duracion_trimestres_ficha";
        $fechas=DB::select($sql);
       
		$tipo_trimestre="lectiva";
		$pla_fec_tri_id=$fechas[0]->pla_fec_tri_id;
		$id_trimestre_fin_lectiva=$pla_fec_tri_id + $trimestres_lectiva;
		
        $contador=0;
		foreach ($fechas as $key => $val) {
			$contador++;
			if($val->pla_fec_tri_id >= $id_trimestre_fin_lectiva){
				$tipo_trimestre="productiva";
			}
			$horario[$tipo_trimestre][$contador]['fecha_inicio'][] = $val->inicio;
			$horario[$tipo_trimestre][$contador]['fecha_fin'][] = $val->fin;
		}


		$divisiones_ultimo_trimestre = (count($horario['lectiva'][$trimestres_lectiva]['fecha_fin']) - 1);
		$fecha_fin_lectiva = $horario['lectiva'][$trimestres_lectiva]['fecha_fin'][$divisiones_ultimo_trimestre];

		
        if(isset($horario['productiva'])){
			$cantidad_registros=count($horario['productiva'][$duracion_trimestres_ficha]['fecha_fin']);
			$fecha_inicio_productiva = $horario['productiva'][($trimestres_lectiva + 1)]['fecha_inicio'][0];
			$fecha_fin_productiva = $horario['productiva'][$duracion_trimestres_ficha]['fecha_fin'][($cantidad_registros - 1)];
				foreach ($horario['productiva'] as $key => $value) {
					for($i=1; $i<=$cantidad_registros; $i++){
						$horario['productiva'][$key]['instructor'][] = $instructor_lider;
					}
				}	
		}else{
			$fecha_inicio_productiva = 'null';
			$fecha_fin_productiva = 'null';
		}

		/*if($trimestres_productiva > 0){
			$fecha_inicio_productiva = $horario['productiva'][($trimestres_lectiva + 1)]['fecha_inicio'][0];
			foreach ($horario['productiva'] as $key => $value) {
				$cantidad_registros = count($value['fecha_inicio']);
				for($i=1; $i<=$cantidad_registros; $i++){
					$horario['productiva'][$key]['instructor'][] = $instructor_lider;
				}
			}
			$fecha_fin_productiva = $horario['productiva'][$duracion_trimestres_ficha]['fecha_fin'][($cantidad_registros - 1)];
		}else{
			$fecha_inicio_productiva = 'null';
			$fecha_fin_productiva = 'null';
		}*/

		// Id de la oferta seleccionada
		if($pla_tip_ofe_id == "abierta"){
			$tipo_oferta = 1;
		}else if($pla_tip_ofe_id == "cerrada"){
			$tipo_oferta = 2;
		}

		// Registramos los datos principales de la ficha
		$sql = '
			insert into sep_planeacion_ficha(
				pla_fic_id, pla_fic_usu_creador,
				pla_fic_can_trimestre, pla_fic_can_trimestre_productiva,
				pla_fic_fec_ini_induccion, pla_fic_fec_fin_lectiva,
				fecha_inicio_productiva, fecha_fin_productiva,
				fic_numero, pla_tip_ofe_id, pla_fra_id, pla_ins_lider
			) values (
				default, "'. \Auth::user()->par_identificacion .'",
				'.$trimestres_lectiva.', '.$trimestres_productiva.',
				"'.$fecha_inicio_lectiva.'", "'.$fecha_fin_lectiva.'",
				"'.$fecha_inicio_productiva.'", "'.$fecha_fin_productiva.'",
				"'.$fic_numero.'", '.$tipo_oferta.', '.$pla_fra_id.', "'.$instructor_lider.'")';
		DB::insert($sql);
		$pla_fic_id = DB::getPdo()->lastInsertId();

		// Registrar actividades del programa
		$sql = '
			select 	fas_id, com_descripcion, res_descripcion,
					act_descripcion, pla_can_hor_presenciales, pla_tip_id
			from 	sep_plantilla pla, sep_plantilla_detalle pla_det
			where 	pla.pla_id = pla_det.pla_id
			and 	pla.prog_codigo = "'.$programaCodigo.'"';
		$datosPlantilla = DB::select($sql);
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
		}

		// Registrar los trimestres
		foreach($horario as $key1 => $val1){
			foreach($val1 as $key2 => $val2){
				foreach($horario[$key1][$key2]['fecha_inicio'] as $key3 => $val3){
					$fecha_inicio = $val3;
					$fecha_fin = $horario[$key1][$key2]['fecha_fin'][$key3];

					$sql = '
						insert into sep_planeacion_ficha_trimestre
						(id, pla_fic_id, fecha_inicio, fecha_fin, trimestre_numero, trimestre_tipo)
						values
						(default, '.$pla_fic_id.', "'.$fecha_inicio.'", "'.$fecha_fin.'", "'.$key2.'", "'.$key1.'")';
					DB::insert($sql);
				}
			}
		}
		
		// Cargar actividades por trimestre
		if($trimestres_lectiva == 1){
			$horas_trimestre = 300;
			foreach($pla_can_hor_presenciales as $key => $val){
				$horario['lectiva'][1]['actividad'][0]['fas_id'][] = $fas_id[$key];
				$horario['lectiva'][1]['actividad'][0]['com_descripcion'][] = $com_descripcion[$key];
				$horario['lectiva'][1]['actividad'][0]['res_descripcion'][] = $res_descripcion[$key];
				$horario['lectiva'][1]['actividad'][0]['act_descripcion'][] = $act_descripcion[$key];
				$horario['lectiva'][1]['actividad'][0]['pla_can_hor_presenciales'][] = $val;
				$horario['lectiva'][1]['actividad'][0]['par_id_instructor'][] = $par_id_instructor[$key];
				$horario['lectiva'][1]['actividad'][0]['amb_id'][] = $amb_id[$key];
			}
		}else{
			$horas_trimestre = ceil($horas_programa / $trimestres_lectiva);
			$contador_horas_presenciales = 0;
			$trimestre = 1;
			$fecha = 0;
			foreach($pla_can_hor_presenciales as $key => $val){
				$val =  number_format(round($val), 0);
				$copia_contador_horas_presenciales = $contador_horas_presenciales;
				$contador_horas_presenciales += $val;

				if($contador_horas_presenciales > $horas_trimestre){
					$diferencia_antes = number_format(($horas_trimestre - $copia_contador_horas_presenciales), 0);
					$diferencia_despues = number_format(($contador_horas_presenciales - $horas_trimestre), 0);

					$horario['lectiva'][$trimestre]['actividad'][$fecha]['fas_id'][] = $fas_id[$key];
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['com_descripcion'][] = $com_descripcion[$key];
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['res_descripcion'][] = $res_descripcion[$key];
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['act_descripcion'][] = $act_descripcion[$key];
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['pla_can_hor_presenciales'][] = $diferencia_antes;
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['par_id_instructor'][] = $par_id_instructor[$key];
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['amb_id'][] = $amb_id[$key];

					$trimestre++;
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['fas_id'][] = $fas_id[$key];
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['com_descripcion'][] = $com_descripcion[$key];
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['res_descripcion'][] = $res_descripcion[$key];
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['act_descripcion'][] = $act_descripcion[$key];
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['pla_can_hor_presenciales'][] = $diferencia_despues;
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['par_id_instructor'][] = $par_id_instructor[$key];
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['amb_id'][] = $amb_id[$key];
					$contador_horas_presenciales = $diferencia_despues;
				}else{
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['fas_id'][] = $fas_id[$key];
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['com_descripcion'][] = $com_descripcion[$key];
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['res_descripcion'][] = $res_descripcion[$key];
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['act_descripcion'][] = $act_descripcion[$key];
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['pla_can_hor_presenciales'][] = $val;
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['par_id_instructor'][] = $par_id_instructor[$key];
					$horario['lectiva'][$trimestre]['actividad'][$fecha]['amb_id'][] = $amb_id[$key];
				}
			}
		}

		/*// Registrar actividades lectiva
		$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';',':','--', '?');
		foreach($horario['lectiva'] as $llave1 => $valor1){
			$fecha_inicio = $valor1['fecha_inicio'][0];
			$fecha_fin = $valor1['fecha_fin'][0];
			foreach($valor1['actividad'][0]['fas_id'] as $key => $valor2){
				$fase = $valor2;
				$competencia = $valor1['actividad'][0]['com_descripcion'][$key];
				$resultado = $valor1['actividad'][0]['res_descripcion'][$key];
				$actividad = $valor1['actividad'][0]['act_descripcion'][$key];
				$act_horas = $valor1['actividad'][0]['pla_can_hor_presenciales'][$key];
				$instructor = $valor1['actividad'][0]['par_id_instructor'][$key];

				$resultado = str_replace($caractereNoPremitidos, '', ucfirst(strtolower(mb_convert_encoding($resultado,'HTML-ENTITIES', 'UTF-8'))));
				$actividad = str_replace($caractereNoPremitidos, '', ucfirst(strtolower(mb_convert_encoding($actividad,'HTML-ENTITIES', 'UTF-8'))));
				$competencia = str_replace($caractereNoPremitidos, '', ucfirst(strtolower(mb_convert_encoding($competencia,'HTML-ENTITIES', 'UTF-8'))));

				$sql = '
					insert into 	sep_planeacion_ficha_actividades
					(pla_fic_act_id,pla_fic_act_competencia,pla_fic_act_resultado,pla_fic_act_actividad,
					pla_fic_act_horas,pla_fic_id,par_id_instructor,pla_tip_id,pla_trimestre_numero,fas_id, fecha_inicio, fecha_fin)
					values
					(default,"'.$competencia.'","'.$resultado.'","'.$actividad.'",
					"'.$act_horas.'",'.$pla_fic_id.',"'.$instructor.'",2, '.$llave1.' ,'.$fase.', "'.$fecha_inicio.'", "'.$fecha_fin.'")';
				DB::insert($sql);
			}
		}*/
		
		// Generamos la programación de los Instructores
		foreach($horario['lectiva'] as $llave1 => $valor1){
			foreach($valor1['fecha_inicio'] as $llave2 => $valor2){
				foreach($horario['lectiva'][$llave1]['actividad'][0]['pla_can_hor_presenciales'] as $llave3 => $act_hora){
					$instructor = $horario['lectiva'][$llave1]['actividad'][0]['par_id_instructor'][$llave3];
					$ambiente = $horario['lectiva'][$llave1]['actividad'][0]['amb_id'][$llave3];

					if(!isset($horario['lectiva'][$llave1]['programacion'][$llave2][$instructor][$ambiente])){
						$horario['lectiva'][$llave1]['programacion'][$llave2][$instructor][$ambiente][] = $act_hora;
					}else{
						$horario['lectiva'][$llave1]['programacion'][$llave2][$instructor][$ambiente][0] += $act_hora;
					}
				}
			}
		}

		// Operar horas por trimestre
		$instructor_datos = array();
		foreach($horario['lectiva'] as $llave1 => $valor1){
			foreach($valor1['programacion'] as $lleve2 => $valor2){
				foreach($valor2 as $instructor => $valor3){
					foreach($valor3 as $ambiente => $valor4){
						$horas = $valor4[0];
						$operacion = round(($horas / 11));
						if(($operacion % 2) == 1){
							if($operacion == 1 or $operacion == 3 or $operacion == 5){
								$operacion++;
							}else{
								$operacion--;
							}
						}
						$consulta_dato['horas_instructor'][$instructor] = $instructor;
						$consulta_dato['ambiente_nombre'][$ambiente] = $ambiente;
						$horario['lectiva'][$llave1]['programacion'][$lleve2][$instructor][$ambiente][0] = $operacion;
					}
				}
			}
		}

		// Horas mensuales de cada Instructor
		$sql = '
			select 	par_nombres, par_apellidos, par_identificacion, par_horas_semanales as horas
			from   	sep_participante
			where 	par_identificacion in ('.implode(',', $consulta_dato['horas_instructor']).')';
		$instructores_horas = DB::select($sql);
		foreach($instructores_horas as $val){
			$consulta_dato['horas_instructor'][$val->par_identificacion] = $val->horas;
			$consulta_dato['instructor_nombre'][$val->par_identificacion] = $val->par_nombres.' '.$val->par_apellidos;
		}

		// Nombre ambiente
		$sql = '
			select 	pla_amb_id, pla_amb_descripcion
			from 	sep_planeacion_ambiente
			where 	pla_amb_id in ('.implode(',', $consulta_dato['ambiente_nombre']).')';
		$instructores_horas = DB::select($sql);
		foreach($instructores_horas as $val){
			$consulta_dato['ambiente_nombre'][$val->pla_amb_id] = $val->pla_amb_descripcion;
		}

		// Hora inicio y fin de la jornada seleccionada
		$sql = '
			select 	pla_fra_descripcion, pla_fra_hor_inicio, pla_fra_hor_fin
			from 	sep_planeacion_franja
			where   pla_fra_id = '.$pla_fra_id.' limit 1';
		$franja = DB::select($sql);
		$jornada = $franja[0]->pla_fra_descripcion;
		$jornadaInicio = $franja[0]->pla_fra_hor_inicio;
		$jornadaFin = $franja[0]->pla_fra_hor_fin;

		if($nivel_formacion == 1){
			if($jornada == "Mañana"){
				$jornadaFin = 11;
			}
			else if($jornada == "Tarde"){
				$jornadaInicio = 11; $jornadaFin = 17;
			}
			else{
				 $jornadaInicio = 17; $jornadaFin = 21;
			}
		}

		// Validar y programar lectiva
		foreach($horario['lectiva'] as $llave1 => $valor1){
			foreach($valor1['programacion'] as $lleve2 => $valor2){
				$fecha_inicio = $horario['lectiva'][$llave1]['fecha_inicio'][$lleve2];
				$fecha_fin = $horario['lectiva'][$llave1]['fecha_fin'][$lleve2];
				foreach($valor2 as $instructor => $valor3){
					foreach($valor3 as $ambiente => $valor4){
						$instructorHoras = $valor4[0];
						$horasSumadas = 0;
						$instructorProgramado = false;
						if($instructorProgramado == true){
							$instructorProgramado = false; break;
						}
						for($j=1; $j<=6; $j++){
							if($j == 6 and $pla_fra_id == 3){
								$jornadaInicio = 6; $jornadaFin = 18;
							}else if($pla_fra_id == 3){
								$jornadaInicio = 18; $jornadaFin = 22;
							}

							$horariosLibresAmbientes = $this->validar($jornadaInicio, $jornadaFin, $jornada, $ambiente, $fecha_inicio, $fecha_fin, $j, "ambiente", "trimestre");
							if($horariosLibresAmbientes != null){

								// Validar el grupo según el rango de fechas
								$horariosLibresGrupo = $this->validar($jornadaInicio, $jornadaFin, $jornada, $pla_fic_id, $fecha_inicio, $fecha_fin, $j, "grupo", "trimestre");
								if($horariosLibresGrupo != null){

									// Buscar los espacios libres entre ambiente y grupo
									$horariosLibresAmbGru = $this->validarLosDosArrays($horariosLibresGrupo, $horariosLibresAmbientes, $jornadaInicio, $jornadaFin, $jornada);
									if($horariosLibresAmbGru != null){
	
										// Validar el instructor según el rango de fechas
										$horariosLibresInstructor = $this->validar($jornadaInicio, $jornadaFin, $jornada, $instructor, $fecha_inicio, $fecha_fin, $j, "instructor", "trimestre");
										if($horariosLibresInstructor != null){
	
											// Buscar los espacios libres entre los dos arrays
											$horariosLibresInsOtro = $this->validarLosDosArrays($horariosLibresAmbGru, $horariosLibresInstructor, $jornadaInicio, $jornadaFin, $jornada);
											if($horariosLibresInsOtro != null){
	
												if($instructorHoras > $horasSumadas){
													foreach($horariosLibresInsOtro[$jornada]["inicio"] as $key2 => $val2){
														$sql = '
															select 	sum(pla_fic_det_hor_totales) as total
															from 	sep_planeacion_ficha_detalle
															where 	((pla_fic_det_fec_inicio < "'.$fecha_inicio.'" and (pla_fic_det_fec_fin > "'.$fecha_inicio.'" and pla_fic_det_fec_fin <= "'.$fecha_inicio.'"))
																or 	((pla_fic_det_fec_inicio >= "'.$fecha_inicio.'" and pla_fic_det_fec_inicio < "'.$fecha_inicio.'") and pla_fic_det_fec_fin > "'.$fecha_inicio.'")
																or 	(pla_fic_det_fec_inicio < "'.$fecha_inicio.'" and pla_fic_det_fec_fin > "'.$fecha_inicio.'")
																or 	(pla_fic_det_fec_inicio >= "'.$fecha_inicio.'" and pla_fic_det_fec_fin <= "'.$fecha_inicio.'"))
															and 	pla_tip_id != 5
															and 	par_id_instructor = "'.$instructor.'"';
														$horas = DB::select($sql);
														$horas = $horas[0]->total;
														if(is_null($horas)){
															$horas = 0;
														}
	
														if($horas >= $consulta_dato['horas_instructor'][$instructor]){
															$nombre = ucwords(mb_strtolower($consulta_dato['instructor_nombre'][$instructor]));
															$errores["trimestre"][$llave1][] = 'Al instructor <strong>'.$nombre.'</strong>. Se le deben programar <strong>'.$instructorHoras.'</strong> horas, y se le programaron <strong>'.$horasSumadas.'</strong> horas porque supero el total de sus horas semanales '.$horas.'.';
															$terminarCicloDias = true;
															break;
														}else{
															$horaDeInicio = $val2;
															$horaDeFin = $horariosLibresInsOtro[$jornada]["fin"][$key2];
	
															$validarHoras = $horaDeFin - $horaDeInicio;
															if($validarHoras > $instructorHoras){
																$horaDeFin = $horaDeInicio+$instructorHoras;
															}
	
															$validarHoras = $horasSumadas + ($horaDeFin-$horaDeInicio);
															if($validarHoras > $instructorHoras){
																$horaDeFin = $horaDeInicio + ($instructorHoras - $horasSumadas);
															}
															
															$validarHoras = $horas + ($horaDeFin - $horaDeInicio);
															if($validarHoras > $consulta_dato['horas_instructor'][$instructor]){
																$diferenciaHoras = $consulta_dato['horas_instructor'][$instructor] - $horas;
																$horaDeFin = $horaDeInicio + $diferenciaHoras;
															}
															
															if($horaDeInicio >= $horaDeFin or $horaDeInicio > $jornadaFin or $horaDeFin > $jornadaFin or $horaDeInicio < $jornadaInicio or $horaDeFin < $jornadaInicio){ continue; }
															$totHoras = $horaDeFin - $horaDeInicio;
															$horasSumadas += $totHoras;
	
															$sql = '
																insert into	sep_planeacion_ficha_detalle (
																	pla_fic_det_id,pla_fic_id,pla_fic_det_fec_inicio,pla_fic_det_fec_fin,
																	pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_fic_det_hor_totales,par_id_instructor,
																	pla_dia_id,pla_amb_id,pla_trimestre_numero_ficha,pla_trimestre_numero_year,pla_tip_id
																)values(
																	default,'.$pla_fic_id.',"'.$fecha_inicio.'","'.$fecha_fin.'",
																	"'.$horaDeInicio.'","'.$horaDeFin.'","'.$totHoras.'","'.$instructor.'",
																	'.$j.',"'.$ambiente.'",'.$llave1.',1,2)';
															DB::insert($sql);
														}
													}
												}else{
													$instructorProgramado = true;
												}
											}else{
												if($j == 6){
												$nombre = ucwords(mb_strtolower($consulta_dato['instructor_nombre'][$instructor]));
												$errores["trimestre"][$llave1][] = 'Los horarios disponibles del instructor <strong>'.$nombre.'</strong>, no corresponden con los horarios asignados al grupo.';
												}
											}
										}else{
											if($j == 6 and $instructorHoras != $horasSumadas){
												$nombre = ucwords(mb_strtolower($consulta_dato['instructor_nombre'][$instructor]));
												$errores["trimestre"][$llave1][] = 'El instructor <strong>'.$nombre.'</strong> no tiene disponibilidad ningun día de la semana en esta franja horaria.';
											}
										}
									}else{
										$nombre = $consulta_dato['ambiente_nombre'][$ambiente];
										$errores["trimestre"][$llave1][] = 'El ambiente <strong>'.$nombre.'</strong> o el grupo <strong>'.$fic_numero.'</strong> no se encuentra disponible el día <strong>'.$dia[$j].'</strong>.';
									}
								}
							}else{
								if($j == 6 and $instructorHoras != $horasSumadas){
									$nombre = ucwords(mb_strtolower($consulta_dato['instructor_nombre'][$instructor]));
									$ambiente_nombre = $consulta_dato['ambiente_nombre'][$ambiente];
									$errores["trimestre"][$llave1][] = 'El ambiente <strong>'.$ambiente_nombre.'</strong> no está disponible para el instructor <strong>'.$nombre.'</strong>';
								}
							}
	
							if($j == 6 and $instructorHoras != $horasSumadas){
								$nombre = ucwords(mb_strtolower($consulta_dato['instructor_nombre'][$instructor]));
								$errores["trimestre"][$llave1][] = 'Al instructor <strong>'.$nombre.'</strong> se le deben programar <strong>'.$instructorHoras.'</strong> horas y se le programaron <strong>'.($horasSumadas).'</strong> horas';
							}
						}
					}
				}
			}
		}

		if(count($errores)>0){
			$this->registrarErrores($pla_fic_id,$errores);
		}

		// Validar y programar transversales lectiva
		// Consultamos las actividades para las transversales del programa
		if($nuevo == 'SI'){
			$disenoCurricularTipo = 'Viejo';
		}else{
			$disenoCurricularTipo = 'Nuevo';
		}

		/*$sql = '
			select 	tip.tra_tip_id, tra_tip_descripcion, tra_com_descripcion,
					tra_res_descripcion, tra_act_descripcion, niv_for_id,
					tra_act_horas, numero_trimestre_inicio
			from 	sep_transversal_nivel_formacion niv_for, sep_transversal_tipo tip,
					sep_transversal_actividad act
			where 	niv_for.tra_tip_id = tip.tra_tip_id
			and 	niv_for.tra_tip_id = act.tra_tip_id
			and 	act.dc_tipo = "'.$disenoCurricularTipo.'"
			and 	niv_for.dc_tipo = "'.$disenoCurricularTipo.'"
			and 	niv_for.niv_for_id = '.$nivel_formacion.'
			order by tra_tip_id asc';
		$actividades_transversal = DB::select($sql);

		$paraActuaizarTransversal = array();
		foreach($actividades_transversal as $key => $val){
			$pla_trimestre_numero = $val->numero_trimestre_inicio;
			$cantidad_trimestre = 1;
			$fecha_ini = array();
			$fecha_f = array();
			if(isset($horario['lectiva'][$pla_trimestre_numero]['fecha_inicio'][0])){
				$cantidad_trimestre = count($horario['lectiva'][$pla_trimestre_numero]['fecha_inicio']);
				for($i=0; $i<$cantidad_trimestre; $i++){
					$fecha_ini[$i] = '"'.$horario['lectiva'][$pla_trimestre_numero]['fecha_inicio'][$i].'"';
					$fecha_f[$i] = '"'.$horario['lectiva'][$pla_trimestre_numero]['fecha_fin'][$i].'"';
				}
			}else{
				$fecha_ini[0] = 'null';
				$fecha_f[0] = 'null';
			}

			foreach($fecha_ini as $llave1 => $fec){
				$fecha_inicio_programar = $fec;
				$fecha_fin_programar = $fecha_f[$llave1];
				$texto_parte = '';
				if($cantidad_trimestre == 2){
					$texto_parte = ' Duplicado # '.($llave1+1);
				}
				$competencia = ucfirst(mb_strtolower($val->tra_com_descripcion, 'UTF-8'));
				$resultado = ucfirst(mb_strtolower($val->tra_res_descripcion, 'UTF-8'));
				$actividad = '('.$val->tra_tip_descripcion.') - '.$texto_parte.' '.ucfirst(mb_strtolower($val->tra_act_descripcion, 'UTF-8'));
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
						"1111111111", 7, '.$pla_trimestre_numero.',
						5, '.$fecha_inicio_programar.', '.$fecha_fin_programar.')';
				DB::insert($sql);
				$paraActuaizarTransversal[$val->tra_tip_id][($llave1+1)]['id'][] = DB::getPdo()->lastInsertId();
				$paraActuaizarTransversal[$val->tra_tip_id][($llave1+1)]['fecha_inicio'][] = $fecha_inicio_programar;
				$paraActuaizarTransversal[$val->tra_tip_id][($llave1+1)]['fecha_fin'][] = $fecha_fin_programar;
			}
		}*/
		//dd($paraActuaizarTransversal);
		// Horarios dependiendo la jornada
		if($jornada == "Mañana"){
			$transversalJornada = "Tarde"; $transversalJornadaInicio = 12; $transversalJornadaFin = 18;
		}else if($jornada == "Tarde"){
			$transversalJornada = "Mañana"; $transversalJornadaInicio = 6; $transversalJornadaFin = 12;
		}else{
			$transversalJornada = "Noche"; $transversalJornadaInicio = 18; $transversalJornadaFin = 22;
		}

		// Transversales del programa de formación
		$sql = '
			select 	hor.tra_tip_id, numero_trimestre_inicio, tra_hor_can_hora
			from 	sep_transversal_nivel_formacion niv_for, sep_transversal_hora hor
			where 	niv_for.tra_tip_id = hor.tra_tip_id
			and 	niv_for.niv_for_id = '.$nivel_formacion.' and niv_for.dc_tipo = "'.$disenoCurricularTipo.'"
			and		hor.niv_for_id = '.$nivel_formacion.' and hor.dc_tipo = "'.$disenoCurricularTipo.'" order by numero_trimestre_inicio, tra_tip_id';
		$transversales_nivel_formacion = DB::select($sql);
		if(count($transversales_nivel_formacion) > 0){
			foreach($transversales_nivel_formacion as $key => $val){
				if(isset($horario['lectiva'][$val->numero_trimestre_inicio])){
					foreach($horario['lectiva'][$val->numero_trimestre_inicio]['fecha_inicio'] as $val1){
						$tra_trimestre[$val->tra_tip_id][] = $val->numero_trimestre_inicio;
						$tra_hora[$val->tra_tip_id] = $val->tra_hor_can_hora;
					}
				}
			}
			$concatenar = 'and tra_tip_id in('.implode(',', array_keys($tra_trimestre)).')';
			
			foreach($tra_trimestre as $key => $val){
				foreach($val as $key1 => $val1){
					for($i=$val1; $i<=$trimestres_lectiva; $i++){
						if(!in_array($i, $tra_trimestre[$key])){
							$tra_trimestre[$key][] = $i;
						}
					}
				}
			}
			
			// Consultar instructores para impartir cada transversal
			$valor_jornada_inicio = $transversalJornadaInicio.':00';
			$valor_jornada_fin = $transversalJornadaFin.':00';
			if($transversalJornadaInicio == 6){
				$valor_jornada_inicio = '06';
			}
			$sql = '
				select 	tra_tip_id,par.par_identificacion,par.par_nombres,par.par_apellidos,tra_ins_prioridad
				from 	sep_transversal_instructor tra_ins, sep_participante par,
						users user, sep_transversal_jornada tra_jor
				where 	tra_ins.par_id_instructor = par.par_identificacion 
				and 	tra_ins.par_id_instructor = tra_jor.par_id_instructor '.$concatenar.'
				and 	par.par_identificacion = user.par_identificacion
				and 	(jornada_inicio >= "'.$valor_jornada_inicio.'" and jornada_fin <= "'.$valor_jornada_fin.'")
				and 	user.estado = "1" and par.rol_id = 2
				order 	by tra_tip_id, tra_ins_prioridad';
			$instructores = DB::select($sql);
			if(count($instructores) > 0){
				$tra_instructores = array();
				foreach($instructores as $key => $val){
					$tra_instructores[$val->tra_tip_id][] = $val->par_identificacion;
				}
				
				// Actualiza prioridad del Instructor
				foreach($tra_instructores as $key => $val){
					$contador_instructor = count($tra_instructores[$key]);
					if($contador_instructor > 1){
						$contador_instructor--;
						$contador = 2;
						for($i=0; $i<$contador_instructor; $i++){
							$sql = 'update sep_transversal_instructor set tra_ins_prioridad = '.$contador.' where par_id_instructor = '.$tra_instructores[$key][$i];
							DB::update($sql);
							$contador++;
						}
						$sql = 'update sep_transversal_instructor set tra_ins_prioridad = 1 where par_id_instructor = '.$tra_instructores[$key][$i];
						DB::update($sql);
					}
				}

				// Consultar ambientes para impartir cada transversal
				$sql = 'select 	pla_amb.pla_amb_id, pla_amb_descripcion, tra_tip_id
						from 	sep_transversal_ambiente tra_amb, sep_planeacion_ambiente pla_amb
						where 	tra_amb.pla_amb_id = pla_amb.pla_amb_id '.$concatenar.'
						and 	pla_amb_estado = "Activo"
						order 	by tra_tip_id';
				$ambientes = DB::select($sql);
				if(count($ambientes) > 0){

					$tra_ambiente = array();
					foreach($ambientes as $key => $val){
						$tra_ambiente[$val->tra_tip_id][] = $val->pla_amb_id;
					}

					// Cargamos el array con la información que se necesita para iniciar la validación y programar
					$transversal = array();
					foreach($tra_trimestre as $key => $val){
						if(isset($tra_hora[$key]) and isset($tra_instructores[$key]) and isset($tra_ambiente[$key])){
							$transversal[$key]["trimestres"] = $tra_trimestre[$key];
							$transversal[$key]["instructor"] = $tra_instructores[$key];
							$transversal[$key]["ambiente"] = $tra_ambiente[$key];
							$transversal[$key]["hora"] = $tra_hora[$key];
						}
					}
					//dd($tra_trimestre);
					// Validar y programar transversales
					if(count($transversal) > 0){
						foreach($transversal as $key => $val){
							$termineProgramarActividad = false;
							$yaResgistreActividades = false;
							$horas_transversal = $transversal[$key]['hora'];
							$trimestre_anterior = 0;
							foreach($transversal[$key]["trimestres"] as $key1 => $val1){
								$numero_trimestre = $val1;
								$contador_instructor = count($transversal[$key]["instructor"]);
								$fechas_trimestre[$key][$numero_trimestre][] = $horario['lectiva'][$numero_trimestre]['fecha_inicio'][0];
								$contador_fecha = count($fechas_trimestre[$key][$numero_trimestre]);

								$fecInicio = $horario['lectiva'][$numero_trimestre]['fecha_inicio'][($contador_fecha-1)];
								$fecFin = $horario['lectiva'][$numero_trimestre]['fecha_fin'][($contador_fecha-1)];
								foreach($transversal[$key]["instructor"] as $key2 => $val2){
									$horasSumadas = 0;
									$instructorHoras = $horas_transversal;
									$contador_ambiente = count($transversal[$key]["ambiente"]);
									$instructor_no_disponible = false;
									$instructorProgramado = false;
									foreach($transversal[$key]["ambiente"] as $key3 => $val3){
										//echo 'Transversal '.$key.' trimestre '.$val1.' Ambiente '.$val3.' Instructor '.$val2.'<br>';
										if($instructorProgramado == true){
											$instructorProgramado = false; break;
										}
										for($j=1; $j<7; $j++){
										    if($jornada == "Noche"){
												if($j == 6){
													$transversalJornadaInicio = 12;
													$transversalJornadaFin = 18;
												}else{
													$transversalJornadaInicio = 18;
													$transversalJornadaFin = 22;
												}
											}
											$horariosLibresAmbientes = $this->validar($transversalJornadaInicio, $transversalJornadaFin, $transversalJornada, $val3, $fecInicio, $fecFin, $j, "ambiente", "trimestre");
											if($horariosLibresAmbientes != null){
												
												// Validar el grupo según el rango de fechas
												$horariosLibresGrupo = $this->validar($transversalJornadaInicio, $transversalJornadaFin, $transversalJornada, $pla_fic_id, $fecInicio, $fecFin, $j, "grupo", "trimestre");
												if($horariosLibresGrupo != null){
													
													// Buscar los espacios libres entre ambiente y grupo
													$horariosLibresAmbGru = $this->validarLosDosArrays($horariosLibresGrupo, $horariosLibresAmbientes, $transversalJornadaInicio, $transversalJornadaFin, $transversalJornada);
													if($horariosLibresAmbGru != null){
														
														// Validar el instructor según el rango de fechas
														$horariosLibresInstructor = $this->validar($transversalJornadaInicio, $transversalJornadaFin, $transversalJornada, $val2, $fecInicio, $fecFin, $j, "instructor", "trimestre");
														if($horariosLibresInstructor != null){

															// Buscar los espacios libres entre los dos arrays
															$horariosLibresInsOtro = $this->validarLosDosArrays($horariosLibresAmbGru, $horariosLibresInstructor, $transversalJornadaInicio, $transversalJornadaFin, $transversalJornada);
															if($horariosLibresInsOtro != null){
																//echo "select par_horas_semanales as horas from sep_participante where par_identificacion = '$val2'";
																if($instructorHoras > $horasSumadas){
																	$horasInstructor = DB::select('select par_horas_semanales as horas from sep_participante where par_identificacion = "'.$val2.'" limit 1');
																	$horasInstructor = $horasInstructor[0]->horas;
																	
																	foreach($horariosLibresInsOtro[$transversalJornada]["inicio"] as $key4 => $val4){
																		$sql = '
																			select 	sum(pla_fic_det_hor_totales) as total
																			from 		sep_planeacion_ficha_detalle
																			where 	((pla_fic_det_fec_inicio < "'.$fecInicio.'" and (pla_fic_det_fec_fin > "'.$fecInicio.'" and pla_fic_det_fec_fin <= "'.$fecFin.'"))
																				or 		((pla_fic_det_fec_inicio >= "'.$fecInicio.'" and pla_fic_det_fec_inicio < "'.$fecFin.'") and pla_fic_det_fec_fin > "'.$fecFin.'")
																				or 		(pla_fic_det_fec_inicio < "'.$fecInicio.'" and pla_fic_det_fec_fin > "'.$fecFin.'")
																				or 		(pla_fic_det_fec_inicio >= "'.$fecInicio.'" and pla_fic_det_fec_fin <= "'.$fecFin.'"))
																			and	 		not	pla_tip_id = 5
																			and 		par_id_instructor = "'.$val2.'"';
																		$horas = DB::select($sql);
																		$horas = $horas[0]->total;
																		if(is_null($horas)){ $horas = 0; }

																		if($horas >= $horasInstructor){
																			$ambiente = DB::select('select pla_amb_descripcion from sep_planeacion_ambiente where pla_amb_id = '.$val3.' limit 1');
																			$instructor = DB::select('select concat(par_nombres," ",par_apellidos) as nombre from sep_participante where par_identificacion = "'.$val2.'" limit 1');
																			$nombre = ucwords(mb_strtolower($instructor[0]->nombre));
																			$ambiente = $ambiente[0]->pla_amb_descripcion;
																			$errores["trimestre"][$key][] = 'Al instructor <strong>'.$nombre.'</strong>. Se le deben programar <strong>'.$instructorHoras.'</strong> horas, y se le programaron <strong>'.($instructorHoras + ($horasSumadas)).'</strong> horas, porque supero el total de sus horas semanales <strong>'.$horas.'</strong>.';
																			$terminarCicloDias = true;
																			break;
																		}else{
																			$horaDeInicio = $val4;
																			$horaDeFin = $horariosLibresInsOtro[$transversalJornada]["fin"][$key4];

																			$validarHoras = $horaDeFin - $horaDeInicio;
																			if($validarHoras > $instructorHoras){
																				$horaDeFin = $horaDeInicio+$instructorHoras;
																			}

																			$validarHoras = $horasSumadas + ($horaDeFin-$horaDeInicio);
																			if($validarHoras > $instructorHoras){
																				$horaDeFin = $horaDeInicio + ($instructorHoras - $horasSumadas);
																			}
																			
																			$validarHoras = $horas + ($horaDeFin - $horaDeInicio);
																			if($validarHoras > $horasInstructor){
																				$diferenciaHoras = $horasInstructor - $horas;
																				$horaDeFin = $horaDeInicio + $diferenciaHoras;
																			}

																			if($horaDeInicio >= $horaDeFin or $horaDeInicio > $transversalJornadaFin or $horaDeFin > $transversalJornadaFin or $horaDeInicio < $transversalJornadaInicio or $horaDeFin < $transversalJornadaInicio){ continue; }

																			$totHoras = $horaDeFin - $horaDeInicio;
																			$horasSumadas += $totHoras;

																			$sql = '
																				insert into	sep_planeacion_ficha_detalle (
																					pla_fic_det_id,pla_fic_id,pla_fic_det_fec_inicio,pla_fic_det_fec_fin,
																					pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_fic_det_hor_totales,par_id_instructor,
																					pla_dia_id,pla_amb_id,pla_trimestre_numero_ficha,pla_trimestre_numero_year,pla_tip_id
																				) values (
																					default,'.$pla_fic_id.',"'.$fecInicio.'","'.$fecFin.'",
																					"'.$horaDeInicio.'","'.$horaDeFin.'","'.$totHoras.'","'.$val2.'",
																					'.$j.',"'.$val3.'",'.$val1.', 1, 7)';
																			DB::insert($sql);

																			/*if($yaResgistreActividades == false){
																				if(isset($paraActuaizarTransversal[$key])){
																					$recorra = 1;
																					if($trimestre_anterior == $key){
																						$recorra = 2;
																					}
																					foreach($paraActuaizarTransversal[$key][$recorra]['id'] as $llave20 => $traValor){
																						$fecha_inicio_actualizar = $paraActuaizarTransversal[$key][$recorra]['fecha_inicio'][$llave20];
																						$fecha_fin_actualizar = $paraActuaizarTransversal[$key][$recorra]['fecha_fin'][$llave20];
																						$sql = '
																							update  sep_planeacion_ficha_actividades
																							set 	par_id_instructor = "'.$val2.'", pla_trimestre_numero = "'.$val1.'",
																									fecha_inicio = '.$fecha_inicio_actualizar.', fecha_fin = '.$fecha_fin_actualizar.'
																							where 	pla_fic_act_id = '.$traValor;
																						DB::update($sql);
																					}
																					$yaResgistreActividades = true;
																				}
																			}*/
																			$trimestre_anterior = $key;
																		}
																		if($horasSumadas == $instructorHoras){
																			$termineProgramarActividad = true;
																			break;
																		}
																	}
																	if($termineProgramarActividad == true){
																		break;
																	}
																}else{
																	$instructorProgramado = true;
																	break;
																}
															}else{
																if($j == 6 and $contador_instructor == ($key2+1)){
																	$errores["trimestre"][$val1][] = 'Los horarios disponibles de los instructores, no corresponden con los horarios asignados al grupo.';
																}
															}
														}else{
															if($j == 6 and $instructorHoras != $horasSumadas){
																$instructor = DB::select('select concat(par_nombres," ",par_apellidos) as nombre from sep_participante where par_identificacion = "'.$val2.'" limit 1');
																$nombre = ucwords(mb_strtolower($instructor[0]->nombre));
																$errores["trimestre"][$val1][] = 'El instructor <strong>'.$nombre.'</strong> no tiene disponibilidad ningun día de la semana en esta franja horaria.';
																$instructor_no_disponible = true;
																break;
															}
														}
													}else{
														$ambiente = DB::select('select pla_amb_descripcion from sep_planeacion_ambiente where pla_amb_id = '.$val3.' limit 1');
														$ambiente = $ambiente[0]->pla_amb_descripcion;
														$errores["trimestre"][$val1][] = 'El ambiente <strong>'.$ambiente.'</strong> o el grupo <strong>'.$fic_numero.'</strong> no se encuentra disponible el día <strong>'.$dia[$j].'</strong>.';
													}
												}
											}else{
												if($j == 6 and $instructorHoras != $horasSumadas and $contador_ambiente == $key3){
													$ambiente = DB::select('select pla_amb_descripcion from sep_planeacion_ambiente where pla_amb_id = '.$val3.' limit 1');
													$instructor = DB::select('select concat(par_nombres," ",par_apellidos) as nombre from sep_participante where par_identificacion = "'.$val2.'" limit 1');
													$nombre = ucwords(mb_strtolower($instructor[0]->nombre));
													$ambiente = $ambiente[0]->pla_amb_descripcion;
													$errores["trimestre"][$val1][] = 'El ambiente <strong>'.$ambiente.'</strong> no está disponible para el instructor <strong>'.$nombre.'</strong> se le deben programar <strong>'.$instructorHoras.'</strong> horas y se le programaron <strong>'.$horasSumadas.'</strong> horas.';
												}
											}

											if($j == 6 and $instructorHoras != $horasSumadas and $contador_ambiente == $key3){
												$instructor = DB::select('select concat(par_nombres," ",par_apellidos) as nombre from sep_participante where par_identificacion = "'.$val2.'" limit 1');
												$nombre = ucwords(mb_strtolower($instructor[0]->nombre));
												$errores["trimestre"][$val1][] = 'Al instructor <strong>'.$nombre.'</strong> se le deben programar <strong>'.$instructorHoras.'</strong> horas y se le programaron <strong>'.$horasSumadas.'</strong> horas';
											}
										}
										if($instructor_no_disponible == true){ break; }
										if($termineProgramarActividad == true){ break; }
									}
									if($termineProgramarActividad == true){ break; }
								}
								$puede_parar = true;
								if(isset($transversal[$key]["trimestres"][($key1 + 1)])){ 
									if($transversal[$key]["trimestres"][($key1 + 1)] == $val1){
										$puede_parar = false;
										$yaResgistreActividades = false;
									}
								}
								if($termineProgramarActividad == true and $puede_parar == true){ break; }
							}
						}

						if(count($errores)>0){
							$this->registrarErrores($pla_fic_id,$errores);
						}
					}
				}
			}
		}

		// Etapa práctica
		if($trimestres_productiva > 0 and $jornada != 'Noche'){
		    // Registrar actividades de etapa práctica
			/*$competencia = 'Resultado de aprendizaje etapa práctica';
			$resultado = 'Aplicar en la resolución de problemas reales del sector productivo, los conocimientos, habilidades y destrezas pertinentes a las competencias del programa de formación asumiendo estrategias y metodologías de autogestión.';
			$actividad = 'Resultado de aprendizaje etapa práctica';
			$act_horas =  40;*/
    				
			$ambienteEtapaPractica = 72;
			$jornadaInicio = 6;
			$jornadaFin = 22;
			foreach($horario['productiva'] as $trimestreFicha => $trimestreYear){
			    foreach($trimestreYear['fecha_inicio'] as $key_fecha => $valor_fecha){
					$fechaInicio = $horario['productiva'][$trimestreFicha]['fecha_inicio'][$key_fecha];
					$fechaFin = $horario['productiva'][$trimestreFicha]['fecha_fin'][$key_fecha];
					$instructor = $horario['productiva'][$trimestreFicha]['instructor'][$key_fecha];
    				$termineProgramacion = false;
    				$duracionResultado = 4;
    
    				/*$sql = '
    					insert into sep_planeacion_ficha_actividades(
    						pla_fic_act_id, pla_fic_act_competencia, pla_fic_act_resultado, pla_fic_act_actividad,
    						pla_fic_act_horas, pla_fic_id, par_id_instructor, pla_tip_id,
    						pla_trimestre_numero, fas_id, fecha_inicio, fecha_fin
    					) values (
    						default, "'.$competencia.'", "'.$resultado.'", "'.$actividad.'",
    						"'.$act_horas.'", '.$pla_fic_id.', "'.$instructor.'", 6,
    						'.$trimestreFicha.', 5, "'.$fechaInicio.'", "'.$fechaFin.'")';
    				DB::insert($sql);*/
    
    				foreach($dia as $keyDia => $d){
    					$horariosLibresGrupo = $this->franjasDisponibles($jornadaInicio, $jornadaFin, $fechaInicio, $fechaFin, $keyDia, 'grupo', $pla_fic_id);
    
    					if($horariosLibresGrupo != null){
    						$horariosLibresInstructor = $this->franjasDisponibles($jornadaInicio, $jornadaFin, $fechaInicio, $fechaFin, $keyDia, 'instructor', $instructor);
    
    						if($horariosLibresInstructor != null){
    							$compararGrupoConInstructor = $this->compararFranjas($horariosLibresGrupo, $horariosLibresInstructor);
    							
    							if($compararGrupoConInstructor != null){
    								if($termineProgramacion == false){
    									foreach($compararGrupoConInstructor['inicio'] as $key => $val){
    										$horaInicio = $val;
    										$horaFin = $compararGrupoConInstructor['fin'][$key];
    
    										$validarHoras = $horaFin - $horaInicio;
    										if($validarHoras > $duracionResultado){
    											$horaFin = $horaInicio + $duracionResultado;
    										}
    
    										if(($horaFin - $horaInicio)==$duracionResultado){
    											$sql = '
    												select 	sum(pla_fic_det_hor_totales) as total
    												from 	sep_planeacion_ficha_detalle
    												where 	((pla_fic_det_fec_inicio < "'.$fechaInicio.'" and (pla_fic_det_fec_fin > "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))
    													or 	((pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_inicio < "'.$fechaFin.'") and pla_fic_det_fec_fin > "'.$fechaFin.'")
    													or 	(pla_fic_det_fec_inicio < "'.$fechaInicio.'" and pla_fic_det_fec_fin > "'.$fechaFin.'")
    													or 	(pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))
    												and 	pla_tip_id != 5
    												and 	par_id_instructor = "'.$instructor.'"';
    											$instructorHorasProgramadas = DB::select($sql);
    											$instructorHorasProgramadas = $instructorHorasProgramadas[0]->total;
    											
    											$sql = '
    												select 	par_horas_semanales
    												from 	sep_participante
    												where 	par_identificacion = "'.$instructor.'" limit 1';
    											$instructorHorasSemanales = DB::select($sql);
    											$instructorHorasSemanales = $instructorHorasSemanales[0]->par_horas_semanales;
    
    											$validarDisponibilidad = $instructorHorasProgramadas+$duracionResultado;
    											if($validarDisponibilidad <= $instructorHorasSemanales){
    												$sql = '
    													insert into	sep_planeacion_ficha_detalle (
    														pla_fic_det_id, pla_fic_id,
    														pla_fic_det_fec_inicio, pla_fic_det_fec_fin,
    														pla_fic_det_hor_inicio, pla_fic_det_hor_fin,
    														pla_fic_det_hor_totales,par_id_instructor,
    														pla_dia_id, pla_amb_id, pla_trimestre_numero_ficha,
    														pla_trimestre_numero_year, pla_tip_id
    													) values (
    														default,'.$pla_fic_id.',
    														"'.$fechaInicio.'","'.$fechaFin.'",
    														"'.$horaInicio.'","'.$horaFin.'",
    														"'.$duracionResultado.'","'.$instructor.'",
    														'.$keyDia.',"'.$ambienteEtapaPractica.'",'.$trimestreFicha.',
    														1, 6)';
    												DB::insert($sql);
    												$termineProgramacion = true;
    											}
    										}
    									}
    								}
    							}else{
    								//echo 'Los horarios libres del instructor y el grupo no coinciden.';
    							}
    						}else{
    							//echo 'El instructor no esta disponible';
    						}
    					}else{
    						//echo 'El grupo no esta disponible';
    					}
    				}
			    }
			}
		}

		return $pla_fic_id;
	}

	public function generarEtapaPractica($etapaPractica,$jornada,$pla_fic_id,$fic_numero,$dias,$posicion){
		$ambienteEtapaPractica = 72;
		$jornadaInicio = 6;
		$jornadaFin = 22;
		
		foreach($etapaPractica['trimestreYear'] as $trimestreFicha => $trimestreYear){
			$fechaInicio = $etapaPractica['trimestreFechaInicio'][$trimestreFicha];
			$fechaFin = $etapaPractica['trimestreFechaFin'][$trimestreFicha];
			$duracionResultado = 4;
			$termineProgramacion = false;
			foreach($dias as $keyDia => $dia){
				foreach($etapaPractica['instructor'] as $keyInstructor => $instructor){
					/*echo 'instructor '.$instructor.'<br>'; echo 'dia '.$dia.'<br>';
					echo 'fechaInicio '.$fechaInicio.'<br>'; echo 'fechaFin '.$fechaFin.'<br><br>';*/ 
					
					/*echo '<pre>';
					print_r($sql);
					dd($horas);*/
					//comentar
					//$pla_fic_id = 9;
					$horariosLibresGrupo = $this->franjasDisponibles($jornadaInicio,$jornadaFin,$fechaFin,$keyDia,'grupo',$pla_fic_id);
					
					if($horariosLibresGrupo != null){
						$horariosLibresInstructor = $this->franjasDisponibles($jornadaInicio,$jornadaFin,$fechaFin,$keyDia,'instructor',$keyInstructor);
						if($horariosLibresInstructor != null){
							/* Pruebas */
							/*$horariosLibresGrupo = array();
							$horariosLibresGrupo['inicio'][] = 6;
							$horariosLibresGrupo['fin'][] = 10;

							$horariosLibresInstructor = array();
							$horariosLibresInstructor['inicio'][] = 6;
							$horariosLibresInstructor['fin'][] = 12;*/
							
							$compararGrupoConInstructor = $this->compararFranjas($horariosLibresGrupo,$horariosLibresInstructor);
							if($compararGrupoConInstructor != null){
								if($termineProgramacion == false){
									foreach($compararGrupoConInstructor['inicio'] as $key => $val){
										$horaInicio = $val;
										$horaFin = $compararGrupoConInstructor['fin'][$key];

										$validarHoras = $horaFin - $horaInicio;
										if($validarHoras > $duracionResultado){
											$horaFin = $horaInicio + $duracionResultado;
										}

										if(($horaFin - $horaInicio)==$duracionResultado){
											$sql = '
												select 	sum(pla_fic_det_hor_totales) as total
												from 	sep_planeacion_ficha_detalle
												where 	((pla_fic_det_fec_inicio < "'.$fechaInicio.'" and (pla_fic_det_fec_fin > "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))
													or 	((pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_inicio < "'.$fechaFin.'") and pla_fic_det_fec_fin > "'.$fechaFin.'")
													or 	(pla_fic_det_fec_inicio < "'.$fechaInicio.'" and pla_fic_det_fec_fin > "'.$fechaFin.'")
													or 	(pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))
												and 	pla_tip_id != 5
												and 	par_id_instructor = "'.$instructor.'"';
											$instructorHorasProgramadas = DB::select($sql);
											$instructorHorasProgramadas = $instructorHorasProgramadas[0]->total;
											$instructorHorasSemanales = DB::select('select par_horas_semanales from sep_participante where par_identificacion = "'.$instructor.'" limit 1');
											$instructorHorasSemanales = $instructorHorasSemanales[0]->par_horas_semanales;

											$validarDisponibilidad = $instructorHorasProgramadas+$duracionResultado;
											if($validarDisponibilidad <= $instructorHorasSemanales){
												$sql = '
													insert into	sep_planeacion_ficha_detalle (
														pla_fic_det_id,pla_fic_id,pla_fic_det_fec_inicio,pla_fic_det_fec_fin,
														pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_fic_det_hor_totales,par_id_instructor,
														pla_dia_id,pla_amb_id,pla_trimestre_numero_ficha,pla_trimestre_numero_year,pla_tip_id
													) values (
														default,'.$pla_fic_id.',"'.$fechaInicio.'","'.$fechaFin.'",
														"'.$horaInicio.'","'.$horaFin.'","'.$duracionResultado.'","'.$keyInstructor.'", 
														'.$keyDia.',"'.$ambienteEtapaPractica.'",'.$trimestreFicha.','.$trimestreYear.',6)';
												DB::insert($sql);
												$termineProgramacion = true;

												$competencia = 'Resultado de aprendizaje etapa práctica';
												$resultado = 'Aplicar en la resolución de problemas reales del sector productivo, los conocimientos, habilidades y destrezas pertinentes a las competencias del programa de formación asumiendo estrategias y metodologías de autogestión.';
												$actividad = 'Resultado de aprendizaje etapa práctica';
												$act_horas =  40;
									
												$sql = '
													insert into sep_planeacion_ficha_actividades (
														pla_fic_act_id,pla_fic_act_competencia,pla_fic_act_resultado,pla_fic_act_actividad,
														pla_fic_act_horas,pla_fic_id,par_id_instructor,pla_tip_id,pla_trimestre_numero,fas_id
													) values (
														default,"'.$competencia.'","'.$resultado.'","'.$actividad.'",
														"'.$act_horas.'",'.$pla_fic_id.',"'.$keyInstructor.'",6,'.$trimestreFicha.',5)';
												DB::insert($sql);
											}
										}
									}
								}
							}else{
								//echo 'Los horarios libres del instructor y el grupo no coinciden.';
							}
						}else{
							//echo 'El instructor no esta disponible';
						}
					}else{
						//echo 'El grupo no esta disponible';
					}
				}
			}
		}
	}
	
	public function compararFranjas($arrayUno,$arrayDos){
		$horariosDisponible = array();

		foreach($arrayUno['inicio'] as $keyUno => $valUno){
			foreach($arrayDos['inicio'] as $keyDos => $valDos){
				// Ordenar valores
				if($valUno <= $valDos and $arrayUno['fin'][$keyUno] <= $arrayDos['fin'][$keyDos]){
					$inicioUno = $valUno; $finUno = $arrayUno['fin'][$keyUno];
					$inicioDos = $valDos; $finDos = $arrayDos['fin'][$keyDos];
				}else{
					$inicioUno = $valDos; $finUno = $arrayDos['fin'][$keyDos];
					$inicioDos = $valUno; $finDos = $arrayUno['fin'][$keyUno];
				}

				if($inicioDos < $finUno){
					if($inicioUno >= $inicioDos){
						$horariosDisponible['inicio'][] = $inicioUno;
					}else{
						$horariosDisponible['inicio'][] = $inicioDos;
					}

					if($finUno >= $finDos){
						$horariosDisponible['fin'][] = $finDos;
					}else{
						$horariosDisponible['fin'][] = $finUno;
					}
				}
			}
		}
		return $horariosDisponible;
	}

	public function franjasDisponibles($jornadaInicio, $jornadaFin, $fechaInicio, $fechaFin, $dia, $tipo, $valor){
		$horariosDisponible = array();
		if($tipo == 'grupo'){
			$where = ' and pla_fic_id = '.$valor;
		}else if($tipo == 'instructor'){
			$where = ' and par_id_instructor = "'.$valor.'"';
		}
		
		$sql = '
			select 	pla_fic_det_id,par_id_instructor,pla_fic_det_hor_inicio,pla_fic_det_hor_fin
			from 	sep_planeacion_ficha_detalle
			where 	((pla_fic_det_hor_inicio < '.$jornadaInicio.' and (pla_fic_det_hor_fin > '.$jornadaInicio.' and pla_fic_det_hor_fin <= '.$jornadaFin.'))
				or		((pla_fic_det_hor_inicio >= '.$jornadaInicio.' and  pla_fic_det_hor_inicio < '.$jornadaFin.') and pla_fic_det_hor_fin > '.$jornadaFin.')
				or		(pla_fic_det_hor_inicio >= '.$jornadaInicio.' and pla_fic_det_hor_fin <= '.$jornadaFin.')
				or		(pla_fic_det_hor_inicio < '.$jornadaInicio.' and pla_fic_det_hor_fin > '.$jornadaFin.'))
			and 	((pla_fic_det_fec_inicio < "'.$fechaInicio.'" and (pla_fic_det_fec_fin > "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))
				or 	((pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_inicio < "'.$fechaFin.'") and pla_fic_det_fec_fin > "'.$fechaFin.'")
				or 	(pla_fic_det_fec_inicio < "'.$fechaInicio.'" and pla_fic_det_fec_fin > "'.$fechaFin.'")
				or 	(pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))
			and  pla_dia_id = '.$dia.' '.$where.'
			order 	by pla_fic_det_hor_inicio asc';
		$horarios = DB::select($sql);

		if(count($horarios)>0){
			// Cargamos las horas en un array
			$horas = array();
			$contadorCiclo = 1;
			foreach($horarios as $key => $val){
				$horas[$contadorCiclo] = $val->pla_fic_det_hor_inicio; $contadorCiclo++;
				$horas[$contadorCiclo] = $val->pla_fic_det_hor_fin; $contadorCiclo++;
			}

			// Identificamos las horas repetidas
			$horasRepetidas = array();
			foreach($horas as $key => $hor){
				if(isset($horas[$key+1]) and $hor == $horas[$key+1]){
					$horasRepetidas[] = $hor;
				}
			}
			
			// Validamos si existe la hora inicio de la jornada en nuestro arreglo
			$horasRevisadas = array();
			if($horas[1] <= $jornadaInicio){
				unset($horas[1]);
			}else{
				$horasRevisadas[] = $jornadaInicio;
			}

			// Agregamos los valores que no esten repetidos
			foreach($horas as $key => $hor){
				if(!in_array($hor,$horasRepetidas)){
					$horasRevisadas[] = $hor;
				}
			}
			
			// Validamos si existe la hora fin de la jornada en nuestro arreglo
			$contador = (count($horasRevisadas)-1);
			if($horasRevisadas[$contador] >= $jornadaFin){
				unset($horasRevisadas[$contador]);
			}else{
				$horasRevisadas[] = $jornadaFin;
			}

			// Cargamos los horarios disponible
			if(count($horasRevisadas)>0){
				foreach($horasRevisadas as $key => $val){
					if($key % 2 == 0){
						$horariosDisponible['inicio'][] = $val;
						continue;
					}
					$horariosDisponible['fin'][] = $val;
				}
			}
		}else{
			$horariosDisponible['inicio'][] = $jornadaInicio;
			$horariosDisponible['fin'][] = $jornadaFin;
		}
		
		return $horariosDisponible;
	}

	public function generarHorarioTecnicas($horario, $pla_fra_id, $jornada, $jornadaInicio, $jornadaFin, $pla_fic_id, $fic_numero, $dia){
		$errores = array();
		
		foreach($horario['trimestre'] as $key => $val){
			$key++;
			$fecInicio = $horario["trimestreFechaInicio"][$key];
			$fecFin = $horario["trimestreFechaFin"][$key];
			foreach($horario["programacion"][$key]["instructor_cedula"] as $key1 => $val1){
				$ambiente = $horario["programacion"][$key]["amb_id"][$key1];
				$instructorCedula = $horario["programacion"][$key]["instructor_cedula"][$key1];
				$instructorHoras = $horario["programacion"][$key]["instructor_horas"][$key1];
				$horasSumadas = 0;

				$instructorProgramado = false;
				$misAmbientes = array();
				$misAmbientes[] = $ambiente;
				$contadorAmbientes = count($misAmbientes);
				for($i=0; $i<$contadorAmbientes; $i++){
					if($instructorProgramado == true){ $instructorProgramado = false; break; }
					for($j=1; $j<7; $j++){
						if($j == 6 and $pla_fra_id == 3){ $jornadaInicio = 6; $jornadaFin = 18;}else if($pla_fra_id == 3){ $jornadaInicio = 18; $jornadaFin = 22; }

						$horariosLibresAmbientes = $this->validar($jornadaInicio, $jornadaFin, $jornada, $misAmbientes[$i], $fecInicio, $fecFin, $j, "ambiente", "trimestre");
						if($horariosLibresAmbientes != null){

							// Validar el grupo según el rango de fechas
							$horariosLibresGrupo = $this->validar($jornadaInicio, $jornadaFin, $jornada, $pla_fic_id, $fecInicio, $fecFin, $j, "grupo", "trimestre");
							if($horariosLibresGrupo != null){
								
								// Buscar los espacios libres entre ambiente y grupo
								$horariosLibresAmbGru = $this->validarLosDosArrays($horariosLibresGrupo, $horariosLibresAmbientes, $jornadaInicio, $jornadaFin, $jornada);
								if($horariosLibresAmbGru != null){

									// Validar el instructor según el rango de fechas
									$horariosLibresInstructor = $this->validar($jornadaInicio, $jornadaFin, $jornada, $val1, $fecInicio, $fecFin, $j, "instructor", "trimestre");
									if($horariosLibresInstructor != null){

										// Buscar los espacios libres entre los dos arrays
										$horariosLibresInsOtro = $this->validarLosDosArrays($horariosLibresAmbGru, $horariosLibresInstructor, $jornadaInicio, $jornadaFin, $jornada);
										if($horariosLibresInsOtro != null){

											if($instructorHoras > $horasSumadas){
												$horasInstructor = DB::select('select par_horas_semanales as horas from sep_participante where par_identificacion = "'.$val1.'" limit 1');
												$horasInstructor = $horasInstructor[0]->horas;
												
												foreach($horariosLibresInsOtro[$jornada]["inicio"] as $key2 => $val2){
													$sql = '
														select 	sum(pla_fic_det_hor_totales) as total
														from 	sep_planeacion_ficha_detalle
														where 	((pla_fic_det_fec_inicio < "'.$fecInicio.'" and (pla_fic_det_fec_fin > "'.$fecInicio.'" and pla_fic_det_fec_fin <= "'.$fecFin.'"))
														    or 	((pla_fic_det_fec_inicio >= "'.$fecInicio.'" and pla_fic_det_fec_inicio < "'.$fecFin.'") and pla_fic_det_fec_fin > "'.$fecFin.'")
															or 	(pla_fic_det_fec_inicio < "'.$fecInicio.'" and pla_fic_det_fec_fin > "'.$fecFin.'")
															or 	(pla_fic_det_fec_inicio >= "'.$fecInicio.'" and pla_fic_det_fec_fin <= "'.$fecFin.'"))
														and 	pla_tip_id != 5
														and 	par_id_instructor = "'.$val1.'"';
													$horas = DB::select($sql);
													$horas = $horas[0]->total;
													if(is_null($horas)){ $horas = 0; }

													if($horas >= $horasInstructor){
														$ambiente = DB::select('select pla_amb_descripcion from sep_planeacion_ambiente where pla_amb_id = '.$misAmbientes[$i].' limit 1');
														$instructor = DB::select('select concat(par_nombres," ",par_apellidos) as nombre from sep_participante where par_identificacion = "'.$val1.'" limit 1');
														$nombre = ucwords(mb_strtolower($instructor[0]->nombre));
														$ambiente = $ambiente[0]->pla_amb_descripcion;
														$errores["trimestre"][$key][] = 'Al instructor <strong>'.$nombre.'</strong>. Se le deben programar <strong>'.$instructorHoras.'</strong> horas, y se le programaron <strong>'.$horasSumadas.'</strong> horas porque supero el total de sus horas semanales '.$horas.'.';
														$terminarCicloDias = true;
														break;
													}else{
														$horaDeInicio = $val2;
														$horaDeFin = $horariosLibresInsOtro[$jornada]["fin"][$key2];

														$validarHoras = $horaDeFin - $horaDeInicio;
														if($validarHoras > $instructorHoras){
															$horaDeFin = $horaDeInicio+$instructorHoras;
														}

														$validarHoras = $horasSumadas + ($horaDeFin-$horaDeInicio);
														if($validarHoras > $instructorHoras){
															$horaDeFin = $horaDeInicio + ($instructorHoras - $horasSumadas);
														}
														
														$validarHoras = $horas + ($horaDeFin - $horaDeInicio);
														if($validarHoras > $horasInstructor){
														    $diferenciaHoras = $horasInstructor - $horas;
														    $horaDeFin = $horaDeInicio + $diferenciaHoras;
														}
														
														if($horaDeInicio >= $horaDeFin or $horaDeInicio > $jornadaFin or $horaDeFin > $jornadaFin or $horaDeInicio < $jornadaInicio or $horaDeFin < $jornadaInicio){ continue; }
														$totHoras = $horaDeFin - $horaDeInicio;
														$horasSumadas += $totHoras;

														$sql = '
															insert into	sep_planeacion_ficha_detalle (
																pla_fic_det_id,pla_fic_id,pla_fic_det_fec_inicio,pla_fic_det_fec_fin,
																pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_fic_det_hor_totales,par_id_instructor,
																pla_dia_id,pla_amb_id,pla_trimestre_numero_ficha,pla_trimestre_numero_year,pla_tip_id
															)values(
																default,'.$pla_fic_id.',"'.$fecInicio.'","'.$fecFin.'",
																"'.$horaDeInicio.'","'.$horaDeFin.'","'.$totHoras.'","'.$val1.'", 
																'.$j.',"'.$misAmbientes[$i].'",'.$key.','.$horario["trimestreYear"][$key-1].',2)';
														DB::insert($sql);
														/*
														echo "instructor: ".$val1."<br>";
														echo "Instructor horas semanales: ".$horasInstructor."<br>";
														echo "Instructor horas asignadas: ".$horas."<br>";
														echo "Instructor horas a programar: ".$instructorHoras."<br>";
														echo "Ambiente: ".$misAmbientes[$i]."<br>";
														echo "dia: ".$dia[$j]."<br>";
														echo "horaInicio: ".$horaDeInicio."<br>";
														echo "horaFin: ".$horaDeFin."<br>";
														//echo "totHoras: ".$totHoras."<br>";
														echo "horasSumadas: ".$horasSumadas."<br>";
														echo "<br>";*/
													}
												}
											}else{
												$instructorProgramado = true;
											}
										}else{
											if($j == 6){
											$instructor = DB::select('select concat(par_nombres," ",par_apellidos) as nombre from sep_participante where par_identificacion = "'.$val1.'" limit 1');
											$nombre = ucwords(mb_strtolower($instructor[0]->nombre));
											$errores["trimestre"][$key][] = 'Los horarios disponibles del instructor <strong>'.$nombre.'</strong>, no corresponden con los horarios asignados al grupo.';
											}
										}
									}else{
										if($j == 6 and $instructorHoras != $horasSumadas){
											$instructor = DB::select('select concat(par_nombres," ",par_apellidos) as nombre from sep_participante where par_identificacion = "'.$val1.'" limit 1');
											$nombre = ucwords(mb_strtolower($instructor[0]->nombre));
											$errores["trimestre"][$key][] = 'El instructor <strong>'.$nombre.'</strong> no tiene disponibilidad ningun día de la semana en esta franja horaria.';
										}
									}
								}else{
									$ambiente = DB::select('select pla_amb_descripcion from sep_planeacion_ambiente where pla_amb_id = '.$misAmbientes[$i].' limit 1');
									$ambiente = $ambiente[0]->pla_amb_descripcion;
									$errores["trimestre"][$key][] = 'El ambiente <strong>'.$ambiente.'</strong> o el grupo <strong>'.$fic_numero.'</strong> no se encuentra disponible el día <strong>'.$dia[$j].'</strong>.';
								}
							}
						}else{
							if($j == 6 and $instructorHoras != $horasSumadas){
								$ambiente = DB::select('select pla_amb_descripcion from sep_planeacion_ambiente where pla_amb_id = '.$misAmbientes[$i].' limit 1');
								$instructor = DB::select('select concat(par_nombres," ",par_apellidos) as nombre from sep_participante where par_identificacion = "'.$val1.'" limit 1');
								$nombre = ucwords(mb_strtolower($instructor[0]->nombre));
								$ambiente = $ambiente[0]->pla_amb_descripcion;
								$errores["trimestre"][$key][] = 'El ambiente <strong>'.$ambiente.'</strong> no está disponible para el instructor <strong>'.$nombre.'</strong>';
							}
						}

						if($j == 6 and $instructorHoras != $horasSumadas){
							$instructor = DB::select('select concat(par_nombres," ",par_apellidos) as nombre from sep_participante where par_identificacion = "'.$val1.'" limit 1');
							$nombre = ucwords(mb_strtolower($instructor[0]->nombre));
							$errores["trimestre"][$key][] = 'Al instructor <strong>'.$nombre.'</strong> se le deben programar <strong>'.$instructorHoras.'</strong> horas y se le programaron <strong>'.($instructorHoras + ($horasSumadas)).'</strong> horas';
    					}
					}
				}
			}

			/*DB::beginTransaction();
			foreach($horario['programacion'][$key]['competencia'] as $key1 => $val1){
				$competencia = ucfirst(mb_strtolower($val1, 'UTF-8'));
				$resultado = ucfirst(mb_strtolower($horario["programacion"][$key]["resultado"][$key1], 'UTF-8'));
				$actividad = ucfirst(mb_strtolower($horario["programacion"][$key]["actividad"][$key1], 'UTF-8'));
				$act_horas = $horario["programacion"][$key]["act_horas"][$key1];
				$instructor = $horario["programacion"][$key]["instructor"][$key1];
				$fase = $horario["programacion"][$key]["fas_id"][$key1];
				
				$resultado = str_replace(array("","'",'"','\\','/'), " ", mb_convert_encoding($resultado, 'HTML-ENTITIES', 'UTF-8'));
				$actividad = str_replace(array("","'",'"','\\','/'), " ", mb_convert_encoding($actividad, 'HTML-ENTITIES', 'UTF-8'));
				$competencia = str_replace(array("","'",'"','\\','/'), " ", mb_convert_encoding($competencia, 'HTML-ENTITIES', 'UTF-8'));
				
				$resultado = str_replace(array("","'",'"')," ",$resultado);
				$actividad = str_replace(array("","'",'"')," ",$actividad);
				$competencia = str_replace(array("","'",'"')," ",$competencia);

				$sql = 'insert into sep_planeacion_ficha_actividades (
							pla_fic_act_id,pla_fic_act_competencia,pla_fic_act_resultado,pla_fic_act_actividad,
							pla_fic_act_horas,pla_fic_id,par_id_instructor,pla_tip_id,pla_trimestre_numero,fas_id
						) values (default,"'.$val1.'","'.$resultado.'","'.$actividad.'",
							"'.$act_horas.'",'.$pla_fic_id.',"'.$instructor.'",2,'.$key.','.$fase.')';
				DB::insert($sql);
			}
			DB::commit();*/
		}
		
		$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';',':','--', '?');
		foreach($horario['programacion'] as $key => $val){
			foreach($horario['programacion'][$key]['competencia'] as $key1 => $val1){
				$competencia = $val1;
				$resultado = $horario["programacion"][$key]["resultado"][$key1];
				$actividad = $horario["programacion"][$key]["actividad"][$key1];
				$act_horas = $horario["programacion"][$key]["act_horas"][$key1];
				$instructor = $horario["programacion"][$key]["instructor"][$key1];
				$fase = $horario["programacion"][$key]["fas_id"][$key1];
				$resultado = str_replace($caractereNoPremitidos, '', ucfirst(strtolower(mb_convert_encoding($resultado,'HTML-ENTITIES', 'UTF-8'))));
				$actividad = str_replace($caractereNoPremitidos, '', ucfirst(strtolower(mb_convert_encoding($actividad,'HTML-ENTITIES', 'UTF-8'))));
				$competencia = str_replace($caractereNoPremitidos, '', ucfirst(strtolower(mb_convert_encoding($competencia,'HTML-ENTITIES', 'UTF-8'))));

				$sql = 'insert into sep_planeacion_ficha_actividades (
							pla_fic_act_id,pla_fic_act_competencia,pla_fic_act_resultado,pla_fic_act_actividad,
							pla_fic_act_horas,pla_fic_id,par_id_instructor,pla_tip_id,pla_trimestre_numero,fas_id
						) values (default,"'.$competencia.'","'.$resultado.'","'.$actividad.'",
							"'.$act_horas.'",'.$pla_fic_id.',"'.$instructor.'",2,'.$key.','.$fase.')';
				DB::insert($sql);
			}
		}
		
		if(count($errores)>0){
			$this->registrarErrores($pla_fic_id,$errores);
		}
	}
	
    public function generarHorarioTransversales($disenoCurricularTipo, $trimestreFicha,$trimestreYear,$cantidadTrimestres, $fechasInicio,$fechasFin,$nivel_formacion,$jornada,$pla_fic_id,$fic_numero,$dia){
		$errores = array();

		// Consultamos las actividades para las transversales del programa
		$sql = '
			select 	tip.tra_tip_id,tra_tip_descripcion,tra_com_descripcion, tra_res_descripcion, tra_act_descripcion, niv_for_id,tra_act_horas
			from 	sep_transversal_nivel_formacion niv_for, sep_transversal_tipo tip, sep_transversal_actividad act
			where 	niv_for.tra_tip_id = tip.tra_tip_id and dc_tipo = "'.$disenoCurricularTipo.'"
			and 	niv_for.tra_tip_id = act.tra_tip_id and niv_for.niv_for_id = '.$nivel_formacion;
		$DB_transversales = DB::select($sql);
		$paraActuaizarTransversal = array();
		foreach($DB_transversales as $key => $val){
			$competencia = ucfirst(mb_strtolower($val->tra_com_descripcion, 'UTF-8'));
			$resultado = ucfirst(mb_strtolower($val->tra_res_descripcion, 'UTF-8'));
			$actividad = '('.$val->tra_tip_descripcion.') - '.ucfirst(mb_strtolower($val->tra_act_descripcion, 'UTF-8'));
			$act_horas = $val->tra_act_horas;

			$sql = '
				insert into sep_planeacion_ficha_actividades (
					pla_fic_act_id,pla_fic_act_competencia,pla_fic_act_resultado,pla_fic_act_actividad,
					pla_fic_act_horas,pla_fic_id,par_id_instructor,pla_tip_id,pla_trimestre_numero,fas_id
				) values (
					default,"'.$competencia.'","'.$resultado.'","'.$actividad.'",
					"'.$act_horas.'",'.$pla_fic_id.',"1111111111",7,0,5)';
			DB::insert($sql);

			$paraActuaizarTransversal[$val->tra_tip_id]['id'][] = DB::getPdo()->lastInsertId();
		}

		// Horarios dependiendo la jornada
		if($jornada == "Mañana"){
			$transversalJornada = "Tarde"; $transversalJornadaInicio = 12; $transversalJornadaFin = 18;
		}else if($jornada == "Tarde"){
			$transversalJornada = "Mañana"; $transversalJornadaInicio = 6; $transversalJornadaFin = 12;
		}else{
			$transversalJornada = "Noche"; $transversalJornadaInicio = 18; $transversalJornadaFin = 22;
		}

		// Transversales del programa de formación
		$sql = '
			select 	hor.tra_tip_id, numero_trimestre_inicio, tra_hor_can_hora
			from 	sep_transversal_nivel_formacion niv_for, sep_transversal_hora hor
			where 	niv_for.tra_tip_id = hor.tra_tip_id
			and 	niv_for.niv_for_id = '.$nivel_formacion.' and niv_for.dc_tipo = "'.$disenoCurricularTipo.'"
			and		hor.niv_for_id = '.$nivel_formacion.' and hor.dc_tipo = "'.$disenoCurricularTipo.'" order by tra_tip_id';
		$transversales_nivel_formacion = DB::select($sql);
		if(count($transversales_nivel_formacion) == 0){
			return true;
		}

		foreach($transversales_nivel_formacion as $key => $val){
			$tra_trimestre[$val->tra_tip_id][] = $val->numero_trimestre_inicio;
			$tra_hora[$val->tra_tip_id] = $val->tra_hor_can_hora;
		}
		$concatenar = 'and tra_tip_id in('.implode(',', array_keys($tra_trimestre)).')';
		
		foreach($tra_trimestre as $key => $val){
			foreach($val as $key1 => $val1){
				for($i=$val1; $i<=$cantidadTrimestres; $i++){
					if(!in_array($i, $tra_trimestre[$key])){
						$tra_trimestre[$key][] = $i;
					}
					$tra_year[$key][] = $trimestreYear[$i-1];
				}
			}
		}

		// Consultar instructores para impartir cada transversal
		$sql = 'select 	tra_tip_id,par.par_identificacion,par.par_nombres,par.par_apellidos,tra_ins_prioridad
				from 	sep_transversal_instructor tra_ins, sep_participante par, users user
				where 	tra_ins.par_id_instructor = par.par_identificacion '.$concatenar.'
				and 	par.par_identificacion = user.par_identificacion
				and 	user.estado = "1" and par.rol_id = 2
				order 	by tra_tip_id, tra_ins_prioridad';
		$instructores = DB::select($sql);

		if(count($instructores) == 0){
			return false;
		}

		$tra_instructores = array();
		foreach($instructores as $key => $val){
			$tra_instructores[$val->tra_tip_id][] = $val->par_identificacion;
		}

		// Actualiza prioridad del Instructor
		foreach($tra_instructores as $key => $val){
			$contador_instructor = count($tra_instructores[$key]);
			if($contador_instructor > 1){
				$contador_instructor--;
				$contador = 2;
				for($i=0; $i<$contador_instructor; $i++){
					$sql = 'update sep_transversal_instructor set tra_ins_prioridad = '.$contador.' where par_id_instructor = '.$tra_instructores[$key][$i];
					DB::update($sql);
					$contador++;
				}
				$sql = 'update sep_transversal_instructor set tra_ins_prioridad = 1 where par_id_instructor = '.$tra_instructores[$key][$i];
				DB::update($sql);
			}
		}

		// Consultar ambientes para impartir cada transversal
		$sql = 'select 	pla_amb.pla_amb_id, pla_amb_descripcion, tra_tip_id
				from 	sep_transversal_ambiente tra_amb, sep_planeacion_ambiente pla_amb
				where 	tra_amb.pla_amb_id = pla_amb.pla_amb_id '.$concatenar.'
				and 	pla_amb_estado = "Activo"
				order 	by tra_tip_id';
		$ambientes = DB::select($sql);
		if(count($ambientes)==0){
			return false;
		}

		$tra_ambiente = array();
		foreach($ambientes as $key => $val){
			$tra_ambiente[$val->tra_tip_id][] = $val->pla_amb_id;
		}

		// Cargamos el array con la información que se necesita para iniciar la validación y programar
		$transversal = array();
		foreach($tra_trimestre as $key => $val){
			if(isset($tra_trimestre[$key]) and isset($tra_hora[$key]) and isset($tra_instructores[$key]) and isset($tra_ambiente[$key]) and isset($tra_year[$key])){
				$transversal[$key]["trimestres"] = $tra_trimestre[$key];
				$transversal[$key]["trimestresYear"] = $tra_year[$key];
				$transversal[$key]["instructor"] = $tra_instructores[$key];
				$transversal[$key]["ambiente"] = $tra_ambiente[$key];
				$transversal[$key]["hora"] = $tra_hora[$key];
			}
		}

		if(count($transversal)==0){
			return false;
		}

		/*echo '<pre>';
		print_r($tra_trimestre);
		print_r($tra_year);
		print_r($tra_instructores);
		print_r($tra_ambiente);
		dd($transversal);*/

		foreach($transversal as $key => $val){
			$termineProgramarActividad = false;
			$yaResgistreActividades = false;
			$horas_transversal = $transversal[$key]['hora'];
			foreach($transversal[$key]["trimestres"] as $key1 => $val1){
				$numero_trimestre = $val1;
				$contador_instructor = count($transversal[$key]["instructor"]);
				$fecInicio = $fechasInicio[$val1];
				$fecFin = $fechasFin[$val1];
				foreach($transversal[$key]["instructor"] as $key2 => $val2){
					$horasSumadas = 0;
					$instructorHoras = $horas_transversal;
					$contador_ambiente = count($transversal[$key]["ambiente"]);
					$instructor_no_disponible = false;
					$instructorProgramado = false;
					foreach($transversal[$key]["ambiente"] as $key3 => $val3){
						if($instructorProgramado == true){ $instructorProgramado = false; break; }
						for($j=1; $j<7; $j++){
							$horariosLibresAmbientes = $this->validar($transversalJornadaInicio, $transversalJornadaFin, $transversalJornada, $val3, $fecInicio, $fecFin, $j, "ambiente", "trimestre");
							if($horariosLibresAmbientes != null){
								
								// Validar el grupo según el rango de fechas
								$horariosLibresGrupo = $this->validar($transversalJornadaInicio, $transversalJornadaFin, $transversalJornada, $pla_fic_id, $fecInicio, $fecFin, $j, "grupo", "trimestre");
								if($horariosLibresGrupo != null){
									
									// Buscar los espacios libres entre ambiente y grupo
									$horariosLibresAmbGru = $this->validarLosDosArrays($horariosLibresGrupo, $horariosLibresAmbientes, $transversalJornadaInicio, $transversalJornadaFin, $transversalJornada);
									if($horariosLibresAmbGru != null){
										
										// Validar el instructor según el rango de fechas
										$horariosLibresInstructor = $this->validar($transversalJornadaInicio, $transversalJornadaFin, $transversalJornada, $val2, $fecInicio, $fecFin, $j, "instructor", "trimestre");
										if($horariosLibresInstructor != null){

											// Buscar los espacios libres entre los dos arrays
											$horariosLibresInsOtro = $this->validarLosDosArrays($horariosLibresAmbGru, $horariosLibresInstructor, $transversalJornadaInicio, $transversalJornadaFin, $transversalJornada);
											if($horariosLibresInsOtro != null){
												//echo "select par_horas_semanales as horas from sep_participante where par_identificacion = '$val2'";
												if($instructorHoras > $horasSumadas){
													$horasInstructor = DB::select('select par_horas_semanales as horas from sep_participante where par_identificacion = "'.$val2.'" limit 1');
													$horasInstructor = $horasInstructor[0]->horas;
													
													foreach($horariosLibresInsOtro[$transversalJornada]["inicio"] as $key4 => $val4){
														$sql = '
															select 	sum(pla_fic_det_hor_totales) as total
															from 		sep_planeacion_ficha_detalle
															where 	((pla_fic_det_fec_inicio < "'.$fecInicio.'" and (pla_fic_det_fec_fin > "'.$fecInicio.'" and pla_fic_det_fec_fin <= "'.$fecFin.'"))
																or 		((pla_fic_det_fec_inicio >= "'.$fecInicio.'" and pla_fic_det_fec_inicio < "'.$fecFin.'") and pla_fic_det_fec_fin > "'.$fecFin.'")
																or 		(pla_fic_det_fec_inicio < "'.$fecInicio.'" and pla_fic_det_fec_fin > "'.$fecFin.'")
																or 		(pla_fic_det_fec_inicio >= "'.$fecInicio.'" and pla_fic_det_fec_fin <= "'.$fecFin.'"))
															and	 		not	pla_tip_id = 5
															and 		par_id_instructor = "'.$val2.'"';
														$horas = DB::select($sql);
														$horas = $horas[0]->total;
														if(is_null($horas)){ $horas = 0; }

														if($horas >= $horasInstructor){
															$ambiente = DB::select('select pla_amb_descripcion from sep_planeacion_ambiente where pla_amb_id = '.$val3.' limit 1');
															$instructor = DB::select('select concat(par_nombres," ",par_apellidos) as nombre from sep_participante where par_identificacion = "'.$val2.'" limit 1');
															$nombre = ucwords(mb_strtolower($instructor[0]->nombre));
															$ambiente = $ambiente[0]->pla_amb_descripcion;
															$errores["trimestre"][$key][] = 'Al instructor <strong>'.$nombre.'</strong>. Se le deben programar <strong>'.$instructorHoras.'</strong> horas, y se le programaron <strong>'.($instructorHoras + ($horasSumadas)).'</strong> horas, porque supero el total de sus horas semanales <strong>'.$horas.'</strong>.';
															$terminarCicloDias = true;
															break;
														}else{
															$horaDeInicio = $val4;
															$horaDeFin = $horariosLibresInsOtro[$transversalJornada]["fin"][$key4];

															$validarHoras = $horaDeFin - $horaDeInicio;
															if($validarHoras > $instructorHoras){
																$horaDeFin = $horaDeInicio+$instructorHoras;
															}

															$validarHoras = $horasSumadas + ($horaDeFin-$horaDeInicio);
															if($validarHoras > $instructorHoras){
																$horaDeFin = $horaDeInicio + ($instructorHoras - $horasSumadas);
															}
															
															$validarHoras = $horas + ($horaDeFin - $horaDeInicio);
    														if($validarHoras > $horasInstructor){
    														    $diferenciaHoras = $horasInstructor - $horas;
    														    $horaDeFin = $horaDeInicio + $diferenciaHoras;
    														}

															if($horaDeInicio >= $horaDeFin or $horaDeInicio > $transversalJornadaFin or $horaDeFin > $transversalJornadaFin or $horaDeInicio < $transversalJornadaInicio or $horaDeFin < $transversalJornadaInicio){ continue; }

															$totHoras = $horaDeFin - $horaDeInicio;
															$horasSumadas += $totHoras;

															$sql = '
																insert into	sep_planeacion_ficha_detalle (
																	pla_fic_det_id,pla_fic_id,pla_fic_det_fec_inicio,pla_fic_det_fec_fin,
																	pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_fic_det_hor_totales,par_id_instructor,
																	pla_dia_id,pla_amb_id,pla_trimestre_numero_ficha,pla_trimestre_numero_year,pla_tip_id
																) values (
																	default,'.$pla_fic_id.',"'.$fecInicio.'","'.$fecFin.'",
																	"'.$horaDeInicio.'","'.$horaDeFin.'","'.$totHoras.'","'.$val2.'", 
																	'.$j.',"'.$val3.'",'.$val1.','.$transversal[$key]["trimestresYear"][$key1].',2)';
															DB::insert($sql);
															/*if($yaResgistreActividades == false){
																foreach($paraActuaizarTransversal[$key]["id"] as $traValor){
																	$sql = '
																		update sep_planeacion_ficha_actividades
																		set 	par_id_instructor = "'.$val2.'", pla_trimestre_numero = "'.$val1.'"
																		where 	pla_fic_act_id = '.$traValor;
																	DB::update($sql);
																}
																$yaResgistreActividades = true;
															}*/
														}
														if($horasSumadas == $instructorHoras){
															$termineProgramarActividad = true;
															break;
														}
													}
													if($termineProgramarActividad == true){
														break;
													}
												}else{
													$instructorProgramado = true;
													break;
												}
											}else{
											    if($j == 6 and $contador_instructor == ($key2+1)){
    												$errores["trimestre"][$val1][] = 'Los horarios disponibles de los instructores, no corresponden con los horarios asignados al grupo.';
											    }
											}
										}else{
											if($j == 6 and $instructorHoras != $horasSumadas){
												$instructor = DB::select('select concat(par_nombres," ",par_apellidos) as nombre from sep_participante where par_identificacion = "'.$val2.'" limit 1');
												$nombre = ucwords(mb_strtolower($instructor[0]->nombre));
												$errores["trimestre"][$val1][] = 'El instructor <strong>'.$nombre.'</strong> no tiene disponibilidad ningun día de la semana en esta franja horaria.';
												$instructor_no_disponible = true;
												break;
											}
										}
									}else{
										$ambiente = DB::select('select pla_amb_descripcion from sep_planeacion_ambiente where pla_amb_id = '.$val3.' limit 1');
										$ambiente = $ambiente[0]->pla_amb_descripcion;
										$errores["trimestre"][$val1][] = 'El ambiente <strong>'.$ambiente.'</strong> o el grupo <strong>'.$fic_numero.'</strong> no se encuentra disponible el día <strong>'.$dia[$j].'</strong>.';
									}
								}
							}else{
								if($j == 6 and $instructorHoras != $horasSumadas and $contador_ambiente == $key3){
									$ambiente = DB::select('select pla_amb_descripcion from sep_planeacion_ambiente where pla_amb_id = '.$val3.' limit 1');
									$instructor = DB::select('select concat(par_nombres," ",par_apellidos) as nombre from sep_participante where par_identificacion = "'.$val2.'" limit 1');
									$nombre = ucwords(mb_strtolower($instructor[0]->nombre));
									$ambiente = $ambiente[0]->pla_amb_descripcion;
									$errores["trimestre"][$val1][] = 'El ambiente <strong>'.$ambiente.'</strong> no está disponible para el instructor <strong>'.$nombre.'</strong> se le deben programar <strong>'.$instructorHoras.'</strong> horas y se le programaron <strong>'.$horasSumadas.'</strong> horas.';
								}
							}

							if($j == 6 and $instructorHoras != $horasSumadas and $contador_ambiente == $key3){
								$instructor = DB::select('select concat(par_nombres," ",par_apellidos) as nombre from sep_participante where par_identificacion = "'.$val2.'" limit 1');
								$nombre = ucwords(mb_strtolower($instructor[0]->nombre));
								$errores["trimestre"][$val1][] = 'Al instructor <strong>'.$nombre.'</strong> se le deben programar <strong>'.$instructorHoras.'</strong> horas y se le programaron <strong>'.$horasSumadas.'</strong> horas';
							}
						}
						if($instructor_no_disponible == true){ break; }
						if($termineProgramarActividad == true){ break; }
					}
					if($termineProgramarActividad == true){ break; }
				}
				if($termineProgramarActividad == true){ break; }
			}
		}

		if(count($errores)>0){
			$this->registrarErrores($pla_fic_id,$errores);
		}
	}
	
	public function registrarErrores($pla_fic_id,$errores){
		DB::beginTransaction();
		foreach($errores["trimestre"] as $key => $val){
			foreach($errores["trimestre"][$key] as $val1){
				$sql = '
					insert into sep_planeacion_ficha_error (pla_fic_err_id, pla_fic_id, pla_fic_det_numero_trimestre, pla_fic_err_mensaje, pla_tip_id)
					values (default,'.$pla_fic_id.','.$key.',"'.$val1.'",2)';
				DB::insert($sql);
			}
		}
		DB::commit();
	}
	
	public function getIndex(){
		$_GET = $this->seguridad($_GET);
		extract($_GET);
		
		//validamos la modalidad seleccionada
		$campo="";
		if (isset($modalidad)) {
			if (is_numeric($modalidad) && $modalidad == 1 or $modalidad == 2) {
				$campo = " and fic.fic_modalidad = ".$modalidad;			
			}else{
				$modalidad="";
			}
		}else{
			$modalidad="";
		}

		$rol = \Auth::user()->participante->rol_id;
		if(isset($pla_fec_tri_id)){
			// Selecciona una fecha especifica ?
			$concatenar_programacion = '';
			$concatenar_horario = '';
			$concatenar_horario_detalle = '';
			$concatenar_horario_trimestres = '';
			if($pla_fec_tri_id != 'todos' and is_numeric($pla_fec_tri_id)){
				$sql = 'select * from sep_planeacion_fecha_trimestre where pla_fec_tri_id = '.$pla_fec_tri_id.' limit 1';
				$fecha = DB::select($sql);
				$fechaInicio = $fecha[0]->pla_fec_tri_fec_inicio;
				$fechaFin = $fecha[0]->pla_fec_tri_fec_fin;
				$concatenar_horario = '
					and ((pla_fic_fec_ini_induccion >= "'.$fechaInicio.'" and pla_fic_fec_ini_induccion <= "'.$fechaFin.'") or
					(fecha_fin_productiva >= "'.$fechaInicio.'" and fecha_fin_productiva <= "'.$fechaFin.'") or
					(pla_fic_fec_ini_induccion < "'.$fechaInicio.'" and fecha_fin_productiva > "'.$fechaFin.'"))';

				$concatenar_horario_detalle = '
					and ((pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_inicio <= "'.$fechaFin.'") or
					(pla_fic_det_fec_fin >= "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'") or
					(pla_fic_det_fec_inicio < "'.$fechaInicio.'" and pla_fic_det_fec_fin > "'.$fechaFin.'"))';

				$concatenar_horario_trimestres = '
					and ((fecha_inicio >= "'.$fechaInicio.'" and fecha_inicio <= "'.$fechaFin.'") or
					(fecha_fin >= "'.$fechaInicio.'" and fecha_fin <= "'.$fechaFin.'") or
					(fecha_inicio < "'.$fechaInicio.'" and fecha_fin > "'.$fechaFin.'"))';
			}

			// Consulta al Coordinador?
			if(isset($par_identificacion_coordinador)){
				if(is_numeric($par_identificacion_coordinador)){
					$concatenar_asignar = ' and fic.par_identificacion_coordinador = "'.$par_identificacion_coordinador.'"';
					$concatenar_horario .= $concatenar_asignar;
					$concatenar_horario_detalle .= $concatenar_asignar;
					$concatenar_horario_trimestres .= $concatenar_asignar;
				}else if($par_identificacion_coordinador == ''){
					$concatenar_asignar = ' and p_f.pla_fic_id in ('.implode(',',$pla_fic_id).')';
					$concatenar_horario .= $concatenar_asignar;
					$concatenar_horario_detalle .= $concatenar_asignar;
					$concatenar_horario_trimestres .= $concatenar_asignar;
				}

			}else{
				$concatenar_asignar = ' and p_f.pla_fic_id in ('.implode(',',$pla_fic_id).')';
				$concatenar_horario .= $concatenar_asignar;
				$concatenar_horario_detalle .= $concatenar_asignar;
				$concatenar_horario_trimestres .= $concatenar_asignar;
			}

			// Reducir nombre de los programas para generar el PDF
			$concatenarGenerar = 'prog_nombre, prog_sigla,';
			if(isset($generar)){
				$concatenarGenerar = 'substring(prog_nombre, 1,38) AS prog_nombre, prog_sigla,';
			}

			// Horario
			$sql = '
				select 	pla_fic_id, p_f.fic_numero, pla_tip_ofe_descripcion, pla_fec_tri_fin, if(fic.fic_modalidad = 1,"Presencial","Virtual") as Modalidad,
						pla_fic_can_trimestre, pla_ins_lider, pla_fic_fec_creacion,
						pla_fic_fec_ini_induccion, pla_fic_fec_fin_induccion,
						pla_fic_fec_ini_lectiva, pla_fic_fec_fin_lectiva, pla_fic_can_trimestre_productiva,
						pla_fra_descripcion, p_fra.pla_fra_id, '.$concatenarGenerar.'
						substring_index(par.par_nombres," ",1) as par_nombres, p_t_o.pla_tip_ofe_id,
						substring_index(par.par_apellidos," ",1) as par_apellidos, niv.niv_for_id,
						substring_index(niv_for_nombre," ",1) as niv_for_nombre, pla_fic_consecutivo_ficha
				from 	sep_planeacion_ficha p_f, sep_planeacion_tipo_oferta p_t_o, sep_participante par,
						sep_planeacion_franja p_fra, sep_programa pro, sep_ficha fic, sep_nivel_formacion niv
				where 	p_f.pla_tip_ofe_id = p_t_o.pla_tip_ofe_id
				and  	p_f.pla_fra_id = p_fra.pla_fra_id
				and 	p_f.fic_numero = fic.fic_numero '.$campo.'
				and  	fic.prog_codigo = pro.prog_codigo
				and 	pro.niv_for_id = niv.niv_for_id
				and  	p_f.pla_fic_usu_creador = par.par_identificacion
				and  	not p_f.pla_fic_id in (0,1) '.$concatenar_horario.'
				order 	by prog_nombre, pla_fic_id, fic.prog_codigo asc';
			$horarios = DB::select($sql);

			// Horario detalle
			$sql ='
				select 	p_f.pla_fic_id, pla_fic_det_id, fic.fic_numero, par_nombres, par_apellidos,
						pla_trimestre_numero_ficha, par.par_identificacion,
						substring_index(par_nombres," ",1) as nombre,
						substring_index(par_apellidos," ",1) as apellido,
						pla_dia_id, par.par_identificacion, pla_fic_det_hor_inicio,
						pla_fic_det_hor_fin, pla_fic_det_hor_totales, pla_amb_descripcion,
						amb.pla_amb_id, pla_fic_det_fec_inicio, pla_fic_det_fec_fin
				from 	sep_planeacion_ficha p_f, sep_planeacion_ficha_detalle p_f_d,
						sep_participante par, sep_planeacion_ambiente amb, sep_ficha fic
				where 	p_f.pla_fic_id = p_f_d.pla_fic_id
				and 	p_f.fic_numero = fic.fic_numero '.$campo.'
				and  	p_f_d.par_id_instructor = par.par_identificacion
				and 	p_f_d.pla_amb_id = amb.pla_amb_id
				and  	not pla_tip_id = 5 '.$concatenar_horario_detalle.'
				order 	by p_f.pla_fic_id, pla_trimestre_numero_ficha, pla_fic_det_fec_inicio, pla_fic_det_hor_inicio, pla_dia_id';
			$horarios_detalle = DB::select($sql);
			
			// Errores en la programación de los horarios
			$sql = '
				select 	pla_fic_id, pla_fic_det_numero_trimestre, pla_fic_err_mensaje
				from 	sep_planeacion_ficha_error
				order by fas_id, pla_fic_err_id asc';
			$errores = DB::select($sql);
			$arrayErrores = array();
			if(!empty($errores)){
				foreach($errores as $err){
					$arrayErrores[$err->pla_fic_id][$err->pla_fic_det_numero_trimestre][] = $err->pla_fic_err_mensaje;
				}
			}

			if(!empty($horarios)){
				$programacionDetalle = array();
				$llave_inicio = 0;
				$fechas_programadas = array();
				foreach($horarios_detalle as $key => $val){
					$fecha_inicio_val = $val->pla_fic_det_fec_inicio;
					$fecha_fin_val = $val->pla_fic_det_fec_fin;
					if(isset($programacion[$val->pla_fic_id][$val->pla_trimestre_numero_ficha])){
						if(!in_array($fecha_inicio_val, $programacion[$val->pla_fic_id][$val->pla_trimestre_numero_ficha]["fechas_inicio"])){
							$llave_inicio++;
						}
					}
					
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero_ficha]["fechas_inicio"][$llave_inicio] = $fecha_inicio_val;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero_ficha]["fechas_fin"][$llave_inicio] = $fecha_fin_val;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero_ficha]["hora_inicio"][$llave_inicio][] = $val->pla_fic_det_hor_inicio;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero_ficha]["hora_fin"][$llave_inicio][] = $val->pla_fic_det_hor_fin;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero_ficha]["horas_totales"][$llave_inicio][] = $val->pla_fic_det_hor_totales;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero_ficha]["dia_id"][$llave_inicio][] = $val->pla_dia_id;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero_ficha]["amb_id"][$llave_inicio][] = $val->pla_amb_id;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero_ficha]["pla_amb_descripcion"][$llave_inicio][] = $val->pla_amb_descripcion;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero_ficha]["instructor_cedula"][$llave_inicio][] = $val->par_identificacion;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero_ficha]["instructor_nombre"][$llave_inicio][] = $val->nombre." ".$val->apellido;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero_ficha]["nombre_largo"][$llave_inicio][] = $val->par_nombres." ".$val->par_apellidos;
					$programacion[$val->pla_fic_id][$val->pla_trimestre_numero_ficha]["pla_fic_det_id"][$llave_inicio][] = $val->pla_fic_det_id;
					$programacionDetalle[$val->pla_fic_id][$val->pla_trimestre_numero_ficha][$fecha_inicio_val][] = $val->pla_fic_det_id;
				}

				$sql = '
					select  p_t.pla_fic_id, trimestre_numero, fecha_inicio, fecha_fin
					from 	sep_planeacion_ficha_trimestre p_t, sep_planeacion_ficha p_f,
							sep_ficha fic
					where  	p_f.fic_numero = fic.fic_numero
					and		p_t.pla_fic_id = p_f.pla_fic_id '.$concatenar_horario_trimestres.'
					order 	by fecha_inicio asc';
				$pintar_trimestres = DB::select($sql);

				foreach($pintar_trimestres as $val){
					if(!isset($programacion[$val->pla_fic_id][$val->trimestre_numero])){
						$programacion[$val->pla_fic_id][$val->trimestre_numero]["fechas_inicio"][] = $val->fecha_inicio;
						$programacion[$val->pla_fic_id][$val->trimestre_numero]["fechas_fin"][] = $val->fecha_fin;
					}else{
						if(!in_array($val->fecha_fin, $programacion[$val->pla_fic_id][$val->trimestre_numero]["fechas_fin"])){
							$programacion[$val->pla_fic_id][$val->trimestre_numero]["fechas_inicio"][] = $val->fecha_inicio;
							$programacion[$val->pla_fic_id][$val->trimestre_numero]["fechas_fin"][] = $val->fecha_fin;
						}
					}
				}
				
				$actividades = array_keys($programacion);
				if(count($actividades)>0){
					$id_actividades = ' pla_fic_id in ('.implode(',', $actividades).')';
					$sql = '
						select 	pla_fic_id, fecha_inicio, par_id_instructor
						from 	sep_planeacion_ficha_actividades
						where	'.$id_actividades.'
						and 	par_id_instructor is not null
						group by pla_fic_id, fecha_inicio, par_id_instructor
						order by pla_fic_id, fecha_inicio';
					$actividades = DB::select($sql);
					$actividades_programadas = array();
					foreach($actividades as $val){
						$actividades_programadas[$val->pla_fic_id][$val->fecha_inicio][$val->par_id_instructor] = '';
					}
				}
	
				//echo '<pre>'; echo $sql; print_r($programacion); dd($programacion);
			}
		}else{
			// Trimestre actual en el anio
				$fecha_actual = date('Y-m-d');
				$sql = '
					select 	pla_fec_tri_id
					from 	sep_planeacion_fecha_trimestre
					where	pla_fec_tri_fec_fin >= "'.$fecha_actual.'"
					order by pla_fec_tri_id asc limit 1';
				$trimestre_actual = DB::select($sql);
				$tri_id = $trimestre_actual[0]->pla_fec_tri_id;
			$pla_fec_tri_id = $tri_id;
		}

		/*echo '<pre>';
		print_r($programacion);
		dd();*/

		$cc = \Auth::user()->participante->par_identificacion;


		$sql = '
			select 	pla_fic_id, pla_fic.fic_numero, prog_nombre, pla_fra_descripcion
			from 	sep_planeacion_ficha pla_fic, sep_ficha fic, sep_programa pro, sep_planeacion_franja fra
			where 	pla_fic.fic_numero = fic.fic_numero
			and  	fic.prog_codigo = pro.prog_codigo '.$campo.'
            and 	fra.pla_fra_id = pla_fic.pla_fra_id
			and  	not fic.fic_numero in("Restriccion","Complementario")
			order 	by prog_nombre, pla_fic_id desc';
		$fichas = DB::select($sql);

		$sql = '
			select 	par_identificacion, par_nombres, par_apellidos
			from 	sep_participante where rol_id = 3 order by par_nombres';
		$coordinadores = DB::select($sql);

		$sql = '
			select 	par.par_identificacion, par_nombres,par_apellidos
			from 	sep_participante par, users u
			where 	par.par_identificacion = u.par_identificacion
			and		rol_id = 2 and not par.par_identificacion = "0"
			and 	estado = "1"
			order by par_nombres';
		$instructores = DB::select($sql);
		
		$sql = '
			select 	* 
			from 	sep_transversal_instructor tra_ins, sep_participante par, users u
			where	tra_ins.par_id_instructor = par.par_identificacion
			and 	par.par_identificacion = u.par_identificacion
			and		rol_id = 2 and not par.par_identificacion = "0"
			and 	estado = "1"
			group 	by tra_ins.par_id_instructor
			order 	by par_nombres';
		$instructores_transversal = DB::select($sql);

		$anio_actual = date('Y');
		$sql = '
			select 	pla_fec_tri_id, pla_fec_tri_year, pla_fec_tri_trimestre,
					pla_fec_tri_fec_inicio, pla_fec_tri_fec_fin 
			from 	sep_planeacion_fecha_trimestre
			where 	pla_fec_tri_year in("'.($anio_actual - 1).'", "'.$anio_actual.'","'.($anio_actual + 1).'","'.($anio_actual + 2).'")
			order by pla_fec_tri_year,pla_fec_tri_trimestre asc';
		$trimestres = DB::select($sql);

		$ambientes = DB::select('select pla_amb_id,pla_amb_descripcion from sep_planeacion_ambiente where pla_amb_estado = "Activo" and not pla_amb_id = 72 and not pla_amb_tipo = "Restriccion" order by pla_amb_descripcion');
		$tipo_oferta = DB::select('select * from sep_planeacion_tipo_oferta');
		$franjasArray = array(1 => 'Mañana', 2 => 'Tarde', 3 => 'Noche', 4 => 'Mixta');
		$diaOrtografia = array('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
		$faseOrtografia = array(1 =>'Análisis','Planeación','Ejecución','Evaluación');
		
		//arreglo de años permitidos para buscar
		$anioslis=["".($anio_actual - 1)."","$anio_actual","".($anio_actual + 1).""];
		
		if(isset($generar)){
			return view("Modules.Seguimiento.Horario.indexPDF",compact('par_identificacion_coordinador','coordinadores','rol','programacionDetalle','tipos','ambientes','instructores',"horarios_detalle","pla_fic_id","pla_fec_tri_id","trimestre","trimestres","fichas","arrayErrores","diaOrtografia", "horarios", "programacion", "faseOrtografia"));
		}else{
			if($rol == 0 or $rol == 3 or $rol == 5 or $rol == 8 or $rol == 10 or $rol == 16 or $rol == 19){
				return view("Modules.Seguimiento.Horario.index",compact('year','anioslis','franjasArray', 'actividades_programadas', 'instructores_transversal','cc','tipo_oferta','par_identificacion_coordinador','coordinadores','rol','programacionDetalle','tipos','ambientes','instructores',"horarios_detalle","pla_fic_id","pla_fec_tri_id","trimestre","trimestres","fichas","arrayErrores","diaOrtografia", "horarios", "programacion", "faseOrtografia","modalidad"));
			}else{
				return view("Modules.Seguimiento.Horario.indexRolInstructor",compact('cc','tipo_oferta','par_identificacion_coordinador','coordinadores','rol','programacionDetalle','tipos','ambientes','instructores',"horarios_detalle","pla_fic_id","pla_fec_tri_id","trimestre","trimestres","fichas","arrayErrores","diaOrtografia", "horarios", "programacion", "faseOrtografia","modalidad"));
			}
		}
	}

 	public function getModalidad()
	{
		extract($_GET);
		$campo="";
		if (is_numeric($modalidad) && $modalidad >= 1 && $modalidad <=3) {
			if ($modalidad == 1 or $modalidad == 2 ) {
			    $campo = " and fic.fic_modalidad = ".$modalidad;
			}
			$sql = '
			select 	pla_fic_id, pla_fic.fic_numero, prog_nombre, pla_fra_descripcion
			from 	sep_planeacion_ficha pla_fic, sep_ficha fic, sep_programa pro, sep_planeacion_franja fra
			where 	pla_fic.fic_numero = fic.fic_numero
			and  	fic.prog_codigo = pro.prog_codigo '.$campo.'
            and 	fra.pla_fra_id = pla_fic.pla_fra_id
			and  	not fic.fic_numero in("Restriccion","Complementario")
			order 	by prog_nombre, pla_fic_id desc';
			$fichas = DB::select($sql);
			$list="";
			foreach ($fichas as $val) {
				$list=$list."<option value='$val->pla_fic_id'>".$val->fic_numero." - ".$val->prog_nombre." - ".$val->pla_fra_descripcion."</option>";
			}
            return $list;
		}else{
			return "err";
		}
	}

	public function getContenidomodalmodificar(){
		$rol = \Auth::user()->participante->rol_id;
		if($rol == 0 or $rol == 3 or $rol == 5){
			extract($_GET);
			//dd($_GET);
			$sql = 'select 	pla_fic_det_id,pla_dia_id,pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_fic_det_hor_totales,
							pla_amb_descripcion,par_nombres,par_apellidos, pla_tip_id
					from 	sep_planeacion_ficha_detalle p_f_d, sep_planeacion_ambiente amb, sep_participante par 
					where 	p_f_d.pla_amb_id = amb.pla_amb_id 
					and 	p_f_d.par_id_instructor = par.par_identificacion 
					and 	pla_fic_det_id in('.$datos.')
					order 	by pla_tip_id, pla_dia_id, pla_fic_det_hor_inicio';
			$horario_detalle = DB::select($sql);
			$dias = array(1=>'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
			return view('Modules.Seguimiento.Horario.contenidoModalModificar',compact('dias','horario_detalle'));
		}else{
			echo "No tienes los permisos para ingresar a esta función";
		}
	}

	public function getContenidomodalagregar(){
		$rol = \Auth::user()->participante->rol_id;
		if($rol == 0 or $rol == 3 or $rol == 5){
			extract($_GET);
			//dd($_GET);
			$dias = DB::select('select * from sep_planeacion_dia');
			$ambientes = DB::select('select pla_amb_id, pla_amb_descripcion from sep_planeacion_ambiente where pla_amb_id not in(69,70,71,72,73)');
			$instructores = DB::select('select par_identificacion, par_nombres, par_apellidos from sep_participante where rol_id = 2 order by par_nombres asc');
			
			return view('Modules.Seguimiento.Horario.agregarContenido',compact('pla_fic_id','dias','ambientes','instructores'));
		}else{
			echo "No tienes los permisos para ingresar a esta función";
		}
	}

	public function postActualizarambins(){
		extract($_POST);
		$acciones = array();
		if($hora_inicio == $hora_fin){ $acciones['errores'][] = 'La hora inicio y la hora fin tienen que ser diferentes.'; }
		if($hora_inicio > $hora_fin){ $acciones['errores'][] = 'La hora de inicio no puede ser mayor a la hora de fin.'; }

		if(!isset($acciones['errores'])){
			$ins_amb = 'select 	par_id_instructor,pla_amb_id  from  sep_planeacion_ficha_detalle
						where 	((pla_fic_det_hor_inicio < '.$hora_inicio.' and (pla_fic_det_hor_fin > '.$hora_inicio.' and pla_fic_det_hor_fin <= '.$hora_fin.'))
						or	    	((pla_fic_det_hor_inicio >= '.$hora_inicio.' and  pla_fic_det_hor_inicio < '.$hora_fin.') and pla_fic_det_hor_fin > '.$hora_fin.')
						or	    	(pla_fic_det_hor_inicio >= '.$hora_inicio.' and pla_fic_det_hor_fin <= '.$hora_fin.')
						or 	    	(pla_fic_det_hor_inicio < '.$hora_inicio.' and pla_fic_det_hor_fin > '.$hora_fin.'))
						and		pla_fic_det_fec_fin = "'.$fechaFin.'"  and  pla_dia_id = '.$dia.' and not pla_fic_det_id = '.$pla_fic_det_id.'
						and     not pla_amb_id = 72 
						group 	by par_id_instructor,pla_amb_id';
			$instructores_ambientes = DB::select($ins_amb);
			//dd($instructores_ambientes);
			$validar = array();
			foreach($instructores_ambientes as $val){
				$validar['instructor'][] = $val->par_id_instructor; 
				if($val->pla_amb_id != 123){
				    $validar['ambiente'][] = $val->pla_amb_id;
				}
			}
			
			$concatenarAmbiente = '';
			$concatenarInstructor = '';
			if(isset($validar['instructor'])){
				$instructoresOcupados = implode(',',$validar['instructor']);  $ambientesOcupados = implode(',',$validar['ambiente']);
				$concatenarAmbiente = ' not in ('.$ambientesOcupados.')';  $concatenarInstructor = ' not in('.$instructoresOcupados.')';
				$acciones['exito'] = 'si';
			}
			//dd($instructoresOcupados);
			$ambientes = DB::select('select pla_amb_id, pla_amb_descripcion from sep_planeacion_ambiente where pla_amb_id '.$concatenarAmbiente.' and not pla_amb_tipo = "Restriccion" order by pla_amb_descripcion');
			$instructores = DB::select('select par_identificacion, concat(par_nombres," ", par_apellidos) as nombre from sep_participante where rol_id = 2 and par_identificacion '.$concatenarInstructor.' order by par_nombres asc');
			
			$concatenar_ambientes = '';
			foreach($ambientes as $val){
				$selected = '';
				if($val->pla_amb_id == $det_ambiente){	$selected = 'selected';	}
				$concatenar_ambientes .= '<option '.$selected.' value="'.$val->pla_amb_id.'">'.$val->pla_amb_descripcion.'</option>';
			}

			$concatenar_instructores = '';
			foreach($instructores as $val){
				$selected = '';
				if($val->par_identificacion == $det_instructor){	$selected = 'selected';	}
				$concatenar_instructores .= '<option '.$selected.' value="'.$val->par_identificacion.'">'.$val->nombre.'</option>';
			}
			
			$acciones['ambientes'] = $concatenar_ambientes;
			$acciones['instructores'] = $concatenar_instructores;
		}
		echo json_encode($acciones);
	}

	public function getContenidomodificar(){
		$rol = \Auth::user()->participante->rol_id;
		if($rol == 0 or $rol == 3 or $rol == 5){
			extract($_GET);
			$pla_fic_det = implode(',',$pla_fic_det);
			$dias = array(1=>'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
			$sql = 'select 	pla_fic_det_id,pla_dia_id,pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_fic_det_hor_totales,
							amb.pla_amb_id,pla_amb_descripcion,par.par_identificacion,par_nombres,par_apellidos
					from 	sep_planeacion_ficha_detalle p_f_d, sep_planeacion_ambiente amb, sep_participante par 
					where 	p_f_d.pla_amb_id = amb.pla_amb_id 
					and 	p_f_d.par_id_instructor = par.par_identificacion 
					and 	pla_fic_det_id in('.$pla_fic_det.')';
			$horario_detalle = DB::select($sql);
			$ambientes = DB::select('select pla_amb_id,pla_amb_descripcion from sep_planeacion_ambiente where not pla_amb_tipo = "Restriccion" and pla_amb_estado = "Activo" order by pla_amb_descripcion');
			$instructores = DB::select('select par.par_identificacion, par.par_nombres, par.par_apellidos, usu.estado from sep_participante par
										left join users usu on usu.par_identificacion =  par.par_identificacion
										where par.rol_id = 2 
										and not par.par_identificacion = 0 
										and usu.estado = "1" 
										order by par.par_nombres');
			//dd($horario_detalle);
			return view('Modules.Seguimiento.Horario.contenidoModificar',compact('ambientes','pla_fic_det','dias','horario_detalle','instructores'));
		}else{
			echo 'No tienes los permisos para ingresar a esta función';
		}
	}

	public function postUpdatetrueque(){
		$rol = \Auth::user()->participante->rol_id;
		/*if($rol == 0 or $rol == 3 or $rol == 5){
			extract($_POST);
			$concatenar = 'where pla_fic_det_id in(';
			foreach($pla_fic_det_id_checked as $val){ $concatenar .= $val.','; }
			$concatenar = substr($concatenar,0,-1);
			$concatenar .=')';

			$notificaciones = '<ol>';
			$contadorValidacion = 0;
			$datos_instructores = array();

			$sql = '
				select 	det.*,par_nombres
				from 		sep_planeacion_ficha_detalle det,sep_participante par '.$concatenar.' 
				and 		det.par_id_instructor = par.par_identificacion
				order 	by pla_fic_det_id asc limit 2';
			$instructores = DB::select($sql);
			if($instructores[0]->pla_fic_det_hor_totales == $instructores[1]->pla_fic_det_hor_totales){
				if($instructores[0]->par_id_instructor != $instructores[1]->par_id_instructor){
					$whereFechas = '
						((pla_fic_det_fec_inicio < "'.$fechaInicio.'" and (pla_fic_det_fec_fin > "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))
						or 	((pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_inicio < "'.$fechaFin.'") and pla_fic_det_fec_fin > "'.$fechaFin.'")
						or 	(pla_fic_det_fec_inicio < "'.$fechaInicio.'" and pla_fic_det_fec_fin > "'.$fechaFin.'")
						or 	(pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))';
					$valor = 1;
					foreach($instructores as $key => $val){
						$ambiente = '
							select 	pla_dia_id
							from 		sep_planeacion_ficha_detalle
							where 	((pla_fic_det_hor_inicio < '.$val->pla_fic_det_hor_inicio.' and (pla_fic_det_hor_fin > '.$val->pla_fic_det_hor_inicio.' and pla_fic_det_hor_fin <= '.$val->pla_fic_det_hor_fin.'))
							or	    	((pla_fic_det_hor_inicio >= '.$val->pla_fic_det_hor_inicio.' and  pla_fic_det_hor_inicio < '.$val->pla_fic_det_hor_fin.') and pla_fic_det_hor_fin > '.$val->pla_fic_det_hor_fin.')
							or	    	(pla_fic_det_hor_inicio >= '.$val->pla_fic_det_hor_inicio.' and pla_fic_det_hor_fin <= '.$val->pla_fic_det_hor_fin.')
							or 	    	(pla_fic_det_hor_inicio < '.$val->pla_fic_det_hor_inicio.' and pla_fic_det_hor_fin > '.$val->pla_fic_det_hor_fin.')) 
							and 		'.$whereFechas.'
							and			pla_amb_id = '.$val->pla_amb_id.'
							and			pla_dia_id = '.$val->pla_dia_id.'
							and 		not pla_fic_det_id = '.$val->pla_fic_det_id.' limit 1';
						$validar = DB::select($ambiente);
						if(count($validar) == 0){
							$grupo = '
								select 	pla_dia_id
								from 		sep_planeacion_ficha_detalle
								where 	((pla_fic_det_hor_inicio < '.$val->pla_fic_det_hor_inicio.' and (pla_fic_det_hor_fin > '.$val->pla_fic_det_hor_inicio.' and pla_fic_det_hor_fin <= '.$val->pla_fic_det_hor_fin.'))
								or	    	((pla_fic_det_hor_inicio >= '.$val->pla_fic_det_hor_inicio.' and  pla_fic_det_hor_inicio < '.$val->pla_fic_det_hor_fin.') and pla_fic_det_hor_fin > '.$val->pla_fic_det_hor_fin.')
								or	    	(pla_fic_det_hor_inicio >= '.$val->pla_fic_det_hor_inicio.' and pla_fic_det_hor_fin <= '.$val->pla_fic_det_hor_fin.')
								or 	    	(pla_fic_det_hor_inicio < '.$val->pla_fic_det_hor_inicio.' and pla_fic_det_hor_fin > '.$val->pla_fic_det_hor_fin.'))
								and			'.$whereFechas.'
								and			pla_dia_id = '.$val->pla_dia_id.'
								and 		pla_fic_id = '.$fic_id.'
								and 		not pla_fic_det_id = '.$val->pla_fic_det_id.' limit 1';
							$validar = DB::select($grupo);
							if(count($validar) == 0){
								$instructor = '
									select 	pla_dia_id
									from 		sep_planeacion_ficha_detalle
									where 	((pla_fic_det_hor_inicio < '.$val->pla_fic_det_hor_inicio.' and (pla_fic_det_hor_fin > '.$val->pla_fic_det_hor_inicio.' and pla_fic_det_hor_fin <= '.$val->pla_fic_det_hor_fin.'))
									or	    	((pla_fic_det_hor_inicio >= '.$val->pla_fic_det_hor_inicio.' and  pla_fic_det_hor_inicio < '.$val->pla_fic_det_hor_fin.') and pla_fic_det_hor_fin > '.$val->pla_fic_det_hor_fin.')
									or	    	(pla_fic_det_hor_inicio >= '.$val->pla_fic_det_hor_inicio.' and pla_fic_det_hor_fin <= '.$val->pla_fic_det_hor_fin.')
									or 	    	(pla_fic_det_hor_inicio < '.$val->pla_fic_det_hor_inicio.' and pla_fic_det_hor_fin > '.$val->pla_fic_det_hor_fin.')) 
									and 		'.$whereFechas.'
									and			par_id_instructor = "'.$instructores[$valor]->par_id_instructor.'"
									and			pla_dia_id = '.$val->pla_dia_id.'
									and 		not pla_fic_det_id = '.$val->pla_fic_det_id.' limit 1';
								$validar = DB::select($instructor);
								if(count($validar) == 0){
									$contadorValidacion++;
									$validador[$key]['instructor_cc'] = $instructores[$valor]->par_id_instructor;
								}else{
									$notificaciones .= '<li>El instructor <strong>'.$instructores[$valor]->par_nombres.'</strong> esta programado en la franja de tiempo seleccionada.</li>';
								}
							}else{
								$notificaciones .= '<li>La modificación # <strong>'.$fila.'</strong> <strong style="color:red;">NO</strong> se logro, porque el grupo esta ocupado.</li>';
							}
						}else{
							$notificaciones .= '<li>El ambiente esta ocupado en la franja de tiempo seleccionada.</li>';
						}
						$valor = 0;
					}
				}else{
					echo 'No se puede realizar un trueque con el mismo instructor, seleccione diferentes.';
				}
			}else{
				echo 'Para realizar un trueque los dos instructores deben tener la misma cantidad de horas totales.';
			}
			
			if($contadorValidacion == 2){
				foreach($instructores as $val1){
					$bitacora = '
						insert into sep_planeacion_ficha_bitacora
						values (default, "'.\Auth::user()->participante->par_identificacion.'", '.$val1->pla_fic_id.', 
						"'.$val1->pla_fic_det_fec_inicio.'", "'.$val1->pla_fic_det_fec_fin.'",
						'.$val1->pla_fic_det_hor_inicio.', '.$val1->pla_fic_det_hor_fin.',
						'.$val1->pla_fic_det_hor_totales.', '.$val1->par_id_instructor.',
						'.$val1->pla_dia_id.', '.$val1->pla_amb_id.', '.$val1->pla_trimestre_numero_ficha.',
						 "Modifico", default)';
					DB::insert($bitacora);
				}
				
				$actualizar = '
					update 	sep_planeacion_ficha_detalle
					set 	  par_id_instructor = "'.$validador[0]['instructor_cc'].'"
					where 	pla_fic_det_id = '.$instructores[0]->pla_fic_det_id;
				DB::update($actualizar);

				$actualizar = '
					update 	sep_planeacion_ficha_detalle
					set 	  par_id_instructor = "'.$validador[1]['instructor_cc'].'"
					where 	pla_fic_det_id = '.$instructores[1]->pla_fic_det_id;;
				DB::update($actualizar);
				echo 'El trueque se realizo exitosamente';
			}else{
				echo $notificaciones;
			}
		}*/
	}

	public function postUpdate(){
		$rol = \Auth::user()->participante->rol_id;
		if($rol == 0 or $rol == 3 or $rol == 5){
		    $_POST = $this->seguridad($_POST);
			extract($_POST);
			
			if(!is_numeric($fic_id)){
				dd('El valor fic_id debe ser numérico');
			}
			
			$sql = '
				select 	pla_fra.pla_fra_id, pla_fra_hor_inicio, pla_fra_hor_fin, niv_for_id
				from 	sep_planeacion_ficha pla_fic, sep_planeacion_franja pla_fra, 
						sep_ficha fic, sep_programa pro
				where 	pla_fic.pla_fra_id = pla_fra.pla_fra_id
				and 	pla_fic.fic_numero = fic.fic_numero
				and 	fic.prog_codigo = pro.prog_codigo
				and 	pla_fic_id = '.$fic_id.' limit 1';
			$datos_ficha = DB::select($sql);
			if(count($datos_ficha) == 0){
				dd('No encontramos los datos de la ficha.');
			}

			$nivel_formacion = $datos_ficha[0]->niv_for_id;
			$jornada_inicio_original = $datos_ficha[0]->pla_fra_hor_inicio;
			$jornada_fin_original = $datos_ficha[0]->pla_fra_hor_fin;
			$franja = $datos_ficha[0]->pla_fra_id;
			
			// Tranversales por jornada
			// Mañana
			$jornada_contraria[1]['inicio'] = 12;
			$jornada_contraria[1]['fin'] = 18;
			// Tarde
			$jornada_contraria[2]['inicio'] = 6;
			$jornada_contraria[2]['fin'] = 12;
			// Noche
			$jornada_contraria[3]['inicio'] = 16;
			$jornada_contraria[3]['fin'] = 22;
			// Mixto
			$jornada_contraria[4]['inicio'] = 6;
			$jornada_contraria[4]['fin'] = 18;
			
			$sql = '
				select 	par_identificacion
				from 	sep_transversal_instructor tra_ins, sep_participante par
				where	tra_ins.par_id_instructor = par.par_identificacion
				and		rol_id = 2 and not par.par_identificacion = "0"
                group by par.par_identificacion';
			$instructor_transversal_db = DB::select($sql);
			foreach($instructor_transversal_db as $val){
				$instructor_transversal[] = $val->par_identificacion;
			}
			
			$exitos = count($pla_fic_det_id);
			$contador_exitos = 0;
			$whereFechas = '
				((pla_fic_det_fec_inicio < "'.$fechaInicio.'" and (pla_fic_det_fec_fin > "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))
				or 	((pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_inicio < "'.$fechaFin.'") and pla_fic_det_fec_fin > "'.$fechaFin.'")
				or 	(pla_fic_det_fec_inicio < "'.$fechaInicio.'" and pla_fic_det_fec_fin > "'.$fechaFin.'")
				or 	(pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))';
			$fila = 1;
			$notificaciones = '<ol>';
			
			foreach($pla_fic_det_id as $key => $val){
				if($pla_fic_det_hor_inicio[$key] != $pla_fic_det_hor_fin[$key]){
					if($pla_fic_det_hor_inicio[$key] < $pla_fic_det_hor_fin[$key]){
					    $validar = array();
					    
					    // Clase técnica
						if($nivel_formacion != 1 and $pla_amb_id[$key] != 72){
							if($pla_dia_id[$key] == 6){
								$jornada_inicio = 6;
								$jornada_fin = 18;
							}else{
							    $jornada_inicio = $jornada_inicio_original;
								$jornada_fin = $jornada_fin_original;
								$sql = '
									select 	pla_tip_id 
									from 	sep_planeacion_ficha_detalle 
									where 	pla_fic_det_id = '.$val.' limit 1';
								$query = DB::select($sql);
								$tipoRegistro = $query[0]->pla_tip_id;
								if($tipoRegistro != 2){
									if(in_array($par_id_instructor[$key], $instructor_transversal)){
										$jornada_inicio = $jornada_contraria[$franja]['inicio'];
										$jornada_fin = $jornada_contraria[$franja]['fin'];
									}
								}
							}

							if($pla_fic_det_hor_inicio[$key] < $jornada_inicio or $pla_fic_det_hor_fin[$key] > $jornada_fin){
								$notificaciones .= '<li>La modificación # <strong>'.$fila.'</strong> <strong style="color:red;">NO</strong> se logro, porque la programación debe estar entre la hora inicio <strong>'.$jornada_inicio.'</strong> y hora fin <strong>'.$jornada_fin.'</strong></li>';
								$validar[] = 'error';
							}
						}
						
						if(count($validar) == 0){
    					    if($pla_amb_id[$key] != 72 and $pla_amb_id[$key] != 123){
        						$ambiente = '
        							select 	pla_dia_id
        							from 		sep_planeacion_ficha_detalle
        							where 	((pla_fic_det_hor_inicio < '.$pla_fic_det_hor_inicio[$key].' and (pla_fic_det_hor_fin > '.$pla_fic_det_hor_inicio[$key].' and pla_fic_det_hor_fin <= '.$pla_fic_det_hor_fin[$key].'))
        							or	    	((pla_fic_det_hor_inicio >= '.$pla_fic_det_hor_inicio[$key].' and  pla_fic_det_hor_inicio < '.$pla_fic_det_hor_fin[$key].') and pla_fic_det_hor_fin > '.$pla_fic_det_hor_fin[$key].')
        							or	    	(pla_fic_det_hor_inicio >= '.$pla_fic_det_hor_inicio[$key].' and pla_fic_det_hor_fin <= '.$pla_fic_det_hor_fin[$key].')
        							or 	    	(pla_fic_det_hor_inicio < '.$pla_fic_det_hor_inicio[$key].' and pla_fic_det_hor_fin > '.$pla_fic_det_hor_fin[$key].'))
        							and			'.$whereFechas.'  and  pla_amb_id = '.$pla_amb_id[$key].'  and	 pla_dia_id = '.$pla_dia_id[$key].'
        							and 		not pla_fic_det_id = '.$val.' limit 1';
        						$validar = DB::select($ambiente);
    					    }
    						if(count($validar) == 0){
    							$instructor = '
    								select 	pla_dia_id
    								from 	sep_planeacion_ficha_detalle
    								where 	((pla_fic_det_hor_inicio < '.$pla_fic_det_hor_inicio[$key].' and (pla_fic_det_hor_fin > '.$pla_fic_det_hor_inicio[$key].' and pla_fic_det_hor_fin <= '.$pla_fic_det_hor_fin[$key].'))
    								or	    	((pla_fic_det_hor_inicio >= '.$pla_fic_det_hor_inicio[$key].' and  pla_fic_det_hor_inicio < '.$pla_fic_det_hor_fin[$key].') and pla_fic_det_hor_fin > '.$pla_fic_det_hor_fin[$key].')
    								or	    	(pla_fic_det_hor_inicio >= '.$pla_fic_det_hor_inicio[$key].' and pla_fic_det_hor_fin <= '.$pla_fic_det_hor_fin[$key].')
    								or 	    	(pla_fic_det_hor_inicio < '.$pla_fic_det_hor_inicio[$key].' and pla_fic_det_hor_fin > '.$pla_fic_det_hor_fin[$key].'))
    								and		'.$whereFechas.'  and  par_id_instructor = "'.$par_id_instructor[$key].'"  and  pla_dia_id = '.$pla_dia_id[$key].'
    								and 		not pla_fic_det_id = '.$val.' limit 1';
    							$validar = DB::select($instructor);
    							if(count($validar) == 0){
    								$grupo = '
    									select 	pla_dia_id
    									from 		sep_planeacion_ficha_detalle
    									where 	((pla_fic_det_hor_inicio < '.$pla_fic_det_hor_inicio[$key].' and (pla_fic_det_hor_fin > '.$pla_fic_det_hor_inicio[$key].' and pla_fic_det_hor_fin <= '.$pla_fic_det_hor_fin[$key].'))
    									or	    	((pla_fic_det_hor_inicio >= '.$pla_fic_det_hor_inicio[$key].' and  pla_fic_det_hor_inicio < '.$pla_fic_det_hor_fin[$key].') and pla_fic_det_hor_fin > '.$pla_fic_det_hor_fin[$key].')
    									or	    	(pla_fic_det_hor_inicio >= '.$pla_fic_det_hor_inicio[$key].' and pla_fic_det_hor_fin <= '.$pla_fic_det_hor_fin[$key].')
    									or 	    	(pla_fic_det_hor_inicio < '.$pla_fic_det_hor_inicio[$key].' and pla_fic_det_hor_fin > '.$pla_fic_det_hor_fin[$key].'))
    									and			'.$whereFechas.'  and  pla_dia_id = '.$pla_dia_id[$key].'  and  pla_fic_id = '.$fic_id.'
    									and 		not pla_fic_det_id = '.$val.' limit 1';
    								$validar = DB::select($grupo);
    								if(count($validar) == 0){
    									$horasTotales ='
    										select 	sum(pla_fic_det_hor_totales) as total
    										from 		sep_planeacion_ficha_detalle
    										where 	'.$whereFechas.'  and  not pla_fic_det_id = '.$val.'
    										and 	not pla_tip_id = 5  and  par_id_instructor = "'.$par_id_instructor[$key].'"';
    									$horasTotales = DB::select($horasTotales);
    									$horasTotales = $horasTotales[0]->total;
    									$horasInstructor = DB::select('select par_horas_semanales from sep_participante where par_identificacion = "'.$par_id_instructor[$key].'" limit 1');
    									$horasAColocar = $pla_fic_det_hor_fin[$key] - $pla_fic_det_hor_inicio[$key];
    									if(($horasTotales + $horasAColocar) <= $horasInstructor[0]->par_horas_semanales){
    										$sql = 'select 	pla_fic_id,pla_fic_det_id,pla_dia_id,pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_trimestre_numero_ficha,
    														pla_fic_det_fec_inicio,pla_fic_det_fec_fin,pla_fic_det_hor_totales,amb.pla_amb_id,par.par_identificacion
    												from 	sep_planeacion_ficha_detalle p_f_d, sep_planeacion_ambiente amb, sep_participante par 
    												where 	p_f_d.pla_amb_id = amb.pla_amb_id  and 	p_f_d.par_id_instructor = par.par_identificacion 
    												and 	pla_fic_det_id = '.$val.' limit 1';
    										$detalle = DB::select($sql);
    										$bitacora = '
    											insert into sep_planeacion_ficha_bitacora
    											values (default, "'.\Auth::user()->participante->par_identificacion.'", '.$detalle[0]->pla_fic_id.', 
    											"'.$detalle[0]->pla_fic_det_fec_inicio.'", "'.$detalle[0]->pla_fic_det_fec_fin.'",
    											'.$detalle[0]->pla_fic_det_hor_inicio.', '.$detalle[0]->pla_fic_det_hor_fin.',
    											'.$detalle[0]->pla_fic_det_hor_totales.', "'.$detalle[0]->par_identificacion.'",
    											'.$detalle[0]->pla_dia_id.', '.$detalle[0]->pla_amb_id.', '.$detalle[0]->pla_trimestre_numero_ficha.',
    											"Modifico", default)';
    										
    										$actualizar = '
    											update 	sep_planeacion_ficha_detalle
    											set 	pla_fic_det_hor_inicio = '.$pla_fic_det_hor_inicio[$key].', pla_fic_det_hor_fin = '.$pla_fic_det_hor_fin[$key].',
    													pla_fic_det_hor_totales = '.$horasAColocar.', par_id_instructor = "'.$par_id_instructor[$key].'",
    													pla_dia_id = '.$pla_dia_id[$key].', pla_amb_id = '.$pla_amb_id[$key].'
    											where 	pla_fic_det_id = '.$val;
    
    										DB::beginTransaction();
    										DB::insert($bitacora);
    										DB::update($actualizar);
    										DB::commit();
    										$notificaciones .= '<li>La modificación # <strong>'.$fila.'</strong> <strong style="color:green;">SI</strong> se logro exitosamente.</li>';
    										$contador_exitos++;
    									}else{
    										$notificaciones .= '<li>La modificación # <strong>'.$fila.'</strong> <strong style="color:red;">NO</strong> se logro, porque al programar esta actividad sobrepasa las horas semanales del instructor quedando con <strong>'.($horasTotales+$horasAColocar).'</strong> horas.</li>';
    									}
    								}else{
    									$notificaciones .= '<li>La modificación # <strong>'.$fila.'</strong> <strong style="color:red;">NO</strong> se logro, porque el grupo esta ocupado.</li>';
    								}
    							}else{
    								$notificaciones .= '<li>La modificación # <strong>'.$fila.'</strong> <strong style="color:red;">NO</strong> se logro, porque el instructor ya esta programado en la franja de tiempo seleccionada.</li>';
    							}
    						}else{
    							$notificaciones .= '<li>La modificación # <strong>'.$fila.'</strong> <strong style="color:red;">NO</strong> se logro, porque el ambiente esta ocupado en la franja de tiempo seleccionada.</li>';
    						}
    					}
					}else{
						$notificaciones .= '<li>La modificación # <strong>'.$fila.'</strong> <strong style="color:red;">NO</strong> se logro, porque la hora de inicio debe de ser menor a las hora fin.</li>';
					}
				}else{
					$notificaciones .= '<li>La modificación # <strong>'.$fila.'</strong> <strong style="color:red;">NO</strong> se logro, porque la hora de inicio y fin no pueden ser iguales.</li>';
				}
				$fila++;
			}
			$notificaciones .= '</ol>';
			
			if($exitos == $contador_exitos){
				echo 'Los cambios se realizaron exitosamente.';
			}else{
				echo $notificaciones;
			}
		}else{
			echo 'No tienes los permisos para ingresar a esta función';
		}
	}
	
	public function getIndexambiente(){
		extract($_GET);
		
		$anio_actual = date('Y');
		$sql = '
			select 	pla_fec_tri_id, pla_fec_tri_year, pla_fec_tri_trimestre,
					pla_fec_tri_fec_inicio, pla_fec_tri_fec_fin 
			from 	sep_planeacion_fecha_trimestre
			where 	pla_fec_tri_year in("'.($anio_actual - 1).'", "'.$anio_actual.'","'.($anio_actual + 1).'","'.($anio_actual + 2).'")
			order by pla_fec_tri_year desc';
		$trimestres = DB::select($sql);
		$ambientes = DB::select('select pla_amb_id,pla_amb_descripcion from sep_planeacion_ambiente where pla_amb_estado = "Activo" and not pla_amb_id in(72,123) and pla_amb_tipo != "Restriccion" order by pla_amb_descripcion asc');
		if(isset($pla_amb_id) and isset($pla_fec_tri_id)){
			if(!in_array('todas',$pla_amb_id)){
				$concatenar_horarios = 'and amb.pla_amb_id in(';
				foreach($pla_amb_id as $val){ $concatenar_horarios .= $val.','; }
				$concatenar_horarios = substr($concatenar_horarios,0,-1);
				$concatenar_horarios .= ')';
			}else{
				$concatenar_horarios = '';
			}
			
			if($pla_fec_tri_id != 'todos'){
				/*$fecha = DB::select('select pla_fec_tri_fec_fin from sep_planeacion_fecha_trimestre where pla_fec_tri_id = '.$pla_fec_tri_id.' limit 1');
				$concatenar_horarios_detalle = " and pla_fic_det_fec_fin = '".$fecha[0]->pla_fec_tri_fec_fin."'";*/
				$sql = '
					select 	pla_fec_tri_fec_inicio, pla_fec_tri_fec_fin
					from 	sep_planeacion_fecha_trimestre
					where 	pla_fec_tri_id = '.$pla_fec_tri_id.' limit 1';
				$fecha = DB::select($sql);
				$fecha_inicio = $fecha[0]->pla_fec_tri_fec_inicio;
				$fecha_fin = $fecha[0]->pla_fec_tri_fec_fin;
				$concatenar_horarios_detalle = '
					and	((pla_fic_det_fec_inicio < "'.$fecha_inicio.'" and (pla_fic_det_fec_fin > "'.$fecha_inicio.'" and pla_fic_det_fec_fin <= "'.$fecha_fin.'"))
						or 	((pla_fic_det_fec_inicio >= "'.$fecha_inicio.'" and pla_fic_det_fec_inicio < "'.$fecha_fin.'") and pla_fic_det_fec_fin > "'.$fecha_fin.'")
						or 	(pla_fic_det_fec_inicio < "'.$fecha_inicio.'" and pla_fic_det_fec_fin > "'.$fecha_fin.'")
						or 	(pla_fic_det_fec_inicio >= "'.$fecha_inicio.'" and pla_fic_det_fec_fin <= "'.$fecha_fin.'"))';
			}else{
				$concatenar_horarios_detalle = '';
			}
			
			$concatenarGenerar = 'prog_nombre,';
			if(isset($generar)){
				$concatenarGenerar = 'substring(prog_nombre, 1,25) AS prog_nombre,';
			}
			
			$sql = '
				select 	p_f.fic_numero, pla_fic_det_id, pla_dia_id,
						pla_fic_det_fec_inicio, pla_fic_det_fec_fin, pla_fic_consecutivo_ficha,
						prog_sigla, p_f_d.pla_amb_id, pla_amb_descripcion, amb.pla_amb_id,
						p_f.pla_fic_id, pla_trimestre_numero_year, '.$concatenarGenerar.'
						pla_fic_det_hor_inicio, pla_fic_det_hor_fin, pla_fic_det_hor_totales,
						par_id_instructor, pla_trimestre_numero_ficha,
    					concat(substring_index(par.par_nombres," ",1)," ",substring_index(par.par_apellidos," ",1)) as nombre
    			from 	sep_planeacion_ficha_detalle p_f_d,sep_planeacion_ficha p_f,sep_programa pro,
    					sep_ficha fic,sep_participante par,sep_planeacion_ambiente amb
				where 	p_f_d.pla_fic_id = p_f.pla_fic_id
				and  	p_f.fic_numero = fic.fic_numero
				and 	p_f_d.pla_amb_id = amb.pla_amb_id
				and 	p_f_d.par_id_instructor = par.par_identificacion
				and 	not pla_amb_tipo = "Restriccion"
				and 	not amb.pla_amb_id in(72,123)
				and 	pla_amb_estado = "Activo"
			    and 	fic.prog_codigo = pro.prog_codigo '.$concatenar_horarios.' '.$concatenar_horarios_detalle.'
    			order 	by pla_amb_descripcion, pla_fic_det_fec_inicio, pla_dia_id, pla_fic_det_hor_inicio asc';
			$horario = DB::select($sql);

			//echo '<pre>'; echo $sql; dd($horario);

			$fechas_trimestre = array();
			foreach($trimestres as $key => $val){
				$fechas_trimestre["fecIni"][$key+1] = $val->pla_fec_tri_fec_inicio;
				$fechas_trimestre["fecFin"][$key+1] = $val->pla_fec_tri_fec_fin;
			}

			if(count($horario)>0){
				$programacionDetalle = array();
				$fechas_inicio_fin = array();
				$programacion = array();

				foreach($horario as $key => $valor){
					if(isset($fechas_inicio_fin[$valor->pla_amb_id]['todas'])){
						if(!in_array($valor->pla_fic_det_fec_inicio, $fechas_inicio_fin[$valor->pla_amb_id]['todas'])){
							$fechas_inicio_fin[$valor->pla_amb_id]['todas'][] = $valor->pla_fic_det_fec_inicio;
						}

						if(!in_array($valor->pla_fic_det_fec_fin, $fechas_inicio_fin[$valor->pla_amb_id]['todas'])){
							$fechas_inicio_fin[$valor->pla_amb_id]['todas'][] = $valor->pla_fic_det_fec_fin;
						}
					}else{
						$fechas_inicio_fin[$valor->pla_amb_id]['todas'][] = $valor->pla_fic_det_fec_inicio;
						$fechas_inicio_fin[$valor->pla_amb_id]['todas'][] = $valor->pla_fic_det_fec_fin;
					}

					sort($fechas_inicio_fin[$valor->pla_amb_id]['todas']);
				}

				foreach($fechas_inicio_fin as $key => $instructor){
					$contador = 1;
					foreach($instructor['todas'] as $llave => $fecha){
						$dia_fecha = date('N', strtotime($fecha));
						$numero_par_impar = fmod($contador, 2);
						if($dia_fecha == 1){
							if($numero_par_impar == 0){
								$fecha_programar = date('Y-m-d', strtotime($fecha. 'last saturday'));
								$fechas_inicio_fin[$key]['todas'][] = $fecha_programar;
								continue;
							}
						}else if($dia_fecha == 6){
							if($numero_par_impar != 0){
								$fecha_programar = date('Y-m-d', strtotime($fecha. 'last monday'));
								$fechas_inicio_fin[$key]['todas'][] = $fecha_programar;
								continue;
							}
						}
						$contador++;
					}
					sort($fechas_inicio_fin[$key]['todas']);
				}

				foreach($fechas_inicio_fin as $key => $instructor){
					foreach($instructor['todas'] as $llave => $fecha){
						$numero_par_impar = fmod($llave, 2);
						if($numero_par_impar == 0){
							$fechas_inicio_fin[$key]['fecha_inicio'][] = $fecha;
						}else{
							$fechas_inicio_fin[$key]['fecha_fin'][] = $fecha;
						}
					}
				}

				foreach($horario as $key => $valor){
					foreach($fechas_inicio_fin[$valor->pla_amb_id]['fecha_inicio'] as $key1 => $fecha_inicio){
						$fecha_fin = $fechas_inicio_fin[$valor->pla_amb_id]['fecha_fin'][$key1];
						if($valor->pla_fic_det_fec_inicio <= $fecha_inicio and $valor->pla_fic_det_fec_fin > $fecha_inicio){
							if(isset($programacion[$valor->pla_amb_id][$fecha_inicio]['horas_programadas'])){
								$programacion[$valor->pla_amb_id][$fecha_inicio]['horas_programadas'] += $valor->pla_fic_det_hor_totales;
							}else{
								$programacion[$valor->pla_amb_id][$fecha_inicio]['horas_programadas'] = $valor->pla_fic_det_hor_totales;
							}
							
							$programacion[$valor->pla_amb_id][$fecha_inicio]['ambiente'] = $valor->pla_amb_descripcion;
							$programacion[$valor->pla_amb_id][$fecha_inicio]['fecha_inicio_actividad'][] = $valor->pla_fic_det_fec_inicio;
							$programacion[$valor->pla_amb_id][$fecha_inicio]['fecha_fin'] = $fecha_fin;
							$programacion[$valor->pla_amb_id][$fecha_inicio]['pla_fic_id'][] = $valor->pla_fic_id;
							$programacion[$valor->pla_amb_id][$fecha_inicio]['pla_fic_det_id'][] = $valor->pla_fic_det_id;
							$programacion[$valor->pla_amb_id][$fecha_inicio]['dia_id'][] = $valor->pla_dia_id;
							$programacion[$valor->pla_amb_id][$fecha_inicio]['hora_inicio'][] = $valor->pla_fic_det_hor_inicio;
							$programacion[$valor->pla_amb_id][$fecha_inicio]['hora_fin'][] = $valor->pla_fic_det_hor_fin;
							$programacion[$valor->pla_amb_id][$fecha_inicio]['horas_totales'][] = $valor->pla_fic_det_hor_totales;
							$programacion[$valor->pla_amb_id][$fecha_inicio]['ficha'][] = $valor->fic_numero;
							$programacion[$valor->pla_amb_id][$fecha_inicio]['programa'][] =  ucwords(mb_strtolower($valor->prog_nombre));
							$programacion[$valor->pla_amb_id][$fecha_inicio]['instructor_cc'][] = $valor->par_id_instructor;
							$programacion[$valor->pla_amb_id][$fecha_inicio]['instructor'][] = ucwords(mb_strtolower($valor->nombre));
							$programacion[$valor->pla_amb_id][$fecha_inicio]['trimestre'][] =  $valor->pla_trimestre_numero_ficha;
							$programacion[$valor->pla_amb_id][$fecha_inicio]['prog_sigla'][] =  $valor->prog_sigla;
							$programacion[$valor->pla_amb_id][$fecha_inicio]['consecutivo_ficha'][] =  $valor->pla_fic_consecutivo_ficha;

							$programacionDetalle[$valor->pla_amb_id][$fecha_inicio][$valor->par_id_instructor][] = $valor->pla_fic_det_id;
						}
					}
				}
			}
		}else{
			$fecha_actual = date('Y-m-d');
			$trimestre_actual = DB::select('select pla_fec_tri_id from sep_planeacion_fecha_trimestre where	pla_fec_tri_fec_fin > "'.$fecha_actual.'" and 	pla_fec_tri_fec_inicio < "'.$fecha_actual.'" limit 1');
			if(count($trimestre_actual)>0){
				$pla_fec_tri_id = $trimestre_actual[0]->pla_fec_tri_id;
			}
		}
		$diaOrtografia = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
		if(!isset($generar)){
			return view('Modules.Seguimiento.Horario.indexAmbiente',compact('programacionDetalle','fechas_trimestre','programacion','pla_fec_tri_id','pla_amb_id','ambientes','diaOrtografia','trimestres'));
		}else{
			return view('Modules.Seguimiento.Horario.indexAmbientePDF',compact('programacionDetalle','fechas_trimestre','programacion','pla_fec_tri_id','pla_amb_id','ambientes','diaOrtografia','trimestres'));
		}
	}
	
	public function getIndexinstructor(){
		extract($_GET);
		$anio_actual = date('Y');
		$anios = '"'.($anio_actual - 1).'", "'.$anio_actual.'","'.($anio_actual + 1).'","'.($anio_actual + 2).'"';
		//arreglo de años permitidos para buscar
        $anioslis=["".($anio_actual - 1)."","$anio_actual","".($anio_actual + 1).""];
		
		$sql = '
			select 	pla_fec_tri_id, pla_fec_tri_year, pla_fec_tri_trimestre,
					pla_fec_tri_fec_inicio, pla_fec_tri_fec_fin 
			from 	sep_planeacion_fecha_trimestre
			where 	pla_fec_tri_year in('.$anios.')
			order by pla_fec_tri_year desc';
		$trimestres = DB::select($sql);
		$sql = '
			select 	p.par_identificacion, concat(par_nombres," ",par_apellidos) as nombre
			from 	sep_participante p, users u
			where 	rol_id = 2
			and 	u.par_identificacion = p.par_identificacion and estado = "1"
			order by par_nombres';
		$instructores = DB::select($sql);
		
		$sql = '
			select 	par_identificacion,par_nombres,par_apellidos
			from 	sep_participante where rol_id = 3 order by par_nombres';
		$coordinadores = DB::select($sql);
		
		if(isset($pla_fec_tri_id)){
		    $concatenar_horarios = '';
		    if(isset($par_identificacion)){
    			if(!in_array('todas',$par_identificacion)){
    				$concatenar_horarios = 'and par.par_identificacion in(';
    				if(isset($par_identificacion_coordinador) and $par_identificacion_coordinador != ''){
						$sql = '
							select 	par_identificacion_instructor
							from 	sep_instructor_coordinador
							where 	par_identificacion_coordinador = "'.$par_identificacion_coordinador.'"';
						$instructores_coordinacion = DB::select($sql);
						
    					foreach($instructores_coordinacion as $val){
							$concatenar_horarios .= "'$val->par_identificacion_instructor',";
						}
    				}else{
    					foreach($par_identificacion as $val){
							$concatenar_horarios .= "'$val',";
						}
					}
					
    				if($concatenar_horarios == 'and par.par_identificacion in('){
    					echo '<h1>El coordinador seleccionado no tiene instructores asignados.</h1>';
    					dd();
    				}
    				$concatenar_horarios = substr($concatenar_horarios,0,-1);
    				$concatenar_horarios .= ')';
    			}
		    }else{
		        $concatenar_horarios = 'and par.par_identificacion in(';
		        if(isset($par_identificacion_coordinador) and $par_identificacion_coordinador != ''){
		            $concatenar = '';
					if($par_identificacion_coordinador != 'todas'){
						$concatenar = ' where par_identificacion_coordinador = "'.$par_identificacion_coordinador.'"';
					}

					$sql = 'select par_identificacion_instructor from sep_instructor_coordinador '.$concatenar;
					$instructores_coordinacion = DB::select($sql);
					foreach($instructores_coordinacion as $val){
						$concatenar_horarios .= "'$val->par_identificacion_instructor',";
					}
				}
				$concatenar_horarios = substr($concatenar_horarios,0,-1);
    			$concatenar_horarios .= ')';
		    }
		    
		    if($concatenar_horarios == 'and par.par_identificacion in)'){
				echo '<h1>El coordinador seleccionado no tiene instructores asignados.</h1>';
				dd();
			}
			
			if($pla_fec_tri_id != 'todos'){
				$sql = '
					select 	pla_fec_tri_fec_inicio, pla_fec_tri_fec_fin
					from 	sep_planeacion_fecha_trimestre
					where 	pla_fec_tri_id = '.$pla_fec_tri_id.' limit 1';
				$fecha = DB::select($sql);
				$fecha_inicio = $fecha[0]->pla_fec_tri_fec_inicio;
				$fecha_fin = $fecha[0]->pla_fec_tri_fec_fin;
				$concatenar_horarios_detalle = '
					and	((pla_fic_det_fec_inicio < "'.$fecha_inicio.'" and (pla_fic_det_fec_fin > "'.$fecha_inicio.'" and pla_fic_det_fec_fin <= "'.$fecha_fin.'"))
						or 	((pla_fic_det_fec_inicio >= "'.$fecha_inicio.'" and pla_fic_det_fec_inicio < "'.$fecha_fin.'") and pla_fic_det_fec_fin > "'.$fecha_fin.'")
						or 	(pla_fic_det_fec_inicio < "'.$fecha_inicio.'" and pla_fic_det_fec_fin > "'.$fecha_fin.'")
						or 	(pla_fic_det_fec_inicio >= "'.$fecha_inicio.'" and pla_fic_det_fec_fin <= "'.$fecha_fin.'"))';
			}else{
				if(isset($pla_fec_tri_id) and $pla_fec_tri_id == 'todos'){
					$anios = $year;
				}
				$concatenar_horarios_detalle = " and not amb.pla_amb_id in(72) and amb.pla_amb_tipo != 'Restriccion' and pla_fic_det_fec_fin like '%".$anios."%' and pla_fic_det_fec_inicio like '%".$anios."%'";
			}
			$concatenarGenerar = 'prog_nombre,';
			if(isset($generar)){
				$concatenarGenerar = 'substring(prog_nombre, 1,25) AS prog_nombre,';
			}
			
			$sql = '
				select 	p_f.fic_numero, p_f.pla_fic_id, pla_fic_det_id, pla_dia_id,
						pla_fic_det_fec_inicio, pla_fic_det_fec_fin, prog_sigla,
						p_f_d.pla_amb_id, pla_amb_descripcion, pla_fic_det_hor_inicio, pla_fic_det_hor_fin,
						pla_trimestre_numero_ficha, pla_fic_det_hor_totales, par_id_instructor, '.$concatenarGenerar.'
						concat(par_nombres," ",par_apellidos) as nombre, pla_tip_id
			    from 	sep_planeacion_ficha_detalle p_f_d, sep_planeacion_ficha p_f,
						sep_programa pro, sep_ficha fic, sep_participante par,
						sep_planeacion_ambiente amb
			    where 	p_f_d.pla_fic_id = p_f.pla_fic_id
			    and 	p_f.fic_numero = fic.fic_numero
			    and 	p_f_d.pla_amb_id = amb.pla_amb_id
			    and 	p_f_d.par_id_instructor = par.par_identificacion
			    and 	fic.prog_codigo = pro.prog_codigo '.$concatenar_horarios.' '.$concatenar_horarios_detalle.'
				order 	by par_nombres, pla_fic_det_fec_inicio, pla_dia_id, pla_fic_det_hor_inicio asc';
			$horario = DB::select($sql);
			
			if(count($horario)>0){
				$programacion = array();
				$fechas_inicio_fin = array();
				foreach($horario as $key => $valor){
					if(isset($fechas_inicio_fin[$valor->par_id_instructor]['todas'])){
						if(!in_array($valor->pla_fic_det_fec_inicio, $fechas_inicio_fin[$valor->par_id_instructor]['todas'])){
							$fechas_inicio_fin[$valor->par_id_instructor]['todas'][] = $valor->pla_fic_det_fec_inicio;
						}

						if(!in_array($valor->pla_fic_det_fec_fin, $fechas_inicio_fin[$valor->par_id_instructor]['todas'])){
							$fechas_inicio_fin[$valor->par_id_instructor]['todas'][] = $valor->pla_fic_det_fec_fin;
						}
					}else{
						$fechas_inicio_fin[$valor->par_id_instructor]['todas'][] = $valor->pla_fic_det_fec_inicio;
						$fechas_inicio_fin[$valor->par_id_instructor]['todas'][] = $valor->pla_fic_det_fec_fin;
					}

					sort($fechas_inicio_fin[$valor->par_id_instructor]['todas']);
				}
				//echo '<pre>'; print_r($fechas_inicio_fin); dd();
				
				foreach($fechas_inicio_fin as $key => $instructor){
					$contador = 1;
					foreach($instructor['todas'] as $llave => $fecha){
						$dia_fecha = date('N', strtotime($fecha));

						if($dia_fecha == 1){
							if(isset($instructor['todas'][($llave+1)])){
								$dia_fecha_siguiente = date('N', strtotime($instructor['todas'][($llave+1)]));
								if($dia_fecha_siguiente == 1){
									$fecha_programar = date('Y-m-d', strtotime($instructor['todas'][($llave+1)]. 'last saturday'));
									$fechas_inicio_fin[$key]['todas'][] = $fecha_programar;
								}
							}
						}else if($dia_fecha == 6){
							if(isset($instructor['todas'][($llave+1)])){
								$dia_fecha_siguiente = date('N', strtotime($instructor['todas'][($llave+1)]));
								if($dia_fecha_siguiente == 6){
									$fecha_programar = date('Y-m-d', strtotime($fecha. 'next monday'));
									$fechas_inicio_fin[$key]['todas'][] = $fecha_programar;
								}
							}
						}
						$contador++;
					}
					sort($fechas_inicio_fin[$key]['todas']);
				}

				foreach($fechas_inicio_fin as $key => $instructor){
					foreach($instructor['todas'] as $llave => $fecha){
						$numero_par_impar = fmod($llave, 2);
						if($numero_par_impar == 0){
							$fechas_inicio_fin[$key]['fecha_inicio'][] = $fecha;
						}else{
							$fechas_inicio_fin[$key]['fecha_fin'][] = $fecha;
						}
					}
				}

				foreach($horario as $key => $valor){
					foreach($fechas_inicio_fin[$valor->par_id_instructor]['fecha_inicio'] as $key1 => $fecha_inicio){
						$fecha_fin = $fechas_inicio_fin[$valor->par_id_instructor]['fecha_fin'][$key1];
						if($valor->pla_fic_det_fec_inicio <= $fecha_inicio and $valor->pla_fic_det_fec_fin > $fecha_inicio){
						    if($valor->pla_tip_id != 5){
    							if(isset($programacion[$valor->par_id_instructor][$fecha_inicio]['horas_programadas'])){
    								$programacion[$valor->par_id_instructor][$fecha_inicio]['horas_programadas'] += $valor->pla_fic_det_hor_totales;
    							}else{
    								$programacion[$valor->par_id_instructor][$fecha_inicio]['horas_programadas'] = $valor->pla_fic_det_hor_totales;
    							}
						    }
						    
							$programacion[$valor->par_id_instructor][$fecha_inicio]["instructor"] = $valor->nombre;
							$programacion[$valor->par_id_instructor][$fecha_inicio]["fecha_fin"] = $fecha_fin;
							$programacion[$valor->par_id_instructor][$fecha_inicio]["fecha_inicio_actividad"][] = $valor->pla_fic_det_fec_inicio;
							$programacion[$valor->par_id_instructor][$fecha_inicio]['fecha_fin_actividad'][] = $valor->pla_fic_det_fec_fin;
							$programacion[$valor->par_id_instructor][$fecha_inicio]["pla_fic_id"][] = $valor->pla_fic_id;
							$programacion[$valor->par_id_instructor][$fecha_inicio]["pla_fic_det_id"][] = $valor->pla_fic_det_id;
							$programacion[$valor->par_id_instructor][$fecha_inicio]['dia_id'][] = $valor->pla_dia_id;
							$programacion[$valor->par_id_instructor][$fecha_inicio]['hInicio'][] = $valor->pla_fic_det_hor_inicio;
							$programacion[$valor->par_id_instructor][$fecha_inicio]['hFin'][] = $valor->pla_fic_det_hor_fin;
							$programacion[$valor->par_id_instructor][$fecha_inicio]['hTotales'][] = $valor->pla_fic_det_hor_totales;
							$programacion[$valor->par_id_instructor][$fecha_inicio]['ambiente'][] = $valor->pla_amb_descripcion;
							$programacion[$valor->par_id_instructor][$fecha_inicio]['id_ambiente'][] = $valor->pla_amb_id;
							$programacion[$valor->par_id_instructor][$fecha_inicio]['ficha'][] = $valor->fic_numero;
							$programacion[$valor->par_id_instructor][$fecha_inicio]['programa'][] =  $valor->prog_nombre;
							$programacion[$valor->par_id_instructor][$fecha_inicio]['trimestre'][] =  $valor->pla_trimestre_numero_ficha;
							$programacion[$valor->par_id_instructor][$fecha_inicio]['sigla'][] =  $valor->prog_sigla;
						}
					}
				}
				//echo '<pre>'; print_r($programacion); dd($programacion);
			}
		}else{
			$fecha_actual = date('Y-m-d');
			$trimestre_actual = DB::select('select pla_fec_tri_id from sep_planeacion_fecha_trimestre where	pla_fec_tri_fec_fin > "'.$fecha_actual.'" and 	pla_fec_tri_fec_inicio < "'.$fecha_actual.'" limit 1');
			if(count($trimestre_actual)>0){
				$pla_fec_tri_id = $trimestre_actual[0]->pla_fec_tri_id;
			}
		}
		$diaOrtografia = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
        $rol = \Auth::user()->participante->rol_id;
        $horasReales=0;
        $fechas_trimestre=0;
		if(!isset($generar)){
			return view('Modules.Seguimiento.Horario.indexInstructor',compact('anioslis','year','par_identificacion_coordinador', 'coordinadores','rol','horasReales','fechas_trimestre','programacion','diaOrtografia','par_identificacion','pla_fec_tri_id','instructores','trimestres'));
		}else{
			return view('Modules.Seguimiento.Horario.indexInstructorPDF',compact('par_identificacion_coordinador', 'coordinadores','rol','horasReales','fechas_trimestre','programacion','diaOrtografia','par_identificacion','pla_fec_tri_id','instructores','trimestres'));
		}
	}
	
	public function getImportar(){
		$sql = '
			select 	fic_numero, prog_nombre
			from 	sep_ficha f, sep_programa pro
			where 	f.prog_codigo = pro.prog_codigo
			and	not fic_numero in(
				select 	fic.fic_numero
				from 	sep_planeacion_ficha pla_fic, sep_ficha fic
				where 	pla_fic.fic_numero = fic.fic_numero)
			and 	not fic_numero like "N%"
			and 	fic_numero > 2000000
			order 	by fic_numero desc, prog_nombre';
		$fichasSinHorario = DB::select($sql);

		$sql = '
			select 	*
			from 	sep_participante par, users u
			where 	par.par_identificacion = u.par_identificacion
			and 	rol_id = 2
			and 	estado = "1" order by par_nombres';
		$instructores = DB::select($sql);

		$anio_actual=date('Y');
		$anio_siguiente=$anio_actual + 1;
		$temporal=$anio_actual + 2;
		
		$sql = '
			select 	*
			from 	sep_planeacion_fecha_trimestre
			where   pla_fec_tri_year in("'.$anio_actual.'", "'.$anio_siguiente.'", "'.$temporal.'") 
			order by pla_fec_tri_fec_inicio asc';
		$trimestres = DB::select($sql);

		$sql = 'select prog_codigo, prog_nombre from sep_programa where not prog_codigo in ("",0,1) order by prog_nombre asc, prog_codigo desc';
		$programas = DB::select($sql);
		
		return view('Modules.Seguimiento.Horario.indexImportar', compact('programas', 'trimestres', 'fichasSinHorario', 'instructores'));
	}
	
	public function postImportar(Request $request){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

		// Validaciones
		$registros = array();
		if(!isset($ficha) or !is_numeric($ficha)){
			$registros['errores'][] = 'El campo <strong>ficha</strong> es obligatoria y debe ser numérico';
		}else{
			if($ficha == 0){
				if(isset($programa)){
					if($programa == ''){
						$registros['errores'][] = 'Debe seleccionar el campo <strong>programa</strong> para las fichas provisionales.';
					}
				}else{
					$registros['errores'][] = 'El campo <strong>programa</strong> es obligatoria.';
				}
			}
		}

		if(!isset($oferta) or ($oferta != 'abierta' and $oferta != 'cerrada')){
			$registros['errores'][] = 'El campo <strong>oferta</strong> debe es obligatoria y debe ser "abierta" o "cerrada"';
		}else{
			$fecha_inicio_lectiva = $fecha_inicio_seleccionar;
			$sql = '
				select * from sep_planeacion_fecha_trimestre
				where pla_fec_tri_fec_inicio = "'.$fecha_inicio_lectiva.'" limit 1';
			$validar = DB::select($sql);
			if(count($validar) == 0){
				$registros['errores'][] = 'El campo <strong>fecha inicio</strong> no coincide con nuestras fechas de inicio.';
			}
		}

		if(!isset($instructor_lider) or !is_numeric($instructor_lider)){
			$registros['errores'][] = 'El campo <strong>instructor líder</strong> es obligatorio y debe ser numérico';
		}

		if(!isset($nuevo) or ($nuevo != 'SI' and $nuevo != 'NO')){
			$registros['errores'][] = 'El campo <strong>Diseño curricular</strong> es obligatorio y debe ser "SI" o "NO"';
		}

		if(!isset($jornada) or ($jornada != 1 and $jornada != 2 and $jornada != 3)){
			$registros['errores'][] = 'El campo <strong>jornada</strong> es obligatorio y debe ser "Mañana", "Tarde" o "Noche".';
		}

		if(!isset($fecha_inicio_seleccionar)){
			$registros['errores'][] = 'El campo <strong>fecha inicio</strong> es obligatorio';
		}
		
		if(!isset($modalidad)){
			$registros['errores'][] = 'El campo <strong>modalidad</strong> es obligatorio';
		}

		// ¿Se ha cargado el archivo CSV?
		if($request->hasFile('archivoCsv')) {
			$archivo = $request->file('archivoCsv');
			if($archivo->getClientOriginalExtension() == 'xlsx') {
				$filename = time() . '-' . $archivo->getClientOriginalName();
				$pathCsv = getPathUploads() . '/CSV/Horario';
				$archivo->move($pathCsv, $filename);
				$validar_registros = leerExcelHorario($pathCsv, $filename);
				if(isset($validar_registros['errores'])){
					foreach($validar_registros['errores'] as $error){
						foreach($error as $valor){
							$registros['errores'][] = $valor;
						}
					}
				}else{
					if(!isset($registros['errores'])){
						$registros = $validar_registros;
					}
				}
			}else{
				$registros['errores'][] = 'El archivo no cumple con el formato esperado - <strong>xlsx</strong>, favor cargar un formato valido';
			} 
		}else{
			$registros['errores'][] = 'No se adjunto ning&uacute;n archivo';
		}

		if(!isset($registros['errores'])){
			$registros['instructor_lider'] = $instructor_lider;
			$registros['pla_tip_ofe_id'] = $oferta;
			$registros['pla_fra_id'] = $jornada;
			$registros['fecha_inicio_lectiva'] = $fecha_inicio_lectiva;
			$registros['nuevo'] = $nuevo;
			$registros['trimestres_lectiva'] = $trimestres_lectiva;
			$registros['trimestres_productiva'] = $trimestres_productiva;
			$registros['modalidad'] = $modalidad;

			// Asignar ficha
			if($ficha != 0){
				$sql = '
					select 	pro.niv_for_id , pro.prog_codigo
					from 	sep_programa pro, sep_ficha fic
					where 	pro.prog_codigo = fic.prog_codigo
					and		fic_numero = "'.$ficha.'" limit 1';
				$nivel_formacion = DB::select($sql);
				$programa = $nivel_formacion[0]->prog_codigo;
				$nivel_formacion = $nivel_formacion[0]->niv_for_id;
				$registros['fic_numero'] = $ficha;
				$registros['nivel_formacion'] = $nivel_formacion;
				if(count($nivel_formacion) !=0){
				    $sql="update sep_ficha set fic_modalidad = ".$modalidad." 
				          where  fic_numero = '".$ficha."'";
				    DB::update($sql);      
				}
			}else{
				$sql = '
					select 	max(fic_numero) as fic_numero
					from 	sep_ficha
					where 	fic_numero like "Nueva%" limit 1';
				$ultimoRegistro = DB::select($sql);
				if(!is_null($ultimoRegistro[0]->fic_numero)){
					$fic_numero = $ultimoRegistro[0]->fic_numero;
					$fic_numero++;
				}else{
					$fic_numero = "Nueva00001";
				}
				
				$par_identificacion_coordinador = '1111111111';
				$sql = '
					select par_identificacion_coordinador
					from sep_apoyo_coordinador
					where par_identificacion_apoyo = "'. \Auth::user()->participante->par_identificacion .'" limit 1';
				$coordinador_asignar = DB::select($sql);
				if(count($coordinador_asignar)>0){
					$par_identificacion_coordinador = $coordinador_asignar[0]->par_identificacion_coordinador;
				}

				$sql = '
					insert into sep_ficha (
						fic_numero, prog_codigo, cen_codigo,
						fic_fecha_inicio, fic_fecha_fin, par_identificacion,
						fic_estado, fic_modalidad, fic_localizacion, fic_version_matriz,
						act_version, fic_proyecto, 	par_identificacion_coordinador,
						fic_duracion_lectiva, fic_duracion_productiva,
						par_identificacion_productiva )
					values (
						"'.$fic_numero.'", "'.$programa.'", "1", "2019/01/01", "2019/01/01",
						"1111111111", "A",'.$modalidad.', "Pondaje", "0",
						"0", "NN", "'.$par_identificacion_coordinador.'", 0,
						0, "1111111111" )';
				DB::insert($sql);

				$sql = '
					select 	niv_for_id
					from 	sep_programa
					where	prog_codigo = '.$programa.' limit 1';
				$nivel_formacion = DB::select($sql);
				$nivel_formacion = $nivel_formacion[0]->niv_for_id;

				$registros['fic_numero'] = $fic_numero;
				$registros['nivel_formacion'] = $nivel_formacion;
			}

			$pla_fic_id = $this->crearHorario($registros, $programa);
			return redirect(url('seguimiento/horario/index?pla_fec_tri_id=todos&par_identificacion_coordinador=&pla_fic_id%5B%5D='.$pla_fic_id));
		}else{
			session()->put('mensajes',$registros['errores']);
			echo "<script> window.history.back(); </script>";
		}
	}

	public function postEliminar(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);
		
		$programacionDetalle = session()->get('programacionDetalle');
		$continue = true;
		$concatenar = ' in (';
		foreach($pla_fic_det as $val1){
			if(is_numeric($val1)){
			    $detalle = $programacionDetalle[$val1];
				$sql = 'select 	pla_fic_id,pla_fic_det_id,pla_dia_id,pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_trimestre_numero_ficha,
								pla_fic_det_fec_inicio,pla_fic_det_fec_fin,pla_fic_det_hor_totales,amb.pla_amb_id,par.par_identificacion
						from 	sep_planeacion_ficha_detalle p_f_d, sep_planeacion_ambiente amb, sep_participante par 
						where 	p_f_d.pla_amb_id = amb.pla_amb_id  and 	p_f_d.par_id_instructor = par.par_identificacion 
						and 	pla_fic_det_id = '.$val1.' limit 1';
				$detalle = DB::select($sql);

				$bitacora = '
				insert into sep_planeacion_ficha_bitacora
				values (default, "'.\Auth::user()->participante->par_identificacion.'", '.$detalle[0]->pla_fic_id.', 
				"'.$detalle[0]->pla_fic_det_fec_inicio.'", "'.$detalle[0]->pla_fic_det_fec_fin.'",
				'.$detalle[0]->pla_fic_det_hor_inicio.', '.$detalle[0]->pla_fic_det_hor_fin.',
				'.$detalle[0]->pla_fic_det_hor_totales.', "'.$detalle[0]->par_identificacion.'",
				'.$detalle[0]->pla_fic_det_id.', '.$detalle[0]->pla_amb_id.', '.$detalle[0]->pla_trimestre_numero_ficha.',
				"Elimino", default)';
				DB::insert($bitacora);	
				$concatenar .= $val1.',';
			}else{
				$continue = false;
			}
		}

		if($continue){
			$concatenar = substr($concatenar,0,-1);
			$concatenar .= ')';
			DB::delete('delete from sep_planeacion_ficha_detalle where pla_fic_det_id '.$concatenar);
		}
	}
	
	public function postEliminartodoelhorario(){
	    $_POST = $this->seguridad($_POST);
		extract($_POST);

		if(!is_numeric($pla_fic_id)){
			dd('El valor recibido no es númerico.');
		}
		$password_DB = \Auth::user()->password;
		$rol = \Auth::user()->participante->rol_id;
		if(password_verify($clave, $password_DB) and ($rol == 0 or $rol == 3 or $rol == 5)) {
			DB::beginTransaction();
				$sql ='delete from sep_planeacion_ficha where pla_fic_id = '.$pla_fic_id;
				DB::delete($sql);
				$sql ='delete from sep_planeacion_ficha_detalle where pla_fic_id = '.$pla_fic_id;
				DB::delete($sql);
				$sql ='delete from sep_planeacion_ficha_actividades where pla_fic_id = '.$pla_fic_id;
				DB::delete($sql);
				$sql ='delete from sep_planeacion_ficha_error where pla_fic_id = '.$pla_fic_id;
				DB::delete($sql);
				$sql ='delete from sep_planeacion_ficha_trimestre where pla_fic_id = '.$pla_fic_id;
				DB::delete($sql);
			DB::commit();
			echo 1;
		}else{
			echo 0;
		}
	}

	public function postMieliminarxd(){
	    if(\Auth::user()->par_identificacion != "1111111111"){
	        dd("Acción no permitida");
	    }else{
	        $_POST = $this->seguridad($_POST);
    		extract($_POST);
    
    		if(!is_numeric($id)){
    			dd('El valor recibido no es númerico.');
    		}
    		//dd($_POST);
    		$sql = "delete from sep_planeacion_ficha where pla_fic_id = $id";
    		DB::delete($sql);
    		$sql = "delete from sep_planeacion_ficha_detalle where pla_fic_id = $id";
    		DB::delete($sql);
    		$sql = "delete from sep_planeacion_ficha_error where pla_fic_id = $id";
    		DB::delete($sql);
    		$sql = "delete from sep_planeacion_ficha_actividades where pla_fic_id = $id";
    		DB::delete($sql);
    
    		return redirect(url("seguimiento/horario/index"));
	    }
	}

	public function getRestriccion(){
		extract($_GET);
		$instructores = DB::select('select par_identificacion, concat(par_nombres," ",par_apellidos) as nombre from sep_participante where rol_id = 2 order by par_nombres');
		
		$anio_actual=date('Y');
		
		if(date('m') == 12){
		   $anio_actual=date('Y')+1;
		}
		$sql = '
			select 	*
			from 	sep_planeacion_fecha_trimestre
			where   pla_fec_tri_year = "'.$anio_actual.'" 
			order by pla_fec_tri_fec_inicio asc';
		$trimestres = DB::select($sql);
		
		$dias = DB::select('select * from sep_planeacion_dia ');
		$ambientesRestriccion = DB::select('select * from sep_planeacion_ambiente where pla_amb_tipo = "Restriccion" and not pla_amb_id = 88 and pla_amb_estado = "Activo" order by pla_amb_descripcion asc');
		return view('Modules.Seguimiento.Horario.restriccion',compact('ambientesRestriccion', 'trimestres','instructores','dias'));
	}

	public function concatenar($array){
		$concatenar = ' in (';
		foreach($array as $val){ $concatenar .= $val.','; }
		$concatenar = substr($concatenar,0,-1);
		$concatenar .= ')';

		return $concatenar;
	}

	public function postRestriccion(){
		extract($_POST);
		//dd($_POST);
		$concatenar = $this->concatenar($id);
		$ambiente_suma_horas = DB::select('select pla_amb_suma_horas from sep_planeacion_ambiente where pla_amb_id = '.$amb_id);
	    $trimestre = DB::select('select pla_fec_tri_id, pla_fec_tri_year, pla_fec_tri_trimestre, pla_fec_tri_fec_inicio, pla_fec_tri_fec_fin  from sep_planeacion_fecha_trimestre where pla_fec_tri_id '.$concatenar);

		$contador_trimestres = count($id);
		$exito = 0;
		
		if(count($trimestre)>0){
			$pla_tip_id = 4;
			if($ambiente_suma_horas[0]->pla_amb_suma_horas == 'NO'){ $pla_tip_id = 5; }
			if($amb_id == 72){ $pla_tip_id = 6; }

			$notificaciones = '<ol>';
			foreach($trimestre as $key => $val){
				$fecha_inicio = $val->pla_fec_tri_fec_inicio;
				$fecha_fin = $val->pla_fec_tri_fec_fin;

				$whereFechas = '
				((pla_fic_det_fec_inicio < "'.$fecha_inicio.'" and (pla_fic_det_fec_fin > "'.$fecha_inicio.'" and pla_fic_det_fec_fin <= "'.$fecha_fin.'"))
				or 	((pla_fic_det_fec_inicio >= "'.$fecha_inicio.'" and pla_fic_det_fec_inicio < "'.$fecha_fin.'") and pla_fic_det_fec_fin > "'.$fecha_fin.'")
				or 	(pla_fic_det_fec_inicio < "'.$fecha_inicio.'" and pla_fic_det_fec_fin > "'.$fecha_fin.'")
				or 	(pla_fic_det_fec_inicio >= "'.$fecha_inicio.'" and pla_fic_det_fec_fin <= "'.$fecha_fin.'"))';

				$instructor = '
					select 	pla_dia_id
					from 		sep_planeacion_ficha_detalle
					where 	((pla_fic_det_hor_inicio < '.$hora_inicio.' and (pla_fic_det_hor_fin > '.$hora_inicio.' and pla_fic_det_hor_fin <= '.$hora_fin.'))
					or	    	((pla_fic_det_hor_inicio >= '.$hora_inicio.' and  pla_fic_det_hor_inicio < '.$hora_fin.') and pla_fic_det_hor_fin > '.$hora_fin.')
					or	    	(pla_fic_det_hor_inicio >= '.$hora_inicio.' and pla_fic_det_hor_fin <= '.$hora_fin.')
					or 	    	(pla_fic_det_hor_inicio < '.$hora_inicio.' and pla_fic_det_hor_fin > '.$hora_fin.'))
					and			'.$whereFechas.'
					and			par_id_instructor = "'.$par_identificacion.'"
					and			pla_dia_id = '.$pla_dia_id.' limit 1';
				$validar = DB::select($instructor);

				if(count($validar) == 0){
					$horasTotales ='
						select 	sum(pla_fic_det_hor_totales) as total
						from 	sep_planeacion_ficha_detalle
						where 	'.$whereFechas.'  and  par_id_instructor = "'.$par_identificacion.'"  and  not pla_tip_id = 5';
					$horasTotales = DB::select($horasTotales);
					$horasTotales = $horasTotales[0]->total;
					//dd($horasTotales);
					$horasInstructor = DB::select('select par_horas_semanales from sep_participante where par_identificacion = "'.$par_identificacion.'" limit 1');
					$horasAColocar = $hora_fin - $hora_inicio;
					$horasSemanalesInstructor = $horasInstructor[0]->par_horas_semanales;
					if($pla_tip_id == 5){
					    $horasSemanalesInstructor = 1000;
					}
					if(($horasTotales + $horasAColocar) <= $horasSemanalesInstructor){
						$sql = '
							insert into	sep_planeacion_ficha_detalle (
								pla_fic_det_id,pla_fic_id,pla_fic_det_fec_inicio,pla_fic_det_fec_fin,
								pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_fic_det_hor_totales,par_id_instructor,
								pla_dia_id,pla_amb_id,pla_trimestre_numero_ficha,pla_tip_id,pla_trimestre_numero_year
							) values	(
								default,0,"'.$fecha_inicio.'","'.$fecha_fin.'",
								"'.$hora_inicio.'","'.$hora_fin.'","'.$horasAColocar.'","'.$par_identificacion.'", 
								'.$pla_dia_id.','.$amb_id.',1,'.$pla_tip_id.',1)';
						DB::insert($sql);
						$exito++;
						$notificaciones .= '<li>La restricción desde el <strong style="color:green;">'.$fecha_inicio.'</strong> hasta el <strong style="color:green;">'.$fecha_fin.'</strong> se registro exitosamente.</li>';
					}else{
						$notificaciones .= '<li>La restricción desde el <strong style="color:red;">'.$fecha_inicio.'</strong> hasta el <strong style="color:red;">'.$fecha_fin.'</strong>, sobrepasa las <strong style="color:red;">'.$horasInstructor[0]->par_horas_semanales.'</strong> horas semanales del instructor, quedaria con <strong style="color:red;">'.($horasTotales+$horasAColocar).'</strong> horas semanales.</li>';
					}
				}else{
					$notificaciones .= '<li>El instructor esta ocupado desde el <strong style="color:red;">'.$fecha_inicio.'</strong> hasta el <strong style="color:red;">'.$fecha_fin.'</strong> en el horario seleccionada.';
				}
			}
			$notificaciones .= '<ol>';

			if($exito == $contador_trimestres){
				$notificaciones = 'Todas las restricciones se registraron exitosamente.';
			}
			echo $notificaciones;
		}
	}

	public function getAgregarcontenido(){
		$dias = DB::select('select * from sep_planeacion_dia');
		$ambientes = DB::select('select pla_amb_id, pla_amb_descripcion from sep_planeacion_ambiente and pla_amb_id not in(69,70,71,72,73)');
		$instructores = DB::select('select par_identificacion, par_nombres, par_apellidos from sep_participante where rol_id = 2 order by par_nombres asc');
		
		return view('Modules.Seguimiento.Horario.agregarContenido',compact('dias','ambientes','instructores'));
	}

	public function postAgregarcontenido(){
		$rol = \Auth::user()->participante->rol_id;
		if($rol == 0 or $rol == 3 or $rol == 5){
    		$_POST = $this->seguridad($_POST);
			extract($_POST);
			if(!is_numeric($fic_id)){
				dd('El valor fic_id debe ser numérico');
			}

			if($tipo_registro == ''){
				dd('El tipo de registro es obligatorio.');
			}

			$sql = '
				select 	pla_fra.pla_fra_id, pla_fra_hor_inicio, pla_fra_hor_fin, niv_for_id
				from 	sep_planeacion_ficha pla_fic, sep_planeacion_franja pla_fra, 
						sep_ficha fic, sep_programa pro
				where 	pla_fic.pla_fra_id = pla_fra.pla_fra_id
				and 	pla_fic.fic_numero = fic.fic_numero
				and 	fic.prog_codigo = pro.prog_codigo
				and 	pla_fic_id = '.$fic_id.' limit 1';
			$datos_ficha = DB::select($sql);
			if(count($datos_ficha) == 0){
				dd('No encontramos los datos de la ficha.');
			}

			$nivel_formacion = $datos_ficha[0]->niv_for_id;
		    if($tipo_registro == 1){
		        $tipo_clase = 2;
				if($nivel_formacion != 1){
					if($lec_dia == 6){
						$jornada_inicio = 6;
						$jornada_fin = 18;
					}else{
						$jornada_inicio = $datos_ficha[0]->pla_fra_hor_inicio;
						$jornada_fin = $datos_ficha[0]->pla_fra_hor_fin;
					}
					
					if($lec_hora_inicio < $jornada_inicio or $lec_hora_fin > $jornada_fin){
						dd('La programación debe estar entre la hora inicio '.$jornada_inicio.' y hora fin '.$jornada_fin);
					}
				}

				if(!is_numeric($lec_par_identificacion)){
					dd('El campo instructor debe ser numérico.');
				}

				$dia[] = $lec_dia;
				$hora_inicio[] = $lec_hora_inicio;
				$hora_fin[] = $lec_hora_fin;
				$par_identificacion[] = $lec_par_identificacion;
				$pla_amb_id[] = $lec_pla_amb_id;
			}else if($tipo_registro == 2){
			    $tipo_clase = 7;
				if($nivel_formacion != 1){
					if($tra_dia == 6){
						$jornada_inicio = 6;
						$jornada_fin = 18;
					}else{
						$franja = $datos_ficha[0]->pla_fra_id;
						if($franja == 1){
							$jornada_inicio = 12;
							$jornada_fin = 18;
						}else if($franja == 2){
							$jornada_inicio = 6;
							$jornada_fin = 12;
						}else{
							$jornada_inicio = 6;
							$jornada_fin = 22;
						}
					}
					
					if($tra_dia == 6 && $tra_pla_amb_id == 123){
						$jornada_inicio = 6;
						$jornada_fin = 22;	
					}

					if($tra_hora_inicio < $jornada_inicio or $tra_hora_fin > $jornada_fin){
						dd('La programación debe estar entre la hora inicio '.$jornada_inicio.' y hora fin '.$jornada_fin);
					}
				}

				if(!is_numeric($tra_par_identificacion)){
					dd('El campo instructor debe ser numérico.');
				}

                $sql = '
					select 	par_id_instructor
					from 	sep_transversal_instructor tra_ins, 
							sep_participante par, users u
					where	tra_ins.par_id_instructor = par.par_identificacion
					and 	par.par_identificacion = u.par_identificacion
					and		rol_id = 2 and not par.par_identificacion = "0"
					and 	estado = "1"
					and 	par.par_identificacion = "'.$tra_par_identificacion.'" limit 1';
				$validar_instructor_transversal = DB::select($sql);
				if(count($validar_instructor_transversal) == 0){
					dd('El Instructor seleccionado no hace parte de los Instructores de transversales o el Instructor está deshabilitado.');
				}

				$sql = '
					select 	jornada_inicio, jornada_fin
					from 	sep_transversal_jornada
					where 	par_id_instructor = "'.$tra_par_identificacion.'"';
				$consulta = DB::select($sql);
				if(count($consulta) > 0){
					$cumple_sigue = false;
					$concatenar_mensaje_jornadas = '';
					foreach($consulta as $val){
						$jornada_inicio_validar = substr($val->jornada_inicio, 0, -3);
						$jornada_fin_validar = substr($val->jornada_fin, 0, -3);
						$concatenar_mensaje_jornadas .= "\n - desde las ".$jornada_inicio_validar.':00 a las '.$jornada_fin_validar.':00 ';
						if($tra_hora_inicio >= $jornada_inicio_validar and $tra_hora_fin <= $jornada_fin_validar){
							$cumple_sigue = true; //break;
						}
					}
					if($cumple_sigue == false){
						dd('El Instructor está habilitado en la(s) siguiente(s) jornada(s):'.$concatenar_mensaje_jornadas);
					}
				}
				
				/*$sql = '
					select 	* 
					from 	sep_transversal_instructor tra_ins, sep_participante par, users u
					where	tra_ins.par_id_instructor = par.par_identificacion
					and 	par.par_identificacion = u.par_identificacion
					and		rol_id = 2 and not par.par_identificacion = "0"
					and 	estado = "1"
					and 	par.par_identificacion = "'.$tra_par_identificacion.'" limit 1';
				$validar_instructor_transversal = DB::select($sql);
				if(count($validar_instructor_transversal) == 0){
					dd('El instructor seleccionado no hace parte de los Instructores de transversales.');
				}*/

				$dia[] = $tra_dia;
				$hora_inicio[] = $tra_hora_inicio;
				$hora_fin[] = $tra_hora_fin;
				$par_identificacion[] = $tra_par_identificacion;
				$pla_amb_id[] = $tra_pla_amb_id;
			}else if($tipo_registro == 3){
			    $tipo_clase = 6;
				if(!is_numeric($pra_par_identificacion)){
					dd('El campo instructor debe ser numérico.');
				}

				$dia[] = $pra_dia;
				$hora_inicio[] = $pra_hora_inicio;
				$hora_fin[] = $pra_hora_fin;
				$par_identificacion[] = $pra_par_identificacion;
				$pla_amb_id[] = 72;
			}else if($tipo_registro == 4){
				$resultado = $this->postComplementario($_POST);
				return $resultado['mensaje'];
			}else{
				dd('El tipo de registro seleccionado no es valido');
			}
			
			$arrayTrimestres = array();
			$exitos = count($dia);
			$contador_exitos = 0;
			$whereFechas = '
				((pla_fic_det_fec_inicio < "'.$fechaInicio.'" and (pla_fic_det_fec_fin > "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))
				or 	((pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_inicio < "'.$fechaFin.'") and pla_fic_det_fec_fin > "'.$fechaFin.'")
				or 	(pla_fic_det_fec_inicio < "'.$fechaInicio.'" and pla_fic_det_fec_fin > "'.$fechaFin.'")
				or 	(pla_fic_det_fec_inicio >= "'.$fechaInicio.'" and pla_fic_det_fec_fin <= "'.$fechaFin.'"))';
			$fila = 1;
			$notificaciones = '<ol>';
				foreach($dia as $key => $val){
					if($hora_inicio[$key] != $hora_fin[$key]){
						if($hora_inicio[$key] < $hora_fin[$key]){
						    $validar = array();
						    if($pla_amb_id[$key] != 72 and $pla_amb_id[$key] != 123){
    							$ambiente = '
    								select 	pla_dia_id
    								from 	sep_planeacion_ficha_detalle
    								where 	((pla_fic_det_hor_inicio < '.$hora_inicio[$key].' and (pla_fic_det_hor_fin > '.$hora_inicio[$key].' and pla_fic_det_hor_fin <= '.$hora_fin[$key].'))
    								or	    	((pla_fic_det_hor_inicio >= '.$hora_inicio[$key].' and  pla_fic_det_hor_inicio < '.$hora_fin[$key].') and pla_fic_det_hor_fin > '.$hora_fin[$key].')
    								or	    	(pla_fic_det_hor_inicio >= '.$hora_inicio[$key].' and pla_fic_det_hor_fin <= '.$hora_fin[$key].')
    								or 	    	(pla_fic_det_hor_inicio < '.$hora_inicio[$key].' and pla_fic_det_hor_fin > '.$hora_fin[$key].'))
    								and		'.$whereFechas.' and  pla_amb_id = '.$pla_amb_id[$key].'  and  pla_dia_id = '.$val.' limit 1';
    							$validar = DB::select($ambiente);
						    }
							if(count($validar) == 0){
								$instructor = '
									select 	pla_dia_id
									from 	sep_planeacion_ficha_detalle
									where 	((pla_fic_det_hor_inicio < '.$hora_inicio[$key].' and (pla_fic_det_hor_fin > '.$hora_inicio[$key].' and pla_fic_det_hor_fin <= '.$hora_fin[$key].'))
									or	    	((pla_fic_det_hor_inicio >= '.$hora_inicio[$key].' and  pla_fic_det_hor_inicio < '.$hora_fin[$key].') and pla_fic_det_hor_fin > '.$hora_fin[$key].')
									or	    	(pla_fic_det_hor_inicio >= '.$hora_inicio[$key].' and pla_fic_det_hor_fin <= '.$hora_fin[$key].')
									or 	    	(pla_fic_det_hor_inicio < '.$hora_inicio[$key].' and pla_fic_det_hor_fin > '.$hora_fin[$key].'))
									and		'.$whereFechas.'  and  par_id_instructor = "'.$par_identificacion[$key].'"  and	 pla_dia_id = '.$val.' limit 1';
								$validar = DB::select($instructor);
								if(count($validar) == 0){
									$grupo = '
										select 	pla_dia_id
										from 	sep_planeacion_ficha_detalle
										where 	((pla_fic_det_hor_inicio < '.$hora_inicio[$key].' and (pla_fic_det_hor_fin > '.$hora_inicio[$key].' and pla_fic_det_hor_fin <= '.$hora_fin[$key].'))
										or	    	((pla_fic_det_hor_inicio >= '.$hora_inicio[$key].' and  pla_fic_det_hor_inicio < '.$hora_fin[$key].') and pla_fic_det_hor_fin > '.$hora_fin[$key].')
										or	    	(pla_fic_det_hor_inicio >= '.$hora_inicio[$key].' and pla_fic_det_hor_fin <= '.$hora_fin[$key].')
										or 	    	(pla_fic_det_hor_inicio < '.$hora_inicio[$key].' and pla_fic_det_hor_fin > '.$hora_fin[$key].'))
										and		'.$whereFechas.'  and  pla_dia_id = '.$val.'  and  pla_fic_id = '.$fic_id.' limit 1';
									$validar = DB::select($grupo);
									if(count($validar) == 0){
										$horasTotales ='
											select 	sum(pla_fic_det_hor_totales) as total
											from 	sep_planeacion_ficha_detalle
											where 	'.$whereFechas.'  and  not pla_tip_id = 5  and  par_id_instructor = "'.$par_identificacion[$key].'"';
										$horasTotales = DB::select($horasTotales);
										$horasTotales = $horasTotales[0]->total;
										$horasInstructor = DB::select('select par_horas_semanales from sep_participante where par_identificacion = "'.$par_identificacion[$key].'" limit 1');
										if(count($horasInstructor)>0){
    										$horasAColocar = $hora_fin[$key] - $hora_inicio[$key];
    										if(($horasTotales + $horasAColocar) <= $horasInstructor[0]->par_horas_semanales){
    											$sql = '
    												insert into sep_planeacion_ficha_detalle
    												values (default,'.$fic_id.',"'.$fechaInicio.'","'.$fechaFin.'",'.$hora_inicio[$key].',
    												'.$hora_fin[$key].','.$horasAColocar.','.$tipo_clase.',"'.$par_identificacion[$key].'",'.$val.',
    												'.$pla_amb_id[$key].','.$trimestre.', 1)';
    											//DB::beginTransaction();
    											DB::insert($sql);
    
    											if(isset($competencia[$key])){
    												foreach($competencia[$key] as $key1 => $val1){
    													if($resultado[$key][$key1] != "" and $actividad[$key][$key1] != "" and $val1 != "" ){
    														$sql = '
    															insert into sep_planeacion_ficha_actividades
    															values (default, "'.$val1.'", "'.$resultado[$key][$key1].'","'.$actividad[$key][$key1].'",
    															"'.$horas_presenciales[$key][$key1].'", '.$fic_id.', "'.$par_identificacion[$key].'", 2, '.$trimestre.', 5)';
    														//DB::insert($sql);
    													}
    												}
    											}
    											//DB::commit();
    											$notificaciones .= '<li>El registro # <strong>'.$fila.'</strong> <strong style="color:green;">SI</strong> se logro exitosamente.</li>';
    											$contador_exitos++;
    										}else{
    										    $notificaciones .= '<li>El registro # <strong>'.$fila.'</strong> <strong style="color:red;">NO</strong> se logro, porque al programar esta actividad sobrepasa las <strong>'.$horasInstructor[0]->par_horas_semanales.'</strong> horas semanales asignadas al instructor quedando con <strong>'.($horasTotales + $horasAColocar).'</strong> horas.</li>';
    										}
										}else{
											$notificaciones .= '<li>El registro # <strong>'.$fila.'</strong> <strong style="color:red;">NO</strong> se logro, porque el número de documento no existe en nuestra base de datos.</li>';
										}
									}else{
										$notificaciones .= '<li>El registro # <strong>'.$fila.'</strong> <strong style="color:red;">NO</strong> se logro, porque el grupo está ocupado.</li>';
									}
								}else{
									$notificaciones .= '<li>El registro # <strong>'.$fila.'</strong> <strong style="color:red;">NO</strong> se logro, porque el instructor ya esta programado en la franja de tiempo seleccionada.</li>';
								}
							}else{
								$notificaciones .= '<li>El registro # <strong>'.$fila.'</strong> <strong style="color:red;">NO</strong> se logro, porque el ambiente esta ocupado en la franja de tiempo seleccionada.</li>';
							}
						}else{
							$notificaciones .= '<li>El registro # <strong>'.$fila.'</strong> <strong style="color:red;">NO</strong> se logro, porque la hora de inicio debe de ser menor a las hora fin.</li>';
						}
					}else{
						$notificaciones .= '<li>El registro # <strong>'.$fila.'</strong> <strong style="color:red;">NO</strong> se logro, porque la hora de inicio y fin no pueden ser iguales.</li>';
					}
					$fila++;
				}
			
			$notificaciones .= '</ol>';
			
			if($exitos == $contador_exitos){
				echo 'Los registros se realizaron exitosamente.';
			}else{
				echo $notificaciones;
			}
		}else{
			echo 'No tienes los permisos para ingresar a esta función';
		}
	}
	
	public function registrarInduccion($pla_fic_id){
		$competencia = 'Promover la interaccion idonea consigo mismo, con los demas y con la naturaleza en los contextos laboral y social.';
		
		$induccion['sena']['nombre'] = 'Inducción - SENA.';
		$induccion['sena']['resultado'][] = 'IDENTIFICAR LAS OPORTUNIDADES QUE EL SENA OFRECE EN EL MARCO DE LA FORMACIÓN PROFESIONAL DE ACUERDO CON EL CONTEXTO NACIONAL E INTERNACIONAL.';
		$induccion['instructor']['nombre'] = 'Inducción - Instructor técnico.';
		$induccion['instructor']['resultado'][] = 'RECONOCER EL ROL DE LOS PARTICIPANTES EN EL PROCESO FORMATIVO, EL PAPEL DE LOS AMBIENTES DE APRENDIZAJE Y LA METODOLOGÍA DE FORMACIÓN, DE ACUERDO CON LA DINÁMICA ORGANIZACIONAL DEL SENA';
		$induccion['instructor']['resultado'][] = 'CONCERTAR ALTERNATIVAS Y ACCIONES DE FORMACIÓN PARA EL DESARROLLO DE LAS COMPETENCIAS DEL PROGRAMA FORMACIÓN, CON BASE EN LA POLÍTICA INSTITUCIONAL.';
		$induccion['etica']['nombre'] = 'Inducción - Ética.';
		$induccion['etica']['resultado'][] = 'ASUMIR LOS DEBERES Y DERECHOS CON BASE EN LAS LEYES Y LA NORMATIVA INSTITUCIONAL EN EL MARCO DE SU PROYECTO DE VIDA.';
		$induccion['tics']['nombre'] = 'Inducción - TICS.';
		$induccion['tics']['resultado'][] = 'GESTIONAR LA INFORMACIÓN DE ACUERDO CON LOS PROCEDIMIENTOS ESTABLECIDOS Y CON LAS TECNOLOGÍAS DE LA INFORMACIÓN Y LA COMUNICACIÓN DISPONIBLES.';
		
		foreach($induccion as $key => $val){
			$nombreActividad = $val['nombre'];
			foreach($val['resultado'] as $key1 => $val1){
				$sql = "
					insert into sep_planeacion_ficha_actividades(
						pla_fic_act_id,pla_fic_act_competencia,
						pla_fic_act_resultado,pla_fic_act_actividad,
						pla_fic_act_horas,pla_fic_id,
						par_id_instructor,pla_tip_id,
						pla_trimestre_numero,fas_id
					)values(
						default,'".$competencia."', 
						'".$val1."','".$nombreActividad."',
						'8',".$pla_fic_id.",
						'1111111111',1,
						'1','5')";
				//DB::insert($sql);
			}
		}
	}
	
	//listado de aprendices por ficha
	public function getListadoaprendices()
	{
		$ficha=$_GET['ficha'];
		$tbody="";
		if (is_numeric($ficha)) {
			$sql="
				select par.par_identificacion, par.par_nombres ,par.par_apellidos , par.par_correo , par.par_telefono
				from sep_participante as par
				left join sep_matricula mat on mat.par_identificacion = par.par_identificacion
				where mat.fic_numero = $ficha and mat.est_id = 2";
			$aprendices= DB::select($sql);
			foreach ($aprendices as $val) {
				$tbody.="<tr><td>".$val->par_identificacion."</td>";
				$tbody.="<td>".$val->par_nombres."</td>";
				$tbody.="<td>".$val->par_apellidos."</td>";
				$tbody.="<td>".$val->par_correo."</td>";
				$tbody.="<td>".$val->par_telefono."</td></tr>";
			}
		}
		return $tbody;
	}
	
	public function getExportaraprendices()
	{
		$ficha = $_GET['ficha'];

		if (is_numeric($ficha)) {
			$sql="select prog.prog_nombre , niv.niv_for_nombre
			from sep_ficha fic , sep_programa prog , sep_nivel_formacion niv
			where prog.prog_codigo = fic.prog_codigo and fic.fic_numero = $ficha and prog.niv_for_id = niv.niv_for_id";
			$programa = DB::select($sql);

			$sql="
				select par.par_identificacion, par.par_nombres ,par.par_apellidos , par.par_correo , par.par_telefono
				from sep_participante as par
				left join sep_matricula mat on mat.par_identificacion = par.par_identificacion
				where mat.fic_numero = $ficha and mat.est_id = 2";
			$aprendices= DB::select($sql);
			
			$sql = "select par.par_nombres , par.par_apellidos , par.par_telefono, par.par_correo
			        from sep_participante par, sep_ficha fic where par.par_identificacion = fic.par_identificacion and fic.fic_numero = ".$ficha;
			$instructor =  DB::select($sql);
			if(count($instructor)>0){
			    $nombres = $instructor[0]->par_nombres." ".$instructor[0]->par_apellidos;
			}else{
			    $nombres = "No tiene instructor lider";
			}
			$filas="";
			$c=1;
			//Creamos las filas
			foreach ($aprendices as $val){
				$filas.="
				<tr>
				<td>".$c++."</td>
				<td>".utf8_decode($val->par_identificacion)."</td>
				<td>".utf8_decode($val->par_nombres)."</td>
				<td>".utf8_decode($val->par_apellidos)."</td>
				<td>".utf8_decode($val->par_correo)."</td>
				<td>".utf8_decode($val->par_telefono)."</td>";
			}
			//Exportamos la tabla
			$tabla = '
			<style>
			table, th, td {
				border: 1px solid black;
				border-collapse: collapse;
				font-family:Arial;
			}
			#campos{ background:#5e83ba; color:white; }
			</style>
			<h2>LISTADO DE APRENDICES</h2>
			<h3>Ficha: '.$ficha.'</h3>
			<h3 style="text-transform: uppercase !important;">Nivel: '.utf8_decode($programa[0]->niv_for_nombre).'</h3>
			<h3>Programa: '.utf8_decode($programa[0]->prog_nombre).'</h3>
			<h3>Instructor lider: '.$nombres.'</h3>
			<table cellspacing="0" cellpadding="0">
			<tr id="campos"><th>C&oacute;digo</th><th>Documento</th><th>Nombres</th><th>Apellidos</th>
			<th>Correo</th><th>Telefono</th>';
			$tabla.=$filas."</table><h4>Power By Setalpro ".date('Y')."</h4>";
		    header('Content-type: application/vnd.ms-excel; charset=utf-8');
			header("Content-Disposition: attachment; filename=LISTADO_APRENDICES_".$ficha.".xls");
			header("Pragma: no-cache");
			header("Expires: 0");
			echo $tabla;
		}
	}
}