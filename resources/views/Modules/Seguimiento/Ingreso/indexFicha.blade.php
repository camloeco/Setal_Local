@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Fichas','') !!}
	<div class="row" id="urls" data-token="{{ csrf_token() }}" data-cambio-input = "{{ url('seguimiento/horario/modificarnumeroprograma') }}" data-cambio-select = "{{ url('seguimiento/horario/modificartipooferta') }}">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Fichas</span>
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
						    <table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Ficha</th>
									<th>Hora inicio</th>
									<th>Hora fin</th>
									<th>D&iacute;as</th>
									<th>Fecha inicio</th>
									<th>Fecha fin</th>
									<th>Instructor</th>
									<th>Ambiente</th>
									<th>Aforo</th>
									<th>Aprendices programados</th>
									@if($rol == 0 or $rol == 12)
									<th>Acci&oacute;n</th>
									@endif
								</tr>
							</thead>
							<tbody id="contenidoTabla" data-url="{{ url('seguimiento/ingreso/index?updateTable=yes') }}">
								<?php $contador = 1; 
								    $fecha_inicio = $fichas[0]->ing_fic_fecha_inicio; ?>
									@if($rol == 0 or $rol == 12)
										@foreach($fichas as $fic)
										<?php
											if($fecha_inicio != $fic->ing_fic_fecha_inicio){
												echo '<tr><td colspan="12" > Otra fecha inicio </td></tr>';
											}
										?>
										<tr>
											<td>{{ $contador++ }}</td>
											<td>{{ $fic->fic_numero }}</td>
											<td>{{ $fic->ing_fic_hor_inicio }}</td>
											<td>{{ $fic->ing_fic_hor_fin }}</td>
											<td>{{ $fic->ing_fic_dia }}</td>
											<td>{{ $fic->ing_fic_fecha_inicio }}</td>
											<td>{{ $fic->ing_fic_fecha_fin }}</td>
											<td>{{ ucwords(mb_strtolower($fic->nombreCorto)) }} {{ ucwords(mb_strtolower($fic->apellidoCorto)) }}</td>
											<td>{{ $fic->ing_fic_ambiente }}</td>
											<td>{{ $fic->ing_fic_aforo }}</td>
											<td>{{ $fic->totalAprendices }}</td>
											<td><a class="botonHorario" data-id="{{ $fic->ing_fic_id }}">Eliminar</a></td>
										</tr>
										<?php $fecha_inicio = $fic->ing_fic_fecha_inicio; ?>
										@endforeach
									@else
										@foreach($fichas as $fic)
										<?php
											if($fecha_inicio != $fic->ing_fic_fecha_inicio){
												echo '<tr><td colspan="12" > Otra fecha inicio </td></tr>';
											}
										?>
										<tr>
											<td>{{ $contador++ }}</td>
											<td>{{ $fic->fic_numero }}</td>
											<td>{{ $fic->ing_fic_hor_inicio }}</td>
											<td>{{ $fic->ing_fic_hor_fin }}</td>
											<td>{{ $fic->ing_fic_dia }}</td>
											<td>{{ $fic->ing_fic_fecha_inicio }}</td>
											<td>{{ $fic->ing_fic_fecha_fin }}</td>
											<td>{{ ucwords(mb_strtolower($fic->nombreCorto)) }} {{ ucwords(mb_strtolower($fic->apellidoCorto)) }}</td>
											<td>{{ $fic->ing_fic_ambiente }}</td>
											<td>{{ $fic->ing_fic_aforo }}</td>
											<td>{{ $fic->totalAprendices }}</td>
										</tr>
										<?php $fecha_inicio = $fic->ing_fic_fecha_inicio; ?>
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
	@if($rol == 0 or $rol == 12)
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
									<form id="eliminarProgramacion" data-url="{{ url('seguimiento/ingreso/eliminarprogramacionficha') }}">
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
	@endif
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
								window.location.href = window.location.href;
							}else{
								alert(respuesta);
							}
						}
					});
				}
			});
		});
	</script>
@endsection