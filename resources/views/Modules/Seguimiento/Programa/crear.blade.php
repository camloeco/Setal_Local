@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Crear programas') !!}
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


                @if (isset($mensaje['formato']))
                <div class="alert alert-danger">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <strong>Error!</strong> {{ $mensaje['formato'] }}
                </div>
                @endif

                @if (isset($mensaje['archivo']))
                <div class="alert alert-danger">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <strong>Error!</strong> {{ $mensaje['archivo'] }}
                </div>
                @endif

                @if (isset($mensaje['duplicado']))
                <div class="alert alert-danger">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <strong>Error!</strong> {{ $mensaje['duplicado'] }}
                </div>
                @endif

                @if (isset($mensaje['errores']))
                @if ($mensaje['errores']['exito']>0)
                <div class="alert alert-success">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    Se importaron <strong>{{ $mensaje['errores']['exito'] }}</strong> registros exitosamente
                </div>
                @endif
                @if (isset($mensaje['errores']['errores']))
                <div class="alert alert-danger">
                    <button data-dismiss="alert" class="close" type="button">×</button>

                    <ul>
                        @foreach ($mensaje['errores']['errores'] as $key=>$mensajes)
                        @foreach($mensajes as $msg)
                        <li><strong>Linea {{ $key }}!</strong> {{ $msg }}</li>
                        @endforeach
                        @endforeach
                    </ul>
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i>Crear programa</i>
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

                {!! Form::open(array("url" => url("seguimiento/programa/crear"), "method"=>"post", "files"=> true, "class"=>"form-horizontal", "id"=>"crearPrograma")) !!}
 
                <div class="form-group">
                    {!! Form::label("codigo","C&oacute;digo del programa",array("for"=>"codigo", "class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-4">
                        {!! Form::number("codigo", 1, array("id"=>"codigo","placeholder"=>"C&oacute;digo del programa", "class"=>"col-md-4 form-control", "required"=>"required")) !!}
                    </div>
                </div>
                 
                <div class="form-group">
                    {!! Form::label("prog_nombre","Nombre del programa",array("for"=>"prog_nombre", "class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-4">
                        {!! Form::text("prog_nombre", null, array("id"=>"prog_nombre","placeholder"=>"Nombre del programa", "class"=>"col-md-4 form-control" , "required"=>"required")) !!}
                    </div>
                </div>
                 <div class="form-group">
                    <label class='control-label col-md-3'>Nivel formaci&oacute;n</label>
                    <div class="col-sm-4">
                        <select class="col-md-4 form-control" required name='niv_for_id'>
                            <option value=''>Seleccione...</option>
                            @foreach($nivel_formacion as $niv)
                            <option value='{{ $niv->niv_for_id }}'>{{ $niv->niv_for_nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!--
                <div class="form-group has-error has-feedback">
                    {!! Form::label("prog_pdf","Cargar archivo (Programa en PDF)",array("for"=>"archivoCsv", "class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-4">
                        {!! Form::file("prog_pdf",array("id"=>"prog_pdf")) !!}
                        <p class="help-block">Cargar archivo en formato PDF.</p>
                    </div>
                </div>
                

                <div class="form-group has-success has-feedback">
                    {!! Form::label("archivoCsv","Cargar archivo (Planeaci&oacute;n pedag&oacute;gica)",array("for"=>"archivoCsv", "class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-4">
                        {!! Form::file("archivoCsv",array("id"=>"archivoCsv")) !!}
                        <p class="help-block">Cargar archivo en formato EXCEL.</p>
                    </div>
                </div>
                -->
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        {!! Form::submit("Cargar", array("class"=>"btn btn-success ajax-link")) !!}
                    </div>
                    {!! Form::close() !!}


                </div>
            </div>
        </div>

        <!--/span-->

    </div>
</div>

@endsection

@section('plugins-js')


<script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {

    $.validator.setDefaults({
        ignore: []
    });

    $('#crearPrograma').validate(/*{
        submitHandler: validarFormTabs()
    }*/);
});
</script>

@endsection