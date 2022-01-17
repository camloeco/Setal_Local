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

class BienestarController extends Controller {
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('control_roles');
		date_default_timezone_set('America/Bogota');
    }
	public function getIndex($valor=false , $filtro=false)
	{
	    //Actualizar el estado de los beneficios
		actualizarBenedicioSena();
		
		extract($_GET);
		$rol = \Auth::user()->participante->rol_id;
        $fecha_actual = date('Y-m-d');
        $registroPorPagina = 10;
        $limit = $registroPorPagina ;
        if(isset($pagina)){
            $hubicacionPagina = $registroPorPagina*($pagina-1);
            $limit = $hubicacionPagina.','.$registroPorPagina;
        }else{
            $pagina = 1;
        }
		$Consulta="";
		$tabla="";
		$campos="";
        //filtro del index
        if($valor!=""){
            if($filtro == 1){
                $Consulta=" and par.par_identificacion = '$valor'";
            }else if($filtro == 2){
                $Consulta=" and par.par_nombres like '%$valor%'";
            }else if ($filtro == 3) {
				$Consulta = " and par.par_apellidos like '%$valor%'";
			}else if($filtro == 4){
                if (is_numeric($valor)) {
					if ($valor == 100) {
						$Consulta ="
						and par.par_identificacion = (select bene.par_identificacion
						from sep_beneficios_sena_aprendiz as bene
						where bene.par_identificacion=par.par_identificacion and bene.estado = 1 and bene.fecha_fin >= '".$fecha_actual."' limit 1)";
					}else if ($valor == 0) {
						$Consulta ="
						and par.beneficio_sena != 1";
					}else{
						$tabla=" , sep_beneficios_sena_aprendiz as bene";
						$Consulta = "
						and bene.par_identificacion = par.par_identificacion
						and bene.beneficio_sena_id = $valor
						and bene.estado = 1
						and bene.fecha_fin >= '".$fecha_actual."'";
					}
				}
			}else if($filtro == 5){
				$tabla=" , sep_matricula as mat";
				$Consulta = "
				and mat.par_identificacion = par.par_identificacion
				and mat.fic_numero = $valor ";
			}
        }
		// Consultar aprendices
		$sql = '
		select  par.par_identificacion, par.par_nombres, par.par_apellidos , user.estado, user.id '.$campos.'
		from    sep_participante as par , users as user '.$tabla.'
		where   user.par_identificacion = par.par_identificacion
		and     par.rol_id = 1 '.$Consulta.'
		order   by par.par_nombres asc  limit '.$limit;
        $aprendices= DB::select($sql);
		$sqlContador = '
		select  COUNT(par.par_nombres) as total
		from    sep_participante as par , users as user '.$tabla.'
		where   user.par_identificacion = par.par_identificacion
		and     par.rol_id = 1 '.$Consulta.'
		order   by par.par_identificacion asc';
        $aprendicesContador = DB::select($sqlContador);

		//Aprendices con beneficios
		$sql="select par_identificacion from sep_beneficios_sena_aprendiz 
		where estado = 1 
		and fecha_fin >= '".$fecha_actual."'
		group by par_identificacion";
        $beneficiarios_sena=DB::select($sql);
		$beneficiarios=array();
		foreach ($beneficiarios_sena as $val) {
			$beneficiarios[$val->par_identificacion]="ok";
		}
          
		// Paginado
		$aprendicesContador = $aprendicesContador[0]->total;
        $cantidadPaginas = ceil($aprendicesContador/$registroPorPagina);
        $contador = (($pagina-1)*$registroPorPagina)+1;

		//Beneficios
        $beneficios=DB::select("select * from sep_beneficios_sena");
        
		//Condicionados
		$condicionado = array();
	    foreach ($beneficiarios_sena as $bene) {
			$sql="select apr_fal.par_identificacion as documento, fal.edu_falta_id
			from sep_edu_falta_apr as apr_fal
			left join sep_edu_falta fal on fal.edu_falta_id = apr_fal.edu_falta_id
			where apr_fal.par_identificacion = ".$bene->par_identificacion."
			and fal.edu_est_id !=3
			and fal.edu_est_id !=5";
			$condicionados = DB::select($sql);
			if (count($condicionados) > 0) {
				$condicionado[$condicionados[0]->documento]=$condicionados[0]->edu_falta_id;
			}
		}
		return view('Modules.Seguimiento.Bienestar.index', compact('rol','condicionado','aprendices','aprendicesContador','cantidadPaginas','pagina','valor','filtro','contador','beneficios','beneficiarios'));
	}

	public function getBeneficios($identificacion){

		$beneficios = DB::select("select * from sep_beneficios_sena");

		$sql ="select * from sep_participante where par_identificacion = ".$identificacion."";
		$aprendiz = DB::select($sql);

		$sql= "
			select apr_sena.id , apr_sena.fecha_inicio , apr_sena.fecha_fin , apr_sena.observacion, sena.ben_sen_nombre as beneficio, apr_sena.estado
			from sep_beneficios_sena_aprendiz as apr_sena , sep_beneficios_sena as sena
			where apr_sena.par_identificacion = ".$identificacion." and sena.id = apr_sena.beneficio_sena_id";
        $historial = DB::select($sql);

		//Validar  si esta condicionado
		$sql="select apr_fal.par_identificacion as documento, fal.edu_falta_id , fal.edu_falta_fecha , est.edu_est_descripcion , tip_fal.edu_tipo_falta_descripcion
		from sep_edu_falta_apr as apr_fal
		left join sep_edu_falta fal on fal.edu_falta_id = apr_fal.edu_falta_id
		left join sep_edu_estado est on est.edu_est_id = fal.edu_est_id
		left join sep_edu_tipo_falta tip_fal on tip_fal.edu_tipo_falta_id = fal.edu_tipo_falta_id
		where apr_fal.par_identificacion = ".$identificacion."
		and fal.edu_est_id !=3
		and fal.edu_est_id !=5";
		$faltas = DB::select($sql);
	  	return view('Modules.Seguimiento.Bienestar.beneficios', compact('identificacion', 'aprendiz', 'beneficios','historial', 'faltas'));
	}

	public function postAsignarbeneficio()
	{
		extract($_POST);
		$error="";
		$fecha_actual = date('Y-m-d');
		if (isset($id) && isset($beneficio) && isset($fecha_inicio) && isset($fecha_fin) && isset($observacion)) {
			if (is_numeric($id) && $id!="") {
				if (count($beneficio) == count($fecha_inicio) && count($fecha_inicio) == count($fecha_fin)) {
					//se indica que el aprendiz se le asigna beneficios sena
					$sql="
						update sep_participante
						set beneficio_sena = 1
						where par_identificacion = '".$id."'";
					DB::update($sql);

                    //registro de beneficios
					for ($i=0; $i <count($beneficio); $i++) {
						if (is_numeric($beneficio[$i]) && $fecha_inicio[$i] !="" && $fecha_fin[$i] !="") {
							$sql = "select * from sep_beneficios_sena_aprendiz
							where fecha_inicio = '".$fecha_inicio[$i]."'
							and fecha_fin = '".$fecha_fin[$i]."'
							and par_identificacion = ".$id."
							and beneficio_sena_id = ".$beneficio[$i]."";
							$total = DB::select($sql);
							if (count($total) == 0) {
								if ($fecha_fin[$i] > $fecha_actual) {
									$estado = 1;
								}else{
									$estado = 3;
								}
								DB::table('sep_beneficios_sena_aprendiz')->insert([
									'par_identificacion' => $id,
									'beneficio_sena_id' => $beneficio[$i],
									'fecha_inicio' => $fecha_inicio[$i],
									'fecha_fin' => $fecha_fin[$i],
									'observacion' => $observacion[$i],
									'estado' => $estado
								]);
							}else {
								$error=$error."El aprendiz ya tiene uno de los beneficios registrados";
							}
						}else {
							$error=$error."El campo fecha de inicio o fecha fin estan vacios";
						}
					}
				}else{
					$error=$error."Error #404";
				}
			}else{
				$error=$error."Error #404";
			}
			if ($error == "") {
				$_SESSION['mensaje']['ok']="Los beneficios fueron registrados";
				return redirect('seguimiento/bienestar/beneficios/'.$id);
			}else{
				$_SESSION['mensaje']['error']= $error;
				return redirect('seguimiento/bienestar/beneficios/'.$id);
			}
		}
	}
	public function getEdit(){
        extract($_GET);
		$respuesta="";
		$fecha_actual = date('Y-m-d');
		if (isset($option)) {
			if ($id !="" && is_numeric($id) && $aprendiz !="" && is_numeric($aprendiz) && isset($observacion)) {
				$sql="update sep_beneficios_sena_aprendiz
				set observacion ='".$observacion."'
				where id = ".$id." and par_identificacion = ".$aprendiz."";
		  		DB::update($sql);
		 		$respuesta = "ok";
			}
		}else{
			if ($vis == 1 && $fecha_inicio != "" && $fecha_fin !="" && $id !="" && is_numeric($id) && $aprendiz !="" && is_numeric($aprendiz)) {
				if ($fecha_actual > $fecha_fin) {
					$estado = 3;
				}else{
					$estado = 1;
				}
				$sql="update sep_beneficios_sena_aprendiz
					set fecha_inicio ='".$fecha_inicio."' ,
					fecha_fin = '".$fecha_fin."',
					estado = $estado
					where id = ".$id." and par_identificacion = ".$aprendiz."";
				DB::update($sql);
				$respuesta = "ok";
			}else if($vis == 2 && $aprendiz !="" && $id !="" && $estado !="" && is_numeric($aprendiz) && is_numeric($id) && is_numeric($estado)){
                if ($estado == 1) {
					$estado = 2;
				}else{
					$estado = 1;
				}
				$sql="update sep_beneficios_sena_aprendiz
			    set estado = ".$estado."
				where id = ".$id." and par_identificacion = ".$aprendiz."";
				DB::update($sql);
				$respuesta = "ok";
			}
		}
		//Se valida si el usuario tiene beneficios activos si no es asi se cambia el estado en sep_participante
		if ($respuesta == "ok") {
			$sql="
				select * from sep_beneficios_sena_aprendiz
				where par_identificacion = ".$aprendiz."
				and fecha_fin >= '".$fechaActual."' and estado = 1";
			$beneficios = DB::select($sql);
			if (count($beneficios) == 0) {
				$sql="
					update sep_participante set beneficio_sena = 0
					where par_identificacion = '".$aprendiz."'";
				DB::update($sql);
			}else{
			    $sql="
					update sep_participante set beneficio_sena = 1
					where par_identificacion = '".$aprendiz."'";
				DB::update($sql);
			}
		}
		return $respuesta;
	}
	public function getDelete(){
		extract($_GET);
        if (is_numeric($id) && $id !="" && is_numeric($aprendiz) && $aprendiz !="") {
			$sql="delete from sep_beneficios_sena_aprendiz where id = ".$id."";
		    DB::delete($sql);
			$sql="select id from sep_beneficios_sena_aprendiz where par_identificacion = ".$aprendiz."";
            $beneficios_aprendiz=DB::select($sql);
			if (count($beneficios_aprendiz) == 0) {
				$sql="update sep_participante set beneficio_sena = 0 where par_identificacion = ".$aprendiz."";
		    	DB::update($sql);
			}
			//Se valida si el usuario tiene beneficios activos si no es asi se cambia el estado en sep_participante 
			$sql="
				select * from sep_beneficios_sena_aprendiz
				where par_identificacion = ".$aprendiz."
				and fecha_fin >= '".$fechaActual."' and estado = 1";
			$beneficios = DB::select($sql);
			if (count($beneficios) == 0) {
				$sql="
					update sep_participante
					set beneficio_sena = 0
					where par_identificacion = '".$aprendiz."'";
				DB::update($sql);
			}else{
			    $sql="
					update sep_participante
					set beneficio_sena = 1
					where par_identificacion = '".$aprendiz."'";
				DB::update($sql);
			}
			return "ok";
		}else{
			return "";
		}
	}
	public function getImportar(){
	    //Actualizar el estado de los beneficios
		actualizarBenedicioSena();
		return view("Modules.Seguimiento.Bienestar.importar");
	}
	public function postImportar(Request $request){
		// ���Se ha cargado el archivo CSV?
		if($request->hasFile('archivo')) {
			$archivo = $request->file('archivo');
			// ���El archivo cumple con el formato esperado - EXCEL (xls, xlsx) ?
			if($archivo->getClientOriginalExtension() == 'xls' || $archivo->getClientOriginalExtension() == 'xlsx') {
				$filename = time() . '-' . $archivo->getClientOriginalName();
				// Configuracion del directorio multimedia
				$pathCsv = getPathUploads() . '/CSV/Beneficiarios';
				// Se mueve el archivo CSV al directorio multimedia
				$archivo->move($pathCsv, $filename);
				
				$mensajes = validarBeneficiariosSena($pathCsv, $filename);
			}else{
				$mensajes['formato'] = 'El archivo no cumple con el formato esperado - CSV, por favor cargar un formato valido';
			}
		}else{
			$mensajes['archivo'] = 'No se adjunto ning&uacute;n archivo';
		}
		session()->put('mensajes', $mensajes);
		return redirect(url('seguimiento/bienestar/importar'));
	}

	public function getReporte()
	{
        $tipo=$_GET['tipo'];
		$fecha_actual=Date('Y-m-d');
        $filas="";
        $productos = array();
        $fichas=array();
		$c=1;
        if ($tipo >= 1 && $tipo<= 3) {
		   $tipo_beneficio=" and apr.beneficio_sena_id = $tipo";
		}elseif ($tipo == 4) {
			$tipo_beneficio="";
		}
		$sql="
		select apr.par_identificacion , par.par_nombres , par.par_apellidos ,
		ben.ben_sen_nombre , apr.fecha_inicio , apr.fecha_fin , apr.observacion
		from sep_beneficios_sena_aprendiz as apr
		left join sep_participante par on par.par_identificacion = apr.par_identificacion
		left join sep_beneficios_sena ben on ben.id = apr.beneficio_sena_id
		where  apr.fecha_fin >= '".$fecha_actual."'
		and apr.estado = 1 $tipo_beneficio";
        $aprendices =  DB::select($sql);
		
		//Agrupar los beneficios en una sola columna
		$beneficios_1 = array();
		$fecha_1 = array();
		$fecha_2 = array();
		$observacion_1 = array();

		foreach ($aprendices as $apr) {
			$sql="
			select ben.ben_sen_nombre , apr.fecha_inicio , 
			apr.fecha_fin , apr.observacion
			from sep_beneficios_sena_aprendiz as apr
			left join sep_beneficios_sena ben on ben.id = apr.beneficio_sena_id
			where apr.par_identificacion = ".$apr->par_identificacion."
			and apr.fecha_fin >= '".$fecha_actual."'
			and apr.estado = 1 $tipo_beneficio";
			$Beneficios_aprendiz=DB::select($sql);
			$beneficio="";
			$fecha1="";
			$fecha2="";
			$observacion="";
			$contador=1;
			foreach ($Beneficios_aprendiz as $val) {
				if (count($Beneficios_aprendiz) < 2) {
					$beneficio.=$val->ben_sen_nombre;
					$fecha1.=$val->fecha_inicio;
					$fecha2.=$val->fecha_fin;
					$observacion.=$val->observacion;
				}else{
					$beneficio.=$contador.". ".$val->ben_sen_nombre."<br>";
					$fecha1.=$contador.". ".$val->fecha_inicio."<br>";
					$fecha2.=$contador.". ".$val->fecha_fin."<br>";
					$observacion.=$contador.". ".$val->observacion."<br>";
				}
				
				$contador++;
			}
			$beneficios_1["".$apr->par_identificacion.""]=$beneficio;
			$fecha_1["".$apr->par_identificacion.""]=$fecha1;
			$fecha_2["".$apr->par_identificacion.""]=$fecha2;
			$observacion_2["".$apr->par_identificacion.""]=$observacion;
		}
        //Crear filas con la información
        foreach ($aprendices as $val){
            $filas.="
            <tr>
            <td>".$c++."</td>
			<td>".utf8_decode($val->par_identificacion)."</td>
            <td>".utf8_decode($val->par_nombres)."</td>
            <td>".utf8_decode($val->par_apellidos)."</td>
            <td>".utf8_decode($beneficios_1["".$val->par_identificacion.""])."</td>
            <td>".utf8_decode($fecha_1["".$val->par_identificacion.""])."</td>
            <td>".utf8_decode($fecha_2["".$val->par_identificacion.""])."</td>
            <td>".utf8_decode($observacion_2["".$val->par_identificacion.""])."</td></tr>";
        }
        //Exportar la tabla
        $tabla = '
        <style>
          table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            font-family:Arial;
          }
          #campos{ background:#e9791a; color:white; }
        </style>
        <h1>BENEFICIARIOS SENA</h1>
        <table cellspacing="0" cellpadding="0">
        <tr id="campos"><th>C&oacute;digo</th><th>Documento</th><th>Nombres</th><th>Apellidos</th>
		<th>Beneficio</th><th>Fecha Inicio</th><th>Fecha Fin</th><th>Observacion</th>';
        $tabla.=$filas."</table>";
        header('Content-type: application/vnd.ms-excel; charset=utf-8');
        header("Content-Disposition: attachment; filename=BENEFICIARIOS_SENA_CDTI.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $tabla;
	}
	public function getFalta(Request $request)
	{
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
        if ($estado == "PROGRAMADO") {
            $sqlComite = "SELECT sep_edu_comite.edu_tipo_com_id,edu_comite_hora,edu_comite_fecha,edu_tipo_com_descripcion "
                    . "FROM sep_edu_comite, sep_edu_tipo_com "
                    . "WHERE sep_edu_comite.edu_tipo_com_id=sep_edu_tipo_com.edu_tipo_com_id and edu_falta_id=$id ORDER BY edu_comite_id DESC";
            $comi = DB::select($sqlComite);
            return view("Modules.Seguimiento.Bienestar.faltamodal", compact('literales',"queja", "aprendicesQueja", "comi", "instructor", "estado"));
        } else {
            return view("Modules.Seguimiento.Bienestar.faltamodal", compact('literales',"queja", "aprendicesQueja", "instructor", "estado"));
        }
	}
	//ver lista de beneficios para los funcionarios que solo pueden consultar el modulo bienestar
	public function getShow($id) {
		if (is_numeric($id)) {
			$sql="select par_nombres, par_apellidos from sep_participante where par_identificacion = ".$id."";
			$user = DB::select($sql);
			$fecha_actual=date("Y-m-d");
			$sql= "
				select apr_sena.fecha_inicio , apr_sena.fecha_fin , sena.ben_sen_nombre as beneficio
				from sep_beneficios_sena_aprendiz as apr_sena , sep_beneficios_sena as sena
				where apr_sena.par_identificacion = ".$id." and sena.id = apr_sena.beneficio_sena_id and apr_sena.estado = 1";
        	$beneficios=DB::select($sql);
        	return view("Modules.Seguimiento.Bienestar.show", compact('beneficios','user'));
		}else{
			return "<h1>Error #404</h2>";
		}
    }
}