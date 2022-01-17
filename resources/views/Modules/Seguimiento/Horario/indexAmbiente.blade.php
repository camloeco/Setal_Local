@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Horarios','Ambiente') !!}
	<div class="row">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Horario - Ambiente</span>
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
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<form method="GET">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="margin-top: 5px;">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="col-lg-4 col-lg-push-4 col-md-6 col-md-push-3 col-sm-12 col-xs-12">
											<label>Trimestre: </label><br>
											<small>Año - N&uacute;mero trimestre - Fecha inicio - Fecha fin</small>
											<select class="form-control" name="pla_fec_tri_id" required>
												<option value = "">Seleccione...</option>
												@if(isset($pla_fec_tri_id))
													@foreach($trimestres as $val)
														<?php $selected = ""; ?>
														@if($pla_fec_tri_id == $val->pla_fec_tri_id)
														<?php $selected = "selected"; ?>
														@endif
														<option <?php echo $selected; ?> value="{{ $val->pla_fec_tri_id }}">{{ $val->pla_fec_tri_year }} - {{ $val->pla_fec_tri_trimestre }} - {{ $val->pla_fec_tri_fec_inicio }} - {{ $val->pla_fec_tri_fec_fin }}</option>
													@endforeach
												@else
													@foreach($trimestres as $val)
														<option value="{{ $val->pla_fec_tri_id }}">{{ $val->pla_fec_tri_year }} - {{ $val->pla_fec_tri_trimestre }} - {{ $val->pla_fec_tri_fec_inicio }} - {{ $val->pla_fec_tri_fec_fin }}</option>
													@endforeach
												@endif
											</select>
										</div>
									</div>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="col-lg-6 col-lg-push-3 col-md-8 col-md-push-2 col-sm-12 col-xs-12">
											<label>Seleccione ambiente(s): </label><br>
											<select class="js-example-basic-multiple" name="pla_amb_id[]" multiple="multiple" required>
												@if(isset($pla_amb_id))
													<?php $selected = ""; ?>
													@if(in_array("todas", $pla_amb_id))
													<?php $selected = "selected"; ?>
													@endif
													<option <?php echo $selected; ?> value = "todas">Todos los ambientes</option>
													@foreach($ambientes as $val)
														<?php $selected = ""; ?>
														@if(in_array($val->pla_amb_id, $pla_amb_id))
														<?php $selected = "selected"; ?>
														@endif
														<option <?php echo $selected; ?> value="{{ $val->pla_amb_id }}">{{ $val->pla_amb_descripcion }}</option>
													@endforeach
												@else
													<option value = "todas">Todos los ambientes</option>
													@foreach($ambientes as $val)
														<option value="{{ $val->pla_amb_id }}">{{ $val->pla_amb_descripcion }}</option>
													@endforeach
												@endif
											</select>
										</div>
									</div>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="col-lg-3 col-lg-push-3 col-md-3 col-md-push-3" style="margin-top: 10px;">
											<input value="Buscar" class ="form-control btn btn-primary" type="submit">
										</div>
										<div class="col-lg-3 col-lg-push-3 col-md-3 col-md-push-3" style="margin-top: 10px;">
											<a href="{{ url('seguimiento/horario/indexambiente') }}" class ="form-control btn btn-default">Limpiar filtro</a>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="row" id="url" data-url="{{ url('seguimiento/horario/actividadesinstructor') }}">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							@if(isset($programacion) and count($programacion)>0)
							    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
									<?php
										$host= $_SERVER["HTTP_HOST"];
										$url= $_SERVER["REQUEST_URI"];
										$url ="http://" . $host . $url.'&generar=PDF';
									?>
									<a title="Exportar a PDF" target="_blank" href="<?php echo $url; ?>"><img style="cursor:pointer;" src="{{ asset('img/horario/PDF2.png') }}"></a>
								</div>
								@foreach($programacion as $key => $val)
									@foreach($val as $key1 => $val1)
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
										<h5 style="font-weight: bold;margin:0px;">{{ $val1["ambiente"] }}</h5>
										<h6 style="text-align:center;margin:0px 0px 2px 0px;">
											<strong>Fecha inicio:</strong> {{ $key1 }}
											<strong>Fecha fin:</strong> {{ $programacion[$key][$key1]['fecha_fin'] }}
										</h6>
									</div>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="padding: 0px 75px;">
										<table style="margin-bottom:15px;" data-ambiente="{{ $val1['ambiente'] }}" data-fecha-inicio="{{ $key1 }}" data-fecha-fin="{{ $programacion[$key][$key1]['fecha_fin'] }}" class="table table-bordered table-hover">
											<thead>
												<tr>
													<th style="width:78px;padding:2px;font-size:12px;text-align: center;">Hora</th>
													@for($i=0; $i<=5; $i++)
													<th style="min-width:90px;padding:2px;font-size:12px;text-align: center;">{{ $diaOrtografia[$i] }}</th>	
													@endfor
												</tr>
											</thead>
											<tbody>
												<?php
												for($h=1; $h<7; $h++){
													$horas[$h]['can'] = 16;
													$horas[$h]['total'] = 16;
												}
												?>
												@for($j=6; $j<22; $j++)
													<tr>
														<th style='padding:5px;font-size:11px;text-align:center;'>{{ $j }}:00 - {{ ($j+1) }}:00</th>
														@for($i=1; $i<7; $i++)
															@if($horas[$i]['can'] == $horas[$i]['total'])
																<?php	$control = false; ?>
																@foreach($val1['hora_inicio'] as $key2 => $val2)
																	@if($val1['dia_id'][$key2] == $i and $val2 == $j)
																		<?php
																			$ficha = $val1['ficha'][$key2];
																			$programa = $val1['programa'][$key2];
																			$sigla = $val1['prog_sigla'][$key2];
																			$consecutivo = $val1['consecutivo_ficha'][$key2];
																			$horas_totales = $val1['horas_totales'][$key2];
																			$numero_trimestre = $val1['trimestre'][$key2];
																			$pla_fic_id = $val1['pla_fic_id'][$key2];
																			$instructor = $val1['instructor'][$key2];
																			$instructor_cc = $val1['instructor_cc'][$key2];
																			$pla_fic_id = $val1['pla_fic_id'][$key2];
																			$fecha_inicio_actividad = $val1['fecha_inicio_actividad'][$key2];
																			$control = true;
																		?>
																	@endif
																@endforeach
																
																@if($control == true)
																	<td fecha_inicio_actividad="{{ $fecha_inicio_actividad }}" title="{{ $programa }}" data-pla-fic-id = "{{ $pla_fic_id }}" data-cc="{{ $instructor_cc }}" data-trimestre="{{ $numero_trimestre }}" data-ficha="{{ $ficha }}" data-programa="{{ $programa }}" rowspan="{{ $horas_totales }}" class="text-center actividadAmbiente" style="padding: 0px;vertical-align: middle;cursor:pointer;">
																		<h6 style="margin:2px 0px 0px 0px;font-size:11px;font-weight: bold;"><strong style="font-size:9px;">{{ $sigla }} {{ $consecutivo }}</strong> {{ $ficha }}</h6>
																		<h6 style="margin:1px 0px 2px 0px;font-size:11px;">
																			{{ $instructor }}<br>{{ $instructor_cc }}
																		</h6>
																	</td>
																	<?php $horas[$i]['can'] -= $horas_totales; ?>
																@else
																	<td></td>
																	<?php $horas[$i]['can']--; ?>
																@endif
															@endif
															<?php $horas[$i]['total']--; ?>
														@endfor
													</tr>
												@endfor
											</tbody>
										</table>
									</div>
									@endforeach
								@endforeach
							@else
								@if(isset($pla_amb_id))
								<h5 class="text-center">No se encontraron registros del ambiente en el trimestre seleccionado.</h5>
								@endif
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width:76%">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="text-center">
					<button type="button" class="close" data-dismiss="modal" style="margin:8px 14px 0px 0px;">&times;</button>
					<div class="alert alert-success" style="background-color:#087b76;margin:0px;color:white;border:black;border-radius:0px;">
						<span style="display: block;position: fixed;">Actividades</span>
						<strong class="modal-title" style="font-size:15px;">Ficha: </strong><small id="ficha" style="font-size:16px;"></small><br>
						<strong class="modal-title" style="font-size:15px;">Trimestre ficha # </strong><small id="trimestre" style="font-size:16px;"></small><br>
						<strong class="modal-title" style="font-size:15px;">Programa: </strong><small id="programa" style="font-size:16px;"></small><br>
						<strong class="modal-title" style="font-size:15px;">Fecha inicio: </strong><small id="fecInicio" style="font-size:16px;"></small>&nbsp;&nbsp;
						<strong class="modal-title" style="font-size:15px;">Fecha fin: </strong><small id="fecFin" style="font-size:16px;"></small><br>
						<strong class="modal-title" style="font-size:15px;">Ambiente: </strong><small id="ambiente" style="font-size:16px;"></small>
					</div>
				</div>
				<div class="borrar modal-body">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th style="text-align:center;">Fase</th>
								<th style="text-align:center;">Competencia</th>
								<th style="text-align:center;">Resultado</th>
								<th style="text-align:center;">Actividad</th>
								<th style="text-align:center;">Horas</th>
							</tr>
						</thead>
						<tbody id="contenido">
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button style="margin:0px;" class="btn btn-danger btn-xs" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>
@endsection