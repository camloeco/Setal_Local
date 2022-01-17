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
                    <span>Rechazar falta</span>
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

                <p>Est&aacute; seguro de aprobar la falta? <code>{{ Auth::user()->participante->par_nombres." ".Auth::user()->participante->par_apellidos }}</code></p>
                <p>Falta: {{ $datos->edu_falta_descripcion }}</strong></p>
                <h6>
                    <form action="{{ url('seguimiento/educativa/aprobarqueja') }}" method="post" name="form-eliminar" id="form-eliminar" >
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <strong><a href="javascript: document.forms['form-eliminar'].submit(); " ><span ><i class="icon_clock_alt"></i> SI, estoy seguro</span></a></strong> |
                        <strong><a href="{{ url('seguimiento/educativa/gestionarqueja') }}"><span><i class="icon_calendar"></i> NO, aun no lo estoy</span></a></strong>
                        <input type='hidden' value="{{ $datos->edu_falta_id }}" name="cod_queja" >
                    </form>
                </h6>


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

@endsection