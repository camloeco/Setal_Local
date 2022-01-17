@extends('templates.devoops')

@section('content')

@if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))

{!! getHeaderMod('Gesti&oacute;n de Usuarios','Eliminar rol') !!}

<div class="col-xs-12 col-sm-12">
    <div class="box ui-draggable ui-droppable">
        <div class="box-header">
            <div class="box-name ui-draggable-handle">
                <i class="fa fa-search"></i>
                <span>Eliminar usuario</span>
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
                <div class="col-md-10 col-md-offset-2">
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt>Nombre del rol</dt>
                            <dd>{{ $rol->nombre_rol }}</dd>
                        </dl>
                        {!! Form::hidden("id", $rol->id_rol) !!}

                    </div><!-- /.box-body -->
                </div>
            </div>


            @if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
        </div><!-- /.box-body -->
    </div>
</div>

@endif 

@endsection
