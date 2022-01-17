@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Horarios','Importar plantilla') !!}

<div class="row">
    @if (isset($registros))
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
                @if (isset($registros['errores']))
                    <div class="alert alert-danger">
                        <strong>Solucionar los siguientes errores:</strong>
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        <ol>
                            @foreach ($registros['errores'] as $key => $mensajes)
                                @foreach ($mensajes as $key1 => $mensajes1)
                                    <li><?php echo "$mensajes1"; ?></li>
                                @endforeach
                            @endforeach
                        </ol>
                    </div>
                @endif

                @if (isset($mensaje['formato']))
                <div class="alert alert-danger">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <strong>Error!</strong> {{ $mensaje['formato'] }}
                </div>
                @endif

                @if (isset($mensaje['archivo']))
                <div class="alert alert-danger">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <strong>Error!</strong> {{ $mensaje['archivo'] }}
                </div>
                @endif

                @if (isset($mensaje['errores']))
                @if ($mensaje['errores']['exito']>0)
                <div class="alert alert-success">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    Se importaron <strong>{{ $mensaje['errores']['exito'] }}</strong> registros exitosamente
                </div>
                @endif
                @if (isset($mensaje['errores']['errores']))
                <div class="alert alert-danger">
                    <button data-dismiss="alert" class="close" type="button">×</button>

                    <ul>
                        @foreach ($mensaje['errores']['errores'] as $key=>$mensajes)
                        @foreach($mensajes as $msg)
                        <li><strong>Linea {{ $key }}!</strong> {!! $msg !!}</li>
                        @endforeach
                        @endforeach
                    </ul>
                </div>
                @endif
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
                    <span>Cargar archivo</span>
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
                            <div class="alert alert-danger">
                                <strong>Notificaciones!</strong><br>
                                <?php $arregloMensajes = session()->get('mensajes');?>
                                @if(isset($arregloMensajes))
                                    @foreach($arregloMensajes as $key => $val)
                                        <li> <?php echo $val; ?></li>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    {{ session()->forget('mensajes') }}
                @endif
                <h4>Datos principales</h4>
                <form method="POST" action='{{ url("seguimiento/horario/importar") }}' enctype="multipart/form-data">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Fichas sin horario</th>
                                <th>C&oacute;digo - Programa</th>
                                <th>Tipo oferta</th>
                                <th>Modalidad</th>
                                <th>Jornada</th>
                                <th>Instructor l&iacute;der</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select id="ficha" name="ficha" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <option value="0">Ficha provisional</option>
                                        @foreach($fichasSinHorario as $val)
                                        <option value="{{ $val->fic_numero }}">{{ $val->fic_numero }} - {{ ucfirst(mb_strtolower($val->prog_nombre)) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select id="programa" name="programa" class="form-control">
                                        <option value="">Seleccione...</option>
                                        @foreach($programas as $val)
                                        <option value="{{ $val->prog_codigo }}">{{ $val->prog_codigo }} - {{ ucfirst(mb_strtolower($val->prog_nombre)) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select id='oferta' name="oferta" class="form-control" style="width: auto;" required>
                                        <option value="">Seleccione...</option>
                                        <option value="abierta">Abierta</option>
                                        <option value="cerrada">Cerrada</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="modalidad" name="modalidad" class="form-control" style="width: auto;" required>
                                        <option value="">Seleccione...</option>
                                        <option value="1">Presencial</option>
                                        <option value="2">Virtual</option>
                                    </select>
                                </td>
                                <td>
                                    <select id='jornada' name="jornada" class="form-control" style="width: auto;" required>
                                        <option value="">Seleccione...</option>
                                        <option value="1">Mañana</option>
                                        <option value="2">Tarde</option>
                                        <option value="3">Noche</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="instructor_lider" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($instructores as $val)
                                        <option value="{{ $val->par_identificacion }}">{{ $val->par_nombres }} {{ $val->par_apellidos }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>El programa tiene competencia promover?</th>
                                <th>Fecha inicio lectiva</th>
                                <th>Trimestres lectiva</th>
                                <th>Trimestres productiva</th>
                                <th colspan="2">Adjuntar plan de trabajo</th>
                            </tr>
                            <tr>
                                <td>
                                    <select name="nuevo" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <option value="SI">SI</option>
                                        <option value="NO">NO</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="fecha_inicio_seleccionar" class="form-control">
                                        <option value="">Seleccione...</option>
                                        @foreach($trimestres as $val)
                                        <option value="{{ $val->pla_fec_tri_fec_inicio }}"> {{$val->pla_fec_tri_year}} - {{$val->pla_fec_tri_trimestre}} - {{ $val->pla_fec_tri_fec_inicio }} - {{ $val->pla_fec_tri_fec_fin }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="trimestres_lectiva" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        @for($i=1; $i<=7; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </td>
                                <td>
                                    <select name="trimestres_productiva" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        @for($i=0; $i<=2; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </td>
                                <td  colspan="2">
                                    <input type="file" name="archivoCsv" required>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <input id="cargar" class="btn btn-success form-control" style="height: auto;" type="submit" value="Cargar">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('plugins-css')
    <link rel="stylesheet" href="{{ asset('css/alertify.min.css') }}">
    <style>
        .alertify-notifier > div{
            color: white;
            text-align: center;
            border: black 1px solid;
            font-size: 15px;
        }
    </style>
@endsection
@section('plugins-js')
	<script type="text/javascript" src="{{ asset('js/alertify.min.js') }}"></script>
	<script type="text/javascript">
		$(document).ready(function () {
            $(document).on('change','#ficha', function(){
                var valor = $(this).val();
                if(valor == 0){
                    $('#programa').attr('required', true);
                    $('#programa').attr('readonly', false);
                }else{
                    $('#programa').removeAttr('required');
                    $('#programa').attr('readonly', true);
                    $('#programa').val('');
                }
            });
            
            $(document).on('change','#digitar', function(){
                var fecha = $(this).val();
                var objFecha = new Date(fecha);
                var dia  = objFecha.getDay();

                if(dia != 0){
                    alertify.error('La fecha inicio debe ser lunes.');
                }
                console.log(dia);
            });
        });
    </script>
@endsection