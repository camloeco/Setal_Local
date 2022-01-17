@extends('templates.devoops')

@section('content')

@if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))

{!! getHeaderMod('Alerta','No tiene los permisos suficientes') !!}

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

            <div class="alert alert-danger">

                <i class="icon_error-triangle"> </i>

                No posee los permisos suficientes para esta funci&oacute;n del sistema. Por favor contactar con el administrador
            </div>

@if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
        </div>
    </div>
</div>
@endif

@endsection