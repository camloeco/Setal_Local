@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Ficha','Asistencia') !!}
	<style>
		.fila td, .fila th{
			padding:4px 0px 4px 0px;font-size:12px;vertical-align:middle;cursor:pointer;text-align:center;
		}
		.filaTitulo th{
			padding:2px;font-size:13px;vertical-align:middle;text-align:center;
		}
		.icono:hover{
			transform:scale(1.15)
		}
		.miTable{
			width:100%;
		}
		.miTable th,.miTable td{
			border: 1px solid;
		}
	</style>
	<div class="row" id="urls" data-url-retardo="{{ url('seguimiento/ficha/retardo') }}" data-token="{{ csrf_token() }}" data-cambio-input = "{{ url('seguimiento/horario/modificarnumeroprograma') }}" data-cambio-select = "{{ url('seguimiento/horario/modificartipooferta') }}">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Asistencia</span>
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
							@if(count($horariosInstructor)>0)
							<table class="miTable table-hover">
								<thead>
									<tr class="filaTitulo">
										<th>#</th>
										<th>Ficha</th>
										<th class="hidden-xs hidden-sm">Nivel</th>
										<th class="hidden-xs">Programa</th>
										<th class="hidden-lg hidden-md hidden-sm">Siglas</th>
										<th>D&iacute;a</th>
										<th>Hora</th>
										<th>Lista</th>
									</tr>
								</thead>
								<tbody data-url="{{url('seguimiento/ficha/asistenciaaprendices')}}" data-token="{{ csrf_token() }}">
									<?php $contador=1; ?>
									@foreach($horariosInstructor as $hor)
										<?php
											$horaInicio = $hor->pla_fic_det_hor_inicio.':00';
											if($hor->pla_fic_det_hor_inicio < 10){
												$horaInicio = '0'.$hor->pla_fic_det_hor_inicio.':00';
											}
											
											$horaFin = $hor->pla_fic_det_hor_fin.':00';
											if($hor->pla_fic_det_hor_fin < 10){
												$horaFin = '0'.$hor->pla_fic_det_hor_fin.':00';
											}
											
											$celda = '';
											$icono = '';
											if(date('N') == $hor->pla_dia_id){
												$celda = "background:#087b76;border:1px solid black;";
												$icono = "color:white;";
											}
										?>
										<tr class="fila">
											<th>{{ $contador++ }}</th>
											<td class="ficha">{{ $hor->fic_numero }}</td>
											<td class="hidden-xs hidden-sm">{{ $hor->niv_for_nombre }}</td>
											<td class="programa hidden-xs">{{ $hor->prog_nombre }}</td>
											<td class="hidden-lg hidden-md hidden-sm">{{ $hor->prog_sigla }}</td>
											<td class="dia">{{ $diasOrtografia[$hor->pla_dia_id] }}</td>
											<td class="hora">{{ $horaInicio }} - {{ $horaFin }}</td>
											<td style="font-size:17px; padding:0px;{{ $celda }}">
												<a style="{{ $icono }}" class="activarModal" title="Asistencia ficha {{ $hor->fic_numero }}" data-nombre-modal="modalAsistencia" data-id="{{ $hor->pla_fic_det_id }}">
													Ver
												</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							@else 
								<div class="alert alert-danger" style="margin-bottom: 5px;">
									<strong>Alerta!</strong> Usted no tiene fichas asociadas en este trimestre, por favor comuniquese con la coordinación académica a la que Usted pertenece.</strong>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="modalAsistencia" class="modal fade" role="dialog">
        <div class="modal-dialog" >
            <!-- Modal content-->
            <div class="modal-content">
                <div class="text-center modal-header" style="background-color:#309591;color:white;padding:10px;">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 style="margin:0px;font-size:15px;"><strong>Ficha:</strong> <data id="ficha"></data></h5>
					<h5 style="margin:0px;"><strong>Programa:</strong> <data id="programa"></data></h5>
					<h5 style="margin:0px;">
						<strong>D&iacute;a:</strong> <data id="dia"></data>
						<strong>Hora:</strong> <data id="hora"></data>
					</h5>
                </div>
				<input id="_token" type="hidden" name="_token" value="{{ csrf_token() }}">
				<input id="url" type="hidden" value="{{ url('seguimiento/ficha/inasistencia') }}">
				<input id="urlInstructor" type="hidden" value="{{ url('seguimiento/ficha/asistenciainstructor') }}">
				<div class="modal-body" style="margin-top:-13px;" id="contenidomodalAsistencia">

				</div>
				<div class="modal-footer" style="padding:10px;">
					<a type="button" style="margin:0px;" class="btn btn-danger btn-xs" data-dismiss="modal">Cerrar</a>
				</div>
            </div>
        </div>
    </div>
@endsection