@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Certificación','Listado') !!}
	<div class="row" id="urls" data-token="{{ csrf_token() }}" data-cambio-input = "{{ url('seguimiento/horario/modificarnumeroprograma') }}" data-cambio-select = "{{ url('seguimiento/horario/modificartipooferta') }}">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Certificación - Listado</span>
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
							<div style="overflow-y: auto;">
								<table class="table table-hover">
		                            <thead>
		                                <tr>
		                                    <th>#</th>
		                                    <th>Documento</th>
		                                    <th>Nombre</th>
		                                    <th>Ficha</th>
		                                    <th>Contactar</th>

		                                    <th>Coordinaci&oacute;n acad&eacute;mica</th>
		                                    <th>Observaciones - Coordinaci&oacute;n acad&eacute;mica</th>

		                                    <th>Instructor seguimiento etapa productiva</th>
		                                    <th>Observaciones - Instructor seguimiento etapa productiva</th>

		                                    <th>Responsable bienestar al Aprendiz</th>
		                                    <th>Observaciones - Responsable bienestar al Aprendiz</th>

		                                    <th>Reponsable agencia p&uacute;blica de empleo</th>
		                                    <th>Observaciones - Reponsable agencia p&uacute;blica de empleo</th>

		                                    <th>Biblioteca</th>
		                                    <th>Observaciones - Biblioteca</th>

		                                    <th>Acci&oacute;n</th>
		                                </tr>
		                            </thead>
		                            <tbody>
		                            	<?php $contador = 1; ?>
		                            	@foreach($aprendices as $apr)
		                            	<tr>
		                            		<td>{{ $contador++ }}</td>
		                            		<td class="numero_documento">{{ $apr->par_identificacion }}</td>
		                            		<td class="nombre_completo">{{ ucfirst(mb_strtolower($apr->par_nombres, 'UTF-8')) }}<br>{{ ucfirst(mb_strtolower($apr->par_apellidos, 'UTF-8')) }}</td>
		                            		<td class="ficha">{{ $apr->fic_numero }}</td>
		                            		<td><button data-telefono="{{ $apr->par_telefono }}" data-correo="{{ $apr->par_correo }}" class="contactar">Contactar</button></td>

		                            		<td><input class="form-control" type="checkbox"></td>
		                            		<td><textarea cols="20" rows="4" class="form-control"></textarea></td>

		                            		<td><input class="form-control" type="checkbox"></td>
		                            		<td><textarea cols="20" rows="4" class="form-control"></textarea></td>

		                            		<td><input class="form-control" type="checkbox"></td>
		                            		<td><textarea cols="20" rows="4" class="form-control"></textarea></td>

		                            		<td><input class="form-control" type="checkbox"></td>
		                            		<td><textarea cols="20" rows="4" class="form-control"></textarea></td>

		                            		<td><input class="form-control" type="checkbox"></td>
		                            		<td><textarea cols="20" rows="4" class="form-control"></textarea></td>
		                            		<td><button>Certificar</button></td>
		                            	</tr>
		                            	@endforeach
		                            </tbody>
		                        </table>
							</div>
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
					<h4 class="modal-title">Datos de contacto</h4>
				</div>
				<div class="modal-body">
					<h4>N&uacute;mero documento</h4>
					<h5 id="numero_documento"></h5><br>

					<h4>Nombre completo</h4>
					<h5 id="nombre_completo"></h5><br>

					<h4>Ficha</h4>
					<h5 id="ficha"></h5><br>

					<h4>Tel&eacute;fono</h4>
					<h5 id="telefono"></h5><br>

					<h4>Correo</h4>
					<h5 id="correo"></h5>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('plugins-css')
<style>
	.modal-body{
		text-align: center !important;
	}

	th, td{
		text-align: center !important;
		padding: 3px !important;
		font-size: 12px !important;
	}

	td{
		vertical-align: middle !important;
		white-space: pre !important;
	}

	input{
		height: 15px !important;
	}

	textarea{
		width: auto !important;
		font-size: 12px !important;
	}
</style>
@endsection
@section('plugins-js')
	<script type="text/javascript">
		$(document).ready(function () {
			$(document).on('click','.contactar',function(){
				var telefono = $(this).attr('data-telefono');
				var correo = $(this).attr('data-correo');
				var nombre_completo = $(this).parent().parent().find('.nombre_completo').html();
				var numero_documento = $(this).parent().parent().find('.numero_documento').html();
				var ficha = $(this).parent().parent().find('.ficha').html();

				$('#nombre_completo').html(nombre_completo);
				$('#numero_documento').html(numero_documento);
				$('#correo').html(correo);
				$('#telefono').html(telefono);
				$('#ficha').html(ficha);

				$('#myModal').modal('show');
			});
		});
	</script>
@endsection