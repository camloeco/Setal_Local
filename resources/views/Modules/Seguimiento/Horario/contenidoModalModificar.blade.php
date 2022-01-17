<?php 
$contador = 1;
foreach($horario_detalle as $horario){ 
    $tipo = '';
    if($horario->pla_tip_id == 2){
        $tipo = 'Técnico';
    }else if($horario->pla_tip_id == 3){
        $tipo = 'Complementario';
    }else if($horario->pla_tip_id == 6){
        $tipo = 'Etapa práctica';
    }else if($horario->pla_tip_id == 7){
        $tipo = 'Transversal';
    }
    echo '
    <tr>
        <td style="padding:0px;"><input style="cursor:pointer;" id="'.$contador.'" value="'.$horario->pla_fic_det_id.'" class="filaModificar" type="checkbox" name="pla_fic_det_id_checked[]"></td>
        <td style="padding:0px;"><label for="'.$contador.'" style="font-weight:unset;cursor:pointer;">'.$tipo.'</label></td>
        <td style="padding:0px;"><label for="'.$contador.'" style="font-weight:unset;cursor:pointer;">'.$dias[$horario->pla_dia_id].'</label></td>
        <td style="padding:0px;"><label for="'.$contador.'" style="font-weight:unset;cursor:pointer;">'.$horario->pla_fic_det_hor_inicio.':00 - '.$horario->pla_fic_det_hor_fin.':00</label></td>
        <td style="padding:0px;"><label for="'.$contador.'" style="font-weight:unset;cursor:pointer;">'.$horario->pla_fic_det_hor_totales.'</label></td>
        <td style="padding:0px;"><label for="'.$contador.'" style="font-weight:unset;cursor:pointer;">'.$horario->par_nombres.' '.$horario->par_apellidos.'</label></td>
        <td style="padding:0px;"><label for="'.$contador.'" style="font-weight:unset;cursor:pointer;">'.$horario->pla_amb_descripcion.'</label></td>
    </tr>';
    $contador++;
} 
?>