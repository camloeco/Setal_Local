@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Todas las fichas') !!}

<section class='content'>
    <div class="row">
        <div class="col-xs-12">
            <div class="box ui-draggable ui-droppable">

                <div class="box-header">
                    <div class="box-name ui-draggable-handle">
                        <i class="fa fa-table"></i>
                        <span>Importar aprendices con beneficios sena</span>
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
                    @if(session()->get('mensajes') != null)
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="alert alert-default" style="background-color:#309591;color:white;">
                                    <strong>Notificaciones!</strong><br>
                                    <?php $arregloMensajes = session()->get('mensajes'); ?>
                                    <h5>Registros revisados: <strong>{{ $arregloMensajes['registrosRevisados'] }}</strong></h5>
                                    <h5>Registros nuevos: <strong>{{ $arregloMensajes['registrosNuevos'] }}</strong></h5>
                                    <h5>Cantidad de errores: <strong>{{ $arregloMensajes['contadorErrores'] }}</strong></h5>
                                    @if(isset($arregloMensajes['errores']))
                                        @foreach($arregloMensajes['errores'] as $key => $val)
                                            @foreach($val as $val1)
                                                <li>Error en la fila <strong>{{ $key }}</strong> {{ $val1 }}</li>
                                            @endforeach
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{ session()->forget('mensajes') }}
                    @endif
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <form enctype="multipart/form-data" action="{{ url('seguimiento/bienestar/importar') }}" method="POST">
                                <label>Seleccionar archivo</label>
                                <input required style="padding-bottom: 15px;" class="form-control-static" name="archivo" type="file">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input class="btn btn-success btn-xs" type="submit" value="Cargar datos">
                                <a href="{{ asset('Modules/Seguimiento/Formatos/FormatoBeneficiariosSena.xlsx') }}" class="btn btn-warning btn-xs">Descargar formato</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection