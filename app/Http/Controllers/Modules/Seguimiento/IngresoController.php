<?php
namespace App\Http\Controllers\Modules\Seguimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class IngresoController extends Controller {
	private $aforo = 558;
	private $aforoAprendiz = 400;
	private $aforoEmpleado = 158;

	public function __construct(){
		$this->middleware('auth');
		$this->middleware('control_roles');
	}
	
	public function postEliminarprogramacionficha(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

        if(!is_numeric($ing_fic_id)){
			echo 'El valor recibido no es númerico.';
		}else{
    		$rol = \Auth::user()->participante->rol_id;
    		if($rol == 0 or $rol == 3 or $rol == 5 or $rol == 12){
    			$sql = '
    				select 	*
    				from 	sep_ingreso_ficha
    				where 	ing_fic_id = '.$ing_fic_id.' limit 1';
    			$validar_ficha_existe = DB::select($sql);
    			if(count($validar_ficha_existe)>0 and is_numeric($ing_fic_id)){
    				$instructor = $validar_ficha_existe[0]->ing_fic_instructor;
    				$fecha_inicio = $validar_ficha_existe[0]->ing_fic_fecha_inicio;
    				$fecha_fin = $validar_ficha_existe[0]->ing_fic_fecha_fin;
    				$hora_inicio = $validar_ficha_existe[0]->ing_fic_hor_inicio;
    				$hora_fin = $validar_ficha_existe[0]->ing_fic_hor_fin;
    				$franja = $validar_ficha_existe[0]->ing_fic_dia;
    
    				$sql = '
    					select 	ing_hab_id
    					from 	sep_ingreso_habilitar
    					where 	doc_instructor = "'.$instructor.'"
    					and 	fecha_inicio = "'.$fecha_inicio.'"
    					and 	fecha_fin = "'.$fecha_fin.'"
    					and 	hora_inicio = "'.$hora_inicio.'"
    					and 	hora_fin = "'.$hora_fin.'"
    					and 	franja = "'.$franja.'"';
    				$aprendices_habilitados = DB::select($sql);
    				if(count($aprendices_habilitados)>0){
    					$id_aprendices = array();
    					foreach($aprendices_habilitados as $apr){
    						$id_aprendices[] = $apr->ing_hab_id;
    					}
    					$concatenar = implode(',', $id_aprendices);
    					$eliminar_programacion_aprendices ='delete from sep_ingreso_habilitar where ing_hab_id in ('.$concatenar.')';
    				}
    				//dd($eliminar_programacion_aprendices);
    				$eliminar_programacion_instructor ='delete from sep_ingreso_ficha where ing_fic_id = '.$ing_fic_id;
    				if(isset($eliminar_programacion_aprendices)){
    					DB::beginTransaction();
    						DB::delete($eliminar_programacion_aprendices);
    						DB::delete($eliminar_programacion_instructor);
    					DB::commit();
    				}else{
    					DB::beginTransaction();
    						DB::delete($eliminar_programacion_instructor);
    					DB::commit();
    				}
    				
    				echo 1;
    			}else{
    				echo 'La ficha no existe o el código debe ser númerico';
    			}
    		}else{
    			echo 'Rol no permitido para eliminar registros.';
    		}
		}
	}
	
	 public function getEditar(){
	    extract($_REQUEST);
		if(!is_numeric($ing_fic_id2)){
			echo 'El valor recibido no es númerico.';
		}else{
    		$rol = \Auth::user()->participante->rol_id;
    		if($rol == 0 or $rol == 3 or $rol == 12){
    			$sql = '
    				select 	*
    				from 	sep_ingreso_ficha
    				where 	ing_fic_id = '.$ing_fic_id2.' limit 1';
    			$validar_ficha_existe = DB::select($sql);
    			if(count($validar_ficha_existe)>0 and is_numeric($ing_fic_id2)){
    				$instructor = $validar_ficha_existe[0]->ing_fic_instructor;
    				$fecha_inicio = $validar_ficha_existe[0]->ing_fic_fecha_inicio;
    				$fecha_fin = $validar_ficha_existe[0]->ing_fic_fecha_fin;
    				$hora_inicio = $validar_ficha_existe[0]->ing_fic_hor_inicio;
    				$hora_fin = $validar_ficha_existe[0]->ing_fic_hor_fin;
    				$franja = $validar_ficha_existe[0]->ing_fic_dia;
    
    				$sql = '
    					select 	ing_hab_id
    					from 	sep_ingreso_habilitar
    					where 	doc_instructor = "'.$instructor.'"
    					and 	fecha_inicio = "'.$fecha_inicio.'"
    					and 	fecha_fin = "'.$fecha_fin.'"
    					and 	hora_inicio = "'.$hora_inicio.'"
    					and 	hora_fin = "'.$hora_fin.'"
    					and 	franja = "'.$franja.'"';
    				$aprendices_habilitados = DB::select($sql);

					if(count($aprendices_habilitados)>0){
    					$id_aprendices = array();
    					foreach($aprendices_habilitados as $apr){
    						$id_aprendices[] = $apr->ing_hab_id;
    					}
    					$concatenar = implode(',', $id_aprendices);
    					$editar_programacion_aprendices ='update sep_ingreso_habilitar set fecha_inicio="'.$fechaInicio.'" , fecha_fin="'.$fechaFin.'" where ing_hab_id in ('.$concatenar.')';
    				}
    				//dd($editar_programacion_aprendices);
    				$editar_programacion_instructor ='update sep_ingreso_ficha set ing_fic_fecha_inicio="'.$fechaInicio.'", ing_fic_fecha_fin="'.$fechaFin.'"  where ing_fic_id = '.$ing_fic_id2;
    				if(isset($editar_programacion_aprendices)){
    						DB::update($editar_programacion_aprendices);
    						DB::update($editar_programacion_instructor);
    				}else{
    						DB::update($editar_programacion_instructor);
    				}
    				echo 1;
    			}else{
    				echo 'La ficha no existe o el código debe ser númerico';
    			}
    		}else{
    			echo 'Rol no permitido para eliminar registros.';
    		}
		}	
	}
	
	public function getProgramarpersona(){
		$_GET = $this->seguridad($_GET);
		extract($_GET);

		$sql = '
			select 	ing_hab_id, ing_est_documento, ing_est_nombre, ing_est_rol, ambiente,
					fecha_inicio, fecha_fin, hora_inicio, hora_fin, franja, ing_est_ficha
			from 	sep_ingreso_estado est left join sep_ingreso_habilitar hab
			on 		est.ing_est_documento = hab.doc_aprendiz
			where 	ing_est_documento =  "'.$documento.'"';
		$programacion_sin_ambiente = DB::select($sql);
		$datos = array();
		if(count($programacion_sin_ambiente)>0){
			$nombre = $programacion_sin_ambiente[0]->ing_est_nombre;
			$rol = $programacion_sin_ambiente[0]->ing_est_rol;
			if($rol == 'Instructor'){
				$sql = '
					select *,
					(	select ing_hab_id
						from sep_ingreso_habilitar
						where doc_aprendiz = ing_fic_instructor
						and ing_fic_fecha_inicio = fecha_inicio
						and ing_fic_hor_inicio = hora_inicio and ing_fic_dia = franja) as ing_hab_id
					from sep_ingreso_ficha fic  where ing_fic_instructor = "'.$documento.'"
					order by ing_fic_fecha_inicio desc';
				$programacion = DB::select($sql);
				foreach($programacion as $pro){
					$datos['ing_hab_id'][] = $pro->ing_hab_id;
					$datos['fecha_inicio'][] = $pro->ing_fic_fecha_inicio;
					$datos['fecha_fin'][] = $pro->ing_fic_fecha_fin;
					$datos['franja'][] = $pro->ing_fic_hor_inicio.' - '.$pro->ing_fic_hor_fin;
					$datos['dia'][] = $pro->ing_fic_dia;
					$datos['ficha'][] = $pro->fic_numero;
					$datos['ambiente'][] = $pro->ing_fic_ambiente;
					$datos['boton'][] = '';
				}
			}else if($rol == 'Aprendiz'){
				$sql = '
					select *
					from sep_ingreso_habilitar hab, sep_ingreso_ficha fic
					where hab.doc_instructor = fic.ing_fic_instructor
					and doc_instructor = ing_fic_instructor
					and ing_fic_fecha_inicio = fecha_inicio
					and ing_fic_hor_inicio = hora_inicio
					and ing_fic_dia = franja
					and doc_aprendiz = "'.$documento.'"
					group by ing_hab_id';
				$programacion = DB::select($sql);
				
				foreach($programacion as $pro){
					$datos['ing_hab_id'][] = $pro->ing_hab_id;
					$datos['fecha_inicio'][] = $pro->ing_fic_fecha_inicio;
					$datos['fecha_fin'][] = $pro->ing_fic_fecha_fin;
					$datos['franja'][] = $pro->ing_fic_hor_inicio.' - '.$pro->ing_fic_hor_fin;
					$datos['dia'][] = $pro->ing_fic_dia;
					$datos['ficha'][] = $pro->fic_numero;
					$datos['ambiente'][] = $pro->ing_fic_ambiente;
					$datos['boton'][] = '';
				}
			}
			
			if(isset($datos['ing_hab_id'])){
				foreach($programacion_sin_ambiente as $pro){
					if(!in_array($pro->ing_hab_id, $datos['ing_hab_id'])){
						$datos['ing_hab_id'][] = $pro->ing_hab_id;
						$datos['fecha_inicio'][] = $pro->fecha_inicio;
						$datos['fecha_fin'][] = $pro->fecha_fin;
						$datos['franja'][] = $pro->hora_inicio.' - '.$pro->hora_fin;
						$datos['dia'][] = $pro->franja;
						$datos['ficha'][] = $pro->ing_est_ficha;
						$datos['ambiente'][] = $pro->ambiente;
						$datos['boton'][] = '<a class="botonHorario" data-id="'.$pro->ing_hab_id.'"">Eliminar</a>';
					}
				}
			}else{
				foreach($programacion_sin_ambiente as $pro){
					if($pro->ing_hab_id != ''){
						$datos['ing_hab_id'][] = $pro->ing_hab_id;
						$datos['fecha_inicio'][] = $pro->fecha_inicio;
						$datos['fecha_fin'][] = $pro->fecha_fin;
						$datos['franja'][] = $pro->hora_inicio.' - '.$pro->hora_fin;
						$datos['dia'][] = $pro->franja;
						$datos['ficha'][] = $pro->ing_est_ficha;
						$datos['ambiente'][] = $pro->ambiente;
						$datos['boton'][] = '<a class="botonHorario" data-id="'.$pro->ing_hab_id.'">Eliminar</a>';
					}
				}
			}

			if(isset($datos['ing_hab_id'])){
				array_multisort($datos['fecha_inicio'], SORT_DESC, $datos['fecha_fin'], $datos['franja'], $datos['dia'], $datos['ficha'], $datos['ambiente'], $datos['ing_hab_id'], $datos['boton']);
			}
			
			$ambientes = DB::select('select * from sep_ingreso_ambiente order by descripcion');
		}else{
			dd('El número de documento no existe');
		}
		
		return view('Modules.Seguimiento.Ingreso.programarPersona', compact('ambientes', 'datos', 'documento', 'nombre', 'rol'));
	}
	
	public function postProgramarpersona(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);
		//dd($_POST);

		$rol = \Auth::user()->participante->rol_id;
		if($rol == 12 or $rol == 0){
			if(!isset($dia)){
				$mensaje[] = 'El campo <strong>días</strong> es oblgatorio.';
			}else if($hora_inicio >= $hora_fin){
				$mensaje[] = 'La <strong>hora inicio</strong> debe ser menor a la <strong>hora fin</strong>';
			}else if($fechaInicio > $fechaFin){
				$mensaje[] = 'La <strong>fecha inicio</strong> debe ser menor a la <strong>fecha fin</strong>';
			}else{
				// 	Que rol tiene la persona ?
				$mensaje = array();
				$sql = '
					select 	ing_est_rol
					from 	sep_ingreso_estado
					where 	ing_est_documento = "'.$documento.'" limit 1';
				$validar = DB::select($sql);
				if(count($validar)>0){
					$rol = $validar[0]->ing_est_rol;
					$dia_letra = implode('-', $dia);
					$dia_llave = $this->llaves_dias($dia_letra);
					$concatenar = ' and (';
					foreach($dia as $fra){
						if($fra == 'M'){
							$concatenar .= " (franja like '%-M-%' or franja like 'M' or franja like 'M-%' or franja like '%-M') ";
						}else{
							$concatenar .= ' franja like "%'.$fra.'%" ';
						}

						if(end($dia) != $fra){
							$concatenar .= ' or ';
						}
					}
					$concatenar .= ')';

					// Validamos que no se cruze con otra programación de la persona.
					$sql = '
						select	doc_aprendiz
						from	sep_ingreso_habilitar
						where 	((fecha_inicio >= "'.$fechaInicio.'" and fecha_inicio <= "'.$fechaFin.'") or
								(fecha_fin >= "'.$fechaInicio.'" and fecha_fin <= "'.$fechaFin.'") or
								(fecha_inicio < "'.$fechaInicio.'" and fecha_fin > "'.$fechaFin.'")) '.$concatenar.'
						and 	doc_aprendiz = "'.$documento.'" limit 1';
					$validar = DB::select($sql);

					if($rol == 'Instructor'){
						$concatenar_instructor = ' and (';
						foreach($dia as $fra){
							if($fra == 'M'){
								$concatenar_instructor .= " (ing_fic_dia like '%-M-%' or ing_fic_dia like 'M' or ing_fic_dia like 'M-%' or ing_fic_dia like '%-M') ";
							}else{
								$concatenar_instructor .= ' ing_fic_dia like "%'.$fra.'%" ';
							}

							if(end($dia) != $fra){
								$concatenar_instructor .= ' or ';
							}
						}
						$concatenar_instructor .= ')';
						$sql = '
							select 	*
							from 	sep_ingreso_ficha
							where	((ing_fic_fecha_inicio >= "'.$fechaInicio.'" and ing_fic_fecha_inicio <= "'.$fechaFin.'") or
									(ing_fic_fecha_fin >= "'.$fechaInicio.'" and ing_fic_fecha_fin <= "'.$fechaFin.'") or
									(ing_fic_fecha_inicio < "'.$fechaInicio.'" and ing_fic_fecha_fin > "'.$fechaFin.'")) '.$concatenar_instructor.'
							and		ing_fic_instructor = "'.$documento.'" limit 1';
						$validar_instructor = DB::select($sql);
					}else{
						$validar_instructor = array();
					}

					if(count($validar) == 0 and count($validar_instructor) == 0){
						$aforo = $this->aforoAprendiz;
						$concatenar_rol = ' and ing_est_rol in ("Instructor", "Aprendiz")';
						if($rol == 'Empleado'){
							$aforo = $this->aforoEmpleado;
							$concatenar_rol = ' and not ing_est_rol in ("Instructor", "Aprendiz")';
						}

						// Validamos el aforo en la fecha seleccionada
						$sql = '
							select	count(doc_aprendiz) total
							from	sep_ingreso_habilitar hab, sep_ingreso_estado est
							where 	hab.doc_aprendiz = est.ing_est_documento
							and		((fecha_inicio >= "'.$fechaInicio.'" and fecha_inicio <= "'.$fechaFin.'") or
									(fecha_fin >= "'.$fechaInicio.'" and fecha_fin <= "'.$fechaFin.'") or
									(fecha_inicio < "'.$fechaInicio.'" and fecha_fin > "'.$fechaFin.'"))
							and 	((hora_inicio >= "'.$hora_inicio.'" and hora_inicio <= "'.$hora_fin.'") or
									(hora_fin >= "'.$hora_inicio.'" and hora_fin <= "'.$hora_fin.'") or
									(hora_inicio < "'.$hora_inicio.'" and hora_fin > "'.$hora_fin.'")) '.$concatenar.' '.$concatenar_rol.'';
						$validar = DB::select($sql); 
						$aforo_fecha = $validar[0]->total;
						if($aforo_fecha < $aforo){
							$programar = true;
							if($ambiente != 0){
								$sql = 'select descripcion, aforo from sep_ingreso_ambiente where id = '.$ambiente.' limit 1';
								$ambiente = DB::select($sql);
								$ambiente_descripcion = $ambiente[0]->descripcion;
								$ambiente_aforo = $ambiente[0]->aforo;

								$sql = '
									select	count(doc_aprendiz) total
									from	sep_ingreso_habilitar hab, sep_ingreso_estado est
									where 	hab.doc_aprendiz = est.ing_est_documento
									and		((fecha_inicio >= "'.$fechaInicio.'" and fecha_inicio <= "'.$fechaFin.'") or
											(fecha_fin >= "'.$fechaInicio.'" and fecha_fin <= "'.$fechaFin.'") or
											(fecha_inicio < "'.$fechaInicio.'" and fecha_fin > "'.$fechaFin.'"))
									and 	((hora_inicio >= "'.$hora_inicio.'" and hora_inicio <= "'.$hora_fin.'") or
											(hora_fin >= "'.$hora_inicio.'" and hora_fin <= "'.$hora_fin.'") or
											(hora_inicio < "'.$hora_inicio.'" and hora_fin > "'.$hora_fin.'")) '.$concatenar.'
									and		ambiente like "%'.$ambiente_descripcion.'%" ';
								$validar = DB::select($sql);
								$aforo_ambiente = $validar[0]->total;
								if($aforo_ambiente >= $ambiente_aforo){
									$programar = false;
								}
								$ambiente = '"'.$ambiente_descripcion.'"';
							}else{
								$ambiente = 'null';
							}

							if($programar == true){
								$sql = '
									insert into sep_ingreso_habilitar
									(ing_hab_id, doc_aprendiz, doc_instructor, fecha_inicio, fecha_fin, hora_inicio, hora_fin, franja, ambiente, estado)
									values
									(default,"'.$documento.'","'.$documento.'","'.$fechaInicio.'", "'.$fechaFin.'", "'.$hora_inicio.'","'.$hora_fin.'","'.$dia_letra.'", '.$ambiente.',"si")';
								DB::insert($sql);
							}else{
								$mensaje[] = 'El aforo del ambiente está completo en la fecha seleccionada.';
							}
						}else{
						    if($rol == 'Empleado'){
								$mensaje[] = 'El aforo de <strong>Empleado</strong> está completo en las fechas seleccionadas';
							}else{
								$mensaje[] = 'El aforo de <strong>Instructor y Aprendiz</strong> está completo en las fechas seleccionadas';
							}
						}
					}else{
						$mensaje[] = 'La persona ya está programada en las fechas seleccionadas.';
					}
				}else{
					$mensaje[] = 'El número de documento no está registrado en nuestra base de datos.';
				}
			}

			if(count($mensaje) == 0){
				$mensaje[] = 'Registro realizado exitosamente';
			}
		}else{
			$mensaje[] = 'Rol no permitido para eliminar registros.';
		}
		
		session()->put('mensajes',$mensaje);
		echo "
		<script>
			window.history.back();
		</script>";
	}

	public function postEliminarprogramacionpersona(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

        if(!is_numeric($ing_fic_id)){
			$mensaje[] = 'El Id no es númerico.';
			echo 0;
		}else{
		    $rol = \Auth::user()->participante->rol_id;
    		if($rol == 12 or $rol == 0){
    			$sql ='delete from sep_ingreso_habilitar where ing_hab_id = '.$ing_fic_id;
    			$mensaje[] = 'Registro eliminado exitosamente.';
    			DB::delete($sql);
    			echo 1;
    		}else{
    			$mensaje[] = 'Rol no permitido para eliminar registros.';
    			echo 0;
    		}
		}
		
		session()->put('mensajes', $mensaje);
	}
	
	
	public function getReporteprogramado(){
		return view('Modules.Seguimiento.Ingreso.reporteProgramado');
	}

    public function postReporteprogramado(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

		if(!isset($fecha_inicio) or !isset($fecha_fin)){
			echo 'La fecha inicio y fecha sin son obligatorias.'; dd();
		}
		
		$anio = substr($fecha_inicio,0,4);
		$mes = substr($fecha_inicio,5,2);
		$dia = substr($fecha_inicio,8,2);
		// Anio mes y día sean númericos en la fecha inicio
		if(!is_numeric($anio) or !is_numeric($mes) or !is_numeric($dia)){
			echo 'Año, mes y día de la fecha inicio deben ser númericos'; dd();
		}

		$anio = substr($fecha_fin,0,4);
		$mes = substr($fecha_fin,5,2);
		$dia = substr($fecha_fin,8,2);
		// Anio mes y día sean númericos en la fecha inicio
		if(!is_numeric($anio) or !is_numeric($mes) or !is_numeric($dia)){
			echo 'Año, mes y día de la fecha fin deben ser númericos'; dd();
		}

		if($fecha_inicio > $fecha_fin){
			echo 'La fecha inicio debe ser menor a la fecha fin.'; dd();
		}

		$fecha_actual = date('Y-m-d');
		$hora_actual = date('h:i a');
		
		$sql = '
			select 	ing_est_rol, ing_est_tip_documento, ing_est_documento,
					ing_est_nombre, ing_est_telefono, ing_est_correo, ing_est_ficha
			from 	sep_ingreso_habilitar hab, sep_ingreso_estado est
			where	hab.doc_aprendiz = est.ing_Est_documento
			and 	hab.estado = "si"
			and 	((fecha_inicio >= "'.$fecha_inicio.'" and fecha_inicio <= "'.$fecha_fin.'") or
					(fecha_fin >= "'.$fecha_inicio.'" and fecha_fin <= "'.$fecha_fin.'") or
					(fecha_inicio < "'.$fecha_inicio.'" and fecha_fin > "'.$fecha_fin.'"))
			group by ing_est_documento
			order by ing_est_rol, ing_est_ficha, ing_est_nombre';
		$registros = DB::select($sql);

		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename=Reporte_programados_'.$fecha_actual.'.csv');
		header('Pragma: no-cache');
		header("Expires: 0");

		echo ";F reporte:;".$fecha_actual.";H reporte:;".$hora_actual."\n";
		echo ";Desde:;".$fecha_inicio.";Hasta:;".$fecha_fin."\n";
		echo "Rol;Tip. documento;Documento;Nombre;".utf8_decode('Teléfono').";Correo;Ficha\n";

		if(count($registros)>0){
			foreach($registros as $reg){
				echo $reg->ing_est_rol.";";
				echo $reg->ing_est_tip_documento.";";
				echo $reg->ing_est_documento.";";
				echo utf8_decode($reg->ing_est_nombre).";";
				echo $reg->ing_est_telefono.";";
				echo $reg->ing_est_correo.";";
				echo $reg->ing_est_ficha.";";
				echo "\n";
			}
		}else{
			echo utf8_decode('No hay información en los rangos de fechas seleccionados.');
		}
	}
	
	public function postProgramarver(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

		$sql = '
			select 	*
			from 	sep_ingreso_estado
			where 	ing_est_documento = "'.$documento.'" limit 1';
		$rol = DB::select($sql);
		if(count($rol)>0){
			$datos = array();
			$rol = $rol[0]->ing_est_rol;
			if($rol == 'Instructor'){
				$sql =  '
					select 	*
					from 	sep_ingreso_ficha left join sep_ingreso_estado
					on 		sep_ingreso_ficha.ing_fic_instructor = sep_ingreso_estado.ing_est_documento
					where 	ing_fic_instructor =  "'.$documento.'"';
				$programacion = DB::select($sql);
				if(count($programacion)>0){
					foreach($programacion as $val){
						$datos['dia'][] = $val->ing_fic_dia;
						$datos['franja'][] = $val->ing_fic_hor_inicio.' - '. $val->ing_fic_hor_fin;
						$datos['ficha'][] = $val->fic_numero;
						$datos['fecha_inicio'][] = $val->ing_fic_fecha_inicio;
						$datos['fecha_fin'][] = $val->ing_fic_fecha_fin;
						$datos['nombre'][] = $val->ing_est_nombre;
					}
				}
			}

			$sql =  '
				select 	*
				from 	sep_ingreso_habilitar left join sep_ingreso_estado
				on 		sep_ingreso_habilitar.doc_aprendiz = sep_ingreso_estado.ing_est_documento
				where 	doc_aprendiz =  "'.$documento.'"';
			$programacion = DB::select($sql);
			if(count($programacion)>0){
				if(count($datos)>0){
					foreach($programacion as $val){
						if(!in_array($val->franja, $datos['dia']) and !in_array($val->franja, $datos['franja']) and !in_array($val->franja, $datos['fecha_inicio']) and !in_array($val->franja, $datos['fecha_fin'])){
							$datos['dia'][] = $val->franja;
							$datos['franja'][] = $val->hora_inicio.' - '. $val->hora_fin;
							$datos['ficha'][] = '';
							$datos['fecha_inicio'][] = $val->fecha_inicio;
							$datos['fecha_fin'][] = $val->fecha_fin;
							$datos['nombre'][] = $val->ing_est_nombre;
						}
					}
				}else{
					foreach($programacion as $val){
						$datos['dia'][] = $val->franja;
						$datos['franja'][] = $val->hora_inicio.' - '. $val->hora_fin;
						$datos['ficha'][] = '';
						$datos['fecha_inicio'][] = $val->fecha_inicio;
						$datos['fecha_fin'][] = $val->fecha_fin;
						$datos['nombre'][] = $val->ing_est_nombre;
					}
				}
			}

			if(count($datos)>0){
				$contador = 1;
				foreach($datos['franja'] as $key => $val){
					echo '
					<tr>
						<td>'.$contador++.'</td>
						<td>'.$datos['nombre'][$key].'</td>
						<td>'.$datos['ficha'][$key].'</td>
						<td>'.$datos['dia'][$key].'</td>
						<td>'.$datos['franja'][$key].'</td>
						<td>'.$datos['fecha_inicio'][$key].'</td>
						<td>'.$datos['fecha_fin'][$key].'</td>
					</tr>';
				}
			}else{
				echo '
					<tr>
						<td colspan="7">Sin registro</td>
					</tr>';
			}
		}else{
			echo '
				<tr>
					<td colspan="7">El número de documento no existe en nuestra base de datos</td>
				</tr>';
		}
	}
	
	
	public function postEliminar(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

		if(!is_numeric($ing_fic_id)){
			echo 'El valor recibido no es númerico.';
		}else{
			$password_DB = \Auth::user()->password;
			$rol = \Auth::user()->participante->rol_id;
			if(password_verify($clave, $password_DB)) {
				if($rol == 0 or $rol == 3 or $rol == 5){
					$par_identificacion = \Auth::user()->participante->par_identificacion;
					$sql = '
						select 	*
						from 	sep_ingreso_habilitar_ficha hab_fic, sep_ingreso_ficha fic
						where 	fic.fic_numero = hab_fic.ficha
						and		coordinador = "'.$par_identificacion.'"
						and		ing_fic_id = '.$ing_fic_id.' limit 1';
					$validar_ficha_asignada = DB::select($sql);
					if(count($validar_ficha_asignada)>0){
						$instructor = $validar_ficha_asignada[0]->ing_fic_instructor;
						$fecha_inicio = $validar_ficha_asignada[0]->ing_fic_fecha_inicio;
						$fecha_fin = $validar_ficha_asignada[0]->ing_fic_fecha_fin;
						$hora_inicio = $validar_ficha_asignada[0]->ing_fic_hor_inicio;
						$hora_fin = $validar_ficha_asignada[0]->ing_fic_hor_fin;
						$franja = $validar_ficha_asignada[0]->ing_fic_dia;

						$sql = '
							select 	ing_hab_id
							from 	sep_ingreso_habilitar
							where 	doc_instructor = "'.$instructor.'"
							and 	fecha_inicio = "'.$fecha_inicio.'"
							and 	fecha_fin = "'.$fecha_fin.'"
							and 	hora_inicio = "'.$hora_inicio.'"
							and 	hora_fin = "'.$hora_fin.'"
							and 	franja = "'.$franja.'"';
						$aprendices_habilitados = DB::select($sql);
						if(count($aprendices_habilitados)>0){
							$id_aprendices = array();
							foreach($aprendices_habilitados as $apr){
								$id_aprendices[] = $apr->ing_hab_id;
							}
							$concatenar = implode(',', $id_aprendices);
							$eliminar_programacion_aprendices ='delete from sep_ingreso_habilitar where ing_hab_id in ('.$concatenar.')';
						}
						
						$eliminar_programacion_instructor ='delete from sep_ingreso_ficha where ing_fic_id = '.$ing_fic_id;
						if(isset($eliminar_programacion_aprendices)){
							DB::beginTransaction();
								DB::delete($eliminar_programacion_aprendices);
								DB::delete($eliminar_programacion_instructor);
							DB::commit();
						}else{
							DB::beginTransaction();
								DB::delete($eliminar_programacion_instructor);
							DB::commit();
						}
						
						echo 1;
					}else{
						echo 'La ficha no pertenece a Usted';
					}
				}else{
					echo 'Rol no permitido para eliminar registros.';
				}
			}else{
				echo 'Contraseña no valida';
			}
		}
	}
	
    public function getProgramar(){
		$documentoLoguiado = \Auth::user()->participante->par_identificacion;
		$rol = \Auth::user()->participante->rol_id;
		$sql = 'select * from sep_apoyo_coordinador';
		$apoyos = DB::select($sql);
		foreach($apoyos as $apo){
			$arra_apoyos[$apo->par_identificacion_apoyo] = $apo->par_identificacion_coordinador;
		}

		$concatenar = '';
		if(!in_array($documentoLoguiado, $arra_apoyos)){
			if(isset($arra_apoyos[$documentoLoguiado])){
				$concatenar = ' and fic.par_identificacion_coordinador = "'.$arra_apoyos[$documentoLoguiado].'"';
			}
		}else{
			$concatenar = ' and fic.par_identificacion_coordinador = "'.$documentoLoguiado.'"';
		}

		if($rol == 12){
			$concatenar = '';
		}
		$fechaActual = date('Y').'-01-01';

		$sql = '
			select 	ing_fic_id, ing_fic.fic_numero, prog_nombre, ing_fic_aforo,
					ing_fic_fecha_inicio, ing_fic_fecha_fin, ing_fic_ambiente,
					ing_fic_dia, ing_fic_hor_inicio, ing_fic_hor_fin, par.par_identificacion,
					substring(prog_nombre, 1, 20) as programa_corto,
					substring_index(par.par_apellidos," ",1) as par_apellido_corto,
					substring_index(par.par_nombres," ",1) as par_nombre_corto
			from 	sep_ingreso_ficha ing_fic, sep_ficha fic, sep_programa pro, sep_participante par
			where 	ing_fic.fic_numero = fic.fic_numero
			and 	ing_fic.ing_fic_instructor = par.par_identificacion
			and     ing_fic_fecha_inicio >= "'.$fechaActual.'"
			and 	fic.prog_codigo = pro.prog_codigo '.$concatenar.'
			order by ing_fic_fecha_fin desc, ing_fic_ambiente desc, ing_fic_hor_inicio, ing_fic_dia';
		$fichas_programadas = DB::select($sql);

	/*	$sql = '
			select 	doc_instructor, fecha_inicio, hora_inicio, franja, count(doc_aprendiz) as total
			from 	sep_ingreso_habilitar hab
			where 	doc_instructor != doc_aprendiz
			and     fecha_inicio >= "'.$fechaActual.'"
			group by doc_instructor, fecha_inicio, hora_inicio, franja  
			order by doc_instructor, ing_hab_id';
		$consulta = DB::select($sql);
		$totalAprendicesProgramados = [];
		foreach($consulta as $apr){
			$totalAprendicesProgramados[$apr->doc_instructor][$apr->fecha_inicio][$apr->hora_inicio][$apr->franja] = $apr->total;
		}
		
		$sql = '
			select 	fic.fic_numero, prog_nombre, count(fic.fic_numero) as aprendices , pro.prog_sigla
			from 	sep_ingreso_estado est, sep_ficha fic, sep_programa pro
			where 	est.ing_est_ficha = fic.fic_numero
			and 	fic.prog_codigo = pro.prog_codigo
			and		est.ing_est_rol = "Aprendiz"
			and 	est.ing_est_ingresa = "si" '.$concatenar.'
			group by ing_est_ficha
			order by prog_nombre, fic.fic_numero';*/
			
        $sql = '
			select fic.fic_numero,pro.prog_nombre, count(mat.fic_numero) as aprendices , pro.prog_sigla
			from sep_ficha fic, sep_programa pro , sep_matricula mat
			where fic.prog_codigo = pro.prog_codigo 
			and mat.fic_numero = fic.fic_numero 
			and mat.est_id = 2 '.$concatenar.'
			group by fic.fic_numero
			order by pro.prog_nombre, fic.fic_numero';			
		$fichas_habilitadas = DB::select($sql);

		$sql = 'select * from sep_ingreso_ambiente order by descripcion asc';
		$ambiente = DB::select($sql);
		
		$sql = '
			select 	*
			from 	sep_participante par, users u, sep_ingreso_estado est
			where 	par.par_identificacion = u.par_identificacion
			and 	par.par_identificacion = est.ing_est_documento
			and		ing_est_ingresa = "si"
			and 	rol_id = 2
			and 	estado = "1" order by par_nombres';
		$instructores = DB::select($sql);

		return view('Modules.Seguimiento.Ingreso.programar', compact('documentoLoguiado', 'fichas_habilitadas', 'instructores', 'ambiente', 'fichas_programadas'));
	}

    public function postProgramar(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

		// Validaciones
		$mensaje = array();
		$dia_actual = date("N", strtotime(date('Y-m-d')));
		$hora_actual = date('H:i');
		$rol = \Auth::user()->participante->rol_id;
		
	/*	if($rol != 0 and $rol != 12){
			if($dia_actual > 4){
				$mensaje[] = 'La programación se puede realizar desde el <strong>lunes</strong> hasta el <strong>jueves</strong> a las <strong>16:00</strong>';
			}else if($dia_actual == 4){
				if($hora_actual > '16:00'){
					$mensaje[] = 'La programación se puede realizar desde el <strong>lunes</strong> hasta el <strong>jueves</strong> a las <strong>16:00</strong>';
				}
			}
		}*/

		if(count($mensaje) == 0){
			if(!isset($fechaInicio) or !isset($fechaFin)){
				$mensaje[] = 'La <strong>fecha inicio</strong> y la <strong>fecha fin</strong> son obligatorias.';
			}else{
				if($fechaInicio > $fechaFin){
					$mensaje[] = 'La fecha inicio debe ser menor o igual a la fecha fin.';
				}else{
					$validar_fecha_inicio = date("N", strtotime($fechaInicio));
					$validar_fecha_fin = date("N", strtotime($fechaFin));

					if($validar_fecha_inicio != 1){
						$mensaje[] = 'El día de la <strong>fecha inicio</strong> debe ser lunes.';
					}else if($validar_fecha_inicio == 1){
						if($rol != 0 and $rol != 12){
							$proximo_lunes = date("Y-m-d", strtotime(date("Y-m-d").' next monday'));
							if($fechaInicio < $proximo_lunes){
								$mensaje[] = 'La <strong>fecha inicio</strong> debe ser mayor o igual a la del próximo lunes.';
							}
						}
					}

					if($validar_fecha_fin != 6){
						$mensaje[] = 'El día de la <strong>fecha fin</strong> debe ser sábado.';
					}else if($validar_fecha_fin == 6){
						if($rol != 0 and $rol != 12){
							$proximo_sabado = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d").' next monday'))));
							if($fechaFin < $proximo_sabado){
								$mensaje[] = 'La <strong>fecha fin</strong> debe ser mayor o igual a la del próximo sábado.';
							}
						}
					}
				}
			}
		
			if(!isset($dia)){
				$mensaje[] = 'El campo de <strong>días</strong> es obligatorio.';
			}

			$sql = '
				select  *
				from 	sep_ingreso_ambiente
				where 	id = '.$ambiente.' limit 1';
			$validar_ambiente = DB::select($sql);
			if(count($validar_ambiente) == 0){
				$mensaje[] = 'El c&oacute;digo del <strong>Ambiente</strong> no existe.';
			}

			if(count($mensaje) == 0){
				$puedeSeguir = true;
				$dia_letra = implode('-', $dia);
				$dia_llave = $this->llaves_dias($dia_letra);

				$contador = (count($dia)-1);
				$concatenar = ' and (';
				foreach($dia as $fra){
					if($fra == 'M'){
						$concatenar .= " (ing_fic_dia like '%-M-%' or ing_fic_dia like 'M' or ing_fic_dia like 'M-%' or ing_fic_dia like '%-M') ";
					}else{
						$concatenar .= ' ing_fic_dia like "%'.$fra.'%" ';
					}

					if(end($dia) != $fra){
						$concatenar .= ' or ';
					}
				}
				$concatenar .= ')';
				$horaImplode = explode('-', $franja);
				$horaInicio = $horaImplode[0];
				$horaFin = $horaImplode[1];

				$aforo = $validar_ambiente[0]->aforo;
				$ambiente = $validar_ambiente[0]->descripcion;

				// Validar que el Instructor no se cruce con otra programación
				$sql = '
					select 	ing_fic_id, ing_fic_ambiente, ing_fic_dia, ing_fic_instructor,
							ing_fic_hor_inicio, ing_fic_hor_fin, fic_numero, par_nombres, par_apellidos
					from 	sep_ingreso_ficha ing_fic, sep_participante par
					where	ing_fic_instructor = "'.$instructor.'"
					and 	ing_fic.ing_fic_instructor = par.par_identificacion
					and 	((ing_fic_fecha_inicio >= "'.$fechaInicio.'" and ing_fic_fecha_inicio <= "'.$fechaFin.'") or
							(ing_fic_fecha_fin >= "'.$fechaInicio.'" and ing_fic_fecha_fin <= "'.$fechaFin.'") or
							(ing_fic_fecha_inicio < "'.$fechaInicio.'" and ing_fic_fecha_fin > "'.$fechaFin.'")) '.$concatenar.'
					order by ing_fic_id desc';
				$validarCruceInstructor = DB::select($sql);
				if(count($validarCruceInstructor) > 0){
					$arrayFichas = [];
					$arrayAmbiente = [];
					foreach($validarCruceInstructor as $validacion){
						$arrayFicha[] = $validacion->fic_numero;
						$arrayAmbiente[] = $validacion->ing_fic_ambiente;
				    }

					// El instrutor no puede programarce el mismo día en ambientes diferentes           
					if(!in_array($ambiente, $arrayAmbiente)){
						$mensaje[] = 'El <strong>Instructor</strong> ya está programado en otro ambiente.';
				        $puedeSeguir = false;
					}else if(in_array($ficha, $arrayFicha)){
						$mensaje[] = 'El <strong>Instructor</strong> ya está programado en la ficha <strong>'.$ficha.'</strong>.';
				        $puedeSeguir = false;
					}
				}
				
				// Validar si se cruza el ambiente
				$sql = '
					select 	ing_fic_id, ing_fic_ambiente, ing_fic_dia, ing_fic_instructor,
							ing_fic_hor_inicio, ing_fic_hor_fin, fic_numero,
							ing_fic_fecha_inicio, ing_fic_fecha_fin
					from 	sep_ingreso_ficha
					where	ing_fic_ambiente = "'.$ambiente.'"
					and 	((ing_fic_fecha_inicio >= "'.$fechaInicio.'" and ing_fic_fecha_inicio <= "'.$fechaFin.'") or
							(ing_fic_fecha_fin >= "'.$fechaInicio.'" and ing_fic_fecha_fin <= "'.$fechaFin.'") or
							(ing_fic_fecha_inicio < "'.$fechaInicio.'" and ing_fic_fecha_fin > "'.$fechaFin.'"))
					and 	((ing_fic_hor_inicio >= "'.$horaInicio.'" and ing_fic_hor_inicio <= "'.$horaFin.'") or
							(ing_fic_hor_fin >= "'.$horaInicio.'" and ing_fic_hor_fin <= "'.$horaFin.'") or
							(ing_fic_hor_inicio < "'.$horaInicio.'" and ing_fic_hor_fin > "'.$horaFin.'")) '.$concatenar.' 
							order by ing_fic_id desc limit 1';
				$validarCruceDeAmbiente = DB::select($sql);
				if(count($validarCruceDeAmbiente) > 0){
			        if($validarCruceDeAmbiente[0]->ing_fic_instructor != $instructor){
			            $mensaje[] = 'El <strong>Ambiente</strong> ya está programado en el horario seleccionado.';
				        $puedeSeguir = false;
			        }
				}

				// Si se cruza el horario del Instructor guarde el error y pase al siguiente registro
				if($puedeSeguir != false){
					// Registra el horario de la ficha
					foreach($dia as $fra){
    					$sql ='
    						insert into sep_ingreso_ficha
    						(ing_fic_id, fic_numero, ing_fic_hor_inicio, ing_fic_hor_fin, ing_fic_dia, ing_fic_aforo, ing_fic_fecha_inicio, ing_fic_fecha_fin, ing_fic_instructor, ing_fic_ambiente)
    						values
    						(default, "'.$ficha.'", "'.$horaInicio.'", "'.$horaFin.'", "'.$fra.'", '.$aforo.', "'.$fechaInicio.'", "'.$fechaFin.'", "'.$instructor.'", "'.$ambiente.'")';
    					DB::insert($sql);
					}
					$mensaje[] = 'Registro exitoso';
				}
			}
		}

		session()->put('mensajes',$mensaje);
		echo "<script> window.history.back(); </script>";
	}

	public function llaves_dias($dias){
		$dia_letra = explode('-', $dias);
		$numero_dia = array(
			'L' => 1, 'M' => 2, 'Mi' => 3,
			'J' => 4, 'V' => 5, 'S' => 6
		);
		$resultado = array();
		foreach($dia_letra as $val){
			$resultado[] = $numero_dia[$val];
		}

		return $resultado;
	}
	
	public function getReportesalida(){
		$_GET = $this->seguridad($_GET);
		extract($_GET);

		$fechaActual = date('Y-m-d');
		$horaActual = date('H:i');
		$diaActual = date('N');
		$dia = array(
			1 => array('%L%'),
			2 => array('%-M-%','M%','M-%'),
			3 => array('%Mi%'),
			4 => array('%J%'),
			5 => array('%V%'),
			6 => array('%s%'),
			7 => array('%D%'),
		);
		
		$contador = (count($dia[$diaActual])-1);
		$concatenar = '';
		foreach($dia[$diaActual] as $key => $d){
			$concatenar .= ' franja like "'.$d.'" ';
			if($contador != $key){
				$concatenar .= ' or ';
			}
		}
		$concatenar .= ')';
		//echo '<pre>';
		$sql = '
			select 	*
			from	sep_ingreso_detalle ing_det, sep_ingreso_estado ing_est
					left join sep_ingreso_habilitar hab on hab.doc_aprendiz = ing_est.ing_est_documento
					left join sep_ingreso_ficha fic on fic.fic_numero = ing_est.ing_est_ficha
			where	ing_est.ing_est_id = ing_det.ing_est_id
			and		fecha_inicio <= "'.$fechaActual.'" and fecha_fin >= "'.$fechaActual.'"
			and		ing_det_fecha = "'.$fechaActual.'"
			and 	ing_det_tem_salida is null
			and 	hora_fin < "'.$horaActual.'"
			group by ing_est.ing_est_id
			order 	by ing_est_rol, ing_fic_ambiente desc';
		$registros = DB::select($sql);
		//dd($registros);
		$datos = array();
		$aprendiz = 0;
		$instructor = 0;
		$empleado = 0;
		$externo = 0;
		if(count($registros)>0){
			foreach($registros as $reg){
				$datos['documento'][] = $reg->ing_est_documento;
				$datos['tipo_documento'][] = $reg->ing_est_tip_documento;
				$datos['rol'][] = $reg->ing_est_rol;
				$datos['nombre'][] = $reg->ing_est_nombre;
				$datos['temperatura_ingreso'][] = $reg->ing_det_tem_ingreso;
				$datos['hora_ingreso'][] = $reg->ing_det_hor_ingreso;
				$datos['hora_inicio_clase'][] = $reg->hora_inicio;
				$datos['hora_fin_clase'][] = $reg->hora_fin;
				$datos['ambiente'][] = $reg->ing_fic_ambiente;

				if($reg->ing_est_rol == 'Aprendiz'){
					$aprendiz++;
				}else if($reg->ing_est_rol == 'Instructor'){
					$instructor++;
				}else if($reg->ing_est_rol == 'Empleado'){
					$empleado++;
				}
			}
		}

		$sql = '
			select 	ing_est_documento, ing_est_tip_documento, ing_est_rol, ing_est_nombre,
					ing_det_tem_ingreso, ing_det_hor_ingreso, ing_det_hor_salida
			from	sep_ingreso_estado ing_est, sep_ingreso_detalle ing_det
			where	ing_est.ing_est_id = ing_det.ing_est_id
			and		ing_det_fecha = "'.$fechaActual.'"
			and 	ing_det_tem_salida is null
			and 	ing_est_rol = "Externo"
			order 	by ing_det_id desc ';
		$registros = DB::select($sql);
		if(count($registros)>0){
			foreach($registros as $reg){
				$datos['documento'][] = $reg->ing_est_documento;
				$datos['tipo_documento'][] = $reg->ing_est_tip_documento;
				$datos['rol'][] = $reg->ing_est_rol;
				$datos['nombre'][] = $reg->ing_est_nombre;
				$datos['temperatura_ingreso'][] = $reg->ing_det_tem_ingreso;
				$datos['hora_ingreso'][] = $reg->ing_det_hor_ingreso;
				$datos['hora_inicio_clase'][] = '';
				$datos['hora_fin_clase'][] = '';
				$datos['ambiente'][] = '';
				$externo++;
			}
		}
		$total_registros = $aprendiz+$empleado+$instructor+$externo;
		
		return view('Modules.Seguimiento.Ingreso.reporteSalida', compact('externo', 'empleado', 'instructor', 'aprendiz', 'datos', 'fechaActual', 'total_registros'));
	}

	public function getReporteaseo(){
		return view('Modules.Seguimiento.Ingreso.reporteAseo');
	}

	public function postReporteaseo(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

		if(!isset($fecha_inicio) or !isset($fecha_fin)){
			echo 'La fecha inicio y fecha sin son obligatorias.'; dd();
		}
		
		$anio = substr($fecha_inicio,0,4);
		$mes = substr($fecha_inicio,5,2);
		$dia = substr($fecha_inicio,8,2);
		// Anio mes y día sean númericos en la fecha inicio
		if(!is_numeric($anio) or !is_numeric($mes) or !is_numeric($dia)){
			echo 'Año, mes y día de la fecha inicio deben ser númericos'; dd();
		}

		$anio = substr($fecha_fin,0,4);
		$mes = substr($fecha_fin,5,2);
		$dia = substr($fecha_fin,8,2);
		// Anio mes y día sean númericos en la fecha inicio
		if(!is_numeric($anio) or !is_numeric($mes) or !is_numeric($dia)){
			echo 'Año, mes y día de la fecha fin deben ser númericos'; dd();
		}

		if($fecha_inicio > $fecha_fin){
			echo 'La fecha inicio debe ser menor a la fecha fin.'; dd();
		}

		$fecha_actual = date('Y-m-d');
		$hora_actual = date('h:i a');
		
		$sql = '
			select 	ambiente, hora_inicio, hora_fin, franja
			from 	sep_ingreso_habilitar ing_hab, sep_ingreso_estado est
			where 	est.ing_est_documento = ing_hab.doc_aprendiz
			and 	ambiente is not null
			and 	((fecha_inicio >= "'.$fecha_inicio.'" and fecha_inicio <= "'.$fecha_fin.'") or
					(fecha_fin >= "'.$fecha_inicio.'" and fecha_fin <= "'.$fecha_fin.'") or
					(fecha_inicio < "'.$fecha_inicio.'" and fecha_fin > "'.$fecha_fin.'"))
			group 	by ambiente, franja, hora_inicio, hora_fin
			order by hora_inicio, hora_fin, ambiente';
		$registros = DB::select($sql);

		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename=Reporte_aseo_'.$fecha_actual.'.csv');
		header('Pragma: no-cache');
		header("Expires: 0");

		echo ";F reporte:;".$fecha_actual.";H reporte:;".$hora_actual."\n";
		echo ";Desde:;".$fecha_inicio.";Hasta:;".$fecha_fin."\n";
		echo "Ambiente;Hora_inicio;Hora_fin;".utf8_decode('Día').";\n";

		if(count($registros)>0){
			foreach($registros as $reg){
				echo utf8_decode($reg->ambiente).";";
				echo $reg->hora_inicio.";";
				echo $reg->hora_fin.";";
				echo $reg->franja.";";
				echo "\n";
			}
		}else{
			echo utf8_decode('No hay información en los rangos de fechas seleccionados.');
		}
	}

	public function getIndexficha(){
	    $fechaActual = date('Y').'-01-01';
		$sql = '
			select 	ing_fic_id, fic_numero, ing_fic_hor_inicio, ing_fic_hor_fin,
					ing_fic_dia, ing_fic_fecha_inicio, ing_fic_fecha_fin,
					ing_fic_ambiente, ing_fic_aforo,
					substring_index(par.par_nombres," ",1) as nombreCorto,
					substring_index(par.par_apellidos," ",1) as apellidoCorto,
					(select count(ing_hab_id)
						from sep_ingreso_habilitar hab, sep_ingreso_estado est
						where doc_aprendiz = est.ing_est_documento
						and doc_instructor = ing_fic_instructor
						and ing_fic_fecha_inicio = fecha_inicio
						and ing_fic_hor_inicio = hora_inicio and ing_fic_dia = franja
						and fic_numero = ing_est_ficha) as totalAprendices
			from 	sep_ingreso_ficha fic, sep_participante = par
			where 	fic.ing_fic_instructor = par.par_identificacion
			and     ing_fic_fecha_inicio >= "'.$fechaActual.'"
			order by ing_fic_fecha_inicio desc, ing_fic_fecha_fin, ing_fic_ambiente, ing_fic_hor_inicio, nombreCorto, ing_fic_dia, totalAprendices';
		$fichas = DB::select($sql);

		$rol = \Auth::user()->participante->rol_id;
		return view('Modules.Seguimiento.Ingreso.indexFicha', compact('fichas', 'rol'));
	}

	public function getIndexpersona(){
		$rol = \Auth::user()->participante->rol_id;
		$sql = '
			select 	ing_est_rol, ing_est_tip_documento, ing_est_documento,
					ing_est_nombre, ing_est_ficha, restriccion, capacitacion,
					capacitacion, priorizado, ing_est_ingresa
			from 	sep_ingreso_estado
			order by ing_est_rol, ing_est_ficha, ing_est_ingresa';
		$personas = DB::select($sql);
		
		return view('Modules.Seguimiento.Ingreso.indexPersona', compact('personas', 'rol'));
	}

	public function getHorario(){
		return view('Modules.Seguimiento.Ingreso.horario', compact('fichas','trimestre_fecha_inicio'));
	}

	public function postHorario(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

		if(!isset($desde) or !isset($hasta)){
			echo 'Los campos desde y hasta son obligatorios.'; dd();
		}

		$fechas = array();
		$fechas['desde'] = $desde;
		$fechas['hasta'] = $hasta;

		foreach($fechas as $key => $fec){
			$anio = substr($fec, 0, 4);
			$mes = substr($fec, 5, 2);
			$dia = substr($fec, 8, 2);

			if(!is_numeric($anio) or !is_numeric($mes) or !is_numeric($dia)){
				echo 'El campo fecha '.$key.' debe ser numérico.'; dd();
			}
		}

		$documentoLoguiado = \Auth::user()->participante->par_identificacion;

		dd($_POST);
	}

	public function getReporteasistencia(){
		return view('Modules.Seguimiento.Ingreso.reporteAsistencia');
	}

	public function postReporteasistencia(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);
		
		if(!isset($fecha_inicio) or !isset($fecha_fin)){
			echo 'La fecha es obligatorios.'; dd();
		}
		
		$anio = substr($fecha_inicio,0,4);
		$mes = substr($fecha_inicio,5,2);
		$dia = substr($fecha_inicio,8,2);
		// Anio mes y día sean númericos en la fecha inicio
		if(!is_numeric($anio) or !is_numeric($mes) or !is_numeric($dia)){
			echo 'Año, mes y día deben ser númericos en la fecha inicio'; dd();
		}

		$anio = substr($fecha_fin,0,4);
		$mes = substr($fecha_fin,5,2);
		$dia = substr($fecha_fin,8,2);
		// Anio mes y día sean númericos en la fecha inicio
		if(!is_numeric($anio) or !is_numeric($mes) or !is_numeric($dia)){
			echo 'Año, mes y día deben ser númericos en la fecha fin'; dd();
		}

		$fecha_actual = date('Y-m-d');
		$hora_actual = date('h:i a');

		$sql = '
			select 	ing_det_fecha, ing_est_nombre, ing_est_tip_documento,
					ing_est_documento, ing_est_rol, ing_det_tem_ingreso,
					ing_det_tem_salida, ing_det_hor_ingreso, observacion
			from 	sep_ingreso_detalle ing_det, sep_ingreso_estado ing_est
			where	ing_det.ing_est_id = ing_est.ing_est_id
			and		(ing_det_fecha >= "'.$fecha_inicio.'" and ing_det_fecha <= "'.$fecha_fin.'") 
			order by ing_det_fecha desc, ing_det_id asc';
		$registros = DB::select($sql);
		
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename=Reporte_asistencia_'.$fecha_actual.'.csv');
		header('Pragma: no-cache');
		header("Expires: 0");

		echo ";Fec reporte:;".$fecha_actual.";Hor reporte:;".$hora_actual."\n";
		echo "Fecha;Nombre;Tipo de Doc.;".utf8_decode("N° de Doc.").";Cargo/Rol;Entrada Tem;Salida Temp;Hora ingreso;Observaciones;\n";

		foreach($registros as $reg){
			echo $reg->ing_det_fecha.";";
			echo utf8_decode($reg->ing_est_nombre).";";
			echo $reg->ing_est_tip_documento.";";
			echo $reg->ing_est_documento.";";
			echo $reg->ing_est_rol.";";
			echo $reg->ing_det_tem_ingreso.";";
			echo $reg->ing_det_tem_salida.";";
			echo $reg->ing_det_hor_ingreso.";";
			echo $reg->observacion.";";
			echo "\n";
		}
	}

    public function postEnableaprendiz(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);
		//dd($_POST);

		$datos = array();
		$datos['exito'] = 'no';
		$datos['cambiarValor'] = 'no';
		if(!isset($documento) or !is_numeric($documento) or $documento == ''){
			$datos['mensaje'] = 'El documento del aprendiz es obligatorio y debe ser solo numeros';
		}else if(!isset($ficha) or !is_numeric($ficha) or $ficha == ''){
			$datos['mensaje'] = 'La ficha es obligatoria y debe ser solo numérico';
		}else if(!isset($valor) or ($valor != 'si' and $valor != 'no')){
			$datos['mensaje'] = 'El valor solo debe ser si o no';
		}else if(!isset($fecha_inicio) or $fecha_inicio == ''){
			$datos['mensaje'] = 'La fecha inicio es obligatoria o el formato de fecha es incorrecto';
		}else if(!isset($fecha_fin) or $fecha_fin == ''){
			$datos['mensaje'] = 'La fecha fin es obligatoria o el formato de fecha es incorrecto';
		}else{
			// El instructor tiene la ficha asignada ?
			$documentoLoguiado = \Auth::user()->participante->par_identificacion;
			$sql = '
				select 	*
				from 	sep_ingreso_ficha
				where	fic_numero = "'.$ficha.'"
				and 	ing_fic_fecha_inicio = "'.$fecha_inicio.'" and ing_fic_fecha_fin = "'.$fecha_fin.'"
				and 	ing_fic_hor_inicio = "'.$hora_inicio.'" and ing_fic_hor_fin = "'.$hora_fin.'"
				and 	ing_fic_dia = "'.$dia.'"
				and 	ing_fic_instructor = "'.$documentoLoguiado.'" limit 1';
			$data = DB::select($sql);
			if(count($data)==0){
				$datos['mensaje'] = 'El instructor <strong>no</strong> tiene habilitada la ficha seleccionada o la fecha no coincide con lo registrado.';
			}else{
				// El aprendiz pertenece a la ficha seleccionada ?
				$datos['mensaje'] = 'Aprendiz actualizado.';
				$sql = '
					select 	ing_est_documento, ing_est_ingresa
					from 	sep_ingreso_estado
					where	ing_est_documento = "'.$documento.'"
					and 	ing_est_ficha = "'.$ficha.'" limit 1';
				$validar = DB::select($sql);
				if(count($validar)>0){
					// Declaración de variables
					$fechaInicio = $data[0]->ing_fic_fecha_inicio;
					$fechaFin = $data[0]->ing_fic_fecha_fin;
					$hora_inicio = $data[0]->ing_fic_hor_inicio;
					$hora_fin = $data[0]->ing_fic_hor_fin;
					$franja = $data[0]->ing_fic_dia;

					// Generamos una setencia de SQL para consultar cada uno de los días de las franjas
					$franjas_implode = explode('-', $franja);
					$contador = (count($franjas_implode)-1);
					$concatenar = ' and (';
					foreach($franjas_implode as $key => $fra){
						if($fra == 'M'){
							$concatenar .= " (franja like '%-M-%' or franja like 'M' or franja like 'M-%' or franja like '%-M') ";
						}else{
							$concatenar .= ' franja like "%'.$fra.'%" ';
						}

						if($contador != $key){
							$concatenar .= ' or ';
						}
					}
					$concatenar .= ')';
					if($valor == 'si'){
						// El aprendiz está habilitado para ser programado ?
						$habilitadoParaProgramar = $validar[0]->ing_est_ingresa;
						if($habilitadoParaProgramar == "si"){
							// El aforo de los Aprendices e Instructores está completo ?
							$fecha_inicio_copia = date('Y-m-d');
							if($fecha_inicio > $fecha_inicio_copia){
							    $fecha_inicio_copia = $fecha_inicio;
							}
							
							$sql = '
								select	count(doc_aprendiz) total
								from	sep_ingreso_habilitar hab, sep_ingreso_estado est
								where 	hab.doc_aprendiz = est.ing_est_documento
								and		((fecha_inicio >= "'.$fecha_inicio_copia.'" and fecha_inicio <= "'.$fechaFin.'") or
										(fecha_fin >= "'.$fecha_inicio_copia.'" and fecha_fin <= "'.$fechaFin.'") or
										(fecha_inicio < "'.$fecha_inicio_copia.'" and fecha_fin > "'.$fechaFin.'"))
								and 	((hora_inicio >= "'.$hora_inicio.'" and hora_inicio <= "'.$hora_fin.'") or
										(hora_fin >= "'.$hora_inicio.'" and hora_fin <= "'.$hora_fin.'") or
										(hora_inicio < "'.$hora_inicio.'" and hora_fin > "'.$hora_fin.'")) '.$concatenar.'
								and		ing_est_rol in("Aprendiz", "Instructor")
								and 	estado = "si"';
							$aforoRegistrado = DB::select($sql);
							$aforoTotal = $aforoRegistrado[0]->total;
							$aforoAprendiz = $this->aforoAprendiz;
							// Validamos si el Instructor ya se incluye en el aforo
							$sql = '
								select	ing_hab_id, estado
								from	sep_ingreso_habilitar
								where 	((fecha_inicio >= "'.$fechaInicio.'" and fecha_inicio <= "'.$fechaFin.'") or
										(fecha_fin >= "'.$fechaInicio.'" and fecha_fin <= "'.$fechaFin.'") or
										(fecha_inicio < "'.$fechaInicio.'" and fecha_fin > "'.$fechaFin.'"))
								and 	((hora_inicio >= "'.$hora_inicio.'" and hora_inicio <= "'.$hora_fin.'") or
										(hora_fin >= "'.$hora_inicio.'" and hora_fin <= "'.$hora_fin.'") or
										(hora_inicio < "'.$hora_inicio.'" and hora_fin > "'.$hora_fin.'")) '.$concatenar.'
								and 	doc_aprendiz = "'.$documentoLoguiado.'" limit 1';
							$instructor_resgitrado = DB::select($sql);
							$puede_registrar_al_instructor = false;
							if(count($instructor_resgitrado)>0){
								$aumenta_aforo = 1;
							}else{
								$aumenta_aforo = 2;
								$puede_registrar_al_instructor = true;
							}
							$aforoTotal = $aforoTotal + $aumenta_aforo;

							if($aforoTotal <= $aforoAprendiz){
								// El aforo del ambiente está completo ?
								$aforoFicha = $data[0]->ing_fic_aforo;
								$ambiente = $data[0]->ing_fic_ambiente;

								$sql = '
									select	count(distinct(doc_aprendiz)) as total
									from	sep_ingreso_habilitar hab
									where 	ambiente like "%'.$ambiente.'%"
									and 	((fecha_inicio >= "'.$fechaInicio.'" and fecha_inicio <= "'.$fechaFin.'") or
											(fecha_fin >= "'.$fechaInicio.'" and fecha_fin <= "'.$fechaFin.'") or
											(fecha_inicio < "'.$fechaInicio.'" and fecha_fin > "'.$fechaFin.'"))
									and 	((hora_inicio >= "'.$hora_inicio.'" and hora_inicio <= "'.$hora_fin.'") or
											(hora_fin >= "'.$hora_inicio.'" and hora_fin <= "'.$hora_fin.'") or
											(hora_inicio < "'.$hora_inicio.'" and hora_fin > "'.$hora_fin.'")) '.$concatenar.'
									and 	estado = "si"';
								$aforoRegistradoFicha = DB::select($sql);
								$aforoAmbiente = $aforoRegistradoFicha[0]->total + $aumenta_aforo;
								if($aforoAmbiente <= $aforoFicha){
									// El aprendiz ya está registrado en las fechas seleccionadas ?
								// 	$sql = '
								// 		select 	hora_inicio, hora_fin, franja, doc_instructor, estado
								// 		from 	sep_ingreso_habilitar
								// 		where	((fecha_inicio >= "'.$fechaInicio.'" and fecha_inicio <= "'.$fechaFin.'") or
								// 				(fecha_fin >= "'.$fechaInicio.'" and fecha_fin <= "'.$fechaFin.'") or
								// 				(fecha_inicio < "'.$fechaInicio.'" and fecha_fin > "'.$fechaFin.'")) '.$concatenar.'
								// 		and 	doc_aprendiz = "'.$documento.'" limit 1';
								// 	$validar = DB::select($sql);
								$sql = '
										select 	hora_inicio, hora_fin, franja, doc_instructor, estado
										from 	sep_ingreso_habilitar
										where   (fecha_inicio = "'.$fechaInicio.'" and fecha_fin = "'.$fechaFin.'") '.$concatenar.'
										and 	doc_aprendiz = "'.$documento.'" limit 1';
									$validar = DB::select($sql);
									if(count($validar)>0){
										// Validamos si la hora inicio, hora fin y franja son iguales para actualizar
										// si no el aprendiz debe estar registrado en jornada contraria
										$validarHoraInicio = $validar[0]->hora_inicio;
										$validarHoraFin = $validar[0]->hora_fin;
										$validarFranja = $validar[0]->franja;
										$validarInstructor = $validar[0]->doc_instructor;
										$validarEstado = $validar[0]->estado;
									    
										if($validarInstructor == $documentoLoguiado and $validarHoraInicio == $hora_inicio and $validarHoraFin == $hora_fin and $validarFranja == $franja){
											$sql = '
												update 	sep_ingreso_habilitar
												set		estado = "si", hora_inicio = "'.$hora_inicio.'",
														hora_fin = "'.$hora_fin.'", franja = "'.$franja.'"
												where 	doc_aprendiz = "'.$documento.'"
												and		fecha_inicio = "'.$fechaInicio.'" and fecha_fin = "'.$fechaFin.'"';
											DB::update($sql);
											$datos['exito'] = 'si';
										}else{
											$datos['cambiarValor'] = 'si';
											$datos['mensaje'] = 'El aprendiz ya está programado en la fecha seleccionado.';
										}
							
									}else{
									    $sqlAprendiz = '
											insert into sep_ingreso_habilitar
											(ing_hab_id, doc_aprendiz, doc_instructor, fecha_inicio, fecha_fin, hora_inicio, hora_fin, franja, ambiente, estado)
											values
											(default,"'.$documento.'","'.$documentoLoguiado.'","'.$fechaInicio.'", "'.$fechaFin.'", "'.$hora_inicio.'","'.$hora_fin.'","'.$franja.'","'.$ambiente.'","si")';
										
										DB::beginTransaction();
										try {
											if($puede_registrar_al_instructor == true){
												$sqlInstructor = '
													insert into sep_ingreso_habilitar
													(ing_hab_id, doc_aprendiz, doc_instructor, fecha_inicio, fecha_fin, hora_inicio, hora_fin, franja, ambiente, estado)
													values
													(default,"'.$documentoLoguiado.'","'.$documentoLoguiado.'","'.$fechaInicio.'", "'.$fechaFin.'", "'.$hora_inicio.'","'.$hora_fin.'","'.$franja.'","'.$ambiente.'", "si")';
												DB::insert($sqlInstructor);
											}
										    DB::insert($sqlAprendiz);
										    DB::commit();
										    $datos['exito'] = 'si';
										} catch (\Exception $e) {
										    DB::rollback();
										    $datos['exito'] = 'no';
										    $datos['mensaje'] = 'La acción no se ha completa, intente nuevamente.';
										}
										/*$sql = '
											insert into sep_ingreso_habilitar
											(ing_hab_id, doc_aprendiz, doc_instructor, fecha_inicio, fecha_fin, hora_inicio, hora_fin, franja, ambiente, estado)
											values
											(default,"'.$documento.'","'.$documentoLoguiado.'","'.$fechaInicio.'", "'.$fechaFin.'", "'.$hora_inicio.'","'.$hora_fin.'","'.$franja.'","'.$ambiente.'","si")';
										DB::insert($sql);
										if($puede_registrar_al_instructor == true){
											$sql = '
												insert into sep_ingreso_habilitar
												(ing_hab_id, doc_aprendiz, doc_instructor, fecha_inicio, fecha_fin, hora_inicio, hora_fin, franja, ambiente, estado)
												values
												(default,"'.$documentoLoguiado.'","'.$documentoLoguiado.'","'.$fechaInicio.'", "'.$fechaFin.'", "'.$hora_inicio.'","'.$hora_fin.'","'.$franja.'","'.$ambiente.'", "si")';
											DB::insert($sql);
										}
										$datos['exito'] = 'si';*/
									}
								}else{
									$datos['cambiarValor'] = 'si';
									$datos['mensaje'] = 'El aforo del <strong>ambiente</strong> está completo en la fecha seleccionada.';
								}
							}else{
								$datos['cambiarValor'] = 'si';
								$datos['mensaje'] = 'El aforo de aprendices e Instructores está completo en la fecha seleccionada.';
							}
						}else{
							$datos['mensaje'] = 'El aprendiz <strong>no</strong> esta habilitado para ser programado.';
						}
					}else if($valor == 'no'){
					    $ambiente = $data[0]->ing_fic_ambiente;
						$sql = '
							select	count(doc_aprendiz) as total
							from	sep_ingreso_habilitar
							where 	ambiente = "'.$ambiente.'"
							and 	((fecha_inicio >= "'.$fechaInicio.'" and fecha_inicio <= "'.$fechaFin.'") or
									(fecha_fin >= "'.$fechaInicio.'" and fecha_fin <= "'.$fechaFin.'") or
									(fecha_inicio < "'.$fechaInicio.'" and fecha_fin > "'.$fechaFin.'"))
							and 	((hora_inicio >= "'.$hora_inicio.'" and hora_inicio <= "'.$hora_fin.'") or
									(hora_fin >= "'.$hora_inicio.'" and hora_fin <= "'.$hora_fin.'") or
									(hora_inicio < "'.$hora_inicio.'" and hora_fin > "'.$hora_fin.'")) '.$concatenar.'
							and 	doc_instructor = "'.$documentoLoguiado.'"
							and 	estado = "si"';
						$aforoRegistradoFicha = DB::select($sql);

						$sql = '
							delete  from sep_ingreso_habilitar
							where 	doc_aprendiz = "'.$documento.'"
							and 	doc_instructor = "'.$documentoLoguiado.'"
							and		fecha_inicio = "'.$fechaInicio.'" and fecha_fin = "'.$fechaFin.'" '.$concatenar;
						DB::beginTransaction();
						try {
							DB::delete($sql);
							if(($aforoRegistradoFicha[0]->total - 1) == 1){
								$sql = '
									delete from	sep_ingreso_habilitar
									where 	((fecha_inicio >= "'.$fechaInicio.'" and fecha_inicio <= "'.$fechaFin.'") or
											(fecha_fin >= "'.$fechaInicio.'" and fecha_fin <= "'.$fechaFin.'") or
											(fecha_inicio < "'.$fechaInicio.'" and fecha_fin > "'.$fechaFin.'"))
									and 	((hora_inicio >= "'.$hora_inicio.'" and hora_inicio <= "'.$hora_fin.'") or
											(hora_fin >= "'.$hora_inicio.'" and hora_fin <= "'.$hora_fin.'") or
											(hora_inicio < "'.$hora_inicio.'" and hora_fin > "'.$hora_fin.'")) '.$concatenar.'
									and 	doc_aprendiz = "'.$documentoLoguiado.'"';
								DB::delete($sql);
							}
							DB::commit();
						    $datos['exito'] = 'si';
						}catch (\Exception $e) {
						    DB::rollback();
						    $datos['exito'] = 'no';
						    $datos['mensaje'] = 'La acción no se ha completa, intente nuevamente.';
						}
					}
				}else{
					$datos['mensaje'] = 'El aprendiz <strong>no</strong> pertenece a la ficha seleccionada.';
				}
			}
		}

		echo json_encode($datos);
	}

	public function postFecha(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);
		//dd($_POST);

		$datos = array();
		$datos['exito'] = 'no';
		$datos['mensaje'] = 'Tabla actualizada';
		if($fecha != ''){
			if($fecha >= date('Y-m-d')){
				$documentoLoguiado = \Auth::user()->participante->par_identificacion;
				echo $sql = '
					select 	ing_fic_hor_inicio, ing_fic_hor_fin, ing_fic_dia,
							ing_fic_fecha_inicio, ing_fic_fecha_fin
					from 	sep_ingreso_ficha
					where	fic_numero = "'.$ficha.'"
					and 	ing_fic_fecha_fin = "'.$fecha.'"
					and 	ing_fic_instructor = "'.$documentoLoguiado.'" limit 1';
				$franja = DB::select($sql);

				if(count($franja)>0){
					$horaInicio = $franja[0]->ing_fic_hor_inicio;
					$horaFin = $franja[0]->ing_fic_hor_fin;
					$fechaInicio = $franja[0]->ing_fic_fecha_inicio;
					$fechaFin = $franja[0]->ing_fic_fecha_fin;
					$dia = $franja[0]->ing_fic_dia;

					$sql = '
						select  ing_est_documento, ing_est_nombre
						from 	sep_ingreso_estado ing_est, sep_ingreso_ficha ing_fic
						where 	ing_est.ing_est_ficha = ing_fic.fic_numero
						and		ing_est_ficha  = "'.$ficha.'"
						and 	ing_fic_hor_inicio = "'.$horaInicio.'" and ing_fic_hor_fin = "'.$horaFin.'"
						and 	ing_fic_fecha_inicio = "'.$fechaInicio.'" and ing_fic_fecha_fin = "'.$fechaFin.'"
						and 	ing_fic_dia = "'.$dia.'"
						and 	ing_est_ingresa = "si"
						order	by ing_est_nombre, ing_est_documento';
					$aprendices = DB::select($sql);
					//dd($aprendices);
					$sql = '
						select	ing_hab.doc_aprendiz
						from	sep_ingreso_habilitar ing_hab, sep_ingreso_estado ing_est
						where 	ing_hab.doc_aprendiz = ing_est.ing_Est_documento
						and 	ing_est.ing_est_ficha  = "'.$ficha.'"
						and 	fecha_inicio = "'.$fechaInicio.'" and fecha_fin = "'.$fechaFin.'"
						and 	hora_inicio = "'.$horaInicio.'"
						and 	hora_fin = "'.$horaFin.'"
						and 	franja = "'.$dia.'"
						and 	estado = "si"';
					$habilitados = DB::select($sql);
					//dd($habilitados);
					$habilitados_array = array();
					foreach($habilitados as $hab){
						$habilitados_array[] = $hab->doc_aprendiz;
					}

					if(count($aprendices)>0){
						$datos['hora'] = $horaInicio.' - '.$horaFin;
						$datos['dia'] = $dia;
						$datos['tabla'] = '';

						foreach($aprendices as $apr){
							$datos['tabla'] .=
							'<tr>
								<td>
									<select class="aprendiz" class="form-control">';
										if(in_array($apr->ing_est_documento, $habilitados_array)){
										$datos['tabla'] .=
											'<option value="si">si</option>
											<option value="no">no</option>';
										}else{
										$datos['tabla'] .=
											'<option value="no">no</option>
											<option value="si">si</option>';
										}
							$datos['tabla'] .=
									'</select>
								</td>
								<td>'. $apr->ing_est_nombre .'</td>
								<td class="documento">'. $apr->ing_est_documento .'</td>
							</tr>';
						}
						$datos['exito'] = 'si';
					}else{
						$datos['mensaje'] = 'No hay aprendices habiltiados en la ficha seleccionada';
					}
				}else{
					$datos['mensaje'] = 'El instructor <strong>no</strong> tiene habilitada la ficha seleccionada o la fecha no corresponde a la registrada.';
				}
			}else{
				$datos['mensaje'] = 'La fecha actual debe estar entre el rango de <strong>fecha</strong> seleccionada.';
			}
		}else{
			$datos['mensaje'] = 'La <strong>fecha</strong> es obligatoria';
		}

		echo json_encode($datos);
	}


	public function postAprendicestabla(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

		// Validaciones
		$datos['exito'] = 'no';
		if($fecha_inicio == '' and $fecha_fin == ''){
			$datos['mensaje'] = 'La <strong>fecha inicio y fin</strong> son obligatorias.';
		}else if($fecha_fin < date('Y-m-d')){
			$datos['mensaje'] = 'La programación de la ficha ya paso.';
		}else if($dia == ''){
			$datos['mensaje'] = 'El campo <strong>día</strong> es obligatorio.';
		}else if($hora_inicio == ''){
			$datos['mensaje'] = 'El campo <strong>hora inicio</strong> es obligatorio.';
		}else if($hora_fin == ''){
			$datos['mensaje'] = 'El campo <strong>hora fin</strong> es obligatorio.';
		}else{
			// El instructor tiene la ficha asignada ?
			$documentoLoguiado = \Auth::user()->participante->par_identificacion;
			$sql = '
				select 	ing_fic_hor_inicio
				from 	sep_ingreso_ficha
				where	fic_numero = "'.$ficha.'"
				and 	ing_fic_fecha_inicio = "'.$fecha_inicio.'" and ing_fic_fecha_fin = "'.$fecha_fin.'"
				and 	ing_fic_hor_inicio = "'.$hora_inicio.'" and ing_fic_hor_fin = "'.$hora_fin.'"
				and 	ing_fic_dia = "'.$dia.'"
				and 	ing_fic_instructor = "'.$documentoLoguiado.'" limit 1';
			$validar_ficha_asignada = DB::select($sql);
			if(count($validar_ficha_asignada) == 0){
				$datos['mensaje'] = 'Usted <strong>no</strong> tiene la ficha habilitada o se a modificado el horario, comunicar al Coordinador de su área.'; echo json_encode($datos); dd();
			}

			// Aprendices habilitados para programar
			$sql = '
				select  ing_est_documento, ing_est_nombre
				from 	sep_ingreso_estado
				where 	ing_est_ficha  = "'.$ficha.'"
				and 	ing_est_ingresa = "si"
				order	by ing_est_nombre, ing_est_documento';
			$aprendices = DB::select($sql);
			if(count($aprendices) == 0){
				$datos['mensaje'] = '<strong>No</strong> hay aprendices habilitados para programar en está ficha.'; echo json_encode($datos); dd();
			}

			// Cuales aprendices han sido habilitados ?
			$sql = '
				select	ing_hab.doc_aprendiz
				from	sep_ingreso_habilitar ing_hab
				where 	((fecha_inicio >= "'.$fecha_inicio.'" and fecha_inicio <= "'.$fecha_fin.'") or
						(fecha_fin >= "'.$fecha_inicio.'" and fecha_fin <= "'.$fecha_fin.'") or
						(fecha_inicio < "'.$fecha_inicio.'" and fecha_fin > "'.$fecha_fin.'"))
				and 	((hora_inicio >= "'.$hora_inicio.'" and hora_inicio <= "'.$hora_fin.'") or
						(hora_fin >= "'.$hora_inicio.'" and hora_fin <= "'.$hora_fin.'") or
						(hora_inicio < "'.$hora_inicio.'" and hora_fin > "'.$hora_fin.'"))
				and 	franja = "'.$dia.'"
				and 	doc_instructor = "'.$documentoLoguiado.'"
				and 	not doc_aprendiz = "'.$documentoLoguiado.'"
				and 	estado = "si"';
			$habilitados = DB::select($sql);

			$habilitados_array = array();
			foreach($habilitados as $hab){
				$habilitados_array[] = $hab->doc_aprendiz;
			}

			// Cargamos los aprendices en una tabla
			$datos['tabla'] = '';
			foreach($aprendices as $apr){
				$datos['tabla'] .=
				'<tr>
					<td>
						<select class="consultar_ficha" class="form-control">';
							if(in_array($apr->ing_est_documento, $habilitados_array)){
							$datos['tabla'] .=
								'<option value="si">si</option>
								<option value="no">no</option>';
							}else{
							$datos['tabla'] .=
								'<option value="no">no</option>
								<option value="si">si</option>';
							}
				$datos['tabla'] .=
						'</select>
					</td>
					<td>'. $apr->ing_est_nombre .'</td>
					<td class="documento">'. $apr->ing_est_documento .'</td>
				</tr>';
			}
			$datos['exito'] = 'si';
			$datos['mensaje'] = 'Tabla actualizada.';
		}
		echo json_encode($datos);
	}

	public function postFicha(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

		// Revisamos las fichas asignadas al Instructor desde la fecha actual
		$fechaActual = date('Y-m-d');
		$documentoLoguiado = \Auth::user()->participante->par_identificacion;
		$data = array();
		$sql = '
			select 	ing_fic_aforo, ing_fic_ambiente, prog_nombre,
					ing_fic_fecha_inicio, ing_fic_fecha_fin
			from 	sep_ingreso_ficha ing_fic, sep_ficha fic, sep_programa pro
			where 	ing_fic.fic_numero = fic.fic_numero
			and 	fic.prog_codigo = pro.prog_codigo
			and 	ing_fic.ing_fic_instructor = "'.$documentoLoguiado.'"
			and 	ing_fic.fic_numero = "'.$ficha.'"
			and 	ing_fic_fecha_fin >= "'.$fechaActual.'"
			order by ing_fic_fecha_fin asc';
		$datos = DB::select($sql);
		if(count($datos)>0){

			// Cargamos los datos de la ficha seleccionada
			$data['fecha'] = '<option value="">Seleccione...</option>';
			foreach($datos as $val){
				$data['fecha'] .= '<option value="'.$val->ing_fic_fecha_fin.'">'.$val->ing_fic_fecha_inicio.' - '.$val->ing_fic_fecha_fin.'</option>';
			}

			$data['aforo'] = ($datos[0]->ing_fic_aforo - 1);
			$data['ambiente'] = $datos[0]->ing_fic_ambiente;
			$data['programa'] = $datos[0]->prog_nombre;
			$data['mensaje'] = 'Por favor, seleccione el campo de <strong>fecha</strong>';
			$data['exito'] = 'si';
		}else{
			$data['mensaje'] = 'El Instructor no tiene asignada la ficha o las fechas asignas ya pasaron.';
			$data['exito'] = 'no';
		}

		echo json_encode($data);
	}

	public function getImportarficha(){
		return view("Modules.Seguimiento.Ingreso.indexImportarFicha");
	}

	public function postImportarficha(Request $request){
		// ¿Se ha cargado el archivo CSV?
        if ($request->hasFile('archivoCsv')) {

			// ¿El archivo cumple con el formato esperado - EXCEL (xlsx) ?
            $archivo = $request->file('archivoCsv');
            if ($archivo->getClientOriginalExtension() == "xlsx") {
				$filename = time() . '-' . $archivo->getClientOriginalName();

                // Configuracion del directorio multimedia
				$path = getPathUploads() . "/CSV/Ingreso";

                // Se mueve el archivo Excel al directorio multimedia
				$archivo->move($path, $filename);

                // Convertir archivo XLSX a un arreglo
				$mensaje = $this->leerExcelFicha($path, $filename);
            }else {
                $mensaje['formato'] = "El archivo no cumple con el formato esperado - xlsx(Libro de excel), por favor cargar un formato valido";
            }
        }
        else {
            $mensaje['archivo'] = "No se adjunto ning&uacute;n archivo";
        }
        return view("Modules.Seguimiento.Ingreso.indexImportarFicha", compact("mensaje"));
	}

	public function leerExcelFicha($path, $filename){
		// Leemos el archivo de excel cargado
		$objReader = new \PHPExcel_Reader_Excel2007();
		$objPHPExcel = $objReader->load($path . "/" . $filename);
		$objPHPExcel->setActiveSheetIndex(0);

		$horas = array(
			'6am-10am' => array('inicio' => '06:00','fin' => '10:00'),
			'7am-11am' => array('inicio' => '07:00','fin' => '11:00'),
			'8am-12pm' => array('inicio' => '08:00','fin' => '12:00'),
			'8am-12' => array('inicio' => '08:00','fin' => '12:00'),
			'1pm-5pm' => array('inicio' => '13:00','fin' => '17:00'),
			'2pm-6pm' => array('inicio' => '14:00','fin' => '18:00'),
			'6pm-9pm' => array('inicio' => '18:00','fin' => '21:00'),
			'6pm-10pm' => array('inicio' => '18:00','fin' => '22:00'),
			'7pm-10pm' => array('inicio' => '19:00','fin' => '22:00')
		);

		// Leemos fila por fila y validamos la información del archivo
		$fila = 3;
		$mensaje = array();
		$mensaje['cantidadErrores'] = 0;
		$mensaje['cantidadExitos'] = 0;
		$registro = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
		$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';',':','--');
		while(trim($registro) != "") {
			$ficha = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila));
			$dia = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('I' . $fila));
			$franja = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('J' . $fila));
			$aforo = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('F' . $fila));
			$ambiente = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila));
			$instructor = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('M' . $fila));
			$fechaInicio = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('O' . $fila)->getFormattedValue());
			$fechaInicio = formatoFecha($fechaInicio);
			$fechaFin = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('P' . $fila)->getFormattedValue());
			$fechaFin = formatoFecha($fechaFin);

			// Validaciones para las columnas que se utilizarán
			$errorExistente = false;
			$msg = '<strong>Valor no</strong> valido en la(s) siguiente(s) <strong>columna(s)</strong>: ';
			if($fechaInicio == false){
				$msg .= '<strong>· Fecha inicio </strong>'; $errorExistente = true;
			}

			if($fechaFin == false){
				$msg .= '<strong>· Fecha fin </strong>'; $errorExistente = true;
			}

			if($fechaInicio != false and $fechaFin != false and $fechaInicio > $fechaFin){
				$msg .= '<strong>· Fecha inicio debe ser menor o diferente a la fecha fin </strong>'; $errorExistente = true;
			}

			if(!is_numeric($instructor) or $instructor == ''){
				$msg .= '<strong>· Documento Instructor </strong>'; $errorExistente = true;
			}

			if(!is_numeric($ficha ) or $ficha == ''){
				$msg .= '<strong>· Ficha </strong>'; $errorExistente = true;
			}

			if(!isset($horas[$franja])){
				$msg .= '<strong>· Franja </strong>'; $errorExistente = true;
			}

			if(!is_numeric($aforo) or $aforo == ''){
				$msg .= '<strong>· Aforo </strong>'; $errorExistente = true;
			}

			if($ambiente == ''){
				$msg .= '<strong>· Ambiente </strong>'; $errorExistente = true;
			}

			// Si hay errores en está fila pase al siguiente registro
			if($errorExistente){
				$mensaje['errores'][$fila] = $msg;
				$fila++;
				$registro = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
				$mensaje['cantidadErrores']++;
				continue;
			}

			$horaInicio = $horas[$franja]['inicio'];
			$horaFin = $horas[$franja]['fin'];

			$ambDiaNumerosIngresa = $this->llaves_dias($dia);
			$dias_en_letras = array(
				1 => ' franja like "%L%" ',
				2 => " (franja like '%-M-%' or franja like 'M' or franja like 'M-%' or franja like '%-M') ",
				3 => ' franja like "%Mi%" ',
				4 => ' franja like "%J%" ',
				5 => ' franja like "%V%" ',
				6 => ' franja like "%S%" ',
				7 => ' franja like "%D%" ',
			);
			//$concatenar = $dias_en_letras[$diaActual];

			$dias_en_letras_ficha = array(
				1 => ' ing_fic_dia like "%L%" ',
				2 => " (ing_fic_dia like '%-M-%' or ing_fic_dia like 'M' or ing_fic_dia like 'M-%' or ing_fic_dia like '%-M') ",
				3 => ' ing_fic_dia like "%Mi%" ',
				4 => ' ing_fic_dia like "%J%" ',
				5 => ' ing_fic_dia like "%V%" ',
				6 => ' ing_fic_dia like "%S%" ',
				7 => ' ing_fic_dia like "%D%" ',
			);
			//$concatenar_ficha = $dias_en_letras_ficha[$diaActual];

			$validarDías = $this->llaves_dias($dia);
			$contadorDias = count($validarDías);
			$concatenar = 'and (';
			foreach($validarDías as $key => $val){
				$concatenar .= $dias_en_letras_ficha[$val];
				if($key != ($contadorDias-1)){
					$concatenar .= ' or ';
				}
			}
			$concatenar .= ')';
			//dd($concatenar);
			$sql = '
				select 	ing_fic_id, ing_fic_ambiente, ing_fic_dia, ing_fic_instructor,
						ing_fic_hor_inicio, ing_fic_hor_fin, fic_numero,
						ing_fic_fecha_inicio, ing_fic_fecha_fin
				from 	sep_ingreso_ficha
				where	ing_fic_ambiente = "'.$ambiente.'"
				and 	((ing_fic_fecha_inicio >= "'.$fechaInicio.'" and ing_fic_fecha_inicio <= "'.$fechaFin.'") or
						(ing_fic_fecha_fin >= "'.$fechaInicio.'" and ing_fic_fecha_fin <= "'.$fechaFin.'") or
						(ing_fic_fecha_inicio < "'.$fechaInicio.'" and ing_fic_fecha_fin > "'.$fechaFin.'"))
				and 	((ing_fic_hor_inicio >= "'.$horaInicio.'" and ing_fic_hor_inicio <= "'.$horaFin.'") or
						(ing_fic_hor_fin >= "'.$horaInicio.'" and ing_fic_hor_fin <= "'.$horaFin.'") or
						(ing_fic_hor_inicio < "'.$horaInicio.'" and ing_fic_hor_fin > "'.$horaFin.'")) '.$concatenar;
			$validarCruceDeAmbiente = DB::select($sql);
			
			$puedeSeguir = true;
			if(count($validarCruceDeAmbiente)>0){
				foreach($validarCruceDeAmbiente as $val){
					$val_fic = $val->fic_numero;
					$val_ins = $val->ing_fic_instructor;
					if($val_fic == $ficha and $val_ins == $instructor){
						$msg = 'Programación ya realizada.';
						$puedeSeguir = false; break;
					}else{
						if($val_ins != $instructor){
							$msg = 'El ambiente <strong>"'.$ambiente.'"</strong> se <strong>cruza</strong> en uno o varios días.';
							$puedeSeguir = false; break;
						}
					}
				}
			}

			// Si se cruza el horario del ambiente guarde el error y pase al siguiente registro
			if($puedeSeguir == false){
				$mensaje['errores'][$fila] = $msg;
				$fila++;
				$registro = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
				$mensaje['cantidadErrores']++;
				continue;
			}

			// Validar existe Instructor
			$sql = '
				select 	par_identificacion
				from 	sep_participante
				where 	rol_id = 2
				and 	par_identificacion = "'.$instructor.'" limit 1';
			$validar = DB::select($sql);
			if(count($validar)==0){
				$msg = 'El número de documento <strong>'.$instructor.'</strong> no es de un Instructor registrado.';
				$mensaje['errores'][$fila] = $msg;
				$fila++;
				$registro = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
				$mensaje['cantidadErrores']++;
				continue;
			}
			
			// Validar que el Instructor no se cruce con otra programación
			$sql = '
				select 	ing_fic_id, ing_fic_ambiente, ing_fic_dia, ing_fic_instructor,
						ing_fic_hor_inicio, ing_fic_hor_Fin, fic_numero, par_nombres, par_apellidos
				from 	sep_ingreso_ficha ing_fic, sep_participante par
				where	ing_fic_instructor = "'.$instructor.'"
				and 	ing_fic.ing_fic_instructor = par.par_identificacion
				and 	((ing_fic_fecha_inicio >= "'.$fechaInicio.'" and ing_fic_fecha_inicio <= "'.$fechaFin.'") or
						(ing_fic_fecha_fin >= "'.$fechaInicio.'" and ing_fic_fecha_fin <= "'.$fechaFin.'") or
						(ing_fic_fecha_inicio < "'.$fechaInicio.'" and ing_fic_fecha_fin > "'.$fechaFin.'")) '.$concatenar.'
				order by ing_fic_id';
			$validarCruceInstructor = DB::select($sql);
			if(count($validarCruceInstructor)>0){
				foreach($validarCruceInstructor as $val){
				    $val_amb = $val->ing_fic_ambiente;
					$val_hora_inicio = $val->ing_fic_hor_inicio;
					if($ambiente != $val_amb){
						$msg = 'El <strong>Instructor</strong> no puede ser programado en dos ambientes diferentes el mismo día.';
						$puedeSeguir = false; break;
					}

					if($horaInicio != $val_hora_inicio){
						$msg = 'El <strong>Instructor</strong> no puede ser programado en dos jornadas el mismo día.';
						$puedeSeguir = false; break;
					}
				}
			}
			
			// Si se cruza el horario del Instructor guarde el error y pase al siguiente registro
			if($puedeSeguir == false){
				$mensaje['errores'][$fila] = $msg;
				$fila++;
				$registro = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
				$mensaje['cantidadErrores']++;
				continue;
			}

			// Si no han habido errores se registra el horario de la ficha
			$sql ='
				insert into sep_ingreso_ficha
				(ing_fic_id, fic_numero, ing_fic_hor_inicio, ing_fic_hor_fin, ing_fic_dia, ing_fic_aforo, ing_fic_fecha_inicio, ing_fic_fecha_fin, ing_fic_instructor, ing_fic_ambiente)
				values
				(default, "'.$ficha.'", "'.$horaInicio.'", "'.$horaFin.'", "'.$dia.'", '.$aforo.', "'.$fechaInicio.'", "'.$fechaFin.'", "'.$instructor.'", "'.$ambiente.'")';
			DB::insert($sql);

			$mensaje['cantidadExitos']++;
			$fila++;
			$registro = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
		}

		$mensaje['exito'] = "El archivo se cargo exitosamente";
		return $mensaje;
	}

	public function formatoFecha($fecha){
		$dia = substr($fecha, 3, 2);
		$mes = substr($fecha, 0, 2);
		$anio = substr($fecha, 6);
		echo $anio = "20".$anio;
	
		if($anio < '2020' or $anio > '2022' or !is_numeric($dia) or strlen($dia) != 2 or !is_numeric($mes) or strlen($mes) != 2 or !is_numeric($anio) or strlen($anio) !=4){
			return false;
		}
	
		$fecha = $anio."-".$mes."-".$dia;
		return $fecha;
	}

	public function getImportar(){
		return view("Modules.Seguimiento.Ingreso.indexImportar");
	}

	public function postImportar(Request $request){
		// ¿Se ha cargado el archivo CSV?
        if ($request->hasFile('archivoCsv')) {
            $archivo = $request->file('archivoCsv');
            // ¿El archivo cumple con el formato esperado - EXCEL (xls, xlsx) ?
            if ($archivo->getClientOriginalExtension() == "xlsx") {
                $filename = time() . '-' . $archivo->getClientOriginalName();
                // Configuracion del directorio multimedia
                $path = getPathUploads() . "/CSV/Ingreso/Persona";
                // Se mueve el archivo Excel al directorio multimedia
                $archivo->move($path, $filename);
                // Convertir archivo XLSX a un arreglo
				$registros = $this->leerExcelPersona($path, $filename, $_POST['rol']);
				$mensaje['exito'] = "El archivo se cargo exitosamente";
            }else {
                $mensaje['formato'] = "El archivo no cumple con el formato esperado - xlsx(Libro de excel), por favor cargar un formato valido";
            }
        }
        else {
            $mensaje['archivo'] = "No se adjunto ning&uacute;n archivo";
        }
        return view("Modules.Seguimiento.Ingreso.indexImportar", compact("mensaje"));
	}

	public function leerExcelPersona($path, $filename, $rol){
	    ini_set('max_execution_time', '300'); 
		$horario = array();

		$objReader = new \PHPExcel_Reader_Excel2007();
		$objPHPExcel = $objReader->load($path . "/" . $filename);
		$objPHPExcel->setActiveSheetIndex(0);

		$dias = $this->crearArrayDias();
		$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';','--');
		$mensaje = array();
		$mensaje['cantidadErrores'] = 0;
		if($rol == 'Aprendiz'){
		    $validar_archivo = $objPHPExcel->getActiveSheet()->getCell('B1');
			if($validar_archivo != 'Nombre Completo'){
				echo '<h3>El arhivo que debe cargar con la opción de Aprendiz es "Caracterización aprendices CDTI."</h3>';
				dd();
			}
			$fila = 2;
			$registro = $objPHPExcel->getActiveSheet()->getCell('D' . $fila);
			while(trim($registro) != "") {
				$nombreCompleto = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('B' . $fila));
				$nombreCompleto = ucwords(mb_strtolower($nombreCompleto));
				$tipoDocumento = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila));
				$tipoDocumento = substr($tipoDocumento, 0,2);
				$documento = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila));
				$ficha = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('I' . $fila));
				$restriccion = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('J' . $fila));
				$capacitacion = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('K' . $fila));
				
                $telefono = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila));
                $correo = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila));
                
				$ing_est_ingresa = 'no';
				if(($restriccion == 'Ok' or $restriccion == 'Familiar') and ($capacitacion == 'Pondaje' or $capacitacion == 'Ambas')){
					$ing_est_ingresa = 'si';
				}
				
				if(!is_numeric($documento)){
					dd('Error en la fila '.$fila.' el valor en la columna número de documento, debe ser númerico');
				}

				$sql = '
					select 	*
					from 	sep_ingreso_estado
					where	ing_est_documento = '.$documento.' limit 1';
				$existe = DB::select($sql);
				
				if($correo==""){
                    $correo="Sin correo";
				}
				
				if(count($existe)>0){
					$sql = '
						update	sep_ingreso_estado
						set		ing_est_ingresa = "'.$ing_est_ingresa.'", ing_est_nombre = "'.$nombreCompleto.'",
								ing_est_ficha = "'.$ficha.'", ing_est_tip_documento = "'.$tipoDocumento.'",
								restriccion = "'.$restriccion.'", capacitacion = "'.$capacitacion.'",
								ing_est_telefono = "'.$telefono.'", ing_est_correo = "'.$correo.'",
								ing_est_rol = "Aprendiz", priorizado = null
						where	ing_est_documento = "'.$documento.'"';
					DB::update($sql);
				}else{
					$sql = '
						insert into sep_ingreso_estado
						(ing_est_id, ing_est_documento, ing_est_tip_documento, ing_est_ficha, ing_est_nombre, ing_est_rol, ing_est_ingresa,
						priorizado, restriccion, capacitacion, ing_est_telefono, ing_est_correo
						)
						values
						(default, "'.$documento.'", "'.$tipoDocumento.'", "'.$ficha.'", "'.$nombreCompleto.'", "Aprendiz", "'.$ing_est_ingresa.'",
						null, "'.$restriccion.'", "'.$capacitacion.'", "'.$telefono.'", "'.$correo.'")';
					DB::insert($sql);
				}

				$fila++;
				$registro = (String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila);
			}
		}else if($rol == 'Empleado'){
		    $validar_archivo = $objPHPExcel->getActiveSheet()->getCell('A2');
			if($validar_archivo != 'Tipo documento'){
				echo '<h3>El arhivo que debe cargar con la opción de Empleado es "Base datos personal CDTI"</h3>';
				dd();
			}
			$fila = 3;
			$registro = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
			
			while(trim($registro) != "") {
				$nombreCompleto = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila));
				$nombreCompleto = ucwords(mb_strtolower($nombreCompleto));

				$tipoDocumento = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila));
				$tipoDocumento = substr($tipoDocumento, 0,2);

				$documento = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('B' . $fila));
				$priorizado = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('I' . $fila));
				$restriccion = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('J' . $fila));
				$capacitacion = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('K' . $fila));

				$telefono = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila));
				$correo = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila));

				// Validaciones para las columnas que se utilizarán
				$errorExistente = false;
				$msg = '<strong>Valor no</strong> valido en la(s) siguiente(s) <strong>columna(s)</strong>: ';
				if($nombreCompleto == ''){
					$msg .= '<strong>· Nombre y apellido </strong>'; $errorExistente = true;
				}

				if($tipoDocumento == ''){
					$msg .= '<strong>· Tipo de documento </strong>'; $errorExistente = true;
				}

				if(!is_numeric($documento) or $documento == ''){
					$msg .= '<strong>· Número de documento </strong>'; $errorExistente = true;
				}

				if($priorizado == ''){
					$msg .= '<strong>· Priorizado </strong>'; $errorExistente = true;
				}

				if($restriccion == ''){
					$msg .= '<strong>· Restricción </strong>'; $errorExistente = true;
				}

				// Si hay errores en está fila pase al siguiente registro
				if($errorExistente){
					$mensaje['errores'][$fila] = $msg;
					$fila++;
					$registro = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
					$mensaje['cantidadErrores']++;
					continue;
				}

				$rol_interno = str_replace($caractereNoPremitidos, '', (String) $objPHPExcel->getActiveSheet()->getCell('F' . $fila));
				$rol_interno = explode(" ", $rol_interno);
				$rol_interno = ucfirst(strtolower($rol_interno[0]));

				$rol_registrar = 'Empleado';
				if($rol_interno == 'Instructor'){
					$rol_registrar = 'Instructor';
				}

				$ing_est_ingresa = 'no';
				if(($priorizado == 'Mixto' or $priorizado == 'Presencialidad') and $restriccion == 'No' and ($capacitacion == 'Pondaje' or $capacitacion == 'Ambas')){
					$ing_est_ingresa = 'si';
				}

				$sql = '
					select 	ing_est_documento
					from 	sep_ingreso_estado
					where	ing_est_documento = "'.$documento.'" limit 1';
				$existe = DB::select($sql);
				if(count($existe)>0){
					$sql = '
						update	sep_ingreso_estado
						set		ing_est_ingresa = "'.$ing_est_ingresa.'", ing_est_nombre = "'.$nombreCompleto.'",
								ing_est_ficha = null, ing_est_tip_documento = "'.$tipoDocumento.'", priorizado = "'.$priorizado.'",
								restriccion = "'.$restriccion.'", capacitacion = "'.$capacitacion.'",
								ing_est_telefono = "'.$telefono.'", ing_est_correo = "'.$correo.'"
						where	ing_est_documento = "'.$documento.'"';
					DB::update($sql);
				}else{
					$sql = '
						insert into sep_ingreso_estado
						(ing_est_id, ing_est_documento, ing_est_tip_documento, ing_est_ficha, ing_est_nombre, ing_est_rol, ing_est_ingresa,
						priorizado, restriccion, capacitacion, ing_est_telefono, ing_est_correo
						)
						values
						(default, "'.$documento.'", "'.$tipoDocumento.'", null, "'.$nombreCompleto.'", "'.$rol_registrar.'", "'.$ing_est_ingresa.'",
						"'.$priorizado.'", "'.$restriccion.'", "'.$capacitacion.'", "'.$telefono.'", "'.$correo.'")';
					DB::insert($sql);
				}

				$fila++;
				$registro = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
			}
		}
	}

	public function postAprendices(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);
		//dd($_POST);

		$detalle_id = $id;
		if(!is_numeric($detalle_id)){
			echo '
			<div class="alert alert-danger" style="margin: 10px 0px 0px 0px;">
				Se está modificando el formato de los datos recibidos.
			</div>'; dd();
		}

		// Validar que el instructor tenga la ficha asignada a sus horarios
		$par_identificacion = \Auth::user()->participante->par_identificacion;
		$sql = '
			select 	fic_numero, pla_fic_det.pla_fic_id, pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_dia_id
			from 	sep_planeacion_ficha_detalle pla_fic_det, sep_planeacion_ficha pla_fic
			where 	pla_fic_det.pla_fic_id = pla_fic.pla_fic_id
			and		pla_fic_det.pla_fic_det_id = '.$detalle_id.'
			and 	pla_fic_det.par_id_instructor = "'.$par_identificacion.'" limit 1';
		$validarFichaAsignada = DB::select($sql);
		if(count($validarFichaAsignada)>0){
			$sql = '
				select 	fic_numero
				from 	sep_planeacion_ficha pla_fic,sep_planeacion_ficha_detalle pla_fic_det
				where	pla_fic.pla_fic_id = pla_fic_det.pla_fic_id
				and 	pla_fic_det_id = '.$detalle_id.' limit 1';
			$ficha = DB::select($sql);
			$ficha = $ficha[0]->fic_numero;

			$sql = '
				select 	par.par_identificacion,par_nombres,par_apellidos,
				substring_index(par.par_nombres," ",1) as nombreCorto,
				substring_index(par.par_apellidos," ",1) as apellidoCorto
				from 	sep_matricula mat,sep_participante par
				where 	fic_numero = "'.$ficha.'"
				and 	est_id in (2,10) and mat.par_identificacion = par.par_identificacion
				order by par_nombres,par.par_identificacion';
			$aprendices = DB::select($sql);

			$existenAprendices = 'SI';
			if(count($aprendices)==0){
				$existenAprendices = 'NO';
				return view('Modules.Seguimiento.Ingreso.modalAprendiz',compact('existenAprendices'));
			}

			return view('Modules.Seguimiento.Ingreso.modalAprendiz', compact('existenAprendices', 'aprendices'));
		}else{
			echo '
			<div class="alert alert-danger" style="margin: 10px 0px 0px 0px;">
				El instructor no tiene asignada la ficha
			</div>';
		}
	}

	public function getEnable(){
		$documentoLogiado = \Auth::user()->participante->par_identificacion;
		$sql = '
			select 	ing_est_ingresa
			from 	sep_ingreso_estado
			where 	ing_est_documento = "'.$documentoLogiado.'" limit 1';
		$validar_instructor_ingreso = DB::select($sql);

		$fechaActual = date('Y-m-d');
		$sql = '
			select 	*
			from 	sep_ingreso_ficha
			left join sep_ficha on sep_ingreso_ficha.fic_numero = sep_ficha.fic_numero
			left join sep_programa on sep_ficha.prog_codigo = sep_programa.prog_codigo
			where 	ing_fic_instructor = "'.$documentoLogiado.'"
			and 	ing_fic_fecha_fin >= "'.$fechaActual.'"
			order by ing_fic_fecha_inicio desc, ing_fic_dia desc';
		$ficha = DB::select($sql);
		//dd($ficha);
		return view('Modules.Seguimiento.Ingreso.enable',compact('validar_instructor_ingreso', 'ficha'));
	}

	public function getIndex(){
		$_GET = $this->seguridad($_GET);
		extract($_GET);

		$fechaActual = date('Y-m-d');
		$sql = '
			select 	ing_est_documento, ing_est_tip_documento, ing_est_rol, ing_est_nombre,
					ing_det_tem_ingreso, ing_det_tem_salida, ing_det_fecha,
					ing_det_hor_ingreso, ing_det_hor_salida
			from	sep_ingreso_estado ing_est, sep_ingreso_detalle ing_det
			where	ing_est.ing_est_id = ing_det.ing_est_id
			and		ing_det_fecha = "'.$fechaActual.'"
			order 	by ing_det_id desc limit 20';
		$registros = DB::select($sql);

		if(isset($updateTable)){
			return view('Modules.Seguimiento.Ingreso.updateTable', compact('registros'));
		}
		return view('Modules.Seguimiento.Ingreso.index', compact('registros'));
	}

    public function postQuery(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

		$datos = array();
		if(!isset($documento) or $documento == '' or !is_numeric($documento)){
			$datos['mensaje'] = 'El ducumento es obligatorio y debe ser numérico.';
		}else{

			// Validar si la persona ya está registrada
			$sql = '
				select 	*
				from 	sep_ingreso_estado left join sep_ingreso_ficha
				on 		sep_ingreso_estado.ing_est_ficha = sep_ingreso_ficha.fic_numero
				where 	ing_est_documento = "'.$documento.'" limit 1';
			$persona = DB::select($sql);
			if(count($persona)>0){

				// Validamos si está autorizado para ingresar
				$datos['existe'] = 'si';
				$datos['puedePasar'] = 'no';
				$datos['rol'] = $persona[0]->ing_est_rol;
				$datos['nombreCompleto'] = $persona[0]->ing_est_nombre;
				$datos['tipoDocumento'] = $persona[0]->ing_est_tip_documento;
				$datos['habilitado'] = $persona[0]->ing_est_ingresa;
				$datos['restriccion'] = $persona[0]->restriccion;
				$datos['capacitacion'] = $persona[0]->capacitacion;
				$aforoFicha = ($persona[0]->ing_fic_aforo - 1);
				$aforoFicha = 500;
				if($datos['habilitado'] == 'si'){

					// Validar si es la entrada o la salida de la persona
					$fechaActual = date('Y-m-d');
					//$fechaActual = date('2020-09-28'); // Prueba1
					$sql = '
						select 	ing_det_id, ing_det_tem_salida, ing_det_tem_ingreso
						from 	sep_ingreso_estado ing_est, sep_ingreso_detalle ing_det
						where 	ing_est.ing_est_id = ing_det.ing_est_id and ing_est_documento = "'.$documento.'"
						and 	ing_det_fecha = "'.$fechaActual.'" order by	ing_det_id desc';
					$validar_entrada_salida = DB::select($sql);
					if(count($validar_entrada_salida)>0){
						$temperaturaSalida = $validar_entrada_salida[0]->ing_det_tem_salida;
						if($temperaturaSalida == ''){
							//$temperaturaIngreso = $validar_entrada_salida[0]->ing_det_tem_ingreso;
							//$datos['mensaje'] = 'Por favor registre la temperaura de <strong>salida</strong>';
							//$datos['temperaturaIngreso'] = $temperaturaIngreso;
							$datos['desplazamiento'] = 'salida';
							$datos['puedePasar'] = 'si';
						}else{
							$datos['mensaje'] = 'La persona <strong>no</strong> puede volver a entrar, porque ya ingreso.';
						}
						echo json_encode($datos); dd();
					}else{
						$aforo_consultar = $this->aforoEmpleado;
						$mensaje_rol = ' Empleado o persona externa ';
						$consultar_roles = ' and not ing_est_rol in("Aprendiz", "Instructor")';
						if($datos['rol'] == 'Aprendiz' or $datos['rol'] == 'Instructor'){
							$aforo_consultar = $this->aforoAprendiz;
							$mensaje_rol = ' Aprendiz o Instructor ';
							$consultar_roles = ' and ing_est_rol in("Aprendiz", "Instructor")';
						}

						// Validar aforo centro de formación
						$sql = '
							select 	count(ing_det_id) as total
							from 	sep_ingreso_detalle det, sep_ingreso_estado est
							where 	det.ing_est_id = est.ing_est_id '.$consultar_roles.'
							and 	ing_det_fecha = "'.$fechaActual.'" and ing_det_tem_salida is null';
						$aforoActual = DB::select($sql);
						$aforoActual = $aforoActual[0]->total;
						
						//$aforoActual = 429;
						//echo $aforoActual; echo '<br>'; echo $aforo_consultar;

						if($aforoActual >= $aforo_consultar){
							$datos['mensaje'] =  'El aforo de '.$datos['rol'].' está completo, la persona <strong>no</strong> puede ingresar, hasta que salga alguien con rol '.$mensaje_rol.'';
							echo json_encode($datos); dd();
						}

						//$datos['mensaje'] = 'Por favor registre la temperaura de <strong>Ingreso</strong>';
						$datos['desplazamiento'] = 'entrada';
						$datos['puedePasar'] = 'si';
					}

					$diaActual = date('N');
					//$diaActual = 1;  // Prueba1
					$dias_en_letras = array(
						1 => ' franja like "%L%" ',
						2 => " (franja like '%-M-%' or franja like 'M' or franja like 'M-%' or franja like '%-M') ",
						3 => ' franja like "%Mi%" ',
						4 => ' franja like "%J%" ',
						5 => ' franja like "%V%" ',
						6 => ' franja like "%S%" ',
						7 => ' franja like "%D%" ',
					);
					$concatenar = $dias_en_letras[$diaActual];

					$dias_en_letras_ficha = array(
						1 => ' ing_fic_dia like "%L%" ',
						2 => " (ing_fic_dia like '%-M-%' or ing_fic_dia like 'M' or ing_fic_dia like 'M-%' or ing_fic_dia like '%-M') ",
						3 => ' ing_fic_dia like "%Mi%" ',
						4 => ' ing_fic_dia like "%J%" ',
						5 => ' ing_fic_dia like "%V%" ',
						6 => ' ing_fic_dia like "%S%" ',
						7 => ' ing_fic_dia like "%D%" ',
					);
					$concatenar_ficha = $dias_en_letras_ficha[$diaActual];
					$horaActual = date('H:i');
					//$horaActual = '07:00';  // Prueba1

					// Validación para los Empleados e Instructores
					if($datos['rol'] != 'Externo' and $datos['desplazamiento'] == 'entrada'){
						$sql = '
							select 	hora_inicio, hora_fin, franja, estado
							from	sep_ingreso_habilitar
							where 	doc_aprendiz = "'.$documento.'"
							and 	(fecha_inicio <= "'.$fechaActual.'" and fecha_fin >= "'.$fechaActual.'")
							and 	'.$concatenar.'
							and 	estado = "si" limit 1';
						$programado = DB::select($sql);
						if(count($programado)>0){
							$horaFin = $programado[0]->hora_fin;
							$franja = $programado[0]->franja;
							$hora_inicio_antes_actualizar = $programado[0]->hora_inicio;

							$horaInicio = date('H:i', strtotime('- 60 minutes', strtotime($hora_inicio_antes_actualizar)));
							$cumpleHorario = false;
							$datos['horarioHabilitado'] = 'no';
							$horariosFicha = '';
							
							// Primero validamos el día y luego la hora
							$horariosFicha .= '<br>Día(s): '.$franja.' Hora inicio: '.$horaInicio.' Hora fin: '.$horaFin;
							if(in_array($diaActual, $this->llaves_dias($franja))){
								if($horaInicio <= $horaActual and $horaActual <= $horaFin){
									$datos['horarioHabilitado'] = 'si';
									$cumpleHorario = true;
								}
							}

							if($cumpleHorario ==  false){
								$datos['mensaje'] = 'El '.$datos['rol'].' está habilitado, pero <strong>no</strong> cumple con su horario de ingreso: '.$horariosFicha;
								$datos['habilitado'] = 'no';
							}
						}else{
							$datos['mensaje'] = 'El '.$datos['rol'].' <strong>no</strong> está programado para ingresar en la fecha actual.';
							$datos['habilitado'] = 'no';
						}
					}
				}else{
					$mensajeValor = 'La persona <strong style="color:red;">no</strong> está habilitado para ingresar<br>';
					if($datos['restriccion'] != 'Ok' and $datos['restriccion'] != 'Familiar' and $datos['restriccion'] != 'No'){
						$mensajeValor .= ' - Restricción = <strong style="color:red;">'.$datos['restriccion'].'</strong>';
					}

					if($datos['capacitacion'] != 'Pondaje' and $datos['capacitacion'] != 'Ambas'){
						if($datos['capacitacion'] == ''){
							$mensajeValor .= ' - Capacitación = <strong style="color:red;">Sin capacitacion</strong>';
						}else{
							$mensajeValor .= ' - Capacitación = <strong style="color:red;">'.$datos['capacitacion'].'</strong>';
						}
					}

					$datos['mensaje'] = $mensajeValor;
				}
			}else{
				// Debe ser una persona externa
				$datos['existe'] = 'no';
				$datos['mensaje'] = 'Por favor registre a la persona, si es externa, de lo contrario <strong>no</strong> permitir el ingreso';
			}
		}
		echo json_encode($datos);
	}

	public function postCreate(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);

		$datos = array();
		if(!isset($documento) or $documento == '' or !is_numeric($documento)){
			$datos['mensaje'] =  'El campo documento es obligatorio y debe ser numérico.';
			$datos['exito'] = 'no';
			echo json_encode($datos); dd();
		}

		// Validamos si la persona ya está registrado
		$sql = '
			select 	*
			from 	sep_ingreso_estado left join sep_ingreso_ficha
			on 		sep_ingreso_estado.ing_est_ficha = sep_ingreso_ficha.fic_numero
			where 	ing_est_documento = "'.$documento.'" limit 1';
		$persona = DB::select($sql);
		if(count($persona)>0){
			$ing_est_id = $persona[0]->ing_est_id;
			$rol = $persona[0]->ing_est_rol;
			$habilitado = $persona[0]->ing_est_ingresa;
		}else{
			$nombreCompleto = ucwords(mb_strtolower($nombreCompleto));
			$sql = '
				insert into 	sep_ingreso_estado
					(ing_est_id, ing_est_documento, ing_est_tip_documento, ing_est_ficha, ing_est_nombre, ing_est_rol, ing_est_ingresa)
				values
					(default, "'.$documento.'", "'.$tipoDocumento.'",null, "'.$nombreCompleto.'", "Externo", "si")';
			DB::insert($sql);
			$ing_est_id = DB::getPdo()->lastInsertId();
			$rol = 'Externo';
			$habilitado = 'si';
		}

		// Validar si la persona está habilitada para entrar
		if($habilitado == 'no'){
			$datos['mensaje'] = 'La persona <strong>no</strong> está habilitada para ingresar.';
			$datos['exito'] = 'no';
			echo json_encode($datos); dd();
		}

		// Validamos si es la entra o la salida
		$docRegistra = \Auth::user()->participante->par_identificacion;
		$fechaActual = date('Y-m-d');
		//$fechaActual = '2020-09-28'; // Prueba1
		$sql = '
			select 	ing_det_id, ing_det_tem_salida, ing_det_tem_ingreso
			from 	sep_ingreso_estado ing_est, sep_ingreso_detalle ing_det
			where 	ing_est.ing_est_id = ing_det.ing_est_id
			and 	ing_est_documento = "'.$documento.'"
			and 	ing_det_fecha = "'.$fechaActual.'"
			order 	by	ing_det_id desc';
		$validar = DB::select($sql);
		if(count($validar)>0){

			// Validar temperatura de salida
		       $temSalida = $validar[0]->ing_det_tem_salida;
			if($temSalida == ''){
				/*if(!isset($temperaturaSalida) or $temperaturaSalida == '' or !is_numeric($temperaturaSalida)){
					$datos['mensaje'] = 'El campo <strong>temperatura de salida</strong> es obligatorio y debe ser numérico.';
					$datos['exito'] = 'no';
					echo json_encode($datos); dd();
				}*/

				$mensaje = '';
				/*if($temperaturaSalida < 10 or $temperaturaSalida >= 37){
					$mensaje = ', pero la temperatura <strong>no</strong> puede ser mayor o igual a <strong>37 °C</strong>, por favor aplicar el protocolo.';
				}*/

				$ing_det_id = $validar[0]->ing_det_id;
				$sql = '
					update 	sep_ingreso_detalle
					set		ing_det_hor_salida = "'.date('h:i a').'",
							usu_registra_salida = "'.$docRegistra.'"
					where 	ing_det_id = '.$ing_det_id;
				DB::update($sql);

				$datos['mensaje'] = 'La <strong>salida</strong> fue registrada exitosamente'.$mensaje;
				$datos['exito'] = 'si';
			}else{
				$datos['mensaje'] = 'La persona <strong>no</strong> puede volver a entrar, porque ya ingreso.';
				$datos['exito'] = 'no';
			}
			echo json_encode($datos); dd();
		}else{

			/* Validar Temperatura de ingreso
			if($temperaturaIngreso == '' or !is_numeric($temperaturaIngreso) or !isset($temperaturaIngreso)){
				$datos['mensaje'] = 'El campo <strong>temperatura de salida</strong> es obligatorio y debe ser numérico.';
				$datos['exito'] = 'no';
				echo json_encode($datos); dd();
			}

			if($temperaturaIngreso < 10 or $temperaturaIngreso >= 37){
				$datos['mensaje'] = 'La temperatura <strong>no</strong> puede ser mayor o igual a <strong>37 °C</strong>, por favor separar a la persona por 7 minutos y después volver a tomar la temperatura, <strong>no permitir el ingreso</strong>.';
				$datos['exito'] = 'no';
				echo json_encode($datos); dd();
			}*/

			$aforo_consultar = $this->aforoEmpleado;
			$mensaje_rol = ' Empleado o persona externa ';
			$consultar_roles = ' and not ing_est_rol in("Aprendiz", "Instructor")';
			if($rol == 'Aprendiz' or $rol == 'Instructor'){
				$aforo_consultar = $this->aforoAprendiz;
				$mensaje_rol = ' Aprendiz o Instructor ';
				$consultar_roles = ' and ing_est_rol in("Aprendiz", "Instructor")';
			}

			// Validar aforo centro de formación
			$sql = '
				select 	count(ing_det_id) as total
				from 	sep_ingreso_detalle det, sep_ingreso_estado est
				where 	det.ing_est_id = est.ing_est_id '.$consultar_roles.'
				and 	ing_det_fecha = "'.$fechaActual.'" and ing_det_tem_salida is null';
			$aforoActual = DB::select($sql);
			$aforoActual = $aforoActual[0]->total;

			if($aforoActual >= $aforo_consultar){
				$datos['mensaje'] =  'El aforo de '.$datos['rol'].' está completo, la persona <strong>no</strong> puede ingresar, hasta que salga alguien con rol '.$mensaje_rol.'';
				$datos['exito'] = 'no';
				echo json_encode($datos); dd();
			}

			$diaActual = date('N');
			//$diaActual = 1; // Prueba1
			$dias_en_letras = array(
				1 => ' franja like "%L%" ',
				2 => " (franja like '%-M-%' or franja like 'M' or franja like 'M-%' or franja like '%-M') ",
				3 => ' franja like "%Mi%" ',
				4 => ' franja like "%J%" ',
				5 => ' franja like "%V%" ',
				6 => ' franja like "%S%" ',
				7 => ' franja like "%D%" ',
			);
			$concatenar = $dias_en_letras[$diaActual];

			$dias_en_letras_ficha = array(
				1 => ' ing_fic_dia like "%L%" ',
				2 => " (ing_fic_dia like '%-M-%' or ing_fic_dia like 'M' or ing_fic_dia like 'M-%' or ing_fic_dia like '%-M') ",
				3 => ' ing_fic_dia like "%Mi%" ',
				4 => ' ing_fic_dia like "%J%" ',
				5 => ' ing_fic_dia like "%V%" ',
				6 => ' ing_fic_dia like "%S%" ',
				7 => ' ing_fic_dia like "%D%" ',
			);
			$concatenar_ficha = $dias_en_letras_ficha[$diaActual];
			$horaActual = date('H:i');
			//$horaActual = '07:00'; // Prueba1

			// Validación
			if($rol != 'Externo'){
				$sql = '
					select 	hora_inicio, hora_fin, franja, estado
					from	sep_ingreso_habilitar
					where 	doc_aprendiz = "'.$documento.'"
					and 	(fecha_inicio <= "'.$fechaActual.'" and fecha_fin >= "'.$fechaActual.'")
					and 	'.$concatenar.'
					and 	estado = "si" limit 1';
				$programado = DB::select($sql);
				if(count($programado)>0){
					$horaFin = $programado[0]->hora_fin;
					$franja = $programado[0]->franja;
					$hora_inicio_antes_actualizar = $programado[0]->hora_inicio;

					$horaInicio = date('H:i', strtotime('- 60 minutes', strtotime($hora_inicio_antes_actualizar)));
					$cumpleHorario = false;
					$datos['horarioHabilitado'] = 'no';
					$horariosFicha = '';
					
					// Primero validamos el día y luego la hora
					$horariosFicha .= '<br>Día(s): '.$franja.' Hora inicio: '.$horaInicio.' Hora fin: '.$horaFin;
					if(in_array($diaActual,  $this->llaves_dias($franja))){
						if($horaInicio <= $horaActual and $horaActual <= $horaFin){
							$datos['horarioHabilitado'] = 'si';
							$cumpleHorario = true;
						}
					}

					if($cumpleHorario ==  false){
						$datos['mensaje'] = 'El Empleado está habilitado, pero <strong>no</strong> cumple con su horario de ingreso: '.$horariosFicha;
						$datos['habilitado'] = 'no';
						echo json_encode($datos); dd();
					}
				}else{
					$datos['mensaje'] = 'El Empleado <strong>no</strong> está programado para ingresar.';
					$datos['habilitado'] = 'no';
					echo json_encode($datos); dd();
				}
			}
			$observacion = trim($observacion);
			$sql = '
				insert into 	sep_ingreso_detalle
					(ing_det_id, ing_est_id, ing_det_tem_ingreso, ing_det_tem_salida,
					ing_det_fecha, ing_det_hor_ingreso, ing_det_hor_salida, usu_registra_entrada, usu_registra_salida, observacion)
				values
					(default, '.$ing_est_id.', null, null,
					"'.$fechaActual.'", "'.date('h:i a').'", null, "'.$docRegistra.'", null, "'.$observacion.'")';
			DB::insert($sql);

			/*$sql = '
				insert into 	sep_ingreso_detalle
					(ing_det_id, ing_est_id, ing_det_tem_ingreso, ing_det_tem_salida,
					ing_det_fecha, ing_det_hor_ingreso, ing_det_hor_salida, usu_registra_entrada, usu_registra_salida)
				values
					(default, '.$ing_est_id.', '.$temperaturaIngreso.', null,
					"'.$fechaActual.'", "'.date('h:i a').'", null, "'.$docRegistra.'", null)';
			DB::insert($sql);*/

			$datos['mensaje'] = 'La <strong>Entrada</strong> fue registrada exitosamente.';
			$datos['exito'] = 'si';
		}

		echo json_encode($datos);
	}

	public function crearArrayDias(){
		$dias = array(
			'L-M-Mi-J-V' => array(1,2,3,4,5),
			'L-Mi-V' => array(1,3,5),
			'L-Mi-Vi' => array(1,3,5),
			'Mi-Vi' => array(3,5),
			'Mi-V' => array(3,5),
			'M-J-S' => array(2,4,6),
			'M-J' => array(2,4),

			'L' => array(1),
			'M' => array(2),
			'Mi' => array(3),
			'J' => array(4),
			'V' => array(5),
			'S' => array(6),

			'Mi-J' => array(3,4),
			'L-Mi' => array(1,3),
			'L-M' => array(1,2),
			'M-Mi' => array(2,3),
			'J-V' => array(4,5),
		);

		return $dias;
	}

	public function seguridad($array){
		// Quitamos los simbolos no permitidos de cada variable recibida,
		// para evitar ataques XSS e Inyección SQL
		$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';','--');
		$array = str_replace($caractereNoPremitidos,'',$array);
		return	$array;
	}
}
?>