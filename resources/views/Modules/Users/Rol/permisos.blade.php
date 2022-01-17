@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Gesti&oacute;n de roles','Asignar permisos') !!}

<div class="col-xs-12 col-sm-12">

    <div class="row">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Asignar permisos</span>
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


                <dl class="dl-horizontal">
                    <dt>Nombre del rol</dt>
                    <dd><code>{{ $rol->nombre_rol }}</code></dd>
                </dl>

            </div><!-- /.box-body -->
        </div>
    </div>

    <div class="row">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Asignar permisos</span>
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
                <p>Para asignar permisos a las diferentes entidades del modulo, arrastre y suelte de una columna a otra.</p>

                <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                {!! Form::open(array('url' => 'users/rol/edit', 'method' => 'POST', 'novalidate'=>'novalidate', 'id'=>'formRoles')) !!}

                <input type="hidden" name="validacion" id="validacion" />

                <input type="hidden" name="_token" value="{!! csrf_token() !!}">

                <div id="tabs">
                    <ul class="ui-tabs-nav">
                        @foreach($estructura as $id_modulo=>$modulo)
                        <li><a href="#tabs-{{$id_modulo}}" >{{ strtoupper($modulo['nombre']) }}</a></li>
                        @endforeach
                    </ul>

                    @foreach($estructura as $id_modulo=>$modulo)
                    <div id="tabs-{{$id_modulo}}">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>#</th>
                                    <th>Entidad</th>
                                    <th>Acciones permitidas</th>
                                    <th>Acciones no permitidas</th>
                                </tr>
                                <?php $count = 1; ?>
                                @foreach($modulo['controladores'] as $id_controlador=>$controlador)

                                <tr>
                                    <td>{{ $count++ }}</td>
                                    <td>{{ $controlador['nombre'] }}</td>
                                    <td class="funciones-disponibles funcion-ok funcion-{{ $id_controlador }}">
                                        @foreach($controlador['funciones']as $id_funcion=>$funcion)
                                        @if(in_array($id_funcion,$permisos))
                                        <span class="label label-success funciones funciones-{{$id_controlador}}" data-cont="{{$id_controlador}}" id="funcion-{{$id_funcion}}" style="margin-right: 2px;">
                                            {{ $funcion }}
                                            {!! Form::checkbox( 
                                            "funcion[]",
                                            $id_funcion, 
                                            true,
                                            array("style"=>"display:none")
                                            ) !!}
                                        </span>

                                        @endif
                                        @endforeach
                                    </td>
                                    <td class="funciones-disponibles funcion-nok funcion-{{ $id_controlador }}" >
                                        @foreach($controlador['funciones']as $id_funcion=>$funcion)
                                        @if(!in_array($id_funcion,$permisos))
                                        <span class="label label-danger funciones funciones-{{$id_controlador}}" data-cont="{{$id_controlador}}" id="funcion-{{$id_funcion}}" style="margin-right: 2px;">
                                            {{ $funcion }}
                                            {!! Form::checkbox( 
                                            "funcion[]",
                                            $id_funcion, 
                                            false,
                                            array("style"=>"display:none")
                                            ) !!}
                                        </span>

                                        @endif
                                        @endforeach
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    @endforeach
                </div>

                <div class="box-footer">
                    <div class="col-sm-2 col-sm-offset-5 input-group">
                        {!! Form::hidden("id_rol", $rol->id_rol) !!}
                        {!! Form::submit("Actualizar", array("class"=>"btn btn-success control-label ajax-link")) !!}
                    </div>
                </div><!-- /.box-footer -->

                {!! Form::close()!!}

            </div>


        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>

@endsection

@section('plugins-css')
<style>
    .funciones{
        cursor: move;
    }
</style>
@endsection

@section('plugins-js')

<script src="{{ asset('/plugins/jQueryUI/jquery-ui-1.10.3.min.js') }}" type="text/javascript"></script>    

<script type="text/javascript">
$(function () {
    
    validarPermisos();
    
    $(".funciones").each(function () {

        $(this).draggable({revert: "invalid"});

        var controlador = $(this).attr("data-cont");

        $(".funcion-" + controlador).droppable({
            accept: ".funciones-" + controlador,
            drop: function (event, ui) {
                if ($(this).hasClass('funcion-nok')) {
                    ui.draggable.attr("style", "margin-right:2px;").removeClass("label-success").addClass("label-danger").appendTo(this);
                    ui.draggable.find("input").prop("checked", false);
                    validarPermisos();
                } else if ($(this).hasClass('funcion-ok')) {
                    ui.draggable.attr("style", "margin-right:2px;").removeClass("label-danger").addClass("label-success").appendTo(this);
                    ui.draggable.find("input").prop("checked", true);
                    validarPermisos();

                }
                ui.draggable.draggable({revert: "invalid"});
            }
        });

    });

    $(document).on("click", ".tituloAcordeon", function () {
        var id = $(this).attr("data-id");
        $("#enlace-" + id).trigger("click");
    });

    function validarPermisos() {
        if ($(".funcion-ok span").length == 0) {
            $("#validacion").val("");
        }else{
            $("#validacion").val("1");
        }
    }

});
</script>

<script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {

    $.validator.setDefaults({
        ignore: []
    });

    $('#formRoles').validate({
        rules: {
            validacion: {
                required: true
            }
        },
        messages: {
            validacion: {
                required: "Por favor seleccionar al menos un permiso para este rol"
            }
        }
    });
});
</script>

@endsection