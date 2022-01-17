@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Horarios','Ficha') !!}
	<div class="row" id="urls" data-token="{{ csrf_token() }}" data-cambio-input = "{{ url('seguimiento/horario/modificarnumeroprograma') }}" data-cambio-select = "{{ url('seguimiento/horario/modificartipooferta') }}">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Horarios por ficha</span>
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
												<label>Trimestre: </label><br>
												<small>A&ntilde;o N&uacute;mero trimestre - Fecha inicio - Fecha fin</small>
												<select class="form-control" name="pla_fec_tri_id" required id="pla_fec_tri_id">
													<option value = "">Seleccione...</option>
													<?php //dd($pla_fec_tri_id); ?>
													@if(isset($pla_fec_tri_id))
														<option id="todosLosTrimestres" <?php echo $pla_fec_tri_id =='todos' ? 'selected' :  '' ?> value = "todos">Todos los trimestres</option>
														@foreach($trimestres as $val)
															<?php $selected = ''; ?>
															@if($pla_fec_tri_id == $val->pla_fec_tri_id)
															<?php $selected = 'selected'; ?>
															@endif
															<option <?php echo $selected; ?> value="{{ $val->pla_fec_tri_id }}">{{ $val->pla_fec_tri_year }} - {{ $val->pla_fec_tri_trimestre }} - {{ $val->pla_fec_tri_fec_inicio }} - {{ $val->pla_fec_tri_fec_fin }}</option>
														@endforeach
													@else
														<option id="todosLosTrimestres" value = "todos">Todos los trimestres</option>
														@foreach($trimestres as $val)
																<option value="{{ $val->pla_fec_tri_id }}">{{ $val->pla_fec_tri_year }} - {{ $val->pla_fec_tri_trimestre }} - {{ $val->pla_fec_tri_fec_inicio }} - {{ $val->pla_fec_tri_fec_fin }}</option>
														@endforeach
													@endif
												</select>
											</div>
										</div>
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
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="contenedor_seleccionar_ficha" style="margin-top:15px;">
											<div style="font-size:12px;" class="col-lg-6 col-lg-push-3 col-md-8 col-md-push-2 col-sm-12 col-xs-12">
												<label style="font-size:14px;">Seleccione ficha(s): </label><br>
												<select class="js-example-basic-multiple" id="pla_fic_id" name="pla_fic_id[]" multiple="multiple" required>
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
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
											<?php
												$host= $_SERVER["HTTP_HOST"];
												$url= $_SERVER["REQUEST_URI"];
												$url ="http://" . $host . $url.'&generar=PDF';
											?>
											<a title="Exportar a PDF" target="_blank" href="<?php echo $url; ?>"><img style="cursor:pointer;" src="{{ asset('img/horario/PDF2.png') }}"></a>
										</div>
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" >
											<h5 style="margin:0px;">
												<label>Ficha:</label> {{ $hor->fic_numero }}&nbsp;<b>{{ $hor->prog_sigla }}</b>&nbsp;&nbsp;
												<label>Modalidad:</label> {{$hor->Modalidad}}&nbsp;&nbsp;
												@if($hor->pla_fic_consecutivo_ficha == "")
												    <b>Consecutivo:</b> 0
												@else
												    <b>Consecutivo:</b> {{ $hor->pla_fic_consecutivo_ficha }}
												@endif
											</h5>
											<h6 style="margin:0px;">{{ $hor->niv_for_nombre }} en {{ ucwords(mb_strtolower($hor->prog_nombre)) }}</h6>
											<h6 style="margin:0px;">
												<label>Lectiva trimestre(s):</label> {{ $hor->pla_fic_can_trimestre }} &nbsp;&nbsp;&nbsp;
												<label>Tipo oferta:</label> {{ $hor->pla_tip_ofe_descripcion }} &nbsp;&nbsp;&nbsp;
												<label>Franja horaria:</label> {{ $hor->pla_fra_descripcion }}
											</h6>
											<h6 style="margin:0px">
												<strong>Instructor l&iacute;der</strong>
												@foreach($instructores as $ins)
												<?php
													if($ins->par_identificacion == $hor->pla_ins_lider){ ?>
														{{ $ins->par_nombres }} {{ $ins->par_apellidos }}
												<?php	
														break; 
													}
												?>
												@endforeach
											</h6>
											<h6 style="margin:0px;">
												<label>Creado por:</label> {{ ucwords(mb_strtolower($hor->par_nombres)) }} {{ ucwords(mb_strtolower($hor->par_apellidos)) }} &nbsp;&nbsp;
												<label>Fecha creaci&oacute;n:</label> {{ $hor->pla_fic_fec_creacion }}
											</h6>
										</div>
										<?php $cantidad_trimestres = $hor->pla_fic_can_trimestre; $cantidad_trimestres +=2;?>
										@for($l=1; $l<=$cantidad_trimestres; $l++)
											@if(isset($programacion[$hor->pla_fic_id][$l]))
												@foreach($programacion[$hor->pla_fic_id][$l]["fechas_inicio"] as $key10 => $val10)
													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="padding-bottom: 2px;">
														<h5 style="margin:0px;">
															<strong>Ficha</strong>: {{ $hor->fic_numero }}
															<strong>Trimestre</strong> # {{ $l }}
														</h5>
														<h6 style="margin:0px;">
															<strong>Fecha inicio:</strong> {{ $val10 }}
															<strong>Fecha fin:</strong> {{ $programacion[$hor->pla_fic_id][$l]["fechas_fin"][$key10] }}
														</h6>
														
														<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
														    <!--Este es el boton "Actividades" que le hace aparecer a los instructores lider-->
                                                            @if($cc == $hor->pla_ins_lider)
															    <a style="cursor:pointer;font-size:14px;" name="pla_fic_id" data-programa="{{ ucwords(mb_strtolower($hor->prog_nombre)) }}" data-ficha="{{ $hor->fic_numero }}" data-trimestre="{{ $l }}" data-fec-fin="{{ $programacion[$hor->pla_fic_id][$l]['fechas_fin'][$key10] }}" data-fec-inicio="{{ $val10 }}" data-pla-fic-id="{{ $hor->pla_fic_id }}" value="{{ $hor->pla_fic_id }}" class="modificarActividades">Actividades</a>
														    @endif
														        <a style="cursor:pointer;font-size:14px;" id="aprendices" data-url="{{url('seguimiento/horario/listadoaprendices')}}" data-ficha="{{ $hor->fic_numero }}" data-programa="{{ ucwords(mb_strtolower($hor->prog_nombre)) }}">Listado de aprendices</a>
														</div>
														<!---->
													</div>
													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="padding: 0px 75px;">
														<table style="margin-bottom:15px;" data-plaFicId="{{ $hor->pla_fic_id }}" data-ficha="{{ $hor->fic_numero }}" data-url="{{ url('seguimiento/horario/contenidomodal') }}"  data-programa="{{ ucwords(mb_strtolower($hor->prog_nombre)) }}" data-trimestre="{{ $l }}" data-fec-fin="{{ $programacion[$hor->pla_fic_id][$l]['fechas_fin'][$key10] }}" data-fec-inicio="{{ $val10 }}" class="table table-bordered table-hover">
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
																<?php //echo '<pre>'; print_r($programacion[$hor->pla_fic_id][$l]);?> 
																@if(isset($programacion[$hor->pla_fic_id][$l]['hora_inicio'][$key10]))
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
																							$ambiente = $programacion[$hor->pla_fic_id][$l]["pla_amb_descripcion"][$key10][$key1];
																							$rowspan = $programacion[$hor->pla_fic_id][$l]["horas_totales"][$key10][$key1];
																							/*if(isset($actividades_programadas[$hor->pla_fic_id][$l][$instructor_cedula])){
																								$mensaje = '<h6 style="margin:1px 0px 2px 0px;font-size:9px;">Ver actividades</h6>';
																								$estilos_celda = 'background:#087b76';
																								$actividad_clase = 'actividad';
																							}else{
																								$estilos_celda = 'background:red';
																								$mensaje = '<h6 style="margin:1px 0px 2px 0px;font-size:9px;">Sin actividad</h6>';
																								$actividad_clase = '';
																							}*/
																							break;
																						}
																						?>
																					@endforeach

																					@if($control)
																						<td data-tipo="ficha" data-cc="{{ $instructor_cedula }}" data-instructor="{{ $instructor_nombre }}" rowspan="{{ $rowspan }}" class="actividad" style="padding: 0px;vertical-align: middle;cursor:pointer;">
																							<h6 style="margin:2px 0px 1px 0px;font-size:10.5px;">{{ $instructor_nombre }}</h6>
																							<h6 style="margin:0px;font-size:10.5px;font-weight: bold;">{{ $ambiente }}</h6>
																						</td>
																						<?php	$horas[$i]['can'] -= $rowspan; ?>
																					@else
																						<td> </td>
																						<?php	$horas[$i]['can'] --; ?>
																					@endif
																				@endif
																				<?php $horas[$i]['total']--; ?>
																			@endfor
																		</tr>
																	@endfor
																@else
																	@for($j=6; $j<22; $j++)
																		<tr>
																			<th style='padding:5px;font-size:11px;text-align:center;'> {{ $j.":00 - ".($j+1).":00" }} </th>
																			@for($i=1; $i<7; $i++)
																				<td></td>
																			@endfor
																		</tr>
																	@endfor
																@endif
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
	</script>
@endsection