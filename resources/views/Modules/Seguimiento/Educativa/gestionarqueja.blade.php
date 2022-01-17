@extends('templates.devoops')

@section('content')


{!! getHeaderMod('Educativa','Gestionar faltas') !!}

<div class="row">

    @if (isset($mensaje))
    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Mensaje de respuesta</span>
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
                @if (isset($mensaje['exito']))
                <div class="alert alert-success">
                    <button data-dismiss="success" class="close" type="button">×</button>
                    {{ $mensaje['exito'] }}
                </div>
                @endif

                @if (isset($mensaje['errores']))
                <div class="alert alert-danger">
                    <button data-dismiss="alert" class="close" type="button">×</button>

                    <ul>
                        @foreach ($mensaje['errores'] as $key=>$msg)
                        <li>{{ $msg }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

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
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-6" style="float:right;">
                                <form method="GET" action="{{ url('seguimiento/educativa/gestionarqueja') }}">
                                    <div class="input-group input-group-xs">
                                    <span style="cursor:pointer;" class="input-group-addon"><a href="{{ url('seguimiento/educativa/gestionarqueja') }}">Limpiar filtro</a></span>
                                        @if($par_identificacion == '')
                                            <input autocomplete="off" required style="border: 1px solid #ccc;padding: 2px 12px;width: 260px;" class="form-control" list="browsers" name="par_identificacion" placeholder="Buscar instructor...">
                                        @else
                                            <input autocomplete="off" value="{{ $par_identificacion }}" required style="border: 1px solid #ccc;padding: 2px 12px;width: 260px;" class="form-control" list="browsers" name="par_identificacion" placeholder="Buscar instructor...">
                                            <?php $par_identificacion = '&par_identificacion='.$par_identificacion?>
                                        @endif
                                        <datalist id="browsers">
                                            @foreach($instructores AS $ins)	
                                                <option value="{{$ins->par_identificacion}}">{{$ins->par_nombres}} {{$ins->par_apellidos}}</option>
                                            @endforeach
                                        </datalist>
                                        <input style="border: 1px solid #ccc;border-radius: 0px 4px 4px 0px;background: #eee;height: 26px;color: #087b76;" type="submit" value="Buscar">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="padding:1px;">#</th>
                                    <th style="padding:1px;">C&oacute;digo</th>
                                    <th style="padding:1px;">Identificaci&oacute;n</th>
                                    <th style="padding:1px;">Instructor</th>
                                    <th style="padding:1px;">Fecha</th>
                                    <th style="padding:1px;">Tipo</th>
                                    <th style="padding:1px;">Estado</th>
                                    <th style="padding:1px;text-align:center;">Ver</th>
                                    <th style="padding:1px;text-align:center;" colspan="2">Acciones</th>
                                    <th style="padding:1px;text-align:center;">Asignar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $inicioContador = $contador; ?>
                                @foreach($faltasInstructor as $comuna)
                                    <tr>
                                        <td style="padding:1px;font-size:13px;">{{ $contador++ }}</td>
                                        <td style="padding:1px;font-size:13px;">{{ $comuna->edu_falta_id }}</td>
                                        <td style="padding:1px;font-size:13px;">{{ $comuna->par_identificacion }}</td>
                                        <td style="padding:1px;font-size:13px;">{{ $comuna->nombreInstructor }}</td>
                                        <td style="padding:1px;font-size:13px;">{{ $comuna->edu_falta_fecha }}</td>
                                        <td style="padding:1px;font-size:13px;"><code>{{ $comuna->edu_tipo_falta_descripcion }}</code></td>
                                        <td style="padding:1px;font-size:13px;"><span class="tag tag-{{ $estado[$comuna->edu_est_descripcion] }}">{{ $comuna->edu_est_descripcion }}</span></td>
                                        <td style="padding:1px;font-size:13px;text-align:center;"><a title="Modal" href="#" data-estado="{{ $comuna->edu_est_descripcion }}" data-id="{{ $comuna->edu_falta_id }}" data-url="{{url("seguimiento/educativa/verdetalle")}}" class='cargarAjax' data-toggle="modal" data-target="#modal">Ver</a></td>
                                        @if($comuna->edu_est_descripcion == "PENDIENTE")
                                        <td style="padding:1px;font-size:13px;text-align:center;"><a style="font-weight: bold;"href="{{ url('seguimiento/educativa/aprobarqueja/'.$comuna->edu_falta_id) }}" class="ajax-link">Aprobar</a></td>
                                        <td style="padding:1px;font-size:13px;text-align:center;"><a style="color:red;font-weight: bold;" href="{{ url('seguimiento/educativa/rechazarqueja/'.$comuna->edu_falta_id) }}" class="ajax-link">Rechazar</a></td>
                                        @else
                                        <td></td>
                                        <td></td>
                                        @endif
                                        <td style="padding:5px;font-size:11px;text-align:center;">
                                            <select class="form-control" id="asignar_coordinador" style="width:210px" data-falta="{{$comuna->edu_falta_id}}" data-url="{{url('seguimiento/educativa/coordinador')}}">
                                                @foreach($coordinadores as $cor)
                                                    <?php 
                                                       $selected="";
                                                       if($comuna->par_identificacion_coordinador == $cor->par_identificacion){
                                                        $selected = "selected";
                                                        }
                                                    ?>
                                                    <option value="{{$cor->par_identificacion}}" {{$selected}}>{{$cor->par_nombres." ".$cor->par_apellidos}} </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($cantidadPaginas > 1)
                    @if($cantidadPaginas <= 10)
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            @for($i=$cantidadPaginas; $i>0; $i--)
                                <?php
                                    $style='';
                                    if($i == $pagina){
                                        $style=";background:#087b76; color:white;";
                                    }
                                ?>
                                <a href="{{ url('seguimiento/educativa/gestionarqueja') }}?pagina=<?php echo $i.$par_identificacion; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $i }}</button></a>
                            @endfor
                        </div>
                    </div>
                    @else
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <small style="float:left;">
                                Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $contadorFaltasInstructor }} registros
                            </small>
                            <?php
                                $style='';
                                if($cantidadPaginas == $pagina){
                                    $style=";background:#087b76; color:white;";
                                }
                                $cantidadInicia = 10;
                                if($pagina >= 10){
                                    if($pagina == $cantidadPaginas){
                                        $cantidadInicia = $pagina;
                                    }else{
                                        $cantidadInicia = ($pagina+1);
                                    }
                                }
                            ?>
                            @if($pagina < ($cantidadPaginas-1))
                                <a href="{{ url('seguimiento/educativa/gestionarqueja') }}?pagina=<?php echo $cantidadPaginas.$par_identificacion; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;{{ $style }}">{{ $cantidadPaginas }}</button></a>
                                <a href=""><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                            @endif
                            @for($i=10; $i>0; $i--)
                                <?php
                                    $style='';
                                    if($cantidadInicia == $pagina){
                                        $style=";background:#087b76; color:white;";
                                    }
                                ?>
                                <a href="{{ url('seguimiento/educativa/gestionarqueja') }}?pagina=<?php echo $cantidadInicia.$par_identificacion; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $cantidadInicia }}</button></a>
                                <?php $cantidadInicia--; ?>
                            @endfor
                            @if($pagina >= 10)
                                <a href=""><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                                <a href="{{ url('seguimiento/educativa/gestionarqueja') }}?pagina=1<?php echo $par_identificacion; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">1</button></a> 
                            @endif
                        </div>
                    </div>
                    @endif
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

</div>

@endsection
@section('plugins-js')
<script type="text/javascript">
$(document).ready(function () {
    $(document).on("change", "#asignar_coordinador" , function() {
        var url = $(this).attr("data-url");
        var falta = $(this).attr("data-falta");
        var coordinador = $(this).val();
        if (coordinador !="" && url !="") {
            if (confirm("¿Esta seguro en realizar este cambio?")) {
                $.ajax({
                url:url,
                type:"GET",
                data:"coordinador="+coordinador+"&falta="+falta,
                success:function() {
                    location.reload();
                }
                })   
            }else{
                location.reload();
            }
        }
    });
});
</script>
@endsection