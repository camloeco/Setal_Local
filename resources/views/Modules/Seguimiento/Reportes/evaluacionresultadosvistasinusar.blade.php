@extends('templates.devoops')

@section('content')
{!! getHeaderMod('Seguimiento a proyectos','Reporte de Evaluaci&oacute;n por Ficha') !!}
<div class="row">

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

                @if (isset($mensaje['duplicado']))
                <div class="alert alert-danger">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <strong>Error!</strong> {{ $mensaje['duplicado'] }}
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
                        <li><strong>Linea {{ $key }}!</strong> {{ $msg }}</li>
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
       
        @if(isset($fichas))
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Resultados de aprendizaje</span>
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
                
                <p><code>Listado de Fichas </code>
                    Asignadas al Coordinador <code> {{$instructorMuestra[0]->par_nombres . " " . $instructorMuestra[0]->par_apellidos }} </code></p>

                            <table class="table table-responsive table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th width="04%" rowspan="2"style="vertical-align:middle;" >Ficha</th>
                                        <th width="20%" style="vertical-align:middle;" class="text-center" rowspan="2">Programa</th>
                                        <th width="05%" style="vertical-align:middle;" rowspan="2">Instructor Lider</th>
    <!--                                <th width="250px">Instructor</th>-->
                                        <th width="08%" style="vertical-align:middle;" rowspan="2" class="text-center">Fecha Inicio</th>
                                        <th width="08%" style="vertical-align:middle;" rowspan="2" class="text-center">Fecha Fin</th>
                                        <th width="40%" style="vertical-align:middle;" colspan="4" class="text-center">Fases</th>
                                        <th width="10%" style="vertical-align:middle;" rowspan="2" class="text-center">General</th>
                                        <th width="05%" style="vertical-align:middle;" rowspan="2" class="text-center">Ver Detalle</th>
                                    </tr>
                                    <tr>
                                        <th width="10%" class="text-center">An&aacute;lisis</th>
                                        <th width="10%" class="text-center">Planeaci&oacute;n</th>
                                        <th width="10%" class="text-center">Ejecuci&oacute;n</th>
                                        <th width="10%" class="text-center">Evaluaci&oacute;n</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @foreach($fichas as $ficha)
                                    
                                    <tr>
                                        <td style="vertical-align:middle;" >
                                            <p>{{ $ficha['fic_numero'] }}</p>
                                        </td>
                                        <td style="vertical-align:middle;" class="text-center text-danger">
                                            <strong>{{ $ficha['prog_nombre'] }}</strong>
                                        </td>
                                        <td style="vertical-align:middle;" >
                                            <p>{{ $ficha['ins_lider'] }}</p>
                                        </td>
                                        <td style="vertical-align:middle;" >
                                            
                                            <p>{{ $ficha['fic_fecha_inicio'] }}</p>
                                        </td>
                                        <td style="vertical-align:middle;" >
                                            <p>{{ $ficha['fic_fecha_fin'] }}</p>
                                        </td>
                                        <td style="vertical-align:middle;" class="text-center">
                                            {{ $ficha['analisis'] }}% 
                                        </td>
                                        <td style="vertical-align:middle;" class="text-center">
                                            {{ $ficha['planeacion'] }}%
                                        </td>
                                        <td style="vertical-align:middle;" class="text-center">{{ $ficha['ejecucion'] }}%</td>
                                        <td style="vertical-align:middle;" class="text-center">{{ $ficha['evaluacion'] }}%</td>
                                        <td style="vertical-align:middle;" class="text-center">{{ $ficha['general'] }}%</td>
                                        <td style="vertical-align:middle;"><form method="post" action="{{ url('/seguimiento/horario/reporte') }}"><input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="fic_numero" value="{{ $ficha['fic_numero'] }}"><button class="btn btn-primary btn-block" type="submit">Ver</button></form></td>
                                    </tr>

                                    @endforeach
                                </tbody>
                            </table>
                        
            </div>
        </div>
        @endif
    </div>

    
   
    <!--contiene las modales-->
    <div id="modales">
    </div>

    @endsection

    @section('plugins-js')

    <script type="text/javascript" src="{{ asset("pruebacalendario/moment.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset("pruebacalendario/jquery.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset("pruebacalendario/jquery-ui.min.js") }}"></script>

    @endsection
