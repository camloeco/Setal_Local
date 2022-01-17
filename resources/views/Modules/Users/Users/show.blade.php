@extends('templates.devoops')

@section('content')

@if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))

{!! getHeaderMod('Gesti&oacute;n de Usuarios','Ver detalle <small><code>'.$user->participante->par_nombres.' '.$user->participante->par_apellidos.'</code></small>') !!}

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
    <div class="col-md-2">
        <div class="box-body">
            <img class="img-circle" src="{{ getGravatar($user, 160) }}" title="{{ $user->participante->par_nombres }}"/>
        </div>
    </div>
    <div class="col-md-10">
        <div class="box-body">
            <dl class="dl-horizontal">
                <dt>Nombres</dt>
                <dd>{{ $user->participante->par_nombres }}</dd>

                <dt>Apellidos</dt>
                <dd>{{ $user->participante->par_apellidos }}</dd>

                <dt>Email</dt>
                <dd>{{ $user->participante->par_correo }}</dd>

                @if ($user->participante->par_telefono)
                <dt>T&eacute;lefono</dt>
                <dd>{{ $user->participante->par_telefono }}</dd>
                @endif
                
                @if ($user->participante->par_direccion)
                <dt>Direcci&oacute;n</dt>
                <dd>{{ $user->participante->par_direccion }}</dd>
                @endif

                @if ($user->profile->birthdate)
                <dt>Fecha de nacimiento</dt>
                <dd>{{ $user->profile->birthdate }}</dd>
                @endif

                @if ($user->profile->observations)
                <dt>Observaciones</dt>
                <dd>{{ $user->profile->observations}}</dd>
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