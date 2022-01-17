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
					@if(session()->get('mensajes') != null)
						<div class="row">
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
						</div>
						{{ session()->forget('mensajes') }}
					@endif
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<h4 class="text-center"><strong>{{ $rol }} - {{ $nombre }}</strong></h4>
							<form method="POST" class="text-center">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>Documento</th>
											<th colspan="6">D&iacute;as</th>
											<th>Hora inicio</th>
											<th>Hora fin</th>
											<th>Fecha inicio</th>
											<th>Fecha fin</th>
											<th>Ambiente - aforo</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>
												<input value="{{ $documento }}" name="documento" type="number"  class="form-control" readonly>
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
												<select name="hora_inicio" class="hora_inicio form-control" required>
													<option value="">Seleccione...</option>
													<?php
														for($i=6; $i<=21; $i++){
															if($i < 10){
																echo '<option value="0'.$i.':00">0'.$i.':00</option>';
																echo '<option value="0'.$i.':30">0'.$i.':30</option>';
															}else{
																echo '<option value="'.$i.':00">'.$i.':00</option>';
																echo '<option value="'.$i.':30">'.$i.':30</option>';
															}
														}
													?>
												</select>
											</td>
											<td>
												<select name="hora_fin" class="hora_fin form-control" required>
													<option value="">Seleccione...</option>
													<?php
														for($i=7; $i<=22; $i++){
															if($i < 10){
																echo '<option value="0'.$i.':00">0'.$i.':00</option>';
																echo '<option value="0'.$i.':30">0'.$i.':30</option>';
															}else{
																echo '<option value="'.$i.':00">'.$i.':00</option>';
																if($i != 22){
																	echo '<option value="'.$i.':30">'.$i.':30</option>';
																}
															}
														}
													?>
												</select>
											</td>
											<td>
												<input class="form-control fechaInicio" name="fechaInicio" type="date" value="{{ date('Y-m-d')}}">
											</td>
											<td>
												<input class="form-control fechaFin" name="fechaFin" type="date" value="{{ date('Y-m-d') }}">
											</td>
											<td>
												<select name="ambiente" class="form-control" required>
													<option value="0">Sin ambiente</option>
													<?php

														foreach($ambientes as $amb){
															echo '<option value="'.$amb->id.'">'.$amb->descripcion.' - '.$amb->aforo.'</option>';
														}
													?>
												</select>
											</td>
										</tr>
									</tbody>
								</table>
								<input name="_token" type="hidden" value="{{ csrf_token() }}">
								<a class="btn btn-danger" href="{{ url('seguimiento/ingreso/indexpersona') }}">Cancelar</a>
								<button class="btn btn-success">Registrar</button>
							</form>

							<h4 class="text-center">Mi programación</h4>
							<table class="table table-bordered table-hover" style="margin:0px;">
								<thead>
									<tr>
										<th>#</th>
										<th>Fecha inicio</th>
										<th>Fecha fin</th>
										<th>Franja</th>
										<th>D&iacute;as</th>
										<th>Ficha</th>
										<th>Ambiente</th>
										<th>Acci&oacute;n</th>
									</tr>
								</thead>
								<tbody>
									@if(isset($datos['ing_hab_id']))
										@foreach($datos['fecha_inicio'] as $key => $val)
										<tr>
											<td>{{ $key+1 }}</td>
											<td>{{ $val }}</td>
											<td>{{ $datos['fecha_fin'][$key] }}</td>
											<td>{{ $datos['franja'][$key] }}</td>
											<td>{{ $datos['dia'][$key] }}</td>
											<td>{{ $datos['ficha'][$key] }}</td>
											<td>{{ $datos['ambiente'][$key] }}</td>
											<td><?php echo $datos['boton'][$key] ?></td>
										</tr>
										@endforeach
									@endif
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
					<div class="modal-body text-center">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="col-lg-6 col-lg-push-3 col-md-6 col-sm-6 col-xs-12">
									<form id="eliminarProgramacion" data-url="{{ url('seguimiento/ingreso/eliminarprogramacionpersona') }}">
										<label>Está seguro que desea eliminar la programación ?</label><br>
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<input type="hidden" id="ing_fic_id" name="ing_fic_id">
										<button class="btn btn-danger btn-xs">Si, eliminar.</button>
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
	.botonHorario{
		cursor: pointer;
	}
	.botonHorario:hover {
		color: #ec7114;
	}
	.alertify-notifier > div{
		color: white;
		text-align: center;
		border: black 1px solid;
		font-size: 15px;
	}
	.table  th, .table  td{
		text-align: center;
		font-size: 12px;
	}
	.table tbody tr td, .table thead tr th{
		vertical-align: middle;
		padding: 3px;
	}
	tr{
		cursor: pointer;
	}
</style>
@endsection
@section('plugins-js')
	<script type="text/javascript" src="{{ asset('js/alertify.min.js') }}"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			$(document).on('click','.botonHorario',function(e){
				var ing_fic_id = $(this).attr('data-id');
				$('#ing_fic_id').val(ing_fic_id);
				$('#myModal').modal('show');
			});

			$("#eliminarProgramacion").submit(function(e){
				e.preventDefault();
				var url = $(this).attr("data-url");
				var datos = $(this).serialize();
				var r = confirm("Estas seguro que desea eliminar está programación?");
				if (r == true) {
					$.ajax({url:url, type:"POST", data:datos, success: function(respuesta){
							if(respuesta == 1){
								localStorage.setItem("posicion", $('html').scrollTop());
								window.location.href = window.location.href;
							}
						}
					});
				}
			});
		});
	</script>
@endsection