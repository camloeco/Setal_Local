<div class="col-lg-12">
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th>Fase</th>
				<th>Competencia</th>
				<th>Resultado</th>
				<th>Cant horas</th>
				<th>Presencial</th>
				<th>Autonoma</th>
				<th>Fecha evaluaci&oacute;n</th>
				<th>Encargado</th>
				<th>Evaluado</th>
			</tr>
		</thead>
		<tbody>
			@foreach($plantillas as $pla)
			<tr>
				<td>
					<input type="hidden" name="pla_det_id[]" value="{{ $pla->pla_det_id }}"> 
					<input type="hidden" name="pla_can_hor_presenciales[]" value="{{ $pla->pla_can_hor_presenciales }}"> 
					<input type="hidden" name="fas_id[]" value="{{ $pla->fas_id }}"> 
					{{ $pla->fas_descripcion }}
				</th>
				<td>{{ $pla->com_nombre }}</th>
				<td>{{ $pla->res_nombre }}</th>
				<td>{{ $pla->pla_can_hor_total }}</th>
				<td>{{ $pla->pla_can_hor_presenciales }}</th>
				<td>{{ $pla->pla_can_hor_autonomas }}</th>
				<td><input name="hor_fec_evaluacion[]" type="date"></th>
				<td>
					<select name="par_ide_instructor[]" class="form-control">
						<option value="">Seleccione</option>
						@foreach($instructores as $ins)
							<option value="{{ $ins->par_identificacion }}">{{ $ins->par_nombres }} {{ $ins->par_apellidos }}</option>
						@endforeach
					</select>
				</th>
				<td>
					<select name="hor_evaluado[]" class="form-control">
						<option value="">Seleccione</option>
						<option value="">Programado</option>
						<option value="">No</option>
					</select>
				</th>
			</tr>
			@endforeach
			<input type="hidden" name="prog_codigo" value="{{ $prog_codigo[0]->prog_codigo }}">
		</tbody>
	</table>
</div>