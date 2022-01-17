@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Seguimiento a proyectos','Generar falta o informe') !!}
<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Listado de faltas o informes</span>
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
            <div class="box-content" >
				<div class="row">
					<!--
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
						<div class="input-group">
							<input placeholder="Buscar..." type="text" class="form-control">
							<a class="input-group-addon" href=""><span>Buscar</span></a>
						</div>
					</div>-->
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<table class="table table-striped">
							<thead>
								<tr>
									<th style="padding:1px;">#</th>
									<th style="padding:1px;">Fecha realizaci&oacute;n</th>
									<th style="padding:1px;">Tipo</th>
									<th style="padding:1px;">Estado</th>
									<th style="padding:1px;text-align:center;">Descargar</th>
									<th style="padding:1px;text-align:center;">Motivo rechazo</th>
									<th style="padding:1px;text-align:center;">Ver</th>
								</tr>
							</thead>
							<tbody data-url="{{url('seguimiento/educativa/modalrechazo')}}" data-token="{{ csrf_token() }}">
								@foreach($faltasInstructor as $comuna)
								<tr>
									<td style="padding:1px;font-size:13px;">{{ $contador++ }}</td>
									<td style="padding:1px;font-size:13px;">{{ $comuna->edu_falta_fecha }}</td>
									<td style="padding:1px;font-size:13px;"><code>{{ $comuna->edu_tipo_falta_descripcion }}</code></td>
									<td style="padding:1px;font-size:13px;"><span style="font-size:10px;" class="tag tag-{{ $estado[$comuna->edu_est_descripcion] }}">{{ $comuna->edu_est_descripcion }}</span></td>
									<td style="padding:1px;text-align:center;"><a title="Descargar falta" href="{{asset('/Modules/Seguimiento/Educativa/Queja/'.$comuna->par_identificacion.'-'.$comuna->edu_falta_id.'.docx') }}" target='_blank'>Descargar</a></td>
									@if($comuna->edu_est_descripcion == 'RECHAZADO')
									<td style="padding:1px;text-align:center;"><a style="cursor:pointer;" class="activarModal" title="Modal" data-nombre-modal="modalRechazo" data-id="{{ $comuna->edu_falta_id }}">Rechazo</a></td>
									@else
									<td></td>
									@endif
									<td style="padding:1px;text-align:center;"><a title="Modal" href="#" data-estado="{{ $comuna->edu_est_descripcion }}" data-id="{{ $comuna->edu_falta_id }}" data-url="{{url("seguimiento/educativa/verdetalle")}}" class='cargarAjax' data-toggle="modal" data-target="#modal">Ver</a></td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				@if($cantidadPaginas > 1)
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<small style="float:left;"><strong>Total registros:</strong> {{ $contadorFaltasInstructor }}</small>
						@for($i=$cantidadPaginas; $i>0; $i--)
							<?php
								$style='';
								if($i == $pagina){
									$style=";background:#087b76; color:white;";
								}
							?>
							<a href="{{ url('seguimiento/educativa/listarqueja') }}?pagina=<?php echo $i; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $i }}</button></a>
						@endfor
					</div>
				</div>
				@endif
            </div>
        </div>
    </div>
	
    <!--/span-->
    <div id="modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Detalle</h4>
                </div>
                <div class="modal-body" id="modalBody">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalRechazo" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Detalle</h4>
                </div>
                <div class="modal-body" id="contenidomodalRechazo">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
