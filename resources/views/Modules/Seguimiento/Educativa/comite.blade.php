@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Programar Comit&eacute;') !!}

<div class="row">
@include('errors.messages')
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
                    <i class="fa fa-search"></i>
                    <span>Programar Comit&eacute;</span>
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

                <p>Tenga en cuenta que la informaci&oacute;n del comit&eacute; se enviar&aacute; por correo electr&oacute;nico <code>{{ Auth::user()->participante->par_nombres." ".Auth::user()->participante->par_apellidos }}</code></p>

                {!! Form::open(array("url" => url("seguimiento/educativa/comite"), "method"=>"post", "class"=>"form-horizontal", "id"=>"generarComite")) !!}

                <div id="tabs">
                    <ul class="ui-tabs-nav">
                        <li><a id="tab-1" href="#tabs-1" >Informaci&oacute;n del comit&eacute;</a></li>
                        <li><a id="tab-2" href="#tabs-2" >Participantes</a></li>
                    </ul>
                    <input type="hidden" name="implicadoHidden" id="implicadoHidden" required="required">
                    <input type="hidden" name="horarioValido" id="horarioValido" required="required">
                    <div id="tabs-1">
                        <div class="boton-flotante">
                            <div class="col-sm-6">			
                                <small><button class="btn btn-default trigger" data-target="#tab-2" ><i class="fa fa-hand-o-right"></i> Adelante</button></small>
                            </div>
                        </div>
                        <!--<div class="form-group">
                            {!! Form::label("tipo","Tipo de Comit&eacute;",array("for"=>"tipo","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-4">
                                <select name="tipo" id="tipo" class="form-control" required="required">
                                    <option value="">-- Seleccione el tipo --</option>
                                    @foreach($tipos as $id_tipo=>$tipo)
                                    <option value="{{ $id_tipo }}">{{ $tipo }}</option>
                                    @endforeach
                                </select>
                                
                            </div>
                        </div>-->
                        <div class="form-group">
                            <input type="hidden" name="idFalta" id="idFalta" value="{{ $id }}">
                            <input type="hidden" name="tipo" id="tipo" value="2">
                            {!! Form::label("hora","Hora y Fecha Comit&eacute;",array("for"=>"hora","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-4">
                                <div class='input-group date' id='datetimepicker1'>
                                    <input type='text' class="form-control" name="fecha" id="fechaComite" required="required" data-enlace="{{url('seguimiento/educativa/validahora')}}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>    
                            </div>
                            <label id="fecha_incorrecta"></label>
                        </div>
                        <div class="form-group">
                            {!! Form::label("direccion","Direcci&oacute;n Comit&eacute;",array("for"=>"direccion","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-4">
                                <input type="text" name="direccion" id="direccion" class="form-control" required="required">    
                            </div>
                        </div>
                    </div>
                    <div id="tabs-2">
                        <div class="boton-flotante">
                            <div class="col-sm-6">			
                                <small><button class="btn btn-danger trigger" data-target="#tab-1"><i class="fa fa-hand-o-left "></i> Atras</button></small>
                            </div>
                            <div class="col-sm-6">	
                                <small>{!! Form::submit("Generar", array("class"=>"btn btn-success ajax-link")) !!}</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                {!! Form::label("participante","Seleccionar",array("for"=>"evidencias","class"=>"control-label col-md-3")) !!}
                                <div class="col-sm-4">
                                    {!! Form::text("aprendiz", null, array("id"=>"comite_implicado",
                                    "class"=>"col-md-4 form-control",
                                    "data-enlace"=> url("seguimiento/educativa/implicados"))) !!}
                                    <span class="help-block">
                                        Por favor digite el <code>n&uacute;mero de cedula, nombre o apellido</code> del participante que desea relacionar
                                    </span>
                                    <small>
                                        <table class="table" id="tabla_queja">

                                        </table>
                                    </small>
                                    <label>Enviar correo </label>
									<select class="form-control" name="enviarCorreo">
										<option value="1">SI</option>
										<option value="2">NO</option>
									</select>
                                </div>
                                <div class="col-sm-4">

                                    <table class="table" id="tabla_queja_final">

                                    </table>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-offset-7 col-sm-4">

                            </div>
                        </div>

                    </div>



                </div>

                {!! Form::close() !!}
            </div>


        </div>
    </div>
</div>

@endsection

@section("plugins-css")
<link rel="stylesheet" href="{{ asset("css/bootstrap-datetimepicker.css") }}">
@endsection

@section("plugins-js")
<script type="text/javascript" src="{{ asset("devoops/plugins/moment/moment.min.js") }}"></script>
<script type="text/javascript" src="{{ asset("js/bootstrap-datetimepicker.js") }}"></script>
  <script type="text/javascript">
            $(function () {
                $('#datetimepicker1').datetimepicker();
            });
        </script>
<script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {

    $.validator.setDefaults({
        ignore: []
    });

$('#generarComite').validate({
        rules: {
            implicadoHidden: {
                required: true
            },
            horarioValido: {
                required: true
            }
        },
        messages: {
            implicadoHidden: {
                required: "Debe seleccionar por lo menos un(1) implicado para el comite"
            },
            horarioValido: {
                required: "La hora del comit&eacute; es invalida, debe haber 30 minutos entre uno y otro"
            }
        }
    });
});
</script>

<script type="text/javascript">
    function validaTablas()
    {
        if($(".implicadoSelect").length>0)
        {
            $("#implicadoHidden").val("1");
        }
        else
        {
            $("#implicadoHidden").val("");
        }
        
    }
    
    function validarHorario()
    {
        if($(".horarioSelect").length>0)
        {
            $("#horarioValido").val("1");
        }
        else
        {
            $("#horarioValido").val("");
        }
    }

</script>
@endsection