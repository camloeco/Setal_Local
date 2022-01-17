@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Ingreso','Habilitar') !!}
	<div class="row" id="urls" data-url-retardo="{{ url('seguimiento/ficha/retardo') }}" data-token="{{ csrf_token() }}" data-cambio-input = "{{ url('seguimiento/horario/modificarnumeroprograma') }}" data-cambio-select = "{{ url('seguimiento/horario/modificartipooferta') }}">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Habilitar</span>
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
							@if(count($validar_instructor_ingreso) == 0)
								<div class="alert alert-danger" style="margin-bottom: 5px;">
									Usted no está registrado en listado de retorno gradual, comunicar al Coordinador de su área.</strong>
								</div>
							@elseif($validar_instructor_ingreso[0]->ing_est_ingresa == 'no')
								<div class="alert alert-danger" style="margin-bottom: 5px;">
									Usted no está habilitado para poder retornar al CDTI, comunicar al Coordinador de su área.</strong>
								</div>
							@elseif(count($ficha)==0)
								<div class="alert alert-danger" style="margin-bottom: 5px;">
									Usted no tiene fichas asociadas para habilitar aprendices o no hay aprendices habilitados.</strong>
								</div>
							@else
								<table class="table table-hover table-bordered">
									<thead>
										<tr>
											<th>#</th>
											<th>Seleccione</th>
											<th>Ficha</th>
											<th>Programa</th>
											<th>Fecha inicio</th>
											<th>Fecha fin</th>
											<th>D&iacute;a(s)</th>
											<th>Hora inicio</th>
											<th>Hora fin</th>
											<th>Aforo</th>
											<th>Ambiente</th>
										</tr>
									</thead>
									<tbody>
										<?php $contador = 1; ?>
										@foreach($ficha as $key => $fic)
											<tr>
												<td>
													<span>{{ $contador++ }}</span>
												</td>
												<td>
													<span>
														<input class="consultar_ficha" name="consultar_ficha" type="radio">
													</span>
												</td>
												<td>
													<span class="ficha">{{ $fic->fic_numero }}</span>
												</td>
												<td>
													<span>{{ $fic->prog_nombre }}</span>
												</td>
												<td>
													<span class="fecha_inicio">{{ $fic->ing_fic_fecha_inicio }}</span>
												</td>
												<td>
													<span class="fecha_fin">{{ $fic->ing_fic_fecha_fin }}</span>
												</td>
												<td>
													<span class="dia">{{ $fic->ing_fic_dia }}</span>
												</td>
												<td>
													<span class="hora_inicio">{{ $fic->ing_fic_hor_inicio }}</span>
												</td>
												<td>
													<span class="hora_fin">{{ $fic->ing_fic_hor_fin }}</span>
												</td>
												<td>
													<span>{{ $fic->ing_fic_aforo-1 }}</span>
												</td>
												<td>
													<span>{{ $fic->ing_fic_ambiente }}</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
								<table class="table table-bordered table-hover">
									<thead>
										<tr>
											<th>Habilitado</th>
											<th>Nombre completo</th>
											<th>Documento</th>
										</tr>
									</thead>
									<tbody id="contenidoTabla" url-habilitar="{{ url('seguimiento/ingreso/enableaprendiz') }}" url-aprendices-tabla="{{ url('seguimiento/ingreso/aprendicestabla') }}" token="{{ csrf_token() }}"></tbody>
								</table>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('plugins-css')
<link rel="stylesheet" href="{{ asset('css/alertify.min.css') }}">
<style>
	.alertify-notifier > div{
		color: white;
		text-align: center;
		border: black 1px solid;
		font-size: 15px;
	}
	.notificaciones{
		margin: 0px;
	}
	.notificaciones h5{
		margin: 0px 0px 5px 0px;
	}
	#consultar{
		cursor: pointer;
		padding: 8px;
	}
	#consultar:hover{
		color: blue;
	}
	#documento{
		margin-bottom: 5px;
	}
	.table  th, .table  td{
		text-align: center;
		font-size: 12px;
	}
	.table tbody tr td{
		vertical-align: middle;
		padding: 1px;
	}
	input{
		text-align: center;
	}
	.registrar:hover{
		background: #087b76;
		color: white;
	}
	.box-content .row{
		padding: 0px 10px 0px 10px;
	}
</style>
@endsection
@section('plugins-js')
	<script type="text/javascript" src="{{ asset('js/alertify.min.js') }}"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			$(document).on('change','.consultar_ficha', function(){
				var valor = $(this).val();
				if(valor !== 'on' && valor == 'si' && valor == 'no'){
					alertify.error('Valor <strong>seleccionado</strong> incorrecto.'); return;
				}

				if(valor == 'on'){
					var elemento = $(this).parent().parent().parent();
				}else{
					var elemento = $('input:radio[name=consultar_ficha]:checked').parent().parent().parent();
				}

				// Declaración de variables
				var ficha = elemento.find('.ficha').html();
				var fecha_inicio = elemento.find('.fecha_inicio').html();
				var fecha_fin = elemento.find('.fecha_fin').html();
				var dia = elemento.find('.dia').html();
				var hora_inicio = elemento.find('.hora_inicio').html();
				var hora_fin = elemento.find('.hora_fin').html();

				// Validaciones
				if(typeof ficha === 'undefined' || ficha == '' || !Number.isInteger(parseInt(ficha))){
					alertify.error('La <strong>ficha</strong> es obligatoria y debe ser numérica.'); return;
				}

				if(typeof fecha_inicio === 'undefined' || fecha_inicio == ''){
					alertify.error('La <strong>fecha inicio</strong> es obligatoria'); return;
				}

				if(typeof fecha_fin === 'undefined' || fecha_fin == ''){
					alertify.error('La <strong>fecha fin</strong> es obligatoria'); return;
				}

				if(typeof dia === 'undefined' || dia == ''){
					alertify.error('Los <strong>días</strong> son obligatorios'); return;
				}

				if(typeof hora_inicio === 'undefined' || hora_inicio == ''){
					alertify.error('La <strong>hora inicio</strong> son obligatorios'); return;
				}

				if(typeof hora_fin === 'undefined' || hora_fin == ''){
					alertify.error('La <strong>hora fin</strong> son obligatorios'); return;
				}

				// Proceso
				var token = $("#contenidoTabla").attr('token');
				if(valor == 'on'){
					var url = $('#contenidoTabla').attr("url-aprendices-tabla");
					var datos = {
						'_token' : token, 'ficha' : ficha,'fecha_inicio' : fecha_inicio,
						'fecha_fin' : fecha_fin,'dia' : dia, 'hora_inicio' : hora_inicio,
						'hora_fin' : hora_fin
					};
				
					$.post(url, datos, function(respuesta){
						var data = JSON.parse(respuesta);
					    console.log(data);
						if(data.exito == 'si'){
							$('#contenidoTabla').html(data.tabla);
							$('#contenidoTabla').show('fast');
							alertify.success(data.mensaje);
						}else{
							$('#contenidoTabla').html('');
							alertify.error(data.mensaje);
						}
					});
				}else{
					var documento = $(this).parent().parent().find('.documento').html();
					if(typeof documento === 'undefined' || documento == '' || !Number.isInteger(parseInt(documento))){
						alertify.error('El <strong>documento</strong> es obligatoria y debe ser numérica.'); return;
					}

					var url = $('#contenidoTabla').attr("url-habilitar");
					var datos = {
						'_token' : token, 'ficha' : ficha,'fecha_inicio' : fecha_inicio,
						'fecha_fin' : fecha_fin,'dia' : dia, 'hora_inicio' : hora_inicio,
						'hora_fin' : hora_fin, 'valor' : valor, 'documento' : documento
					};

					elemento = $(this);
					$.post(url, datos, function(respuesta){
						var data = JSON.parse(respuesta);
						//console.log(data);
						if(data.exito == 'si'){
							alertify.success(data.mensaje);
						}else{
							alertify.error(data.mensaje);
							//elemento.val('no');
						}

						if(data.cambiarValor == 'si'){
							elemento.val('no');
						}
					});
				}
			});

			$(document).on('change','.aprendiz', function(){
				// Declaración de variables
				var elemento = $('input:radio[name=consultar_ficha]:checked').parent().parent().parent();
				var ficha = elemento.find('.ficha').html();
				var fecha_inicio = elemento.find('.fecha_inicio').html();
				var fecha_fin = elemento.find('.fecha_fin').html();
				var dia = elemento.find('.dia').html();
				var hora_inicio = elemento.find('.hora_inicio').html();
				var hora_fin = elemento.find('.hora_fin').html();

				// Validaciones
				if(typeof ficha === 'undefined' || ficha == '' || !Number.isInteger(parseInt(ficha))){
					alertify.error('La <strong>ficha</strong> es obligatoria y debe ser numérica.'); return;
				}

				if(typeof fecha_inicio === 'undefined' || fecha_inicio == ''){
					alertify.error('La <strong>fecha inicio</strong> es obligatoria'); return;
				}

				if(typeof fecha_fin === 'undefined' || fecha_fin == ''){
					alertify.error('La <strong>fecha fin</strong> es obligatoria'); return;
				}

				if(typeof dia === 'undefined' || dia == ''){
					alertify.error('Los <strong>días</strong> son obligatorios'); return;
				}

				if(typeof hora_inicio === 'undefined' || hora_inicio == ''){
					alertify.error('La <strong>hora inicio</strong> son obligatorios'); return;
				}

				if(typeof hora_fin === 'undefined' || hora_fin == ''){
					alertify.error('La <strong>hora fin</strong> son obligatorios'); return;
				}

				// Proceso
				var url = $('#contenidoTabla').attr("url-aprendices-tabla");
				var token = $("#contenidoTabla").attr('token');
				var datos = {
					'_token' : token, 'ficha' : ficha,'fecha_inicio' : fecha_inicio,
					'fecha_fin' : fecha_fin,'dia' : dia, 'hora_inicio' : hora_inicio,
					'hora_fin' : hora_fin,
				};

				//console.log(ficha);
				return;
				var elemento = $(this);
				var valor = elemento.val();
				var fecha = $("#fecha").val();
				var ficha = $("#ficha").val();
				var documento = $(this).parent().parent().find('.documento').html();
				var url = $('#contenidoTabla').attr("url-habilitar");
				var token = $("#contenidoTabla").attr('token');

				//Validaciones
				if(typeof fecha === 'undefined' || fecha == ''){
					alertify.error('La campo <strong>fecha</strong> es obligatorio.'); return;
				}

				if(typeof valor === 'undefined' || valor == '' || (valor != 'si' && valor != 'no')){
					alertify.error('La campo <strong>habilitado</strong> es obligatorio y debe ser si o no.'); return;
				}

				if(typeof documento === 'undefined' || documento == '' || !Number.isInteger(parseInt(documento))){
					alertify.error('El <strong>documento</strong> es obligatoria y debe ser numérica.'); return;
				}

				$.post(url, {'_token' : token, 'ficha' : ficha, 'fecha' : fecha,  'valor' : valor, 'documento' : documento}, function(respuesta){
					var data = JSON.parse(respuesta);
					if(data.exito == 'si'){
						alertify.success(data.mensaje);
					}else{
						alertify.error(data.mensaje);
					}

					if(data.cambiarValor == 'si'){
						elemento.val('no');
					}
				});
			});

			function limpiarDatos(){
				$('#hora').val('');
				$('#dia').val('');
				$('#contenidoTabla').html('');
			}
		});
	</script>
@endsection