@if(count($detalle)>0)
<?php
foreach($detalle as $dato){
  if($dato->pla_amb_id != 72){
?>
  <tr>
    <td style="padding:0px;">
      <select required class="form-control" name="dia[]">
<?php   foreach($dias as $key => $dia){
          $selected = '';
          if($dato->pla_dia_id == $key){ $selected = 'selected'; } ?>
          <option <?php echo $selected; ?> value="<?php echo $key; ?>"><?php echo $dia; ?></option>
<?php   } ?>
      </select>
      <input type="hidden" name="pla_fic_det_id[]" value="{{ $dato->pla_fic_det_id }}">
      <input type="hidden" name="fecha_inicio[]" value="{{ $dato->pla_fic_det_fec_inicio }}">
      <input type="hidden" name="fecha_fin[]" value="{{ $dato->pla_fic_det_fec_fin }}">
    </td>
    <td style="padding:0px;">
      <select required class="form-control" name="hora_inicio[]">
<?php   for($i=6; $i<=21; $i++){
          $selected = '';
          if($dato->pla_fic_det_hor_inicio == $i){ $selected = 'selected'; } ?>
          <option <?php echo $selected; ?> value="<?php echo $i; ?>"><?php echo $i.':00'; ?></option>
<?php   } ?>
      </select>
    </td>
    <td style="padding:0px;">
      <select required class="form-control" name="hora_fin[]">
<?php   for($i=7; $i<=22; $i++){
          $selected = '';
          if($dato->pla_fic_det_hor_fin == $i){ $selected = 'selected'; }  ?>
          <option <?php echo $selected; ?> value="<?php echo $i; ?>"><?php echo $i.':00'; ?></option>
<?php     } ?>
      </select>
    </td>
    <td style="padding:0px;">
      <select required class="form-control" name="pla_amb_id[]">
<?php   foreach($ambientes as $val){
          $selected = '';
          if($dato->pla_amb_id == $val->pla_amb_id){ $selected = 'selected'; } ?>
          <option <?php echo $selected; ?> value="<?php echo $val->pla_amb_id; ?>"><?php echo $val->pla_amb_descripcion; ?></option>
  <?php } ?>
      </select>
    </td>
    <td style="padding:0px;text-align:center;">
      <img class="eliminarRestriccion" style="cursor:pointer;" data-id="{{ $dato->pla_fic_det_id }}" src="{{ asset('img/horario/basura.png') }}">
    </td>
  </tr>
<?php
    }
  } ?>
<input id="par_identificacion" type="hidden" name="par_identificacion" value="{{ $cc }}">
@else
  <tr>
    <td colspan="5" class="text-center">No se encontro registros del instructor seleccionado.</td>
  </tr>
@endif