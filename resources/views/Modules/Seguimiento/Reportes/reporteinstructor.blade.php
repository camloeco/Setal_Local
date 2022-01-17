@extends('templates.devoops')

@section('content')
{!! getHeaderMod('Seguimiento a proyectos','Reporte de Evaluaci&oacute;n por Instructor') !!}
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
       
        @if(isset($planeaciones))
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
                <p><code>Resultados de aprendizaje por Ficha</code>
                    Asignados al Instructor <code> {{$instructorMuestra[0]->par_nombres . " " . $instructorMuestra[0]->par_apellidos }}</code></p>

                            <table class="table table-responsive table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th width="15%">Actividad</th>
                                        <th width="10%" class="text-center">Duraci&oacute;n</th>
                                        <th width="25%">Resultado</th>
    <!--                                <th width="250px">Instructor</th>-->
                                        <th width="20%" class="text-center">Ficha - Programa</th>
                                        <th width="10%" class="text-center">Fecha Inicio</th>
                                        <th width="10%" class="text-center">Fecha Fin</th>
                                        <th width="10%" class="text-center">Evaluado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @foreach($planeaciones as $planeacion)
                                    
                                    <tr>
                                        <td style="vertical-align:middle;" >
                                            <p>{{ $planeacion['act_descripcion'] }}</p>
                                        </td>
                                        <td style="vertical-align:middle;" class="text-center text-danger">
                                            <strong>{{ $planeacion['plf_cantidad_horas'] }}</strong>
                                        </td>
                                        <td style="vertical-align:middle;" >
                                            <p>{{ $planeacion['res_nombre'] }}</p>
                                        </td>
                                        <td style="vertical-align:middle;" >
                                            <strong><p style="text-align: center; border-bottom-style: solid;">{{ $planeacion['fic_numero'] }}</p></strong>
                                            <p>{{ $planeacion['prog_nombre'] }}</p>
                                        </td>
                                        <td style="vertical-align:middle;" >
                                            <p>{{ $planeacion['plf_fecha_inicio'] }}</p>
                                        </td>
                                        <td style="vertical-align:middle;" >
                                            <p>{{ $planeacion['plf_fecha_fin'] }}</p>
                                        </td>
                                            <?php                                                      
                                                    if(strcmp(date('Y-m-d'),$planeacion['plf_fecha_fin'])<0){
                                                        echo '<td style="vertical-align:middle; text-align:center; background-color: #00FF00;">';                                                        
                                                    }
                                                    else if(strcmp(date('Y-m-d'),$planeacion['plf_fecha_fin'])>0){
                                                        if($planeacion['plf_calificacion']=="NO"){
                                                            echo '<td style="vertical-align:middle; text-align:center; background-color: #FF0000;">';
                                                        }
                                                        else{
                                                            echo '<td style="vertical-align:middle; text-align:center; background-color: #00FF00;">';
                                                        }
                                                    }
                                                    else{
                                                        if($planeacion['plf_calificacion']=="NO"){
                                                            echo '<td style="vertical-align:middle; text-align:center; background-color: #FFFF00;">';
                                                        }
                                                        else{
                                                            echo '<td style="vertical-align:middle; text-align:center; background-color: #00FF00;">';
                                                        }
                                                    }
                                                    echo $planeacion['plf_calificacion'];
                                            ?>
                                        </td>
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