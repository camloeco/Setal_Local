@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Ejecutar Comit&eacute;') !!}

<div class="row">
    @include('errors.messages')
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
                    <span>Ejecutar Comit&eacute;</span>
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

                <p>Tenga en cuenta que la informaci&oacute;n del comit&eacute; se enviar&aacute; por correo electr&oacute;nico <code>{{ Auth::user()->participante->par_nombres." ".Auth::user()->participante->par_apellidos }}</code></p>

                {!! Form::open(array("url" => url("seguimiento/educativa/acta"), "method"=>"post", "class"=>"form-horizontal", "id"=>"generarActa", "novalidate"=>"novalidate")) !!}

                <div id="tabs">
                    <ul class="ui-tabs-nav">
                        <li><a id="tab-1" href="#tabs-1" >Informaci&oacute;n del acta</a></li>
                        <li><a id="tab-2" href="#tabs-2" >Novedades</a></li>
                    </ul>

                    <div id="tabs-1">
                        <div class="boton-flotante">
                            <div class="col-sm-6">			
                                <small><button class="btn btn-default trigger" data-target="#tab-2" ><i class="fa fa-hand-o-right"></i> Adelante</button></small>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label("edu_acta_quorum","Verificaci&oacute;n del Qu&oacute;rum",array("for"=>"edu_acta_quorum","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-6">
                                <textarea name="edu_acta_quorum" id="edu_acta_quorum" class="form-control" rows="3" required="required" /></textarea>
                                <input type="hidden" name="idFalta" id="idFalta" value="{{ $id }}">
                                <input type="hidden" name="horaInicio" id="horaInicio" value="{{ date('h:i A') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label("edu_acta_descargos","Presentaci&oacute;n de descargos",array("for"=>"edu_acta_descargos","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-6">
                                <textarea name="edu_acta_descargos" id="edu_acta_descargos" class="form-control" rows="8" required="required" /></textarea>

                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label("edu_acta_practicas","Pr&aacute;cticas de pruebas necesarias para el esclarecimieno de los hechos",array("for"=>"edu_acta_practicas","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-6">
                                <textarea name="edu_acta_practicas" id="edu_acta_practicas" class="form-control" rows="3" required="required" /></textarea>    
                                <p class="help-block">Que considere el comit&eacute; decretar o las que le solicite el (los) aprendiz(ces) 
                                    investigado(s)
                                </p>
                            </div>

                        </div>

                        <div class="form-group">
                            {!! Form::label("edu_acta_existencia","Existencia de la conducta",array("for"=>"edu_acta_existencia","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-6">
                                <select name="edu_acta_existencia" id="edu_acta_existencia" class="form-control" required="required" />
                                <option values="">Seleccione...</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                                </select>    

                            </div>

                        </div>

                        <div class="form-group">
                            {!! Form::label("edu_acta_constituye","Constituye o no una falta",array("for"=>"edu_acta_constituye","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-6">
                                <select name="edu_acta_constituye" id="edu_acta_constituye" class="form-control" required="required" />
                                <option values="">Seleccione...</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                                </select>    

                            </div>

                        </div>

                        <div class="form-group">
                            {!! Form::label("edu_acta_autor","Probable autor(es) de la misma",array("for"=>"edu_acta_autor","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-6">
                                <select name="edu_acta_autor" id="edu_acta_autor" class="form-control" required="required" />
                                <option values="">Seleccione...</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                                </select>    

                            </div>

                        </div>

                        <div class="form-group">
                            {!! Form::label("edu_acta_grado_res","Grado de responsabilidad de cada uno",array("for"=>"edu_acta_grado_res","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-6">
                                <textarea name="edu_acta_grado_res" id="edu_acta_grado_res" class="form-control" rows="3" required="required" /></textarea>        

                            </div>

                        </div>

                        <div class="form-group">
                            {!! Form::label("edu_acta_grado_falta","Grado de calificaci&oacute;n de las faltas",array("for"=>"edu_acta_grado_falta","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-6">
                                <textarea name="edu_acta_grado_falta" id="edu_acta_grado_falta" class="form-control" rows="3" required="required" /></textarea>        
                            </div>

                        </div>

                        <div class="form-group">
                            {!! Form::label("edu_acta_sancion","Amerita o no una sanci&oacute;n",array("for"=>"edu_acta_sancion","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-6">
                                <select name="edu_acta_sancion" id="edu_acta_sancion" class="form-control" required="required" />
                                <option values="">Seleccione...</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                                </select>    
                            </div>

                        </div>

                    </div>

                    <div id="tabs-2">
                        <div class="boton-flotante">
                            <div class="col-sm-6">			
                                <small><button class="btn btn-danger trigger" data-target="#tab-1"><i class="fa fa-hand-o-left "></i> Atras</button></small>
                            </div>
                            <div class="col-sm-6">	
                                <small>{!! Form::submit("Generar", array("class"=>"btn btn-success ajax-link")) !!}</small>
                            </div>
                        </div>

                        <div class="well">
                            <p>A continuaci&oacute;n podr&aacute; seleccionar las diferentes sanciones para los aprendices implicados.</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Aprendiz</th>
                                            <th>
                                                Sanciones
                                                <small><code>(Llamado de atenci&oacute;n escrito, Condicionamiento de la matr&iacute;cula, 
                                                        Cancelaci&oacute;n de la matr&iacute;cula)</code></small>
                                            </th>
                                            <th>
                                                Observaciones
                                                <small><code>Medidas formativas</code></small>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($aprendices as $aprendiz)
                                        <tr>
                                            <th><small>{{ $aprendiz->par_nombres." ".$aprendiz->par_apellidos}}</small></th>
                                            <td>
                                                <small>
                                                    @foreach($novedad as $nove)
                                                        <input type="checkbox" name="novedades[{{ $aprendiz->par_identificacion }}][{{$nove->edu_novedad_id}}]" />
                                                        <label for='llamado-{{ $aprendiz->par_identificacion }}'>{{$nove->edu_novedad_descripcion}}</label>
                                                    @endforeach
                                                    
                                                </small>
                                            </td>
                                            <td><small><textarea id="observacion-{{$aprendiz->par_identificacion}}" name="novedades[{{ $aprendiz->par_identificacion }}][observacion]"></textarea></small></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>



                </div>


                {!! Form::close() !!}
            </div>


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
<script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {

    $.validator.setDefaults({
        ignore: []
    });

    $('#generarActa').validate({
        rules: {
            implicadoHidden: {
                required: true
            },
            horarioValido: {
                required: true
            }
        },
        messages: {
            implicadoHidden: {
                required: "Debe seleccionar por lo menos un(1) implicado para el comite"
            },
            horarioValido: {
                required: "La hora del comit&eacute; es invalida, debe haber 30 minutos entre uno y otro"
            }
        }
    });
});
</script>

<script type="text/javascript">
    function validaTablas()
    {
        if ($(".implicadoSelect").length > 0)
        {
            $("#implicadoHidden").val("1");
        }
        else
        {
            $("#implicadoHidden").val("");
        }

    }

    function validarHorario()
    {
        if ($(".horarioSelect").length > 0)
        {
            $("#horarioValido").val("1");
        }
        else
        {
            $("#horarioValido").val("");
        }
    }

</script>
@endsection