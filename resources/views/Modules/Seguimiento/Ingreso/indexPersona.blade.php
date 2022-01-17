@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Personas','') !!}
	<div class="row" id="urls" data-token="{{ csrf_token() }}" data-cambio-input = "{{ url('seguimiento/horario/modificarnumeroprograma') }}" data-cambio-select = "{{ url('seguimiento/horario/modificartipooferta') }}">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Personas</span>
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
							<table class="text-center table table-bordered table-hover">
								<thead>
									<tr>
										<th class="text-center">#</th>
										<th class="text-center">Rol</th>
										<th class="text-center">Tipo doc.</th>
										<th class="text-center">Documento</th>
										<th class="text-center">Nombre completo</th>
										<th class="text-center">Ficha</th>
										<th class="text-center">Habilitado</th>
										<th class="text-center">Restricci&oacute;n</th>
										<th class="text-center">Capacitaci&oacute;n</th>
										<th class="text-center">Priorizado</th>
										<th class="text-center">Horario</th>
									</tr>
								</thead>
								<tbody id="contenidoTabla" data-token="{{ csrf_token() }}" data-url="{{ url('seguimiento/ingreso/programarver') }}">
									<?php $contador = 1;
									if($rol == 12 or $rol == 0){ ?>
									@foreach($personas as $per)
									<tr>
										<td>{{ $contador++ }}</td>
										<td>{{ $per->ing_est_rol }}</td>
										<td>{{ $per->ing_est_tip_documento }}</td>
										<td>{{ $per->ing_est_documento }}</td>
										<td>{{ $per->ing_est_nombre }}</td>
										<td>{{ $per->ing_est_ficha }}</td>
										<td>{{ $per->ing_est_ingresa }}</td>
										<td>{{ $per->restriccion }}</td>
										<td>{{ $per->capacitacion }}</td>
										<td>{{ $per->priorizado }}</td>
										@if($per->ing_est_rol != "Externo" and $per->ing_est_ingresa == "si")
										<td>
											<a class="botonHorario ver" data-id="{{ $per->ing_est_documento }}">ver</a> -
											<a href="{{ url('seguimiento/ingreso/programarpersona?documento=') }}{{ $per->ing_est_documento }}" class="botonHorario">programar</a>
										</td>
										@else
										<td></td>
										@endif
									</tr>
									@endforeach
									<?php }else{ ?>
									@foreach($personas as $per)
									<tr>
										<td>{{ $contador++ }}</td>
										<td>{{ $per->ing_est_rol }}</td>
										<td>{{ $per->ing_est_tip_documento }}</td>
										<td>{{ $per->ing_est_documento }}</td>
										<td>{{ $per->ing_est_nombre }}</td>
										<td>{{ $per->ing_est_ficha }}</td>
										<td>{{ $per->ing_est_ingresa }}</td>
										<td>{{ $per->restriccion }}</td>
										<td>{{ $per->capacitacion }}</td>
										<td>{{ $per->priorizado }}</td>
										@if($per->ing_est_rol != "Externo" and $per->ing_est_ingresa == "si")
										<td><a class="botonHorario ver" data-id="{{ $per->ing_est_documento }}">ver</a></td>
										@else
										<td></td>
										@endif
									</tr>
									@endforeach
									<?php } ?>
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
				<h4 class="modal-title">Programaci&oacute;n</h4>
			</div>
			<div class="modal-body">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>#</th>
							<th>Nombre</th>
							<th>Ficha</th>
							<th>D&iacute;as</th>
							<th>Franja</th>
							<th>Fecha inicio</th>
							<th>Fecha fin</th>
						</tr>
					</thead>
					<tbody class="contenidoTabla">
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>

		</div>
	</div>
@endsection
@section('plugins-css')
<style>
	.botonHorario{
		cursor: pointer;
	}
	.botonHorario:hover {
		color: #ec7114;
	}
	.table  th, .table  td{
		text-align: center;
		font-size: 12px;
	}
	.table tbody tr td, .table thead tr th{
		vertical-align: middle;
		padding: 2px;
	}
	tr{
		cursor: pointer;
	}
</style>
@endsection
@section('plugins-js')
	<script type="text/javascript">
		$(document).ready(function () {
			$(document).on('click','.ver',function(){
				var documento = $(this).attr('data-id');
				var token = $('tbody').attr('data-token');
				var url = $('tbody').attr('data-url');

				if(documento === '' || !Number.isInteger(parseInt(documento)) || documento <= 0){
					alert('<h3>El campo <strong>Número de documento</strong> es obligatorio y debe ser numérico.</h3>.');
				}else{
					$.post(url, {'_token':token, 'documento':documento}, function(respuesta){
						$('.contenidoTabla').html('');
						$('.contenidoTabla').html(respuesta);
						$('#myModal').modal();
					});
				}
			});
		});
	</script>
@endsection