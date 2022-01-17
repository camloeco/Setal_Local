@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Generar falta o informe') !!}

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
                    <span>Creaci&oacute;n de la falta o informe</span>
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

                <p>Tenga en cuenta que la queja, informe o falta quedar&aacute; radicada a nombre del instructor <code>{{ Auth::user()->participante->par_nombres." ".Auth::user()->participante->par_apellidos }}</code></p>

                {!! Form::open(array("url" => url("seguimiento/educativa/queja"), "method"=>"post", "class"=>"form-horizontal", "id"=>"formQueja", "onSubmit"=>"validaTablas()")) !!}
                <input type="hidden" name="aprendizHidden" id="aprendizHidden" required="required">
                <input type="hidden" name="literalHidden" id="literalHidden" required="required">
                <div id="tabs">
                    <ul class="ui-tabs-nav">
                        <li><a id="tab-1" href="#tabs-1" >Informaci&oacute;n de la falta</a></li>
                        <li><a id="tab-2" href="#tabs-2" >Aprendices</a></li>
                        <li><a id="tab-3" href="#tabs-3" >Literales Reglamento</a></li>
                    </ul>
                    <div id="tabs-1">
                        <div class="boton-flotante">
                            <div class="col-sm-6">			
                                <small><button class="btn btn-default trigger" data-target="#tab-2"><i class="fa fa-hand-o-right"></i> Adelante</button></small>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label("tipo","Tipo de la falta",array("for"=>"tipo","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-6">
                                <select name="tipo" id="tipo" class="form-control" required="required">
                                    <option value="">-- Seleccione el tipo --</option>
                                    @foreach($tipos as $id_tipo=>$tipo)
                                    <option value="{{ $id_tipo }}">{{ $tipo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label("coordinador","Coordinador Acad&eacute;mico",array("for"=>"coordinador","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-6">
                                <select name="coordinador" id="coordinador" class="form-control" required="required">
                                    <option value="">-- Seleccione el coordinador --</option>
                                    @foreach($tiposB as $id_tipo=>$tipo)
                                    <option value="{{ $id_tipo }}">{{ strtoupper($tipo) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label("calificacion","Calificaci&oacute;n de la falta",array("for"=>"calificacion","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-6">
                                <select name="calificacion" id="calificacion" class="form-control" required="required">
                                    <option value="">-- Seleccione la calificaci&oacute;n --</option>
                                    <option value="LEVE">LEVE</option>
                                    <option value="GRAVE">GRAVE</option>
                                    <option value="GRAVISIMA">GRAV&Iacute;SIMA</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label("hechos","Descripci&oacute;n detallada de los hechos que presuntamente constituyen la falta",array("for"=>"hechos","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-6">
                                {!! Form::textarea("hechos", null, array("id"=>"hechos","class"=>"col-md-4 form-control", "required"=>"required")) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label("evidencias","Describa y folie las Evidencias que lo soportan (Testigos y/o pruebas que aporta)",array("for"=>"evidencias","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-6">
                                {!! Form::textarea("evidencias", null, array("id"=>"evidencias","class"=>"col-md-4 form-control", "required"=>"required")) !!}
                            </div>
                        </div>


                    </div>
                    <div id="tabs-2">
                        <div class="boton-flotante">
                            <div class="col-sm-6">			
                                <small><button class="btn btn-danger trigger" data-target="#tab-1"><i class="fa fa-hand-o-left "></i> Atras</button></small>
                            </div>
                            <div class="col-sm-6">			
                                <small><button class="btn btn-default trigger" data-target="#tab-3"><i class="fa fa-hand-o-right"></i> Adelante</button></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                {!! Form::label("aprendiz","Seleccionar aprendiz",array("for"=>"evidencias","class"=>"control-label col-md-3")) !!}
                                <div class="col-sm-4">
                                    {!! Form::text("aprendiz", null, array("id"=>"queja_aprendiz",
                                    "class"=>"col-md-4 form-control",
                                    "data-enlace"=> url("seguimiento/educativa/aprendices"))) !!}
                                    <span class="help-block">
                                        Por favor digite el <code>n&uacute;mero de identificaci&oacute;n</code> o <code>n&uacute;mero de ficha</code> del aprendiz que desea relacionar
                                    </span>
                                    <small>
                                        <table class="table table-striped" id="tabla_queja">

                                        </table>
                                    </small>
                                </div>
                                <div id="aprendices_final">

                                    <table class="table table-striped" id="tabla_queja_final">

                                    </table>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-offset-7 col-sm-4">

                            </div>
                        </div>

                    </div>

                    <div id="tabs-3">

                        <div class="boton-flotante">
                            <div class="col-sm-6">			
                                <small><button class="btn btn-danger trigger" data-target="#tab-2" ><i class="fa fa-hand-o-left "></i> Atras</button></small>
                            </div>
                            <div class="col-sm-6">			
                                <small>{!! Form::submit("Generar", array("class"=>"btn btn-success ajax-link")) !!}</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">

                                <div class="col-sm-8 col-sm-offset-1">
                                    <div class="form-group">
                                        {!! Form::label("Capitulos","Cap&iacute;tulos",array("for"=>"tipo","class"=>"control-label col-md-3")) !!}
                                        <div class="col-sm-8">
                                            <select name="capitulos" id="capitulos" class="form-control" data-url="{{ url("seguimiento/educativa/literales") }}">
                                                <option value="">-- Seleccione el Cap&iacute;tulo --</option>
                                                @foreach($capitulos as $id_tipo=>$tipo)
                                                <option value="{{ $tipo->cap_codigo }}">{{ $tipo->cap_codigo . $tipo->cap_descripcion }}</option>
                                                @endforeach
                                            </select>
                                            <span class="help-block">
                                                Por favor seleccione el cap&iacute;tulo que desea relacionar
                                            </span>
                                        </div>
                                    </div>

                                    <small>
                                        <table class="table table-striped" id="tabla_literal">

                                        </table>
                                    </small>
                                </div>
                                <div id="literales">

                                    <table class="table table-striped" id="tabla_literal_final">

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

@section('plugins-css')

<style>
    #literales {
        background-color: #ffad55;
        border: 1px dotted #ec7114;
        float: left;
        opacity: 0.81;
        position: fixed;
        right: 20px;
        top: 55px;
        width: 20%;
        z-index: 9999999;
    }
    
    #aprendices_final {
        background-color: #ffad55;
        border: 1px dotted #ec7114;
        float: left;
        opacity: 0.81;
        position: fixed;
        right: 20px;
        top: 55px;
        width: 25%;
        z-index: 9999999;
    }
</style>

@endsection

@section('plugins-js')


<script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {

    $.validator.setDefaults({
        ignore: []
    });

    $('#formQueja').validate({
        rules: {
            literalHidden: {
                required: true
            },
            aprendizHidden: {
                required: true
            }
        },
        messages: {
            literalHidden: {
                required: "Debe seleccionar por lo menos un(1) literal del reglamento para la falta"
            },
            aprendizHidden: {
                required: "Debe seleccionar por lo menos un(1) aprendiz para la falta"
            }
        }
    });
});
</script>

<script type="text/javascript">
    function validaTablas()
    {
        if ($(".aprendizSelect").length > 0)
        {
            $("#aprendizHidden").val("1");
        }
        else
        {
            $("#aprendizHidden").val("");
        }

        if ($(".literalSelect").length > 0)
        {
            $("#literalHidden").val("1");
        }
        else
        {
            $("#literalHidden").val("");
        }
    }

</script>

@endsection