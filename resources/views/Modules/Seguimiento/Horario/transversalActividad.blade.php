@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Horarios','Transversal - actividad') !!}
	<div class="row">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Transversal - actividad</span>
					</div>
					<div class="box-icons">
						<a class="collapse-link">
							<i class="fa fa-chevron-up"></i>
						</a>
						<a class="expand-link">
							<i class="fa fa-expand"></i>
						</a>
						<a class="close-link">
							<i class="fa fa-times"></i>
						</a>
					</div>
					<div class="no-move"></div>
				</div>
				<div class="box-content">
					<div class="row">
						@if(session()->get('mensaje') != null)
							@if(session()->get('mensaje') == 'yes')
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="alert alert-success">
										<strong>Actualizaci&oacute;n exitosa!</strong>
									</div>
								</div>
							@else
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="alert alert-info">
										<strong>Por favor seleccione las 3 columnas deben ser diligenciadas.</strong>
									</div>
								</div>
							@endif
						<?php session()->forget('mensaje');?>
						@endif
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<form method="POST">
								<table class="table table-bordered table-striped">
									<thead>
										<tr>
											<th style="border-bottom: 2px solid;">#</th>
											<th style="border-bottom: 2px solid; width: 160px;">Transversal</th>
											<th style="border-bottom: 2px solid; width: 160px;">Diseño Curricular</th>
											<th style="border-bottom: 2px solid;">Competencia - Resultado - Actividad</th>
										</tr>
									</thead>
									<tbody>
									<?php $contador = 1; $imprimir = true; ?>
									@foreach($actividadAsignada as $key1 => $tra)
										@foreach($tra as $key2 => $tra2)
										<tr>
											<td style="vertical-align: middle;">{{ $contador++ }}</td>
											<td style="vertical-align: middle;font-size: 14px;">{{ $tra2['descripcion'] }}</td>
											<td style="vertical-align: middle;">{{ $key2 }}</td>
											<td class="styleSelect">
												<table class="table table-bordered">
													<thead>
														<tr>
															<th>#</th>
															<th>Competencia</th>
															<th>Resultado</th>
															<th>Actividad</th>
															<th>Horas trimestre</th>
															<th>Acci&oacute;n</th>
														</tr>
													</thead>
													<tbody>
														@if(is_array($tra2['competencia']))
															<?php $contador2 = 1; ?>
															@foreach($tra2['competencia'] as $key => $com)
																<?php
																	$competencia = $com;
																	$resultado = $actividadAsignada[$key1][$key2]['resultado'][$key];
																	$actividad = $actividadAsignada[$key1][$key2]['actividad'][$key];
																	$hora = $actividadAsignada[$key1][$key2]['hora'][$key];
																?>
																<tr>
																	<td style="vertical-align:middle;" class="numero">{{ $contador2++ }}</td>
																	<td><textarea style="font-size: 11px; border: 2px solid black;" maxlength="1000" name="competencia[{{ $key1 }}][{{ $key2 }}][]" class="form-control competencia" rows="6" cols="50">{{ $competencia }}</textarea></td>
																	<td><textarea style="font-size: 11px; border: 2px solid black;" maxlength="1000" name="resultado[{{ $key1 }}][{{ $key2 }}][]" class="form-control resultado" rows="6" cols="50">{{ $resultado }}</textarea></td>
																	<td><textarea style="font-size: 11px; border: 2px solid black;" maxlength="1000" name="actividad[{{ $key1 }}][{{ $key2 }}][]" class="form-control actividad" rows="6" cols="50">{{ $actividad }}</textarea></td>
																	<td style="vertical-align: middle; padding: 0px;">
																		<input style="border: 2px solid black;" type="number" name="hora[{{ $key1 }}][{{ $key2 }}][]" class="form-control hora" value="{{ $hora }}" min = "1" max = "100">
																	</td>
																	<td>
																		<a class="btn btn-success duplicar">Agregar</a>
																		<a class="btn btn-danger eliminar">Eliminar</a>
																	</td>
																</tr>
															@endforeach
														@else
														<tr>
															<td style="vertical-align:middle;" class="numero">1</td>
															<td><textarea style="font-size: 11px; border: 2px solid black;" maxlength="1000" name="competencia[{{ $key1 }}][{{ $key2 }}][]" class="form-control competencia" rows="6" cols="50"></textarea></td>
															<td><textarea style="font-size: 11px; border: 2px solid black;" maxlength="1000" name="resultado[{{ $key1 }}][{{ $key2 }}][]" class="form-control resultado" rows="6" cols="50"></textarea></td>
															<td><textarea style="font-size: 11px; border: 2px solid black;" maxlength="1000" name="actividad[{{ $key1 }}][{{ $key2 }}][]" class="form-control actividad" rows="6" cols="50"></textarea></td>
															<td style="vertical-align: middle; padding: 0px;">
																<input style="border: 2px solid black;" type="number" name="hora[{{ $key1 }}][{{ $key2 }}][]" class="form-control hora" min = "1" max = "100">
															</td>
															<td>
																<a class="btn btn-success duplicar">Agregar</a>
																<a class="btn btn-danger eliminar">Eliminar</a>
															</td>
														</tr>
														@endif
													</tbody>
												</table>
											</td>
										</tr>
										@endforeach
									@endforeach
									</tbody>
								</table>
								<div class="text-center">
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<input class="btn btn-success" type="submit" value="Guardar cambios">
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('plugins-css')
<style>
.styleSelect{
	color: blue;
	font-weight: bold;
	border: 1px solid;
}
table tr th, table tr td{
	text-align: center;
	vertical-align: middle;
}
</style>
@endsection
@section('plugins-js')
<script type="text/javascript">
	$(document).ready(function () {
		$(document).on('keyup','.form-control', function(){
			console.log('Estoy funcionando');
			var elemento = $(this).parent().parent();
			var competencia = elemento.find('.competencia').val();
			var resultado = elemento.find('.resultado').val();
			var actividad = elemento.find('.actividad').val();
			var hora = elemento.find('.hora').val();

			if(competencia != '' || resultado != '' || actividad != '' || hora != ''){
				elemento.find('.competencia').attr('required', true);
				elemento.find('.resultado').attr('required', true);
				elemento.find('.actividad').attr('required', true);
				elemento.find('.hora').attr('required', true);
			}else{
				elemento.find('.competencia').removeAttr('required', false);
				elemento.find('.resultado').removeAttr('required', false);
				elemento.find('.actividad').removeAttr('required', false);
				elemento.find('.hora').removeAttr('required', false);
			}

			console.log(competencia);
		});

		$(document).on('click','.duplicar', function(){
			var fila = $(this).parent().parent().html();
			contador = $(this).parent().parent().parent().find('tr').size();
			contador++;
			$(this).parent().parent().parent().append('<tr>'+fila+'</tr>');
			//$('tr').last().remove('');
			$(this).parent().parent().parent().find( "tr" ).last().find('.form-control').html('');
			$(this).parent().parent().parent().find( "tr" ).last().find('.form-control').val('');
			$(this).parent().parent().parent().find( "tr" ).last().find('.numero').html(contador);
		});

		$(document).on('click','.eliminar', function(){
			contador = $(this).parent().parent().parent().find('tr').size();
			if(contador > 1){
				$(this).parent().parent().remove();
			}else{
				alert('No se puede eliminar la única fila');
			}
		});
	});
</script>
@endsection
