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