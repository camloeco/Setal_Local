<?php

namespace App\Http\Controllers\Modules\Seguimiento;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\Modules\Seguimiento\SepFicha;
use App\Http\Models\Modules\Seguimiento\SepCentro;
use App\Http\Models\Modules\Seguimiento\SepPrograma;
use App\Http\Models\Modules\Seguimiento\SepParticipante;
use App\Http\Models\Modules\Seguimiento\SepActividad;
use DB;
use \Illuminate\Pagination\LengthAwarePaginator;
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
//
use App\Http\Models\Modules\Users\User;

class FichaController extends Controller {
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('control_roles');
		date_default_timezone_set('America/Bogota');
    }

    public function getIndex($id= false, $campo=false) {
        $page = Input::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        if($id==""){
            $sql = ' 
            select  fic_numero, prog_codigo, fic_fecha_inicio, fic_fecha_fin
            from    sep_ficha ';
        }else{
            $sql = ' 
            select  fic_numero, prog_codigo, fic_fecha_inicio, fic_fecha_fin
            from    sep_ficha 
            where   '.$campo.' like "%'.$id.'%"';
        }
        $tipos = DB::select($sql);
            
        
       
        //$tipos = DB::select("SELECT edu_falta_id,par_identificacion,par_nombres,par_apellidos, edu_falta_fecha,edu_tipo_falta_descripcion,edu_est_descripcion FROM sep_edu_falta NATURAL JOIN sep_participante NATURAL JOIN sep_edu_tipo_falta NATURAL JOIN sep_edu_estado ");

        $tipos = new LengthAwarePaginator(
                array_slice(
                        $tipos, $offset, $perPage, true
                ), count($tipos), $perPage, $page);

        $tipos->setPath("index");
        $rol = \Auth::user()->participante->rol_id;

        return view("Modules.Seguimiento.Ficha.index", compact('rol',"tipos", "offset"));

    }
    
            public function postIndex(){
            return $this->getIndex($_POST['numFicha'],$_POST['campo']);
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
    public function show($id) {
        //

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

    public function getEditar($id) {
        $centros = SepCentro::all()->lists('cen_nombre', 'cen_codigo');
        $programas = SepPrograma::all()->lists('prog_nombre', 'prog_codigo');
        $instructoresA = DB::select("SELECT * FROM sep_participante WHERE rol_id = 2");
        $instructores = array();
        foreach ($instructoresA as $instructor) {
            $instructores[$instructor->par_identificacion] = $instructor->par_nombres . " " . $instructor->par_apellidos;
        }
        
        $coordinadoresA = DB::select("SELECT * FROM sep_participante WHERE rol_id = 3 order by par_nombres");
        $coordinadores = array();
        foreach ($coordinadoresA as $coordinador) {
            $coordinadores[$coordinador->par_identificacion] = $coordinador->par_nombres . " " . $coordinador->par_apellidos;
        }
        
        $ficha = DB::select("SELECT * FROM sep_ficha WHERE fic_numero= ?", array($id));

        $version = DB::select("SELECT MAX(act_version) as ver FROM sep_actividad WHERE prog_codigo=?", array($ficha[0]->prog_codigo));
        
        $sql = "select pro_codigo, pro_nombre from sep_proyecto order by pro_codigo asc";
		$proyecto = DB::select($sql);

		$fic_proyecto = $ficha[0]->fic_proyecto;
		$sql = "select pro_codigo, pro_nombre from sep_proyecto where pro_codigo ='$fic_proyecto' order by pro_codigo asc";
		$proyecto2 = DB::select($sql);

        return view("Modules.Seguimiento.Ficha.editar", compact("centros", "programas", "instructores", "ficha", "version","coordinadores","proyecto","proyecto2"));

    }

    public function postEditar(Request $request) {
        $reglas = Array(
            'fic_numero' => 'required | min:6',
            "fic_proyecto" => "required | min:6",
            "cen_codigo" => "required",
            "prog_codigo" => "required",
            "act_version" => "required",
            "par_identificacion" => "required",
            "fic_fecha_inicio" => "required",
            "fic_fecha_fin" => "required",
            "fic_localizacion" => "required | min:4"
        );

        // Mensajes de error para los diferentes campos
        $messages = [
            'fic_numero.required' => 'El campo n&uacute;mero de la ficha es obligatorio',
            'fic_numero.min' => 'El campo n&uacute;mero de la ficha debe contener minimo 6 caracteres',
            "fic_proyecto.required" => "El campo Nombre del proyecto es obligatorio",
            "fic_proyecto.min" => "El campo Nombre del proyecto debe contener minimo 6 caracteres",
            "cen_codigo.required" => "El campo Centro de Formaci&oacute;n es obligatorio",
            "prog_codigo.required" => "El campo Programa de Formaci&oacute;n es obligatorio",
            "act_version.required" => "El campo Versi&oacute;n es obligatorio",
            "par_identificacion.required" => "El campo Instructor L&iacute;der es obligatorio",
            "fic_fecha_inicio.required" => "El campo duraci&oacute;n es obligatorio",
            "fic_fecha_fin.required" => "El campo duraci&oacute;n es obligatorio",
            "fic_localizacion.required" => "El campo Localizaci&oacute;n es obligatorio",
            "fic_localizacion.min" => "El campo Localizaci&oacute;n debe contener minimo 4 caracteres"
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



        $version = SepActividad::where('prog_codigo', $request->input('prog_codigo'))->max("act_version");


        $inicio = $request->input('fic_fecha_inicio');
        $fin = $request->input('fic_fecha_fin');

        $inicio = substr($inicio, 0, 10);
        $fin = substr($fin, 0, 10);

        $fic_numero = $request->input('fic_numero');
        $prog_codigo = $request->input('prog_codigo');
        $cen_codigo = $request->input('cen_codigo');
        $fic_fecha_inicio = $inicio;
        $fic_fecha_fin = $fin;
        $par_identificacion = $request->input('par_identificacion');
        $par_identificacionC = $request->input('par_identificacionC');
        $fic_proyecto = $request->input('fic_proyecto');
        $fic_estado = "A";
        $fic_localizacion = $request->input('fic_localizacion');
        $act_version = $request->input('act_version');
        $fic_version_matriz = $version;

        $sql = "UPDATE sep_ficha SET "
                . "prog_codigo= ?, "
                . "cen_codigo= ?, "
                . "fic_fecha_inicio= ?, "
                . "fic_fecha_fin= ?, "
                . "par_identificacion= ?, "
                . "fic_proyecto= ?, "
                . "fic_localizacion= ?, "
                . "fic_version_matriz= ?, "
                . "act_version= ?,"
                . "par_identificacion_coordinador= ? "
                . "WHERE fic_numero= ?";

        DB::update($sql, array($prog_codigo, $cen_codigo, $fic_fecha_inicio, $fic_fecha_fin, $par_identificacion, $fic_proyecto, $fic_localizacion, $fic_version_matriz, $act_version,$par_identificacionC, $fic_numero));

        return redirect(url("seguimiento/ficha/index"));

    }

    function getEliminar($id) {

        $datos = DB::select("SELECT * FROM sep_ficha WHERE fic_numero = ? ", array($id));
        $datos = $datos[0];

        return view("Modules.Seguimiento.Ficha.eliminar", compact("datos"));

    }

    function postEliminar() {

        //Campos por post
        $fic_numero = $_POST['fic_numero'];

        DB::delete("DELETE FROM sep_ficha WHERE fic_numero= ?", array($fic_numero));

        return redirect(url("seguimiento/ficha/index"));

    }
    
    public function getCreate() {
        $sql = '
            select  prog_codigo, prog_nombre
            from    sep_programa
            where   prog_codigo not in("1","0","")
            order by    prog_nombre';
        $programas = DB::select($sql);

        $instructoresA = DB::select("SELECT * FROM sep_participante WHERE rol_id = 2");
        $instructores = array();
        foreach ($instructoresA as $instructor) {
            $instructores[$instructor->par_identificacion] = $instructor->par_nombres . " " . $instructor->par_apellidos;
        }
        
        $coordinadoresA = DB::select("SELECT * FROM sep_participante WHERE rol_id = 3 order by par_nombres");
        $coordinadores = array();
        foreach ($coordinadoresA as $coordinador) {
            $coordinadores[$coordinador->par_identificacion] = $coordinador->par_nombres . " " . $coordinador->par_apellidos;
        }
        
        $sql = "select pro_codigo, pro_nombre from sep_proyecto";
		$proyecto = DB::select($sql);

        return view("Modules.Seguimiento.Ficha.create", compact("centros", "programas", "instructores","coordinadores","proyecto"));
    }

    public function postCreate(Request $request) {
        $reglas = Array(
            'fic_numero' => 'required | numeric',
            "prog_codigo" => "required",
            "par_identificacionC" => "required",
            "fic_fecha_inicio" => "required",
            "fic_fecha_fin" => "required",
            "fic_localizacion" => "required",
            "fic_proyecto" => "required",
        );

        // Mensajes de error para los diferentes campos
        $messages = [
            'fic_numero.required' => 'El campo n&uacute;mero de la ficha es obligatorio',
            "prog_codigo.required" => "El campo Programa de Formaci&oacute;n es obligatorio",
            "par_identificacionC.required" => "El campo Coordinador es obligatorio",
            "fic_fecha_inicio.required" => "El campo duraci&oacute;n es obligatorio",
            "fic_fecha_fin.required" => "El campo duraci&oacute;n es obligatorio",
            "fic_localizacion.required" => "El campo localizaci&oacute;n es obligatorio",
            "fic_proyecto.required" => "El campo c&oacute;digo proyecto es obligatorio",
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

            return redirect()->back()->withErrors($validacion->errors())->withInput();
        }
    
        $_POST = $this->seguridad($_POST);
        extract($_POST);

        $sql = '
            select fic_numero
            from    sep_ficha
            where   fic_numero = "'.$fic_numero.'"';
        $validar_ficha = DB::select($sql);

        if (count($validar_ficha) > 0) {
            $mensaje['error'][] = 'Ya existe la ficha # '.$fic_numero.' en la base de datos.';
        }

        if($fic_fecha_inicio > $fic_fecha_fin){
            $mensaje['error'][] = 'La fecha inicio debe ser menor a la fecha fin.';
        }

        if (!isset($mensaje['error'])){
            $inicio = $fic_fecha_inicio;
            $fin = $fic_fecha_fin;

            $inicio = substr($inicio, 0, 10);
            $fin = substr($fin, 0, 10);

            $sql = '
                insert into sep_ficha
                    (fic_numero, par_identificacion_coordinador, prog_codigo, 
                    cen_codigo, fic_fecha_inicio, fic_fecha_fin, fic_estado,
                    fic_localizacion, fic_proyecto)
                values 
                    ("'.$fic_numero.'", "'.$par_identificacionC.'", "'.$prog_codigo.'", 
                    "1", "'.$inicio.'", "'.$fin.'", "A", "'.$fic_localizacion.'", "'.$fic_proyecto.'")';
            DB::insert($sql);
            $mensaje['exito'] = 'Se creo la ficha satisfactoriamente.';
        }

        session()->put('mensaje',$mensaje);
        echo "<script> window.history.back(); </script>";
        //return redirect(url("seguimiento/ficha/create"));
    }

// getCarga

    function getVersion(Request $request) {

        $version = SepActividad::where('prog_codigo', $request->input('id'))->max("act_version");

        //if($version){
        $return = "";
        if ((string) $version == "0") {
            $return .= "<option value='0' selected>Versi&oacute;n 0.0</option>";
        } else {
            for ($i = 1; $i <= $version; $i++) {
                $return .= "<option value='$i' " . (($i == $version) ? 'selected' : '') . ">Versi&oacute;n $i.0</option>";
            }
        }
        /* }else{
          $return = "<option value=''>-- Seleccione versi&oacute;n --</option>";
          } */

        return $return;

    }

// getVersion

    function getVerdetalle(Request $request) {


        $id = $request->input("id");
        //$estado = $request->input("estado");

        $ficha = DB::select("SELECT sep_programa.prog_codigo, prog_nombre, fic_numero , fic_fecha_inicio, fic_fecha_fin, par_nombres, par_apellidos, fic_localizacion, fic_version_matriz, fic_proyecto "
                        . "FROM sep_ficha,  sep_programa , sep_participante "
                        . "WHERE sep_ficha.prog_codigo=sep_programa.prog_codigo "
                        . "AND sep_ficha.par_identificacion=sep_participante.par_identificacion "
                        . "AND fic_numero=$id");


        return view("Modules.Seguimiento.Ficha.fichamodal", compact("ficha"));

    }
	
	public function getFichasasignadas(){
	    
	   	$año= date('Y')-2;
		$fecha_actual=date('Y-m-d');
		$concatenar="fic.fic_fecha_fin > $año";
		$sql = '
            select  fic.fic_numero,  niv_for_nombre, prog_nombre, fic.fic_fecha_fin
            from    sep_planeacion_ficha_detalle pla_fic_det, sep_planeacion_ficha pla_fic,
                    sep_ficha fic, sep_programa pro, sep_nivel_formacion niv_for
            where   pla_fic_det.pla_fic_id = pla_fic.pla_fic_id
            and     pla_fic.fic_numero = fic.fic_numero
            and     fic.prog_codigo = pro.prog_codigo 
            and     pro.niv_for_id = niv_for.niv_for_id
            and     pla_fic.pla_fic_id not in (1,0)
            and     pla_tip_id = 6
            and     '.$concatenar.'
			and     fic.par_identificacion_productiva = "'.\Auth::user()->participante->par_identificacion.'"
			group by pla_fic.pla_fic_id
            order by fecha_fin_productiva desc';
		$instructor = DB::select($sql);
				
		$array  = $fechas = array();
		$totalResultados = 0;
		foreach ($instructor as $key => $ins) {
			$ficha = $ins->fic_numero;
			/*$sql =	"select segpro.fic_numero
					from    sep_seguimiento_productiva segpro,
							sep_matricula matri
					where  	segpro.fic_numero = $ficha  
					and 	matri.par_identificacion = segpro.par_identificacion_aprendiz
					and  	matri.est_id = 2
					";
			$aprendices = DB::select($sql);
			if (count($aprendices) > 0) {
				$sql =	"select  segpro.fic_numero, segpro.seg_pro_id
						from    sep_seguimiento_productiva segpro, sep_seguimiento_visita vis,
								sep_matricula matri
						where  	segpro.fic_numero = $ficha 
						and 	segpro.seg_pro_id = vis.seg_pro_id 
						and 	vis.seg_vis_visita = 3
						and 	matri.par_identificacion = segpro.par_identificacion_aprendiz
						and  	matri.est_id = 2
						";
				$visitas = DB::select($sql);
				$conteoVisitas = count($visitas);
			}else{
				$conteoVisitas = 0;
			}*/
			$sql =	"select  count(matri.fic_numero) as mat 
					from    sep_matricula matri
					where  	matri.fic_numero = $ficha
					and 	matri.est_id = 2 ";
			$matriculados = DB::select($sql);

			if ($matriculados[0]->mat != 0) {
				$array[$key] = $ins->fic_numero;
				$totalResultados++;
			}else{
				$array[$key] = "";
			}

			$fecha_ficha= $ins->fic_fecha_fin;
			
			//resto 1 mes al la finalizacion de la ficha 
			$fecha_ficha = date("Y-m-d",strtotime($fecha_ficha."- 1 month"));
            if($fecha_actual >= $fecha_ficha){
				if($fecha_actual<$ins->fic_fecha_fin){
					$alertas[$key]="La ficha <strong>".$ins->fic_numero." le Falta un mes</strong> para finalizar etapa productiva"; 
				}else{
					$fecha_p=$ins->fic_fecha_fin;
					$fecha_f = date("Y-m-d",strtotime($fecha_p."+ 18 month"));
					$fecha_a = date("Y-m-d",strtotime($fecha_p."+ 17 month"));               
					//echo $fecha_f."<br>";
					//echo $fecha_a;

					if($fecha_actual >= $fecha_a && $fecha_actual < $fecha_f){
					$alertas[$key]="Ficha <strong>".$ins->fic_numero." termina por tiempo en un mes</strong>"; 
					}else if($fecha_actual >= $fecha_f){
						$alertas[$key]="Ficha <strong>".$ins->fic_numero." terminada por tiempo</strong>"; 
					}else{
						$alertas[$key]="Ficha <strong>".$ins->fic_numero." terminada por fecha</strong>"; 						
					}
				}           
			}else{
				$alertas[$key]="";
			}
		}
		
		return view("Modules.Seguimiento.Ficha.fichasAsignadas",compact("instructor","totalResultados","array","alertas"));
	    
	  /*  
	  sql viejo 2
	  $fecha_actual= date('Y-m-d');
		$sql = '
            select  fic.fic_numero, niv_for_nombre, prog_nombre
            from    sep_planeacion_ficha_detalle pla_fic_det, sep_planeacion_ficha pla_fic,
                    sep_ficha fic, sep_programa pro, sep_nivel_formacion niv_for
            where   pla_fic_det.pla_fic_id = pla_fic.pla_fic_id
            and     pla_fic.fic_numero = fic.fic_numero
            and     fic.prog_codigo = pro.prog_codigo 
            and     pro.niv_for_id = niv_for.niv_for_id
            and     pla_fic.pla_fic_id not in (1,0)
            and     pla_tip_id = 6
            and     fecha_fin_productiva >= "'.$fecha_actual.'"
            and     par_id_instructor = "'.\Auth::user()->participante->par_identificacion.'"
            group by pla_fic.pla_fic_id
            order by fecha_fin_productiva desc';
		$instructor = DB::select($sql);
		$totalResultados = count($instructor);
			
		return view("Modules.Seguimiento.Ficha.fichasAsignadas",compact("instructor","totalResultados"));*/
	
	
		/* sql viejo 1
		
		$page = Input::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;
		
		$sqlTotal = ("
		SELECT count(*) as total
		FROM 
			sep_ficha AS fic, sep_participante AS par, 
			sep_programa As pro, sep_nivel_formacion AS niv_for
		WHERE 
			fic.prog_codigo = pro.prog_codigo 
			AND fic.par_identificacion = par.par_identificacion 
			AND pro.niv_for_id = niv_for.niv_for_id
			and fic.par_identificacion_productiva = '".\Auth::user()->participante->par_identificacion."' ");
		
		$sqlInstructor = "
		SELECT 
			fic.fic_numero, niv_for_nombre, pro.prog_nombre, 
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
			AND pro.niv_for_id = niv_for.niv_for_id
			and fic.par_identificacion_productiva = '".\Auth::user()->participante->par_identificacion."'
			group by fic.fic_numero";
			
		$instructor = DB::select($sqlInstructor);
		$totalResultados = DB::select($sqlTotal);
			
		return view("Modules.Seguimiento.Ficha.fichasAsignadas",compact("instructor","totalResultados"));*/
	}
	
	public function getAjaxconsulta(){
		extract($_GET);
		
		if(is_numeric($vIngresado)){
			$sqlFicha = DB::select("
			SELECT 
				fic.fic_numero, niv_for_nombre, pro.prog_nombre, 
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
				AND fic.fic_numero LIKE '$vIngresado%'
				and fic.par_identificacion_productiva ='".\Auth::user()->participante->par_identificacion."'");
		}else{
			$sqlFicha = DB::select("
			SELECT 
				fic.fic_numero, niv_for_nombre, pro.prog_nombre, 
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
				AND pro.prog_nombre LIKE '$vIngresado%'
				and fic.par_identificacion_productiva ='".\Auth::user()->participante->par_identificacion."'");
		}
		return view("Modules.Seguimiento.Reportes.inputConsulta",compact("sqlFicha"));
	}
	public function getConsulta(){
	extract($_GET);
		
		$page = Input::get('page', 1);
        $perPage = 30;
        $offset = ($page * $perPage) - $perPage;	                  

        if(isset($id) && $id!=""){
			$sql=" 
			select 
				sep_participante.par_identificacion_actual,fic_numero,
				par_nombres,par_apellidos, users.*
			from 
				sep_matricula, sep_participante, users 
			where 
				sep_matricula.par_identificacion = sep_participante.par_identificacion and
				sep_participante.par_identificacion=users.par_identificacion and 
				fic_numero = '$id' and 
				sep_matricula.est_id in(2,6,10)
				ORDER BY sep_participante.par_nombres ASC";
			
			$sql1="
			select 
				prog_nombre,par_nombres, par_apellidos,fic_numero,
				fic_fecha_inicio,fic_fecha_fin,par_telefono,par_correo,
				par_identificacion_productiva
			from 
				sep_participante,sep_ficha,sep_programa
			where   
				sep_programa.prog_codigo=sep_ficha.prog_codigo and 
				sep_ficha.par_identificacion=sep_participante.par_identificacion and
				fic_numero = '$id' 
				ORDER BY sep_participante.par_nombres ASC";
			
		
				$instructores = DB::select("select * from sep_participante where rol_id=2");
	
				$users = DB::select($sql);
			
				$array = array();
				$ficha = $users[0]->fic_numero;
				if (count($users) > 0) {
					foreach ($users as $key => $us) {
						$identificacionAprendiz = $us->par_identificacion_actual;
						$sql =	"select  MAX(vis.seg_vis_visita) as maximo, segpro.seg_pro_id,
								IF(segpro.fic_numero IS NULL, 'no','si') AS ficha
								from    sep_seguimiento_productiva segpro, sep_seguimiento_visita vis,
										sep_matricula matri
								where  	segpro.fic_numero = '$ficha'
								and 	segpro.seg_pro_id = vis.seg_pro_id 
								and 	matri.par_identificacion = segpro.par_identificacion_aprendiz
								and		segpro.par_identificacion_aprendiz = $identificacionAprendiz
								";
						$segui_identificacion = DB::select($sql);
						
						$sql = "
						select COUNT(bit.seg_bit_bitacora) as bitacora , bit.seg_pro_id
						from  sep_seguimiento_bitacora as bit , sep_seguimiento_productiva as pro
						where pro.seg_pro_id = bit.seg_pro_id
						and   pro.fic_numero = $id
						and   pro.par_identificacion_aprendiz = $identificacionAprendiz
						and   bit.seg_bit_bitacora = 12";
						$bitacora=DB::select($sql);
                       
						if($segui_identificacion[0]->maximo != 3 && $bitacora[0]->bitacora != 1){
							$array[$key] = $segui_identificacion[0]->ficha;
						}else{
							$array[$key] = "";
						}
					}
				}
			$datos = DB::select($sql1);
			$fecha_tiempo = date("Y-m-d",strtotime($datos[0]->fic_fecha_fin."+ 18 month"));
			
			$users = new LengthAwarePaginator(
					array_slice(
							$users, $offset, $perPage, true
					), count($users), $perPage, $page);
			
			$users->setPath("consulta");

			return view("Modules.Seguimiento.Ficha.consulta",compact("id","datos","offset","users", "array","fecha_tiempo"));
					
		}
	
	}
	
	public function getAsistencia(){
		$par_identificacion = \Auth::user()->participante->par_identificacion;
		$fechaActual = date('Y-m-d');
		//$trimestreActual = DB::select('select max(pla_fec_tri_fec_fin) as fechaFin from sep_planeacion_fecha_trimestre where pla_fec_tri_fec_inicio <= "'.$fechaActual.'"'); 
		//$trimestreActual = $trimestreActual[0]->fechaFin;
		$sql = '
			select 	fic.fic_numero,prog_nombre,niv_for_nombre,prog_sigla,dia.pla_dia_id,
					pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_fic_det_id
			from 	sep_planeacion_ficha_detalle p_f_d,sep_planeacion_ficha p_f, 
					sep_ficha fic, sep_programa pro,sep_nivel_formacion niv, sep_planeacion_dia dia
			where	p_f_d.pla_fic_id = p_f.pla_fic_id  and  p_f.fic_numero = fic.fic_numero 
			and 	p_f_d.pla_dia_id = dia.pla_dia_id  and	pla_fic_det_fec_inicio <= "'.$fechaActual.'" 
			and     pla_fic_det_fec_fin >= "'.$fechaActual.'" 
			and not fic.fic_numero in ("Restriccion","Complementario") 
			and		fic.prog_codigo = pro.prog_codigo  and  pro.niv_for_id = niv.niv_for_id 
			and 	par_id_instructor = "'.$par_identificacion.'" 
			and 	pla_tip_id in(1,2,7) 
			order by dia.pla_dia_id,pla_fic_det_hor_inicio';
		$horariosInstructor = DB::select($sql);
		$diasOrtografia = array(1=>'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
		
		return view('Modules.Seguimiento.Ficha.asistencia',compact('diasOrtografia','horariosInstructor'));
	}
	
	public function postAsistenciaaprendices(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);
		$detalle_id = $id;
		if(!is_numeric($detalle_id)){
			echo '
			<div class="alert alert-danger" style="margin: 10px 0px 0px 0px;">
				Se está modificando el formato de los datos recibidos.
			</div>';
			dd();
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
			// Declaración de variables
			$horaInicio = $validarFichaAsignada[0]->pla_fic_det_hor_inicio;
			$horaFin = $validarFichaAsignada[0]->pla_fic_det_hor_fin;
			$diaDB = $validarFichaAsignada[0]->pla_dia_id;
			$pla_fic_id = $validarFichaAsignada[0]->pla_fic_id;
			$fic_numero = $validarFichaAsignada[0]->fic_numero;
			
			// Declaración de variables de Session
			session()->put('fic_numero',$fic_numero);
			session()->put('pla_fic_id',$pla_fic_id);
			session()->put('pla_fic_det_id',$detalle_id);
			session()->put('horaInicio',$horaInicio);
			session()->put('horaFin',$horaFin);
			session()->put('diaDB',$diaDB);
			$diaActual = date('N');
			// Validar si el llamado de asistencia es de hoy o ayer
			if($diaDB == $diaActual or $diaDB == ($diaActual-1)){
				$horaActual = date('H');
				if($horaActual < $horaInicio and $diaDB == $diaActual){
					echo '
					<div class="alert alert-success" style="margin: 10px 0px 0px 0px;padding:5px;">
						El listado de asistencia se habilitara hoy desde las <strong>'.$horaInicio.':00</strong> hasta las <strong>'.$horaFin.':00.</strong>
					</div>';
				}else{
					$fechaActual = date('Y-m-d');
					if($horaActual >= $horaFin or $diaDB == ($diaActual-1)){
						if($diaDB == ($diaActual-1)){
							$fechaActual = date('Y-m-d',strtotime($fechaActual.' -1 days'));
						}
						echo '
						<div class="alert alert-warning" style="margin: 10px 0px 0px 0px;padding:5px;font-size: 12px;">
							Se le notificara al Coordinador el llamado de sistencia fuera del horario establecido.
						</div>';
					}
					
					$sql = '
						select	ina_det_aprendiz
						from	sep_inasistencia ina, sep_inasistencia_detalle ina_det
						where	ina_instructor = "'.$par_identificacion.'"
						and 	ina.ina_id = ina_det.ina_id
						and 	pla_fic_det_id = "'.$detalle_id.'"
						and 	ina_fecha = "'.$fechaActual.'"
						and 	ina_det_estado = "1"';
					$inasistenciasRegistradas = DB::select($sql);
					//dd();
					$aprendicesConInasistencia = array();
					foreach($inasistenciasRegistradas as $val){
						$aprendicesConInasistencia[] = $val->ina_det_aprendiz;
					}
					
					$sql = '
						select	ina_ret_aprendiz
						from	sep_inasistencia ina, sep_inasistencia_retardo ina_ret
						where	ina_instructor = "'.$par_identificacion.'"
						and 	ina.ina_id = ina_ret.ina_id
						and 	pla_fic_det_id = "'.$detalle_id.'"
						and 	ina_fecha = "'.$fechaActual.'"
						and 	ina_ret_estado = "1"';
					$retardosRegistradas = DB::select($sql);
					
					$aprendicesConRetardos = array();
					foreach($retardosRegistradas as $val){
						$aprendicesConRetardos[] = $val->ina_ret_aprendiz;
					}
					
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
					$dias = array(1=>'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
					
					$existenAprendices = 'SI';
					if(count($aprendices)==0){
						$existenAprendices = 'NO';
						return view('Modules.Seguimiento.Ficha.asistenciaAprendices',compact('existenAprendices'));
					}
					
					$sql = '
						select 	ina_id 
						from 	sep_inasistencia
						where 	ina_instructor = "'.$par_identificacion.'" and ina_fecha = "'.$fechaActual.'" 
						and 	pla_fic_det_id = '.$detalle_id;
					$validarLlamadoAsistencia = DB::select($sql);
					$contenedorLlamadoAsistencia = 'display:none;';
					$contenedorAsistencia = '';
					$checked = 'checked';
					
					if(count($validarLlamadoAsistencia)==0){
						$contenedorLlamadoAsistencia = '';
						$contenedorAsistencia = 'display:none;';
						$checked = '';
					}else{
						session()->put('ina_id', $validarLlamadoAsistencia[0]->ina_id);
					}
					
					return view('Modules.Seguimiento.Ficha.asistenciaAprendices',compact('aprendicesConRetardos','faltas','existenAprendices','checked','contenedorAsistencia','contenedorLlamadoAsistencia','aprendices','fechaActual','horaInicio','horaFin','dias','diaDB','aprendicesConInasistencia'));
				}
			}else{
				$dias = array(1=>'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
				if($diaDB < $diaActual){
					echo '
					<div class="alert alert-warning" style="margin: 10px 0px 0px 0px;padding:5px;">
						El listado de asistencia estuvo habilitado el <strong>'.$dias[$diaDB].'</strong> desde las <strong>'.$horaInicio.':00</strong> hasta las <strong>'.$horaFin.':00.</strong>
					</div>';
				}else{
					echo '
					<div class="alert alert-success" style="margin: 10px 0px 0px 0px;padding:5px;">
						El listado de asistencia se habilitara el próximo <strong>'.$dias[$diaDB].'</strong> desde las <strong>'.$horaInicio.':00</strong> hasta las <strong>'.$horaFin.':00.</strong>
					</div>';
				}
			}
		}else{
			echo '
			<div class="alert alert-danger" style="margin: 10px 0px 0px 0px;">
				El instructor no tiene asignada la ficha 
			</div>';
		}
	}
	
	public function postInasistencia(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);
		
		$detalle_id = session()->get('pla_fic_det_id');
		$horaInicio = session()->get('horaInicio');
		$horaFin = session()->get('horaFin');
		$diaDB = session()->get('diaDB');
		$diaActual = date('N');
		
		if($diaDB == $diaActual or $diaDB == ($diaActual-1)){
			$horaActual = date('H');
			if($horaActual < $horaInicio and $diaDB == $diaActual){
				echo '<strong id="mensaje" style="color:green;">
					&nbsp;&nbsp;El listado de asistencia se habilitara hoy desde las '.$horaInicio.' hasta las '.$horaFin.'.
					</strong>';
			}else{
				$par_identificacion = \Auth::user()->participante->par_identificacion;
				$fechaActual = date('Y-m-d');
				
				$sql = '
					select 	par_identificacion
					from 	sep_planeacion_ficha pla_fic, sep_planeacion_ficha_detalle pla_fic_det, sep_matricula mat
					where 	pla_fic_det.pla_fic_id = pla_fic.pla_fic_id 
					and 	pla_fic.fic_numero = mat.fic_numero 
					and 	pla_fic_det.pla_fic_det_id = '.$detalle_id.'
					and 	par_identificacion = "'.$documento.'"';
				$validarRegistroAprendiz = DB::select($sql);
				if(count($validarRegistroAprendiz)>0){
					if($diaDB == ($diaActual-1)){
						$fechaActual = date('Y-m-d',strtotime($fechaActual.' -1 days'));
						$sql = '
							update 	sep_inasistencia
							set 	ina_llamada_tarde = "1", ina_update = default
							where 	pla_fic_det_id = '.$detalle_id.'
							and		ina_fecha = "'.$fechaActual.'" 
							and 	ina_instructor = "'.$par_identificacion.'"';
						DB::update($sql);
					}
					$sql = '
						select 	ina_det_id,ina_det_estado
						from 	sep_inasistencia ina,sep_inasistencia_detalle ina_det
						where 	ina_det_aprendiz = "'.$documento.'" 
						and 	ina.ina_id = ina_det.ina_id
						and 	ina.pla_fic_det_id = '.$detalle_id.'
						and		ina_det_fecha = "'.date('Y-m-d').'"';
					$validarRegistroInasistencia = DB::select($sql);
					
					if(count($validarRegistroInasistencia)==0){
						if($valor == 'NO'){
							$ina_id = session()->get('ina_id');
							$sql = '
								insert into sep_inasistencia_detalle
								(ina_det_id,ina_id,ina_det_aprendiz,ina_det_fecha,ina_det_hora,ina_det_update,ina_det_estado)
								values
								(default,"'.$ina_id.'","'.$documento.'",
								"'.date('Y-m-d').'","'.date('H:i').'",null,"1")';
							DB::insert($sql);
						}
					}else{
						if($valor == 'SI'){
							$valor = 0;
						}else{
							$valor = 1;
						}
						if($validarRegistroInasistencia[0]->ina_det_estado != $valor){
							$ina_det_estado = 0;
							if($validarRegistroInasistencia[0]->ina_det_estado == 0){
								$ina_det_estado = 1;
							}
							$sql = '
								update 	sep_inasistencia_detalle
								set 	ina_det_estado = "'.$ina_det_estado.'", ina_det_update = default
								where	ina_det_id = '.$validarRegistroInasistencia[0]->ina_det_id;
							DB::update($sql);
						}
					}
					echo '<strong id="mensaje" style="color:green;">&nbsp;&nbsp;La acción se realizo exitosamente.</strong>';
				}else{
					echo '<strong id="mensaje" style="color:red;">&nbsp;&nbsp;El aprendiz no pertenece a la ficha seleccionada.</strong>';
				}
			}
		}else{
			$dias = array(1=>'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
			if($diaDB < $diaActual){
				echo '<strong id="mensaje" style="color:red;">&nbsp;&nbsp;El listado de asistencia estuvo habilitado el '.$dias[$diaDB].'.</strong>';
			}else{
				echo '<strong id="mensaje" style="color:red;">&nbsp;&nbsp;Listado de asistencia expiro.</strong>';
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
	
	public function postAsistenciainstructor(){
	    $pla_fic_id = session()->get('pla_fic_id');
		$fic_numero = session()->get('fic_numero');
		$detalle_id = session()->get('pla_fic_det_id');
		$horaInicio = session()->get('horaInicio');
		$horaFin = session()->get('horaFin');
		$diaDB = session()->get('diaDB');
		$diaActual = date('N');
		
		if($diaDB == $diaActual or $diaDB == ($diaActual-1)){
			$horaActual = date('H');
			//$horaActual = 19;
			if($horaActual < $horaInicio and $diaDB == $diaActual){
				echo '
				<div class="alert alert-success" style="margin: 10px 0px 0px 0px;">
					El listado de asistencia se habilitara hoy desde las <strong>'.$horaInicio.':00</strong> hasta las <strong>'.$horaFin.':00.</strong>
				</div>';
			}else{
				$par_identificacion = \Auth::user()->participante->par_identificacion;
				$fechaActual = date('Y-m-d');
				$ina_llamada_tarde = 0;
				if($diaDB == ($diaActual-1)){
					$fechaActual = date('Y-m-d',strtotime($fechaActual.' -1 days'));
					$ina_llamada_tarde = 1;
				}
				$sql = '
					select 	ina_id
					from 	sep_inasistencia
					where 	pla_fic_det_id = '.$detalle_id.'
					and		ina_fecha = "'.$fechaActual.'" 
					and 	ina_instructor = "'.$par_identificacion.'"	limit 1';
				$validarRegistroInasistencia = DB::select($sql);
				if(count($validarRegistroInasistencia)==0){
					$horaActualCompleta = date('H:i');
					$sql = '
						insert into sep_inasistencia 
						(ina_id,fic_numero, pla_fic_id, pla_fic_det_id,ina_instructor,ina_fecha,ina_hora,ina_update,ina_llamada_tarde)
						values
						(default,"'.$fic_numero.'",'.$pla_fic_id.','.$detalle_id.',"'.$par_identificacion.'","'.$fechaActual.'","'.$horaActualCompleta.'",null,"'.$ina_llamada_tarde.'")';
					DB::insert($sql);
					session()->put('ina_id',$pla_fic_id = DB::getPdo()->lastInsertId());
				}else{
					session()->put('ina_id',$validarRegistroInasistencia[0]->ina_id);
				}
			}
		}else{
			$dias = array(1=>'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
			if($diaDB < $diaActual){
				echo '
				<div class="alert alert-warning" style="margin: 10px 0px 0px 0px;">
					El listado de asistencia estuvo habilitado el <strong>'.$dias[$diaDB].'</strong> desde las <strong>'.$horaInicio.':00</strong> hasta las <strong>'.$horaFin.':00.</strong>
				</div>';
			}else{
				echo '
				<div class="alert alert-success" style="margin: 10px 0px 0px 0px;">
					El listado de asistencia se habilitara el próximo <strong>'.$dias[$diaDB].'</strong> desde las <strong>'.$horaInicio.':00</strong> hasta las <strong>'.$horaFin.':00.</strong>
				</div>';
			}
		}
	}
	// Desde aquí
	public function getHistorialinasistencia(){
		$par_identificacion = \Auth::user()->participante->par_identificacion;
		$sql = '
			select 	fic.fic_numero,prog_nombre,niv_for_nombre,prog_sigla
			from 	sep_planeacion_ficha_detalle p_f_d,sep_planeacion_ficha p_f, 
					sep_ficha fic, sep_programa pro,sep_nivel_formacion niv
			where	p_f_d.pla_fic_id = p_f.pla_fic_id and p_f.fic_numero = fic.fic_numero 
			and		fic.prog_codigo = pro.prog_codigo and pro.niv_for_id = niv.niv_for_id 
			and		pla_trimestre_numero_year >= 9
			and 	par_id_instructor = "'.$par_identificacion.'" and not fic.fic_numero in ("Restriccion","Complementario") 
			and 	pla_tip_id in(1,2,7) 
			group by p_f_d.pla_fic_id order by prog_nombre,fic.fic_numero';
		$horariosInstructor = DB::select($sql);
		
		return view('Modules.Seguimiento.Ficha.historialInasistencia',compact('horariosInstructor'));
	}
	
	public function getHistorialinasistenciadetalle(){
		$_GET = $this->seguridad($_GET);
		extract($_GET);
		$par_identificacion = \Auth::user()->participante->par_identificacion;
		
		//Validar existencia de la ficha
		$sql = 'select fic_numero from sep_ficha where fic_numero = "'.$id.'" limit 1';
		$validarFicha = DB::select($sql);
		if(count($validarFicha)>0){
			$sql = '
				select 	fic.fic_numero,prog_nombre,niv_for_nombre,prog_sigla
				from 	sep_planeacion_ficha_detalle p_f_d,sep_planeacion_ficha p_f, 
						sep_ficha fic, sep_programa pro,sep_nivel_formacion niv
				where	p_f_d.pla_fic_id = p_f.pla_fic_id and p_f.fic_numero = fic.fic_numero 
				and		fic.prog_codigo = pro.prog_codigo and pro.niv_for_id = niv.niv_for_id 
				and		pla_trimestre_numero_year >= 9 and	par_id_instructor = "'.$par_identificacion.'" 
				and 	fic.fic_numero = "'.$id.'" limit 1';
			$validarFichaInstructor = DB::select($sql);
			if(count($validarFichaInstructor)>0){
				$sql = '
					select 	par.par_identificacion,par_nombres,par_apellidos,
					substring_index(par.par_nombres," ",1) as nombreCorto,
					substring_index(par.par_apellidos," ",1) as apellidoCorto
					from 	sep_matricula mat,sep_participante par 
					where 	fic_numero = "'.$id.'" 
					and 	est_id in (2,10) and mat.par_identificacion = par.par_identificacion
					order by par_nombres,par.par_identificacion';
				$aprendices = DB::select($sql);
				if(count($aprendices)>0){
					$meses = array(1=>'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
					$fechaActual = date('Y-m-d');
					//$fechaActual = '2020-01-29';
					$fechas = array();
					for($i=3; $i>=1; $i--){
						$mes = date('m',strtotime($fechaActual)); $mes--; $mes++;
						$anio = date('Y',strtotime($fechaActual));
						$diasMes = cal_days_in_month(CAL_GREGORIAN,$mes,$anio);
						$fechas[$i]['fechaCompleta'] = $fechaActual; 
						$fechas[$i]['mes'] = $mes; 
						$fechas[$i]['mesEnLetras'] = $meses[$mes]; 
						$fechas[$i]['anio'] = $anio; 
						$fechas[$i]['diasMes'] = $diasMes; 
						$fechaActual = date('Y-m-d',strtotime($fechaActual."- 1 month"));
					}
					
					$sql = '
						select 	par_id_aprendiz,ina_fecha_creacion,par_id_instructor,par_nombres,par_apellidos
						from 	sep_matricula mat, sep_inasistencia ina, sep_participante par
						where 	mat.par_identificacion = ina.par_id_aprendiz
						and 	ina.par_id_instructor = par.par_identificacion
						and 	fic_numero = "'.$id.'" and est_id in (2,10) and ina_estado = "1"';
					$inasistencia = DB::select($sql);
					foreach($inasistencia as $val){
						$inasistenciaFicha[$val->par_id_aprendiz]['fechaInasistencia'][] = $val->ina_fecha_creacion;					
						$inasistenciaFicha[$val->par_id_aprendiz]['intructorMarco'][] = $val->par_id_instructor;					
						$inasistenciaFicha[$val->par_id_aprendiz]['intructorNombre'][] = $val->par_nombres.' '.$val->par_apellidos;					
					}
					$meses = array(
						'1'=>'01','2'=>'02','3'=>'03','4'=>'04','5'=>'05','6'=>'06','7'=>'07','8'=>'08','9'=>'09','10'=>'10','11'=>'11','12'=>'12'
					);
					$dias = array(
						'1'=>'01','2'=>'02','3'=>'03','4'=>'04','5'=>'05','6'=>'06','7'=>'07','8'=>'08','9'=>'09','10'=>'10',
						'11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19','20'=>'20',
						'21'=>'21','22'=>'22','23'=>'13','24'=>'24','25'=>'25','26'=>'26','27'=>'27','28'=>'28','29'=>'29','30'=>'30','31'=>'31'
					);
					//dd($inasistenciaFicha);
					return view('Modules.Seguimiento.Ficha.historialInasistenciaAprendices',compact('par_identificacion','meses','dias','inasistenciaFicha','fechas','aprendices'));
				}else{
					echo '
					<div class="alert alert-warning" style="margin-bottom: -12px;">
						En la ficha <strong>'.$id.'</strong> no hay aprendices en estado formación o inducción.
					</div>';
				}
			}else{
				echo '
				<div class="alert alert-warning" style="margin-bottom: -12px;">
					Usted no tiene asignada la ficha <strong>'.$id.'</strong> en sus horarios.
				</div>';
			}
		}else{
			echo '
			<div class="alert alert-warning" style="margin-bottom: -12px;">
				La ficha <strong>'.$validarFicha[0]->fic_numero.'</strong> no existe en nuestro registros.
			</div>';
		}
		
		dd($validarFicha);
		
		$fechaActual = date('Y-m-d');
		$trimestreActual = DB::select('select max(pla_fec_tri_fec_fin) as fechaFin from sep_planeacion_fecha_trimestre where pla_fec_tri_fec_inicio <= "'.$fechaActual.'"'); 
		$trimestreActual = $trimestreActual[0]->fechaFin;
		$horaActual = date('H');
		$diaActual = date('N');
		$sql = '
			select 	p_f_d.pla_fic_id,fic.fic_numero,prog_nombre,niv_for_nombre,pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_dia_id
			from 	sep_planeacion_ficha_detalle p_f_d,sep_planeacion_ficha p_f, 
					sep_ficha fic, sep_programa pro,sep_nivel_formacion niv
			where	p_f_d.pla_fic_id = p_f.pla_fic_id and p_f.fic_numero = fic.fic_numero 
			and		fic.prog_codigo = pro.prog_codigo and pro.niv_for_id = niv.niv_for_id 
			and		(pla_fic_det_hor_inicio <= "'.$horaActual.'"  and pla_fic_det_hor_fin >= "'.$horaActual.'")
			and		pla_fic_det_fec_fin = "'.$trimestreActual.'" and pla_dia_id = "'.$diaActual.'"
			and 	par_id_instructor = "'.$par_identificacion.'" and not fic.fic_numero in ("Restriccion","Complementario") 
			and 	pla_tip_id in(1,2,7)
			and 	fic.fic_numero = "'.$id.'"
			order by prog_nombre';
		$horariosInstructor = DB::select($sql);
		dd($horariosInstructor);
		if(count($horariosInstructor)>0){
			$sql = '
				select	par_id_aprendiz
				from	sep_inasistencia
				where	par_id_instructor = "'.$par_identificacion.'"
				and 	ina_fecha_creacion = "'.$fechaActual.'"
				and 	pla_fic_id = '.$horariosInstructor[0]->pla_fic_id.' and ina_estado = "1"';
			$inasistenciasRegistradas = DB::select($sql);
			
			$aprendicesConInasistencia = array();
			if(count($inasistenciasRegistradas)>0){
				foreach($inasistenciasRegistradas as $val){
					$aprendicesConInasistencia[] = $val->par_id_aprendiz;
				}
			}
			
			$sql = '
				select	par_id_aprendiz,ina_fecha_creacion
				from	sep_inasistencia
				where 	pla_fic_id = '.$horariosInstructor[0]->pla_fic_id.' and ina_estado = "1"';
			$inasistenciasRegistradas = DB::select($sql);
			$array = array();
			$faltas = array();
			foreach($inasistenciasRegistradas as $val){
				$array[$val->par_id_aprendiz]['fechas'][] = $val->ina_fecha_creacion;
			}
			
			foreach($array as $key => $val){
				foreach($val['fechas'] as $key1 => $val1){
					$fechaCopia = $val1;
					$tresFaltas = 'NO';
					$contadorFaltas = 0;
					for($i=1; $i<=3; $i++){
						if(in_array($fechaCopia, $array[$key]['fechas'])){
							$contadorFaltas++;
						}
						if(date('N',strtotime($fechaCopia)) == 7){
							$i--;
						}
						if($contadorFaltas == 3){
							$faltas[] = $key;
						}
						$fechaCopia = date('Y-m-d',strtotime($fechaCopia."+ 1 days"));
					}
					if($contadorFaltas == 3){
						break;
					}
				}
			}
			//dd($faltas);
			
			$sql = '
				select 	par.par_identificacion,par_nombres,par_apellidos,
				substring_index(par.par_nombres," ",1) as nombreCorto,
				substring_index(par.par_apellidos," ",1) as apellidoCorto
				from 	sep_matricula mat,sep_participante par 
				where 	fic_numero = "'.$horariosInstructor[0]->fic_numero.'" 
				and 	est_id in (2,10) and	mat.par_identificacion = par.par_identificacion
				order by par_nombres,par.par_identificacion';
			$aprendices = DB::select($sql);
			$horaInicio = $horariosInstructor[0]->pla_fic_det_hor_inicio;
			$horaFin = $horariosInstructor[0]->pla_fic_det_hor_fin;
			$dia = $horariosInstructor[0]->pla_dia_id;
			$dias = array(1=>'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
			
			$existenAprendices = 'SI';
			if(count($aprendices)==0){
				$existenAprendices = 'NO';
				return view('Modules.Seguimiento.Ficha.asistenciaAprendices',compact('existenAprendices'));
			}
			
			$sql = '
				select 	ina_ins_id 
				from 	sep_inasistencia_instructor 
				where 	par_id_instructor = "'.$par_identificacion.'" and ina_ins_fecha = "'.$fechaActual.'" 
				and 	pla_fic_id = '.$horariosInstructor[0]->pla_fic_id;
			$validarLlamadoAsistencia = DB::select($sql);
			$contenedorLlamadoAsistencia = 'display:none;';
			$contenedorAsistencia = '';
			$checked = 'checked';
			
			if(count($validarLlamadoAsistencia)==0){
				$contenedorLlamadoAsistencia = '';
				$contenedorAsistencia = 'display:none;';
				$checked = '';
			}
			
			return view('Modules.Seguimiento.Ficha.asistenciaAprendices',compact('faltas','existenAprendices','checked','contenedorAsistencia','contenedorLlamadoAsistencia','aprendices','fechaActual','horaInicio','horaFin','dias','dia','aprendicesConInasistencia'));
		}else{
			$sql = '
				select 	p_f_d.pla_fic_id,fic.fic_numero,prog_nombre,niv_for_nombre,pla_fic_det_hor_inicio,pla_fic_det_hor_fin
				from 	sep_planeacion_ficha_detalle p_f_d,sep_planeacion_ficha p_f, 
						sep_ficha fic, sep_programa pro,sep_nivel_formacion niv
				where	p_f_d.pla_fic_id = p_f.pla_fic_id and p_f.fic_numero = fic.fic_numero 
				and		fic.prog_codigo = pro.prog_codigo and pro.niv_for_id = niv.niv_for_id 
				and		pla_fic_det_fec_fin = "'.$trimestreActual.'" and pla_dia_id = "'.$diaActual.'"
				and 	par_id_instructor = "'.$par_identificacion.'" and not fic.fic_numero in ("Restriccion","Complementario") 
				and 	fic.fic_numero = "'.$id.'"
				order by prog_nombre';
			$aprendices = DB::select($sql);
			
			if(count($aprendices)==0){
				echo '
				<div class="alert alert-danger" style="margin-bottom: -12px;">
					Usted no tiene formación el día de hoy con la ficha <strong>'.$id.'</strong>
				</div>';
			}else{
				if($horaActual > $aprendices[0]->pla_fic_det_hor_fin){
					echo '
					<div class="alert alert-warning" style="margin-bottom: -12px;">
						El listado de asistencia estuvo habilitado desde las <strong>'.$aprendices[0]->pla_fic_det_hor_inicio.':00</strong> hasta las <strong>'.$aprendices[0]->pla_fic_det_hor_fin.':00.</strong>
					</div>';
				}else{
					echo '
					<div class="alert alert-success" style="margin-bottom: -12px;">
						El listado de asistencia se habilitara desde las <strong>'.$aprendices[0]->pla_fic_det_hor_inicio.':00</strong> hasta las <strong>'.$aprendices[0]->pla_fic_det_hor_fin.':00.</strong>
					</div>';
				}
			}
		}
	}
	// Hasta aquí
	
	// Retardo
	public function postRetardo(){
		$_POST = $this->seguridad($_POST);
		extract($_POST);
		//dd($_POST);
		$detalle_id = session()->get('pla_fic_det_id');
		$horaInicio = session()->get('horaInicio');
		$horaFin = session()->get('horaFin');
		$diaDB = session()->get('diaDB');
		$diaActual = date('N');
		
		if($diaDB == $diaActual or $diaDB == ($diaActual-1)){
			$horaActual = date('H');
			if($horaActual < $horaInicio and $diaDB == $diaActual){
				echo '<strong id="mensaje" style="color:green;">
					&nbsp;&nbsp;El listado de asistencia se habilitara hoy desde las '.$horaInicio.' hasta las '.$horaFin.'.
					</strong>';
			}else{
				//dd($_POST);
				// Validar que el instructor tenga la ficha asignada a sus horarios
				$par_identificacion = \Auth::user()->participante->par_identificacion;
				$sql = '
					select 	pla_fic_det_hor_inicio,pla_fic_det_hor_fin,pla_dia_id
					from 	sep_planeacion_ficha_detalle pla_fic_det, sep_planeacion_ficha pla_fic, sep_matricula mat
					where 	pla_fic_det.pla_fic_id = pla_fic.pla_fic_id 
					and 	pla_fic.fic_numero = mat.fic_numero
					and 	par_identificacion = '.$documento.'
					and 	par_id_instructor = "'.$par_identificacion.'" limit 1';
				$validarFichaAsignada = DB::select($sql);
				if(count($validarFichaAsignada)>0){
					$ina_id = session()->get('ina_id');
					$sql = '
						select 	ina_ret_id
						from	sep_inasistencia_retardo ina_ret, sep_inasistencia ina
						where	ina_ret.ina_id = ina.ina_id
						and 	ina_ret.ina_id = '.$ina_id.' 
						and 	ina_ret_aprendiz = "'.$documento.'"
						and 	ina_ret_fecha = "'.date('Y-m-d').'"
						and 	ina_instructor = "'.$par_identificacion.'" limit 1';
					$validarRegistro = DB::select($sql);
					if(count($validarRegistro)>0){
						$ina_ret_estado = '0';
						if($valor == 'true'){
							$ina_ret_estado = '1';
						}
						$sql = '
							update 	sep_inasistencia_retardo
							set		ina_ret_estado = "'.$ina_ret_estado.'", ina_ret_update = default
							where 	ina_ret_id = '.$validarRegistro[0]->ina_ret_id;
						DB::update($sql);
					}else{
						$sql = '
							insert into sep_inasistencia_retardo
							(ina_ret_id,ina_id,ina_ret_aprendiz,ina_ret_fecha,ina_ret_hora,ina_ret_update,ina_ret_estado)
							values
							(default,'.$ina_id.',"'.$documento.'","'.date('Y-m-d').'","'.date('H:i').'",null,"1")';
						DB::insert($sql);
					}
					echo '<strong id="mensaje" style="color:green;">&nbsp;&nbsp;La acción se realizo exitosamente.</strong>';
				}else{
					echo '<strong id="mensaje" style="color:red;">&nbsp;&nbsp;El aprendiz no pertenece a la ficha seleccionada.</strong>';
				}
			}
		}else{
			$dias = array(1=>'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
			if($diaDB < $diaActual){
				echo '<strong id="mensaje" style="color:red;">&nbsp;&nbsp;El listado de asistencia estuvo habilitado el '.$dias[$diaDB].'.</strong>';
			}else{
				echo '<strong id="mensaje" style="color:red;">&nbsp;&nbsp;Listado de asistencia expiro.</strong>';
			}
		}
	}
	// Retardo hasta aquí

	// Ficha caracterización

	public function getIndexinstructorform()
	{
		$sql = "SELECT prog_codigo, prog_nombre FROM sep_programa WHERE prog_codigo NOT IN('1','0','') ORDER BY prog_nombre";
        $programas = DB::select($sql);

		$sql = "SELECT id, descripcion FROM sep_ingreso_ambiente ORDER BY descripcion";
        $ambientes = DB::select($sql);
		
		// Preguntar por tabla de estado de usuario
		$sql = "SELECT par_identificacion, par_nombres, par_apellidos FROM sep_participante p WHERE rol_id = 2 ORDER BY par_apellidos";
        $participante = DB::select($sql);

        return view('Modules.Seguimiento.Ficha.indexInstructorForm', compact('programas', 'ambientes', 'participante'));
	}

	public function postCargar()
	{
		extract($_POST);
        $sql = "INSERT INTO sep_ficha_caracterizacion(fic_car_nombre,fic_car_est_id) VALUES('$fic_car_nombre',1)";
        DB::insert($sql);
	}

	public function getListarcaracterizaciones()
	{
		$sql = "SELECT fc.fic_car_id, fc.fic_car_nombre, e.fic_car_est_descripcion, fc.fic_car_est_id FROM sep_ficha_caracterizacion fc, sep_ficha_caracterizacion_estado e WHERE fc.fic_car_est_id = e.fic_car_est_id";
        $data = DB::select($sql);

		$rol = \Auth::user()->participante->rol_id;
		// $par_identificacion = \Auth::user()->participante->par_identificacion;

        return view('Modules.Seguimiento.Ficha.listarCaracterizaciones', compact('data', 'rol'));
	}

	public function postAccion()
	{
		extract($_POST);
		if ($acc == 'r') {
			$sql = "UPDATE sep_ficha_caracterizacion SET fic_car_est_id = 2, fic_car_observacion = '$fic_car_observacion' WHERE fic_car_id = '$fic_car_id'";
		} elseif ($acc == 'a1') {
			$sql = "UPDATE sep_ficha_caracterizacion SET fic_car_est_id = 3 WHERE fic_car_id = '$fic_car_id'";
		} elseif ($acc == 'a2') {
			$sql = "UPDATE sep_ficha_caracterizacion SET fic_car_est_id = 4 WHERE fic_car_id = '$fic_car_id'";
		} else {
			$sql = "UPDATE sep_ficha_caracterizacion SET fic_car_est_id = 5 WHERE fic_car_id = '$fic_car_id'";
		}
        DB::update($sql);
	}

}













