@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Importar estado aprendiz') !!}

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
				@if (isset($mensaje['exito']))
                <div class="alert alert-success">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    Se actualizo el estado de <strong>{{ $mensaje['exito'] }}</strong> aprendices exitosamente
                </div>
				@endif
				@if (isset($mensaje['sobrecarga']))
                <div class="alert alert-danger">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <strong>{{ $mensaje['sobrecarga'] }}</strong>
                </div>
                @endif
                @if (isset($mensaje['errores']))
                @if (isset($mensaje['errores']['errores']))
                <div class="alert alert-danger">
                    <button data-dismiss="alert" class="close" type="button">×</button>

                    <ul>
                        @foreach ($mensaje['errores']['errores'] as $key=>$mensajes)
                        @foreach($mensajes as $msg)
                        <li><strong>Linea {{ $key }}!</strong> {!! $msg !!}</li>
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
                {!! Form::open(array("url" => url("seguimiento/participante/cargaestadoaprendiz"), "method"=>"post", "files"=> true, "id"=>"formuploadajax","class"=>"form-horizontal")) !!}

                <div class="form-group has-success has-feedback">
                    {!! Form::label("archivoCsv","Cargar archivo",array("for"=>"archivoCsv", "class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-4">
                        {!! Form::file("archivoCsv",array("id"=>"archivoCsv")) !!}
                        <p class="help-block">Cargar archivo en formato Excel.</p>
                        <p class="help-block">
							<strong>Importante!!</strong><br>La cantidad m&aacute;xima de aprendices que se pueden importar son 5500.
						</p>
  
						<img id="imagenCarga" style="display:none;" src="{{ asset('img/cargando.gif')}}"></img>
						<p id="mensajeCarga" style="display:none;" class="help-block">
							Sea paciente, el archivo está siendo cargado.
						</p>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        {!! Form::submit("Cargar", array("class"=>"btn btn-success ajax-link","id"=>"cargando")) !!}
                    </div>
                </div>
				
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
document.getElementById("cargando").addEventListener("click",function(){
	document.getElementById("imagenCarga").style.display = "block";
	document.getElementById("mensajeCarga").style.display = "block";
});
</script>
@endsection