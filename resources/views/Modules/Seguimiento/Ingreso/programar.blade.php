@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Ingreso','Programar') !!}
	<div class="row" id="urls" data-url-retardo="{{ url('seguimiento/ficha/retardo') }}" data-token="{{ csrf_token() }}" data-cambio-input = "{{ url('seguimiento/horario/modificarnumeroprograma') }}" data-cambio-select = "{{ url('seguimiento/horario/modificartipooferta') }}">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Programar</span>
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
						@if(session()->get('mensajes') != null)
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="alert alert-default" style="background-color:#309591;color:white;">
									<strong>Notificaciones!</strong><br>
									<?php $arregloMensajes = session()->get('mensajes');?>
									@if(isset($arregloMensajes))
										@foreach($arregloMensajes as $key => $val)
											<li> <?php echo $val; ?></li>
										@endforeach
									@endif
								</div>
							</div>
							{{ session()->forget('mensajes') }}
						@endif
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<h4 class="text-center">Programar</h4>
							<form method="POST" class="text-center">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>Ficha - Programa - Aprendices hab.</th>
											<th>Ambiente - aforo (aprendiz + instructor)</th>
											<th colspan="6">D&iacute;as</th>
											<th>Franja</th>
											<th>Instructor</th>
											<th>Fecha inicio</th>
											<th>Fecha fin</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>
												<select name="ficha" class="form-control js-example-basic-multiple" required>
													<option value=""> Seleccione una ficha ... </option>
													@foreach($fichas_habilitadas as $fic)
														@if($fic->aprendices > 0)
														<!--<option value="{{ $fic->fic_numero }}">{{ $fic->fic_numero }} - {{ ucfirst(mb_strtolower($fic->prog_nombre)) }} - {{ $fic->aprendices }}</option>-->
														<option value="{{ $fic->fic_numero }}">{{ $fic->fic_numero }} - {{ mb_convert_case($fic->prog_sigla, MB_CASE_UPPER, "UTF-8") }} - {{ $fic->aprendices }}</option>
														@endif
													@endforeach
												</select>
											</td>
											<td>
												<select name="ambiente" class="form-control" required>
													<option value="">Seleccione...</option>
													@foreach($ambiente as $amb)
													<option value="{{ $amb->id }}">{{ $amb->descripcion }} - {{ $amb->aforo }}</option>
													@endforeach
												</select>
											</td>
											<td>
												<label>L</label><br>
												<input name="dia[1]" value="L" type="checkbox">
											</td>
											<td>
												<label>M</label><br>
												<input name="dia[2]" value="M" type="checkbox">
											</td>
											<td>
												<label>Mi</label><br>
												<input name="dia[3]" value="Mi" type="checkbox">
											</td>
											<td>
												<label>J</label><br>
												<input name="dia[4]" value="J" type="checkbox">
											</td>
											<td>
												<label>V</label><br>
												<input name="dia[5]" value="V" type="checkbox">
											</td>
											<td>
												<label>S</label><br>
												<input name="dia[6]" value="S" type="checkbox">
											</td>
											<td>
												<select name="franja" style="width: auto;" class="form-control" required>
													<option value="">Seleccione...</option>
													<option value="07:00-11:00">07:00 - 11:00</option>
													<option value="13:00-17:00">13:00 - 17:00</option>
													<option value="18:00-21:00">18:00 - 21:00</option>
												</select>
											</td>
											<td>
												<select name="instructor" class="form-control" required>
													<option value="">Seleccione...</option>
													@foreach($instructores as $ins)
													<option value="{{ $ins->par_identificacion }}">{{ ucwords(mb_strtolower($ins->par_nombres)) }} {{ ucwords(mb_strtolower($ins->par_apellidos)) }} - {{ $ins->par_identificacion }}</option>
													@endforeach
												</select>
											</td>
											@if($documentoLoguiado == 1107052360 or $documentoLoguiado == 1111111111)
											<td>
												<input class="form-control fechaInicio" name="fechaInicio" type="date" required>
											</td>
											<td>
												<input class="form-control fechaFin" name="fechaFin" type="date" required>
											</td>
											@else
											<td>
												<input class="form-control fechaInicio" min="<?php echo date("Y-m-d", strtotime(date("Y-m-d").' next monday')); ?>" name="fechaInicio" type="date" required>
											</td>
											<td>
												<input class="form-control fechaFin" min="<?php echo date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d").' next monday')).' next saturday')); ?>" name="fechaFin" type="date" required>
											</td>
											@endif
										</tr>
									</tbody>
								</table>
								<input name="_token" type="hidden" value="{{ csrf_token() }}">
								<button class="btn btn-success enviar">Registrar</button>
							</form>

							<h4 class="text-center">Mi programación</h4>
							<div class="table-responsive">
								<table class="table table-bordered table-hover">
									<thead>
										<tr>
											<th>#</th>
											<th>Ficha</th>
											<th>Programa</th>
											<th>Fecha inicio</th>
											<th>Fecha fin</th>
											<th>Ambiente</th>
											<th>D&iacute;as</th>
											<th>Franja</th>
											<th>Instructor</th>
											<th>Documento</th>
											<th>Aforo</th>
											<!--<th>Programados</th>-->
											<th>Acci&oacute;n</th>
										</tr>
									</thead>
									<?php $contador = 1; $fecha_actual = date('Y-m-d');?>
									<tbody>
										@foreach($fichas_programadas as $fic)
										<tr>
											<td>{{ $contador++ }}</td>
											<td>{{ $fic->fic_numero }}</td>
											<td title="{{ ucfirst(mb_strtolower($fic->prog_nombre)) }}">{{ ucfirst(mb_strtolower($fic->programa_corto)) }}</td>
											<td>{{ $fic->ing_fic_fecha_inicio }}</td>
											<td>{{ $fic->ing_fic_fecha_fin }}</td>
											<td>{{ ucfirst(mb_strtolower($fic->ing_fic_ambiente)) }}</td>
											<td>{{ $fic->ing_fic_dia }}</td>
											<td>{{ $fic->ing_fic_hor_inicio }} - {{ $fic->ing_fic_hor_fin }}</td>
											<td>{{ ucwords(mb_strtolower($fic->par_nombre_corto)) }} {{ ucwords(mb_strtolower($fic->par_apellido_corto)) }}</td>
											<td>{{ $fic->par_identificacion }}</td>
											<td>{{ $fic->ing_fic_aforo }}</td>
											<?php if($fic->ing_fic_fecha_fin >= $fecha_actual){ ?>
												<td>
												    <a class="editar" data-programa="{{ ucfirst(mb_strtolower($fic->programa_corto)) }}" data-ficha="{{ $fic->fic_numero }}" data-id="{{ $fic->ing_fic_id }}" data-fecha1="{{ $fic->ing_fic_fecha_inicio }}" data-fecha2="{{ $fic->ing_fic_fecha_fin }}">Editar</a>
												    <a class="eliminar" data-programa="{{ ucfirst(mb_strtolower($fic->programa_corto)) }}" data-ficha="{{ $fic->fic_numero }}" data-id="{{ $fic->ing_fic_id }}">Eliminar</a>
												</td>
											<?php }else{ ?>
												<td></td>
											<?php } ?>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>

							<h4 class="text-center">Fichas habilitadas para programar</h4>
							<table class="table table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>Ficha</th>
										<th>Programa</th>
										<th>Aprendices disponibles</th>
									</tr>
								</thead>
								<?php $contador = 1; $cantidadAprendices = 0;?>
								<tbody>
									@foreach($fichas_habilitadas as $fic)
									<tr>
										<td>{{ $contador++ }}</td>
										<td>{{ $fic->fic_numero }}</td>
										<td>{{ $fic->prog_nombre }}</td>
										<td>{{ $fic->aprendices }}</td>
										<?php $cantidadAprendices += $fic->aprendices; ?>
									</tr>
									@endforeach
									<tr>
										<td colspan="3">Total aprendices</td>
										<td>{{ $cantidadAprendices }}</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title text-center">Eliminar programaci&oacute;n</h4>
				</div>
				<div class="modal-body">
					<div class="alert alert-danger text-center">
						<strong>Alerta!</strong><br>
						Si elimina la programaci&oacute;n del grupo, también eliminará la programación de aprendices,
						favor notificar al Instructor, para que <strong>no</strong> los cite.<br><br>
						<p>
							Desea eliminar la programación del <br> programa: <strong id="programa"></strong><br> ficha: <code id="ficha"></code>?
						</p>
					</div>
					<div class="modal-body text-center">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="col-lg-6 col-lg-push-3 col-md-6 col-sm-6 col-xs-12">
									<form id="eliminarProgramacion" data-url="{{ url('seguimiento/ingreso/eliminarprogramacionficha') }}">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<input type="hidden" id="ing_fic_id" name="ing_fic_id">
										<button class="btn btn-danger btn-xs">Eliminar</button>
									</form>
								</div>
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
	<!-- Modal 2 editar las fechas de inicio y fin de la programación -->
	<div id="myModal2" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title text-center">Editar programaci&oacute;n</h4>
				</div>
				<div class="modal-body">
					<div class="alert alert-danger text-center">
						<form id="editarProgramacion" data-url="{{ url('seguimiento/ingreso/editar') }}">
	                     <div class="form-group">
						 	<label>Fecha de inicio:</label>	 
						 	<input class="form-control fecha1" name="fechaInicio" type="date" required>
						 </div><br>
						 <div class="form-group">
						 <label>Fecha fin:</label>	 	 
						 <input class="form-control fecha2"  name="fechaFin" type="date" required>
						 </div>
					</div>
					<div class="modal-body text-center">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="col-lg-6 col-lg-push-3 col-md-6 col-sm-6 col-xs-12">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<input type="hidden" id="ing_fic_id2" name="ing_fic_id2">
										<button class="btn btn-danger btn-xs">Editar</button>
									</form>
								</div>
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
@endsection
@section('plugins-css')
<link rel="stylesheet" href="{{ asset('css/alertify.min.css') }}">
<style>
	.eliminar{
		cursor: pointer;
	}
	.eliminar:hover {
		color: red;
	}
	.alertify-notifier > div{
		color: white;
		text-align: center;
		border: black 1px solid;
		font-size: 15px;
	}
	.table  th, .table  td{
		text-align: center;
		font-size: 11px;
	}
	.table tbody tr td{
		vertical-align: middle;
		padding: 2px;
	}
</style>
@endsection
@section('plugins-js')
	<script type="text/javascript" src="{{ asset('js/alertify.min.js') }}"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			$("#eliminarProgramacion").submit(function(e){
				e.preventDefault();
				var url = $(this).attr("data-url");
				var datos = $(this).serialize();
				var r = confirm("Estas seguro que desea eliminar está rpogramación?");
				if (r == true) {
					$.ajax({url:url, type:"POST", data:datos, success: function(respuesta){
							if(respuesta == 1){
								alert('Programación eliminada exitosamente.');
								localStorage.setItem("posicion", $('html').scrollTop());
								window.location.href = window.location.href;
							}else{
								alert(respuesta);
								alertify.error('El día en la <strong>fecha inicio</strong> debe ser lunes.');
							}
							console.log(respuesta);
						}
					});
				}
			});
			
			$("#editarProgramacion").submit(function(e){
				e.preventDefault();
				var url = $(this).attr("data-url");
				var datos = $(this).serialize();

				var r = confirm("Estas seguro que deseas actualizar está programación?");
				if (r == true) {
					$.ajax({url:url, type:"GET", data:datos, success: function(respuesta){
							if(respuesta == 1){
								alert('Fechas actualizadas');
								localStorage.setItem("posicion", $('html').scrollTop());
								window.location.href = window.location.href;
							}else{
								alert(respuesta);
								alertify.error('El día en la <strong>fecha inicio</strong> debe ser lunes.');
							}
							console.log(respuesta);
						}
					});
				}
			});
			
			$(document).on('click','.eliminar',function(e){
				var ing_fic_id = $(this).attr('data-id');
				var ficha = $(this).attr('data-ficha');
				var programa = $(this).attr('data-programa');
				$('#myModal').modal('show');
				$('#ing_fic_id').val(ing_fic_id);
				$('#ficha').html(ficha);
				$('#programa').html(programa);
			});
			
			$(document).on('click','.editar',function(e){
				var ing_fic_id = $(this).attr('data-id');
				var ficha = $(this).attr('data-ficha');
				var fecha1 = $(this).attr('data-fecha1');
				var fecha2 = $(this).attr('data-fecha2');
				var programa = $(this).attr('data-programa');
				$('#myModal2').modal('show');
				$('#ing_fic_id2').val(ing_fic_id);
				$('.fecha1').val(fecha1);
				$('.fecha2').val(fecha2);
				$('#ficha').html(ficha);
				$('#programa').html(programa);
			});		
			$(document).on('change','.fechaInicio, .fechaFin',function(e){
				var fechaInicio = $('.fechaInicio').val();
				var fechaFin = $('.fechaFin').val();

				$('.enviar').attr('disabled', true);
				//console.log(fechaInicio+" "+fechaFin);
				if(fechaInicio != '' && fechaFin != ''){
					var formato_fecha_inicio = new Date(fechaInicio);
					var dia_numero_fecha_inicio = formato_fecha_inicio.getDay();

					var formato_fecha_fin = new Date(fechaFin);
					var dia_numero_fecha_fin = formato_fecha_fin.getDay();

					if(fechaInicio < fechaFin){
						if(dia_numero_fecha_inicio == 0 && dia_numero_fecha_fin == 5){
							$('.enviar').attr('disabled', false);
							alertify.success('Boton habilitado.');
						}else{
							if(dia_numero_fecha_inicio != 0){
								alertify.error('El día en la <strong>fecha inicio</strong> debe ser lunes.');
							}

							if(dia_numero_fecha_fin != 5){
								alertify.error('El día en la <strong>fecha fin</strong> debe ser sábado.');
							}
						}
					}else{
						alertify.error('La <strong>fecha inicio</strong> debe ser menor a la <strong>fecha fin</strong> .');
					}
				}
			});
		});
	</script>
@endsection