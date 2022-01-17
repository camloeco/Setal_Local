<?php 
if(count($actividades) > 0){
    $contador = 1;
    $totalHoras = 0;
    foreach($actividades as $actividad){
      $totalHoras += $actividad->pla_fic_act_horas;
      echo '
        <tr>
    	  <th style="text-align:center;font-size:12px;vertical-align:middle;">'.$contador++.'</th>	
          <td style="font-size:12px;vertical-align:middle;">'.$fases[$actividad->fas_id].'</td>
          <td style="padding:2px;font-size:12px;vertical-align:middle;">'.$actividad->pla_fic_act_competencia.'</td>
          <td style="padding:2px;font-size:12px;vertical-align:middle;">'.$actividad->pla_fic_act_resultado.'</td>
          <td style="padding:2px;font-size:12px;vertical-align:middle;">'.$actividad->pla_fic_act_actividad.'</td>
          <td style="padding:2px;font-size:12px;vertical-align:middle;">'.$actividad->pla_fic_act_horas.'</td>
          <td style="padding:2px;font-size:12px;vertical-align:middle;width: 240px;">';
          if(($totalHoras >= $habilitarDesde and $totalHoras <= $habilitarHasta) or ($fases[$actividad->fas_id] == '-')){
      echo '<input type="hidden" name="pla_fic_act_id[]" value="'.$actividad->pla_fic_act_id.'">
            <select class="form-control" name="par_id_instructor[]" style="background: whitesmoke;color: black;font-weight: bold;">
              <option value="noAsignar">Sin asignar</option>';
              foreach($instructores as $instructor){
                $selected = '';
                if($instructor->par_identificacion == $actividad->par_id_instructor){
                  $selected = 'selected';
                }
                echo '<option '.$selected.' value="'.$instructor->par_identificacion.'">'.$instructor->par_nombres.' '.$instructor->par_apellidos.'</option>';
              }
      echo '</select>';
          }else if($totalHoras <= $habilitarDesde){
            if(array_key_exists($actividad->par_id_instructor, $instructoresArray)){
              echo $instructoresArray[$actividad->par_id_instructor];
            }else{
        echo '<input type="hidden" name="pla_fic_act_id[]" value="'.$actividad->pla_fic_act_id.'">
              <select class="form-control" name="par_id_instructor[]" style="background: whitesmoke;color: black;font-weight: bold;">
                <option value="noAsignar">Sin asignar</option>';
                foreach($instructores as $instructor){
                  $selected = '';
                  if($instructor->par_identificacion == $actividad->par_id_instructor){
                    $selected = 'selected';
                  }
                  echo '<option '.$selected.' value="'.$instructor->par_identificacion.'">'.$instructor->par_nombres.' '.$instructor->par_apellidos.'</option>';
                }
        echo '</select>';
            }
          }else{
            echo 'Inhabilitado';
          }
    echo '</td>
    	    <td style="padding:2px;font-size:11px;vertical-align:middle;width: 240px;">';
          if(($totalHoras >= $habilitarDesde and $totalHoras <= $habilitarHasta) or ($fases[$actividad->fas_id] == '-')){
    echo   '<select class="form-control" name="pla_fec_tri_id[]" style="background: whitesmoke;color: black;font-weight: bold;">
            <option value="0">Sin asignar</option>';
            foreach($trimestre_ficha['fecha_inicio'] as $llave => $fecha_inicio){
              $fecha_fin = $trimestre_ficha['fecha_fin'][$llave];
              $trimestre = $trimestre_ficha['trimestre_numero'][$llave];
              $selected = '';
              if($fecha_inicio == $actividad->fecha_inicio){
                $selected = 'selected';
              }
    
              echo '<option '.$selected.' value="'.$fecha_inicio.'"># '.$trimestre.' - '.$fecha_inicio.' '.$fecha_fin.'</option>';
            }
      echo '</select>';
          }else if($totalHoras <= $habilitarDesde){
            if(array_key_exists($actividad->par_id_instructor, $instructoresArray)){
              $llave = array_search($actividad->fecha_inicio, $trimestre_ficha['fecha_inicio']);
              if(isset($trimestre_ficha['trimestre_numero'][$llave])){
                $fecha_fin = $trimestre_ficha['fecha_fin'][$llave];
                echo '# '.$trimestre_ficha['trimestre_numero'][$llave].' | '.$actividad->fecha_inicio.' '.$fecha_fin;
              }else{
                echo 'Sin fecha asignada';
              }
            }else{
              echo   '<select class="form-control" name="pla_fec_tri_id[]" style="background: whitesmoke;color: black;font-weight: bold;">
                <option value="0">Sin asignar</option>';
                foreach($trimestre_ficha['fecha_inicio'] as $llave => $fecha_inicio){
                  $fecha_fin = $trimestre_ficha['fecha_fin'][$llave];
                  $trimestre = $trimestre_ficha['trimestre_numero'][$llave];
                  $selected = '';
                  if($fecha_inicio == $actividad->fecha_inicio){
                    $selected = 'selected';
                  }
    
                  echo '<option '.$selected.' value="'.$fecha_inicio.'"># '.$trimestre.' - '.$fecha_inicio.' '.$fecha_fin.'</option>';
                }
          echo '</select>';
            }
          }else{
            echo 'Inhabilitado';
          }
      echo '</td>
        </tr>';
    } 
}else{
  echo '
  <tr>
    <td colspan="8">
    <strong style="color:red;">El horario creado no tiene actividades porque 
        en la función programa -> listar no se ha cargado el plande trabajo del programa,
        favor comunicar al Coordinador, para el desarrollo del mismo y posterior carge.</strong>
    </td>
  </tr>';
}?>