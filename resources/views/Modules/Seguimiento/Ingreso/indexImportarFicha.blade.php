@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Ingreso','Importar ficha') !!}

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
                    {{ $mensaje['exito'] }}<br>
                    Cantidad exitos: <strong>{{ $mensaje['cantidadExitos'] }}</strong><br>
                    Cantidad errores: <strong>{{ $mensaje['cantidadErrores'] }}</strong><br>
                    Total registros: <strong>{{ $mensaje['cantidadErrores']+$mensaje['cantidadExitos'] }}</strong><br>
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
                <div class="alert alert-danger">
                    <strong>Los siguientes registros no se pudieron registrar o actualizar.</strong>
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <ol>
                        @foreach ($mensaje['errores'] as $key=>$mensajes)
                            <li>
                                <strong>Fila {{ $key }}!</strong> <?php echo "$mensajes"; ?>
                            </li>
                        @endforeach
                    </ol>
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