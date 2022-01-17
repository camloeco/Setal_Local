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
<div id="contenedorAsistencia" class="row">
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
					<th style="padding:1px;text-align:center;">Habilitado</th>
				</tr>
			</thead>
			<tbody>
				<?php $contador=1; ?>
				@foreach($aprendices as $apr)
					<tr class="filaAsistencia" style="cursor:pointer;">
						<th style="padding:1.5px;font-size:12px;vertical-align: middle;">{{ $contador++ }}</th>
						<td style="padding:1.5px;font-size:12px;vertical-align: middle;" class="documento">{{ $apr->par_identificacion }}</td>
						<td class="hidden-xs" style="padding:1.5px;font-size:12px;vertical-align: middle;"><?php echo ucwords(mb_strtolower($apr->par_nombres)); ?></td>
						<td class="hidden-xs" style="padding:1.5px;font-size:12px;vertical-align: middle;"><?php echo ucwords(mb_strtolower($apr->par_apellidos)); ?></td>
						<td class="hidden-lg hidden-md hidden-sm" style="padding:1.5px;font-size:12px;vertical-align: middle;"><?php echo ucwords(mb_strtolower($apr->nombreCorto.' '.$apr->apellidoCorto)); ?></td>
						<td style="padding:2px;font-size:12px;vertical-align: middle;text-align:center;">
							<select class="habilitar" data-documento="{{ $apr->par_identificacion }}">
								<option value="">Seleccione...</option>
								<option value="no">NO</option>
								<option value="si">SI</option>
							</select>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endif