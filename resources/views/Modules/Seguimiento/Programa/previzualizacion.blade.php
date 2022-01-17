@extends('templates.devoops')

@section('content')
{!! getHeaderMod('Seguimiento a proyectos','Previsualizaci&oacute;n de planeaci&oacute;n pedagogica') !!}
<div class="row">

    <div class="col-xs-12 col-sm-12">

        @if(isset($registroFase))
        <?php $registroFaseFinal = $registroFase; ?>
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
                @foreach($registroFase as $actividades)
                    @foreach($actividades as $actividadGlobal)

                        <?php
                        $codigo = $actividadGlobal['codigo'];
                        $programa = $actividadGlobal['programa'];
                        break;
                        ?>

                    @endforeach
                    <?php break; ?>
                @endforeach

                <p>Planeaci&oacute;n pedag&oacute;gica para el programa 
                    <strong>{{$programa}}</strong> (<code>{{$codigo}}</code>)</p>

                <div id="tabs">
                    <ul>
                        <li><a href="#tabs-0">Fase de an&aacute;lisis</a></li>
                        <li><a href="#tabs-1">Fase de planeaci&oacute;n</a></li>
                        <li><a href="#tabs-2">Fase de ejecuci&oacute;n</a></li>
                        <li><a href="#tabs-3">Fase de evaluaci&oacute;n</a></li>
                    </ul>
                    <?php for ($i = 0; $i <= 3; $i++) { $resulFases[]=array();?>
                        <div id="tabs-{{$i}}">
                            <table class="table table-responsive">
                                <thead>
                                    <tr>
                                        <th width="20%">Actividad</th>
                                        <th width="10%" class="text-center">Duraci&oacute;n</th>
                                        <th width="30%">Resultado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sumaFases[$i] = 0;
                                    $cantidadResultados[$i] = 0;
                                    $cantidadActividades[$i] = 0;
                                    
                                    $contador = 1;
                                    unset($registroFase[$i][0]);
                                    
                                    ?>
                                    @foreach($registroFase[$i] as $competencia=>$actividades)
                                        
                                        @foreach($actividades as $actividad=>$resultados)
                                            <?php $cantidadActividades[$i]++ ?>
                                            <tr>
                                                <td style="vertical-align:middle;" rowspan="{{ count($resultados)+1 }}">
                                                    <p>{{ $actividad }}</p></td>
                                                <td style="vertical-align:middle;border-right: 1px solid #ec7114" rowspan="{{ count($resultados)+1 }}" class="text-center text-danger">
                                                    <strong>
                                                        @foreach($resultados as $resul)
                                                            {{ $resultados['duracion'] }}
                                                            <?php  
                                                            $sumaFases[$i] +=  $resultados['duracion'];
                                                            break; 
                                                            ?>
                                                        @endforeach
                                                    </strong>
                                                </td>
                                            </tr>
                                                
                                                @foreach($resultados as $resultado)
                                                <?php if(isset($resultado['resultado'])){?>
                                                <tr>
                                                    <td style="vertical-align:middle;" >
                                                        <small>{{ @$resultado['resultado'] }}</small>
                                                        <?php 
                                                        
                                                        //if (!in_array($resultado['resultado'], $resulFases[])) {
                                                          //  $resulFases[]=$resultado['resultado'];
                                                            $cantidadResultados[$i]++;
                                                        //}
                                                         ?>
                                                    </td>
                                                </tr>
                    <?php }else{?>
                                                <tr>
                                                    <td style="vertical-align:middle;" >
                                                        
                                                    </td>
                                                </tr>
                                                <?php }?>
                                                @endforeach   

                                        @endforeach
                                    @endforeach
                            </tbody>
                        </table>
                    </div>
                    <?php } // foreach ?>
                </div>
                <div id="observacion" >
                    
                    <div><strong>Por favor revisar la planeaci&oacute;n para cada una de las fases </strong></div>
                    <table class="table">
                        <tr>
                            <th><small>Fase</small></th>
                            <th><small>Horas</small></th>
                            <th><small># Actividades</small></th>
                            <th><small># Resultados</small></th>
                        </tr>
                        <tr>
                            <td><code>An&aacute;lisis</code></td>
                            <td class="text-center"><?php echo $sumaFases[0]?></td>
                            <td class="text-center"><?php echo $cantidadActividades[0]?></td>
                            <td class="text-center"><?php echo $cantidadResultados[0]?></td>
                        </tr>
                        <tr>
                            <td><code>Planeaci&oacute;n</code></td>
                            <td class="text-center"><?php echo $sumaFases[1]?></td>
                            <td class="text-center"><?php echo $cantidadActividades[1]?></td>
                            <td class="text-center"><?php echo $cantidadResultados[1]?></td>
                        </tr>
                        <tr>
                            <td><code>Ejecuci&oacute;n</code></td>
                            <td class="text-center"><?php echo $sumaFases[2]?></td>
                            <td class="text-center"><?php echo $cantidadActividades[2]?></td>
                            <td class="text-center"><?php echo $cantidadResultados[2]?></td>
                        </tr>
                        <tr>
                            <td><code>Evaluaci&oacute;n</code></td>
                            <td class="text-center"><?php echo $sumaFases[3]?></td>
                            <td class="text-center"><?php echo $cantidadActividades[3]?></td>
                            <td class="text-center"><?php echo $cantidadResultados[3]?></td>
                        </tr>
                    </table>
                    
                    <form action="{{ url('seguimiento/programa/acentar') }}" method="post">
                        <input type='hidden' name='_token' value="{{ csrf_token() }}">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <small>¿Esta seguro que desea confirmar los cambios?</small>
                                {!! Form::submit("Confirmar", array("class"=>"pull-right btn btn-default ajax-link")) !!}
                            </div>
                            {!! Form::close() !!}

                        </div>

                        
                    </form>
                </div>

            </div>
        </div>
        @endif
    </div>

@endsection

@section('plugins-css')
    <style type="text/css">
        #observacion{
            background-color: #ffad55;
            border: 1px dotted #ec7114;
            float: left;
            opacity: 0.95;
            position: fixed;
            right: 20px;
            top: 200px;
            width: 30%;
            z-index: 9999999;
            color:white; 
            font-size: 16px;
        }
        
        #observacion table tr th, #observacion table tr td{
            border-top: 0px;
        } 
    </style>
@endsection

@section('plugins-js')

    <script type="text/javascript">
        $(document).ready(function() {
            $("#tabs").tabs();
        });
    </script>

@endsection