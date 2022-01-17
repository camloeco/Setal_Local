@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Planeaci&oacute;n pedag&oacute;gica ('.$programa->prog_nombre.')') !!}
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

        {!! Form::open(array("url" => url("seguimiento/programa/planeacion"), "method"=>"post", "files"=> true, "class"=>"form-horizontal")) !!}
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Filtros de b&uacute;squeda</span>
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
                <p>Seleccione los criterios de busqueda para la planeaci&oacute;n pedag&oacute;gica del programa <code>{{ $programa->prog_nombre }}</code> con el c&oacute;digo <code>{{ $programa->prog_codigo }}</code>.</p>

                <div class="form-group">
                    {!! Form::label("version","Versi&oacute;n del programa",array("for"=>"version", "class"=>"control-label col-md-4")) !!}
                    <div class="col-sm-1">
                        {!! Form::number("version", $version, array("id"=>"version","placeholder"=>"Versi&oacute;n del programa", "class"=>"col-md-1 form-control")) !!}
                    </div>
                    <p class="help-block">Por defecto se cargara la versi&oacute;n mas reciente (<code>Versi&oacute;n {{ $version }}.0</code>)</p>
                </div>

                <div class="form-group">
                    {!! Form::label("fase","Fase",array("for"=>"fase","class"=>"control-label col-md-4")) !!}
                    <div class="col-sm-2">
                        <select data-rel="chosen" id="fase" name="fase" class="form-control col-md-4">
                            <option value="">-- Seleccionar --</option>
                            @foreach($fases as $key=>$fasenuevo)
                            <option value="{{ $key }}" <?php echo ($key == $fase) ? 'selected' : '' ?>>{{ $fasenuevo }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">			
                        {!! Form::submit("Filtrar", array("class"=>"btn btn-success control-label ajax-link")) !!}
                    </div>
                </div>

                {!! Form::hidden("programa",$programa->prog_codigo) !!}

                <!-- Form::text("resultados", null, array("id"=>"resultados", "class"=>"col-md-1 form-control")) -->

            </div>
        </div>

        {!! Form::close() !!}
        <!--/span-->

        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Resultados de la b&uacute;squeda</span>
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
                <p>Planeaci&oacute;n pedag&oacute;gica para el programa <code>{{ $programa->prog_nombre }}</code> con el c&oacute;digo <code>{{ $programa->prog_codigo }}</code>.</p>


                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fase</th>
                            <th>Actividad</th>
                            <th>Duraci&oacute;n (Horas)</th>
                            <th>Competencias y resultados</th>
                            <!--<th colspan="3">Acciones</th>-->
                        </tr>
                    </thead>
                    <tbody>

                        <?php $count = 1 ?>
                        @foreach($actividades as $actividad)
                        <tr>
                            <th>{{ $count++ }}</th>
                            <td>
                                {{ $fases[$actividad->fas_id] }}
                            </td>
                            <td>
                                {{ $actividad->act_descripcion }}
                            </td>
                            <td>
                                {{ $actividadResultado[$actividad->act_id] }}
                            </td>
                            <td>
                                Ver competencias y resultados
                            </td>
                            <!--
                            {!! 
                                acciones(
                                            url('/seguimiento/actividad/show/'.$actividad->act_id), 
                                            url("seguimiento/actividad/edit/".$actividad->act_id),
                                            url("seguimiento/actividad/deleted/".$actividad->act_id)
                                        ) 
                            !!}
                            -->
                        </tr>
                        @endforeach

                    </tbody>
                </table>

                <p>Total de horas de la planeaci&oacute;n pedag&oacute;gica: <strong><code><?php echo array_sum($actividadResultado) ?></code></strong></p>

            </div>
        </div>
    </div>
</div>

@endsection