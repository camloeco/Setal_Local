@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Gesti&oacute;n de Usuarios','Creaci&oacute;n de usuario') !!}

<div clas="row">
    @include('errors.messages')

    <div class="col-xs-12 col-sm-12">
        <form class="form-horizontal" action="{{ url('users/users/create') }}" method="post" data-toggle="validator" novalidate id="formUsuario">
            <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">
            <div class="box ui-draggable ui-droppable">
                <div class="box-header">
                    <div class="box-name ui-draggable-handle">
                        <i class="fa fa-search"></i>
                        <span>Creaci&oacute;n del usuario</span>
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
                    <p>A continuaci&oacute;n podra crear usuarios del sistema con sus respectivos roles y permisos. Recuerde que los 
                        campos marcados con <code>(*)</code> son obligatorios </p>

                    <div id="tabs">
                        <ul class="ui-tabs-nav">
                            <li><a href="#tabs-1" >Informaci&oacute;n b&aacute;sica</a></li>
                            <li><a href="#tabs-2" >Configuraci&oacute;n de acceso</a></li>
                            <li><a href="#tabs-3" >Otros datos</a></li>
                        </ul>
                        <div id="tabs-1">
                            <div class="form-group">
                                {!! Form::label("par_identificacion", "N&uacute;mero de identificaci&oacute;n *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::text("par_identificacion", Input::old('par_identificacion'), array("class"=>"form-control", "id"=>"par_identificacion", "placeholder"=>"N&uacute;mero de identificaci&oacute;n", 
                                    "required"=>"required",
                                    "minlength"=>"7")) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label("par_nombres", "Nombres *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::text("par_nombres", Input::old('par_nombres'), array("class"=>"form-control", "id"=>"par_nombres", "placeholder"=>"Nombres",
                                    "required"=>"required")) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label("par_apellidos", "Apellidos *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::text("par_apellidos", Input::old('par_apellidos'), array("class"=>"form-control", "id"=>"par_apellidos", "placeholder"=>"Apellidos",
                                    "required"=>"required")) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label("par_correo", "Email *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::email("par_correo", Input::old('par_correo'), array("class"=>"form-control no-upper", "id"=>"par_correo", "placeholder"=>"Email",
                                    "required"=>"required")) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label("par_telefono", "T&eacute;lefono *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::text("par_telefono", Input::old('par_telefono'), array("class"=>"form-control", "id"=>"par_telefono", "placeholder"=>"T&eacute;lefono",
                                    "required"=>"required")) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label("par_direccion", "Direcci&oacute;n *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::text("par_direccion", Input::old('par_direccion'), array("class"=>"form-control", "id"=>"par_direccion", "placeholder"=>"Direcci&oacute;n",
                                    "required"=>"required")) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label("gender", "Genero *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::select('gender', array(''=> '-- Seleccione genero --','male' => 'Masculino', 'female' => 'Femenino'),null, array("class"=>"form-control",
                                    "required"=>"required")) !!}
                                </div>
                            </div>
                            
                            <div class="form-group">
                                {!! Form::label("gender", "Horas contrato *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    <select name="horas_contrato" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <option value="40">40</option>
                                        <option value="32">32</option>
                                        <option value="20">20</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div id="tabs-2">
                            <div class="form-group">
                                {!! Form::label("password", "Contrase&ntilde;a *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::password("password", array("class"=>"form-control", "id"=>"password", "placeholder"=>"Contrase&ntilde;a",
                                    "required"=>"required",
                                    "minlength"=>"6")) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label("confirm_password", "Confirmar contrase&ntilde;a *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::password("confirm_password", array("class"=>"form-control", "id"=>"confirm_password", "placeholder"=>"Confirmar contrase&ntilde;a",
                                    "required"=>"required",
                                    "match"=>"#password")) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label("roles", "Roles *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    <p class="help-block">Por favor seleccione los roles a los cuales el usuario tendra acceso.
                                        Entre los roles seleccionados, seleccionar el rol principal del usuario.
                                    </p>
                                    <table class="table table-responsive">
                                        <tr>
                                            <th><small>Rol</small></th>
                                            <th><small>Nombre</small></th>
                                            <th><center><small>Rol principal</small></center></th>

                                        </tr>
                                        <?php $count = 0; ?>
                                        @foreach($roles as $id_rol=>$nombre_rol)

                                        <tr>
                                            <td>
                                                <input required type="checkbox" value="{{$id_rol}}" name='roles[]' class="form-control" />
                                            </td>
                                            <td>
                                                {{ $nombre_rol }}
                                            </td>
                                            <td>
                                                <input required type="radio" value="{{$id_rol}}" name='rol' class="form-control" />
                                            </td>

                                        </tr>
                                        <?php $count++ ?>
                                        @endforeach
                                    </table>

                                </div>

                            </div>

                        </div>

                        <div id="tabs-3">
                            <div class="form-group has-feedback">
                                {!! Form::label("birthdate", "Fecha de nacimiento *", array("class"=>"col-sm-3 control-label")) !!}

                                <div class="col-sm-3 input-group datetimepicker">
                                    {!! Form::text('birthdate', Input::old('birthdate'), array("class"=>"form-control",
                                    "required"=>"required")); !!}
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>

                            </div>

                            <div class="form-group has-feedback">
                                {!! Form::label("observations", "Observaci&oacute;n", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::textarea("observations", null, array("class"=>"form-control", "id"=>"observations", "placeholder"=>"Observaciones")); !!}
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="box-footer">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-6 input-group">
                        {!! Form::submit("Crear", array("class"=>"btn btn-success")) !!}
                        {!! Form::reset("Reiniciar campos", array("class"=>"btn btn-default")) !!}
                    </div>
                </div><!-- /.box-footer -->


            </div>
        </form>
    </div>
</div>


@endsection

@section("plugins-css")
<link rel="stylesheet" href="{{ asset("css/bootstrap-datetimepicker.css") }}">
@endsection

@section('plugins-js')
<script type="text/javascript" src="{{ asset("devoops/plugins/moment/moment.min.js") }}"></script>
<script type="text/javascript" src="{{ asset("js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
$(function () {
    $('.datetimepicker').datetimepicker();
});
</script>


<script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {

    $.validator.setDefaults({
        ignore: []
    });

    $('#formUsuario').validate(/*{
        submitHandler: validarFormTabs()
    }*/);
});
</script>

<script type="text/javascript">
/*
    $(document).ready(function () {
        $(document).on("click", "input[type=submit]", function () {
            validarFormTabs();
        });

        $(document).on("click", ".ui-tabs-nav li", function () {
            validarFormTabs();
        });
    });

    function validarFormTabs() {
        $(".ui-tabs-nav li").each(function () {
            var este = $(this);
            var div = este.children("a").attr("href");

//            $(div + " .error").each(function(){
//                if($(this).is(":visible")){
//                    if (!este.children("a").hasClass("tabsError")) {
//                        este.children("a").addClass("tabsError").append(" <i class='fa fa-times' ></i>").parent().removeClass("ui-state-default");
//                    }
//                }else{
//                    este.children("a").removeClass("tabsError").parent().addClass("ui-state-default");
//                    este.children("a").find('i').remove();
//                }
//            });
            
            if ($(div + " .error").length > 0) {
                if (!este.children("a").hasClass("tabsError")) {
                    este.children("a").addClass("tabsError").append(" <i class='fa fa-times' ></i>").parent().removeClass("ui-state-default");
                }
            } else {
                if (este.children("a").hasClass("tabsError")) {
                    este.children("a").removeClass("tabsError").parent().addClass("ui-state-default");
                    este.children("a").find('i').remove();
                }
            }

        });
    }
    */
</script>
@endsection