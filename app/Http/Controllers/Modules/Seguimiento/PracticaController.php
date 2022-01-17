<?php

namespace App\Http\Controllers\Modules\Seguimiento;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use \Illuminate\Pagination\LengthAwarePaginator;

// Modelos del modulo usuarios
//use App\Http\Models\Modules\Users\User;

class PracticaController extends Controller {
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('control_roles');

    }
	public function getIndex($id = false) {
        $page = Input::get('page', 1);
        $perPage = 40;
        $offset = ($page * $perPage) - $perPage;	                  
		
        if($id!=""){
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
					sep_matricula.est_id in(2,10)
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
			}
		else{
				$sql=" select sep_matricula.par_identificacion,fic_numero,par_nombres,par_apellidos, users.*  from sep_matricula, sep_participante, users "
					." where sep_matricula.par_identificacion = sep_participante.par_identificacion and sep_participante.par_identificacion=users.par_identificacion and fic_numero=0  ORDER BY sep_participante.par_nombres ASC";
				$sql1="
				select 
					prog_nombre,par_nombres, par_apellidos,fic_numero,
					fic_fecha_fin,par_identificacion_productiva
				from 
					sep_participante,sep_ficha,sep_programa
				where   
					sep_programa.prog_codigo=sep_ficha.prog_codigo
					and sep_ficha.par_identificacion=sep_participante.par_identificacion
					and fic_numero=0 ORDER BY sep_participante.par_nombres ASC";
			}	
			
		$instructores = DB::select("select * from sep_participante where rol_id=2");
		
		$users = DB::select($sql);
		$datos = DB::select($sql1);
       	$fecha_tiempo = date("Y-m-d",strtotime($datos[0]->fic_fecha_fin."+ 18 month"));
        
        $users = new LengthAwarePaginator(
                array_slice(
                        $users, $offset, $perPage, true
                ), count($users), $perPage, $page);
        
        $users->setPath("index");
        //dd($datos);
        return view("Modules.Seguimiento.Practica.index", compact("users","offset","id","datos","instructores","fecha_tiempo"));
    }
	
	public function postIndex(){
		return $this->getIndex($_POST['cedula']);
	}
	
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
		
		return view("Modules.Seguimiento.Practica.modales",compact("observaciones_lider","fechas","rol","seguiVisita","readonly","disabled","seg_pro_obs_instructor_seguimiento","ope_id","seg_pro_fecha_fin","seg_pro_fecha_ini","seg_pro_nombre_empresa","seg_pro_obs_lider_productiva","alternativas","id","nombre","apellido","seguiProductiva","visitas","bitacoras"));
	} 
	public function postSeguimiento(Request $request){
		extract($_POST);
		//Consultamos que rol inicio sesión
		$rol = DB::select("select rol_id as rol from sep_participante where par_identificacion = ". \Auth::user()->par_identificacion ."");
		$sql="select seg_pro_id, seg_pro_obs_lider_productiva from sep_seguimiento_productiva where par_identificacion_aprendiz = '$id'";
		$aprendiz = DB::select($sql);
		$observaciones_lider_productiva = "";
		if($ope_id == ""){
		    $ope_id = 11;
		}
		
		if( $rol[0]->rol != 7){
			if(count($aprendiz)>0){				
				$id_viejo = $aprendiz[0]->seg_pro_id;
				$observaciones_lider_productiva = $aprendiz[0]->seg_pro_obs_lider_productiva;
				
				$sqlDelete = "delete from sep_seguimiento_productiva where seg_pro_id = $id_viejo";
				DB::delete($sqlDelete);
				
				$sqlDelete = "delete from sep_seguimiento_visita where seg_pro_id = $id_viejo";
				DB::delete($sqlDelete);

				$sqlDelete = "delete from sep_seguimiento_bitacora where seg_pro_id = $id_viejo";
				DB::delete($sqlDelete);
			}

			$sql="INSERT INTO sep_seguimiento_productiva (
					fic_numero,par_identificacion_responsable,par_identificacion_aprendiz,"
					. "ope_id,"
					. "seg_pro_nombre_empresa,"
					. "seg_pro_fecha_ini,"
					. "seg_pro_fecha_fin,"
					. "seg_pro_obs_lider_productiva,"
					. "seg_pro_obs_instructor_seguimiento)"
					. "values("
					. "'$ficha','" . Auth::user()->par_identificacion . "','$id',$ope_id,'$empresa','$fecha_inicio',"
					. "'$fecha_fin','$observaciones_lider_productiva','$seg_pro_obs_instructor_seguimiento')";
            /*print_r($sql);
	    	dd();*/
			DB::insert($sql);
			
		   
			$idSeguimiento = DB::getPdo()->lastInsertId();
			
			// Inserción de las visitas
			$sql = "insert into sep_seguimiento_visita (seg_pro_id,seg_vis_visita,seg_vis_fecha) values ";
					
			if(isset($visita1)){
				$sql_visita = $sql."($idSeguimiento,1,'$fecha1')";
				DB::insert($sql_visita);
			}
			
			if(isset($visita2)){
				$sql_visita = $sql."($idSeguimiento,2,'$fecha2')";
				DB::insert($sql_visita);
			}
			
			if(isset($visita3)){
				$sql_visita = $sql."($idSeguimiento,3,'$fecha3')";
				DB::insert($sql_visita);
			}
			
			//iNSERCION DE LAS BITACORAS
			if(isset($bitacora)){
				foreach($bitacora[$id] as $bit){
					$sql3="
					INSERT INTO sep_seguimiento_bitacora 
					values (".$idSeguimiento.",'$bit',null)";
					$insert = DB::insert($sql3);
				}
			}
			
            //validar si ya tiene las 12 bitacoras y la visita final para actualizar el estado del aprendiz
			$sql= "select COUNT(vis.seg_vis_visita) as final 
			from sep_seguimiento_visita as vis , sep_seguimiento_productiva as pro
			where pro.fic_numero = $ficha
			and pro.par_identificacion_aprendiz = $id
			and vis.seg_pro_id = pro.seg_pro_id
			and vis.seg_vis_visita = 3";
			$visita_final=DB::select($sql);

			$sql = "
			select COUNT(bit.seg_bit_bitacora) as bitacora, bit.seg_pro_id
			from  sep_seguimiento_bitacora as bit , sep_seguimiento_productiva as pro
			where pro.seg_pro_id = bit.seg_pro_id
			and   pro.fic_numero = $ficha
			and   pro.par_identificacion_aprendiz = $id
			and   bit.seg_bit_bitacora = 12";
			$bitacora=DB::select($sql);

			if ($bitacora[0]->bitacora == 1 && $visita_final[0]->final == 1) {
				$sql="
				update sep_matricula set est_id = 3
		        where par_identificacion = ".$id." 
		        and   fic_numero = ".$ficha."";
		        DB::update($sql);
			}
			
			$sqlConsultaAprendiz = "
				select count(*) as total 
				from sep_etapa_practica
				where par_identificacion = '$id'";
			$consultaAprendiz = DB::select($sqlConsultaAprendiz);
			
			$fechaActual = date('d/m/Y');
			
			if($consultaAprendiz[0]->total > 0){
				$sqlActualizar = "
					update sep_etapa_practica
					set ope_id = '$ope_id'
					where par_identificacion = '$id'";
					
				DB::update($sqlActualizar);
			}else{
				$sqlInsertar = "
					insert into sep_etapa_practica
					(par_identificacion,ope_id,etp_fecha_registro)
					values ('$id','$ope_id','$fechaActual')";
					
				DB::insert($sqlInsertar);
			}
		}else{
			if(count($aprendiz)>0){
				$sqlActualizar = "
					update sep_seguimiento_productiva
					set seg_pro_obs_lider_productiva = '$seg_pro_obs_lider_productiva'
					where par_identificacion_aprendiz = '$id'";
					
				DB::update($sqlActualizar);
			}else{
				echo$sqlRegistrar = "
				insert into sep_seguimiento_productiva 
					(fic_numero,par_identificacion_aprendiz,
					par_identificacion_responsable,seg_pro_obs_lider_productiva,ope_id)
				values
					('$ficha','$id','" . Auth::user()->par_identificacion . "',
					'$seg_pro_obs_lider_productiva',11)";

				DB::insert($sqlRegistrar);
			}
		}
    }
	public function getAjaxinstructorliderpractica(){
		extract($_GET);
		
		$sqlUpdate = DB::update("update sep_ficha set par_identificacion_productiva = '$vIngresado' where fic_numero = '$ficha'");
		
		$sqlInstructor = DB::select("select par_nombres,par_apellidos from sep_participante where par_identificacion = '$vIngresado'");
		
		echo $sqlInstructor[0]->par_nombres." ".$sqlInstructor[0]->par_apellidos;
	}
}
