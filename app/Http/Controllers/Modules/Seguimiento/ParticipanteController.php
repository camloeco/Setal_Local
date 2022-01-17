<?php

namespace App\Http\Controllers\Modules\Seguimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Modelos del modulo usuarios
use App\Http\Models\Modules\Seguimiento\SepParticipante;
use App\Http\Models\Modules\Seguimiento\SepFicha;
use App\Http\Models\Modules\Seguimiento\SepMatricula;
use App\Http\Models\Modules\Seguimiento\SepEtapaPractica;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Pagination\LengthAwarePaginator;
use DB;

class ParticipanteController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct() {
        $this->middleware('auth');
		$this->middleware('control_roles');

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex() {

        $page = Input::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        $participantes = SepParticipante::all()->where('rol_id', 1);

        $participantes = DB::select("SELECT * FROM sep_participante");

        $participantes = new LengthAwarePaginator(
                array_slice(
                        $participantes, $offset, $perPage, true
                ), count($participantes), $perPage, $page);

        $participantes->setPath("index");

        return view("Modules.Seguimiento.Participante.index", compact("participantes", "offset"));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function getCreate() {
        echo "<br /><br /><br /><br /><br />En construcci&oacute;n....";

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        //

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getShow($id) {

        return '<div class="form-group has-success has-feedback">' .
                '<label">Nombres ' . $id . '</label>' .
                '<div>' .
                '<span>Pedro Alberto Morales</span>';

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        //

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        //

    }

    public function getCarga() {

        return view("Modules.Seguimiento.Participante.carga");

    }

// getCarga

public function getCargafichas() {

        //return view("Modules.Seguimiento.Participante.carga");
        return view("Modules.Seguimiento.Participante.cargaFichas");

    }

    public function postCarga(Request $request) {


        // ¿Se ha cargado el archivo CSV?
        if ($request->hasFile('archivoCsv')) {

            $archivo = $request->file('archivoCsv');

            // ¿El archivo cumple con el formato esperado - EXCEL (xls, xlsx) ?
            if (
                    $archivo->getClientOriginalExtension() == "xls" || $archivo->getClientOriginalExtension() == "xlsx"
            ) {

                $filename = time() . '-' . $archivo->getClientOriginalName();

                // Configuracion del directorio multimedia
                $pathCsv = getPathUploads() . "/CSV/Participante";

                // Se mueve el archivo CSV al directorio multimedia
                $archivo->move($pathCsv, $filename);

                // Convertir archivo CSV a un arreglo
                //$registros = convertCsvToArray($pathCsv, $filename);
                $registros = leerExcelParticipante($pathCsv, $filename, 1);
                //dd($registros);
                $mensaje['errores'] = $this->importArrayBD($registros, ($request->input('cargo') + 1));
                //dd($mensaje);
            } // if
            else {
                $mensaje['formato'] = "El archivo no cumple con el formato esperado - CSV, por favor "
                        . "cargar un formato valido";
            } // else
        } // if
        else {
            $mensaje['archivo'] = "No se adjunto ning&uacute;n archivo";
        } // else
        // redirect admin#http://seguimiento.proyectos/seguimiento/participante/carga

        return view("Modules.Seguimiento.Participante.carga", compact("mensaje"));

    }

// getCarga
 public function postCargafichas(Request $request) {
    //public function postCarga(Request $request) {


        // ���Se ha cargado el archivo CSV?
        if ($request->hasFile('archivoCsv')) {

            $archivo = $request->file('archivoCsv');

            // ���El archivo cumple con el formato esperado - EXCEL (xls, xlsx) ?
            if (
                    $archivo->getClientOriginalExtension() == "xls" || $archivo->getClientOriginalExtension() == "xlsx"
            ) {

                $filename = time() . '-' . $archivo->getClientOriginalName();

                // Configuracion del directorio multimedia
                $pathCsv = getPathUploads() . "/CSV/Fichas";

                // Se mueve el archivo CSV al directorio multimedia
                $archivo->move($pathCsv, $filename);

                // Convertir archivo CSV a un arreglo
                //$registros = convertCsvToArray($pathCsv, $filename);
                $registros = leerExcelFichas($pathCsv, $filename, 1);
                //dd($registros);
                $mensaje['errores'] = $this->importArrayBDFichas($registros);
            } // if
            else {
                $mensaje['formato'] = "El archivo no cumple con el formato esperado - CSV, por favor "
                        . "cargar un formato valido";
            } // else
        } // if
        else {
            $mensaje['archivo'] = "No se adjunto ning&uacute;n archivo";
        } // else
        // redirect admin#http://seguimiento.proyectos/seguimiento/participante/carga

        return view("Modules.Seguimiento.Participante.cargaFichas", compact("mensaje"));

    }

    private function importArrayBD($registros, $cargo) {
        $error = array();
        $exito = 0;
        $ficha = $registros["ficha"];
        $fecha = $registros["fecha"];
        $contador = (sizeof($registros)+1);
        for($i=6; $i<=$contador; $i++){
            $numero_documento = $registros[$i]["numero_documento"];
            if($numero_documento != ""){
                $nombres = $registros[$i]["nombres"];
                $apellidos = $registros[$i]["apellidos"];
                $telefono = $registros[$i]["telefono"];
                $par_correo = $registros[$i]["correo"];

                $validar_participante = DB::select("select rol_id from sep_participante where par_identificacion ='$numero_documento' limit 1");
                if(count($validar_participante) == 0){
                    $sep_participante = "
                        insert into sep_participante 
                        values  ('$numero_documento','$numero_documento', '$nombres', '$apellidos', '', '$telefono', '$par_correo', 2, 1, 0, default, default, null)";
                    DB::insert($sep_participante);

                    $clave = \Hash::make($numero_documento);
                    $users = "
                        insert into users 
                        values  (default, '$numero_documento', '$par_correo', '$clave', 'na', '1', null, default, default)";
                    DB::insert($users);
                    $id = DB::getPdo()->lastInsertId();

                    $user_profiles = "
                        insert into user_profiles 
                        values  (default, null, null, '', '', $id, default, default)";
                    DB::insert($user_profiles);

                    $sep_detalle_usuario_rol = "
                        insert into sep_detalle_usuario_rol 
                        values  (1, '$numero_documento', default, default)";
                    DB::insert($sep_detalle_usuario_rol);
                }

                $sep_matricula = DB::select("select est_id from sep_matricula where fic_numero = '$ficha' and par_identificacion ='$numero_documento' limit 1");
                if(count($sep_matricula) == 0){
                    $sep_matricula = "
                        insert into sep_matricula 
                        values(default, $ficha, '$numero_documento', 2, '$fecha', '$fecha', '$fecha')";
                    DB::insert($sep_matricula);
                }
                $exito++;
            }else{
                $error['errores'][] = "En la fila número $i el número de documento esta vacio.";
            }
        }
        $error['exito'] = $exito;
        
        return $error;
        dd($registros);
        /*$error = array();
        $fichasError = array();
        $exito = 0;
        dd($registros);
        foreach ($registros as $key => $registro) {

            $linea = $key + 2;

            $opEtapa = false;
            
            // Bandera para validar si el aprendiz ya existe en otra ficha
            $errorMatricula = false;

            // Bandera para validar si existe error en el registro
            $errorFila = false;

            // Se instancia el objeto SepParticipante
            $participante = new SepParticipante();

            // Se instancia el objeto SepMatricula
            $matricula = new SepMatricula();

            // Se instancia el objeto SepEtapaPractica
            $etapaPractica = new SepEtapaPractica();
            
            // Se valida si el aprendiz ya se encuentra registrado en otra ficha
			if (SepMatricula::where('par_identificacion', $registro[0])->count()) {
                $errorMatricula = true;
            }
			
			// Si existe se actualiza la ficha 
			if($errorMatricula){
				$sql = "update sep_matricula set fic_numero = ".$registro[7]."  where par_identificacion = ".$registro[0];
				DB::update($sql);
				$error[$linea][] = "La ficha del aprendiz $registro[0] $registro[1] ha sido modificada";
				continue;
			}
            
            // Se valida si el registro ya existe en la BD
            if (SepParticipante::where('par_identificacion', $registro[0])->count()) {
                //$error[$linea][] = "Identificador duplicado"; 
                $errorFila = true;
            }

            if (SepParticipante::where('par_correo', $registro[5])->count()) {
                $error[$linea][] = "El correo ya se encuentra registrado";
                $errorFila = true;
            }
            
            if (trim($registro[5])=="") {
                $error[$linea][] = "El correo es obligatorio";
                $errorFila = true;
            }

            $participante->rol_id = $cargo;

            // Se valida si el cargo existe en la base de datos
//                if(strtoupper($registro[6]) == "APRENDIZ"){
//                    $participante->rol_id = 1;
//                } // if
//                elseif(strtoupper($registro[6]) == "INSTRUCTOR"){
//                    $participante->rol_id = 2;
//                } // if
//                else{
//                    $error[$linea][] = "No existe el cargo $registro[6] en la BD";
//                    $errorFila = true;
//                } // else

            if ($cargo == 1) {
                // validacion de la ficha
                if (!SepFicha::where('fic_numero', $registro[7])->count()) {
                    if (!in_array($registro[7], $fichasError)) {
                        $error[$linea][] = "La ficha <strong># " . $registro[7] . "</strong> no existe en la base de datos";
                        $fichasError[] = $registro[7];
                    }
                    $errorFila = true;
                } // if

                if (!in_array(strtoupper($registro[8]), getEstados())) {
                    $error[$linea][] = "El estado <strong>" . $registro[8] . "</strong> no existe en la base de datos";
                    $errorFila = true;
                } else {
                    $estado = array_keys(getEstados(), strtoupper($registro[8]));
                    $estado = $estado[0] + 1;
                }

                if (!in_array(strtoupper($registro[12]), getOpcionEtapa())) {
                    if ($registro[12] != "NO APLICA" && $registro[12] != "" && $registro[12] != "DISPONIBLE") {
                        $error[$linea][] = "La opci&oacute;n de etapa practica <strong>" . $registro[12] . "</strong> no existe en la base de datos";
                        $errorFila = true;
                    }
                } else {
                    $opEtapa = array_keys(getOpcionEtapa(), strtoupper($registro[12]));
                    $opEtapa = $opEtapa[0] + 1;
                }
            }
            // Inserta en la base de si no hay errores en el registro
            if ($errorFila) {
                continue;
            } // if
            // Si no hay errores, se insertan los datos en la BD en la tabla participante
            $participante->par_identificacion = $registro[0];
            $participante->par_nombres = $registro[1];
            $participante->par_apellidos = $registro[2];
            $participante->par_direccion = $registro[3];
            $participante->par_telefono = $registro[4];
            $participante->par_correo = $registro[5];

            $participante->save();

            if ($cargo == 1) {
                // Si no hay errores, se insertan los datos en la BD en la tabla matricula
                $matricula->fic_numero = $registro[7];
                $matricula->par_identificacion = $registro[0];
                $matricula->est_id = $estado;
                $matricula->mat_fecha_fin_practica = $registro[9];

                $matricula->save();

                if ($opEtapa) {
                    // Si no hay errores, se insertan los datos en la BD en la tabla etapa_practica
                    $etapaPractica->par_identificacion = $registro[0];
                    $etapaPractica->ope_id = $opEtapa;
                    $etapaPractica->etp_fecha_registro = $registro[10];

                    $etapaPractica->save();
                }
            }

            $id = DB::table('users')->insertGetId(array(
                'par_identificacion' => $registro[0],
                'email' => $registro[5],
                'password' => \Hash::make($registro[0]),
                'gender' => "na",
                'estado' => 1
            ));

            \DB::table('user_profiles')->insert(array(
                'user_id' => $id,
                'birthdate' => "",
                'observations' => ""
            ));

            \DB::table('sep_detalle_usuario_rol')->insert(array(
                'id_rol' => $cargo,
                'id_usuario' => $registro[0]
            ));

            $exito++;
        } // 

        $errores = array();
        $errores["exito"] = $exito;

        if ($error) {
            $errores["errores"] = $error;
        } // if
        */
        return $errores;
    }
    

// importArrayBD

private function importArrayBDFichas($registros) {

        $error = array();
        $fichasError = array();
        $exito = 0;
        foreach ($registros as $key => $registro) {
            
            
            // Bandera para validar si existe error en el registro
            $errorFila = false;

            // Se instancia el objeto SepParticipante
            $participante = new SepParticipante();

            // Se instancia el objeto SepMatricula
            $ficha = new SepFicha();


            if (SepParticipante::where('par_identificacion', $registro[0])->count()) {
                //$error[$linea][] = "Identificador duplicado"; 
                $errorFila = true;
            }

//dd($registro);            
           
            // Inserta en la base de si no hay errores en el registro
            if ($errorFila) {
                continue;
            } // if
            
            // Se valida si el registro ya existe en la BD
            if (SepFicha::where('fic_numero', $registro[0])->count()) {
                 $sqlUpdate="update sep_ficha set "
                         . "fic_fecha_inicio='" . $registro[2] . "', "
                         . "fic_fecha_fin='" . $registro[3] . "', "
                         . "par_identificacion='" . $registro[4] . "', "
                         . "par_identificacion_coordinador='" . $registro[5] . "' "
                         . "where fic_numero='" . $registro[0] . "'";
                // dd($sqlUpdate);
                 DB::update($sqlUpdate);
            }
            else{
                //insert
                $ficha->fic_numero = $registro[0];
                $ficha->prog_codigo = $registro[1];
                $ficha->cen_codigo = '1';
                $ficha->fic_fecha_inicio = $registro[2];
                $ficha->fic_fecha_fin = $registro[3];
                $ficha->par_identificacion = $registro[4];
                $ficha->fic_estado = 'A';
                $ficha->fic_localizacion = 'PONDAJE';
                $ficha->fic_version_matriz = '0';
                $ficha->act_version = '0';
                $ficha->fic_proyecto = 'PROYECTO ' . $registro[0];
                $ficha->par_identificacion_coordinador = $registro[5];
                
                if($registro[6]=="OPERARIO"){
                    $lect=3;
                }
                else if($registro[6]=="ESPECIALIZACI���N TECNOL���GICA" || $registro[6]=="T���CNICO"){
                    $lect=6;
                }
                else{
                    $lect=18;
                }
                
                $ficha->fic_duracion_lectiva = $lect;
                
                if($registro[6]=="OPERARIO"){
                    $prac=3;
                }
                else if($registro[6]=="ESPECIALIZACI���N TECNOL���GICA"){
                    $prac=0;
                }
                else{
                    $prac=6;
                }
                
                $ficha->fic_duracion_productiva = $prac;
                $ficha->par_identificacion_productiva = $registro[4];
                
                $ficha->save();

            }
            
            
            // Si no hay errores, se insertan los datos en la BD en la tabla participante
            
            
            

            $exito++;
        } // 

        $errores = array();
        $errores["exito"] = $exito;

        if ($error) {
            $errores["errores"] = $error;
        } // if

        return $errores;

    }
    
	public function getCargaestadoaprendiz() {
        return view("Modules.Seguimiento.Participante.cargaEstadoAprendiz");
    }
    
	public function postCargaestadoaprendiz(Request $Request) {
		// Si existe el archivo con el nombre archivoCSV entre
        if($Request->hasFile('archivoCsv')){
			// Guardamos en una variable el archivo
			$archivo = $Request->file('archivoCsv');
			
			// Se valida que el archivo subido sea de extensi���n xls o xlsx
			if($archivo->getClientOriginalExtension() == "xls" || $archivo->getClientOriginalExtension() == "xlsx"){
				// Nombre original del archivo
				$nombreArchivo = time()." - ".$archivo->getClientOriginalName();
				
				// Ruta donde se guardar��� el archivo excel
				$rutaCsv = getPathUploads()."/CSV/EstadoAprendices";
				
				// Movemos el archivo a la ruta asignada
				$archivo->move($rutaCsv,$nombreArchivo);
                $arrayResultados = leerExcelEstadoAprendiz($rutaCsv,$nombreArchivo);
                //dd($arrayResultados);
                //ini_set ('max_execution_time', 300);
                //set_time_limit(120);
				//echo '<pre>';
                foreach($arrayResultados as $key => $val){
					DB::beginTransaction();
                    foreach($val as $key1 => $val1){
						$documentos = '';
						foreach($val1['documento'] as $key2 => $val2){
							$documentos .=  '"'.$val2.'",';
						}
						$documentos = substr($documentos,0,-1);
						$concatenar = '
							update 	sep_matricula 
							set 	est_id = '.$key.' 
							where 	fic_numero = "'.$key1.'" 
							and 	par_identificacion in ('.$documentos.')';
						DB::update($concatenar);
					}
					DB::commit();
                }
			}
		}else{
			$mensaje['archivo'] = "No se adjunto ning&uacute;n archivo";
		}
		
		return view("Modules.Seguimiento.Participante.cargaEstadoAprendiz", compact("mensaje"));
    }
    
    public function getActualizaridentificacion(){
		extract($_POST);
		
		if(isset($identificacion_ficha)){
			if($buscarPor == 1){
				$sql = "
					select	par.par_identificacion, fic_numero, par_identificacion_actual, par_nombres, par_apellidos, par_correo,par_telefono 
					from 	sep_participante par, sep_matricula mat
					where 	par.par_identificacion = mat.par_identificacion
					and		par_identificacion_actual = $identificacion_ficha
					and 	rol_id = 1";
			}else {
				$sql = "
					select	par.par_identificacion, fic_numero, par_identificacion_actual, par_nombres, par_apellidos, par_correo,par_telefono 
					from 	sep_participante par, sep_matricula mat 
					where 	par.par_identificacion = mat.par_identificacion
					and 	mat.fic_numero = '$identificacion_ficha'";
			}
			
			$participante = DB::select($sql);
			$contador = 0;
			return view("Modules.Seguimiento.Participante.actualizarIdentificacion",compact('participante','contador'));
		}else{
			return view("Modules.Seguimiento.Participante.actualizarIdentificacion");
		}
		
	}
	
	public function postActualizaridentificacion(){
		return $this->getActualizaridentificacion($_POST['identificacion_ficha'],$_POST['buscarPor']);
	}
	
	public function getCambiardocumento(){
		extract($_GET);
		
		$sql = "
				select	par.par_identificacion, fic_numero, par_identificacion_actual, par_nombres, par_apellidos, par_correo,par_telefono 
				from 	sep_participante par, sep_matricula mat
				where 	par.par_identificacion = mat.par_identificacion
				and		par.par_identificacion = '$par_identificacion'
				and 	rol_id = 1";
		$participante = DB::select($sql);
		
		return view("Modules.Seguimiento.Participante.cambiarDocumento",compact('participante'));
	}
	
	public function postCambiardocumento(){
		extract($_POST);
		
		$reglas = Array(
            'par_identificacion_actual' => 'required | min:6 | unique:sep_participante'
        );

        // Mensajes de error para los diferentes campos
        $messages = [
            'par_identificacion_actual.required' => 'El n&uacute;mero de documeto es obligatorio',
            'par_identificacion_actual.min' => 'El debe de contener como minimo 6 caracteres',
            'par_identificacion_actual.unique' => 'El n&uacute;mero de documento ya est��� registrado.',
        ];

        // Se ejecutan las reglas para la informaci���n recibida por POST
        $validacion = Validator::make($_POST, $reglas, $messages);
		
        /*
         * Se verifica si existen errores, en tal caso se redirecciona 
         * a la vista de donde se recibio el POST y muestra el respectivo
         * mensaje de error
         */

        if ($validacion->fails()) {
            return redirect()->back()->withErrors($validacion->errors())->withInput();
        }
		
		$sql = "update sep_participante set par_identificacion_actual = '$par_identificacion_actual' where par_identificacion = '$par_identificacion'";
		DB::update($sql);
		
		session()->set('editarDocumento','Si');
	
		return redirect(url("seguimiento/participante/actualizaridentificacion"));
	}

}
