@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Registro','') !!}
	<div class="row" id="urls" data-token="{{ csrf_token() }}" data-cambio-input = "{{ url('seguimiento/horario/modificarnumeroprograma') }}" data-cambio-select = "{{ url('seguimiento/horario/modificartipooferta') }}">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Registro</span>
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
						<div class="col-lg-4 col-lg-push-4 col-md-6 col-md-push-3 col-sm-12 col-xd-12 text-center">
							<label>N&uacute;mero de documento</label>
							<input id="consultarDocumento" type="text" value="" class="form-control" placeholder="Escriba el documento..." autofocus required>
							<span id="consultar" data-url="{{ url('seguimiento/ingreso/query') }}" data-token="{{ csrf_token() }}">Consultar</span>
							<span id="limpiarCampo">&nbsp;&nbsp;&nbsp;Borrar&nbsp;&nbsp;&nbsp;</span>
						</div>
						<form action="{{ url('seguimiento/ingreso/create') }}">
							<table id="table1" class="table table-hover">
								<thead>
									<tr>
										<th class="documento">Documento</th>
										<th class="tipoDocumento">Tipo documento</th>
										<th class="rol">Rol</th>
										<th>Nombre completo</th>
										<th class="temperatura">Temperatura ingreso</th>
										<th class="temperatura">Temperatura salida</th>
										<th>Acci&oacute;n</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<input id="documento" name="documento" type="number" class="form-control" readonly required>
										</td>
										<td>
											<select id="tipoDocumento" name="tipoDocumento" class="form-control" readonly>
												<option value="">Seleccione</option>
												<option value="CC">CC</option>
												<option value="TI">TI</option>
												<option value="CE">CE</option>
											</select>
										</td>
										<td>
											<input id="rol" name="rol" type="text" class="form-control" readonly>
										</td>
										<td>
											<input id="nombreCompleto" name="nombreCompleto" type="text" class="form-control" readonly>
										</td>
										<td>
											<input id="temperaturaIngreso" min="35.5" max="38.1" name="temperaturaIngreso" type="number" step="any" class="form-control" readonly>
										</td>
										<td>
											<input id="temperaturaSalida" min="35.5" max="50" name="temperaturaSalida" type="number" step="any" class="form-control" readonly>
										</td>
										<td>
											<input class="registrar" type="submit" class="form-control" value="Registrar">
											<input type="hidden" name="_token" value="{{ csrf_token() }}">
										</td>
									</tr>
								</tbody>
							</table>
						</form>
					</div>
					<div class="row">
						<div class="alert alert-success text-center notificaciones">
							<h5>Notificaciones</h5>
							<div id="notificar">
							</div>
						</div>
					</div>
					<div class="row">
						<h4 class="text-center">Registros</h4>
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Documento</th>
									<th>Tipo documento</th>
									<th>Rol</th>
									<th>Nombre completo</th>
									<th>Fecha</th>
									<th>Hora ingreso</th>
									<th>Tem ingreso</th>
									<th>Hora salida</th>
									<th>Tem salida</th>
								</tr>
							</thead>
							<tbody id="contenidoTabla" data-url="{{ url('seguimiento/ingreso/index?updateTable=yes') }}">
								@foreach($registros as $reg)
								<tr>
									<td>{{ $reg->ing_est_documento }}</td>
									<td>{{ $reg->ing_est_tip_documento }}</td>
									<td>{{ $reg->ing_est_rol }}</td>
									<td>{{ $reg->ing_est_nombre }}</td>
									<td>{{ $reg->ing_det_fecha }}</td>
									<td>{{ $reg->ing_det_hor_ingreso }}</td>
									<td>{{ $reg->ing_det_tem_ingreso }}</td>
									<td>{{ $reg->ing_det_hor_salida }}</td>
									<td>{{ $reg->ing_det_tem_salida }}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						<span>&Uacute;ltimos 20 registros</span>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('plugins-css')
<style>
	.rol{ width: 125px; }
	.tipoDocumento{ width: 125px; }
	.documento{ width: 150px; }
	.temperatura{ width: 80px; }
	.notificaciones{ margin: 0px; padding: 10px;}
	.notificaciones h5{ margin: 0px 0px 5px 0px; font-weight: bold;}
	#consultarDocumento:focus-within {
		border: 2px solid #309591;
		padding: 10px;
		box-shadow: 0px 10px 15px 5px #888888;
		background: #f9f9f9;
	}
	#consultarDocumento{
		height: 40px;
		font-size: 20px;
		text-align: center;
		border: 2px solid;
		border-radius: 15px;
		margin-bottom: 13px;
	}
	#limpiarCampo{ margin-left: 5px; }
	#limpiarCampo:hover, #consultar:hover{
		background: #fbfbf0;
		color: blue;
	}
	#consultar, #limpiarCampo{
		cursor: pointer;
		font-size: 15px;
		padding: 5px;
		background: #eee;
		border: 1px solid;
		border-radius: 10px;
	}
	.table  th, .table  td{
		text-align: center;
		font-size: 14px;
	}
	.table tbody tr td{ padding: 4px; }
	.box-content .row{ padding: 0px 10px 0px 10px; }
	.registrar:hover{
		background: #087b76;
		color: white;
	}
	#table1 input{
		text-align: center;
		font-size: 15px;
	}
	#notificar{
		height: 58px;
		border: 1px solid;
    	border-radius: 5px;
		overflow-y: auto;
	}
</style>
@endsection
@section('plugins-js')
	<script type="text/javascript">
		$(document).ready(function () {
			$(document).on('click','#limpiarCampo',function(){
				$('#documento').val('');
				$('#consultarDocumento').val('');
				$('#consultarDocumento').focus();
				$('#tipoDocumento').attr('readonly', true);
				cleanData();
				$('#notificar').html('');
			});

			$(document).on('click','#consultar',function(){
				var documento = $('#consultarDocumento').val();
				var token = $(this).attr('data-token');
				var url = $(this).attr('data-url');

				if(documento === '' || !Number.isInteger(parseInt(documento)) || documento <= 0){
					cleanData();
					$('#notificar').html('<h3>El campo <strong>Número de documento</strong> es obligatorio y debe ser numérico.</h3>.');
				}else{
					$('#documento').val(documento);
					$.post(url, {'_token':token,'documento':documento}, function(respuesta){
						var datos = JSON.parse(respuesta);
						$('#notificar').html('<h3>'+datos.mensaje+'</h3>');

						$('#rol').prop('readonly', true);
						$('#tipoDocumento').prop('readonly', true);
						$('#nombreCompleto').prop('readonly', true);
						$('#temperaturaIngreso').prop('readonly', true);
						$('#temperaturaSalida').prop('readonly', true);

						$('#rol').val('');
						$('#nombreCompleto').val('');
						$('#temperaturaIngreso').val('');
						$('#temperaturaSalida').val('');
						$('#tipoDocumento').val('');

						if (datos.existe) {
							if (datos.existe == 'si') {
								$('#tipoDocumento').val(datos.tipoDocumento);
								$('#nombreCompleto').val(datos.nombreCompleto);
								$('#rol').val(datos.rol);

								if(datos.puedePasar == 'si'){
									if(datos.habilitado === 'si'){
										if(datos.desplazamiento == 'entrada'){
											$('#temperaturaIngreso').attr('required', true);
											$('#temperaturaIngreso').prop('readonly', false);
											$('#temperaturaSalida').prop('readonly', true);
											$('#temperaturaIngreso').focus();
										}else{
											$('#temperaturaSalida').attr('required', true);
											$('#temperaturaSalida').prop('readonly', false);
											$('#temperaturaIngreso').prop('readonly', true);
											$('#temperaturaIngreso').val(datos.temperaturaIngreso);
											$('#temperaturaSalida').focus();
										}
									}
								}else{
									$('#temperaturaIngreso').prop('readonly', true);
									$('#temperaturaSalida').prop('readonly', true);
									$('#temperaturaSalida').attr('required', false);
									$('#temperaturaSalida').attr('required', false);
								}
							}else{
								$('#rol').val('Externo');
								$('#tipoDocumento').removeAttr('readonly');
								$('#temperaturaIngreso').prop('readonly', false);
								$('#temperaturaSalida').prop('readonly', true);
								$('#nombreCompleto').prop('readonly', false);

								$('#temperaturaIngreso').attr('required', true);
								$('#nombreCompleto').attr('required', true);
								$('#tipoDocumento').attr('required', true);
							}
						}
					});
				}
				$('#consultarDocumento').val('');
				$('#consultarDocumento').focus();
			});

			$(document).on('paste','#consultarDocumento',function(e){
				//return false;
			});

			$(document).on('keypress','#consultarDocumento',function(e){
				var valor = $(this).val();
				var cantidadLetras = valor.length;
				console.log(e.which);
				if(e.which == 13){
					$("#consultar").trigger("click");
					return false;
				}else if(e.which >= 48 && e.which <= 57){
					if(cantidadLetras >= 20){
						return false;
					}
				}else{
					return false;
				}
			});

			$("form").submit(function(e){
				e.preventDefault();
				console.log('Entre en el envio');
				var url = $(this).attr("action");
				var token = $("#consultar").attr('data-token');
				var datos = $(this).serialize();

				$.post(url, datos, function(respuesta){
					var data = JSON.parse(respuesta);
					$('#notificar').html('<h3>'+data.mensaje+'</h3>');
					if(data.exito === 'si'){
						var urlTable = $('#contenidoTabla').attr('data-url');
						cleanData();
						updateTable(urlTable);
					}
					$('#consultarDocumento').focus();
				});
			});

			function cleanData(){
				$('#rol').val('');
				$('#documento').val('');
				$('#tipoDocumento').val('');
				$('#nombreCompleto').val('');
				$('#temperaturaIngreso').val('');
				$('#temperaturaSalida').val('');

				$('#tipoDocumento').prop('readonly', true);
				$('#tipoDocumento').attr('readonly', true);
				$('#nombreCompleto').prop('readonly', true);
				$('#temperaturaIngreso').prop('readonly', true);
				$('#temperaturaSalida').prop('readonly', true);
			}

			function updateTable(urlTable){
				$.get(urlTable, function(respuesta){
					$('#contenidoTabla').html(respuesta);
				});
			}
		});
	</script>
@endsection
