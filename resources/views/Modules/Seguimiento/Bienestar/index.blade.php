@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Seguimiento a proyectos','Todas las fichas') !!}
<style>
    .fila_condicionado{
        color:white;
        background:#F54113;
    }
    .fila_condicionado a {
        color:white;
    }
    #color{
        background-color:#F54113;
        width:30px;
        padding:1px;
        height:10px;
    }
    .des-reporte{
        padding:10px;cursor:pointer;
        color:#F54113;
    }
</style>
<section class='content'>
    <div class="row">
        <div class="col-xs-12">
            <div class="box ui-draggable ui-droppable">
                <div class="box-header">
                    <div class="box-name ui-draggable-handle">
                        <i class="fa fa-table"></i>
                        <span>Listado de todos los aprendices</span>
                    </div>
                </div>               
                <div class="box-content">
                    <p>Listado de todos los <code>aprendices</code> existentes en la base de datos. </p>
                    <center>
                        <div class="row" style="margin-top:20px; margin-bottom:20px; border:1px solid #3498DB;padding:10px;width:500px;">
                            <label>Aprendices condicionados que tienen beneficios sena vigentes:</label>
                            <span id="color">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        </div>
                    </center>
                    <div class="row" style="margin: 0px 0px 15px 0px;">
                    <a href="{{ url('seguimiento/bienestar/index') }}" type="button" style="text-decoration:none;"><button type="button" class="btn-info pull-right" style="margin-left:5px;border-radius:2px; text-decoration: none;">Limpiar filtro</button></a>
                    <form action="<?php echo url('seguimiento/bienestar/index');?>" method="get">   
                        <input type='submit' value="Buscar" class="pull-right">
                            <select class="form-control pull-right otras_opciones" id="option_4" style="width:15em;display:none;border-radius:10px;margin-right: 3px;">
                                @foreach($beneficios as $bene)
                                    <option value="{{$bene->id}}">{{$bene->ben_sen_nombre}}</option>
                                @endforeach
                                <option value="100">Filtrar todos los beneficios</option>
                                <option value="0">Sin beneficio</option>
                            </select>
                            <input type="text" id="valor" placeholder="Digite el valor a buscar" class="form-control pull-right" style="width:15em; margin-right: 3px;">
                            <select class="form-control pull-right" style="width:10em;margin-right: 3px;" name="filtro" id="filtro" data-filtro="{{$filtro}}" data-valor="{{$valor}}" required>
                                <option value="">Seleccione...</option>
                                <option value="1">Identificaci&oacute;n</option>
                                <option value="2">Nombre</option>
                                <option value="3">Apellido</option>
                                <option value="4">Beneficios</option>
                                <option value="5">Ficha</option>
                            </select>
                            <input type="hidden" name="_token" value="<?php echo csrf_token();?>">
                        </form>
                       <span style="border-radius:0.25em; width:10em; height:1.90em;margin-right: 3px; cursor:pointer;" class="input-group-addon"><a href="#" data-url="" data-toggle="modal" data-target="#modal" class="ajax-link" title="Reporte">Reporte de beneficiarios</a></span>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Documento</th>
                                <th class="text-center">Nombres</th>
                                <th class="text-center">Apellidos</th>
                                <th class="text-center">Estado</th>
                                <th colspan="2" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $inicioContador = $contador; ?>
                            @foreach($aprendices as $apr)
                                @if(isset($condicionado[$apr->par_identificacion]))
                                <tr class="text-center fila_condicionado">
                                @else
                                <tr class="text-center">
                                @endif
                                 <td>{{$contador++}}</td>
                                 <td>{{$apr->par_identificacion}}</td>
                                 <td>{{$apr->par_nombres}}</td>
                                 <td>{{$apr->par_apellidos}}</td>
                                 <td>
                                     @if($apr->estado == 0)
                                        <span class="badge bg-danger">Inactivo</span>
                                     @else
                                     <span class="badge bg-info">&nbsp;Activo&nbsp;</span>
                                     @endif
                                 </td>
                                 <td>
                                    <a href="#" data-url="{{ url("users/users/show/".$apr->id ) }}" data-toggle="modal" data-target="#modal" class="ajax-link" title="Ver">Ver detalle</a>
                                </td>
                                <td>
                                    @if($rol == 19 or $rol == 0)
                                        @if(isset($beneficiarios[$apr->par_identificacion]))
                                            <a href="{{url("seguimiento/bienestar/beneficios/".$apr->par_identificacion)}}" class="text-decoration-none" style="text-decoration:none;">Gestionar Beneficios</a>
                                        @else
                                            <a href="{{url("seguimiento/bienestar/beneficios/".$apr->par_identificacion)}}" class="text-decoration-none" style="text-decoration:none;">Asignar Beneficios</a>
                                        @endif
                                    @else
                                        @if(isset($beneficiarios[$apr->par_identificacion]))
                                        <a href="#" data-url="{{ url("seguimiento/bienestar/show/".$apr->par_identificacion)}}" data-toggle="modal" data-target="#modal" class="ajax-link" title="Ver">Beneficios asignados</a>
                                        @else
                                            Sin beneficios sena
                                        @endif
                                    @endif
                                </td>
                             </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($cantidadPaginas > 1)
                        @if($cantidadPaginas <= 10)
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    @if($cantidadPaginas > 1 )
                                        <small style="float:left;">
                                            Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $aprendicesContador }} registros
                                        </small>
                                    @endif
                                    @for($i=$cantidadPaginas; $i>0; $i--)
                                        <?php
                                            $style='';
                                            if($i == $pagina){
                                                $style=";background:#087b76; color:white;";
                                            }
                                        ?>
                                        <a href="{{ url('seguimiento/bienestar/index') }}?pagina=<?php echo $i; ?>&valor=<?php echo $valor; ?>&filtro=<?php echo $filtro; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $i }}</button></a>
                                    @endfor
                                </div>
                            </div>
                            @else
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <small style="float:left;">
                                        Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $aprendicesContador }} registros
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
                                        <a href="{{ url('seguimiento/bienestar/index') }}?pagina=<?php echo $cantidadPaginas; ?>&valor=<?php echo $valor; ?>&filtro=<?php echo $filtro; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;{{ $style }}">{{ $cantidadPaginas }}</button></a>
                                        <a href=""><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                                    @endif
                                    @for($i=10; $i>0; $i--)
                                        <?php
                                            $style='';
                                            if($cantidadInicia == $pagina){
                                                $style=";background:#087b76; color:white;";
                                            }
                                        ?>
                                        <a href="{{ url('seguimiento/bienestar/index') }}?pagina=<?php echo $cantidadInicia; ?>&valor=<?php echo $valor; ?>&filtro=<?php echo $filtro; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $cantidadInicia }}</button></a>
                                        <?php $cantidadInicia--; ?>
                                    @endfor
                                    @if($pagina >= 10)
                                        <a href=""><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                                        <a href="{{ url('seguimiento/bienestar/index') }}?pagina=1&valor=<?php echo $valor; ?>&filtro=<?php echo $filtro; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">1</button></a> 
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                 </div>
            </div>
        </div>
    </div>
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
</section>
@endsection
@section('plugins-js')
<script type="text/javascript">
    $(document).ready(function () {
       $(document).on("click", "a[data-toggle='modal']", function () {
            var destino = $(this).attr("data-target");
            var url = $(this).attr("data-url");
            $(destino + " .modal-body").html("Cargando ...");
            if (url!="") {
                $(".modal-dialog").removeClass("modal-sm");
                $(".modal-dialog").addClass("modal-lg");
                $(".modal-title").text("Detalle");
                $.ajax({
                    url: url,
                    type: "GET",
                    success: function (data) {
                        $(destino + " .modal-body").html(data);
                    }
                });   
            }else{
                $(".modal-dialog").removeClass("modal-lg");
                $(".modal-dialog").addClass("modal-sm"); 
                select = 
                "<div class='container row'><select id='tipo_reporte'>"+
                "<option value='3'>Apoyo de sostenienimento</option>"+
                "<option value='1'>Apoyo de transporte</option>"+
                "<option value='2'>Bono de alimentacion</option>"+
                "<option value='4'>Todos los beneficios</option>"+
                "</select><a href='{{url('seguimiento/bienestar/reporte?tipo=3')}}' class='des-reporte'>Descargar</a></div>";
                $(".modal-title").text("Reporte de beneficiarios");
                $(destino + " .modal-body").html(select);
            }
        });
        //seleccionar automaticamente
        $( window ).load(function() {
            var filtro = $("#filtro").attr("data-filtro");
            var valor = $("#filtro").attr("data-valor");
            if (filtro !="" && valor != "") {
                $("#filtro option[value="+filtro+"]").attr("selected",true);
                if (filtro == 4) {
                    $("#valor").removeAttr("name");
                    $("#valor").css("display","none");
                    $("#option_"+filtro+"").attr("name","valor");
                    $("#option_"+filtro+"").css("display","block");
                    $("#option_"+filtro+" option[value="+valor+"]").attr("selected",true);
                }else{
                    $(".otras_opciones").removeAttr("name");
                    $(".otras_opciones").css("display","none");
                    $("#valor").attr("name","valor");
                    $("#valor").css("display","block");
                    $("#valor").attr("value",valor);
                }
            }
        });
        $(document).on("change","#filtro",function() {
            var option = $(this).val();
            $(".otras_opciones").removeAttr("name");
            $(".otras_opciones").css("display","none");
            if (option == 4) {
                $("#valor").removeAttr("name");
                $("#valor").css("display","none");
                $("#option_"+option+"").attr("name","valor");
                $("#option_"+option+"").css("display","block");
            }else{
                $(".otras_opciones").removeAttr("name");
                $(".otras_opciones").css("display","none");
                $("#valor").attr("name","valor");
                $("#valor").css("display","block");
            }
        });
        $(document).on("change","#tipo_reporte",function() {
            var tipo = $(this).val();
            if (tipo >=1 && tipo<=4) {
                var http = "{{url()}}";
                var url = http+"/"+"seguimiento/bienestar/reporte?tipo="+tipo;
                $(".des-reporte").attr("href",url);   
            }
        });
    });
</script>
@endsection