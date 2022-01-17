@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Educativa','Faltas pendientes') !!}
<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-table"></i>
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
									<th style="padding:1px;">Coordinador</th>
									<th style="padding:1px;">Instructor</th>
									<th style="padding:1px;text-align:center;">Ver</th>
								</tr>
							</thead>
							<tbody>
								@foreach($faltasInstructor as $comuna)
								<tr>
									<td style="padding:1px;font-size:13px;">{{ $contador++ }}</td>
									<td style="padding:1px;font-size:13px;">{{ $comuna->edu_falta_fecha }}</td>
									<td style="padding:1px;font-size:13px;"><code>{{ $comuna->edu_tipo_falta_descripcion }}</code></td>
									<td style="padding:1px;font-size:13px;"><span style="font-size:10px;" class="tag tag-primary">Pendiente</span></td>
									<td style="padding:1px;font-size:13px;">{{ $comuna->nombreCoordinador }}</td>
									<td style="padding:1px;font-size:13px;">{{ $comuna->nombreInstructor }}</td>
									<td style="padding:1px;text-align:center;"><a title="Modal" href="#" data-estado="PENDIENTE" data-id="{{ $comuna->edu_falta_id }}" data-url="{{url("seguimiento/educativa/verdetalle")}}" class='cargarAjax' data-toggle="modal" data-target="#modal"><i class="fa fa-eye"></i></a></td>
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
							<a href="{{ url('seguimiento/educativa/quejaspendientes') }}?pagina=<?php echo $i; ?>"><button style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $i }}</button></a>
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
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
