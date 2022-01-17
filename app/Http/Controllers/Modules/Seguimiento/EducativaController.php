<?php

namespace App\Http\Controllers\Modules\Seguimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use \Illuminate\Support\Facades\Auth;
use DB;
use \Illuminate\Pagination\LengthAwarePaginator;
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class EducativaController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('control_roles');
    }
    
     /* Post */
    function postModalrechazo(){
        $id = $_POST['id'];
        $sql = 'select fal_rec_descripcion from sep_edu_falta_rechazo where fal_rec_id = '.$id;
        $motivoRechazo = DB::select('select fal_rec_descripcion from sep_edu_falta_rechazo where edu_falta_id = '.$id);
        //dd($motivoRechazo);
        if(isset($motivoRechazo[0])){
            $texto = $motivoRechazo[0]->fal_rec_descripcion;
        }else{
            $texto = 'No se registro un motivo de rechazo.';
        }
        return view('Modules.Seguimiento.Educativa.modalRechazo', compact('texto'));
    }
    /* */
    
    /* Queja pendiente*/ 
    function getQuejaspendientes(){
        extract($_GET);
        $resgistroPorPagina = 15;
		$limit = $resgistroPorPagina;
		if(isset($pagina)){
			$hubicacionPagina = $resgistroPorPagina*($pagina-1);
			$limit = $hubicacionPagina.','.$resgistroPorPagina;
        }else{
            $pagina = 1;
        }
       
        $sql = '
            select  edu_falta_id, edu_falta_descripcion, edu_falta_evidencia, edu_falta_fecha,
                    edu_falta_calificacion, falta.edu_tipo_falta_id, edu_tipo_falta_descripcion,
                    concat(ins.par_nombres," ",ins.par_apellidos) as nombreInstructor, 
                    concat(coo.par_nombres," ",coo.par_apellidos) as nombreCoordinador
            from    sep_edu_falta falta, sep_participante ins, sep_participante coo, sep_edu_tipo_falta tipo
            where   falta.edu_est_id = 1 and falta.edu_tipo_falta_id = tipo.edu_tipo_falta_id and not falta.par_identificacion_coordinador = "66849167"
            and     falta.par_identificacion = ins.par_identificacion and falta.par_identificacion_coordinador = coo.par_identificacion
            order 	by edu_falta_fecha asc limit '.$limit;
        $sqlContador = 'select count(edu_falta_id) as total from sep_edu_falta falta where not falta.par_identificacion_coordinador = "66849167" and edu_est_id = 1';
			
		$faltasInstructor = DB::select($sql);
		$faltasInstructorContador = DB::select($sqlContador);
		$contadorFaltasInstructor = $faltasInstructorContador[0]->total;
        $cantidadPaginas = ceil($contadorFaltasInstructor/$resgistroPorPagina);
        $contador = (($pagina-1)*$resgistroPorPagina)+1;

        return view('Modules.Seguimiento.Educativa.faltasPendientes',compact('contadorFaltasInstructor','faltasInstructor','cantidadPaginas','contador','pagina'));
    }
    /* */

    function getQueja() {

        // $instructores = SepParticipante::all()->where('rol_id',2)->lists('par_nombres','par_identificacion');


        $tiposA = DB::select("SELECT * FROM sep_edu_tipo_falta");

        $tipos = array();
        foreach ($tiposA as $tipo) {
            $tipos[$tipo->edu_tipo_falta_id] = $tipo->edu_tipo_falta_descripcion;
        }

       // $coordinador = DB::select("SELECT * FROM sep_participante WHERE par_identificacion IN (SELECT id_usuario FROM sep_detalle_usuario_rol WHERE id_rol=3)");
        $coordinador = DB::select("SELECT * FROM sep_participante WHERE rol_id = 3 and not par_identificacion = '16759526'");
        
        $tiposB = array();
        foreach ($coordinador as $tipo) {
            $tiposB[$tipo->par_identificacion] = $tipo->par_nombres . " " . $tipo->par_apellidos;
        }

        $capitulos = DB::select("SELECT * FROM sep_edu_capitulo");

        return view("Modules.Seguimiento.Educativa.queja", compact("tipos", "tiposB", "capitulos"));
    }

    public function postQueja(Request $request) {

        $reglas = Array(
            'tipo' => 'required',
            "coordinador" => "required",
            "calificacion" => "required",
            "hechos" => "required",
            "evidencias" => "required",
            "literales" => "required",
            "aprendices" => "required"
        );
        $cont1=0;
        $cont2=0;
        // Mensajes de error para los diferentes campos
        $messages = [
            'tipo.required' => 'El campo Tipo de la Falta es obligatorio',
            "coordinador.required" => "El campo Coordinador Acad&eacute;mico es obligatorio",
            "calificacion.required" => "El campo Calificaci&oacute;n de la falta es obligatorio",
            "hechos.required" => "El campo Descripci&oacute;n detallada de los hechos es obligatorio",
            "evidencias.required" => "El campo Describa y folie las evidencias es obligatorio",
            "literales.required" => "Debe seleccionar por lo menos un(1) literal del reglamento para la falta",
            "aprendices.required" => "Debe seleccionar por lo menos un(1) aprendiz para la falta"
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

        $hechos = $request->input('hechos');
        $evidencias = $request->input('evidencias');
        $aprendices = $request->input('aprendices');
        $literales = $request->input('literales');
        $tipo = $request->input('tipo');
        $coordinador = $request->input('coordinador');
        $calificacion = $request->input('calificacion');

        $sql = "INSERT INTO sep_edu_falta "
                . " (edu_falta_descripcion,edu_falta_evidencia,
                           par_identificacion,par_identificacion_coordinador,edu_falta_fecha,edu_tipo_falta_id,edu_est_id,edu_falta_calificacion) VALUES("
                . "'$hechos',"
                . "'$evidencias',"
                . "'" . Auth::user()->par_identificacion . "',"
                . "'$coordinador',"
                . "'" . date('Y/m/d') . "',"
                . "$tipo,"
                . "1,"
                . "'$calificacion'"
                . ")";
        $insert = DB::insert($sql);
        $idFalta = DB::getPdo()->lastInsertId();

        if ($insert) {
           $x=0;
            foreach ($aprendices as $aprendiz) {
                $sql = "INSERT INTO sep_edu_falta_apr "
                        . " (par_identificacion,edu_falta_id) VALUES("
                        . "'$aprendiz',"
                        . "'$idFalta'"
                        . ")";
                $insert = DB::insert($sql);
                //Funcionario(a) encargada de generar el comite
                $sql = "
                select max(mat.fic_numero) as ficha
                from sep_matricula mat
                where mat.par_identificacion = $aprendiz";
                $ficha_actual = DB::select($sql);
                if ($ficha_actual[0]->ficha != "") {
                    $sql= "
                        select prog.genera_comite , prog.prog_codigo , fic.fic_modalidad
                        from sep_programa as prog
                        left join sep_ficha fic on fic.prog_codigo = prog.prog_codigo
                        where fic.fic_numero = ".$ficha_actual[0]->ficha."";
                    $funcionarias=DB::select($sql);
                    //si el programa es multimedia se valida la modalidad para el encargado del comite
                    if ($funcionarias[0]->prog_codigo == "228101") {
                        if ($funcionarias[0]->fic_modalidad == 2) {
                            $cont1++;
                        }else{
                            $cont2++;
                        }
                    }else{
                        if ($funcionarias[0]->genera_comite == "38466728") {
                            $cont1++;
                        }else{
                            $cont2++;
                        }
                        if ($x == 0) {
                            $funcionaria = $funcionarias[0]->genera_comite;
                        }
                    }
                }
                $x++;
            }
            foreach ($literales as $literal) {
                $sql = "INSERT INTO sep_edu_falta_lit "
                        . " (edu_falta_id,lit_id) VALUES("
                        . "$idFalta,"
                        . "$literal"
                        . ")";
                $insert = DB::insert($sql);
            }
        }
        
        //Asiganar funcionario(a) que genera el comite
        if ($cont1 > $cont2) {
            $funcionaria = "38466728";
        }else if($cont2 > $cont1){
            $funcionaria = "1113522696";
        }else if($cont1 == 0 && $cont2 == 0){
            $funcionaria = "1";
        }else if($cont1 == $cont2){
            $funcionaria = $funcionaria;
        }
        DB::update("update sep_edu_falta set par_genera_comite = $funcionaria where edu_falta_id = $idFalta");
        ///

        $this->generarQueja($literales, $hechos, $evidencias, $aprendices, $idFalta, $calificacion, $tipo, $coordinador);

        $sql = "SELECT par_nombres, par_apellidos FROM sep_participante WHERE par_identificacion=" . Auth::user()->par_identificacion;
        $instructor = DB::select($sql);

        $sql = "SELECT par_correo,par_nombres,par_apellidos FROM sep_participante WHERE par_identificacion=$coordinador";
        $coordinador = DB::select($sql);

        // $mensaje = "Señor(a) " . $coordinador[0]->par_nombres . " " . $coordinador[0]->par_apellidos
        //         . "<br><br>Se le informa que el/la Instructor(a) <b>" . $instructor[0]->par_nombres . " " . $instructor[0]->par_apellidos . "</b> ha radicado un <b>INFORME DE FALTA O QUEJA</b> "
        //         . "para su revisión, y posterior aprobación o rechazo. <br><br>"
        //         . ""
        //         . "Recuerde ingresar por medio del siguiente enlace: <a href='" . (url('seguimiento/educativa/gestionarqueja/' . $idFalta)) . "' target='_blank'> Revisar Formato </a> <br><br>"
        //         . ""
        //         . "Muchas gracias.<br><br>"
        //         . "Equipo de Desarrollo SETALPRO<br>"
        //         . "Servicio Nacional de Aprendizaje Sena";
        // $destinatarios = array($coordinador[0]->par_correo);
        $adjunto = public_path() . "/Modules/Seguimiento/Educativa/Queja/" . Auth::user()->par_identificacion . "-$idFalta.docx";
        // $adjuntoTipo = "Formato de falta.docx";
        // $this->enviarMail($mensaje, $destinatarios, $adjunto, $adjuntoTipo);

        return \Redirect::to(url("seguimiento/educativa/listarqueja"));
    }

    public function enviarMail($mensaje, $destinatarios, $adjunto, $adjuntoTipo) {
        $asunto = "Nuevo formato de informe o queja";

        $mail = new \PHPMailer();
        $mensaje=utf8_decode($mensaje);
        
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host = 'exodo.colombiahosting.com.co';
        $mail->Port = 587;

        $mail->Username = 'seguimiento@cdtiapps.com';
        $mail->Password = 'seguimientoEducativa07';

        $mail->From = Auth::user()->email;
        $mail->FromName = utf8_decode($asunto);

//        $mail->AltBody = "gmail.com";

        $mail->Subject = utf8_decode($asunto);
        $mail->isHTML(true);

        foreach ($destinatarios as $destinatario) {
            //$mail->AddAddress($aprendiz->par_correo, $aprendiz->par_nombres . " " . $aprendiz->par_apellidos);
            //$mail->AddAddress("dferbac@gmail.com", $aprendiz->par_nombres." ".$aprendiz->par_apellidos);
            $mail->AddAddress($destinatario);
        }
        $mail->AddAddress("seguimientoscdtisena@gmail.com");




        $mail->AddAttachment($adjunto, $adjuntoTipo);

        $mail->Body = ($mensaje);

        if ($mail->Send()) {
            //echo "Enviado ... ";
        }
    }

    public function getAprendices(Request $request) {

        $cedula = trim($request->input('cedula'));
        $cedula1 = trim($request->input('cedula'));

        $sql = "SELECT "
                . "p.par_identificacion_actual, par_nombres, par_apellidos, fic_numero"
                . " FROM "
                . "sep_matricula m,sep_participante p "
                . "WHERE "
                . "(p.par_identificacion_actual = '$cedula' OR fic_numero = '$cedula')"
                . " AND rol_id=1 AND (est_id=2 OR est_id>=6) AND p.par_identificacion=m.par_identificacion";

        $aprendicesA = DB::select($sql);

        foreach ($aprendicesA as $aprendiz) {
            $aprendices[$aprendiz->par_identificacion_actual]['nombre'] = $aprendiz->par_nombres . " " . $aprendiz->par_apellidos;
            $aprendices[$aprendiz->par_identificacion_actual]['ficha'] = $aprendiz->fic_numero;
        }
        
        //Actualizar el estado de los beneficios sena vencidos de los aprendices
		actualizarBenedicioSena();
		
        //beneficiarios
        $sql="select * from  sep_beneficios_sena_aprendiz group by par_identificacion";
        $beneficiarios_existentes = DB::select($sql); 
        $fecha_actual=date("Y-m-d");
        foreach ($beneficiarios_existentes as $bene) {
            $list="";
            $sql="
                select bene.ben_sen_nombre
                from   sep_beneficios_sena_aprendiz as apr , sep_beneficios_sena as bene
                where  bene.id = apr.beneficio_sena_id 
                and par_identificacion = '".$bene->par_identificacion."' and apr.estado = 1 and apr.fecha_fin >= '".$fecha_actual."'";
            $beneficios_aprendiz=DB::select($sql);
            foreach ($beneficios_aprendiz as $bene_apr) {
                $list=$list."<li>".$bene_apr->ben_sen_nombre."</li>";
            }     
            $beneficiario[$bene->par_identificacion]=$list; 
        }
        return view("Modules.Seguimiento.Educativa.aprendices", compact("aprendices", "cedula", "ficha","beneficiario"));
    }

    public function getImplicados(Request $request) {
        $cedula = trim($request->input('cedula'));
        $sql = "SELECT par_identificacion, par_nombres, par_apellidos, nombre_rol "
                . "FROM sep_participante, sep_roles "
                . "WHERE sep_participante.rol_id = sep_roles.id_rol "
                . "AND (par_identificacion LIKE '$cedula%' OR par_nombres LIKE '%$cedula%' "
                . "OR par_apellidos LIKE '%$cedula%') "
                . "AND (rol_id=2 OR rol_id=3 OR rol_id=4 OR rol_id=7)";
        $aprendicesA = DB::select($sql);

        foreach ($aprendicesA as $aprendiz) {
            $aprendices[$aprendiz->par_identificacion]['nombre'] = $aprendiz->par_nombres . " " . $aprendiz->par_apellidos;
            $aprendices[$aprendiz->par_identificacion]['rol'] = $aprendiz->nombre_rol;
        }
        
        
        return view("Modules.Seguimiento.Educativa.implicados", compact("aprendices", "cedula", "ficha"));
    }

    public function getLiterales(Request $request) {


        $capitulo = $request->input('id');

        //Filtro por ficha y por cedula

        $sql = "SELECT l.lit_id,l.lit_codigo,l.lit_descripcion,l.art_codigo, a.art_descripcion,a.cap_codigo "
                . "FROM sep_edu_literal as l, "
                . "sep_edu_articulo as a, "
                . "sep_edu_capitulo as c "
                . "WHERE (c.cap_codigo=a.cap_codigo and a.art_codigo=l.art_codigo) and c.cap_codigo='$capitulo'";
        //dd($sql);

        $datos = DB::select($sql);
        foreach ($datos as $dato) {
            $datosp[$dato->art_codigo][] = $dato;
        }
        //dd($datosp);
        //dd($datos);
        /*
          foreach ($aprendicesA as $aprendiz) {

          $aprendices[$aprendiz->par_identificacion]['nombre'] = $aprendiz->par_nombres . " " . $aprendiz->par_apellidos;
          $aprendices[$aprendiz->par_identificacion]['ficha'] = $aprendiz->fic_numero;
          } */
        return view("Modules.Seguimiento.Educativa.literales", compact("datosp"));
    }

    public function getValidahora(Request $request) {


        $fechaHora = $request->input('fechaHora');

        $fecha = substr($fechaHora, 0, 10);
        $hora = substr($fechaHora, 11);

        //6 AM 28800
        //10 PM 86400
        //30 mins 1800

        $horanueva = strtotime($hora);

        $horanueva = substr($horanueva, 5);

        $horaBD = DB::select("SELECT edu_comite_hora FROM sep_edu_comite "
                        . "WHERE edu_comite_fecha='$fecha' ORDER BY edu_comite_hora desc");

        if ($horaBD) {
            echo "<table>";
            echo "<tr>";
            echo "<th colspan='2'>Comit&eacute;s programados para hoy";
            echo "</th>";
            echo "</tr>";
            $control = 0;
            foreach ($horaBD as $horComite) {
                $horaBDTransformada = strtotime($horComite->edu_comite_hora);
                $horaBDTransformada = substr($horaBDTransformada, 5);
                echo "<tr>";
                echo "<td><code>Hora";
                echo "</code></td>";
                echo "<td><code>$horComite->edu_comite_hora";
                echo "</code></td>";
                echo "</tr>";

                if ($horanueva < ($horaBDTransformada + 840) && $horanueva >= $horaBDTransformada || ($horanueva >= ($horaBDTransformada - 840) && $horanueva < ($horaBDTransformada))) {
                    $control++;
                }
            }
            if ($control == 0) {
                echo "<tr class='horarioSelect' style='color:green;'><td colspan='2'>Se puede programar este comit&eacute;</td></tr>";
            } else {
                echo "<tr><td colspan='2' style='color:red;'>NO se puede programar este comit&eacute;</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<code class='horarioSelect'>No hay comit&eacute;s programados para hoy</code>";
        }
    }

    public function generarQueja($literales, $hechos, $evidencias, $aprendices, $idFalta, $calificacion, $tipoFalta, $coordinador) {

        \PhpOffice\PhpWord\Autoloader::register();

        $templateWord = new TemplateProcessor(getPathUploads() . '/plantillas/queja2.docx');

        $ciudad = date("Y-m-d");
        $nombre = Auth::user()->participante->par_nombres . " " . Auth::user()->participante->par_apellidos;
        $cedula = Auth::user()->par_identificacion;
        $correo = Auth::user()->participante->par_correo;
        $direccion = Auth::user()->participante->par_direccion;
        $telefono = Auth::user()->participante->par_telefono;

        $tipoF = DB::select("SELECT edu_tipo_falta_descripcion "
                        . "FROM sep_edu_tipo_falta WHERE "
                        . "edu_tipo_falta_id=$tipoFalta");

        $coordinadorF = DB::select("SELECT par_nombres, par_apellidos "
                        . "FROM sep_participante WHERE "
                        . "par_identificacion=$coordinador");


        $datos_aprendices = "";
        foreach ($aprendices as $aprendiz) {

            //$aprendizr = SepParticipante::find($aprendiz);
            $aprendizr = DB::select("SELECT * FROM sep_participante, sep_matricula WHERE sep_participante.par_identificacion=sep_matricula.par_identificacion and sep_matricula.par_identificacion = $aprendiz ");

            $datos_aprendices.= "Nombre: " . $aprendizr[0]->par_nombres . " " . $aprendizr[0]->par_apellidos . "\r\n";
            $datos_aprendices.= "Numero de Documento: $aprendiz \r\n";
            $datos_aprendices.= "Ficha del programa:  " . $aprendizr[0]->fic_numero . "\r\n";
            $datos_aprendices.= "\r\n\r\n";
        }

        $datos_aprendices = str_replace("\n", "<w:br/>", $datos_aprendices);

        $datos_literales = "";
        foreach ($literales as $literal) {

            //$aprendizr = SepParticipante::find($aprendiz);
            $literalr = DB::select("SELECT lit_id,lit_codigo,lit_descripcion,l.art_codigo,a.cap_codigo  "
                            . "FROM sep_edu_literal l, sep_edu_articulo a "
                            . "WHERE l.art_codigo=a.art_codigo AND lit_id=$literal");

            $datos_literales.= $literalr[0]->cap_codigo . "\r\n";
            $datos_literales.= $literalr[0]->art_codigo . "\r\n";
            $datos_literales.= "Literal  \r\n";
            $datos_literales.= $literalr[0]->lit_codigo . ". " . $literalr[0]->lit_descripcion;
            $datos_literales.= "\r\n\r\n";
        }
        $datos_literales = str_replace("\n", "<w:br/>", $datos_literales);
        // --- Asignamos valores a la plantilla
        $templateWord->setValue('ciudad_fecha', $ciudad);
        $templateWord->setValue('nombre_instructor', $nombre);
        $templateWord->setValue('cedula_instructor', $cedula);
        $templateWord->setValue('email_instructor', $correo);
        $templateWord->setValue('direccion_instructor', $direccion);
        $templateWord->setValue('telefono_instructor', $telefono);
        $templateWord->setValue('hechos', $hechos);
        $templateWord->setValue('evidencias', $evidencias);
        $templateWord->setValue('datos_aprendices', $datos_aprendices);

        $templateWord->setValue('literales', $datos_literales);
        $templateWord->setValue('tipo_falta', $tipoF[0]->edu_tipo_falta_descripcion);
        $templateWord->setValue('calificacion', $calificacion);
        $templateWord->setValue('nombre_coordinador', $coordinadorF[0]->par_nombres . " " . $coordinadorF[0]->par_apellidos);
        $templateWord->setValue('documento_coordinador', $coordinador);


        $ruta = public_path()
                . '/Modules/Seguimiento/Educativa/Queja/'
                . $cedula . '-' . $idFalta . '.docx';

        // --- Guardamos el documento
        $templateWord->saveAs($ruta);
    }
    
    function getListarqueja() {
		extract($_GET);
        $resgistroPorPagina = 15;
		$limit = $resgistroPorPagina;
		if(isset($pagina)){
			$hubicacionPagina = $resgistroPorPagina*($pagina-1);
			$limit = $hubicacionPagina.','.$resgistroPorPagina;
        }else{
            $pagina = 1;
        }
		
		$sql = '
			select 	edu_falta_id, f.par_identificacion, f.edu_falta_fecha, t.edu_tipo_falta_descripcion, e.edu_est_descripcion 
            from 	sep_edu_falta f, sep_participante p, sep_edu_tipo_falta t, sep_edu_estado e 
            where 	f.par_identificacion = p.par_identificacion and f.edu_tipo_falta_id = t.edu_tipo_falta_id 
            and 	f.edu_est_id = e.edu_est_id and f.par_identificacion = "'.Auth::user()->par_identificacion.'"
			order 	by edu_falta_fecha desc limit '.$limit;
		$sqlContador = '
			select 	count(edu_falta_id) as total from sep_edu_falta
            where 	par_identificacion = "'.Auth::user()->par_identificacion.'"
			order 	by edu_falta_fecha desc';
			
		$faltasInstructor = DB::select($sql);
		$faltasInstructorContador = DB::select($sqlContador);
		$contadorFaltasInstructor = $faltasInstructorContador[0]->total;
		$cantidadPaginas = ceil($contadorFaltasInstructor/$resgistroPorPagina);
        $estado = array('PENDIENTE' => 'primary', 'APROBADO' => 'success','RECHAZADO' => 'danger','PROGRAMADO' => 'info', 'FINALIZADO' => 'warning');
        $contador = (($pagina-1)*$resgistroPorPagina)+1;

        return view('Modules.Seguimiento.Educativa.listarqueja', compact('pagina','contador','cantidadPaginas', 'faltasInstructor', 'contadorFaltasInstructor', 'estado'));
    }
    
    function getGestionarqueja($queja = false) {
        extract($_GET);

        // Paginado
        $resgistroPorPagina = 15;
		$limit = $resgistroPorPagina;
		if(isset($pagina)){
			$hubicacionPagina = $resgistroPorPagina*($pagina-1);
			$limit = $hubicacionPagina.','.$resgistroPorPagina;
        }else{
            $pagina = 1;
        }

        // Validar busqueda del instructor
        $concatenarInstructorPrimeraSQL = '';
        $concatenarInstructorSegundaSQL = '';
        if(isset($_GET['par_identificacion'])){
            $concatenarInstructorPrimeraSQL = ' and falta.par_identificacion = "'.$par_identificacion.'" ';
            $concatenarInstructorSegundaSQL = ' and par_identificacion = "'.$par_identificacion.'" ';
        }else{
            $par_identificacion = '';
        }

        // Consulta de todos los instructores
        $sql ='select par_identificacion, par_nombres, par_apellidos from sep_participante where rol_id = 2 order by par_nombres asc';
        $instructores = DB::select($sql);
        
        // Consultar la faltas del coordinador que esta logiado
        $sql = '
            select  edu_falta_id, edu_falta_descripcion, edu_falta_evidencia, edu_falta_fecha, falta.par_identificacion,
                    edu_falta_calificacion, falta.edu_tipo_falta_id, edu_tipo_falta_descripcion, edu_est_descripcion,
                    concat(ins.par_nombres," ",ins.par_apellidos) as nombreInstructor, par_identificacion_coordinador
            from    sep_edu_falta falta, sep_participante ins, sep_participante coo, sep_edu_tipo_falta tipo, sep_edu_estado estado
            where   falta.edu_tipo_falta_id = tipo.edu_tipo_falta_id and par_identificacion_coordinador = "'.Auth::user()->par_identificacion.'"
            and     falta.par_identificacion = ins.par_identificacion and falta.par_identificacion_coordinador = coo.par_identificacion
            and     falta.edu_est_id = estado.edu_est_id '.$concatenarInstructorPrimeraSQL.'
            order 	by estado.edu_est_id asc, edu_falta_fecha desc, falta.par_identificacion  limit '.$limit;
            
        // Contar las faltas que tiene el coordinador
        $sqlContador = '
            select  count(edu_falta_id) as total 
            from    sep_edu_falta falta, sep_edu_estado estado 
            where   falta.edu_est_id = estado.edu_est_id '.$concatenarInstructorSegundaSQL.'
            and     par_identificacion_coordinador = "'.Auth::user()->par_identificacion.'"';
        
        // Calculos para el páginado
		$faltasInstructor = DB::select($sql);
		$faltasInstructorContador = DB::select($sqlContador);
		$contadorFaltasInstructor = $faltasInstructorContador[0]->total;
        $cantidadPaginas = ceil($contadorFaltasInstructor/$resgistroPorPagina);
        $contador = (($pagina-1)*$resgistroPorPagina)+1;

        // Array con los posibles estado de cada falta
        $estado = array('PENDIENTE' => 'primary', 'APROBADO' => 'success','RECHAZADO' => 'danger','PROGRAMADO' => 'info', 'FINALIZADO' => 'warning');
        
        //Coordinadores
        $sql="select * from sep_participante where rol_id = 3";
        $coordinadores=DB::select($sql);
        
        return view('Modules.Seguimiento.Educativa.gestionarqueja',compact('coordinadores','par_identificacion','instructores','estado','contadorFaltasInstructor','faltasInstructor','cantidadPaginas','contador','pagina'));
    }
    
    /*function getGestionarqueja($queja = false) {
        extract($_GET);
        $resgistroPorPagina = 15;
		$limit = $resgistroPorPagina;
		if(isset($pagina)){
			$hubicacionPagina = $resgistroPorPagina*($pagina-1);
			$limit = $hubicacionPagina.','.$resgistroPorPagina;
        }else{
            $pagina = 1;
        }

        $sql = '
            select  edu_falta_id, edu_falta_descripcion, edu_falta_evidencia, edu_falta_fecha, falta.par_identificacion,
                    edu_falta_calificacion, falta.edu_tipo_falta_id, edu_tipo_falta_descripcion, edu_est_descripcion,
                    concat(ins.par_nombres," ",ins.par_apellidos) as nombreInstructor
            from    sep_edu_falta falta, sep_participante ins, sep_participante coo, sep_edu_tipo_falta tipo, sep_edu_estado estado
            where   falta.edu_tipo_falta_id = tipo.edu_tipo_falta_id and par_identificacion_coordinador = "'.Auth::user()->par_identificacion.'"
            and     falta.par_identificacion = ins.par_identificacion and falta.par_identificacion_coordinador = coo.par_identificacion
            and     falta.edu_est_id = estado.edu_est_id
            order 	by estado.edu_est_id asc, edu_falta_fecha desc, falta.par_identificacion  limit '.$limit;
        $sqlContador = '
            select  count(edu_falta_id) as total 
            from    sep_edu_falta falta, sep_edu_estado estado 
            where   falta.edu_est_id = estado.edu_est_id 
            and     par_identificacion_coordinador = "'.Auth::user()->par_identificacion.'"';
	    
		$faltasInstructor = DB::select($sql);
		$faltasInstructorContador = DB::select($sqlContador);
		$contadorFaltasInstructor = $faltasInstructorContador[0]->total;
        $cantidadPaginas = ceil($contadorFaltasInstructor/$resgistroPorPagina);
        $contador = (($pagina-1)*$resgistroPorPagina)+1;
        $estado = array('PENDIENTE' => 'primary', 'APROBADO' => 'success','RECHAZADO' => 'danger','PROGRAMADO' => 'info', 'FINALIZADO' => 'warning');
        
        return view('Modules.Seguimiento.Educativa.gestionarqueja',compact('estado','contadorFaltasInstructor','faltasInstructor','cantidadPaginas','contador','pagina'));
    }*/

    //2019-10-25
    /*function getGestionarqueja($queja = false) {

        $page = Input::get('page', 1);
        $perPage = 4000;
        $offset = ($page * $perPage) - $perPage;

        $WHERE = "AND (f.edu_est_id=1 or f.edu_est_id=2 or f.edu_est_id=3 or f.edu_est_id=4) AND par_identificacion_coordinador=" . Auth::user()->par_identificacion;

        if ($queja) {
            $WHERE .= " AND f.edu_falta_id = " . $queja;
        }

        $sql = "SELECT f.edu_falta_id,"
                . "p.par_identificacion,"
                . "p.par_nombres,"
                . "p.par_apellidos, "
                . "f.edu_falta_fecha,"
                . "tf.edu_tipo_falta_descripcion,"
                . "e.edu_est_descripcion "
                . "FROM sep_edu_falta f,"
                . "sep_participante p,"
                . "sep_edu_tipo_falta tf,"
                . "sep_edu_estado e"
                . " WHERE ((f.edu_est_id=e.edu_est_id "
                . "AND f.edu_tipo_falta_id=tf.edu_tipo_falta_id) "
                . "AND f.par_identificacion=p.par_identificacion) $WHERE order by f.edu_est_id,edu_falta_fecha desc";
        //dd($sql);
        $tipos = DB::select($sql);
        //$tipos = DB::select("SELECT edu_falta_id,par_identificacion,par_nombres,par_apellidos, edu_falta_fecha,edu_tipo_falta_descripcion,edu_est_descripcion FROM sep_edu_falta NATURAL JOIN sep_participante NATURAL JOIN sep_edu_tipo_falta NATURAL JOIN sep_edu_estado ");

        $tipos = new LengthAwarePaginator(
                array_slice(
                        $tipos, $offset, $perPage, true
                ), count($tipos), $perPage, $page);

        $tipos->setPath("gestionarqueja");

        $estado = array('PENDIENTE' => 'primary',
            'APROBADO' => 'success',
            'RECHAZADO' => 'danger',
            'PROGRAMADO' => 'info', 'FINALIZADO' => 'warning');

        return view("Modules.Seguimiento.Educativa.gestionarqueja", compact("tipos", "offset", "estado"));
    }*/

    function getProgramarcomite() {
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

        // Validar busqueda del instructor
        $concatenarInstructor = '';
        if(isset($_GET['par_identificacion'])){
            if($_GET['par_identificacion'] != ''){
                $concatenarInstructor = ' and instructor.par_identificacion = "'.$par_identificacion.'" ';
            }
        }else{
            $par_identificacion = '';
        }

        $concatenarCoordinador = '';
        if(isset($_GET['par_identificacion_coordinador'])){
            if($_GET['par_identificacion_coordinador'] != '' && $_GET['par_identificacion_coordinador'] != 0){
                $concatenarCoordinador = ' and falta.par_identificacion_coordinador = "'.$par_identificacion_coordinador.'" ';
            }
        }else{
            $par_identificacion_coordinador = '';
        }

        $concatenarEstado = '';
        if(isset($_GET['edu_est_id'])){
            if($_GET['edu_est_id'] != ''){
                $concatenarEstado = ' and falta.edu_est_id = "'.$edu_est_id.'" ';
            }
        }else{
            $edu_est_id = '';
        }

        $funcionario = \Auth::user()->participante->par_identificacion;
        if($funcionario != "1111111111" && isset($_GET['par_identificacion_coordinador']) && $_GET['par_identificacion_coordinador'] == 0){
            $documento = " and falta.par_genera_comite = $funcionario ";
            $mis_faltas ="selected";
        }else{
            $documento = "";
            $mis_faltas ="";
        }

        $estados = array('PENDIENTE' => 'primary','APROBADO' => 'success', 'RECHAZADO' => 'danger',
            'PROGRAMADO' => 'info', 'FINALIZADO' => 'warning');
        
        $sql = '
            select  falta.edu_falta_id,instructor.par_identificacion, concat(substring_index(par_nombres," ",1), " ", substring_index(par_apellidos," ",1)) as nombreInstructor,
                    edu_falta_fecha, edu_falta_fecha_aprobado, edu_tipo_falta_descripcion, edu_est_descripcion
            from    sep_edu_falta falta, sep_participante instructor, sep_edu_tipo_falta tipoFalta, sep_edu_estado estado 
            where   falta.par_identificacion = instructor.par_identificacion 
            and     falta.edu_tipo_falta_id = tipoFalta.edu_tipo_falta_id 
            and     falta.edu_est_id = estado.edu_est_id '.$documento.' '.$concatenarInstructor.' '.$concatenarCoordinador.' '.$concatenarEstado.'
            order   by edu_falta_fecha desc, par_nombres asc limit '.$limit;
        
        $sqlContador = '
            select  count(falta.edu_falta_id) as total
            from    sep_edu_falta falta, sep_participante instructor, sep_edu_tipo_falta tipoFalta, sep_edu_estado estado 
            where   falta.par_identificacion = instructor.par_identificacion 
            and     falta.edu_tipo_falta_id = tipoFalta.edu_tipo_falta_id '.$documento.' '.$concatenarInstructor.' '.$concatenarCoordinador.' '.$concatenarEstado.'
            and     falta.edu_est_id = estado.edu_est_id';
        $sqlInstructores = '
            select  par_identificacion, par_nombres, par_apellidos
            from    sep_participante
            where   rol_id = 2 and not par_identificacion in("0","1111111111") order by par_nombres asc';
        $instructores = DB::select($sqlInstructores);
        $dbEstados = DB::select('select edu_est_id, edu_est_descripcion from sep_edu_estado');
        $coordinadores = DB::select('select par_identificacion, par_nombres, par_apellidos from sep_participante where rol_id = 3');
        //dd();
        // Paginado
		$faltas = DB::select($sql);
		$faltasContador = DB::select($sqlContador);
		$contadorFaltas = $faltasContador[0]->total;
        $cantidadPaginas = ceil($contadorFaltas/$resgistroPorPagina);
        $contador = (($pagina-1)*$resgistroPorPagina)+1;

        return view('Modules.Seguimiento.Educativa.programarcomite', compact('mis_faltas','edu_est_id','par_identificacion_coordinador','coordinadores','dbEstados','instructores','estados','contadorFaltas', 'par_identificacion','faltasContador','faltas', 'contador', 'cantidadPaginas','contador','pagina'));
        

        /*$apellidoInstructor = Input::get('apellidoInstructor', "");
        $estadoF = Input::get('filtro', "");
        $coordinador = Input::get('coordinador', "");
		$WHERE = "";
        // $page = Input::get('page', 1);
        // $perPage = 10;
        // $offset = ($page * $perPage) - $perPage;

        if($coordinador != ""){
			$WHERE = "AND (f.par_identificacion_coordinador = $coordinador) ";
		}
		
        if ($estadoF == "APROBADO") {
            $WHERE .= "AND (f.edu_est_id=2)";
        } elseif ($estadoF == "PROGRAMADO") {
            $WHERE .= "AND (f.edu_est_id=4)";
        } elseif ($estadoF == "FINALIZADO") {
            $WHERE .= "AND (f.edu_est_id=5)";
        } else {
            $WHERE .= "AND (f.edu_est_id=2 OR f.edu_est_id=4 OR f.edu_est_id=5)";
        }
        
        if($apellidoInstructor!=""){
            $WHERE .= " AND p.par_apellidos LIKE '".$apellidoInstructor."%'";
        }
            
        $sql = "SELECT f.edu_falta_id,"
                . "p.par_identificacion,"
                . "p.par_nombres,"
                . "p.par_apellidos, "
                . "f.edu_falta_fecha,"
                . "f.edu_falta_fecha_aprobado," //--
                . "tf.edu_tipo_falta_descripcion,"
                . "e.edu_est_descripcion "
                . "FROM sep_edu_falta f,"
                . "sep_participante p,"
                . "sep_edu_tipo_falta tf,"
                . "sep_edu_estado e"
                . " WHERE ((f.edu_est_id=e.edu_est_id "
                . "AND f.edu_tipo_falta_id=tf.edu_tipo_falta_id) "
                . "AND f.par_identificacion=p.par_identificacion) $WHERE "
                . "and edu_falta_fecha > '2018/01/01' ORDER BY f.edu_falta_fecha desc";

        $tipos = DB::select($sql);

        // $tipos = new LengthAwarePaginator(
                // array_slice(
                        // $tipos, $offset, $perPage, true
                // ), count($tipos), $perPage, $page);
        
        // $tipos->setPath("programarcomite");
        // $tipos->appends(['filtro' => $estadoF]);
        $estado = array('PENDIENTE' => 'primary',
            'APROBADO' => 'success',
            'RECHAZADO' => 'danger',
            'PROGRAMADO' => 'info', 'FINALIZADO' => 'warning');

        foreach ($tipos as $key => $tipo) {
            if ($tipo->edu_est_descripcion != "PROGRAMADO") {
                continue;
            }
            $sqlComite = "SELECT sep_edu_comite.edu_tipo_com_id,edu_comite_hora,edu_comite_fecha,edu_tipo_com_descripcion "
                    . "FROM sep_edu_comite, sep_edu_tipo_com "
                    . "WHERE sep_edu_comite.edu_tipo_com_id=sep_edu_tipo_com.edu_tipo_com_id and edu_falta_id=" . $tipo->edu_falta_id . " ORDER BY edu_comite_id DESC";
            $comi = DB::select($sqlComite);
            //$tipos[$key]->comite = $comi[0];
        }

        $coordinadores = "select * from sep_participante where rol_id = 3";
		$coordinadores = DB::select($coordinadores);
		
        return view("Modules.Seguimiento.Educativa.programarcomite", compact("coordinador","coordinadores","tipos", "estadoF", "estado","apellidoInstructor"));*/
     }

    function getVerdetalle(Request $request) {
        $id = $request->input("id");
        $estado = $request->input("estado");

        $queja = DB::select("SELECT edu_falta_descripcion, edu_falta_evidencia, edu_tipo_falta_descripcion, par_identificacion FROM "
                        . "sep_edu_falta, sep_edu_tipo_falta "
                        . "WHERE sep_edu_falta.edu_tipo_falta_id=sep_edu_tipo_falta.edu_tipo_falta_id and "
                        . "edu_falta_id=$id");
        $sql = "
            select tabla.*, pro.prog_nombre  from
            (SELECT par.*, max(mat.fic_numero) as fic_numero
            FROM    sep_edu_falta_apr falta, sep_participante par, sep_matricula mat, sep_ficha fic, sep_programa pro
            WHERE   edu_falta_id=$id
            and 	falta.par_identificacion = par.par_identificacion
            and 	par.par_identificacion = mat.par_identificacion
            and 	mat.fic_numero = fic.fic_numero
            and 	fic.prog_codigo = pro.prog_codigo
            group by par.par_identificacion) tabla, sep_ficha fic, sep_programa pro
            where tabla.fic_numero = fic.fic_numero and fic.prog_codigo = pro.prog_codigo";
        $aprendicesQueja = DB::select($sql);
        $instructor = DB::select("SELECT * FROM sep_participante WHERE par_identificacion = " . $queja[0]->par_identificacion);

        $sql = '
            select  edu_cap.cap_codigo, cap_descripcion, edu_lit.art_codigo, lit_descripcion, art_descripcion
            from    sep_edu_falta edu_fal, sep_edu_falta_lit edu_fal_lit,
                    sep_edu_literal edu_lit, sep_edu_articulo edu_art, sep_edu_capitulo edu_cap
            where   edu_fal.edu_falta_id = edu_fal_lit.edu_falta_id
            and     edu_fal_lit.lit_id = edu_lit.lit_id
            and     edu_lit.art_codigo = edu_art.art_codigo
            and     edu_art.cap_codigo = edu_cap.cap_codigo
            and     edu_fal.edu_falta_id = '.$id.'
            order by edu_lit.art_codigo';
        $literales = DB::select($sql);
        
        //Actualizar el estado de los beneficios sena vencidos de los aprendices
		actualizarBenedicioSena();
        
        //verificar si existen aprendices con beneficios sena
        $beneficiario=array();
        $fecha_actual=date("Y-m-d");
        foreach ($aprendicesQueja as $bene) {
            $list="";
            $sql="
                select bene.ben_sen_nombre,apr.fecha_inicio,apr.fecha_fin
                from   sep_beneficios_sena_aprendiz as apr , sep_beneficios_sena as bene
                where  bene.id = apr.beneficio_sena_id 
                and par_identificacion = '".$bene->par_identificacion."' and apr.estado = 1 and apr.fecha_fin >= '".$fecha_actual."'";
            $beneficios_aprendiz=DB::select($sql);
            if (count($beneficios_aprendiz) > 0) {
                foreach ($beneficios_aprendiz as $bene_apr) {
                    $list.="<b>".$bene_apr->ben_sen_nombre."</b>".
                    "<li style='list-style:none'><b>Inicio:</b> ".$bene_apr->fecha_inicio."</li>".
                    "<li style='list-style:none'><b>Fin:</b> ".$bene_apr->fecha_fin."</li><br>";
                    $beneficiario[$bene->par_identificacion]=$list;
                }
            } 
        }
        $rol = \Auth::user()->participante->rol_id;
        $url = "descargarword?id=".$id;

        if ($estado == "PROGRAMADO") {
            $sqlComite = "SELECT sep_edu_comite.edu_tipo_com_id,edu_comite_hora,edu_comite_fecha,edu_tipo_com_descripcion "
                    . "FROM sep_edu_comite, sep_edu_tipo_com "
                    . "WHERE sep_edu_comite.edu_tipo_com_id=sep_edu_tipo_com.edu_tipo_com_id and edu_falta_id=$id ORDER BY edu_comite_id DESC";
            $comi = DB::select($sqlComite);
            return view("Modules.Seguimiento.Educativa.quejamodal", compact('rol','url','literales',"queja", "aprendicesQueja", "comi", "instructor", "estado", "beneficiario"));
        } else {
            return view("Modules.Seguimiento.Educativa.quejamodal", compact('url','literales',"queja", "aprendicesQueja", "instructor", "estado","beneficiario"));
        }
    }

    function getAprobarqueja($id) {

        $datos = DB::select("SELECT * FROM sep_edu_falta WHERE edu_falta_id = ? ", array($id));
        $datos = $datos[0];

        return view("Modules.Seguimiento.Educativa.aprobar", compact("datos"));
    }

    function postAprobarqueja() {

        //Campos por post
        $cod_queja = $_POST['cod_queja'];

        $sql = "SELECT par_identificacion,edu_falta_descripcion,edu_falta_fecha FROM sep_edu_falta WHERE edu_falta_id=$cod_queja";
        $instruc = DB::select($sql);

        $sql = "SELECT par_correo,par_nombres,par_apellidos FROM sep_participante WHERE par_identificacion=" . $instruc[0]->par_identificacion;
        $instructor = DB::select($sql);


        $mensaje = "Señor(a) " . $instructor[0]->par_nombres . " " . $instructor[0]->par_apellidos
                . "<br><br>Se le informa que la falta o queja con descripción "
                . "<i>" . ((strlen($instruc[0]->edu_falta_descripcion) > 40) ? substr($instruc[0]->edu_falta_descripcion, 0, 40) . "..." : $instruc[0]->edu_falta_descripcion) . "</i> "
                . "<br><br>Se encuentra en estado: <b>APROBADO</b> <br>"
                . "Fecha de elaboración del Informe: "
                . "<b>" . $instruc[0]->edu_falta_fecha . ".</b> <br><br>"
                . "Próximamente se le informará la fecha y hora de programación del comité de evaluación y seguimiento."
                . "<br><br>Muchas gracias.<br><br>"
                . "Equipo de Desarrollo SETALPRO<br>"
                . "Servicio Nacional de Aprendizaje Sena";

        $destinatarios = array($instructor[0]->par_correo);
        $adjunto = "";
        $adjuntoTipo = "";

        $this->enviarMail($mensaje, $destinatarios, $adjunto, $adjuntoTipo);

        $sql = "SELECT par_nombres,par_apellidos,par_identificacion,par_correo FROM sep_participante WHERE rol_id=4";
        $administrativos = DB::select($sql);

        $mensaje = "Señor Usuario<br><br>"
                . "Se le informa que se ha aprobado un informe de falta o queja"
                . "para su respectiva programación a comité"
                . "<br><br>Muchas gracias.<br><br>"
                . "Equipo de Desarrollo SETALPRO<br>"
                . "Servicio Nacional de Aprendizaje Sena";
        $destinatarios = array();
        foreach ($administrativos as $admin) {
            $destinatarios[] = $admin->par_correo;
        }


        $this->enviarMail($mensaje, $destinatarios, $adjunto, $adjuntoTipo);


        //$tipos = DB::update("UPDATE sep_edu_falta SET edu_est_id=2 WHERE edu_falta_id= ?", array($cod_queja));
        $fechaAprobacion = date('Y/m/d');
        $tipos = DB::update("UPDATE sep_edu_falta SET edu_est_id=2, edu_falta_fecha_aprobado='$fechaAprobacion' WHERE edu_falta_id = $cod_queja");

        return redirect(url("seguimiento/educativa/gestionarqueja"));
    }

    function getRechazarqueja($id) {

        $datos = DB::select("SELECT * FROM sep_edu_falta WHERE edu_falta_id = ? ", array($id));
        $datos = $datos[0];

        return view("Modules.Seguimiento.Educativa.rechazar", compact("datos"));
    }

    function postRechazarqueja() {

        //Campos por post
        $cod_queja = $_POST['cod_queja'];
        $fal_rec_descripcion = $_POST['fal_rec_descripcion'];

        $sql = "SELECT par_identificacion,edu_falta_descripcion,edu_falta_fecha FROM sep_edu_falta WHERE edu_falta_id=$cod_queja";
        $instruc = DB::select($sql);

        $sql = "SELECT par_correo,par_nombres,par_apellidos FROM sep_participante WHERE par_identificacion=" . $instruc[0]->par_identificacion;
        $instructor = DB::select($sql);


        $mensaje = "Señor(a) " . $instructor[0]->par_nombres . " " . $instructor[0]->par_apellidos
                . "<br><br>Se le informa que la falta o queja con descripción "
                . "<i>" . ((strlen($instruc[0]->edu_falta_descripcion) > 40) ? substr($instruc[0]->edu_falta_descripcion, 0, 40) . "..." : $instruc[0]->edu_falta_descripcion) . "</i> "
                . "<br><br>Se encuentra en estado: <b>RECHAZADO</b> <br>"
                . "Fecha de elaboración del Informe: "
                . "<b>" . $instruc[0]->edu_falta_fecha . ".</b> <br><br>"
                . "Para mayor información comunicarse con la Coordinación Académica."
                . "<br><br>Muchas gracias.<br><br>"
                . "Equipo de Desarrollo SETALPRO<br>"
                . "Servicio Nacional de Aprendizaje Sena";


        $destinatarios = array($instructor[0]->par_correo);
        $adjunto = "";
        $adjuntoTipo = "";

        $this->enviarMail($mensaje, $destinatarios, $adjunto, $adjuntoTipo);


        $tipos = DB::update("UPDATE sep_edu_falta SET edu_est_id=3 WHERE edu_falta_id= ?", array($cod_queja));
        DB::insert('insert into sep_edu_falta_rechazo values(default,'.$cod_queja.',"'.$fal_rec_descripcion.'")');

        return redirect(url("seguimiento/educativa/gestionarqueja"));
    }

    function getComite($id) {

        // $instructores = SepParticipante::all()->where('rol_id',2)->lists('par_nombres','par_identificacion');


        $tiposA = DB::select("SELECT * FROM sep_edu_tipo_com");

        $tipos = array();
        foreach ($tiposA as $tipo) {
            $tipos[$tipo->edu_tipo_com_id] = $tipo->edu_tipo_com_descripcion;
        }


        return view("Modules.Seguimiento.Educativa.comite", compact("tipos", "id"));
    }

    function getActa($id) {

        $tiposA = DB::select("SELECT * FROM sep_edu_tipo_com");

        $tipos = array();
        foreach ($tiposA as $tipo) {
            $tipos[$tipo->edu_tipo_com_id] = $tipo->edu_tipo_com_descripcion;
        }

        $aprendices = DB::select("SELECT pa.*,m.fic_numero,prog_nombre "
                        . "FROM sep_edu_falta_apr fa, "
                        . "sep_participante pa, "
                        . "sep_matricula m, "
                        . "sep_ficha f,  "
                        . "sep_programa p "
                        . "WHERE edu_falta_id=$id "
                        . "AND fa.par_identificacion = pa.par_identificacion AND m.fic_numero=f.fic_numero AND f.prog_codigo=p.prog_codigo AND m.par_identificacion=pa.par_identificacion");

        $novedad = DB::select("SELECT * FROM sep_edu_novedad WHERE edu_tipo_novedad_tipo=1");

        return view("Modules.Seguimiento.Educativa.acta", compact("tipos", "id", "aprendices", "novedad"));
    }

    public function postComite(Request $request) {

        $reglas = Array(
            'tipo' => 'required',
            "direccion" => "required",
            "fecha" => "required",
            "aprendices" => "required"
        );

        // Mensajes de error para los diferentes campos
        $messages = [
            'tipo.required' => 'El campo Tipo de comit&eacute; es obligatorio',
            'direccion.required' => 'El campo Direcci&oacute;n es obligatorio',
            "fecha.required" => "El campo Hora y Fecha del Comit&eacute es obligatorio",
            "aprendices.required" => "Debe seleccionar almenos un(1) implicado para citar al comit&eacute;"
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



        $fecha = $request->input('fecha');
        $fecha = explode(" ", $fecha);
        $tipo = $request->input('tipo');
        $direccion = $request->input('direccion');
        $implicados = $request->input('aprendices');
        $idFalta = $request->input('idFalta');

        $sql = "INSERT INTO sep_edu_comite "
                . " (edu_falta_id,
                    edu_est_id,
                    edu_tipo_com_id,
                    edu_comite_hora,
                    edu_comite_direccion,
                    edu_comite_fecha) VALUES("
                . "'$idFalta',"
                . "'4',"
                . "'$tipo',"
                . "'$fecha[1] $fecha[2]',"
                . "'$direccion',"
                . "'$fecha[0]')";

        $insert = DB::insert($sql);

        $idComite = DB::getPdo()->lastInsertId();

        if ($insert) {
            foreach ($implicados as $implicado) {
                $sql = "INSERT INTO sep_edu_com_par "
                        . " (edu_comite_id,par_identificacion) VALUES("
                        . "'$idComite',"
                        . "'$implicado'"
                        . ")";

                $insert = DB::insert($sql);
            } // foreach

            DB::update("UPDATE sep_edu_falta SET edu_est_id=4 WHERE edu_falta_id=$idFalta");
        } // if 
        //$this->generarQueja($hechos, $evidencias, $aprendices);

        $falta = DB::select("SELECT * FROM sep_edu_falta WHERE edu_falta_id = $idFalta");

        /* $aprendices = DB::select("SELECT * FROM sep_edu_falta_apr, sep_participante WHERE edu_falta_id=$idFalta AND "
          . "sep_edu_falta_apr.par_identificacion = sep_participante.par_identificacion");
         */
        $aprendices = DB::select("SELECT pa.*,m.fic_numero,prog_nombre FROM sep_edu_falta_apr fa, sep_participante pa, sep_matricula m, sep_ficha f,  sep_programa p WHERE edu_falta_id=$idFalta AND fa.par_identificacion = pa.par_identificacion AND m.fic_numero=f.fic_numero AND f.prog_codigo=p.prog_codigo AND m.par_identificacion=pa.par_identificacion");
        $instructor = DB::select("SELECT * FROM sep_participante WHERE par_identificacion = " . $falta[0]->par_identificacion);

        // Validacion tipo de comite para el asunto del correo
        if ($tipo == 1) {
            $tipoComite = "ordinario";
        } elseif ($tipo == 2) {
            $tipoComite = "extraordinario";
        }

        /*
         * Envio de correos a implicados 
         */
        $asunto = "Citación a comité de evaluación y seguimiento $tipoComite de aprendices";

        $fechaComite=explode("/", $fecha[0]);
        
        $fechaFinal=$fechaComite[1]."/".$fechaComite[0]."/".$fechaComite[2];
    
        $mensaje = "Buen día;<br /><br /> 
            Se le solicita presentarse el día $fechaFinal  en  la 
            Coordinación Académica del  Centro de Diseño Tecnológico Industrial 
            CDTI – SENA $direccion de Cali, de acuerdo a la comunicación  
            presentado  por el Instructor " . $instructor[0]->par_nombres . " " . $instructor[0]->par_apellidos . "<br /><br />
            
            Tema: " . $falta[0]->edu_falta_descripcion . "<br /><br />
            
            Hora: $fecha[1] $fecha[2]<br /><br />"
                . "Aprendices:<br /><br />";

        foreach ($aprendices as $aprendiz) {
            $mensaje .= "* " . (strtoupper($aprendiz->par_nombres . " " . $aprendiz->par_apellidos)) . "<br />";
            $mensaje.="Programa de Formación: $aprendiz->prog_nombre<br />

            Ficha: $aprendiz->fic_numero<br /><br />

           ";
        }

        /*
         * Correo envio
         */

        
        //Descomentar cuando se hagan los comites
        if($request->input('enviarCorreo') == 1){
            $mail = new \PHPMailer();
    
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Host = 'exodo.colombiahosting.com.co';
            $mail->Port = 587;
    
            $mail->Username = 'seguimiento@cdtiapps.com';
            $mail->Password = 'seguimientoEducativa07';
    
            $mail->From = Auth::user()->email;
            $mail->FromName = utf8_decode($asunto);
    
            //$mail->AltBody = "gmail.com";
    
            $mail->Subject = utf8_decode($asunto);
            $mail->isHTML(true);
    
            foreach ($aprendices as $aprendiz) {
                $mail->AddAddress($aprendiz->par_correo, $aprendiz->par_nombres . " " . $aprendiz->par_apellidos);
                //$mail->AddAddress("dferbac@gmail.com", $aprendiz->par_nombres." ".$aprendiz->par_apellidos);
            }
    
            foreach ($implicados as $implicado) {
                $implicado = DB::select("SELECT * FROM sep_participante WHERE par_identificacion = $implicado");
                $mail->AddAddress($implicado[0]->par_correo, $implicado[0]->par_nombres . " " . $implicado[0]->par_apellidos);
                //$mail->AddAddress("dferbac@gmail.com", $implicado[0]->par_nombres." ".$implicado[0]->par_apellidos);
            }
    
            $sql = "SELECT par_correo FROM sep_participante WHERE rol_id=4";
            $administrativos = DB::select($sql);
            
            foreach ($administrativos as $admin) {
                $mail->AddAddress($admin->par_correo);
            }
            
    		$mail->AddAddress("seguimientoscdtisena@gmail.com");
    		
            $mail->AddAttachment(public_path() . "/Modules/Seguimiento/Educativa/Queja/" . $falta[0]->par_identificacion . "-$idFalta.docx", "Formato de falta.docx");
    
            $mail->Body = ($mensaje);
    
            if ($mail->Send()) {
                //echo "Enviado ... ";
            }
        }

        return redirect(url("seguimiento/educativa/programarcomite"));
    }
    
    public function postActa(Request $request) {

        $reglas = Array(
            'edu_acta_quorum' => 'required',
            "edu_acta_descargos" => "required",
            "edu_acta_practicas" => "required",
            "edu_acta_existencia" => "required",
            "edu_acta_constituye" => "required",
            "edu_acta_autor" => "required",
            "edu_acta_grado_res" => "required",
            "edu_acta_grado_falta" => "required",
            "edu_acta_sancion" => "required"
        );

        // Mensajes de error para los diferentes campos
        $messages = [
            'edu_acta_quorum.required' => 'El campo Verificaci&oacute;n del Qu&oacute;rum es obligatorio',
            'edu_acta_descargos.required' => 'El campo Presentaci&oacute;n de Descargos es obligatorio',
            "edu_acta_practicas.required" => "El campo Pr&aacute;cticas de pruebas necesarias es obligatorio",
            "edu_acta_existencia.required" => "El campo existencia de la conducta es obligatorio",
            "edu_acta_constituye.required" => "El campo Constituye o no una falta es obligatorio",
            "edu_acta_autor.required" => "El campo Probable Autor(es) es obligatorio",
            "edu_acta_grado_res.required" => "El campo Grado de responsabilidad es obligatorio",
            "edu_acta_grado_falta.required" => "El campo Grado de calificaci&oacute;n es obligatorio",
            "edu_acta_sancion.required" => "El campo Amerita o no una sanci&oacute;n es obligatorio"
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



        $edu_acta_quorum = $request->input('edu_acta_quorum');
        $edu_acta_descargos = $request->input('edu_acta_descargos');
        $edu_acta_practicas = $request->input('edu_acta_practicas');
        $edu_acta_existencia = $request->input('edu_acta_existencia');
        $edu_acta_constituye = $request->input('edu_acta_constituye');
        $edu_acta_autor = $request->input('edu_acta_autor');
        $edu_acta_grado_res = $request->input('edu_acta_grado_res');
        $edu_acta_grado_falta = $request->input('edu_acta_grado_falta');
        $edu_acta_sancion = $request->input('edu_acta_sancion');
        $idFalta = $request->input('idFalta');
        $horaInicio = $request->input('horaInicio');
        $novedades = $request->input('novedades');

        $comite = DB::select("SELECT MAX(edu_comite_id) as edu_comite_id FROM sep_edu_comite WHERE edu_falta_id = $idFalta");

        $sql = "INSERT INTO sep_edu_acta "
                . " (edu_comite_id,
                    edu_acta_quorum,
                    edu_acta_normas,
                    edu_acta_descargos,
                    edu_acta_practicas,
                    edu_acta_existencia,
                    edu_acta_constituye,
                    edu_acta_autor,
                    edu_acta_grado_resp,
                    edu_acta_grado_falta,
                    edu_acta_sancion) VALUES("
                . "'" . $comite[0]->edu_comite_id . "',"
                . "'$edu_acta_quorum',"
                . "'',"
                . "'$edu_acta_descargos',"
                . "'$edu_acta_practicas',"
                . "'$edu_acta_existencia',"
                . "'$edu_acta_constituye',"
                . "'$edu_acta_autor',"
                . "'$edu_acta_grado_res',"
                . "'$edu_acta_grado_falta',"
                . "'$edu_acta_sancion')";


        $insert = DB::insert($sql);

        $idActa = DB::getPdo()->lastInsertId();

        if ($insert) {
            foreach ($novedades as $parti => $nov) {
                foreach ($nov as $novedad => $valor) {

                    if ($novedad != "observacion") {
                        $sql = "INSERT INTO sep_edu_acta_novedad "
                                . " (edu_acta_id,edu_novedad_id,par_identificacion) "
                                . "VALUES("
                                . "'$idActa',"
                                . "'$novedad',"
                                . "'$parti'"
                                . ")";
                        $insert = DB::insert($sql);
                    } else {
                        if (trim($valor) != "") {
                            $sql = "INSERT INTO sep_edu_acta_novedad_observacion "
                                    . " (edu_acta_id,par_identificacion,acta_novedad_observacion) "
                                    . "VALUES("
                                    . "'$idActa',"
                                    . "'$parti',"
                                    . "'$valor'"
                                    . ")";
                            $insert = DB::insert($sql);
                        }
                    }
                }
            } // foreach

            DB::update("UPDATE sep_edu_falta SET edu_est_id=4 WHERE edu_falta_id=$idFalta");
        } // if 
        //$this->generarQueja($hechos, $evidencias, $aprendices);


        $this->generarActa($novedades, $horaInicio, $idActa, $edu_acta_quorum, $idFalta, $edu_acta_descargos, $edu_acta_practicas, $edu_acta_existencia, $edu_acta_constituye, $edu_acta_autor, $edu_acta_grado_res, $edu_acta_grado_falta, $edu_acta_sancion);

        DB::update("UPDATE sep_edu_falta SET edu_est_id=5 WHERE edu_falta_id= ?", array($idFalta));
        ////$aprendices=DB::select("SELECT pa.*,m.fic_numero,prog_nombre FROM sep_edu_falta_apr fa, sep_participante pa, sep_matricula m, sep_ficha f,  sep_programa p WHERE edu_falta_id=$idFalta AND fa.par_identificacion = pa.par_identificacion AND m.fic_numero=f.fic_numero AND f.prog_codigo=p.prog_codigo AND m.par_identificacion=pa.par_identificacion");
        //$instructor = DB::select("SELECT * FROM sep_participante WHERE par_identificacion = " . $falta[0]->par_identificacion);
        // Validacion tipo de comite para el asunto del correo
        return redirect(url("seguimiento/educativa/programarcomite?filtro=FINALIZADO"));
    }

    public function generarActa($novedades, $hora_ini, $id_acta, $edu_acta_quorum, $idFalta, $edu_acta_descargos, $edu_acta_practicas, $edu_acta_existencia, $edu_acta_constituye, $edu_acta_autor, $edu_acta_grado_res, $edu_acta_grado_falta, $edu_acta_sancion) {

        //Se genera la carpeta con el acta
        mkdir(public_path() . "/Modules/Seguimiento/Educativa/Acta/$id_acta", 0777);

        \PhpOffice\PhpWord\Autoloader::register();

        $templateWord = new TemplateProcessor(getPathUploads() . '/plantillas/actacomiteyanin.docx');

        $falta = DB::select("SELECT * FROM sep_edu_falta WHERE edu_falta_id = $idFalta");
        $faltaapr = DB::select("SELECT par_nombres,par_apellidos, sep_participante.par_identificacion "
                        . "FROM sep_edu_falta_apr, sep_participante "
                        . "WHERE sep_edu_falta_apr.par_identificacion=sep_participante.par_identificacion "
                        . "AND edu_falta_id=$idFalta");
        $faltalit = DB::select("SELECT * FROM sep_edu_falta_lit WHERE edu_falta_id = $idFalta");

        //$id_acta="";
        $ciudad_fecha = "Santiago de Cali, " . date("d-m-Y");
        //$hora_ini="";
        $hora_fin = date('h:i A');
        $lugar = "Coordinación Académica CDTI";
        $regional = "Valle";
        $centro = "CDTI";
        $quorum = $edu_acta_quorum;
        $datos_queja = "";
        $aprendices = "";


        foreach ($faltaapr as $fapr) {
            $sql = 'select 	sep_matricula.fic_numero,prog_nombre,niv_for_nombre
					from 	sep_matricula,sep_ficha,sep_programa,sep_nivel_formacion
                    where 	sep_matricula.fic_numero = sep_ficha.fic_numero 
                    and 	sep_ficha.prog_codigo = sep_programa.prog_codigo and sep_programa.niv_for_id = sep_nivel_formacion.niv_for_id
                    and 	sep_matricula.par_identificacion = '.$fapr->par_identificacion;
            $programa = DB::select($sql);

            $datos_queja.="\n\n".$fapr->par_nombres." ".$fapr->par_apellidos.", Identificado(a) con C.C. ".$fapr->par_identificacion." del programa de formación ".$programa[0]->niv_for_nombre.' en '.$programa[0]->prog_nombre.", con número de ficha " . $programa[0]->fic_numero . ".";

            $aprendices.="\n" . $fapr->par_nombres . " " . $fapr->par_apellidos;
            
            /*$sql = "SELECT sep_matricula.fic_numero, prog_nombre FROM sep_matricula,sep_ficha,sep_programa 
                    WHERE sep_matricula.fic_numero=sep_ficha.fic_numero 
                    AND sep_ficha.prog_codigo=sep_programa.prog_codigo 
                    AND sep_matricula.par_identificacion=" . $fapr->par_identificacion;
            $programa = DB::select($sql);

            $datos_queja.="\n\n" . $fapr->par_nombres . " " . $fapr->par_apellidos . ", Identificado(a) con C.C.   " . $fapr->par_identificacion . "
De programa de formación " . $programa[0]->prog_nombre . ", con número de ficha " . $programa[0]->fic_numero . ".";

            $aprendices.="\n" . $fapr->par_nombres . " " . $fapr->par_apellidos;*/
        }

        $instruc = DB::select("SELECT par_nombres, par_apellidos FROM sep_participante WHERE par_identificacion=" . $falta[0]->par_identificacion);
        $datos_queja.="\n\nPor informe y/o queja Nro. $idFalta presentado  por instructor(a) " . $instruc[0]->par_nombres . " " . $instruc[0]->par_apellidos . ", identificado con documento de identidad  No." . $falta[0]->par_identificacion;
        $datos_queja = str_replace("\n", "<w:br/>", $datos_queja);
        $hechos_queja = $falta[0]->edu_falta_descripcion;
        //$evidencias_queja = $falta[0]->edu_falta_evidencia;
        $caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',':','--');
		$evidencias_queja = str_replace($caractereNoPremitidos,'',$falta[0]->edu_falta_evidencia);
        
        $descargos = $edu_acta_descargos;
        $practicas = $edu_acta_practicas;
        $existencia = $edu_acta_existencia;
        $constituye = $edu_acta_constituye;
        $normas_queja = "";
        foreach ($faltalit as $flit) {
            $sql = "SELECT lit_codigo,lit_descripcion,art_codigo FROM sep_edu_literal WHERE lit_id=" . $flit->lit_id;
            $lite = DB::select($sql);
            $sql = "SELECT cap_codigo FROM sep_edu_articulo WHERE art_codigo='" . $lite[0]->art_codigo . "'";
            $capi = DB::select($sql);

            $normas_queja.="\n".$capi[0]->cap_codigo."\n".$lite[0]->art_codigo."\nLiteral ". $lite[0]->lit_codigo.' '.$lite[0]->lit_descripcion."\n";
            /*$normas_queja.="\n" . $capi[0]->cap_codigo . "\n "
                    . $lite[0]->art_codigo . "\n"
                    . "Literal " . $lite[0]->lit_codigo . $lite[0]->lit_descripcion;*/
        }




        $autor = $edu_acta_autor;
        $grado_res = $edu_acta_grado_res;
        $grado_falta = $edu_acta_grado_falta;
        $sancion = $edu_acta_sancion;
        //$novedades = "";
        $coordinadores_participantes = "";
        $instructores_participantes = "";
        $bienestar = "";
        $vocero = "";
        $apr_representante = "";

        $felicitacion = "";
        $llamado_atencion = "";
        $condicionamiento = "";
        $cancelacion = "";

        $conclusiones_yanin = "";

        foreach ($faltaapr as $fapr) {
            $conclusiones_yanin.="\n\nNombre del Aprendiz: $fapr->par_nombres $fapr->par_apellidos  N° de Identificación: $fapr->par_identificacion \n"
                    . "\n$existencia existió conducta que evidencie el incumplimiento de un deber o la incurrencia en una prohibición. Que en relación con la prohibición o deber descritos en el Reglamento de aprendices, conforme al informe o  queja que dio lugar a este comité:"
                    . "\n$normas_queja"
                    . "\n$constituye existió conducta que evidencie el incumplimiento de un deber o la incurrencia en una prohibición. En ese sentido, el o la Aprendiz $autor es probable autor de la falta  y $autor es responsable $grado_res de lo sucedido."
                    . "\nProducto del análisis de este comité se  concluye que  la falta  cometida es $grado_falta y en consecuencia $edu_acta_sancion  amerita sanción.
                        \nLa sanción  recomendada por este comité  es \n";

            foreach ($novedades[$fapr->par_identificacion] as $indi => $nov) {

                if ($indi != "observacion") {
                    $sql = "SELECT edu_novedad_descripcion FROM sep_edu_novedad WHERE edu_novedad_id=$indi";
                    $resul = DB::select($sql);
                    $conclusiones_yanin.="\n" . $resul[0]->edu_novedad_descripcion;
                    if ($indi == 1) {
                        $llamado_atencion = "X";
                        $this->generarLlamadoAtencion($fapr->par_identificacion,$fapr->par_nombres . " " . $fapr->par_apellidos, $fapr->par_identificacion, $normas_queja, $id_acta);
                    }
                    if ($indi == 2) {
                        $condicionamiento = "X";
                    }
                    if ($indi == 3) {
                        $cancelacion = "X";
                    }
                } else {
                    $conclusiones_yanin.="\n$nov";
                }
            }
        }

        $conclusiones_yanin = str_replace("\n", "<w:br/>", $conclusiones_yanin);
        //dd($conclusiones_yanin);
        // --- Asignamos valores a la plantilla
        $templateWord->setValue('id_acta', $id_acta);
        $templateWord->setValue('ciudad_fecha', $ciudad_fecha);
        $templateWord->setValue('hora_ini', $hora_ini);
        $templateWord->setValue('hora_fin', $hora_fin);
        $templateWord->setValue('lugar', $lugar);
        $templateWord->setValue('regional', $regional);
        $templateWord->setValue('centro', $centro);
        $templateWord->setValue('quorum', "<w:br/>".$quorum);
        $templateWord->setValue('datos_queja', $datos_queja);
        $templateWord->setValue('hechos_queja', "<w:br/>".$hechos_queja);
        $templateWord->setValue('evidencias_queja', "<w:br/>".$evidencias_queja);
        $templateWord->setValue('descargos', "<w:br/>".$descargos);

        $templateWord->setValue('practicas', "<w:br/>".$practicas);
        $templateWord->setValue('conclusiones_yanin', $conclusiones_yanin);
        //$templateWord->setValue('existencia', $existencia);
        //$templateWord->setValue('constituye', $constituye);
        //$templateWord->setValue('normas_queja', $normas_queja);
        //$templateWord->setValue('autor', $autor);
        //$templateWord->setValue('grado_res', $grado_res);
        //$templateWord->setValue('grado_falta', $grado_falta);
        //$templateWord->setValue('sancion', $sancion);
        //$templateWord->setValue('novedades', $novedades);
        $templateWord->setValue('coordinadores_participantes', $coordinadores_participantes);
        $templateWord->setValue('instructores_participantes', $instructores_participantes);
        $templateWord->setValue('bienestar', $bienestar);
        $templateWord->setValue('vocero', $vocero);
        $templateWord->setValue('apr_representante', $apr_representante);
        $templateWord->setValue('aprendices', $aprendices);
        $templateWord->setValue('felicitacion', $felicitacion);
        $templateWord->setValue('llamado_atencion', $llamado_atencion);
        $templateWord->setValue('condicionamiento', $condicionamiento);
        $templateWord->setValue('cancelacion', $cancelacion);


        $ruta = public_path()
                . '/Modules/Seguimiento/Educativa/Acta/' . $id_acta . '/acta-'
                . $falta[0]->par_identificacion . '-' . $idFalta . '.docx';

        // --- Guardamos el documento
        $templateWord->saveAs($ruta);

        //Generando y descargo el zip
        $zip = new \zipArchive1();

        $zip = $this->comprime(public_path() . '/Modules/Seguimiento/Educativa/Acta/' . $id_acta . '/', 
                'ACTA-' . $idFalta . '/', $zip);

        $pathSave = public_path() . '/Modules/Seguimiento/Educativa/Acta/ACTA-' . $idFalta . ".zip";
        $zip->saveZip($pathSave);
    }

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
    }

    public function generarLlamadoAtencion($par_identi,$nombre_sujeto, $identificacion, $normas_queja, $id_acta) {

        \PhpOffice\PhpWord\Autoloader::register();

        $templateWordLlamado = new TemplateProcessor(getPathUploads() . '/plantillas/llamado_atencion.docx');

        $sql = "SELECT prog_nombre, SUBSTRING_INDEX(niv_for_nombre,' ',1) as niv_for_nombre FROM sep_matricula,sep_ficha,sep_programa, sep_nivel_formacion
                    WHERE sep_matricula.fic_numero=sep_ficha.fic_numero 
                    AND sep_ficha.prog_codigo=sep_programa.prog_codigo 
                    AND sep_nivel_formacion.niv_for_id = sep_programa.niv_for_id
                    AND sep_matricula.par_identificacion=" . $identificacion;
        $programa = DB::select($sql);
        
        $sql="SELECT fic_numero FROM sep_matricula WHERE par_identificacion=$par_identi";
        $ficha=DB::select($sql);

        $nombre_sujeto=ucwords(strtolower($nombre_sujeto));
        
        $fecha = date("d-F-Y");
        
        $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
  $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
  
  $fecha = str_replace($meses_EN, $meses_ES, $fecha);
  
  $fecha = str_replace("-", " de ", $fecha);
        
        $programa_formacion = ucfirst(strtolower($programa[0]->prog_nombre));
        $nivel_formacion = $programa[0]->niv_for_nombre;
        
        $mensaje = "Con base  en el acta de Comité Seguimiento para los Aprendices "
                . "SENA realizado el " . $fecha . ", se definió realizarle "
                . "llamado de atención por infringir en $normas_queja  ";

        $funcionario_elaboro = Auth::user()->participante->par_nombres ." ". Auth::user()->participante->par_apellidos;
        $observacion = "Copia: Carpeta seguimiento " . $ficha[0]->fic_numero;


        // --- Asignamos valores a la plantilla
        $templateWordLlamado->setValue('fecha', $fecha);
        $templateWordLlamado->setValue('nombre_sujeto', $nombre_sujeto);
        $templateWordLlamado->setValue('programa', $programa_formacion);
        $templateWordLlamado->setValue('nivel_formacion', $nivel_formacion);
        $templateWordLlamado->setValue('mensaje', $mensaje);
        $templateWordLlamado->setValue('funcionario_elaboro', $funcionario_elaboro);
        $templateWordLlamado->setValue('observacion', $observacion);


        $ruta = public_path()
                . '/Modules/Seguimiento/Educativa/Acta/' . $id_acta . '/llamado_atencion-'
                . $identificacion . '.docx';

        // --- Guardamos el documento
        $templateWordLlamado->saveAs($ruta);
    }
	/******FUNCION ANULAR COMITE*****/
	public function getAnularcomite()
	{
		extract($_GET);
		
		$eliminar="update sep_edu_falta set edu_est_id=6 where edu_falta_id='$codigo' ";
		DB::update($eliminar);
		
		return redirect(url("seguimiento/educativa/programarcomite"));
	}
	
	/*******************CONSULTA QUEJA APRENDICES*********************/
	function getListarquejaaprendiz() {
		extract($_GET);
        $page = Input::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        // $WHERE = "AND f.par_identificacion=" . Auth::user()->par_identificacion;
		if(isset($identificacion))
		{	
			if($identificacion==""){
				$concatenar="";
			}else{
				$concatenar=" and fal.par_identificacion=$identificacion";
			}
		}	
		else
		{
			$concatenar="";
		}
        $select = DB::select("SELECT edu_falta_apr_id,fal.par_identificacion,p.par_nombres,p.par_apellidos,
			f.edu_falta_fecha,edu_tipo_falta_descripcion,f.par_identificacion as id_instru,
			concat(i.par_nombres,' ',i.par_apellidos) as instru
			FROM sep_edu_falta_apr as fal,sep_participante as p, sep_edu_falta as f,sep_edu_tipo_falta as t,
			sep_participante as i
			where 
			fal.par_identificacion=p.par_identificacion AND
			f.par_identificacion=i.par_identificacion AND
			fal.edu_falta_id=f.edu_falta_id and 
			f.edu_tipo_falta_id=t.edu_tipo_falta_id  $concatenar
			order by f.edu_falta_fecha DESC ");

        $select = new LengthAwarePaginator(
                array_slice(
                        $select, $offset, $perPage, true
                ), count($select), $perPage, $page);
				 $select->setPath("listarquejaaprendiz");

        return view("Modules.Seguimiento.Educativa.listarquejaAprendiz", compact("select", "offset"));
    }
    //Se asigna la falta a otro coordinador
    public function getCoordinador()
    {   extract($_GET);
        if ($falta !="" && is_numeric($falta) && $coordinador !="" && is_numeric($coordinador)) {
            $sql="update sep_edu_falta set par_identificacion_coordinador = '".$coordinador."'
            where 	edu_falta_id = ".$falta."";
            DB::update($sql);
        }
    }
    
public function getDescargarword()
    {
        $id=$_GET['id'];
        $puedeSeguir = false;
        $opt=$_GET['opt'];
        $identificacion=$_GET['apr'];
        $notificacion=['','comite','personal','aviso'];
        $otros ="";

        if ($opt >= 1 && $opt<=3 && is_numeric($id) && is_numeric($identificacion)) {

            $queja = DB::select("SELECT edu_falta_descripcion, edu_falta_evidencia, edu_tipo_falta_descripcion, par_identificacion,par_genera_comite,edu_falta_calificacion FROM "
            . "sep_edu_falta, sep_edu_tipo_falta "
            . "WHERE sep_edu_falta.edu_tipo_falta_id=sep_edu_tipo_falta.edu_tipo_falta_id and "
            . "edu_falta_id=$id");

            $sql = "
            select tabla.*, pro.prog_nombre  from
            (SELECT par.*, max(mat.fic_numero) as fic_numero
            FROM    sep_edu_falta_apr falta, sep_participante par, sep_matricula mat, sep_ficha fic, sep_programa pro
            WHERE   edu_falta_id=$id
            and 	falta.par_identificacion = par.par_identificacion
            and 	par.par_identificacion = mat.par_identificacion
            and 	mat.fic_numero = fic.fic_numero
            and 	fic.prog_codigo = pro.prog_codigo
            and     par.par_identificacion=$identificacion
            group by par.par_identificacion) tabla, sep_ficha fic, sep_programa pro
            where tabla.fic_numero = fic.fic_numero and fic.prog_codigo = pro.prog_codigo";
            $aprendiz = DB::select($sql);

            $sqlComite = "SELECT sep_edu_comite.edu_tipo_com_id,edu_comite_hora,edu_comite_fecha,edu_tipo_com_descripcion "
                    . "FROM sep_edu_comite, sep_edu_tipo_com "
                    . "WHERE sep_edu_comite.edu_tipo_com_id=sep_edu_tipo_com.edu_tipo_com_id and edu_falta_id=$id ORDER BY edu_comite_id DESC";
            $comi = DB::select($sqlComite);

            \PhpOffice\PhpWord\Autoloader::register();

            //Asigancion de variables
            $contacto="";
            $saltoLinea = "\r<w:br/>";
            $nombres = $aprendiz[0]->par_nombres." ".$aprendiz[0]->par_apellidos;
            $documento = $aprendiz[0]->par_identificacion;
            $programa = $aprendiz[0]->prog_nombre;
            $contacto .= "Ficha: ".$aprendiz[0]->fic_numero.$saltoLinea;
            $contacto .= "Email: ".$aprendiz[0]->par_correo.$saltoLinea;
            $contacto .= "Tel: ".$aprendiz[0]->par_telefono;
            $fecha_comite = $this->calculoFecha($comi,1);
            $fecha_generado=$this->calculoFecha(date('m'),2);

            if ($opt == 1) {

                $sql = '
                select  edu_cap.cap_codigo, cap_descripcion, edu_lit.art_codigo, lit_descripcion, art_descripcion
                from    sep_edu_falta edu_fal, sep_edu_falta_lit edu_fal_lit,
                    sep_edu_literal edu_lit, sep_edu_articulo edu_art, sep_edu_capitulo edu_cap
                where   edu_fal.edu_falta_id = edu_fal_lit.edu_falta_id
                and     edu_fal_lit.lit_id = edu_lit.lit_id
                and     edu_lit.art_codigo = edu_art.art_codigo
                and     edu_art.cap_codigo = edu_cap.cap_codigo
                and     edu_fal.edu_falta_id = '.$id.'
                order by edu_lit.art_codigo';
                $literales = DB::select($sql);

                $instructor = DB::select("SELECT * FROM sep_participante WHERE par_identificacion = " . $queja[0]->par_identificacion);

                $capitulo="";
                //Capitulos o literales
                foreach ($literales as $lit) {
                    $capitulo.= "- ".$lit->cap_codigo;
                    $capitulo.= $lit->cap_descripcion;
                    $capitulo.= $lit->art_codigo;
                    $capitulo.= $lit->art_descripcion;
                    $capitulo.= $lit->lit_descripcion;
                    $capitulo.=$saltoLinea.$saltoLinea;
                }

                //Asignacion de variables
                $descripcion_falta=$queja[0]->edu_falta_descripcion;
                $tipo_falta=$queja[0]->edu_tipo_falta_descripcion;
                $nivel_falta=$queja[0]->edu_falta_calificacion;
                $instructor = $instructor[0]->par_nombres." ".$instructor[0]->par_apellidos;

                //variables que se envian al word
                $templateWord = new TemplateProcessor(getPathUploads() . '/plantillas/FormatoCitacionComite.docx');
                $templateWord->setValue('descripcion_falta',$descripcion_falta);
                $templateWord->setValue('capitulo',$capitulo);
                $templateWord->setValue('tipo_falta',$tipo_falta);
                $templateWord->setValue('nivel_falta',$nivel_falta);
                $templateWord->setValue('instructor',$instructor);
                $otros = ",'SI','NO','NO')";
                $ruta = "Citacion/CitacionComite";
                $puedeSeguir = true;
            }else if($opt == 2){
                if (isset($_GET['res']) && isset($_GET['est']) && isset($_GET['fec_res'])) {
                    //encargada de generar el acta y el comite
                    $encargada = DB::select("SELECT * FROM sep_participante WHERE par_identificacion = " . $queja[0]->par_genera_comite);
                    $encargada = $encargada[0]->par_correo;

                    //Fecha de la resolucion
                    $fecha_resolucion=$this->calculoFecha($_GET['fec_res'],3);

                    //variables que se envian al word
                    $templateWord = new TemplateProcessor(getPathUploads() . '/plantillas/FormatoNotificacionPersonal.docx');
                    $templateWord->setValue('resolucion',$_GET['res']);
                    $templateWord->setValue('estado',utf8_decode($_GET['est']));
                    $templateWord->setValue('fecha_resolucion',$fecha_resolucion);
                    $templateWord->setValue('encargada',$encargada);
                    $otros = ",'NO','SI','NO')";
                    $ruta = "Notificacion-Personal/NotificacionPersonal";
                    $puedeSeguir = true;
                }
            }else if($opt == 3){
                if (isset($_GET['res']) && isset($_GET['fec_res'])){
                    $fecha_resolucion=$this->calculoFecha($_GET['fec_res'],3);
                    $templateWord = new TemplateProcessor(getPathUploads() . '/plantillas/FormatoNotificacionAviso.docx');
                    $templateWord->setValue('resolucion',$_GET['res']);
                    $templateWord->setValue('fecha_resolucion',$fecha_resolucion);
                    $otros = ",'NO','NO','SI')";
                    $ruta = "Notificacion-Aviso/NotificacionAviso";
                    $puedeSeguir = true;
                }
            }

            //variables que se envian al word
            $templateWord->setValue('nombres',$nombres);
            $templateWord->setValue('documento',$documento);
            $templateWord->setValue('programa',$programa);
            $templateWord->setValue('contacto',$contacto);
            $templateWord->setValue('fecha_generado',$fecha_generado);
            $templateWord->setValue('fecha_comite',$fecha_comite);

            if ($puedeSeguir) {
                //validamos si antes se habia generado alguna notificacion para el aprendiz
                $sql="select * from sep_edu_notificacion
                where edu_falta_id = $id and par_identificacion = $identificacion";
                $consulta= DB::select($sql);

                if (count($consulta) > 0) {
                    //validamos si ya se habia generado la notificacion
                    $sql="select * from sep_edu_notificacion
                    where edu_falta_id = $id
                    and par_identificacion = $identificacion
                    and ".$notificacion[$opt]." = 'SI'";
                    $consulta = DB::select($sql);
                    //Si el aprendiz no se le ha generado la notificacion se actualiza
                    if (count($consulta) == 0) {
                        $sql="update sep_edu_notificacion set ".$notificacion[$opt]." = 'SI'
                        where edu_falta_id = $id and par_identificacion = $identificacion";
                        DB::update($sql);
                    }
                }else{
                    $sql="insert into sep_edu_notificacion value(default,$id,$identificacion".$otros;
                    DB::insert($sql);
                }

                //Generamos el archivo
                $ruta = public_path() . '/Modules/Seguimiento/Educativa/'.$ruta.'_'.time().'.docx';
                $templateWord->saveAs($ruta);
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
                header("Cache-Control: post-check=0, pre-check=0", false);
                return response()->download($ruta);
            }
        }
    }
    function calculoFecha($fecha,$opcion)
    {
        $meses=['enero','febrero','marzo','abril','mayo','junio','julio',
        'agosto','septiembre','octubre','noviembre','diciembre'];
        $dia=["domingo","lunes","martes","miércoles","jueves","viernes","sábado"];
        if ($opcion == 1) {
            $fecha_comite = $fecha[0]->edu_comite_fecha;
            $hora = $fecha[0]->edu_comite_hora;
            $diaLetra = $dia[date('w', strtotime($fecha_comite))];
            $diaNumero = date('d', strtotime($fecha_comite));
            $mes = date('m',strtotime($fecha_comite));
            $año = date('Y',strtotime($fecha_comite));
            if ($mes < 10) {
                $mes = substr($mes, 1);
            }
            $fecha = utf8_decode($diaLetra." ".$diaNumero." de ".$meses[$mes-1]." de ".$año." a las ".$hora);
        }else if($opcion == 2){
            if ($fecha < 10) {
                $fecha = substr($fecha, 1);
            }
            $fecha = utf8_decode(date('d')." de ".$meses[$fecha-1]." del ".date('Y'));
        }else if($opcion == 3){
            $diaLetra = $dia[date('w', strtotime($fecha))];
            $diaNumero = date('d', strtotime($fecha));
            $mes = date('m',strtotime($fecha));
            $año = date('Y',strtotime($fecha));
            $fecha = utf8_decode($diaLetra." ".$diaNumero." de ".$meses[$mes-1]." del ".$año);
        }
        return $fecha;
    }
}
