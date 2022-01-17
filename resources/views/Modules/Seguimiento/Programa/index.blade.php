@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Programas','Listar programas') !!}
<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-table">Listar programas</i>
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
                        <div class="row" style="padding-bottom: 5px;">
                            <div class="col-lg-5 col-md-6 col-sm-8 col-xs-12" style="float:right;">
                                <form method="GET" action="{{ url('seguimiento/programa/index') }}">
                                    <div class="input-group input-group-xs">
                                    <span style="cursor:pointer;" class="botonLimpiar input-group-addon"><a href="{{ url('seguimiento/programa/index') }}">Limpiar filtro</a></span>
                                        @if($prog_codigo == '')
                                            <input autocomplete="off" required class="form-control" list="browsers" name="prog_codigo" placeholder="Escriba programa o c&oacute;digo...">
                                        @else
                                            <input autocomplete="off" value="{{ $prog_codigo }}" required class="form-control" list="browsers" name="prog_codigo" placeholder="Escriba programa o c&oacute;digo...">
                                            <?php $prog_codigo = '&prog_codigo='.$prog_codigo?>
                                        @endif
                                        <datalist id="browsers">
                                            @foreach($programasBuscar as $pro)	
                                                <option value="{{$pro->prog_codigo}}">{{ $pro->prog_nombre }}</option>
                                            @endforeach
                                        </datalist>
                                        <label for="idBuscar" class="botonBuscar input-group-addon">Buscar</label>
                                        <input id="idBuscar" style="display:none;" type="submit">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="text-center">Plantilla</th>
                                    <th>C&oacute;digo</th>
                                    <th>Programa</th>
                                    <th>Nivel</th>
                                    <th>Sigla</th>
                                    @if(in_array($rol, $permisoRol))
                                    <th class="text-center">Acci&oacute;n</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody  data-url="{{url('seguimiento/programa/modaleditarprograma')}}" data-token="{{ csrf_token() }}">
                                <?php $inicioContador = $contador; ?>
                                @if(in_array($rol, $permisoRol))
                                    @foreach($programas as $pro)
                                        <tr>
                                            <th>{{ $contador++ }}</th>
                                            <td class="text-center">
                                                @if($pro->pro_url_plan_trabajo == '')
                                                <a class="textoRojo">Sin Plan.</a>
                                                @else
                                                <a class="textoVerde" href="{{ asset('Modules/Seguimiento/Programa/PlanDeTrabajo') }}/{{ $pro->pro_url_plan_trabajo }}">Descargar</a>
                                                @endif
                                            </td>
                                            <td>{{ $pro->prog_codigo }}</td>
                                            <td>{{ $pro->prog_nombre }}</td>
                                            <td>{{ $pro->niv_for_nombre }}</td>
                                            <td>{{ $pro->prog_sigla }}</td>
                                            <td class="text-center">
                                                <a style="cursor:pointer;" class="activarModal" title="Modal" data-nombre-modal="modalEditarPrograma" data-id="{{ $pro->prog_codigo }}">Editar</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    @foreach($programas as $pro)
                                        <tr>
                                            <th>{{ $contador++ }}</th>
                                            <td>{{ $pro->prog_codigo }}</td>
                                            <td>{{ $pro->prog_nombre }}</td>
                                            <td>{{ $pro->niv_for_nombre }}</td>
                                            <td>{{ $pro->prog_sigla }}</td>
                                            <td class="text-center">
                                                 @if($pro->pro_url_plan_trabajo == '')
                                                <a style="cursor:pointer;">Sin plan</a>
                                                @else
                                                <a href="{{ asset('Modules/Seguimiento/Programa/PlanDeTrabajo') }}/{{ $pro->pro_url_plan_trabajo }}" style="cursor:pointer;">Descargar</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($cantidadPaginas > 1)
                    @if($cantidadPaginas <= 10)
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            @if($cantidadPaginas > 1 )
                            <small style="float:left;">
                                Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $contadorProgramas }} registros
                            </small>
                            @endif
                            @for($i=$cantidadPaginas; $i>0; $i--)
                                <?php
                                    $style='';
                                    if($i == $pagina){
                                        $style=";background:#087b76; color:white;";
                                    }
                                ?>
                                <a href="{{ url('seguimiento/programa/index') }}?pagina=<?php echo $i.$prog_codigo; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $i }}</button></a>
                            @endfor
                        </div>
                    </div>
                    @else
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <small style="float:left;">
                                Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $contadorProgramas }} registros
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
                                <a href="{{ url('seguimiento/programa/index') }}?pagina=<?php echo $cantidadPaginas.$prog_codigo; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;{{ $style }}">{{ $cantidadPaginas }}</button></a>
                                <a href=""><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                            @endif
                            @for($i=10; $i>0; $i--)
                                <?php
                                    $style='';
                                    if($cantidadInicia == $pagina){
                                        $style=";background:#087b76; color:white;";
                                    }
                                ?>
                                <a href="{{ url('seguimiento/programa/index') }}?pagina=<?php echo $cantidadInicia.$prog_codigo; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $cantidadInicia }}</button></a>
                                <?php $cantidadInicia--; ?>
                            @endfor
                            @if($pagina >= 10)
                                <a href=""><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                                <a href="{{ url('seguimiento/programa/index') }}?pagina=1<?php echo $prog_codigo; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">1</button></a> 
                            @endif
                        </div>
                    </div>
                    @endif
				@endif
            </div>
        </div>
    </div>
    <!--/span-->

    <div id="modalEditarPrograma" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Editar programa de formaci&oacute;n</h4>
                </div>
                <form data-url="{{ url('seguimiento/programa/guardarcambiosmodal') }}" class="formulario">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="modal-body" id="contenidomodalEditarPrograma">

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success btn-xs">Guardar cambios</button>
                        <a type="button" class="btn btn-default btn-xs" data-dismiss="modal">Cerrar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('plugins-css')
<style>
    .textoRojo{
        color:red !important;
        font-weight: bold;
        text-decoration: none !important;
        cursor: default !important;
    }
    .textoVerde{
        color:#087b76 !important;
        font-weight: bold;
    }
    .botonLimpiar{
        text-decoration: none !important;
    }
    .botonBuscar{
        border: 1px solid #ccc;
        background: #eee;
        color: #087b76;
        border-radius: 0px 0px 4px 0px !important;
        text-decoration: none !important;
    }
    .botonBuscar:hover, .botonLimpiar:hover a, .botonLimpiar:hover{
        background: #ec7114;
        color: white !important;
        text-decoration: none !important;
        cursor: pointer !important;
    }
    th, td{
        padding:1px !important;
        font-size:12px;
    }
</style>
@endsection