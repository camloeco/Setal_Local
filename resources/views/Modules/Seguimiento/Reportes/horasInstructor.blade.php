@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Reporte','Horas instructor') !!}
<section class='content'>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="box ui-draggable ui-droppable"> 
					<div class="box-header" >
					<div class="box-name ui-draggable-handle">
							<i class="fa fa-table"></i>
							<span>Horas instructor</span>
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
								<form method="GET" id="formulario" data-url="{{ url('seguimiento/reportes/updatearea')}}">
									<input id="miToken" type="hidden" name="_token" value="{{ csrf_token() }}">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="margin-top: 5px;">
										@if($rol == 5 or $rol == 3 or $rol == 0 or $rol == 8)
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding: 10px 0px 10px 0px;">
											<div class="col-lg-4 col-lg-push-4 col-md-6 col-md-push-3 col-sm-12 col-xs-12">
												<label>Coordinaci&oacute;n: </label><br>
												<select class="form-control" name="par_identificacion_coordinador" id ="par_identificacion_coordinador">
													<option value = "">Seleccionar instructor(es)</option>	
													@if(isset($par_identificacion_coordinador))
														@foreach($coordinadores as $val)
															<?php $selected = ''; ?>
															@if($par_identificacion_coordinador == $val->par_identificacion)	
															<?php $selected = 'selected'; ?>
															@endif
															<option <?php echo $selected; ?> value="{{ $val->par_identificacion }}">{{ ucwords(mb_strtolower($val->par_nombres)) }} {{ ucwords(mb_strtolower($val->par_apellidos)) }}</option>
														@endforeach
														@if($par_identificacion_coordinador == 'todas')
														<option selected value = "todas">Todas las coordinaciones</option>
														@else 
															<option value = "todas">Todas las coordinaciones</option>
														@endif
													@else
														@foreach($coordinadores as $val)
														<option value="{{ $val->par_identificacion }}">{{ ucwords(mb_strtolower($val->par_nombres)) }} {{ ucwords(mb_strtolower($val->par_apellidos)) }}</option>
														@endforeach
														<option value = "todas">Todas las coordinaciones</option>
													@endif 
												</select>
											</div>
										</div>
										<?php
											$style = '';
											$required = '';
											if(isset($par_identificacion_coordinador) and $par_identificacion_coordinador == ''){
												$style = 'display:none;';
											}
										?>
										@endif

										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<div class="col-lg-4 col-lg-push-4 col-md-6 col-md-push-3 col-sm-12 col-xs-12">
												<label>Año - mes</label><br>
												<select class="form-control" name="anio[]" required>
													<option value="">Seleccione...</option>
													@if(isset($anio))
														@for($i=$anio_actual; $i>=2019; $i--)
															@for($j=12; $j>=1; $j--)
															<?php
																$selected = '';
																if($j < 10){
																	$colocar = '0'.$j;
																}else{
																	$colocar = $j;
																}

																if(in_array($i.'-'.$colocar, $anio)){
																	$selected = 'selected';
																}
															?>
															<option {{ $selected }} value="{{ $i }}-{{ $colocar }}">{{ $i }} - {{ $meses[$j] }}</option>
															@endfor
														@endfor
													@else
														@for($i=$anio_actual; $i>=2019; $i--)
															@for($j=12; $j>=1; $j--)
															<?php
																$selected = '';
																if($i == $anio_actual and $mes_actual == $j){
																	$selected = 'selected';
																}

																if($j < 10){
																	$colocar = '0'.$j;
																}else{
																	$colocar = $j;
																}
															?>
															<option {{ $selected }} value="{{ $i }}-{{ $colocar }}">{{ $i }} - {{ $meses[$j] }}</option>
															@endfor
														@endfor
													@endif
													
												</select>
											</div>
										</div>
										<?php
											$style = '';
											$required = '';
											if(isset($par_identificacion_coordinador) and $par_identificacion_coordinador != ''){
												$style = 'style="display:none;"';
											}else{
												$required = 'required';
											}
										?>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="contenedor_seleccionar_ficha" <?php echo $style; ?>>
											<div class="col-lg-10 col-lg-push-1 col-md-10 col-md-push-1 col-sm-12 col-xs-12">
												<label>Seleccione instructor(es): </label><br>
												<select id="pla_fic_id" class="js-example-basic-multiple" name="par_identificacion[]" multiple="multiple" <?php echo $required; ?>>
													@if(isset($par_identificacion))
														<?php $selected = ""; ?>
														@if(in_array("todas", $par_identificacion))
														<?php $selected = "selected"; ?>
														@endif
														@foreach($instructores_db as $val)
															<?php $selected = ""; ?>
															@if(in_array($val->par_identificacion, $par_identificacion))
															<?php $selected = "selected"; ?>
															@endif
															<option <?php echo $selected; ?> value="{{ $val->par_identificacion }}">{{ ucwords(mb_strtolower($val->nombre)) }}</option>
														@endforeach
													@else
														@foreach($instructores_db as $val)
															<option value="{{ $val->par_identificacion }}">{{ ucwords(mb_strtolower($val->nombre)) }}</option>
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
												<a href="{{ url('seguimiento/reportes/horasinstructor') }}" class ="form-control btn btn-default">Limpiar filtro</a>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
						<div id="notificacion" style="position: absolute;width: 340px;height: 40px;background: #d2cfcf;z-index: 100;border: solid 2px; display: none;border-radius: 24px;text-align: center;">
							<h4 style="color:black;">El cambio se realizo exitosamente.</h4>
						</div>
						<div class="row" style="padding-top: 15px;">
							<div class="col-lg-12 col-md-12">
								@if(isset($horasInstructores) and count($horasInstructores)>0)
									
									<p class="text-center">
										<strong>Dir.</strong><small> = Formaci&oacute;n directa</small>&nbsp;&nbsp;
										<strong>Com.</strong><small> = Formaci&oacute;n complementario</small>&nbsp;&nbsp;
										<strong>Res.</strong><small> = Restricciones</small>&nbsp;&nbsp;
										<strong>Seg.</strong><small> = Seguimiento etapa pr&aacute;ctica</small>&nbsp;&nbsp;
										<strong>Tot.</strong><small> = Total</small>&nbsp;&nbsp;
										<strong>Dis.</strong><small> = Disponibles</small>
									</p>
									<div class="table-responsive" style="overflow-y: auto;">
										<table class="table table-bordered table-hover">
											<thead>
												<tr>
													<th rowspan="3" style="vertical-align: middle; text-align: center;">Instructor</th>
													<th rowspan="3" style="vertical-align: middle; text-align: center;">Coordinador</th>
													@foreach($dias_mes as $key => $val)
													<th colspan="6" style="vertical-align: middle; text-align: center;">{{ substr($key, 0, 4) }} - {{ substr($key, 5, 2) }}</th>
													@endforeach
												</tr>
												<tr>
													@foreach($dias_mes as $key => $val)
													<th colspan="5" style="vertical-align: middle; text-align: center;">Asignadas</th>
													<th rowspan="2" style="vertical-align: middle; text-align: center;">Dis.</th>
													@endforeach
												</tr>
												<tr>
													@foreach($dias_mes as $key => $val)
													<th  style="vertical-align: middle; text-align: center;">Dir.</th>
													<th  style="vertical-align: middle; text-align: center;">Com.</th>
													<th  style="vertical-align: middle; text-align: center;">Res.</th>
													<th  style="vertical-align: middle; text-align: center;">Seg.</th>
													<th  style="vertical-align: middle; text-align: center;">Tot.</th>
													@endforeach
												</tr>
												
												@foreach($horasInstructores as $id_coordinador => $val)
													<?php 
														$nombre_coordinador = $nombres[$id_coordinador];
														$horas_disponibles = 0;
													?>
													@foreach($val as $id_instructor => $val1)
													<tr>
														<td style="vertical-align: middle; text-align: center;">{{ $nombres[$id_instructor] }}</td>
														<td style="vertical-align: middle; text-align: center;">{{ $nombre_coordinador }}</td>
														@foreach($val1 as $fecha_fin => $val2)
														<?php 
															$horas_totales = $val2['horas_semanales'] - $val2['horas_totales'];
															if($horas_totales > 0){
																$style="color:red;";
																$horas_disponibles += $horas_totales;
															}else{
																$style="color:green;";
															}
														?>
														<td style="vertical-align: middle; text-align: center;">{{ $val2['horas_formacion_directa'] }}</td>
														<td style="vertical-align: middle; text-align: center;">{{ $val2['horas_formacion_complementario'] }}</td>
														<td style="vertical-align: middle; text-align: center;">{{ $val2['horas_restriccion'] }}</td>
														<td style="vertical-align: middle; text-align: center;">{{ $val2['horas_etapa_práctica'] }}</td>
														<td style="vertical-align: middle; text-align: center;">{{ $val2['horas_totales'] }}</td>
														<th style="{{ $style }}vertical-align: middle; text-align: center;">{{ ($val2['horas_semanales'] - $val2['horas_totales']) }}</th>
														@endforeach
													</tr>
													@endforeach
													<tr>
														<th colspan="7" style="vertical-align: middle; text-align: center;">Horas disponibles</th>
														<th style="color:red;vertical-align: middle; text-align: center;">{{ $horas_disponibles }}</th>
													</tr>
												@endforeach
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection