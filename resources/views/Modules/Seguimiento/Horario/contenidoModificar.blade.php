<div class="alert alert-info alert-dismissable" style="color: #31708f;background-color: #ececec;border-color: #000000;">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>¡Informaci&oacute;n!</strong>
	Cuando modifiques las celdas D&iacute;a, Inicio o Fin se filtrar&aacute;n los Instructores y Ambientes disponibles.
</div>
<table class="table table-bordered table-hover text-center">
	<thead>
		<tr>
			<th class="text-center">#</th>
			<th class="text-center">D&iacute;a</th>
			<th class="text-center">Inicio</th>
			<th class="text-center">Fin</th>
			<th class="text-center">Instructor</th>
			<th class="text-center">Ambiente</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$fila= 1;  
	foreach($horario_detalle as $horario){ ?>
		<tr>
			<input id='pla_fic_det_id' type='hidden' name='pla_fic_det_id[]' value='{{ $horario->pla_fic_det_id }}'>
			<td style='padding:1px;vertical-align: middle;'>{{ $fila++ }}</td>
			<td style='padding:1px;vertical-align: middle;'>
				<select id='pla_dia_id' class='form-control espacios' name='pla_dia_id[]'>
	<?php		foreach($dias as $key => $dia){
						$selected = '';
						if($horario->pla_dia_id == $key){
							$selected = 'selected';
						}	?>
						<option <?php echo $selected; ?> value='{{ $key }}'>{{ $dia }}</option>
	<?php		} ?>
				</select>
			</td>
			<td style='padding:1px;vertical-align: middle;'>
				<select id='pla_fic_det_hor_inicio' class='form-control espacios' name='pla_fic_det_hor_inicio[]'>
	<?php 		for($i=6; $i<=21; $i++){
					$selected = '';
					if($horario->pla_fic_det_hor_inicio == $i){
						$selected = 'selected';
					}	?>
				<option <?php echo $selected; ?> value='{{ $i }}'>{{ $i }}:00</option>
	<?php 		} ?>
				</select>
			</td>
			<td style='padding:1px;vertical-align: middle;'>
				<select id='pla_fic_det_hor_fin' class='form-control espacios' name='pla_fic_det_hor_fin[]'>
	<?php 		for($i=7; $i<=22; $i++){
					$selected = '';
					if($horario->pla_fic_det_hor_fin == $i){
						$selected = 'selected';
					}	 ?>
				<option <?php echo $selected; ?> value='{{ $i }}'>{{ $i }}:00</option>
	<?php 		} ?>
				</select>
			</td>
			<td style='padding:1px;vertical-align: middle;'>
				<select id='det_instructor' class='form-control espacios2' name='par_id_instructor[]'>
	<?php 		foreach($instructores as $instructor){
					$selected = '';
					if($horario->par_identificacion == $instructor->par_identificacion && $instructor->par_identificacion!="0900700"){
						$selected = 'selected';
					}	 ?>
				<option <?php echo $selected; ?> value='{{ $instructor->par_identificacion }}'>{{ $instructor->par_nombres }} {{ $instructor->par_apellidos }}</option>
	<?php 		} ?>
				</select>
			</td>
			<td  style='padding:1px;vertical-align: middle;'>
				<select id='det_ambiente' class='form-control espacios2' name='pla_amb_id[]'>
	<?php 		foreach($ambientes as $ambiente){
					$selected = '';
					if($horario->pla_amb_id == $ambiente->pla_amb_id){
						$selected = 'selected';
					}	 ?>
				<option <?php echo $selected; ?> value='{{ $ambiente->pla_amb_id }}'>{{ $ambiente->pla_amb_descripcion }}</option>
	<?php 		} ?>
				</select>
			</td>
		</tr>
	<?php 	} ?>
	</tbody>
</table>
