@if($existenAprendices == 'NO')
<div class="row">
	<div class="text-center col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="alert alert-danger" style="margin-bottom: -12px;">
			La ficha no tiene aprendices cargados o los aprendices tienen un estado diferente de 'En formación' o 'Inducción', 
			por favor informar a coordinación.
		</div>
	</div>
</div>	
@else
<div style="<?php echo $contenedorLlamadoAsistencia; ?>" id="contenedorLlamadoAsistencia" class="row">
	<div class="text-center col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h5 style="font-size:16px;">Marcar llamado de asistencia a las <strong><?php echo date('H:i'); ?></strong></h5>
		<input id="checkLlamadoAsistencia" class="form-control" type="checkbox">
	</div>
</div>
<div style="<?php echo $contenedorAsistencia; ?>" id="contenedorAsistencia" class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h6 id="notificaciones" style="margin:0px;position:absolute;"></h6>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:14px;height:calc(100vh - 265px);overflow-y:auto;">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th style="padding:1px;">#</th>
					<th style="padding:1px;">Documento</th>
					<th class="hidden-xs" style="padding:1px;">Nombre(s)</th>
					<th class="hidden-xs" style="padding:1px;">Apellido(s)</th>
					<th class="hidden-lg hidden-md hidden-sm" style="padding:1px;">Nombre</th>
					<th style="padding:1px;text-align:center;">Asisti&oacute;?</th>
					<th style="padding:1px;text-align:center;">Retardo</th>
				</tr>
			</thead>
			<tbody>
				<?php $contador=1; ?>
				@foreach($aprendices as $apr)
					<?php 
						$estilos = '';
						if(in_array($apr->par_identificacion, $aprendicesConInasistencia)){
							$estilos = 'color:red;';
						}
						$retardo = '';
						if(in_array($apr->par_identificacion, $aprendicesConRetardos)){
							$retardo = 'si';
						}
					?>
					<tr class="filaAsistencia" style="cursor:pointer;{{ $estilos }}">
						<th style="padding:1.5px;font-size:12px;vertical-align: middle;">{{ $contador++ }}</th>
						<td style="padding:1.5px;font-size:12px;vertical-align: middle;" class="documento">{{ $apr->par_identificacion }}</td>
						<td class="hidden-xs" style="padding:1.5px;font-size:12px;vertical-align: middle;"><?php echo ucwords(mb_strtolower($apr->par_nombres)); ?></td>
						<td class="hidden-xs" style="padding:1.5px;font-size:12px;vertical-align: middle;"><?php echo ucwords(mb_strtolower($apr->par_apellidos)); ?></td>
						<td class="hidden-lg hidden-md hidden-sm" style="padding:1.5px;font-size:12px;vertical-align: middle;"><?php echo ucwords(mb_strtolower($apr->nombreCorto.' '.$apr->apellidoCorto)); ?></td>
						<td style="padding:4px;font-size:12px;text-align:center;">
							@if($estilos == '')
							<label style="cursor:pointer;font-size:12px;margin:0px;" for="{{ $apr->par_identificacion }}SI">SI</label>
							<input value="SI" class="asistencia" <?php echo $checked; ?> style="cursor:pointer;margin:0px;vertical-align: middle;" id="{{ $apr->par_identificacion }}SI" name="{{ $apr->par_identificacion }}" type="radio">
							<label style="cursor:pointer;font-size:12px;margin:0px;" for="{{ $apr->par_identificacion }}NO">NO</label>
							<input value="NO" class="asistencia" style="cursor:pointer;margin:0px;vertical-align: middle;" id="{{ $apr->par_identificacion }}NO" name="{{ $apr->par_identificacion }}" type="radio">
							@else 
							<label style="cursor:pointer;font-size:12px;margin:0px;" for="{{ $apr->par_identificacion }}SI">SI</label>
							<input value="SI" class="asistencia" style="cursor:pointer;margin:0px;vertical-align: middle;" id="{{ $apr->par_identificacion }}SI" name="{{ $apr->par_identificacion }}" type="radio">
							<label style="cursor:pointer;font-size:12px;margin:0px;" for="{{ $apr->par_identificacion }}NO">NO</label>
							<input value="NO" class="asistencia" <?php echo $checked; ?> style="cursor:pointer;margin:0px;vertical-align: middle;" id="{{ $apr->par_identificacion }}NO" name="{{ $apr->par_identificacion }}" type="radio">
							@endif
						</td>
						<td style="padding:2px;font-size:12px;text-align:center;">
							@if($retardo == '')
							<input class="retardo" style="cursor:pointer;" type="checkbox">
							@else
							<input checked class="retardo" style="cursor:pointer;" type="checkbox">
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endif
				