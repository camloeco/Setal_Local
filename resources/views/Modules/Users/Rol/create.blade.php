@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Gesti&oacute;n de roles','Creaci&oacute;n de rol') !!}


<div clas="row">
    @include('errors.messages')

    <div class="col-xs-12 col-sm-12">
        <form class="form-horizontal" action="{{ url('users/rol/create') }}" method="post" data-toggle="validator" novalidate id="formRol">
            <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">
            <div class="box ui-draggable ui-droppable">
                <div class="box-header">
                    <div class="box-name ui-draggable-handle">
                        <i class="fa fa-search"></i>
                        <span>Creaci&oacute;n de rol</span>
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
                    <p>A continuaci&oacute;n podra crear roles del sistema. Recuerde que los 
                        campos marcados con <code>(*)</code> son obligatorios </p>


                    {!! Form::open(array('url' => 'usuarios/rol/create', 'method' => 'POST'), array('role' => 'form', 'class'=>'ajax-form')) !!}

                    <div class="form-group">
                        {!! Form::label("nombre_rol",  "Nombre del rol" , array("class"=>"col-sm-2 control-label")) !!}
                        <div class="col-sm-6 input-group">
                            {!! Form::text("nombre_rol", null, 
                            array("class"=>"form-control", "id"=>"first_name", "placeholder"=>"Nombre del rol", "required"=>"required")) !!}
                        </div>
                    </div><!-- /.box-body -->

                    <div class="box-footer">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-8 input-group">

                            {!! Form::submit("Crear", array("class"=>"btn btn-success")) !!}
                            {!! Form::reset("Reiniciar campos", array("class"=>"btn btn-default")) !!}

                        </div>
                    </div><!-- /.box-footer -->
                    {!! Form::close()!!}

                </div>

            </div>
        </form>
    </div>
</div>

@endsection

@section('plugins-js')

<script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {

    $.validator.setDefaults({
        ignore: []
    });
//
//    $('#formRol').validate();
});
</script>

@endsection
