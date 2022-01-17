@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Gestión de beneficios','Consultar, asignar , editar y eliminar') !!}
<style>
    .beneficios{ margin-bottom:5px; }
    .add .beneficios{ margin-top:30px; }
    .box-footer{ margin-top:30px; }
    textarea{ resize:none; }
    .bg-inactivo{ background:#b65c02; }
    .modal-backdrop {
        opacity: 0 !important;
        filter: alpha(opacity=0) !important;
    }
</style>
<div clas="row">
    @include('errors.messages')
    <div class="col-xs-12 col-sm-12">
            <div class="box ui-draggable ui-droppable">
                <div class="box-header">
                    <div class="box-name ui-draggable-handle">
                        <i class="fa fa-search"></i>
                        <span>Gestion de beneficios sena</span>
                    </div>
                </div>
                <?php
                    if(isset($_SESSION['mensaje'])){
                        if(isset($_SESSION['mensaje']['ok'])){
                            $color = "cuadro-ok";
                            $barra = "barra-ok";
                            $style="";
                            $mensaje = $_SESSION['mensaje']['ok'];
                        }else{
                            $color="cuadro-err";
                            $barra = "barra-err";
                            $style="";
                            $mensaje = $_SESSION['mensaje']['error'];
                        }
                        unset($_SESSION['mensaje']);
                    }else{
                        $color="";
                        $barra="";
                        $mensaje="";
                        $style="display:none;";
                    }
                    $class[0]="";
                    $class[1]="";
                    if (count($historial) == 0) {
                        $class[0]="ui-tabs-active ui-state-active";
                    }else{
                        $class[1]="ui-tabs-active ui-state-active";
                    }
                ?>
                <div class="cuadro {{$color}}" style="{{$style}}">
                    <div class="cuadro-contenido">
                        <div class="cuadro-header">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                            </svg>
                            <label>Setalpro le informa que:</label>
                            <span id="cuadro-closed">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                </svg>
                            </span>
                        </div>
                        <div class="cuadro-mensaje">
                            <p id="mensaje">{{$mensaje}}</p>
                        </div>
                        <div class="cuadro-footer">
                            <div class="barra {{$barra}}">&nbsp;</div>
                        </div>
                    </div>
                </div>
                <div class="box-content">
                    <p>A continuaci&oacute;n podra ver el historial de beneficios sena que ha tenido el aprendiz <b>{{$aprendiz[0]->par_nombres.' '.$aprendiz[0]->par_apellidos}}</b>,
                        cabe restaltar que si usted lo ve pertinente puede asignar un nuevo beneficio, eliminar beneficios actuales y cambiar sus fechas de acompañamiento.
                        <b>Los campos marcados con <code>(*)</code> son obligatorios esto en caso de asignar un beneficio o editarlo.</br>
                    </p>
                    <div id="tabs">
                        <ul class="ui-tabs-nav">
                            <li class="{{$class[0]}}"><a href="#tabs-1">Asignar Beneficios</a></li>
                            <li class="{{$class[1]}}"><a href="#tabs-2">Historial</a></li>
                            @if(count($faltas) > 0)
                            <li><a href="#tabs-3">Faltas Vigentes</a></li>
                            @endif
                        </ul>
                        <div id="tabs-1">
                            <form class="form-horizontal" action="{{ url('seguimiento/bienestar/asignarbeneficio') }}" method="post">
                                <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">
                                <div class="beneficios">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Beneficio *</label>
                                        <div class="col-sm-4">
                                            <select class="form-control" name="beneficio[]" required>
                                                <option value="">Seleccione...</option>
                                                @foreach($beneficios as $beneficio)
                                                <option value="{{$beneficio->id}}">{{$beneficio->ben_sen_nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span class="btn btn-success agregar" style="width:30px;padding:1px;margin-left:50px;">+</span>
                                        <span class="btn btn-danger quitar" style="width:30px;padding:1px;margin-left:20px;">-</span>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Fecha inicio *</label>
                                        <div class="col-sm-4 input-group" style="padding:10px;">
                                            <input type="date" name="fecha_inicio[]" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label" required>Fecha fin *</label>
                                        <div class="col-sm-4 input-group" style="padding:10px;">
                                        <input type="date" name="fecha_fin[]" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Observaci&oacute;n</label>
                                        <div class="col-sm-4 input-group" style="padding:10px;">
                                            <textarea name="observacion[]" cols="35" rows="2" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="add"></div>
                                <div class="box-footer">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-6 input-group">
                                        <input type="hidden" name="id" id="aprendiz" value="{{$identificacion}}">
                                        <input type="submit" value="Guardar" class="btn btn-success">
                                        <input type="reset"  value="Reiniciar campos" class="btn btn-default" style="margin-left:20px;"></input>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="tabs-2">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Beneficio</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Observacion</th>
                                        <th>Estado</th>
                                        <th class="text-center" colspan="2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($historial) > 0)
                                        <?php $c=1; ?>
                                        @foreach($historial as $his)
                                            <tr>
                                                <td>{{$c++}}</td>
                                                <td>{{$his->beneficio}}</td>
                                                <td>
                                                    <input type="date" name="fecha_inicio" id="fecha_inicio_{{$c}}" class="form-control" style="width:168px;" value="{{$his->fecha_inicio}}" disabled required>
                                                </td>
                                                <td>
                                                    <input type="date" name="fecha_fin" id="fecha_fin_{{$c}}" class="form-control" style="width:168px;" value="{{$his->fecha_fin}}" disabled required>
                                                </td>
                                                <td class="text-center">
                                                    <a href="#" class="text-primary text-decoration-none" data-toggle="modal" data-vista="1" data-target="#modal" data-obs="{{$his->observacion}}" data-id="{{$his->id}}" class="ajax-link" title="Ver">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"></path>
                                                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"></path>
                                                        </svg>
                                                    </a>
                                                </td>
                                                <td>
                                                    @if($his->estado == 1)
                                                        <span id="estado_{{$c}}" class="badge bg-info save" style="cursor:pointer;" data-id="{{$his->id}}" data-fila="{{$c}}" data-vis="2" data-estado="{{$his->estado}}" data-url="{{url('seguimiento/bienestar/edit')}}">&nbsp;Activo&nbsp;</span>
                                                    @elseif($his->estado == 2)
                                                        <span id="estado_{{$c}}" class="badge bg-inactivo save" style="cursor:pointer;" data-id="{{$his->id}}" data-fila="{{$c}}" data-vis="2" data-estado="{{$his->estado}}"  data-url="{{url('seguimiento/bienestar/edit')}}">Inactivo</span>
                                                    @elseif($his->estado == 3)
                                                        <span id="estado_{{$c}}" class="badge bg-danger" data-id="{{$his->id}}" data-fila="{{$c}}" data-vis="2" data-estado="{{$his->estado}}" data-url="{{url('seguimiento/bienestar/edit')}}">Vencido</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div id="editar_{{$c}}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-pencil-square edit text-primary" viewBox="0 0 16 16" data-fila="{{$c}}">
                                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                                        </svg>
                                                    </div>
                                                    <div id="guardar_{{$c}}" class="row" style="display:none;">&nbsp;&nbsp;
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-check-circle text-primary save" viewBox="0 0 16 16" data-id="{{$his->id}}" data-vis="1" data-fila="{{$c}}" data-url="{{url('seguimiento/bienestar/edit')}}">
                                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                            <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                                        </svg>
                                                    </div>
                                                </td>
                                                <td>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-x-circle text-danger delete" viewBox="0 0 16 16" data-id="{{$his->id}}" data-url="{{url('seguimiento/bienestar/delete')}}" data-aprendiz="{{$identificacion}}">
                                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                    </svg>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @if(count($faltas) > 0)
                            <div id="tabs-3">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Fecha de creación</th>
                                            <th class="text-center">Tipo de falta</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $x=1; ?>
                                        @foreach($faltas as $fal)
                                            <tr class="text-center">
                                                <td>{{$x++}}</td>
                                                <td>{{$fal->edu_falta_fecha}}</td>
                                                <td>{{$fal->edu_tipo_falta_descripcion}}</td>
                                                <td>{{$fal->edu_est_descripcion}}</td>
                                                <td>
                                                    <a title="Modal" href="#" data-vista="2" data-estado="{{ $fal->edu_est_descripcion }}" data-id="{{ $fal->edu_falta_id }}" data-url="{{url('seguimiento/bienestar/falta')}}" class='cargarAjax' data-toggle="modal" data-target="#modal">Ver</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
</div>
<div id="modal" class="modal fade" role="dialog" tabindex="-1">
    <div id="modal-dialog" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Observaci&oacute;n</h4>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <span id="btn-actualizar"></span>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section("plugins-css")
<link rel="stylesheet" href="{{ asset("css/bootstrap-datetimepicker.css") }}">
@endsection

@section('plugins-js')
<script type="text/javascript" src="{{ asset("devoops/plugins/moment/moment.min.js") }}"></script>
<script type="text/javascript" src="{{ asset("js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
$(function () {
    $('.datetimepicker').datetimepicker();
});
</script>
<script type="text/javascript">
$(document).ready(function () {
    //agregar o quitar beneficios
    var beneficios = $('.beneficios').html();
    $(document).on("click", ".agregar", function(){
        cantidad=$('#tabs-1').find('.beneficios').length;
        if(cantidad < 3){
            $('.add').append("<div class='beneficios'>"+beneficios+"</div>");
        }
    });
    $(document).on("click",".quitar",function(){
        cantidad=$('#tabs-1').find('.beneficios').length;
        if(cantidad > 1){
        $(this).parent().parent().remove();
        }
    });
    //Habilitar las fechas
    $(document).on("click",".edit",function(){
        var fila = $(this).attr("data-fila");
        $("#fecha_inicio_"+fila+"").attr('disabled',false);
        $("#fecha_fin_"+fila+"").attr('disabled',false);
        $("#editar_"+fila+"").css('display','none');
        $("#guardar_"+fila+"").css('display','block');
    });
    //Modal
    $(document).on("click", "a[data-toggle='modal']", function () {
        var vista = $(this).attr("data-vista");
        if (vista == "1") {
            var id = $(this).attr("data-id");
            var observacion = $(this).attr("data-obs");
            $("#modal-dialog").removeClass("modal-lg");
            $("#modal-dialog").addClass("modal-sm");
            $("#modalBody").html('<textarea id="observacion" cols="35" rows="5" class="form-control"></textarea>');
            $("#btn-actualizar").html('<button type="button" id="actualizar" class="btn btn-success" data-url="{{url("seguimiento/bienestar/edit")}}">Actualizar</button>');
            $("#observacion").val(observacion);
            $("#actualizar").attr("data-id",id);
            $(".modal-header h4").text("Observación");
        }else{
            $("#btn-actualizar").html("");
            $("#modal-dialog").removeClass("modal-sm");
            $("#modal-dialog").addClass("modal-lg");
            $(".modal-header h4").text("Detalle de la Falta");
        }
    });
    //Actualizar fechas y estado del beneficio
    $(document).on("click",".save",function(){
        var url = $(this).attr("data-url");
        var fila = $(this).attr("data-fila");
        var vis = $(this).attr("data-vis");
        var id = $(this).attr("data-id");
        var aprendiz = $("#aprendiz").val();
        var err=0;
        var data = "id="+id+"&aprendiz="+aprendiz;
        if (id == "" || aprendiz == "") {
            err++;
            alert("Los campos de las fechas son obligatorios");
        }
        if (vis == "1") {
            var inicio = $("#fecha_inicio_"+fila+"").val();
            var fin = $("#fecha_fin_"+fila+"").val();
            var data = data + "&fecha_inicio="+inicio+"&fecha_fin="+fin+"&vis=1";
            if (inicio == "" || fin == "") {
                err++;
                alert("Los campos de las fechas son obligatorios");
            }
        }else{
            var estado = $(this).attr("data-estado");
            var data = data + "&vis=2"+"&estado="+estado;
            if (estado == "1") {
                var title = "Inactivo";
                var nuevo_estado = 2;
                var nueva_clase = "badge bg-inactivo save";
            }else if(estado == "2"){
                var title = "&nbsp;Activo&nbsp;";
                var nuevo_estado = 1;
                var nueva_clase = "badge bg-info save";
            }
        }
        if (vis == "1" || vis == "2" && err == 0) {
            var mensaje = "El beneficio sena se ha actualizado correctamente";
            var cuadro = "ok";
            peticion(data,url,mensaje,cuadro);
            if (vis == "1") {
                var d = new Date();
	            var hoy = d.getFullYear()+"-"+((d.getMonth() + 1)<10?'0':'')+(d.getMonth() + 1)+"-"+(d.getDate()<10?'0':'')+d.getDate();
                if (hoy > fin) {
                    $("#estado_"+fila+"").html("Vencido");
                    $("#estado_"+fila+"").attr("class","badge bg-danger");
                    $("#estado_"+fila+"").attr("data-estado",3);
                }else{
                    $("#estado_"+fila+"").html("&nbsp;Activo&nbsp;");
                    $("#estado_"+fila+"").css("cursor:","pointer");
                    $("#estado_"+fila+"").attr("class","badge bg-info save");
                    $("#estado_"+fila+"").attr("data-estado",1);
                }
            }else if(vis == 2){
                $(this).html(title);
                $(this).attr("data-estado",nuevo_estado);
                $(this).attr("class",nueva_clase);
            }
        }
        $("#fecha_inicio_"+fila+"").attr('disabled',true);
        $("#fecha_fin_"+fila+"").attr('disabled',true);
        $("#editar_"+fila+"").css('display','block');
        $("#guardar_"+fila+"").css('display','none');
    });
    //Actualizar Observación
    $(document).on("click", "#actualizar", function() {
        var url = $(this).attr("data-url");
        var observacion = $("#observacion").val();
        var id = $(this).attr("data-id");
        var aprendiz = $("#aprendiz").val();
        if (observacion !="") {
            var mensaje = "La observacion se actualizo correctamente";
            var data = "id="+id+"&aprendiz="+aprendiz+"&observacion="+observacion+"&option=2";
            var cuadro = "ok";
            peticion(data, url , mensaje, cuadro);
        }
        setTimeout(function() {
            location.reload();
        },1800);
    });
   //Eliminar Beneficio
   $(document).on("click",".delete",function(){
       if(confirm("¿Desea eliminar este beneficio?")){
            var id = $(this).attr("data-id");
            var url = $(this).attr("data-url");
            var aprendiz = $(this).attr("data-aprendiz");
            if (id !="") {
                var data = "id="+id+"&aprendiz="+aprendiz;
                var mensaje = "El beneficio sena se ha eliminado correctamente";
                var cuadro = "err";
                peticion(data, url , mensaje, cuadro);
                $(this).parent().parent().remove();
            }
       }
    });
    function peticion(data, url , mensaje, cuadro){
        $.ajax({
            url: url,
            type: "GET",
            data: data,
            success:function(data){
                if (data != "") {
                    $(".cuadro").attr("class","cuadro cuadro-"+cuadro+"");
                    $(".barra").attr("class","barra barra-"+cuadro+"");
                    $("#mensaje").html(mensaje);
                    $(".cuadro").css("display","block");
                }else{
                    alert("ERROR #404");
                }
            }
        });
    }
});
</script>
@endsection