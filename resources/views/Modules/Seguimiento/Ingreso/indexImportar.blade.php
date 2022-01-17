@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Ingreso','Importar persona') !!}

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
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <strong>Bien!</strong> {{ $mensaje['exito'] }}
                </div>
                @endif

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

                @if (isset($mensaje['errores']))
                    @if ($mensaje['errores']['exito']>0)
                    <div class="alert alert-success">
                        <button data-dismiss="alert" class="close" type="button">×</button>Se importaron 
                        <strong>{{ $mensaje['errores']['exito'] }}</strong> registros exitosamente
                    </div>
                    @endif

                    @if (isset($mensaje['errores']['errores']))
                    <div class="alert alert-danger">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        <ul>
                            @foreach ($mensaje['errores']['errores'] as $key=>$mensajes)
                                @foreach($mensajes as $msg)
                                    <li>
                                        <strong>Linea {{ $key }}!</strong> {!! $msg !!}
                                    </li>
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
                    <i class="fa fa-search"></i>
                    <span>Cargar archivo</span>
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
                <div class="row">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group has-success has-feedback">
                            {!! Form::label("archivoCsv","Cargar archivo",array("for"=>"archivoCsv", "class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-4">
                                {!! Form::file("archivoCsv",array("id"=>"archivoCsv")) !!}
                                <p class="help-block">Cargar archivo en formato Excel.</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-3">
                                <div class="col-sm-4">
                                    <div class="row">
                                        <label>Rol</label>
                                        <select class="form-control" name="rol" required>
                                            <option value="">Seleccione...</option>
                                            <option value="Aprendiz">Aprendiz</option>
                                            <option value="Empleado">Empleado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-3">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                {!! Form::submit("Cargar", array("class"=>"btn btn-success ajax-link")) !!}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection