@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Ambiente','Listado') !!}
	<div class="row">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Ambiente - listado</span>
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
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
							<table class="table table-bordered table-hover">
								<thead>
									<tr>
										<th class="text-center">#</th>
										<th class="text-center">C&oacute;digo</th>
										<th class="text-center">Descripci&oacute;n</th>
										<th class="text-center">Tipo</th>
										<th class="text-center">Suma horas</th>
										<th class="text-center">Estado</th>
										@if($rol == 8 or $rol == 0)
										<th class="text-center">Acciones</th>
										@endif
									</tr>
								</thead>
								<tbody id="url" data-url="{{ url('seguimiento/horario/ambientemodalmodificar') }}">
			<?php				$contador = 1;
									foreach($ambientes as $ambiente){ ?>
									<tr>
										<td>{{ $contador++ }}</td>
										<td>{{ $ambiente->pla_amb_id }}</td>
										<td>{{ $ambiente->pla_amb_descripcion }}</td>
										<td>{{ $ambiente->pla_amb_tipo }}</td>
										<td>{{ $ambiente->pla_amb_suma_horas }}</td>
										<td>{{ $ambiente->pla_amb_estado }}</td>
										@if($rol == 8 or $rol == 0)
										<td><img class="ambienteModificar" data-id-coordinador="{{ $ambiente->par_id_coordinador }}" data-amb-estado="{{ $ambiente->pla_amb_estado }}" data-amb-suma-horas="{{ $ambiente->pla_amb_suma_horas }}"  data-amb-tipo="{{ $ambiente->pla_amb_tipo }}" data-amb-id="{{ $ambiente->pla_amb_id }}" style="height:20px; cursor:pointer;" title="Editar" src="{{ asset('img/horario/Editar.png')}}"></td>
										@endif
									</tr>
			<?php				}	?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div id="modalAmbiente" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Modificar ambiente</h4>
				</div>
				<form id="formularioModalAmbiente" data-url="{{ url('seguimiento/horario/ambientemodalmodificar') }}">
					<div class="modal-body" id="contenidoModal">
						<p>Some text in the modal.</p>
					</div>
				</form>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				</div>
			</div>

		</div>
	</div>
@endsection