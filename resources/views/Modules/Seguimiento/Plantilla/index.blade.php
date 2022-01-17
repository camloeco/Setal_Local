@extends('templates.devoops')

@section('content')
	{!! getHeaderMod('Seguimiento a proyectos','Creaci&oacute;n de plantilla') !!}
	<div class="row">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Plantillas</span>
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
					<form action="{{ url('seguimiento/horario/plantilla') }}" method="post">
						<div class="row">
							<div class="col-lg-4">
								
							</div>
							<div class="row">
								<div class="col-lg-12">
									<hr>
								</div>
							</div>
							<div id="contenedorFila" class="row">	
								<div id="fila" class="col-lg-12 fila">
									<div class="col-lg-12">
										<table class="table table-striped" id="miTabla">
											<thead>
												<tr>
													<th>C&oacute;digo</th>
													<th>Nombre</th>
													<th>Versi&oacute;n</th>
													<th>Estado</th>
													<th></th>
													<!--<th></th>-->
												</tr>
											</thead>
											<tbody>
												@foreach($plantillas as $pla)
												<tr>
													<td>{{ $pla->prog_codigo }}</td>
													<td>{{ $pla->prog_nombre }}</td>
													<td>{{ $pla->pla_version }}</td>
													<td>{{ $pla->pla_estado }}</td>
													<td><a class="btn btn-primary btn-xs contenidoPlantilla" data-id="{{ $pla->pla_id }}" data-url="{{ url('seguimiento/plantilla/contenidomodal') }}" data-toggle="modal" data-target="#myModal">Ver<a></td>
													<!--<td><a href="{{ url('seguimiento/horario/editar') }}" class="btn btn-success btn-xs">Editar<a></td>-->
												</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
							</div>	
						</div>
					</form>
					<a href="{{ url('seguimiento/plantilla/create') }}" class="btn btn-primary btn-sm">Crear plantilla</a>
				</div>
			</div>
			
			<!-- Modal -->
			<div id="myModal" class="modal fade" role="dialog">
				<div class="modal-dialog" style="width:94%;">
				
					<!-- Modal content-->
					<div class="modal-content">
						<div id="contenidoModal">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
							<div class="modal-body">
								<h5 style="text-align:center;">Cargando...</h5>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</div>
@endsection