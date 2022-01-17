
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">&times;</button>
	<h4>{{ $plantilla_detalle[0]->prog_codigo }} - {{ $plantilla_detalle[0]->prog_nombre }} - versi&oacute;n {{ $plantilla_detalle[0]->pla_version }}</h4>
</div>
<div class="modal-body">
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th>Fase</th>
				<th>Competencia</th>
				<th>Actividad</th>
				<th>Resultado</th>
				<th>Cantidad horas</th>
				<th>Presenciales</th>
				<th>Autonomas</th>
			</tr>
		</thead>
		<tbody>
			@foreach($plantilla_detalle as $pla)
			<tr>
				@if($pla->fas_id == 1)
				<td>An&aacute;lisis</td>
				@elseif($pla->fas_id == 2)
				<td>Planeaci&oacute;n</td>
				@elseif($pla->fas_id == 3)
				<td>Ejecuci&oacute;n</td>
				@elseif($pla->fas_id == 4)
				<td>Evaluaci&oacute;n</td>
				@endif
				<td>{{ $pla->com_nombre }}</td>
				<td>{{ $pla->act_descripcion }}</td>
				<td>{{ $pla->res_nombre }}</td>
				<td>{{ $pla->pla_can_hor_total }}</td>
				<td>{{ $pla->pla_can_hor_presenciales }}</td>
				<td>{{ $pla->pla_can_hor_autonomas }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
</div>