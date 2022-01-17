@extends('templates.devoops')

@section('content')

@if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))

{!! getHeaderMod('Gesti&oacute;n de Usuarios','Ver detalle <small><code>beneficio</code></small>') !!}

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
@endif

<div class="row">
    <div class="col-md-6">
        <div class="box-body">
        <img src="{{url('img/bienestar.png')}}" style="width:540px;heigth: 580px;" title="bienestar al aprendiz"/>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box-body">
            <dl class="dl-horizontal">
               @if(count($beneficios) > 0)
                    <h4 style="margin-left:108px;"><b>Lista de beneficios sena:</b></h4>
                    @foreach($beneficios as $bene)
                        <dt>Nombre</dt>
                        <dd>{{$bene->beneficio}}</dd>
                        <dt>Inicio</dt>
                        <dd>{{$bene->fecha_inicio}}</dd>
                        <dt>Fin</dt>
                        <dd>{{$bene->fecha_fin}}</dd><br>
                    @endforeach
                @else
                    <h3 style="margin-left:108px;"><b>El aprendiz no tiene beneficios activos a la fecha actual</b></h3>
                @endif
            </dl>
        </div><!-- /.box-body -->
    </div>
</div>

@if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
</div><!-- /.box-body -->
    </div>
</div>

@endif 

@endsection