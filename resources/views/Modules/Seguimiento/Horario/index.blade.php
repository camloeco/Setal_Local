@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Horarios','Ficha') !!}
	<div class="row" id="urls" data-token="{{ csrf_token() }}" data-cambio-input = "{{ url('seguimiento/horario/modificarnumeroprograma') }}" data-cambio-select = "{{ url('seguimiento/horario/modificartipooferta') }}" data-cambio-franja = "{{ url('seguimiento/horario/modificarfranja') }}">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Horario - Ficha</span>
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
									<div class="row">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="col-lg-4 col-lg-push-4 col-md-6 col-md-push-3 col-sm-12 col-xs-12">
    											<center><label>Trimestre Acad&eacute;mico: </label><center>
    											<div class="row">
    												<div class="col-md-4">
    											    	<small>A&ntilde;o</small>
    													<select name="year" id="years" class="form-control" onchange="filterList(document.getElementById('years').value)" required>
    														@foreach($anioslis as $val)
    															<?php $selecione = ""; ?>
    															@if(isset($year) && $val == $year)
    																<?php $selecione = "selected"; ?>
    														    @else
    															    @if(!isset($year) && $val == date('Y'))
    																<?php $selecione = "selected"; ?>
                                                                    @endif
    															@endif
    															<option value="{{$val}}" {{$selecione}}>{{$val}}</option>
    														@endforeach
    													</select>
    												</div>
    												<div class="col-md-8">
    													<small>Trimestre - Inicio - Fin</small>
    													<select class="form-control trimestres" name="pla_fec_tri_id" required>
    														<option value = "">Seleccione...</option>
    														@if(isset($pla_fec_tri_id))
    															<option id="todosLosTrimestres" <?php echo $pla_fec_tri_id =="todos" ? "selected" :  "" ?> value = "todos">Todos los trimestres</option>
    															@foreach($trimestres as $val)
    																<?php 
    																$selecione = "";
    																$contenido = "";
    																?>
    																@if($pla_fec_tri_id == $val->pla_fec_tri_id && isset($year))
    																	<?php $selecione = "selected"; ?>
    																@endif
    																@if(isset($year) && $val->pla_fec_tri_year != $year)
    																  <?php $contenido ="display:none"; ?>
    																@else
    																  @if(!isset($year) && $val->pla_fec_tri_year != date('Y'))
    																  	<?php $contenido ="display:none"; ?>
    																  @endif
    																@endif
    																<option <?php echo $selecione; ?> value="{{ $val->pla_fec_tri_id }}" style="{{$contenido}}">{{ $val->pla_fec_tri_trimestre }} - {{ $val->pla_fec_tri_fec_inicio }} - {{ $val->pla_fec_tri_fec_fin }}</option>
    															@endforeach
    														@else
    													        <option id="todosLosTrimestres" value = "todos">Todos los trimestres</option>
    															@foreach($trimestres as $val)
    																<option value="{{ $val->pla_fec_tri_id }}" style="display:none;">{{ $val->pla_fec_tri_trimestre }} - {{ $val->pla_fec_tri_fec_inicio }} - {{ $val->pla_fec_tri_fec_fin }}</option>
    															@endforeach
    														@endif
    													</select>
    												</div>
    											</div>
									    	</div>
										</div>
										
										@if($rol == 5 or $rol == 3 or $rol == 0 or $rol == 8 or $rol == 10 or $rol == 19 or $rol == 16)
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:15px;">
											<div class="col-lg-4 col-lg-push-4 col-md-6 col-md-push-3 col-sm-12 col-xs-12">
												<label>Coordinaci&oacute;n: </label><br>
												<select class="form-control" name="par_identificacion_coordinador" id ="par_identificacion_coordinador">
													<option value = "">Seleccionar ficha(s)</option>
													@if(isset($par_identificacion_coordinador))
														@foreach($coordinadores as $val)
															<?php $selected = ''; ?>
															@if($par_identificacion_coordinador == $val->par_identificacion)
															<?php $selected = 'selected'; ?>
															@endif
															<option <?php echo $selected; ?> value="{{ $val->par_identificacion }}">{{ ucwords(mb_strtolower($val->par_nombres)) }} {{ ucwords(mb_strtolower($val->par_apellidos)) }}</option>
														@endforeach
														@if($par_identificacion_coordinador == 'todas')
															<option selected value="todas">Todas las coordinaciones</option>
														@else
															<option value="todas">Todas las coordinaciones</option>
														@endif
													@else
														@foreach($coordinadores as $val)
														<option value="{{ $val->par_identificacion }}">{{ ucwords(mb_strtolower($val->par_nombres)) }} {{ ucwords(mb_strtolower($val->par_apellidos)) }}</option>
														@endforeach
														<option value="todas">Todas las coordinaciones</option>	
													@endif
												</select>
											</div>
										</div>
										@endif
										<?php
											$style = '';
											$required = '';
											if(isset($par_identificacion_coordinador) and $par_identificacion_coordinador != ''){
												$style = 'style="display:none;"';
											}else{
												$required = 'required';
											}
										?>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:15px;">
											<div class="col-lg-4 col-lg-push-4 col-md-6 col-md-push-3 col-sm-12 col-xs-12">
												<label>Modalidad: </label><br>
												<select class="form-control" name="modalidad" required id="modalidad" data-url="{{ url('seguimiento/horario/modalidad') }}">
													<?php 
													    $sel1=$sel2=$sel3="";
														if (isset($modalidad)) {
														    if ($modalidad == 1) {
																$sel1="selected";
															}else if($modalidad == 2){
																$sel2="selected";
															}else{
																$sel3="selected";
															}
														}
													?>
													<option value="1" {{$sel1}}>1 - Presencial</option>
													<option value="2" {{$sel2}}>2 - Virtual</option>
													<option value="3" {{$sel3}}>Ambas Modalidades</option>
												</select>
											</div>
										</div>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="contenedor_seleccionar_ficha" <?php echo $style; ?>>
											<div style="font-size:12px;margin-top:10px;" class="col-lg-6 col-lg-push-3 col-md-8 col-md-push-2 col-sm-12 col-xs-12">
												<label style="font-size:14px;">Seleccione ficha(s): </label><br>
												<select class="js-example-basic-multiple" id="pla_fic_id" name="pla_fic_id[]" multiple="multiple" <?php echo $required; ?> >
													@if(isset($pla_fic_id))
														@foreach($fichas as $val)
															<?php $selected = ''; ?>
															@if(in_array($val->pla_fic_id, $pla_fic_id))
															<?php $selected = 'selected'; ?>
															@endif
															<option <?php echo $selected; ?> value="{{ $val->pla_fic_id }}">{{ $val->fic_numero }} - {{ $val->prog_nombre }} - {{ $val->pla_fra_descripcion }}</option>
														@endforeach
													@else
														@foreach($fichas as $val)
															<option value="{{ $val->pla_fic_id }}">{{ $val->fic_numero }} - {{ $val->prog_nombre }} - {{ $val->pla_fra_descripcion }}</option>
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
												<a href="{{ url('seguimiento/horario/index') }}" class ="form-control btn btn-default">Limpiar filtro</a>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="row" id="url" data-url="{{ url('seguimiento/horario/actividadesinstructor') }}">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							@if(isset($horarios))
							<form action="{{ url('seguimiento/horario/mieliminarxd') }}" id="formulario" data-url-modificar-actividades="{{ url('seguimiento/horario/modificaractividades') }}" data-url-agregar="{{ url('seguimiento/horario/contenidomodalagregar') }}" data-url-modificar="{{ url('seguimiento/horario/contenidomodalmodificar') }}" method="POST">
								<input name="_token" type="hidden" value="{{ csrf_token() }}">
									@foreach($horarios as $hor)
										@if(isset($programacion[$hor->pla_fic_id]))
											<?php
											if($programacion[$hor->pla_fic_id] != ''){
											?>
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
												<?php
													$host= $_SERVER["HTTP_HOST"];
													$url= $_SERVER["REQUEST_URI"];
													$url ="http://" . $host . $url.'&generar=PDF';
												?>
												<a title="Exportar a PDF" target="_blank" href="<?php echo $url; ?>"><img style="cursor:pointer;" src="{{ asset('img/horario/PDF2.png') }}"></a>
											</div>
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" >
												<h5 style="margin:0px;"><label>Ficha:</label> {{ $hor->fic_numero }} <label>&nbsp;Modalidad: {{$hor->Modalidad}} &nbsp;{{ $hor->prog_sigla }}</label>
													@if(($rol == 5 or $rol == 0) and $cc != '31378440')
													<input value="{{ $hor->pla_fic_consecutivo_ficha }}" class="valorCambioInput" style="width: 60px;border-radius: 4px;padding-left: 5px;border: 1px solid;font-size: 12px;" type="number">
													<a style="cursor:pointer;" class="cambioInput" data-id="{{ $hor->pla_fic_id}}">Guardar</a>
													@else
													{{ $hor->pla_fic_consecutivo_ficha }}
													@endif
												</h5>
												<h6 style="margin:0px;">{{ $hor->niv_for_nombre }} en {{ ucwords(mb_strtolower($hor->prog_nombre)) }}</h6>
												<h6 style="margin:0px;">
													<label>Lectiva trimestre(s):</label> {{ $hor->pla_fic_can_trimestre }} &nbsp;&nbsp;&nbsp;
													<label>Tipo oferta:</label>
													@if(($rol == 5 or $rol == 0) and $cc != '31378440')
														<select class="cambioSelect" data-id="{{ $hor->pla_fic_id}}">
															@foreach($tipo_oferta as $tip)
															<?php
																$selected = '';
																if($tip->pla_tip_ofe_id == $hor->pla_tip_ofe_id){
																	$selected = 'selected';
																}
															?>
															<option {{ $selected }} value="{{ $tip->pla_tip_ofe_id }}">{{ $tip->pla_tip_ofe_descripcion }}</option>
															@endforeach
														</select>
													@else
														{{ $hor->pla_tip_ofe_descripcion }}
													@endif
													&nbsp;&nbsp;&nbsp;<label>Franja horaria:</label>
													@if($rol == 8 or $rol == 0)
														<select class="cambiarFranja" data-id="{{ $hor->pla_fic_id}}">
															@foreach($franjasArray as $llaveFranja => $valorFranja)
															<?php
																$selected = '';
																if($llaveFranja == $hor->pla_fra_id){
																	$selected = 'selected';
																}
															?>
															<option {{ $selected }} value="{{ $llaveFranja }}">{{ $valorFranja }}</option>
															@endforeach
														</select>
													@else
													 	{{ $hor->pla_fra_descripcion }}
													@endif
												</h6>
												@if(($rol == 5 or $rol == 0) and $cc != '31378440')
													<h6 style="margin:0px;padding: 3px 0px 5px 0px;">
														<strong>Instructor l&iacute;der</strong>
														<?php
															$estilosLider = '';
															if($hor->pla_ins_lider == ''){
																$estilosLider = 'color: white;background: red;padding: 2px;';
															}
														?>
														<select style="{{ $estilosLider }}" data-ficha = '{{ $hor->fic_numero }}' data-url = "{{ url('seguimiento/horario/asignaractividad') }}"  name="asignar_actividades" class="asignarActividades">
															<option value="">Sin asignar</option>
															@if($hor->pla_ins_lider == '')
																<?php
																	if(!isset($optionsAactividades)){
																		$optionsAactividades = '';
																		foreach($instructores as $ins){
																			$optionsAactividades .= '<option value="'. $ins->par_identificacion .'">'. $ins->par_nombres .' '. $ins->par_apellidos .'</option>';
																		}
																	}
																	echo $optionsAactividades;
																?>
															@else
																@foreach($instructores as $ins)
																<?php
																	$selected = '';
																	if($ins->par_identificacion == $hor->pla_ins_lider){
																		$selected = 'selected';
																	}
																?>
																<option {{ $selected }} value="{{ $ins->par_identificacion }}">{{ $ins->par_nombres }} {{ $ins->par_apellidos }}</option>
																@endforeach
															@endif
														</select>
													</h6>
												@endif
												<h6 style="margin:0px;">
													<label>Creado por:</label> {{ ucwords(mb_strtolower($hor->par_nombres)) }} {{ ucwords(mb_strtolower($hor->par_apellidos)) }} &nbsp;&nbsp;
													<label>Fecha creaci&oacute;n:</label> {{ $hor->pla_fic_fec_creacion }}
												</h6>
												@if(($rol == 5 or $rol == 0) and $cc != '31378440')
												@if(isset($programacion[$hor->pla_fic_id]))
												<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="padding-bottom: 5px;">
													<a style="cursor:pointer;font-size:14px;color:red;" name="pla_fic_id" data-id="{{ $hor->pla_fic_id }}" data-programa="{{ ucwords(mb_strtolower($hor->prog_nombre)) }}" data-ficha="{{ $hor->fic_numero }}" class="eliminarHorario">Eliminar todo el horario</a>
												
												</div>
												@endif
												@endif
											</div>
											<?php
											}else{
											?>
											<div style="margin:0px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
												<div  class="col-lg-6 col-lg-push-3 col-md-8 col-md-push-2 col-sm-12 col-xs-12 text-center">
													<div style="border: 1px solid black;" class="alert alert-success">
														<h5 style="margin:0px;" class="text-center">La ficha <strong>{{ $hor->fic_numero }}</strong> no ha iniciado o ya termino su etapa pr&aacute;ctica.</h5>
													</div>
												</div>
											</div>
											<?php
											}
											
											if($hor->pla_fra_id == 1){
												$ficha_franja_inicio = 6;
												$ficha_franja_fin = 12;
											}else if($hor->pla_fra_id == 2){
												$ficha_franja_inicio = 12;
												$ficha_franja_fin = 18;
											}else if($hor->pla_fra_id == 3){
												$ficha_franja_inicio = 18;
												$ficha_franja_fin = 22;
											}else if($hor->pla_fra_id == 4){
												$ficha_franja_inicio = 6;
												$ficha_franja_fin = 22;
											}else{
												$ficha_franja_inicio = 0;
												$ficha_franja_fin = 0;
											} 
											
											$cantidad_trimestres = $hor->pla_fic_can_trimestre; $cantidad_trimestres +=2;?>
											
											@for($l=1; $l<=$cantidad_trimestres; $l++)
												@if(isset($programacion[$hor->pla_fic_id][$l]))
													@foreach($programacion[$hor->pla_fic_id][$l]["fechas_inicio"] as $key10 => $val10)
														<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="padding-bottom: 2px;">
															<h5 style="margin:0px;">
																<strong>Ficha</strong>: {{ $hor->fic_numero }}
																<strong>Trimestre</strong> # {{ $l }}
																<strong>Etapa <?php echo $tipo_trimestre = $l <= $hor->pla_fic_can_trimestre ? 'lectiva' : 'productiva' ?></strong>
															</h5>
															<h6 style="margin:0px;">
																<strong>Fecha inicio:</strong> {{ $programacion[$hor->pla_fic_id][$l]["fechas_inicio"][$key10] }} 
																<strong>Fecha fin:</strong> {{ $programacion[$hor->pla_fic_id][$l]["fechas_fin"][$key10] }}
															</h6>
															@if($cc == $hor->pla_ins_lider)
															<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
																<a style="cursor:pointer;font-size:14px;" name="pla_fic_id" data-programa="{{ ucwords(mb_strtolower($hor->prog_nombre)) }}" data-ficha="{{ $hor->fic_numero }}" data-trimestre="{{ $l }}" data-fec-fin="{{ $programacion[$hor->pla_fic_id][$l]['fechas_fin'][$key10] }}" data-fec-inicio="{{ $programacion[$hor->pla_fic_id][$l]['fechas_inicio'] }}" data-pla-fic-id="{{ $hor->pla_fic_id }}" value="{{ $hor->pla_fic_id }}" class="modificarActividades"></a>
															</div>
															@endif
															
															<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
															    @if(($rol == 5 or $rol == 0) and $cc != '31378440')
																    <?php if(isset($programacionDetalle[$hor->pla_fic_id][$l][$val10])){ ?>
																        <a style="cursor:pointer;font-size:14px;" name="pla_fic_id" data-datos='<?php echo implode(",",$programacionDetalle[$hor->pla_fic_id][$l][$val10]); ?>' data-programa="{{ ucwords(mb_strtolower($hor->prog_nombre)) }}" data-ficha="{{ $hor->fic_numero }}" data-trimestre="{{ $l }}" data-fec-fin="{{ $programacion[$hor->pla_fic_id][$l]['fechas_fin'][$key10] }}" data-fec-inicio="{{ $programacion[$hor->pla_fic_id][$l]['fechas_inicio'][$key10] }}" data-pla-fic-id="{{ $hor->pla_fic_id }}" value="{{ $hor->pla_fic_id }}" class="modificar">Modificar</a> &nbsp;&nbsp;
																    <?php } ?>
																    <a style="cursor:pointer;font-size:14px;" name="pla_fic_id" data-programa="{{ ucwords(mb_strtolower($hor->prog_nombre)) }}" data-ficha="{{ $hor->fic_numero }}" data-trimestre="{{ $l }}" data-fec-fin="{{ $programacion[$hor->pla_fic_id][$l]['fechas_fin'][$key10] }}" data-fec-inicio="{{ $programacion[$hor->pla_fic_id][$l]['fechas_inicio'][$key10] }}" data-pla-fic-id="{{ $hor->pla_fic_id }}" value="{{ $hor->pla_fic_id }}" class="agregar">Agregar</a> &nbsp;&nbsp; 
																    <a style="cursor:pointer;font-size:14px;" name="pla_fic_id" data-programa="{{ ucwords(mb_strtolower($hor->prog_nombre)) }}" data-ficha="{{ $hor->fic_numero }}" data-trimestre="{{ $l }}" data-fec-fin="{{ $programacion[$hor->pla_fic_id][$l]['fechas_fin'][$key10] }}" data-fec-inicio="{{ $programacion[$hor->pla_fic_id][$l]['fechas_inicio'][$key10] }}" data-pla-fic-id="{{ $hor->pla_fic_id }}" value="{{ $hor->pla_fic_id }}" class="modificarActividades">Actividades</a>&nbsp;&nbsp;
                                                                @endif
                                                                <a style="cursor:pointer;font-size:14px;" id="aprendices" data-url="{{url('seguimiento/horario/listadoaprendices')}}" data-ficha="{{ $hor->fic_numero }}" data-programa="{{ ucwords(mb_strtolower($hor->prog_nombre)) }}">Listado de aprendices</a>
															</div>
														</div>
														@if(($rol == 5 or $rol == 0 or $rol==8) and $cc != '31378440')
															@if(isset($arrayErrores[$hor->pla_fic_id][$l]))
																<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="padding: 0px 75px;">
																	<div class="alert alert-danger" style="overflow-y:auto;height: 55px;font-size:11px;padding: 0px;border-color:#999999; background-color:#f1f1f1;color:black">
																		<strong style="color:#de1d19;">Alertas!</strong><br>
																		<ol>
																			@foreach($arrayErrores[$hor->pla_fic_id][$l] as $key => $val)
																			<li><?php echo $val; ?></li>
																			@endforeach
																		</ol>
																	</div>
																</div>
															@endif
														@endif
														<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="padding: 0px 75px;">
															<table style="margin-bottom:15px;" data-plaFicId="{{ $hor->pla_fic_id }}" data-ficha="{{ $hor->fic_numero }}" data-url="{{ url('seguimiento/horario/contenidomodal') }}"  data-programa="{{ ucwords(mb_strtolower($hor->prog_nombre)) }}" data-trimestre="{{ $l }}" data-fec-fin="{{ $programacion[$hor->pla_fic_id][$l]['fechas_fin'][$key10] }}" data-fec-inicio="{{ $programacion[$hor->pla_fic_id][$l]['fechas_inicio'][$key10] }}" class="table table-bordered table-hover">
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
																	if(isset($programacion[$hor->pla_fic_id][$l]['hora_inicio'][$key10])){
																	?>
																	@for($j=6; $j<22; $j++)
																		<tr>
																			<th style='padding:5px;font-size:11px;text-align:center;'> {{ $j.":00 - ".($j+1).":00" }} </th>
																			@for($i=1; $i<7; $i++)
																				@if($horas[$i]['can'] == $horas[$i]['total'])
																					<?php $control = false; ?>
																					@foreach($programacion[$hor->pla_fic_id][$l]["dia_id"][$key10] as $key1 => $val1)
																						<?php
																						$horaInicio = $programacion[$hor->pla_fic_id][$l]["hora_inicio"][$key10][$key1];
																						if($val1 == $i and $horaInicio == $j){
																							$control = true;
																							$instructor_cedula = $programacion[$hor->pla_fic_id][$l]["instructor_cedula"][$key10][$key1];
																							$instructor_nombre = $programacion[$hor->pla_fic_id][$l]["instructor_nombre"][$key10][$key1];
																							$nombre_largo = $programacion[$hor->pla_fic_id][$l]["nombre_largo"][$key10][$key1];
																							$ambiente = $programacion[$hor->pla_fic_id][$l]["pla_amb_descripcion"][$key10][$key1];
																							$rowspan = $programacion[$hor->pla_fic_id][$l]["horas_totales"][$key10][$key1];
																							if(isset($actividades_programadas[$hor->pla_fic_id][$programacion[$hor->pla_fic_id][$l]["fechas_inicio"][$key10]][$instructor_cedula])){
																								$actividad_clase = 'actividad';
																								$mensaje = 'Ver actividad';
																								$celda = 'background:#087b76';
																							}else{
																								$actividad_clase = '';
																								$mensaje = 'Sin actividad';
																								$celda = 'background:red';
																							}
																							break;
																						}
																						?>
																					@endforeach

																					@if($control)
																						<td title="{{ $nombre_largo }} - {{ $mensaje }}" data-tipo="ficha" data-cc="{{ $instructor_cedula }}" data-instructor="{{ $instructor_nombre }}" rowspan="{{ $rowspan }}" class="{{ $actividad_clase }}" style="padding: 0px;vertical-align: middle;cursor:pointer;color:white;{{ $celda  }}"> 
																							<h6 style="margin:2px 0px 1px 0px;font-size:10.5px;">{{ $instructor_nombre }}</h6>
																							<h6 style="margin:0px;font-size:10.5px;font-weight: bold;">{{ $ambiente }}</h6>
																							<h6 style="margin:1px 0px 2px 0px;font-size:9px;">{{ $mensaje }}</h6>
																						</td>
																						<?php	$horas[$i]['can'] -= $rowspan; ?>
																					@else
																					   <?php
																							$estilos = '';
																							$title = '';
																							if($i != 6 and ($j >= $ficha_franja_inicio and $j < $ficha_franja_fin) and $tipo_trimestre == 'lectiva'){
																								$estilos = 'background: #ec7114;cursor:pointer;';
																								$title = 'Hora sin asignar';
																							}
																						?>
																						<td title="{{ $title }}" style="{{ $estilos }}"> </td>
																						<?php	$horas[$i]['can'] --; ?>
																					@endif
																				@endif
																				<?php $horas[$i]['total']--; ?>
																			@endfor
																		</tr>
																	@endfor
																	<?php
																	}else{
																	?>
																	
																	@for($j=6; $j<22; $j++)
																		<tr>
																			<th style='padding:5px;font-size:11px;text-align:center;'> {{ $j.":00 - ".($j+1).":00" }} </th>
																			@for($i=1; $i<7; $i++)
																			    <?php
																					$estilos = '';
																					$title = '';
																					if($i != 6 and ($j >= $ficha_franja_inicio and $j < $ficha_franja_fin) and $tipo_trimestre == 'lectiva'){
																						$estilos = 'background: #ec7114;cursor:pointer;';
																						$title = 'Hora sin asignar';
																					}
																				?>
																				<td title="{{ $title }}" style="{{ $estilos }}" </td>
																			@endfor
																		</tr>
																	@endfor
																	<?php } ?>
																</tbody>
															</table>
														</div>
													@endforeach
												@endif
											@endfor
										@endif
									@endforeach
							</form>
							@else
								@if(isset($pla_fic_id))
								<h5 class="text-center">No se encontraron registros de la(s) ficha(s) o el trimestre seleccionado</h5>
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
						<strong class="modal-title" style="font-size:15px;">Trimestre ficha # </strong><small id="trimestre"  style="font-size:16px;"></small><br>
						<strong class="modal-title" style="font-size:15px;">Programa: </strong><small id="programa"  style="font-size:16px;"></small><br>
						<strong class="modal-title" style="font-size:15px;">Instructor: </strong><small id="instructor"  style="font-size:16px;"></small>&nbsp;&nbsp;
						<strong class="modal-title" style="font-size:15px;">CC: </strong><small id="cc"  style="font-size:16px;"></small><br>
						<strong class="modal-title" style="font-size:15px;">Fecha inicio: </strong><small id="fecInicio"  style="font-size:16px;"></small>&nbsp;&nbsp;
						<strong class="modal-title" style="font-size:15px;">Fecha fin: </strong><small id="fecFin"  style="font-size:16px;"></small><br>
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

	<!-- Modal2 -->
	<div id="myModal2" data-pla-fic-id="" data-url-agregar="{{ url('seguimiento/horario/agregarcontenido') }}" data-url-eliminar="{{ url('seguimiento/horario/eliminar') }}" data-url="{{ url('seguimiento/horario/contenidomodificar') }}" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width:76%">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="text-center">
					<button type="button" class="close" data-dismiss="modal" style="margin:8px 14px 0px 0px;">&times;</button>
					<div class="alert alert-success" style="background-color:#087b76;margin:0px;color:white;border:black;border-radius:0px;">
						<span style="display: block;position: fixed;">Modificar horario</span>
						<strong class="modal-title" style="font-size:15px;">Trimestre # </strong><small id="trimestre"  style="font-size:16px;"></small><br>
						<strong class="modal-title" style="font-size:15px;">Ficha: </strong><small id="ficha" style="font-size:16px;"></small><br>
						<strong class="modal-title" style="font-size:15px;">Programa: </strong><small id="programa"  style="font-size:16px;"></small><br>
						<strong class="modal-title" style="font-size:15px;">Fecha inicio: </strong><small id="fecInicio"  style="font-size:16px;"></small>&nbsp;&nbsp;
						<strong class="modal-title" style="font-size:15px;">Fecha fin: </strong><small id="fecFin"  style="font-size:16px;"></small>
					</div>
				</div>
				<div class="modal-body" >
					<form id="formularioModificarHorario" data-url="{{ url('seguimiento/horario/update') }}">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="margin-bottom:-10px;">
							<table class="table table-bordered table-hover text-center">
								<thead>
									<tr>
										<th style="padding:5px;" class="text-center"><input style="cursor:pointer;" class="filaModificar" type="checkbox" value="todo"></th>
										<th style="padding:5px;" class="text-center">Tipo registro</th>
										<th style="padding:5px;" class="text-center">D&iacute;a</th>
										<th style="padding:5px;" class="text-center">Inicio - Fin</th>
										<th style="padding:5px;" class="text-center">Horas</th>
										<th style="padding:5px;" class="text-center">Instructor</th>
										<th style="padding:5px;" class="text-center">Ambiente</th>
									</tr>
								</thead>
								<tbody id="contenido">
								<tbody>
							</table>
						</div>
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="row text-center">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="row">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-bottom:10px;">
											<div class="alert alert-success" style="padding: 5px;background-color: #ececec;margin:0px;color: black;border: 1px solid black;">
												<small>Para habilitar los botones "<strong style="color:#42617A;">Modificar</strong>" o "<strong style="color:#b3110e;">Eliminar</strong>" debes seleccionar uno o varios registros.</small>
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								    <div class="col-lg-2 col-lg-push-4 col-md-2 col-md-push-4 col-sm-2 col-sm-push-4 col-xs-12 text-center">
										<a id="botonModificar" disabled class="btn btn-primary form-control">Modificar</a>
									</div>
									<div class="col-lg-2 col-lg-push-4 col-md-2 col-md-push-4 col-sm-2 col-sm-push-4 col-xs-12 text-center">
										<a id="botonEliminar" disabled class="btn btn-danger form-control">Eliminar</a>
									</div>
								</div>
							</div>
						</div>
						<input id="token" type="hidden" name="_token" value="{{ csrf_token() }}">
						<div style="display:none;" id="contenidoModificar" date-url-ins-amb="{{ url('seguimiento/horario/actualizarambins') }}" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
						</div>
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class='col-lg-4 col-lg-push-4 col-md-4 col-md-push-4 col-sm-12 col-xs-12 text-center'>
								<a style="display:none;" disabled id="guardarModificado" class="basura btn btn-success form-control">Guardar cambios</a>
							</div>
						</div>
						<div style="display:none;" id="notificaciones" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
								<h5><strong>Notificaciones</strong></h5>
							</div>
							<div class="col-lg-8 col-lg-push-2 col-md-10 col-md-push-1 col-sm-12 col-xs-12 text-center">
								<div id="contenidoNotificaciones" style="border: solid 1px black;background:#ececec;color:black;" class="alert alert-info">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button style="margin:0px;" class="btn btn-danger btn-xs" data-dismiss="modal">Cerrar</button>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal3 -->
	<div id="myModal3" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="text-center">
					<button type="button" class="close" data-dismiss="modal" style="margin:8px 14px 0px 0px;">&times;</button>
					<div class="alert alert-success" style="background-color:#bb1d1d;margin:0px;color:white;border:black;border-radius:0px;">
						<span style="display: block;position: fixed;">Eliminar horario</span>
						<strong class="modal-title" style="font-size:15px;">Ficha: </strong><small id="ficha" style="font-size:16px;"></small><br>
						<strong class="modal-title" style="font-size:15px;">Programa: </strong><small id="programa"  style="font-size:16px;"></small><br>
					</div>
				</div>
				<div class="modal-body text-center">
					<p>Esta seguro que desea eliminar todo este horario?</p>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="col-lg-6 col-lg-push-3 col-md-6 col-sm-6 col-xs-12">
								<form id="formularioEliminar" data-url="{{ url('seguimiento/horario/eliminartodoelhorario') }}">
									<label>Contrase���a</label>
									<input autofocus id="clave" required placeholder="Escriba su contrase���a" class="form-control" type="password" name="clave"><br>
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<input type="hidden" id="id_horario" name="pla_fic_id">
									<button class="btn btn-danger btn-xs">Eliminar</button>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
	</div>

    <!-- Modal4 -->
	<div id="myModal4" data-pla-fic-id="" data-url-agregar="{{ url('seguimiento/horario/agregarcontenido') }}" data-url-eliminar="{{ url('seguimiento/horario/eliminar') }}" data-url="{{ url('seguimiento/horario/contenidomodificar') }}" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width:80%">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="text-center">
					<button type="button" class="close" data-dismiss="modal" style="margin:8px 14px 0px 0px;">&times;</button>
					<div class="alert alert-success" style="background-color:#087b76;margin:0px;color:white;border:black;border-radius:0px;">
						<span style="display: block;position: fixed;">Agregar horario</span>
						<strong class="modal-title" style="font-size:15px;">Trimestre # </strong><small id="trimestre"  style="font-size:16px;"></small><br>
						<strong class="modal-title" style="font-size:15px;">Ficha: </strong><small id="ficha" style="font-size:16px;"></small><br>
						<strong class="modal-title" style="font-size:15px;">Programa: </strong><small id="programa"  style="font-size:16px;"></small><br>
						<strong class="modal-title" style="font-size:15px;">Fecha inicio: </strong><small id="fecInicio"  style="font-size:16px;"></small>&nbsp;&nbsp;
						<strong class="modal-title" style="font-size:15px;">Fecha fin: </strong><small id="fecFin"  style="font-size:16px;"></small>
					</div>
				</div>
				<div class="modal-body">
					<form id="formularioAgregar" data-url="{{ url('seguimiento/horario/agregarcontenido') }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
								<div class="col-lg-2 col-md-2 col-sm-6">
									<div class="row">
										<label>Tipo de registro: </label>
										<select required name="tipo_registro" id="tipo_registro" class="form-control">
											<option value=''>Seleccione...</option>
											<option value='1'>T&eacute;cnico</option>
											<option value='2'>Transversal</option>
											<option value='3'>Etapa pr&aacute;ctica</option>
											<option value='4'>Complementaria</option>
										</select>
									</div>
								</div>
							</div>
							<div style="display:none;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center clase_tecnica" id="contenidoModal4">
								<div class="row" style='padding-top: 10px;padding-bottom: 15px;'>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
										<h5>Registrar clase t&eacute;cnica</h5>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-6">
										<label>D&iacute;a</label>
										<select style="font-size:12px;" name="lec_dia" class="lectiva form-control dia">
											<option value="">Seleccione...</option>
											<?php foreach($diaOrtografia as $key => $val){
												echo '<option value="'.($key+1).'">'.$val.'</option>';
											}
											?>
										</select>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-6">
										<label>Hora inicio</label>
										<select style="font-size:12px;" name="lec_hora_inicio" class="lectiva form-control hora_inicio">
											<option value="">Seleccione...</option>
											<?php for($i=6; $i<=21; $i++){
												echo '<option value="'.$i.'">'.$i.':00</option>';
											}
											?>
										</select>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-6">
										<label>Hora fin</label>
										<select style="font-size:12px;" name="lec_hora_fin" class="lectiva form-control hora_fin">
											<option value="">Seleccione...</option>
											<?php for($i=7; $i<=22; $i++){
												echo '<option value="'.$i.'">'.$i.':00</option>';
											}
											?>
										</select>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6">
										<label>Instructor</label>
										<input name="lec_par_identificacion" style="font-size:12px;" autocomplete="off" class="lectiva form-control par_identificacion" list="browsers" placeholder="Escriba nombre o documento...">
										<datalist id="browsers">
                                            @foreach($instructores as $usu)
                                            	@if($usu->par_identificacion != 777777777)
                                                    <option value="{{$usu->par_identificacion}}">{{$usu->par_nombres}} {{$usu->par_apellidos}}</option>
                                                @endif
                                            @endforeach
                                        </datalist>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6">
										<label>Ambiente</label>
										<select style="font-size:12px;" name="lec_pla_amb_id" class="lectiva form-control pla_amb_id">
											<option value="">Seleccione...</option>
											<?php foreach($ambientes as $val){
												echo '<option value="'.$val->pla_amb_id.'">'.$val->pla_amb_descripcion.'</option>';
											}
											?>
										</select>
									</div>
								</div>
							</div>

							<div style="display:none;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center clase_transversal" id="contenidoModal4">
								<div class="row" style='padding-top: 10px;padding-bottom: 15px;'>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
										<h5>Registrar clase transversal</h5>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-6">
										<label>D&iacute;a</label>
										<select style="font-size:12px;" name="tra_dia" class="transversal form-control dia">
											<option value="">Seleccione...</option>
											<?php foreach($diaOrtografia as $key => $val){
												echo '<option value="'.($key+1).'">'.$val.'</option>';
											}
											?>
										</select>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-6">
										<label>Hora inicio</label>
										<select style="font-size:12px;" name="tra_hora_inicio" class="transversal form-control hora_inicio">
											<option value="">Seleccione...</option>
											<?php for($i=6; $i<=21; $i++){
												echo '<option value="'.$i.'">'.$i.':00</option>';
											}
											?>
										</select>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-6">
										<label>Hora fin</label>
										<select style="font-size:12px;" name="tra_hora_fin" class="transversal form-control hora_fin">
											<option value="">Seleccione...</option>
											<?php for($i=7; $i<=22; $i++){
												echo '<option value="'.$i.'">'.$i.':00</option>';
											}
											?>
										</select>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6">
										<label>Instructor</label>
										<input name="tra_par_identificacion" style="font-size:12px;" autocomplete="off" class="transversal form-control par_identificacion" list="instrucotr_transversal" placeholder="Escriba nombre o documento...">
										<datalist id="instrucotr_transversal">
                                            @foreach($instructores_transversal as $usu)
                                                <option value="{{$usu->par_identificacion}}">{{$usu->par_nombres}} {{$usu->par_apellidos}}</option>
                                            @endforeach
                                        </datalist>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6">
										<label>Ambiente</label>
										<select style="font-size:12px;" name="tra_pla_amb_id" class="transversal form-control pla_amb_id">
											<option value="">Seleccione...</option>
											<?php foreach($ambientes as $val){
												echo '<option value="'.$val->pla_amb_id.'">'.$val->pla_amb_descripcion.'</option>';
											}
											?>
										</select>
									</div>
								</div>
							</div>
							
							<div style="display:none;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center clase_practica" id="contenidoModal4">
								<div class="row" style='padding-top: 10px;padding-bottom: 15px;'>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
										<h5>Registrar Etapa pr&aacute;ctica</h5>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-6">
										<label>D&iacute;a</label>
										<select style="font-size:12px;" name="pra_dia" class="practica form-control dia">
											<option value="">Seleccione...</option>
											<?php foreach($diaOrtografia as $key => $val){
												echo '<option value="'.($key+1).'">'.$val.'</option>';
											}
											?>
										</select>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-6">
										<label>Hora inicio</label>
										<select style="font-size:12px;" name="pra_hora_inicio" class="practica form-control hora_inicio">
											<option value="">Seleccione...</option>
											<?php for($i=6; $i<=21; $i++){
												echo '<option value="'.$i.'">'.$i.':00</option>';
											}
											?>
										</select>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-6">
										<label>Hora fin</label>
										<select style="font-size:12px;" name="pra_hora_fin" class="practica form-control hora_fin">
											<option value="">Seleccione...</option>
											<?php for($i=7; $i<=22; $i++){
												echo '<option value="'.$i.'">'.$i.':00</option>';
											}
											?>
										</select>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6">
										<label>Instructor</label>
										<input name="pra_par_identificacion" style="font-size:12px;" autocomplete="off" class="practica form-control par_identificacion" list="practica" placeholder="Escriba nombre o documento...">
										<datalist id="practica">
                                            @foreach($instructores as $usu)
                                                <option value="{{$usu->par_identificacion}}">{{$usu->par_nombres}} {{$usu->par_apellidos}}</option>
                                            @endforeach
                                        </datalist>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6">
										<label>Ambiente</label>
										<select style="font-size:12px;" name="pra_pla_amb_id" class="practica form-control pla_amb_id">
											<option value="72">Etapa practica</option>
										</select>
									</div>
								</div>
							</div>
                            
                            <div style="display:none;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center clase_complementaria" id="contenidoModal4">
								<div class="row" style='padding-top: 10px;padding-bottom: 15px;'>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
										<h5>Registrar Complementario</h5>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-6">
										<label>D&iacute;a</label>
										<select style="font-size:12px;" name="com_dia" class="complementaria form-control dia">
											<option value="">Seleccione...</option>
											<?php foreach($diaOrtografia as $key => $val){
												echo '<option value="'.($key+1).'">'.$val.'</option>';
											}
											?>
										</select>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-6">
										<label>Hora inicio</label>
										<select style="font-size:12px;" name="com_hora_inicio" class="complementaria form-control hora_inicio">
											<option value="">Seleccione...</option>
											<?php for($i=6; $i<=21; $i++){
												echo '<option value="'.$i.'">'.$i.':00</option>';
											}
											?>
										</select>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-6">
										<label>Hora fin</label>
										<select style="font-size:12px;" name="com_hora_fin" class="complementaria form-control hora_fin">
											<option value="">Seleccione...</option>
											<?php for($i=7; $i<=22; $i++){
												echo '<option value="'.$i.'">'.$i.':00</option>';
											}
											?>
										</select>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6">
										<label>Instructor</label>
										<input name="com_par_identificacion" style="font-size:12px;" autocomplete="off" class="complementaria form-control par_identificacion" list="complementaria" placeholder="Escriba nombre o documento...">
										<datalist id="complementaria">
                                            @foreach($instructores as $usu)
                                                <option value="{{$usu->par_identificacion}}">{{$usu->par_nombres}} {{$usu->par_apellidos}}</option>
                                            @endforeach
                                        </datalist>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6">
										<label>Ambiente</label>
										<select style="font-size:12px;" name="com_pla_amb_id" class="complementaria form-control pla_amb_id">
											<option value="">Seleccione...</option>
											<option value="88">Ambiente externo / Virtual</option>
											<?php foreach($ambientes as $val){
												if($val->pla_amb_id!=123){
												echo '<option value="'.$val->pla_amb_id.'">'.$val->pla_amb_descripcion.'</option>';
												}
											}
											?>
										</select>
									</div>
									<br>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                           				<label>Descripci&oacute;n del complementario</label>
                                		<textarea required class="form-control complementaria" placeholder="Escriba..." maxlength="1000" rows="5" name="descripcion_complementario"></textarea>
                            		</div>
								</div>
							</div>
                            
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">	
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
									<input type="submit" class="btn btn-success" value="Registrar" id="agregar">
								</div>
								<div style="display:none;" id="notificaciones" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
										<h5><strong>Notificaciones</strong></h5>
									</div>
									<div class="col-lg-8 col-lg-push-2 col-md-10 col-md-push-1 col-sm-12 col-xs-12 text-center">
										<div id="contenidoNotificaciones" style="border: solid 1px black;background:#ececec;color:black;" class="alert alert-info">
										</div>
									</div>
								</div>
							</div>
						</div>


						<div class="modal-footer">
							<button style="margin:0px;" class="btn btn-danger btn-xs" data-dismiss="modal">Cerrar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal5 Actividades -->
	<div id="myModal5" data-pla-fic-id="" data-url-agregar="{{ url('seguimiento/horario/agregarcontenido') }}" data-url-eliminar="{{ url('seguimiento/horario/eliminar') }}" data-url="{{ url('seguimiento/horario/contenidomodificar') }}" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width:94%">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="text-center">
					<button type="button" class="close" data-dismiss="modal" style="margin:8px 14px 0px 0px;">&times;</button>
					<div class="alert alert-success" style="background-color:#087b76;margin:0px;color:white;border:black;border-radius:0px;">
						<span style="display: block;position: fixed;">Actividades</span>
						<strong class="modal-title" style="font-size:14px;">Ficha: </strong><small id="ficha" style="font-size:15px;"></small>
						<strong class="modal-title" style="font-size:14px;">Trimestre # </strong><small id="trimestre"  style="font-size:15px;"></small><br>
						<strong class="modal-title" style="font-size:14px;">Programa: </strong><small id="programa"  style="font-size:15px;"></small><br>
						<strong class="modal-title" style="font-size:14px;">Fecha inicio: </strong><small id="fecInicio"  style="font-size:15px;"></small>&nbsp;&nbsp;
						<strong class="modal-title" style="font-size:14px;">Fecha fin: </strong><small id="fecFin"  style="font-size:15px;"></small>
					</div>
				</div>
				<div class="modal-body">
					<form class="formularios" data-url="{{ url('seguimiento/horario/modificaractividades') }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<div class="row" style="height: 400px;overflow-y: auto;border-bottom: 2px solid black;border-top: 2px solid;">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" id="contenidoModal5">
								<table class="table table-bordered table-hover">
									<thead>
										<tr>
											<th	style="font-size:12px;padding:4px;text-align:center;">#</th>
											<th	style="font-size:12px;padding:4px;text-align:center;">Fase</th>
											<th	style="font-size:12px;padding:4px;text-align:center;">Competencia</th>
											<th style="font-size:12px;padding:4px;text-align:center;">Resultado</th>
											<th style="font-size:12px;padding:4px;text-align:center;">Actividad</th>
											<th style="font-size:12px;padding:4px;text-align:center;">Horas</th>
											<th style="font-size:12px;padding:4px;text-align:center;">Instructor</th>
											<th style="font-size:12px;padding:4px;text-align:center;">Trimestre</th>
										</tr>
									</thead>
									<tbody id="contenido"></tbody>
								</table>
							</div>
						</div>
						<div class="modal-footer">
							<div style="display:none;" id="contenedorMensaje">
								<div id="notificaciones" style="background:#efefef;border: solid 1px;border-radius: 10px;" class="col-lg-6 col-lg-push-3 col-md-6 col-md-push-3 col-sm-8 col-md-push-2 col-xs-12">
									<div class="text-center" style="margin: 5px 0px 5px 0px;" id="mensaje"></div>
								</div>
							</div>
							<button class="btn btn-success btn-xs">Guardar cambios</button>
							<a style="margin:0px;" class="btn btn-danger btn-xs" data-dismiss="modal">Cerrar</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
    <!-- Modal aprendices-->
    <div id="listado" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width:76%">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="text-center">
					<button type="button" class="close" data-dismiss="modal" style="margin:8px 14px 0px 0px;">&times;</button>
					<div class="alert alert-success" style="background-color:#087b76;margin:0px;color:white;border:black;border-radius:0px;">
                        <strong class="modal-title" style="font-size:15px;">Listado de aprendices</strong><small id="fecFin"  style="font-size:16px;"></small><br>
						<strong class="modal-title title-ficha" style="font-size:15px;"></strong><small id="fecFin"  style="font-size:16px;"></small><br>
						<strong class="modal-title title-programa" style="font-size:15px;"></strong><small id="fecFin"  style="font-size:16px;"></small><br>
					</div>
				</div>
				<div class="modal-body">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th style="text-align:center;">Documento</th>
								<th style="text-align:center;">Nombres</th>
								<th style="text-align:center;">Apellidos</th>
								<th style="text-align:center;">Correo</th>
								<th style="text-align:center;">Telefono</th>
							</tr>
						</thead>
						<tbody id="listado_ficha"></tbody>
					</table>
				</div>
				<div class="modal-footer">
				    <a style="margin:0px;" class="btn btn-info btn-xs btn-listado">Descargar</a>
					<button style="margin:0px;" class="btn btn-danger btn-xs" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('plugins-js')
	<script type="text/javascript">
		$(document).ready(function () {
		    $(document).on('change','.cambiarFranja', function(){
		        var id = $(this).attr('data-id');
		        var valor = $(this).val();
		        var token = $('#urls').attr('data-token');
		        var url = $('#urls').attr('data-cambio-franja');

		        $.post(url, {'_token':token,'valor':valor,'id':id}, function(respuesta){
		            alert(respuesta);
		        });
		    });
		    
			$(document).on('change','#tipo_registro',function(){
				var valor = $(this).val();
				if(valor == 1){
					$('.lectiva').attr('required', true);
					$('.transversal').removeAttr('required');
					$('.practica').removeAttr('required');
					$('.clase_transversal').css('display', 'none');
					$('.clase_practica').css('display', 'none');
					$('.clase_tecnica').css('display', 'block');
					$('.complementaria').removeAttr('required');
					$('.clase_complementaria').css('display', 'none');
				}else if(valor == 2){
					$('.transversal').attr('required', true);
					$('.practica').removeAttr('required');
					$('.lectiva').removeAttr('required');
					$('.clase_tecnica').css('display', 'none');
					$('.clase_practica').css('display', 'none');
					$('.clase_transversal').css('display', 'block');
					$('.complementaria').removeAttr('required');
					$('.clase_complementaria').css('display', 'none');
				}else if(valor == 3){
					$('.practica').attr('required', true);
					$('.transversal').removeAttr('required');
					$('.lectiva').removeAttr('required');
					$('.clase_tecnica').css('display', 'none');
					$('.clase_transversal').css('display', 'none');
					$('.clase_practica').css('display', 'block');
					$('.complementaria').removeAttr('required');
					$('.clase_complementaria').css('display', 'none');
				}else if(valor == 4){
					$('.complementaria').attr('required', true);
					$('.transversal').removeAttr('required');
					$('.lectiva').removeAttr('required');
					$('.practica').removeAttr('required');
					$('.clase_tecnica').css('display', 'none');
					$('.clase_transversal').css('display', 'none');
					$('.clase_practica').css('display', 'none');
					$('.clase_complementaria').css('display', 'block');
				}
			});

			$("#formularioAgregar").submit(function(e){
		        e.preventDefault();
		        var url = $(this).attr("data-url");
		        var datos = $(this).serialize();
		        var fechaInicio = $("#myModal4").find("#fecInicio").html();
		        var fechaFin = $("#myModal4").find("#fecFin").html();
		        var ficha = $("#myModal4").find("#ficha").html();
		        var trimestre = $("#myModal4").find("#trimestre").html();
		        var fic_id = $("#myModal4").attr("data-pla-fic-id");
		        //console.log(datos);
		        $.ajax({
		            url:url, type:"POST", 
		            data:datos+"&trimestre="+trimestre+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&ficha="+ficha+"&fic_id="+fic_id,
					success: function(respuesta){
		                //alert(respuesta);
		                $("#myModal4").find("#contenidoNotificaciones").html(respuesta);
		                $("#myModal4").find("#notificaciones").css("display","block");
		                $("#myModal4").find("#notificaciones").animate({left: '5px'});
		                $("#myModal4").find("#notificaciones").animate({left: '-10px'});
		                localStorage.setItem("actualizar", "si");
					}
				});
		    });
			$(document).on("change","#modalidad", function(){
				var url = $(this).attr("data-url");
				var modalidad = $(this).val();
				if (modalidad !="" && modalidad >= 1 && modalidad <= 3) {
					$.ajax({
                        url:url,
						type:"GET",
						data:"modalidad="+modalidad,
						success:function(data){
							$("#url").html("");
							$("#pla_fic_id").empty();
                            $("#pla_fic_id").prepend(data);
						}
					});
				}
			});
			$(document).on('click','#aprendices', function(){
               $("#listado").modal();
			   var url = $(this).attr("data-url");
			   var ficha = $(this).attr("data-ficha");
			   var programa = $(this).attr("data-programa");
			   $(".title-ficha").text("Ficha: "+ficha);
			   $(".title-programa").text("Programa: "+programa);
				$.ajax({
					url:url,
					type:"GET",
					data:"ficha="+ficha,
					success:function (data) {
						$("#listado_ficha").html(data);
						var url = "exportaraprendices?ficha="+ficha+"";
						$(".btn-listado").attr("href",url);
					}
				});
			});
		});
		function filterList(param) {
			document.querySelectorAll(".trimestres").forEach(val => {
				options = val.options;
                for (let index = 0; index < options.length; index++) {
					ele = options[index].text;
					valor = options[index].value;
					if (!ele.toLowerCase().includes(param.toLowerCase()) &&  valor!="" && valor!="todos") {
						options[index].style.display="none";
					}else{
						options[0].selected = true;
						options[index].style.display="block";
					}
				}
			});
		}
	</script>
@endsection