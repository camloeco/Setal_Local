@extends('templates.devoops')
@section('plugins-css')
    <style type="text/css">
        .estadosF{
            cursor:pointer;
        }
        .fa{ transform:scale(1.2); cursor:pointer; }
    </style>
@endsection 
@section('content')
{!! getHeaderMod('Programas','Listar programas') !!}
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
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-bottom: 20px;">
                        <form id="form-filtros" method="GET" action="{{ url('seguimiento/educativa/programarcomite') }}">
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Instructor</label>
                                <div class="input-group input-group-xs filtro" name="par_identificacion_instructor">
                                    @if($par_identificacion == '')
                                        <input autocomplete="off" required style="border: 1px solid #ccc;padding: 2px 12px;width: 260px;" class="form-control" list="browsers" name="par_identificacion" placeholder="Escriba el documento o nombre...">
                                    @else
                                        <input autocomplete="off" value="{{ $par_identificacion }}" required style="border: 1px solid #ccc;padding: 2px 12px;width: 260px;" class="form-control" list="browsers" name="par_identificacion" placeholder="Escriba programa o código...">
                                        <?php $par_identificacion = '&par_identificacion='.$par_identificacion?>
                                    @endif
                                    <datalist id="browsers">
                                        <option value="14995914">Anibal Silva Cortes</option>
                                        @foreach($instructores as $ins)	
                                            <option value="{{$ins->par_identificacion}}">{{$ins->par_nombres}} {{$ins->par_apellidos}}</option>
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <label>Coordinador / Funcionario</label>
                                <select class="form-control filtro" name="par_identificacion_coordinador">
                                    <option value=''>Todos...</option>
                                    @if($par_identificacion_coordinador == '')
                                        @foreach($coordinadores as $val)
                                            <option value="{{ $val->par_identificacion }}">{{ $val->par_nombres }} {{ $val->par_apellidos }}</option>
                                        @endforeach
                                    @else
                                        @foreach($coordinadores as $val)
                                            <?php
                                                $selected = '';
                                                if($val->par_identificacion == $par_identificacion_coordinador){
                                                    $selected = 'selected';
                                                }
                                            ?>
                                            <option {{ $selected }} value="{{ $val->par_identificacion }}">{{ $val->par_nombres }} {{ $val->par_apellidos }}</option>
                                        @endforeach
                                    @endif
                                    <option value='0' {{$mis_faltas}}>Mis faltas</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <label>Estado</label>
                                <select class="form-control filtro" name="edu_est_id">
                                    <option value=''>Todos...</option>
                                    @if($edu_est_id == '')
                                        @foreach($dbEstados as $val)
                                            <option value="{{ $val->edu_est_id }}">{{ $val->edu_est_descripcion}}</option>
                                        @endforeach
                                    @else
                                        @foreach($dbEstados as $val)
                                            <?php
                                                $selected = '';
                                                if($val->edu_est_id == $edu_est_id){
                                                    $selected = 'selected';
                                                }
                                            ?>
                                            <option {{ $selected }} value="{{ $val->edu_est_id }}">{{ $val->edu_est_descripcion}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                <label>Limpiar</label>
                                <a href="{{ url('seguimiento/educativa/programarcomite') }}"><span style="cursor:pointer;border:1px solid;padding:4px;" class="input-group-addon">Limpiar filtro</span></a>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="padding:1px;font-size:12px;">C&oacute;digo</th>
                                    <th style="padding:1px;font-size:12px;">Identificaci&oacute;n</th>
                                    <th style="padding:1px;font-size:12px;">Nombre</th>
                                    <th style="padding:1px;font-size:12px;">Fecha</th>
                                    <th style="padding:1px;font-size:12px;">Fecha aprobaci&oacute;n</th>
                                    <th style="padding:1px;font-size:12px;">Tipo</th>
                                    <th style="padding:1px;font-size:12px;">Estado</th>
                                    <th style="padding:1px;text-align:center;font-size:12px;">Ver</th>
                                    <th style="padding:1px;text-align:center;font-size:12px;">Programar</th>
                                    <th style="padding:1px;text-align:center;font-size:12px;">Ejecutar</th>
                                    <th style="padding:1px;text-align:center;font-size:12px;">Acta</th>
                                    <th style="padding:1px;text-align:center;font-size:12px;">Rechazo</th>
                                    <th style="padding:1px;text-align:center;font-size:12px;">Eliminar</th>
                                </tr>
                            </thead>
                            <tbody  data-url="{{url('seguimiento/educativa/modalrechazo')}}" data-token="{{ csrf_token() }}">
                                <?php $inicioContador = $contador; ?>
                                @foreach($faltas as $fal)
                                    <tr>
                                        <th style="padding:1px;font-size:11.4px;">{{ $fal->edu_falta_id }}</th>
                                        <td style="padding:1px;font-size:11.4px;">{{ $fal->par_identificacion }}</td>
                                        <td style="padding:1px;font-size:11.4px;">{{ $fal->nombreInstructor }}</td>
                                        <td style="padding:1px;font-size:11.4px;">{{ $fal->edu_falta_fecha }}</td>
                                        <td style="padding:1px;font-size:11.4px;">{{ $fal->edu_falta_fecha_aprobado }}</td>
                                        <td style="padding:1px;font-size:11.4px;">{{ $fal->edu_tipo_falta_descripcion }}</td>
                                        <td style="padding:1px;font-size:11.4px;"><a class="tag tag-{{ $estados[$fal->edu_est_descripcion] }}">{{ $fal->edu_est_descripcion }}<a></td>
                                        <td style="font-size:12px;padding:2px;text-align:center;"><a title="Modal" href="#" data-estado="{{ $fal->edu_est_descripcion }}" data-id="{{ $fal->edu_falta_id }}" data-url="{{url("seguimiento/educativa/verdetalle")}}" class='cargarAjax' data-toggle="modal" data-target="#modal">Ver</a></td>
                                        @if($fal->edu_est_descripcion == "APROBADO" || $fal->edu_est_descripcion == "PROGRAMADO")
                                            <td style="padding:1px;text-align:center;"><a href="{{ url('seguimiento/educativa/comite/'.$fal->edu_falta_id) }}" class="ajax-link">Prog.</a></td>
                                        @else
                                            <td style="padding:1px;text-align:center;"><code>N/A</code></td>
                                        @endif
                                        @if($fal->edu_est_descripcion == "PROGRAMADO")
                                            <td style="padding:1px;text-align:center;"><a href="{{ url('seguimiento/educativa/acta/'.$fal->edu_falta_id) }}" class="ajax-link">Ejec.</a></td>
                                        @else
                                            <td style="padding:1px;text-align:center;"><code>N/A</code></td>
                                        @endif

                                        @if($fal->edu_est_descripcion == "FINALIZADO")
                                            <!---<td style="padding:1px;text-align:center;"><a href="{{ asset("Modules/Seguimiento/Educativa/Acta/ACTA-".$fal->edu_falta_id.".zip") }}" class="ajax-link">Act.</a></td>-->
                                            <td style="padding:1px;text-align:center;"><code>N/A</code></td>
                                        @else
                                            <td style="padding:1px;text-align:center;"><code>N/A</code></td>
                                        @endif

                                        @if($fal->edu_est_descripcion == "RECHAZADO")
                                        <td style="padding:1px;text-align:center;"><a style="cursor:pointer;" class="activarModal" title="Modal" data-nombre-modal="modalRechazo" data-id="{{ $fal->edu_falta_id }}">Rec.</a></td>
                                        @else
                                            <td style="padding:1px;text-align:center;"><code>N/A</code></td>
                                        @endif
                                        <td style="padding:1px;text-align:center;">
                                            <a data-url="{{ url('seguimiento/educativa/anularcomite') }}?codigo={{$fal->edu_falta_id}}" data-index="{{ url('seguimiento/educativa/programarcomite') }}" type="button"  id="eliminar" class="" style="cursor:pointer;">
                                            Elim.
                                            </a>
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
                            @if($cantidadPaginas > 1 )
                            <small style="float:left;">
                                Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $contadorFaltas }} registros
                            </small>
                            @endif
                            @for($i=$cantidadPaginas; $i>0; $i--)
                                <?php
                                    $style='';
                                    if($i == $pagina){
                                        $style=";background:#087b76; color:white;";
                                    }
                                ?>
                                <a href="{{ url('seguimiento/educativa/programarcomite') }}?pagina=<?php echo $i.$par_identificacion; ?>&par_identificacion_coordinador=<?php echo $par_identificacion_coordinador; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $i }}</button></a>
                            @endfor
                        </div>
                    </div>
                    @else
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <small style="float:left;">
                                Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $contadorFaltas }} registros
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
                                <a href="{{ url('seguimiento/educativa/programarcomite') }}?pagina=<?php echo $cantidadPaginas.$par_identificacion; ?>&par_identificacion_coordinador=<?php echo $par_identificacion_coordinador; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;{{ $style }}">{{ $cantidadPaginas }}</button></a>
                                <a href=""><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                            @endif
                            @for($i=10; $i>0; $i--)
                                <?php
                                    $style='';
                                    if($cantidadInicia == $pagina){
                                        $style=";background:#087b76; color:white;";
                                    }
                                ?>
                                <a href="{{ url('seguimiento/educativa/programarcomite') }}?pagina=<?php echo $cantidadInicia.$par_identificacion; ?>&par_identificacion_coordinador=<?php echo $par_identificacion_coordinador; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $cantidadInicia }}</button></a>
                                <?php $cantidadInicia--; ?>
                            @endfor
                            @if($pagina >= 10)
                                <a href=""><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                                <a href="{{ url('seguimiento/educativa/programarcomite') }}?pagina=1<?php echo $par_identificacion; ?>&par_identificacion_coordinador=<?php echo $par_identificacion_coordinador; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">1</button></a> 
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
        <div class="modal-dialog modal-lg">

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

@section('plugins-js')
<script type='text/javascript'>
    $(document).ready(function() {
        $(document).on("change", ".filtro", function() {
            //alert('xd');
            $("#form-filtros").trigger("submit");
        });
        
        $(document).on("change", ".notificacion", function() {
            var option = $(this).val();
            var url = $("#url_notify").val();
            var id =  $(this).attr("data-id");

            $("#btn_"+id+"").css("display","none");
            $("#resolucion_"+id+"").css("display","none");
            $("#estado_act_"+id+"").css("display","none");
            $("#fecha_res_"+id+"").css("display","none");
            $("#btn_"+id+"").attr("href","");

            if(option == 1){
                $("#btn_"+id+"").css("display","block");
            }else if(option == 2){
                $("#resolucion_"+id+"").css("display","block");
                $("#estado_act_"+id+"").css("display","block");
                $("#fecha_res_"+id+"").css("display","block");
                $("#btn_"+id+"").css("display","block");
            }else if(option == 3){
                $("#resolucion_"+id+"").css("display","block");
                $("#fecha_res_"+id+"").css("display","block");
                $("#btn_"+id+"").css("display","block");
            }
        });

        $(document).on("click", ".enviar", function() {
            var id = $(this).attr('data-id');
            var option = $("#notificacion_"+id+"").val();
            var aprendiz = $("#notificacion_"+id+"").attr("data-aprendiz");
            var url = $("#url_notify").val();        
            var resolucion = $("#resolucion_"+id+"").val();
            var estado_act = $("#estado_act_"+id+"").val();
            var fecha_res = $("#fecha_res_"+id+"").val();
            var valNum = /^[0-9]+$/;
            var envio = 0;
            
            if (option > 0 && option <= 3) {
                url = url + "&apr=" + aprendiz +"&opt=" + option;
                envio++;
            }
            if (resolucion.match(valNum)) {
                url = url +"&res=" + resolucion;    
                envio++;
            }
            if (estado_act!="") {
                if (!estado_act.match(valNum)) {
                   url = url + "&est=" + estado_act;
                   envio++;
                }
            }
            if (fecha_res !="") {
                url = url + "&fec_res=" + fecha_res;
                envio++;
            }
            if (envio >= 1) {
                $("#btn_"+id+"").attr("href",url);   
            }else{
                event.preventDefault();
            }
        });
    });
</script>
@endsection
