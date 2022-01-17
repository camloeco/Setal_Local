<?php

namespace App\Http\Controllers\Modules\Seguimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Http\Models\Modules\Seguimiento\SepPrograma;
use App\Http\Models\Modules\Seguimiento\SepCompetencia;
use App\Http\Models\Modules\Seguimiento\SepResultado;
use App\Http\Models\Modules\Seguimiento\SepFase;
use App\Http\Models\Modules\Seguimiento\SepActividad;
use App\Http\Models\Modules\Seguimiento\SepActividadResultado;
use \Illuminate\Pagination\LengthAwarePaginator;
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

ini_set("memory_limit", "2048M");

//Se elimina el tiempo limite para el proceso
//set_time_limit ( 0 );

class ProgramaController extends Controller {

    private $codigoPrograma = "";
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct() {
        $this->middleware('auth');		$this->middleware('control_roles');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function postModaleditarprograma(){
        $id = $_POST['id'];
        $programa = DB::select('select * from sep_programa where prog_codigo = '.$id);
        $niveles_formacion = DB::select('select * from sep_nivel_formacion where not niv_for_id = 3');

        return view('Modules.Seguimiento.Programa.modalEditarPrograma',compact('id','programa','niveles_formacion'));
    }

    public function postGuardarcambiosmodal(){
        $_POST = $this->seguridad($_POST);
        extract($_POST);
        if(!is_numeric($prog_codigo)){
            dd('El código debe ser numérico');
        }
        
        $sql = '
            update  sep_programa 
            set     prog_nombre = "'.mb_strtoupper($prog_nombre, 'UTF-8').'",
                    niv_for_id = '.$niv_for_id.', prog_sigla = "'.mb_strtoupper($prog_sigla, 'UTF-8').'"
            where   prog_codigo = '.$prog_codigo;
        DB::update($sql);
    }

    public function seguridad($array){
        // Quitamos los simbolos no permitidos de cada variable recibida, 
        // para evitar ataques XSS e Inyección SQL
        $caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';',':','--');
        $array = str_replace($caractereNoPremitidos,'',$array);
        return  $array;
    }

    public function getIndex(){
        extract($_GET);
        // Paginado
        $resgistroPorPagina = 20;
		$limit = $resgistroPorPagina;
		if(isset($pagina)){
			$hubicacionPagina = $resgistroPorPagina*($pagina-1);
			$limit = $hubicacionPagina.','.$resgistroPorPagina;
        }else{
            $pagina = 1;
        }

        // Validar busqueda del programa
        $concatenarProgramaPrimeraSQL = '';
        $concatenarProgramaSegundaSQL = '';
        if(isset($_GET['prog_codigo'])){
            $concatenarProgramaPrimeraSQL = ' and pro.prog_codigo = "'.$prog_codigo.'" ';
            $concatenarProgramaSegundaSQL = ' and prog_codigo = "'.$prog_codigo.'" ';
        }else{
            $prog_codigo = '';
        }

        // Consulta de todos los programas
        $sql ='select prog_codigo, prog_nombre from sep_programa where not prog_codigo in(51250028,1,0,"") order by prog_nombre asc';
        $programasBuscar = DB::select($sql);

        // Consulta programas de formación
        $sql = '
            select  prog_codigo, prog_nombre, prog_sigla, niv_for_nombre, pro_url_plan_trabajo
            from    sep_programa pro, sep_nivel_formacion niv_for 
            where   pro.niv_for_id = niv_for.niv_for_id and not	pro.prog_codigo in(51250028,1,0,"") '.$concatenarProgramaPrimeraSQL.'
            order   by prog_nombre, prog_codigo asc  limit '.$limit;
        $sqlContador = '
            select  count(prog_nombre) as total 
            from    sep_programa pro
            where   not	prog_codigo in(51250028,1,0,"") '.$concatenarProgramaSegundaSQL;
        
        // Paginado
		$programas = DB::select($sql);
		$programaContador = DB::select($sqlContador);
		$contadorProgramas = $programaContador[0]->total;
        $cantidadPaginas = ceil($contadorProgramas/$resgistroPorPagina);
        $contador = (($pagina-1)*$resgistroPorPagina)+1;

        // Rolres permitidos para editar
        $permisoRol = array(0,5);
        $rol = \Auth::user()->participante->rol_id;
        
        return view('Modules.Seguimiento.Programa.index',compact('rol', 'permisoRol', 'programasBuscar','prog_codigo','contadorProgramas','programas','cantidadPaginas','contador','pagina'));
    }
        
    public function postIndex(){
        return $this->getIndex($_POST['codigo'], $_POST['campo']);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
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
        $programa = SepPrograma::find($id);

        return view("Modules.Seguimiento.Programa.show", compact("programa"));
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

    public function getCreate() {

        return view("Modules.Seguimiento.Programa.create");
    }

// getCarga

    public function getCrear() {
        $nivel_formacion = DB::select("select * from sep_nivel_formacion where not niv_for_id in(3,6)");
        return view("Modules.Seguimiento.Programa.crear",compact('nivel_formacion'));
    }

    public function postCrear() {
         extract($_POST);
        $reglas = Array(
            'codigo' => 'required | min:6 | unique:sep_programa,prog_codigo',
            "prog_nombre" => "required",
            "niv_for_id" => "required"
        );

        // Mensajes de error para los diferentes campos
        $messages = [
            'codigo.required' => 'El campo c&oacute;digo del programa es obligatorio',
            'codigo.min' => 'El campo c&oacute;digo del programa debe contener minimo 6 caracteres',
            'codigo.unique' => 'El campo c&oacute;digo del programa ya existe en la base de datos',
            "prog_nombre.required" => "El campo Nombre del programa es obligatorio"
        ];

        // Se ejecutan las reglas para la información recibida por POST
        $validacion = Validator::make($_POST, $reglas, $messages);


        /*
         * Se verifica si existen errores, en tal caso se redirecciona 
         * a la vista de donde se recibio el POST y muestra el respectivo
         * mensaje de error
         */

        if ($validacion->fails()) {
            // Se crea sesion para mantener los datos del formulario
            //Session::flash('form_barrio',$_POST);

            return redirect()->back()
                            ->withErrors($validacion->errors())->withInput();
        }

       

        DB::insert("INSERT INTO sep_programa(prog_codigo,prog_nombre,niv_for_id,prog_pdf,prog_matriz_excel) VALUES(?,?,?,?,?)", array($codigo, mb_strtoupper($prog_nombre, 'UTF-8'),$niv_for_id, "planeacion.pdf", 0));
        DB::insert("INSERT INTO sep_actividad(act_descripcion,prog_codigo,fas_id,act_version,act_estado) VALUES(?,?,?,?,?)", array("ESTANDARD", $codigo, 1, 0, 1));

        return redirect(url("seguimiento/programa/index"));
    }

    public function postAcentar() {
        
        $registro = Session::get("registro");
        
        $fase = 0;
        for ($i = 0; $i < 4; $i++) {
            // Convertir archivo CSV a un arreglo
            $registros = $registro[$i];

            if ($i == 0) {
                if($this->codigoPrograma=="") $this->codigoPrograma =  $registros[0]['codigo'];
                if (!SepPrograma::where('prog_codigo', $registros[0]['codigo'])->count()) {

                    $programa = new SepPrograma();

                    $programa->prog_codigo = $registros[0]['codigo'];
                    $programa->prog_nombre = $registros[0]['programa'];
                    $programa->prog_pdf = "planeación.pdf";
                    $programa->prog_matriz_excel = "matriz.xls";

                    $programa->save();
                }

                $version = SepActividad::where('prog_codigo', $registros[0]['codigo'])->max("act_version");
                if (!$version) {
                    $version = 0;
                }
                DB::update("UPDATE sep_programa SET prog_matriz_excel='matriz.xls' "
                        . "WHERE prog_codigo=" . $registros[0]['codigo']);
            }
            $this->insertarActividades($registros, $fase++, $version);
            //$mensaje['errores'] = $this->importArrayBD($registros, $prog_codigo);
        }
        
        // Enviar codigo $codigo
        return redirect(url("seguimiento/programa/index"));
        
    }

    public function postCreate(Request $request) {
        // ¿Se ha cargado el archivo CSV?
        if ($request->hasFile('archivoCsv')) {

            // ¿El archivo cumple con el formato esperado - EXCEL (xlsx) ?
            $archivo = $request->file('archivoCsv');
            if ($archivo->getClientOriginalExtension() == "xlsx") {
                $filename = time().'.xlsx';

                // Configuracion del directorio multimedia
                $path = public_path() .  "/Modules/Seguimiento/Programa/PlanDeTrabajo";

                // Se mueve el archivo Excel al directorio multimedia
                $archivo->move($path, $filename);

                // Convertir archivo XLSX a un arreglo
                $mensaje = $this->leerExcelPlanDeTrabajo($path, $filename);
            }else {
                $mensaje['error'][] = "El archivo no cumple con el formato esperado - xlsx(Libro de excel), por favor cargar un formato valido";
            }
        }
        else {
            $mensaje['error'][] = "No se adjunto ning&uacute;n archivo";
        }

        return view("Modules.Seguimiento.Programa.create", compact("mensaje"));
    }

    public function leerExcelPlanDeTrabajo($path, $filename){
        // Leemos el archivo de excel cargado
        $mensaje = array();
        $objReader = new \PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load($path . "/" . $filename);
        $objPHPExcel->setActiveSheetIndex(0);

        $prog_codigo = (String) $objPHPExcel->getActiveSheet()->getCell('D4');
        if($prog_codigo == ''){
            $mensaje['error'][] = 'El valor en la celda de código de programa(D4) es obligatorio';
        }else if(!is_numeric($prog_codigo)){
            $mensaje['error'][] = 'El valor en la celda de código de programa(D4) debe ser numérico';
        }
        $sql = 'select prog_nombre from sep_programa where prog_codigo = '.$prog_codigo.' limit 1';
        $validarExistenciaPrograma = DB::select($sql);
        if(count($validarExistenciaPrograma) == 0){
            $mensaje['error'][] = 'El programa con código <strong>'.$prog_codigo.'</strong> no existe en nuestra base de datos, debe registrarlo primero.';
        }

        $sql = 'select pla_id from sep_plantilla where prog_codigo = '.$prog_codigo.' limit 1';
        $validar = DB::select($sql);
        if(count($validar)>0){
            $mensaje['error'][] = 'El programa con código <strong>'.$prog_codigo.'</strong> ya está registrado';
        }

        $tipo = array(0=>1, 1=>2, 2=>2, 3=>2, 4=>2, 5=>7, 6=>6);
        $fila = 14;
        $registro = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
        $caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';',':','--');
        while(trim($registro) != "") {
            $fase = (int) $objPHPExcel->getActiveSheet()->getCell('A' . $fila)->getCalculatedValue();
            $competencia = str_replace($caractereNoPremitidos, '', mb_convert_encoding((String) $objPHPExcel->getActiveSheet()->getCell('B' . $fila),'HTML-ENTITIES', 'UTF-8'));
            $resultado = str_replace($caractereNoPremitidos, '', mb_convert_encoding((String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila),'HTML-ENTITIES', 'UTF-8'));
            $actividad = str_replace($caractereNoPremitidos, '', mb_convert_encoding((String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila),'HTML-ENTITIES', 'UTF-8'));
            $hor_totales = (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila)->getCalculatedValue();
            $hor_presenciales = (String) $objPHPExcel->getActiveSheet()->getCell('F' . $fila)->getCalculatedValue();
            $hor_autonomas = (String) $objPHPExcel->getActiveSheet()->getCell('G' . $fila)->getCalculatedValue();

            // Validaciones
            if(!is_numeric($fase)){
                $mensaje['error'][] = 'El valor en la columna fase en la fila # <strong>'.$fila.'</strong> debe ser númerico';
            }else if($fase < 0 or $fase >6){
                $mensaje['error'][] = 'La columna fase en la fila # <strong>'.$fila.'</strong> debe estar entre el 0 y 6';
            }

            if($competencia == ''){
                $mensaje['error'][] = 'La columna competencia en la fila # <strong>'.$fila.'</strong> no puede estar vacia';
            }

            if($resultado == ''){
                $mensaje['error'][] = 'La columna resultado en la fila # <strong>'.$fila.'</strong> no puede estar vacia';
            }

            if($actividad == ''){
                $mensaje['error'][] = 'La columna actividad en la fila # <strong>'.$fila.'</strong> no puede estar vacia';
            }

            if($hor_totales == ''){
                $mensaje['error'][] = 'El valor en la columna Horas totales(HoT) en la fila # <strong>'.$fila.'</strong> no puede estar vacio';
            }else if(!is_numeric($hor_totales)){
                $mensaje['error'][] = 'El valor en la columna Horas totales(HoT) en la fila # <strong>'.$fila.'</strong> debe ser numérico';
            }

            if($hor_presenciales == ''){
                $mensaje['error'][] = 'El valor en la columna Horas presenciales(HoP) en la fila # <strong>'.$fila.'</strong> no puede estar vacio';
            }else if(!is_numeric($hor_presenciales)){
                $mensaje['error'][] = 'El valor en la columna Horas presenciales(HoP) en la fila # <strong>'.$fila.'</strong> debe ser numérico';
            }

            if($hor_autonomas == ''){
                $mensaje['error'][] = 'El valor en la columna Horas autonomas(HoA) en la fila # <strong>'.$fila.'</strong> no puede estar vacio';
            }else if(!is_numeric($hor_autonomas)){
                $mensaje['error'][] = 'El valor en la columna Horas autonomas(HoA) en la fila # <strong>'.$fila.'</strong> debe ser numérico';
            }

            if(isset($tipo[$fase])){
                $tipo_materia = $tipo[$fase];
                if($fase == 0 or $fase == 5 or $fase == 6){
                    $fase = 5;
                }
            }else{
                $fase = '';
                $tipo_materia = '';
            }

            $horario['fase'][] = $fase;
            $horario['competencia'][] = $competencia;
            $horario['resultado'][] = $resultado;
            $horario['actividad'][] = $actividad;
            $horario['hor_totales'][] = $hor_totales;
            $horario['hor_presenciales'][] = $hor_presenciales;
            $horario['hor_autonomas'][] = $hor_autonomas;
            $horario['pla_tip_id'][] = $tipo_materia;

            $fila++;
            $registro = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
        }

        if(!isset($mensaje['error'])){
            $par_identificacion = \Auth::user()->participante->par_identificacion;
            $sql = '
                insert into sep_plantilla
                (pla_id, prog_codigo, pla_fecha_creacion, pla_usuario_creo, pla_estado)
                values
                (default, '.$prog_codigo.', default, '.$par_identificacion.',"1")';
            DB::insert($sql);
            $pla_id = DB::getPdo()->lastInsertId();

            foreach($horario['fase'] as $key => $fase){
                $competencia = $horario['competencia'][$key];
                $resultado = $horario['resultado'][$key];
                $actividad = $horario['actividad'][$key];
                $hor_totales = $horario['hor_totales'][$key];
                $hor_presenciales = $horario['hor_presenciales'][$key];
                $hor_autonomas = $horario['hor_autonomas'][$key];
                $tipo_materia = $horario['pla_tip_id'][$key];

                $sql = '
                    insert into sep_plantilla_detalle
                        (pla_det_id, pla_id, fas_id, pla_tip_id, com_descripcion,
                        act_descripcion, res_descripcion, pla_can_hor_total, pla_can_hor_presenciales, 
                        pla_can_hor_autonomas, pla_det_estado)
                    values
                        (default, '.$pla_id.', '.$fase.', '.$tipo_materia.', "'.$competencia.'", 
                        "'.$actividad.'", "'.$resultado.'", '.$hor_totales.', '.$hor_presenciales.', 
                        '.$hor_autonomas.', "1")';
                DB::insert($sql);
            }

            $sql = '
                update  sep_programa
                set     pro_url_plan_trabajo = "'.$filename.'"
                where   prog_codigo = '.$prog_codigo;
            DB::update($sql);
            $programa = $validarExistenciaPrograma[0]->prog_nombre;
            $mensaje['exito'] = 'El plan de trabajo del programa <strong>'.$programa.'</strong> se a registrado exitosamente.';
        }

        return $mensaje;

        /*$sql = '
            select  pla_fic.pla_fic_id, fic.fic_numero,
                    (select count(pla_fic_act_id)
                    from    sep_planeacion_ficha_actividades pla_fic_act
                    where   pla_fic_act.pla_fic_id = pla_fic.pla_fic_id) as total
            from    sep_planeacion_ficha pla_fic, sep_ficha fic
            where   fic.fic_numero = pla_fic.fic_numero
            and     fic.prog_codigo ='.$prog_codigo.'
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
                        insert into     sep_planeacion_ficha_actividades
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
        }*/
    }

// postCarga

    private function insertarActividades($actividades, $fase, $version) {
        $fase++;
        $version++;
        
        foreach ($actividades as $key => $actividad) {

            if ((String) $key == "0")
                continue;

            $countCom = SepCompetencia::all()->max("com_codigo");

            $objCompetencia = new SepCompetencia();

            // Se valida si el registro ya existe en la BD
            $objCompetencia->com_codigo = ++$countCom;
            $objCompetencia->com_nombre = (String) $key;
            $objCompetencia->com_horas = 0;
            $objCompetencia->prog_codigo = $this->codigoPrograma;
            $objCompetencia->act_version = $version;

            $objCompetencia->save();



            foreach ($actividad as $keyCom => $competencia) {


                $count = SepActividad::all()->max("act_id");
                //dd($key);
                // Se instancia el objeto SepActividad
                $objActividad = new SepActividad();

                $objActividad->act_id = ++$count;
                $objActividad->act_descripcion = (String) $keyCom;
                $objActividad->prog_codigo = $this->codigoPrograma;
                $objActividad->fas_id = $fase;
                $objActividad->act_version = $version;
                $objActividad->act_estado = 1;
///agregar duracion
               /* foreach ($competencia as $act) {
                    if($act['duracion']==""){
                        $objActividad->act_duracion = 0; 
                    }else{
                        $objActividad->act_duracion = $act['duracion'];
                    }
                    break;
                }*/
                if($competencia['duracion']==""){
                        $objActividad->act_duracion = 0; 
                    }else{
                        $objActividad->act_duracion = $competencia['duracion'];
                    }

                $objActividad->save();
                $cuentaDur=1;
                foreach ($competencia as $act) {
                    
                    if($cuentaDur!=2){
                    // Se instancia el objeto SepResultado
                    $resultado = new SepResultado();

                    $countResultado = SepResultado::all()->max("res_id");

                    $resultado->res_id = ++$countResultado;
                    $resultado->res_nombre = $act['resultado'];
                    $resultado->com_codigo = $countCom;
                    $resultado->act_version = $version;

                    $resultado->save();

                    // Se instancia el objeto SepActividadResultado
                    $actividadResultado = new SepActividadResultado();

                    $actividadResultado->act_id = $count;
                    $actividadResultado->res_id = $countResultado;
                    $actividadResultado->fas_id = $fase;
                    // $actividadResultado->acr_duracion = $act['duracion'];
////quitar acr_duracion
                    $actividadResultado->save();
                    }
                    $cuentaDur++;
                }
            }
        }

        if ($version == 1) {
            $sql = "UPDATE sep_ficha SET act_version='1', fic_version_matriz='1' "
                    . "WHERE prog_codigo=" . $this->codigoPrograma;
            DB::update($sql);
        }
    }

    private function importArrayBD($registros, $prog_codigo) {

        $count = SepResultado::all()->max("res_id");

        $error = array();
        $exito = 0;
        foreach ($registros as $key => $registro) {

            // Se instancia el objeto SepCompetencia
            $competencia = new SepCompetencia();

            // Se instancia el objeto SepResultado
            $resultado = new SepResultado();

            // Se valida si el registro ya existe en la BD
            if (!SepCompetencia::where('com_codigo', trim($registro[0]))->count()) {
                $competencia->com_codigo = $registro[0];
                $competencia->com_nombre = $registro[1];
                $competencia->com_horas = $registro[2];
                $competencia->prog_codigo = $prog_codigo;

                $competencia->save();
            }

            $resultado->res_id = ++$count;
            $resultado->res_nombre = $registro[3];
            $resultado->com_codigo = $registro[0];

            $resultado->save();

            $exito++;
        } // 

        $errores = array();
        $errores["exito"] = $exito;

        if ($error) {
            $errores["errores"] = $error;
        } // if

        return $errores;
    }

// importArrayBD

    public function getPlaneacion($prog_codigo) {

        $fase = "";

        $version = SepActividad::where('prog_codigo', $prog_codigo)->max("act_version");
        if (!$version)
            $version = 0;

        $actividades = SepActividad::where('prog_codigo', $prog_codigo)
                ->where('act_version', $version)
                ->get();

        foreach ($actividades as $actividad) {
            $actividadResultado[$actividad->act_id] = SepActividadResultado::where('act_id', $actividad->act_id)->sum('acr_duracion');
        }

        $programa = SepPrograma::find($prog_codigo);
        $fases = SepFase::all()->lists('fas_descripcion', 'fas_id');

        $competencias = $programa->competencias->lists('com_nombre', 'com_codigo');

        foreach ($programa->competencias as $competencia) {
            $resultados[$competencia->com_nombre] = $competencia->resultados->lists('res_nombre', 'res_id');
        } // foreach

        return view("Modules.Seguimiento.Programa.planeacion", compact('programa', 'fases', 'competencias', 'resultados', 'actividades', 'actividadResultado', 'fase', 'version'));
    }

    public function postPlaneacion(Request $request) {


        $version = $request->input('version');
        $fase = $request->input('fase');


        $prog_codigo = $request->input('programa');

        if ($fase == "") {
            $actividades = SepActividad::where('prog_codigo', $prog_codigo)
                    ->where('act_version', $version)
                    ->get();
        } else {
            $actividades = SepActividad::where('prog_codigo', $prog_codigo)
                    ->where('fas_id', $fase)
                    ->where('act_version', $version)
                    ->get();
        }

        foreach ($actividades as $actividad) {
            $actividadResultado[$actividad->act_id] = SepActividadResultado::where('act_id', $actividad->act_id)->sum('acr_duracion');
        }

        $programa = SepPrograma::find($prog_codigo);
        $fases = SepFase::all()->lists('fas_descripcion', 'fas_id');

        $competencias = $programa->competencias->lists('com_nombre', 'com_codigo');

        foreach ($programa->competencias as $competencia) {
            $resultados[$competencia->com_nombre] = $competencia->resultados->lists('res_nombre', 'res_id');
        } // foreach

        return view("Modules.Seguimiento.Programa.planeacion", compact('programa', 'fases', 'competencias', 'resultados', 'actividades', 'actividadResultado', 'fase', 'version'));
    }

// postPlaenacion
}
