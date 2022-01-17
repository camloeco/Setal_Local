@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Generar falta o informe') !!}

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
                    <span>Eliminar ficha</span>
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

                <p>Est&aacute; seguro de eliminar la ficha? <code>{{ Auth::user()->participante->par_nombres." ".Auth::user()->participante->par_apellidos }}</code></p>
                <p>N&uacute;mero de ficha: {{ $datos->fic_numero }}</strong></p>
                <h6>
                    <form action="{{ url('seguimiento/ficha/eliminar') }}" method="post" name="form-eliminar" id="form-eliminar" >
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <strong><a href="javascript: document.forms['form-eliminar'].submit(); " ><span ><i class="icon_clock_alt"></i> SI, estoy seguro</span></a></strong> |
                        <strong><a href="{{ url('seguimiento/ficha/index') }}"><span><i class="icon_calendar"></i> NO, aun no lo estoy</span></a></strong>
                        <input type='hidden' value="{{ $datos->fic_numero }}" name="fic_numero" >
                    </form>
                </h6>


        </div>
    </div>
</div>

@endsection
