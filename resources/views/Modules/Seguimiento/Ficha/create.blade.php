@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Creaci&oacute;n de ficha') !!}

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
                    <span>Creaci&oacute;n de la ficha</span>
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
                @if(session()->get('mensaje') != null)
                    <?php $mensaje = session()->get('mensaje'); ?>
                    @if(isset($mensaje['exito']))
                    <div class="alert alert-success">
                      <strong>Exito!</strong> {{ $mensaje['exito'] }}
                    </div>
                    @else
                    <div class="alert alert-danger">
                        @foreach($mensaje['error'] as $msj)
                        <strong>Alerta!</strong> {{ $msj }}<br>
                        @endforeach
                    </div>
                    @endif
                    <?php session()->forget('mensaje');?>
                @endif

                {!! Form::open(array("url" => url("seguimiento/ficha/create"), "method"=>"post", "class"=>"form-horizontal", "id"=>"crearFicha")) !!}
                <div class="form-group">
                    {!! Form::label("fic_numero","N&uacute;mero de la ficha",array("for"=>"fic_numero","class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-6">
                        {!! Form::number("fic_numero", null, array("id"=>"fic_numero","class"=>"col-md-4 form-control" , "required"=>"required")) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label("prog_codigo","Programa de formaci&oacute;n",array("for"=>"prog_codigo","class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-6">
                        <select data-rel="chosen" id="prog_codigo" name="prog_codigo" class="js-example-basic-single form-control" required="required">
                            <option value="">-- Seleccione programa de formaci&oacute;n --</option>
                            @foreach($programas as $programa)
                            <option value="{{ $programa->prog_codigo }}">{{ $programa->prog_codigo }} - {{ $programa->prog_nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label("fic_proyecto","C&oacute;digo del proyecto",array("for"=>"fic_proyecto","class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-6 contenido">
                        <select data-rel="chosen" id="fic_proyecto" name="fic_proyecto" class="js-example-basic-single form-control" required="required">
                            <option value="">-- Seleccione proyecto de formaci&oacute;n --</option>
                            @foreach($proyecto as $pro)
                                <option value="{{$pro->pro_codigo}}">{{$pro->pro_codigo}} - {{$pro->pro_nombre}}</option>
                            @endforeach
                            <option value="No se encuentra">No se encuentra</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label("par_identificacion","Coordinador Acad&eacute;mico",array("for"=>"par_identificacionC","class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-6">
                        <select data-rel="chosen" id="par_identificacionC" name="par_identificacionC" class="form-control" required="required">
                            <option value="">-- Seleccione al Coordinador --</option>
                            @foreach($coordinadores as $key=>$coordinador)
                            <option value="{{ $key }}">{{ $coordinador }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group has-error has-feedback">
                    {!! Form::label("duracion","Fechas Sofia Plus",array("for"=>"hora","class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-3">
                        <input type='date' class="form-control" name="fic_fecha_inicio" required="required"/>
                    </div>
                    <div class="col-sm-3">
                        <input type='date' class="form-control" name="fic_fecha_fin" required="required"/>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label("fic_localizacion","Localizaci&oacute;n",array("for"=>"fic_localizacion","class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-6">
                        {!! Form::text("fic_localizacion", null, array("id"=>"fic_localizacion","placeholder"=>"Pradera, Florida, Etc", "class"=>"form-control col-md-4", "required"=>"required")) !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">			
                        {!! Form::submit("Registrar", array("class"=>"btn btn-success control-label ajax-link")) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!--/span-->
</div>
@endsection
@section('plugins-js')
<script type="text/javascript">
    $(document).ready(function () {
		$(document).on("change","#fic_proyecto",function(){
			var seleccion = $(this).val();
            if(seleccion == "No se encuentra"){
             $(".contenido").html("<input type='number' name='fic_proyecto' class='col-md-4 form-control' required='required'>");
            }
		});		
    });
</script>
@endsection