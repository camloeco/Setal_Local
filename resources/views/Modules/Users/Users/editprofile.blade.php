@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Gesti&oacute;n de Usuarios','Edici&oacute;n de usuario <small><code>'.$user->participante->par_nombres.' '.$user->participante->par_apellidos.'</code></small>') !!}

<div clas="row">
    @include('errors.messages')

    <div class="col-xs-12 col-sm-12">
        <form class="form-horizontal" action="{{ url('users/users/editprofile') }}" method="post" data-toggle="validator" novalidate id="formUsuario">
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
                        campos marcados con <code>(*)</code> son obligatorios.<br />
                        Dejar la contrase&ntilde;a en blanco si no desea modificarla.
                    </p>

                    <div id="tabs">
                        <ul class="ui-tabs-nav">
                            <li><a href="#tabs-1" >Informaci&oacute;n b&aacute;sica</a></li>
                            @if($user->participante->rol_id == 1)
                            <li><a href="#tabs-2" >Informaci&oacute;n acad&eacute;mica</a></li>
                            @endif
                            <li><a href="#tabs-3" >Configuraci&oacute;n de acceso</a></li>
                            <li><a href="#tabs-4" >Otros datos</a></li>
                        </ul>
                        <div id="tabs-1">
                            <div class="form-group">
                                {!! Form::label("par_identificacion", "N&uacute;mero de identificaci&oacute;n *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::text("par_identificacion", ((Input::old('par_identificacion'))?Input::old('par_identificacion'):$user->par_identificacion), array("class"=>"form-control", "id"=>"par_identificacion", "placeholder"=>"N&uacute;mero de identificaci&oacute;n", 
                                    "required"=>"required",
                                    "minlength"=>"7",
                                    "readonly"=>"readonly")) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label("par_nombres", "Nombres *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::text("par_nombres", ((Input::old('par_nombres'))?Input::old('par_nombres'):$user->participante->par_nombres), array("class"=>"form-control", "id"=>"par_nombres", "placeholder"=>"Nombres",
                                    "required"=>"required")) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label("par_apellidos", "Apellidos *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::text("par_apellidos", ((Input::old('par_apellidos'))?Input::old('par_apellidos'):$user->participante->par_apellidos), array("class"=>"form-control", "id"=>"par_apellidos", "placeholder"=>"Apellidos",
                                    "required"=>"required")) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label("par_correo", "Email *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::email("par_correo", ((Input::old('par_correo'))?Input::old('par_correo'):$user->participante->par_correo), array("class"=>"form-control no-upper", "id"=>"par_correo", "placeholder"=>"Email",
                                    "required"=>"required")) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label("par_telefono", "T&eacute;lefono *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::text("par_telefono", ((Input::old('par_telefono'))?Input::old('par_telefono'):$user->participante->par_telefono), array("class"=>"form-control", "id"=>"par_telefono", "placeholder"=>"T&eacute;lefono",
                                    "required"=>"required")) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label("par_direccion", "Direcci&oacute;n *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::text("par_direccion", ((Input::old('par_direccion'))?Input::old('par_direccion'):$user->participante->par_direccion), array("class"=>"form-control", "id"=>"par_direccion", "placeholder"=>"Direcci&oacute;n",
                                    "required"=>"required")) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label("gender", "Genero *", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::select('gender', array(''=> '-- Seleccione genero --','male' => 'Masculino', 'female' => 'Femenino'),
                                    ((Input::old('gender'))?Input::old('gender'):$user->gender), array("class"=>"form-control",
                                    "required"=>"required")) !!}
                                </div>
                            </div>

                        </div>
                        
                        @if($user->participante->rol_id == 1)
                            <div id="tabs-2">
                                <div class="form-group">
                                    {!! Form::label("estilo_aprendizaje", "Estilo de aprendizaje *", array("class"=>"col-sm-3 control-label")) !!}
                                    <div class="col-sm-6 input-group">
                                        {!! Form::select("est_apr_id", 
                                        $estilos, 
                                        ((Input::old('est_apr_id'))?Input::old('est_apr_id'):$user->participante->est_apr_id), 
                                        array("class"=>"form-control", "id"=>"est_apr_id", "required"=>"required")) !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div id="tabs-3">
                            <div class="form-group">
                                {!! Form::label("password", "Contrase&ntilde;a", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::password("password", array("class"=>"form-control", "id"=>"password", "placeholder"=>"Contrase&ntilde;a",
                                    "minlength"=>"6")) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label("confirm_password", "Confirmar contrase&ntilde;a", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::password("confirm_password", array("class"=>"form-control", "id"=>"confirm_password", "placeholder"=>"Confirmar contrase&ntilde;a",
                                    "match"=>"#password")) !!}
                                </div>
                            </div>

                        </div>

                        <div id="tabs-4">
                            <div class="form-group has-feedback">
                                {!! Form::label("birthdate", "Fecha de nacimiento *", array("class"=>"col-sm-3 control-label")) !!}

                                <div class="col-sm-3 input-group datetimepicker">
                                    {!! Form::text('birthdate', ((Input::old('birthdate'))?Input::old('birthdate'):$user->profile->birthdate), array("class"=>"form-control",
                                    "required"=>"required")); !!}
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>

                            </div>

                            <div class="form-group has-feedback">
                                {!! Form::label("observations", "Observaci&oacute;n", array("class"=>"col-sm-3 control-label")) !!}
                                <div class="col-sm-6 input-group">
                                    {!! Form::textarea("observations", ((Input::old('observations'))?Input::old('observations'):$user->profile->observations), array("class"=>"form-control", "id"=>"observations", "placeholder"=>"Observaciones")); !!}
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="box-footer">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-6 input-group">
                        <input type="hidden" name="id" value="{{$user->id}}">
                        {!! Form::submit("Editar", array("class"=>"btn btn-success")) !!}
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