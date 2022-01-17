<?php
if(isset($_GET['p']) and $_GET['p'] == '$2y$10$BqxEQa.2C6DCY0znV5vP4eiWf5j9Ys9Vcmb9DQuQ7IQK1VZx6YZV2'){
	$conexion = mysqli_connect('127.0.0.1','cdtiapps_setalpro','y4=ZEdsDyU~z','cdtiapps_seguimientopro');
	mysqli_set_charset($conexion,"utf8");
	
	
	
	$sql1 = '
		select fic_numero, pla_fic_fec_ini_induccion, pla_fic_fec_fin_lectiva, pla_fic_can_trimestre
		from sep_planeacion_ficha
		where pla_fic_fec_fin_lectiva >= "2020-04-11"';
	$fichas = mysqli_query($conexion, $sql);
		
	print_r($fichas);	
	die(); 
	$sql2 = '
		select pla_fic_act_id, fic_numero, pla_fic_act_resultado, par.par_identificacion , par_nombres, par_apellidos, pla_trimestre_numero
		from sep_planeacion_ficha_actividades act, sep_participante par, sep_planeacion_ficha fic
		where act.par_id_instructor = par.par_identificacion
		and 	act.pla_fic_id = fic.pla_fic_id';
	$actividades = mysqli_query($conexion, $sql);
	$sql3 = 'select pla_fec_tri_fec_inicio, pla_fec_tri_fec_fin from sep_planeacion_fecha_trimestre';
	
	
	
	$aprendicesConSeguimiento = array();
	$sql = 'select fic_numero, par_identificacion from sep_matricula';
	$matriculados = mysqli_query($conexion, $sql);
	
	
	foreach($matriculados as $val){
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion']]['bitacoras'] = '';
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion']]['ope_descripcion'] = 'Sin alternativa';
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion']]['seg_pro_fecha_ini'] = '';
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion']]['seg_pro_fecha_fin'] = '';
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion']]['par_identificacion_responsable'] = '';
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion']]['par_nombre'] = '';
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion']][1] = '';
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion']][2] = '';
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion']][3] = '';
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion']]['seg_pro_obs_lider_productiva'] = '';
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion']]['seg_pro_obs_instructor_seguimiento'] = '';
	}
	
	$sql = '
	    select  m.fic_numero,m.par_identificacion,par_identificacion_productiva, concat(par_nombres," ",par_apellidos) as nombreInstructor 
	    from    sep_matricula m, sep_ficha f, sep_participante p
        where   m.fic_numero = f.fic_numero
        and     f.par_identificacion_productiva = p.par_identificacion
        and     par_identificacion_productiva != ""';
    $instructoresAseignados = mysqli_query($conexion, $sql);
    foreach($instructoresAseignados as $val){
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion']]['par_identificacion_responsable'] = $val['par_identificacion_productiva'];
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion']]['par_nombre'] = $val['nombreInstructor'];
	}
	/*echo "<pre>";
	print_r($aprendicesConSeguimiento);
	die();*/
	$sql = '
		select 	pro.seg_pro_id,par_identificacion_aprendiz, fic_numero, count(seg_bit_bitacora) as bitacoras, 
				ope_descripcion, seg_pro_fecha_ini, seg_pro_fecha_fin, par_nombres, par_apellidos, 
				par_identificacion_responsable, seg_pro_obs_lider_productiva, seg_pro_obs_instructor_seguimiento
		from 	sep_seguimiento_productiva pro, sep_seguimiento_bitacora bita, sep_opcion_etapa eta, sep_participante instructor
		where 	pro.seg_pro_id = bita.seg_pro_id
		and 	pro.ope_id = eta.ope_id
		and 	pro.par_identificacion_responsable = instructor.par_identificacion
		group 	by par_identificacion_aprendiz';
	
	$sqlAprendicesConSeguimiento = mysqli_query($conexion, $sql);
	foreach($sqlAprendicesConSeguimiento as $val){
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion_aprendiz']]['bitacoras'] = $val['bitacoras'];
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion_aprendiz']]['ope_descripcion'] = $val['ope_descripcion'];
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion_aprendiz']]['seg_pro_fecha_ini'] = $val['seg_pro_fecha_ini'];
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion_aprendiz']]['seg_pro_fecha_fin'] = $val['seg_pro_fecha_fin'];
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion_aprendiz']]['par_identificacion_responsable'] = $val['par_identificacion_responsable'];
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion_aprendiz']]['par_nombre'] = $val['par_nombres'].' '.$val['par_apellidos'];
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion_aprendiz']][1] = '';
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion_aprendiz']][2] = '';
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion_aprendiz']][3] = '';
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion_aprendiz']]['seg_pro_obs_lider_productiva'] = $val['seg_pro_obs_lider_productiva'];
	    $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion_aprendiz']]['seg_pro_obs_instructor_seguimiento'] = $val['seg_pro_obs_instructor_seguimiento'];
	}
	/*echo "<pre>";
	print_r($aprendicesConSeguimiento);
	die();*/
	$sql = '
	    select  fic_numero, vis.seg_pro_id, seg_vis_visita, seg_vis_fecha, par_identificacion_aprendiz 
	    from    sep_seguimiento_visita vis, sep_seguimiento_productiva pro 
	    where   vis.seg_pro_id = pro.seg_pro_id 
	    order   by vis.seg_pro_id';
	$segundaConsulta = mysqli_query($conexion, $sql);
	foreach($segundaConsulta as $val){
	    if(isset($aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion_aprendiz']])){
	           $aprendicesConSeguimiento[$val['fic_numero']][$val['par_identificacion_aprendiz']][$val['seg_vis_visita']] = $val['seg_vis_fecha'];
	    }
	}
	/*echo "<pre>";
	print_r($aprendicesConSeguimiento);
	die();*/
?>
<style>
	*{
		font-family: arial;
		font-size: 11px;
	}
	table{
		border-collapse:collapse;
	}
</style>
<table border="1">
	<thead>
		<tr>
			<th>No_Documento</th>
			<th>No_Ficha</th>
			<th>No_bitácoras</th>
			<th>Alternativa_EP</th>
			<th>Fecha_inicio_EP</th>
			<th>Fecha_fin_EP</th>
			<th>No_documento</th>
			<th>NombreCompleto</th>
			<th>Fecha_visita_planeación</th>
			<th>Fecha_visita_extraordinaria</th>
			<th>Fecha_visita_evaluación</th>
			<th>Observación_Instructor</th>
			<th>Observación_Coordinador</th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach($aprendicesConSeguimiento as $key1 => $val1){
	    foreach($val1 as $key2 => $val2){
    		echo '
    		<tr>
    		    <td>'.$key2.'</td>
    		    <td>'.$key1.'</td>
    		    <td>'.$val2['bitacoras'].'</td>
    		    <td>'.$val2['ope_descripcion'].'</td>
    		    <td>'.$val2['seg_pro_fecha_ini'].'</td>
    		    <td>'.$val2['seg_pro_fecha_fin'].'</td>
    		    <td>'.$val2['par_identificacion_responsable'].'</td>
    		    <td>'.$val2['par_nombre'].'</td>
    		    <td>'.$val2[1].'</td>
    		    <td>'.$val2[2].'</td>
    		    <td>'.$val2[3].'</td>
    		    <td>'.$val2['seg_pro_obs_lider_productiva'].'</td>
    		    <td>'.$val2['seg_pro_obs_instructor_seguimiento'].'</td>
    		</tr>';
	    }
	}
	?>
	</tbody>
</table>
<?php } ?>
