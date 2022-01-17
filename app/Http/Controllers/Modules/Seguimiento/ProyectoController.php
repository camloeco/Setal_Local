<?php


namespace App\Http\Controllers\Modules\Seguimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use \Illuminate\Pagination\LengthAwarePaginator;
use \Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;


session_start();


class ProyectoController extends Controller{


    public function __construct() {
        $this->middleware('auth');		
        $this->middleware('control_roles');
    }

    public function getIndex($valor=false , $filtro=false){
        extract($_GET);

        $registroPorPagina = 10;
        $limit = $registroPorPagina ;
        if(isset($pagina)){
            $hubicacionPagina = $registroPorPagina*($pagina-1);
            $limit = $hubicacionPagina.','.$registroPorPagina;
        }else{
            $pagina = 1;
        }

      
        // Validar busqueda del Barrio
        $concatenarProyectoprimeraSql = '';
        $concatenarProyectosegundaSQL = '';
        if(isset($_GET['pro_id'])){
            $concatenarProyectoprimeraSql = ' and pro.pro_id = "'.$pro_id.'" ';
            $concatenarProyectosegundaSQL = ' and pro_id = "'.$pro_id.'" ';
        }else{
            $pro_id = '';
        }

        if($valor==""){

            $sql = '
            select  *
            from    sep_proyecto as pro
            where   pro_id = pro_id
            order   by pro_id asc  limit '.$limit;

            // Consulta proyectos de formación
                
            $sqlContador = '
            select  count(pro_nombre) as total 
            from  sep_proyecto';

        }else{

            if($filtro == 1){
                $busqueda=" pro_codigo like '%$valor%'";
            }elseif($filtro == 2){
                $busqueda=" pro_nombre like '%$valor%'";
            }

            $sql = "select * from sep_proyecto 
                    where pro_id=pro_id and (".$busqueda.")
                    order by pro_nombre asc
                    limit ".$limit;

            
            // Consulta proyectos de formación
                
            $sqlContador = "
            select  count(pro_nombre) as total 
            from  sep_proyecto as pro 
            where pro_id=pro_id and (".$busqueda.")";
        
        }

         
        // Paginado
        $proyectos= DB::select($sql);
        
        $sql2="SELECT * FROM sep_producto";
		$productos= DB::select($sql2);

        $proyectosContador = DB::select($sqlContador);
		$contadorProyectos = $proyectosContador[0]->total;
        $cantidadPaginas = ceil($contadorProyectos/$registroPorPagina);
        $contador = (($pagina-1)*$registroPorPagina)+1;

        return view('Modules.Seguimiento.Proyecto.index', compact('productos','pro_id','contadorProyectos','proyectos','cantidadPaginas','contador','pagina','valor','filtro'));  
    }

    public function getCreateproyecto(){
        $proyectDisponibles= DB::select("SELECT * FROM sep_proyecto WHERE pro_id=pro_id");
        
        return view("Modules.Seguimiento.Proyecto.create", compact("proyectDisponibles"));
       
    }

    public function postCreateproyecto(Request $request){

        $array = $request->get('nombre');
        $cont=0;
        for($z=0; $z<count($array); $z++){
            if($array[$z]==""){
                $cont++;
            }
        }

        $validar = Array(
            'pro_codigo' => 'required|numeric',
            'pro_nombre' => 'required',
            'pro_problema'=> 'required',
            'pro_obj_general'=> 'required',
            'pro_obj_especifico'=> 'required',
        );
    
        $messages = [
            'pro_codigo.required' => 'El campo c&oacute;digo del proyecto es obligatorio',
            'pro_nombre.required' => 'El campo nombre del proyecto es obligatorio',
            'pro_problema.required'=> 'El campo problema del proyecto es obligatorio',
            'pro_obj_general.required'=> 'El campo objetivo general del proyecto es obligatorio',
            'pro_obj_especifico.required'=> 'El campo objetivo especifico del proyecto es obligatorio',
        ];

        $validacion = Validator::make($_POST, $validar, $messages);

        if ($validacion->fails() || $cont>0) {
            //$messages=$validacion->errors();
            $messages=[
                'mal'=> 'Uno o/u varios de los campos estan vacios'
            ];
            $antiguos= array();
            $antiguos[0]=$request->get('pro_codigo');
            $antiguos[1]=$request->get('pro_nombre');
            $antiguos[2]=$request->get('pro_problema');
            $antiguos[3]=$request->get('pro_obj_general');
            $antiguos[4]=$request->get('pro_obj_especifico');
            $prueba=$request->get('nombre');
        }else{
            $messages=[
                'ok'=> '¡Registro exitoso!'
            ];
            ///Registro del proyecto///

            $sql = "SELECT max(pro_id) as max_id FROM sep_proyecto";
            $respuesta = DB::select($sql);
            $contador=$respuesta[0]->max_id;
            $pro_id=$contador+1;       

                DB::table('sep_proyecto')->insert(array(
                    'pro_id' => $pro_id,
                    'pro_codigo' => $request->input('pro_codigo'),
                    'pro_nombre' => $request->input('pro_nombre'),
                    'pro_problema'=> $request->input('pro_problema'),
                    'pro_obj_general'=>$request->input('pro_obj_general'),
                    'pro_obj_especifico'=> $request->input('pro_obj_especifico'),
                ));

            
            //registro del producto//

            $conteo = count($request->get('nombre'));
            $arreglo = $request->get('nombre');
            // $sqlnumero = "SELECT max(prod_numero) as max_numero FROM sep_producto";
            // $respuestaNumero = DB::select($sqlnumero);
            // $contadorNumero=$respuestaNumero[0]->max_numero;
            // $prod_numero=$contadorNumero+1;
            
            $n=0;
            for ($i=1; $i <= $conteo; $i++) {  
                $nombres[$i]=$arreglo[$n];
                $n++;
                $sql2 = "SELECT max(prod_codigo) as max_codigo FROM sep_producto";
                $respuesta2 = DB::select($sql2);
                $contador2=$respuesta2[0]->max_codigo;
                $prod_codigo=$contador2+1;       

                DB::table('sep_producto')->insert(array(
                'prod_codigo' => $prod_codigo,
                'prod_nombre' => $nombres[$i],
                'prod_numero' =>  $i,
                'pro_id'=> $pro_id
                ));

            }
        }

        return view("Modules.Seguimiento.Proyecto.create", compact("messages","antiguos","prueba"));
    }
        
    public function getImportar(){
        return view("Modules.Seguimiento.Proyecto.importar");
    }

    public function postImportar(Request $request){
        // extract($_POST);
        $archivo = $request->file('archivo');

        if($archivo->getClientOriginalExtension()=="xlsx"){

            $filename =time() . "-" . $archivo->getClientOriginalName();
            $path = getPathUploads(). "/CSV/Proyecto";
            $archivo->move($path, $filename);
        
            $objReader = new \PHPExcel_Reader_Excel2007();
            // echo $path. "/" . $filename;
            $objPHPExcel = $objReader->load($path. "/" . $filename);
            // dd();
            $objPHPExcel->setActiveSheetIndex(0);

            $fila = 3;
            $codigo = $objPHPExcel->getActiveSheet()->getCell("A".$fila);
            
            $caracteresNoPermitidos = array('(',')','&gt;','&lt;','javascript','"','¬','&','{','}','^','°',"'"); 

            while(trim($codigo)!=''){
                $nombre = str_replace($caracteresNoPermitidos , '', (String) $objPHPExcel->getActiveSheet()->getCell("B".$fila));
                $problema = str_replace($caracteresNoPermitidos , '', (String) $objPHPExcel->getActiveSheet()->getCell("C".$fila));
                $Objgeneral = str_replace($caracteresNoPermitidos , '', (String) $objPHPExcel->getActiveSheet()->getCell("D".$fila));
                $Objespecifico = str_replace($caracteresNoPermitidos , '', (String) $objPHPExcel->getActiveSheet()->getCell("E".$fila));
                $ficha = str_replace($caracteresNoPermitidos , '', (String) $objPHPExcel->getActiveSheet()->getCell("F".$fila));
                $producto[1] = str_replace($caracteresNoPermitidos , '', (String) $objPHPExcel->getActiveSheet()->getCell("G".$fila));
                $producto[2] = str_replace($caracteresNoPermitidos , '', (String) $objPHPExcel->getActiveSheet()->getCell("H".$fila));
                $producto[3] = str_replace($caracteresNoPermitidos , '', (String) $objPHPExcel->getActiveSheet()->getCell("I".$fila));
                $producto[4] = str_replace($caracteresNoPermitidos , '', (String) $objPHPExcel->getActiveSheet()->getCell("J".$fila));
                $producto[5] = str_replace($caracteresNoPermitidos , '', (String) $objPHPExcel->getActiveSheet()->getCell("K".$fila));
                $producto[6] = str_replace($caracteresNoPermitidos , '', (String) $objPHPExcel->getActiveSheet()->getCell("L".$fila));

                $sql ="
                        INSERT INTO sep_proyecto(pro_id,pro_codigo,pro_nombre, pro_problema,
                        pro_obj_general, pro_obj_especifico)
                        VALUES(Null,$codigo,'$nombre','$problema', '$Objgeneral', '$Objespecifico')";
                DB::insert($sql);


                $pro_id = DB::getPdo()->lastInsertId();
                foreach($producto as $numero => $pro){
                    if(trim($pro)!=''){
                        $sql = "
                                INSERT INTO sep_producto(prod_codigo,prod_nombre,prod_numero,pro_id)
                                VALUES(Null,'$pro','$numero',$pro_id)";
                        DB::insert($sql);
                    }
                }
                
                
                $ficha = explode(',' , $ficha);
                foreach ($ficha as $fic) {
                    $sql =" update sep_ficha set fic_proyecto = '$codigo' 
                        where fic_numero = '$fic'";
                    $fichaActulizada = DB::update($sql);
                }
                // echo print_r($ficha) . "<br>";
                // echo $codigo . " " . $nombre." ".$problema." ".$Objgeneral." ".$Objespecifico." ".$producto1." ".$producto2." ".$producto3." ".$producto4." ".$producto5." ".$producto6."<br>";
                $fila++;
                $codigo = $objPHPExcel->getActiveSheet()->getCell("A".$fila);
                
            }
       
            $_SESSION['result']="<span class='text-success'>¡Carga masiva exitosa!</span>";
        }else{
            $_SESSION['result']="<span class='text-danger'>El archivo no cumple con el formato esperado</span>";
        }
     return redirect('seguimiento/proyecto/importar');
    }


    public function getEdit($pro_id){
        $proyectos =  DB::select("SELECT * FROM sep_proyecto as pro WHERE pro.pro_id=".$pro_id);
        $productos = DB::select("SELECT * FROM sep_producto as prod, sep_proyecto as pro where prod.pro_id=pro.pro_id and prod.pro_id=$pro_id");
        $cc=count($productos);
        return view("Modules.Seguimiento.Proyecto.update", compact("proyectos","productos","cc"));
    }

    public function postEdit(Request $request){
        $array = $request->get('nombre');
        $cont=0;

        for($z=0; $z<count($array); $z++){
            if($array[$z]==""){
                $cont++;
            }
        }

        $validar = Array(
            'pro_codigo' => 'required|numeric',
            'pro_nombre' => 'required',
            'pro_problema'=> 'required',
            'pro_obj_general'=> 'required',
            'pro_obj_especifico'=> 'required',
        );
    
        $messages = [
            'pro_codigo.required' => 'El campo c&oacute;digo del proyecto es obligatorio',
            'pro_nombre.required' => 'El campo nombre del proyecto es obligatorio',
            'pro_problema.required'=> 'El campo problema del proyecto es obligatorio',
            'pro_obj_general.required'=> 'El campo objetivo general del proyecto es obligatorio',
            'pro_obj_especifico.required'=> 'El campo objetivo especifico del proyecto es obligatorio',
        ];

        $validacion = Validator::make($_POST, $validar, $messages);

        if ($validacion->fails() || $cont>0) {
            $messages=[
                'mal'=> 'Uno o/u varios de los campos estan vacios'
            ];
            return redirect("seguimiento/proyecto/edit/".$request->get('pro_id'));
        }else{
            $messages=[
                'ok'=> 'Actualizacion exitosa!'
            ];
            //Actualizar campos del proyecto
            DB::table('sep_proyecto')
                ->where("pro_id",  $request->input('pro_id'))
                ->update(array(
                'pro_codigo' => $request->input('pro_codigo'),
                'pro_nombre' => $request->input('pro_nombre'),
                'pro_problema'=> $request->input('pro_problema'),
                'pro_obj_general'=>$request->input('pro_obj_general'),
                'pro_obj_especifico'=> $request->input('pro_obj_especifico'),
            ));

            //Registrar productos Nuevos//
            extract($_POST);

            $codigo=$request->input('codigo');
            $nombre=$request->input('nombre');

            $contador = count($codigo);
            for($y=0; $y<$contador; $y++){
                $validar_codigo = $codigo[$y];
                if($validar_codigo == ""){
                    $sqlnumero = "
                        select  count(prod_numero) as max_numero 
                        from    sep_producto 
                        where   pro_id=".$request->input('pro_id');
                    $respuestaNumero = DB::select($sqlnumero);
                    $contadorNumero=$respuestaNumero[0]->max_numero;
                    $prod_numero=$contadorNumero+1;
            
                    DB::table('sep_producto')->insert(array(
                        'prod_nombre' => $nombre[$y],
                        'prod_numero' => $prod_numero,
                        'pro_id'=> $request->input('pro_id')
                    ));
                
                }else{
                    DB::table('sep_producto')
                        ->where("prod_codigo", $validar_codigo)
                        ->update(array(
                        'prod_nombre' => $nombre[$y]
                    ));
                }
            }   

            //Eliminar los productos //
            $eliminar = explode(",", $request->get('control_quitar'));
            $contador_quitar = count($eliminar) - 1;
            if(isset($eliminar)){
               if($contador_quitar>0 && $contador>=1){
            
                    for ($x=0; $x < count($eliminar)-1; $x++) { 
                        $delete=$eliminar[$x];
                        $patron = '/[0-9]/';
                        if(preg_match($patron, $delete)){
                            DB::delete("DELETE FROM sep_producto WHERE prod_codigo=".$delete);  
                        }
                    }
                }
            }
        }
        return redirect("seguimiento/proyecto/index");
    }



    public function getConsultar($valor=false , $filtro=false){
        extract($_GET);
        $registroPorPagina = 10;
        $limit = $registroPorPagina ;
        if(isset($pagina)){
            $hubicacionPagina = $registroPorPagina*($pagina-1);
            $limit = $hubicacionPagina.','.$registroPorPagina;
        }else{
            $pagina = 1;
        }

        // Validar busqueda del seguimiento
        $fecha_actual = date('Y-m-d');
        $tabla="";
        $concatenar="";

        if($valor!=""){
            if($filtro==1){
                $concatenar=' and (fic.fic_numero like "%'.$valor.'%")';
            }else if($filtro==2){
                $tabla = " , sep_programa prog";
                $concatenar='
                and prog.prog_codigo = fic.prog_codigo
                and (prog.prog_nombre like "%'.$valor.'%")';
            }else if($filtro==3){
                $tabla = " , sep_participante par";
                $concatenar='
                and par.par_identificacion = fic.par_identificacion_coordinador
                and (par.par_nombres like "%'.$valor.'%" or par.par_apellidos like "%'.$valor.'%" or fic.par_identificacion_coordinador like "%'.$valor.'%")';
            }
        }

        // Consulta fichas de formación
        $sql = "
        SELECT prog.prog_nombre, par.par_nombres, par.par_identificacion, par.par_apellidos, 
        pla_fic.pla_fic_id, fic.fic_proyecto, pla_fic.fic_numero as fic_numero ,
        IF(fic.fic_observacion ='', 'No tiene observación',fic_observacion) as fic_observacion ,
        IF(pro_nombre IS NULL, 'Sin asignar un proyecto', pro_nombre) as pro_nombre,
        IF(fic.par_identificacion_coordinador IS NULL or fic.par_identificacion_coordinador='', 'Sin asignar coordinador', fic.par_identificacion_coordinador) as par_identificacion_coordinador
        FROM sep_ficha fic
        left join sep_proyecto pro on pro.pro_codigo= fic.fic_proyecto
        left join sep_planeacion_ficha pla_fic on fic.fic_numero=pla_fic.fic_numero
        left join sep_participante par on par.par_identificacion=fic.par_identificacion_coordinador
        left join sep_programa prog on prog.prog_codigo = fic.prog_codigo
        where fic.fic_numero not regexp '[a-z]'
        and pla_fic.pla_fic_fec_fin_lectiva >= '".$fecha_actual."' ".$concatenar." limit ".$limit;

        $sqlContador = "
        select  count(fic.fic_numero) as total
        from    sep_ficha as fic , sep_planeacion_ficha as pla ".$tabla."
        where   fic.fic_numero not regexp '[a-z]'
        and     pla.pla_fic_fec_fin_lectiva >=  '".$fecha_actual."'
        and     fic.fic_numero = pla.fic_numero ".$concatenar."";

        $planefichas = DB::select($sql);
        $proyectosContador = DB::select($sqlContador);

        // Paginado
        $contadorProyectos = $proyectosContador[0]->total;
        $cantidadPaginas = ceil($contadorProyectos/$registroPorPagina);
        $contador = (($pagina-1)*$registroPorPagina)+1;

        return view('Modules.Seguimiento.Proyecto.consultarSeguimiento', compact('planefichas','pla_fic_id','contadorProyectos','cantidadPaginas','contador','pagina','valor','filtro'));
    }

    public function postConsultar(Request $request){
        $fecha_actual=Date('Y-m-d');
        $filas="";
        $productos = array();
        $fichas=array();
        $proyectos =  DB::select("select * from sep_proyecto");
        //Se obtiene los productos del proyecto y las fichas vinculadas
        foreach ($proyectos as $pro){
            //Se obtiene los productos
            $sql = "select * from sep_producto where pro_id = $pro->pro_id";
            $producto = DB::select($sql);
            foreach ($producto as $val){
                $productos[$pro->pro_id][$val->prod_numero]=$val->prod_nombre;
            }
            for ($i=1; $i <=6; $i++) {
                if (!isset($productos[$pro->pro_id][$i])) {
                    $productos[$pro->pro_id][$i]="";
                }
            }
            //Se obtiene las fichas
            $list="";
            $sql= "select * from sep_ficha where fic_proyecto = $pro->pro_codigo";
            $ficha=DB::select($sql);
            if (count($ficha) > 0) {
                foreach ($ficha as $val) {
                    if (count($ficha) == 1) {
                        $list.=$val->fic_numero;
                    }else{
                        $list.=$val->fic_numero.",";
                    }
                }
            }
            $fichas[$pro->pro_id]=$list;
        }

        foreach ($proyectos as $val){
            $filas.="
            <tr>
            <td>".$val->pro_codigo."</td>
            <td>".utf8_decode($val->pro_nombre)."</td>
            <td>".utf8_decode($val->pro_problema)."</td>
            <td>".utf8_decode($val->pro_obj_general)."</td>
            <td>".utf8_decode($val->pro_obj_especifico)."</td>";
            for ($i=1; $i <=6; $i++) {
                $filas.="<td>".utf8_decode($productos[$val->pro_id][$i])."</td>";
            }
            $filas.="<td>".$fichas[$val->pro_id]."</td></tr>";
        }
        $tabla = '
        <style>
          table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            font-family:Arial;
          }
          #campos{ background:#e9791a; color:white; }
        </style>
        <h1>PROYECTOS FORMATIVOS DEL CDTI</h1>
        <table cellspacing="0" cellpadding="0">
        <tr id="campos"><th>C&oacute;digo Proyecto</th><th>Nombre</th>
        <th>Problema</th><th>Objetivo General</th>
        <th>Objectivo Espec&iacute;fico</th>';
        for ($i=1; $i <=6 ; $i++) {
            $tabla.="<th>Producto ".$i."</th>";
        }
        $tabla.="<th>Fichas asignadas</th></tr>";
        $tabla.=$filas."</table>";
        header('Content-type: application/vnd.ms-excel; charset=utf-8');
        header("Content-Disposition: attachment; filename=PROYECTOS_FORMATIVOS_CDTI.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $tabla;
    }
    
    public function getShow($pla_fic_id){
        
        //Datos de la ficha 
        $sql="select fic.fic_numero,fic.fic_observacion, fic.fic_fecha_seguimiento, 
        fic.fic_calificacion, prog.prog_nombre, niv.niv_for_nombre
        from sep_ficha as fic , sep_planeacion_ficha pla , sep_programa as prog , sep_nivel_formacion as niv
        where fic.fic_numero = pla.fic_numero and pla.pla_fic_id=$pla_fic_id 
        and prog.prog_codigo = fic.prog_codigo
        and prog.niv_for_id = niv.niv_for_id";
        $ficha=DB::select($sql);

        $fecha_vieja = $ficha[0]->fic_fecha_seguimiento; //Fecha de ultimo seguimiento
       
        //Equipo ejecutor tecnico de la ficha
        $sql="select par.par_identificacion, par.par_nombres, par.par_apellidos, par.par_correo, par.par_telefono, usu.estado, 
        IF(pla.par_id_instructor = par.par_identificacion, 'transversal','tecnico') as tipo_instructor
        from sep_participante par 
        left join sep_planeacion_ficha_detalle fic on fic.par_id_instructor = par.par_identificacion 
        left join sep_transversal_instructor pla on pla.par_id_instructor = par.par_identificacion 
        left join users usu on usu.par_identificacion = par.par_identificacion 
        WHERE fic.pla_fic_id=$pla_fic_id
        group by par.par_identificacion";
        $participantes=DB::select($sql);


        $fecha_actual = date('Y'). '-' . date('m'). '-' . date('d');

        //Numero del trimestre en el que esta la ficha
        $sql = "SELECT    pla_det.pla_trimestre_numero_ficha FROM sep_planeacion_ficha_detalle as pla_det
                where     pla_det.pla_fic_id = $pla_fic_id and pla_det.pla_fic_det_fec_fin > '".$fecha_actual."' and pla_det.pla_fic_det_fec_inicio < '".$fecha_actual."'
                group by  pla_det.pla_fic_id";
        $trimestre = DB::select($sql);

        if (count($trimestre) == 0) {
            $triNombre = "No se encuentra en un trimestre actual";
        }else{
            $triNombre = $trimestre[0]->pla_trimestre_numero_ficha;
            $sql="Select trimestre_numero,trimestre_tipo from sep_planeacion_ficha_trimestre 
            where pla_fic_id = $pla_fic_id and trimestre_numero=$triNombre";
            $fechas_tri=DB::select($sql);
            $triNombre = $fechas_tri[0]->trimestre_numero." - Etapa ".$fechas_tri[0]->trimestre_tipo;
        }


        //Productos del proyecto formativo
        $sql="select prod.prod_nombre 
        from   sep_producto prod, sep_proyecto pro, sep_ficha fic, sep_planeacion_ficha pla
        where  pla.pla_fic_id = $pla_fic_id 
        and    prod.pro_id = pro.pro_id
        and    fic.fic_proyecto = pro.pro_codigo
        and    pla.fic_numero = fic.fic_numero";     
        $productos=DB::select($sql);

        $limite= Count($productos); //conteo de cuantos productos tiene la ficha en su proyecto formativo
        $lista2 = "";
        $array3 = array();
        
        //Obtener la calificacion de los productos

        if ($ficha[0]->fic_calificacion == "") {
            //si no tiene ninguna calificacion
            for ($x=0; $x < $limite; $x++) { 
                $lista2 = $lista2 .",2";
            }
            $lista2 = substr($lista2 , 1);
            $array1 = explode("," , $lista2);
            $lista3 = $lista2;
        }else{
            //si ya tiene calificacion
            $lista3 = $ficha[0]->fic_calificacion;
            $array1 = explode("," , $lista3);
        }
        
        //Guardar la calificacion de los productos para enviarla a la interfaz
        for ($i=0; $i < count($array1); $i++) { 
           $array3[$i]= $array1[$i];
        }

        return view('Modules.Seguimiento.Proyecto.show', compact('participantes','productos','ficha','fecha_vieja','triNombre','limite','array3','lista3'));  
    }

    public function postShow(Request $request){
        
        $fecha_actual = date('Y'). '/' . date('m'). '/' . date('d');
        $evaluacion = substr($request->get('evaluacion') , 1);
        $calificacion = explode(",", $evaluacion);

        $lista="";
 
        for ($i=0; $i < count($calificacion); $i++) { 

            if($calificacion[$i] == ""){
                $lista = $lista .",2";
            }else{
                $lista = $lista .",". $calificacion[$i];
            }
        }
        $lista = substr($lista, 1);
        
        DB::table('sep_ficha')
        ->where("fic_numero", $request->input('ficha'))
        ->update(array(
            'fic_observacion' => $request->input('observacion'),
            'fic_fecha_seguimiento' => $fecha_actual,
            'fic_calificacion' => $lista
        ));

        return redirect('seguimiento/proyecto/consultar');
    
    }

    public function getLineas(){
        //Tabla 1
        $fecha_actual=Date('Y-m-d');
        $sql="select fic.par_identificacion_coordinador
         from sep_ficha fic
         left join sep_participante par on par.par_identificacion = fic.par_identificacion_coordinador
         where par.rol_id=3
         group by par.par_identificacion";
        $coordinadores=DB::select($sql);

        $sql="select * from sep_nivel_formacion
        where niv_for_id !=3 and niv_for_id!=6 and niv_for_id!=5
        group by niv_for_nombre
        order by niv_for_id desc";
        $nivel=DB::select($sql);

        $fila=0;
         foreach($coordinadores as $cor){
            $columna=0;
            foreach ($nivel as $n) {
                $sql="select niv.niv_for_id , pro.pro_codigo , pro.pro_nombre
                    from sep_proyecto as pro , sep_ficha as fic
                    left join sep_planeacion_ficha pla on pla.fic_numero = fic.fic_numero
                    left join sep_programa prog on prog.prog_codigo = fic.prog_codigo
                    left join sep_nivel_formacion niv on niv.niv_for_id = prog.niv_for_id
                    where fic.par_identificacion_coordinador = $cor->par_identificacion_coordinador
                    and pro.pro_codigo = fic.fic_proyecto
                    and pla.fic_numero not regexp '[a-z]'
                    and niv.niv_for_id = $n->niv_for_id
                    GROUP by pro.pro_codigo";
                $resultado=DB::select($sql);

                $vec[$fila][$columna]=count($resultado);
                $columna++;
            }
            $fila++;
         }
        //Nombres de nivel de formacion
        $nivel_formacion=array();
        foreach ($nivel as $key=>$val) {
           $nivel_formacion[$key]=$val->niv_for_nombre;
        }
        //Suma de columnas
        $total=array();
        for ($i=0; $i <= (count($vec)-1); $i++) {
            $acumulado = 0;
            for ($j=0; $j < (count($vec[$i])); $j++) {
                $acumulado=$acumulado+$vec[$i][$j];
            }
            $total[$i]=$acumulado;
        }
       return view('Modules.Seguimiento.Proyecto.lineas', compact("vec","nivel_formacion","total"));
    }

    public function getDetallelineas(){
        extract($_GET);
        $fecha_actual=Date('Y-m-d');
        $vista=true;
        if(!isset($nivel)){
          $nivel = "4";
        }else{
            $vista=false;
        }
        //Proyectos por coordinador
        if($opt == "1"){
            $cedula = 16266427;
        }
        if($opt == "2"){
            $cedula = 1113513080;
        }
        if($opt == "3"){
            $cedula = 30310315;
        }
        if($opt == "4"){
            $cedula = 67020609;
        }
        $sql="select * from sep_participante where par_identificacion = $cedula";
        $usu = DB::select($sql);
        $coordinador = $usu[0]->par_nombres." ".$usu[0]->par_apellidos;

        $sql="select niv.niv_for_id , pro.pro_codigo , pro.pro_nombre
        from sep_proyecto as pro , sep_ficha as fic
        left join sep_planeacion_ficha pla on pla.fic_numero = fic.fic_numero
        left join sep_programa prog on prog.prog_codigo = fic.prog_codigo
        left join sep_nivel_formacion niv on niv.niv_for_id = prog.niv_for_id
        where fic.par_identificacion_coordinador = $cedula
        and pro.pro_codigo = fic.fic_proyecto
        and pla.fic_numero not regexp '[a-z]'
        and niv.niv_for_id = $nivel
        GROUP by pro.pro_codigo";
        $proyectos_nivel=DB::select($sql);

        $sql = "
        select fic.fic_proyecto
        from sep_ficha as fic
        left join sep_planeacion_ficha pla on pla.fic_numero = fic.fic_numero
        left join sep_programa prog on prog.prog_codigo = fic.prog_codigo
        left join sep_nivel_formacion niv on niv.niv_for_id = prog.niv_for_id
        left join sep_proyecto pro on pro.pro_codigo= fic.fic_proyecto
        where par_identificacion_coordinador = $cedula
        and pla.pla_fic_fec_fin_lectiva >= '".$fecha_actual."'
        and pla.fic_numero not regexp '[a-z]'";
        $fichas = DB::select($sql);
        if ($vista) {
            return view('Modules.Seguimiento.Proyecto.showLineas', compact('cedula','opt','proyectos_nivel','coordinador','fichas'));
        }else{
            $contador=1;
            $tabla="";
            foreach($proyectos_nivel as $pro){
                $tabla = $tabla."<tr>
                    <td>".($contador++)."</td>
                    <td>".$pro->pro_codigo."</td>
                    <td>".$pro->pro_nombre."</td>";
                    $c=0;
                    foreach($fichas as $fic){
                        if($fic->fic_proyecto == $pro->pro_codigo){
                            $c++;
                        }
                    }
                    if($c == 0){
                        $tabla.="<td class='text-center'>0</td>";
                    }else{
                        $tabla.='<td class="text-center"><a id="lin" data-url="'.url("seguimiento/proyecto/showlineastec").'" coordinador ="'.$cedula.'" data-proyecto="'.$pro->pro_codigo.'" data-toggle="modal" data-target="#modalLineasTec" class="ajax-link" title="Ver" style="cursor: pointer;">'.$c.'</a></td>';
                    }
                $tabla.="</tr>";
            }
            return $tabla;
        }
    }

    public function getShowlineastec($ids=false , $campo=false){
        extract($_GET);
        $fecha_actual=Date('Y-m-d');
        $registroPorPagina = 10;

        $limit = $registroPorPagina ;
        if(isset($pagina)){
            $hubicacionPagina = $registroPorPagina*($pagina-1);
            $limit =$hubicacionPagina.','.$registroPorPagina;
        }else{
            $pagina = 1;
        }
        $sql = "
        select fic.fic_numero , prog.prog_nombre
        from sep_ficha as fic
        left join sep_programa prog on prog.prog_codigo = fic.prog_codigo
        left join sep_planeacion_ficha pla on pla.fic_numero = fic.fic_numero
        where par_identificacion_coordinador = $cedula
        and fic.fic_proyecto = $proyecto
        and fic.fic_numero not regexp '[a-z]'
        and pla.pla_fic_fec_fin_lectiva >= '".$fecha_actual."'";
        $fichasVinculadas= DB::select($sql);

        // Paginación del modal
        $sqlContador = "
            select count(fic.fic_numero) as total
            from sep_ficha as fic
            left join sep_programa prog on prog.prog_codigo = fic.prog_codigo
            left join sep_planeacion_ficha pla on pla.fic_numero = fic.fic_numero
            where par_identificacion_coordinador = $cedula
            and fic.fic_proyecto = $proyecto
            and fic.fic_numero not regexp '[a-z]'
            and pla.pla_fic_fec_fin_lectiva >= '".$fecha_actual."'";
        $ProgramasContador = DB::select($sqlContador);
        $contadorProgramas = $ProgramasContador[0]->total;
        $cantidadPaginas = ceil($contadorProgramas/$registroPorPagina);
        $contador = (($pagina-1)*$registroPorPagina)+1;
    
        return view('Modules.Seguimiento.Proyecto.modalLineasTec', compact('fichasVinculadas','contadorProgramas','cantidadPaginas','contador','pagina','proyecto','cedula'));
    }
}