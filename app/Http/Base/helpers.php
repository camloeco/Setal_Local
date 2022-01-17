<?php
function actualizarBenedicioSena(){
    $fecha_actual = date('Y-m-d');
    $sql="update sep_beneficios_sena_aprendiz set estado = 3 where fecha_fin < '".$fecha_actual."'";
    DB::update($sql);
}
function validarDatosContratos($path, $filename) {
    $objReader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $objReader->load($path . "/" . $filename);
    $objPHPExcel->setActiveSheetIndex(0);

    $fila = 2;
    $contadorErrores = 0;
    $datos = array();
    $mensajes = array();
    $registro = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);

    // Validaciones de los campos
    while(trim($registro) != "") {
        $con_num_contrato = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
        $con_objeto = (String) $objPHPExcel->getActiveSheet()->getCell('B' . $fila);
        $con_obligaciones = (String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila);
        $con_horas = (String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila);
        $con_anio_contrato = (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila);
        $con_fec_inicio = (String) $objPHPExcel->getActiveSheet()->getCell('F' . $fila)->getFormattedValue();
        $con_fec_fin = (String) $objPHPExcel->getActiveSheet()->getCell('G' . $fila)->getFormattedValue();
        $par_identificacion = (String) $objPHPExcel->getActiveSheet()->getCell('H' . $fila);
        //$ciudad_expedicion = (String) $objPHPExcel->getActiveSheet()->getCell('I' . $fila);
        $ciudad_expedicion = "";
        $nombre = (String) $objPHPExcel->getActiveSheet()->getCell('J' . $fila);
        $apellido = (String) $objPHPExcel->getActiveSheet()->getCell('K' . $fila);
        $con_val_total = (String) $objPHPExcel->getActiveSheet()->getCell('L' . $fila);
        
        $errorFila = 0;
        if($con_objeto == ''){ $mensajes['errores'][$fila][] = 'El campo objeto está vacio'; $errorFila++; }
        if($con_obligaciones == ''){ $mensajes['errores'][$fila][] = 'El campo obligaciones está vacio'; $errorFila++; }
        if($con_horas == ''){ $mensajes['errores'][$fila][] = 'El campo horas está vacio'; $errorFila++; }
        if($con_anio_contrato == ''){ $mensajes['errores'][$fila][] = 'El campo año está vacio'; $errorFila++; }
        if($con_fec_inicio == ''){ $mensajes['errores'][$fila][] = 'El campo fecha inicio está vacio'; $errorFila++; }
        if($con_fec_fin == ''){ $mensajes['errores'][$fila][] = 'El campo fecha fin está vacio'; $errorFila++; }
        if($par_identificacion == ''){ $mensajes['errores'][$fila][] = 'El campo cédula está vacio'; $errorFila++; }
        //if($ciudad_expedicion == ''){ $mensajes['errores'][$fila][] = 'El campo cédula municipio de expedición está vacio'; $errorFila++; }
        if($nombre == ''){ $mensajes['errores'][$fila][] = 'El campo nombre está vacio'; $errorFila++; }
        if($apellido == ''){ $mensajes['errores'][$fila][] = 'El campo apellido está vacio'; $errorFila++; }
        if($con_val_total == ''){ $mensajes['errores'][$fila][] = 'El campo valor total está vacio'; $errorFila++; }
        
        if($errorFila == 0){
            $sql = 'select par_identificacion from sep_participante where par_identificacion = "'.$par_identificacion.'" limit 1';
            $validarExistencia = DB::select($sql);
            if(count($validarExistencia) == 0){
                crearUsuario($par_identificacion, $nombre, $apellido);
            }
            $datos['con_num_contrato'][] = $con_num_contrato;
            $datos['con_objeto'][] = $con_objeto;
            $datos['con_obligaciones'][] = $con_obligaciones;
            $datos['con_horas'][] = $con_horas;
            $datos['con_anio_contrato'][] = $con_anio_contrato;
            $datos['con_fec_inicio'][] = formatoFecha($con_fec_inicio);
            $datos['con_fec_fin'][] = formatoFecha($con_fec_fin);
            $datos['par_id_instructor'][] = $par_identificacion;
            $datos['ciudad_expedicion'][] = $ciudad_expedicion;
            $datos['nombre'][] = $nombre;
            $datos['apellido'][] = $apellido;
            $datos['con_val_total'][] = $con_val_total;
        }else{
            $contadorErrores++;
        }
        
        $fila++;
        $registro = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
    }
    
    $mensajes['exito'] = 0;
    $mensajes['registrosRevisados'] = $fila-=2;
    $mensajes['registrosNuevos'] = 0;
    $mensajes['registrosActualizados'] = 0;
    $mensajes['contadorErrores'] = $contadorErrores;
    //Validar si existe la obligación y el registro del contrato
    foreach($datos['con_num_contrato'] as $key => $val){
        $sql = 'select obli_id from sep_obligaciones where obli_descripcion = "'.$datos['con_obligaciones'][$key].'" limit 1';
        $validarObligaciones = DB::select($sql);
        if(count($validarObligaciones)>0){
            $obli_id = $validarObligaciones[0]->obli_id;
        }else{
            $sql = 'insert into sep_obligaciones (obli_id,obli_descripcion) values (default,"'.ucfirst(mb_strtolower($datos['con_obligaciones'][$key])).'")';
            DB::insert($sql);
            $obli_id = DB::getPdo()->lastInsertId();
        }

        $sql = '
            select  con_id from sep_contrato 
            where   con_num_contrato = "'.$val.'"
            and     par_id_instructor = "'.$datos['par_id_instructor'][$key].'"
            and     con_anio_contrato = "'.$datos['con_anio_contrato'][$key].'" limit 1';
        $validarContrato = DB::select($sql);
        if(count($validarContrato)>0){
            $sql = '
                update  sep_contrato
                set     con_objeto = "'.$datos['con_objeto'][$key].'", obli_id = '.$obli_id.', con_horas = '.$datos['con_horas'][$key].',
                        ciudad_expedicion = "'.$datos['ciudad_expedicion'][$key].'", con_fec_inicio = "'.$datos['con_fec_inicio'][$key].'",
                        con_fec_fin = "'.$datos['con_fec_fin'][$key].'", con_val_total = '.$datos['con_val_total'][$key].'
                where   par_id_instructor = "'.$datos['par_id_instructor'][$key].'" and con_anio_contrato = "'.$datos['con_anio_contrato'][$key].'" 
                and     con_num_contrato = "'.$val.'"';
            DB::update($sql);
            $mensajes['registrosActualizados']++;
        }else{
            $sql = '
                insert into sep_contrato
                (con_id, con_num_contrato, con_objeto, obli_id, con_horas, con_anio_contrato,
                con_fec_inicio, con_fec_fin, par_id_instructor, ciudad_expedicion, con_val_total)
                values
                (default, "'.$val.'", "'.ucfirst(mb_strtolower($datos['con_objeto'][$key])).'", '.$obli_id.', '.$datos['con_horas'][$key].', "'.$datos['con_anio_contrato'][$key].'",
                "'.$datos['con_fec_inicio'][$key].'", "'.$datos['con_fec_fin'][$key].'", "'.$datos['par_id_instructor'][$key].'", "'.$datos['ciudad_expedicion'][$key].'",
                '.$datos['con_val_total'][$key].')';
            DB::insert($sql);
            $mensajes['registrosNuevos']++;
        }
        $mensajes['exito']++;
    }

    return $mensajes;
}
function validarBeneficiariosSena($path, $filename) {
    $objReader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $objReader->load($path . "/" . $filename);
    $objPHPExcel->setActiveSheetIndex(0);

    $fila = 3;
    $contadorErrores = 0;
    $datos = array();
    $mensajes = array();
    $registro = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);

    $mensajes['exito'] = 0;
    $mensajes['registrosNuevos'] = 0;
    $mensajes['contadorErrores'] = $contadorErrores;

    // Validaciones de los campos
    while(trim($registro) != "") {
        $par_identificacion = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
        $beneficio = (String) $objPHPExcel->getActiveSheet()->getCell('B' . $fila);
        $fecha_inicio = (String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila)->getFormattedValue();
        $fecha_fin = (String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila)->getFormattedValue();
        $errorFila = 0;
        if($par_identificacion == ''){ $mensajes['errores'][$fila][] = 'El campo cédula está vacio'; $errorFila++; }
        if($beneficio == ''){
            $mensajes['errores'][$fila][] = 'El campo beneficio está vacio'; $errorFila++; 
        }else{
            $array = explode(' ',$beneficio);
            $beneficio = $array[0];
        }
        if($fecha_inicio == ''){ $mensajes['errores'][$fila][] = 'El campo fecha inicio está vacio'; $errorFila++; }
        if($fecha_fin == ''){ $mensajes['errores'][$fila][] = 'El campo fecha fin está vacio'; $errorFila++; }
        if($errorFila == 0){
            $sql = '
                select par_identificacion from sep_beneficios_sena_aprendiz
                where  par_identificacion = "'.$par_identificacion.'"
                and    beneficio_sena_id = '.$beneficio.'
                and    fecha_inicio = "'.$fecha_inicio.'"
                and    fecha_fin = "'.$fecha_fin.'" limit 1';
            $validarExistencia = DB::select($sql);
            if(count($validarExistencia) == 0){
                $fecha_inicio = formatoFecha($fecha_inicio);
                $fecha_fin = formatoFecha($fecha_fin);
                $sql = '
                insert into sep_beneficios_sena_aprendiz
                (id, par_identificacion, beneficio_sena_id, fecha_inicio, fecha_fin, observacion, estado)
                values
                (default, "'.$par_identificacion.'",'.$beneficio.',"'.$fecha_inicio.'","'.$fecha_fin.'",null, 1)';
                //dd($sql);
                DB::insert($sql);
                $mensajes['registrosNuevos']++;
                $mensajes['exito']++;
            }
        }else{
            $contadorErrores++;
        }
        $fila++;
        $registro = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
    }
    $mensajes['registrosRevisados'] = $fila-=3;

    return $mensajes;
}
function crearUsuario($numero_documento, $nombres, $apellidos){
    $randoCorreo = rand(0,3);
    $correoAleatorio = array('gmail','sena','misena','hotmail');
    $tiempo = time();
    $correo = $tiempo.'@'.$correoAleatorio[$randoCorreo];
    
    $sep_participante = "
        insert into sep_participante 
        values  ('$numero_documento','$numero_documento', '".ucwords(mb_strtolower($nombres, 'UTF-8'))."', '".ucwords(mb_strtolower($apellidos, 'UTF-8'))."', '', '11111', '$correo', 2, 1, default, default, null)";
    DB::insert($sep_participante);

    $clave = \Hash::make($numero_documento);
    $users = "
        insert into users 
        values  (default, '$numero_documento', '$correo', '$clave', 'na', '1', null, default, default)";
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

function getGravatar($user, $size = false) {
    
    $genero = ((is_array($user)) ? $user[0]['gender'] : $user->gender);
    $email = ((is_array($user)) ? $user[0]['email'] : $user->email);
    $rol = ((is_array($user)) ? $user[0]['rol_id'] : $user->rol_id);
    
    if(!$rol && !is_array($user)){
        $rol = (isset($user->participante->rol_id))?$user->participante->rol_id:"0";
    }
    
    $roles = array(0,1,2,3,4);
    
    if ($genero == "female" && in_array($rol, $roles)) {
        return asset("img/avatar/female-".$rol.".png");
    } elseif($genero == "male" && in_array($rol, $roles)) {
        return asset("img/avatar/male-".$rol.".png");
    } else {
        return asset("img/avatar/na.png");
    }

    //return "http://www.gravatar.com/avatar/";
    $hash = md5($email);
    $uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
    $headers = @get_headers($uri);
    if (!preg_match("|200|", $headers[0])) {
        $has_valid_avatar = false;
    } else {
        $has_valid_avatar = true;
    }

    if ($has_valid_avatar) {
        return "http://www.gravatar.com/avatar/"
                . md5(strtolower(trim($email)))
                . "?d=" . urlencode("img/noavatar.jpg") . "&s=" . $size;
    } else {
        return "http://www.gravatar.com/avatar/";
    }

}

// getGravatar

function getHeaderMod($modulo, $funcionalidad, $breadcrumb = false) {
    $header = '
        <div class="row">
            <div class="col-xs-12" id="breadcrumb">
                <a class="show-sidebar" href="#">
                    <!-- <i class="glyphicon glyphicon-align-justify"></i> -->
                    <i><img style="width:16px;"  src="'. asset('img/lineas.png') .'"></i>
                </a>
                <ol class="breadcrumb pull-left">';

    $header .= '<li><a class="hidden-xs" href="' . url('/') . '">Panel de Administraci&oacute;n</a></li>';

    if ($breadcrumb) {
        foreach ($breadcrumb as $bread) {
            $header .= '<li>
            <a href="' . $bread[0] . '" class="ajax-link"><i class="fa fa-' . $bread[1] . '"></i>' . $bread[2] . '</a></li>';
        }
    } // if

    /*$redesSociales = '<div class="pull-right" id="social">
			<a href="#"><i class="fa fa-google-plus"></i></a>
			<a href="#"><i class="fa fa-facebook"></i></a>
			<a href="#"><i class="fa fa-twitter"></i></a>
			<a href="#"><i class="fa fa-linkedin"></i></a>
			<a href="#"><i class="fa fa-youtube"></i></a>
		</div>';
    $redesSociales = "";
    $header .= $redesSociales . '</div>
        </div>';*/
    $header .= '</div>
        </div>';

    $header .= '
        <div class="row" id="dashboard-header">
            <div class="col-xs-12 col-sm-4 col-md-10"></div>
        </div>';

    return $header;
}

// getHeaderMod

function getPathUploads() {

    $rutaMedia = str_replace("\public", "", public_path());
    $rutaMedia = str_replace("/public", "", $rutaMedia);
$rutaMedia = str_replace("cdtiapps_html", "cdtiapps/public_html", $rutaMedia);
    return $rutaMedia . "/resources/uploads";

}

// getPathUploads

function convertCsvToArray($path, $filename) {

    $archivo = $path . "/" . $filename;

    $file = file($archivo);

    foreach ($file as $key => $fileA) {
        $file[$key] = str_replace(",", "$/$", $fileA);
    } // foreach
    // Transforma el CSV en Array
    $rows = array_map('str_getcsv', $file);

    // Se elimina la cabecera del archivo CSV
    unset($rows[0]);

    // Creacion del arreglo CSV para su manipulacion 
    $csv = array();
    foreach ($rows as $row) {
        $row[0] = str_replace("$/$", ",", $row[0]);
        $csv[] = explode(";", $row[0]);
    }

    // Eliminar archivo temporal
    unlink($archivo);

    return $csv;

}

// importCsv

function getEstilosAprendizaje() {
    return array('INDEFINIDO', 'DIVERGENTE', 'CONVERGENTE','ASIMILADOR','ACOMODADOR');

}

function getTipoComite() {
    return array('ORDINARIO', 'EXTRAORDINARIO');

}

function getTipoFaltas() {
    return array('ACADEMICA', 'DISCIPLINARIA', 'ACADEMICA / DISCIPLINARIA');

}

function getEstadoEdu() {
    return array('PENDIENTE', 'APROBADO', 'RECHAZADO', 'PROGRAMADO', 'FINALIZADO');

}

function getEstados() {
    return array('CANCELADO', 'EN FORMACION', 'POR CERTIFICAR', 'CERTIFICADO',
        'RETIRO VOLUNTARIO', 'CONDICIONADO', 'TRASLADADO', 'APLAZADO', 'PARA CANCELAR',
        'INDUCCION');

}

function getCargos() {
    return array('APRENDIZ', 'INSTRUCTOR');

}

function getFases() {
    return array('ANALISIS', 'PLANEACION', 'EJECUCION', 'EVALUACION');

}

function getOpcionEtapa() {
    return array("CONTRATO DE APRENDIZAJE",
        "VINCULACION LABORAL O CONTRACTUAL",
        "PARTICIPACION EN UN PROYECTO PRODUCTIVO", //  EN SENA EMPRESA O EN SENA PROVEEDOR SENA  O EN PRODUCCIÓN DE CENTROS
        "APOYO A UNA UNIDAD PRODUCTIVA FAMILIAR",
        "MONITORIAS",
        "APOYO A UNA INSTITUCIÓN ESTATAL", // , NACIONAL, TERRITORIAL, O A UNA ONG O A UNA ENTIDAD SIN ÁNIMO DE LUCRO
        "PASANTÍAS");

}

function acciones($ver, $editar, $eliminar, $otros = false) {

    ob_start();

    ?>
    <td data-title="Ver">
        <a class="ajax-link modal-ajax" data-titulo="Informaci&oacute;n detallada" href="<?php echo $ver ?>" title="Ver">
            <i class="fa fa-eye fa-2x" style="float:left; margin-right: 5px; margin-top:10px;"></i>
        </a>
    </td>
    <td data-title="Editar">
        <a href="<?php echo $editar ?>" class="ajax-link" title="Editar informaci&oacute;n">
            <i class="fa fa-edit fa-2x" style="float:left; margin-right: 5px; margin-top:10px;"></i>
        </a>
    </td>
    <td data-title="Eliminar">
        <a href="<?php echo $eliminar ?>" class="ajax-link" title="Eliminar el registro">
            <i class="fa fa-remove fa-2x" style="float:left; margin-right: 5px; margin-top:10px;"></i>
        </a>
    </td>
    <?php

    if ($otros) {
        foreach ($otros as $otro) {

            ?>
            <td data-title="<?php echo $otro[0] ?>">
                <a href="<?php echo $otro[1] ?>" class="ajax-link" title="<?php echo $otro[0] ?>">
                    <i class="fa <?php echo $otro[2] ?> fa-2x" style="float:left; margin-right: 5px; margin-top:10px;"></i> 
                </a>
            </td>
            <?php

        }
    } // foreach

    $contenido = ob_get_clean();

    return $contenido;

}

function leerExcel($path, $filename, $hoja) {

    $objReader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $objReader->load($path . "/" . $filename);

    // Asignar hoja de calculo activa
    $objPHPExcel->setActiveSheetIndex($hoja);

    $fila = 15;
    $count = 0;


    $planeacion[0]['codigo'] = (String) $objPHPExcel->getActiveSheet()->getCell('I6')->getCalculatedValue();
    $planeacion[0]['programa'] = (String) $objPHPExcel->getActiveSheet()->getCell('B6')->getCalculatedValue();
    
    $actividad = (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila);

    $resultado = $objPHPExcel->getActiveSheet()->getCell('D' . $fila);
    //$planeacion["$actividad"][$count]['resultado'] = (String)$resultado;

    //Revisa si existe un resultado siguiente, hay uno por cada fila siempre
    while (trim($resultado) != "") {
        //averigua si existe una actividad en la fila y la carga
        if (trim((String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila)) != "") {

            $competencia = (String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila);
            //Averigua si hay una competencia en la fila y la captura
            if (trim((String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila)) != "") {
                $actividad = (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila);
            } 
                $resultado2 = (String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila);
                $planeacion["$competencia"]["$actividad"][$count]['resultado'] = $resultado2;

                $resultado2 = (String) $objPHPExcel->getActiveSheet()->getCell('F' . $fila);
                $planeacion["$competencia"]["$actividad"][$count]['duracion'] = $resultado2;

        } else {

            if (trim((String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila)) != "") {
                $actividad = (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila);
            } 
            $resultado2 = (String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila);
            $planeacion["$competencia"]["$actividad"][$count]['resultado'] = $resultado2;

            $resultado2 = (String) $objPHPExcel->getActiveSheet()->getCell('F' . $fila);
            $planeacion["$competencia"]["$actividad"][$count]['duracion'] = $resultado2;
        } // if

        $fila++;
        $count++;
        $resultado = (String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila);
    } // while
    // if($hoja==5) die(print_r($planeacion));
    
    return $planeacion;

}

function leerExcel1($path, $filename, $hoja) {

    $objReader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $objReader->load($path . "/" . $filename);

    // Asignar hoja de calculo activa
    $objPHPExcel->setActiveSheetIndex($hoja);

    $fila = 15;
    $count = 0;


    $planeacion[0]['codigo'] = (String) $objPHPExcel->getActiveSheet()->getCell('I6')->getCalculatedValue();
    $planeacion[0]['programa'] = (String) $objPHPExcel->getActiveSheet()->getCell('B6')->getCalculatedValue();
    
    $actividad = (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila);

    $resultado = $objPHPExcel->getActiveSheet()->getCell('D' . $fila);
    $competencia = $objPHPExcel->getActiveSheet()->getCell('C' . $fila);
    $duracion = $objPHPExcel->getActiveSheet()->getCell('F' . $fila);
    //$planeacion["$actividad"][$count]['resultado'] = (String)$resultado;

    //Revisa si existe un resultado siguiente, hay uno por cada fila siempre
    while (trim($resultado) != "" || trim($actividad) != "" ) {
        //averigua si existe una Competencia en la fila y la carga
        if (trim((String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila)) != "") {
            $competencia = (String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila);
            //Averigua si hay una competencia en la fila y la captura
        }
        if (trim((String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila)) != "") {
            $actividad = (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila);
            $duracion = (String) $objPHPExcel->getActiveSheet()->getCell('F' . $fila);
            //Averigua si hay una actividad en la fila y la captura
        }
        if (trim((String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila)) != "") {
            $resultado = (String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila);
            //Averigua si hay una actividad en la fila y la captura
        }
        
        $planeacion["$competencia"]["$actividad"][$count]['resultado'] = $resultado;

        //$planeacion["$competencia"]["$actividad"][$count]['duracion'] = $duracion;
        $planeacion["$competencia"]["$actividad"]['duracion'] = $duracion;

          // if

        $fila++;
        $count++;
        $res1 = (String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila);
        $act1 = (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila);
        if(trim($res1) == "" && trim($act1) == "" ){
            $resultado = (String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila);
            $actividad = (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila);
        }
    } // while
    // if($hoja==5) die(print_r($planeacion));
    
    return $planeacion;

}

function leerExcelHorario($path, $filename) {
    $horario = array();

    // Leer archivo de excel
    $objReader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $objReader->load($path . "/" . $filename);
    $objPHPExcel->setActiveSheetIndex(0);

    $fila = 14;
    $registro = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
    $validarInstructores = array();
    $validarAmbientes = array();
    $horario['horas_programa'] = 0;
    
    $sql="select par.par_identificacion, par.par_nombres, par.par_apellidos, usu.estado , par.rol_id
    from sep_participante par 
    left join users usu on usu.par_identificacion = par.par_identificacion
    and usu.estado = usu.estado
    and par.rol_id= '2'";
    $Existente = DB::select($sql);
    $estado =0;
    
    while(trim($registro) != "") {
        $fase = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
        $competencia = (String) $objPHPExcel->getActiveSheet()->getCell('B' . $fila);
        $resultado = (String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila);
        $actividad = (String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila);
        $horas_presenciales = (String) $objPHPExcel->getActiveSheet()->getCell('F' . $fila)->getCalculatedValue();
        $instructor = (String) $objPHPExcel->getActiveSheet()->getCell('H' . $fila);

        $codigoAmbiente = (String) $objPHPExcel->getActiveSheet()->getCell('J' . $fila);
		$codigo = substr($codigoAmbiente,0,6);
        $array = explode(' ',$codigo);
        $ambiente = $array[0];

        foreach ($Existente as $key) {
            
            if($key->par_identificacion == $instructor && $key->estado==0){
                $nom_ape=$key->par_nombres ." ".$key->par_apellidos;
                $estado++;
            }
            
        }
        
        if($ambiente == ''){
            $horario['errores']['ambiente'][] = 'El <strong>ambiente</strong> en la fila <strong>'.$fila.'</strong> está vacio.';
        }

        if($competencia == ''){
            $horario['errores']['competencia'][] = 'La <strong>competencia</strong> en la fila <strong>'.$fila.'</strong> está vacio.';
        }

        if($resultado == ''){
            $horario['errores']['resultado'][] = 'El <strong>resultado</strong> en la fila <strong>'.$fila.'</strong> está vacio.';
        }

        if($actividad == ''){
            $horario['errores']['actividad'][] = 'La <strong>actividad</strong> en la fila <strong>'.$fila.'</strong> está vacio.';
        }

        if($horas_presenciales == ''){
            $horario['errores']['horas_presenciales'][] = 'La <strong>horas presenciales</strong> en la fila <strong>'.$fila.'</strong> está vacio.';
        }

        if($instructor == ''){
            $horario['errores']['instructor'][] = 'El <strong>Instructor</strong> en la fila <strong>'.$fila.'</strong> está vacio.';
        }
        
        if($estado == 1){
            $horario['errores']['instructor'][] = 'Cambie al instructor <strong>'.$nom_ape.'</strong> en el <strong>plan de trabajo</strong>, por que ya no se encuentra trabajando en el CDTI';
            $estado=0;
        }

        $horario['fas_id'][] = $fase;
        $horario['com_descripcion'][] = $competencia;
        $horario['res_descripcion'][] = $resultado;
        $horario['act_descripcion'][] = $actividad;
        $horario['pla_can_hor_presenciales'][] = $horas_presenciales;
        $horario['horas_programa'] += $horas_presenciales;
        $horario['par_id_instructor'][] = $instructor;
        $horario['amb_id'][] = $ambiente;

        $fila++;
        $registro = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
    }

    if(!isset($horario['fas_id'])){
        $horario['errores']['fase'][] = 'La columna fase no debe tener celdas vacias.';
        return $horario;
    }else{
        if(count($horario['fas_id']) <= 5){
        $horario['errores']['fase'][] = 'El documento debe tener celdas combinadas o tiene menos de 6 filas';
        return $horario;
        }
    }

    // Instructor
    if(!isset($horario['errores']['instructor'])){
        $validarInstructor = array_unique($horario['par_id_instructor']);
        $concatenarInstructor = implode("','", $validarInstructor);
        $concatenarInstructor = "'".$concatenarInstructor."'";
        $sql = 'select par_identificacion, par_nombres, par_horas_semanales from sep_participante where par_identificacion in('.$concatenarInstructor.')';
        $consultaInstructor = DB::select($sql);
        if(count($consultaInstructor)==0){
            $horario['errores']['instructorCC'][] = 'Los valores en la columna instructor cédula no existen en nuestra base de datos o están vacias.';
        }else{
            $instructorSeleccionado = array();
            foreach($consultaInstructor as $key => $val){
                $instructorSeleccionado['par_identificacion'][] = $val->par_identificacion;
                if($val->par_horas_semanales == 0 or $val->par_horas_semanales == null){
                    $horario['errores']['instructorHora'][] = 'El instructor <strong>'.$val->par_nombres.'</strong> tiene 0 horas asignadas, por favor modificar.';
                }
            }

            $resultado = array_diff($validarInstructor, $instructorSeleccionado['par_identificacion']);
            if(count($resultado)>0){
                foreach($resultado as $key => $val){
                    $horario['errores']['instructorCC'][] = 'El número de identificación <strong>'.$val.'</strong> no existe en nuestra base de datos.';
                }
            }
        }
    }

    // Ambiente
    if(!isset($horario['errores']['ambiente'])){
        $validarAmbiente = array_unique($horario['amb_id']);
        $concatenarAmbiente = implode(',', $validarAmbiente);
        $consultaAmbiente = DB::select('select pla_amb_id, pla_amb_descripcion, pla_amb_estado from sep_planeacion_ambiente where pla_amb_id in('.$concatenarAmbiente.')');
        if(count($consultaAmbiente)==0){
            $horario['errores']['ambiente'][] = 'Los valores en la columna ambiente no existen en nuestra base de datos o están vacias.';
        }else{
            $ambienteSeleccionado = array();
            foreach($consultaAmbiente as $key => $val){
                $ambienteSeleccionado['pla_amb_id'][] = $val->pla_amb_id;
                if($val->pla_amb_estado == 'Inactivo'){
                    $horario['errores']['ambiente'][] = 'No se puede programar en el ambiente <strong>'.$val->pla_amb_id .' - '.$val->pla_amb_descripcion.'</strong> porque está inhabilitado, comunicarse con el Coordinador Misional.';
                }
            }
            $resultado = array_diff($validarAmbiente, $ambienteSeleccionado['pla_amb_id']);

            if(count($resultado)>0){
                foreach($resultado as $key => $val){
                    $horario['errores']['ambiente'][] = 'El ambiente con el código <strong>'.$val.'</strong> no existe en nuestra base de datos.';
                }
            }
        }
    }

    return $horario;
}

function formatoFecha($fecha){
    $dia = substr($fecha, 3, 2);
    $mes = substr($fecha, 0, 2);
    $ano = substr($fecha, 6);
    $ano = "20".$ano;
    $fecha = $ano."-".$mes."-".$dia;

    return $fecha;
}


function leerExcelParticipante($path, $filename, $rol) {
    $objReader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $objReader->load($path . "/" . $filename);

    // Asignar hoja de calculo activa
    $objPHPExcel->setActiveSheetIndex(0);

    $ficha = (String) $objPHPExcel->getActiveSheet()->getCell('C2')->getCalculatedValue();
    $ficha = explode(" ", $ficha);
    $ficha = $ficha[0];

    $fila = 6;
    $participantes = array();
    $participantes["rol"] = $rol;
    $participantes["ficha"] = $ficha;
    $participantes["fecha"] = date("Y-m-d");  
    $participantes["estado"] = 2;
    $registro = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
    while (trim($registro) != "") {
        $participantes[$fila]["numero_documento"] = (String) $objPHPExcel->getActiveSheet()->getCell('B' . $fila);
        $participantes[$fila]["nombres"] = ucwords(mb_strtolower((String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila)));
        $participantes[$fila]["apellidos"] = ucwords(mb_strtolower((String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila)));
        $participantes[$fila]["telefono"] = (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila);
        $participantes[$fila]["correo"] = (String) $objPHPExcel->getActiveSheet()->getCell('F' . $fila);
        $fila++;
        $registro = (String) $objPHPExcel->getActiveSheet()->getCell('D' . $fila);
    }
    return $participantes;
}


function leerExcelFichas($path, $filename, $cargo) {

    $objReader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $objReader->load($path . "/" . $filename);

    // Asignar hoja de calculo activa
    $objPHPExcel->setActiveSheetIndex(0);
  
    $participantes = array();
    $fila = 3;
    $registro = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);

    while (trim($registro) != "") {

        // Numero de ficha
        $participantes[$fila][0] = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
        
        // Codigo del programa
        $participantes[$fila][1] = (String) $objPHPExcel->getActiveSheet()->getCell('B' . $fila);
        
        // Fecha inicio
        $participantes[$fila][2] = (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila)->getFormattedValue();
        
        // Fecha fin
        $participantes[$fila][3] = (String) $objPHPExcel->getActiveSheet()->getCell('F' . $fila)->getFormattedValue();
        
        // Cedula Instructor par_identificacion
        $participantes[$fila][4] = (String) $objPHPExcel->getActiveSheet()->getCell('G' . $fila)->getValue();
        
        // Cedula Coordinador par_identificacion_coordinador
        $participantes[$fila][5] = (String) $objPHPExcel->getActiveSheet()->getCell('I' . $fila);
        
        
        // Nivel Tecnologico
        $participantes[$fila][6] = (String) $objPHPExcel->getActiveSheet()->getCell('C' . $fila);
        
        
        $fila++;
        $registro = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
    }
    
    
    return $participantes;

}

function leerExcelEstadoAprendiz($ruta,$nombreArchivo){
	$objReader = new PHPExcel_Reader_Excel2007();
	
    $objPHPExcel = $objReader->load($ruta . "/" . $nombreArchivo);

    // Asignar hoja de calculo activa
    $objPHPExcel->setActiveSheetIndex(0);
	
	$estados = array(
		'cancelado'=>1,'formacion'=>2,'por certificar'=>3,'certificado'=>4,
		'retiro voluntario'=>5,'condicionado'=>6,'traslado'=>7,'trasladado'=>7,
		'aplazado'=>8,'para cancelar'=>9,'en transito'=>9,'induccion'=>10
	);
	$caractereNoPremitidos = array('(',')','&gt;','&lt;','javascript','"',"'",'\\','/','<','>','=',';',':','--');

    $fila = 2;
    $registro = $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
    $aprendices = array();
    while (trim($registro) != "") {
        $ficha = (String) $objPHPExcel->getActiveSheet()->getCell('E' . $fila)->getFormattedValue();
        $ficha = str_replace($caractereNoPremitidos,'',$ficha);
        $documento = (String) $objPHPExcel->getActiveSheet()->getCell('L' . $fila)->getFormattedValue();
        $documento = str_replace($caractereNoPremitidos,'',$documento);
        $nombreCompleto = ucwords(mb_strtolower($objPHPExcel->getActiveSheet()->getCell('M' . $fila)));
        $nombreCompleto = str_replace($caractereNoPremitidos,'',$nombreCompleto);
        $apellidos = ucwords(mb_strtolower($objPHPExcel->getActiveSheet()->getCell('N' . $fila))).' '.ucwords(mb_strtolower($objPHPExcel->getActiveSheet()->getCell('O' . $fila)));
        $apellidos = str_replace($caractereNoPremitidos,'',$apellidos);
		$estado = mb_strtolower($objPHPExcel->getActiveSheet()->getCell('P' . $fila));
		$estado = str_replace($caractereNoPremitidos,'',$estado);
		 
        $aprendices[$estados[$estado]][$ficha]['documento'][] = $documento; 
        $aprendices[$estados[$estado]][$ficha]['nombreCompleto'][] = $nombreCompleto; 
        $aprendices[$estados[$estado]][$ficha]['apellidos'][] = $apellidos; 
        $aprendices[$estados[$estado]][$ficha]['estado'][] = $estados[$estado]; 
		  
        $fila++;
        $registro = (String) $objPHPExcel->getActiveSheet()->getCell('A' . $fila);
    }
	//dd($aprendices);
    return $aprendices;
}
