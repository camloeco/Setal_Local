@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Reporte por ficha') !!}

<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="glyphicon glyphicon-edit"></i> 
                    <span>Reporte participante por documento de identidad</span>
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

                {!! Form::open(array("url" => url("seguimiento/reportes/ficha"), "method"=>"post", "class"=>"form-horizontal")) !!}
                <div class="form-group">
                    {!! Form::label("ficha","Numero de ficha",array("for"=>"ficha", "class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-4">
                        {!! Form::text("ficha", null, array("id"=>"ficha", "class"=>"form-control")) !!}
                        <p class="help-block">Digite el n&uacute;mero de ficha sin puntos ni comas</p>
                    </div>

                </div>

                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        {!! Form::submit("Buscar", array("class"=>"btn btn-success ajax-link control-label")) !!}
                    </div>


                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</div>

    @if (isset($etapaPractica) && isset($opcionEtapa))
    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <div class="box ui-draggable ui-droppable">
                <div class="box-header">
                    <div class="box-name ui-draggable-handle">
                        <i class="glyphicon glyphicon-edit"></i> 
                        <span>Resultados</span>
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
               
                <div class="box-content" style="overflow-x:auto;">
                        <table class="table table-striped">
                            <thead>
                                <tr role="row">
                                    <th class="sorting_asc"  tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 10%;" aria-sort="ascending" >COORDINADOR</th>
                                    @foreach ($opcionEtapa as $optEtapa)
                                    <th class="table-active" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 7.5%;" aria-sort="ascending" >{{ $optEtapa['ope_descripcion'] }}</th>
                                    @endforeach
                                    <!--<th class="sorting_asc" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 214px;" aria-sort="ascending" >DISPONIBLES</th>-->
                                    <th class="sorting_asc" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 7.5%;" aria-sort="ascending" >TOTAL</th>
                                </tr>
                            </thead>

                            <tbody role="alert" aria-live="polite" aria-relevant="all">
                                
                                <?php
                                //dd($etapaPracticaCoor);
                                foreach($coordinadores as $coordinador){ ?>
                                    <tr class="odd" data-toggle="collapse" data-target="#{{ $coordinador->par_identificacion }}" aria-expanded="false" aria-controls="{{ $coordinador->par_identificacion }}">

                                        <td class="center ">{{ $coordinador->par_nombres }} {{ $coordinador->par_apellidos }}</td>
                                        <?php 
                                        $tot=0;
                                        for($o=1;$o<=11;$o++){
                                            if(isset($etapaPracticaCoor[''.$coordinador->par_identificacion][$o])){
                                             echo "<td class='center text-center'>" . @$etapaPracticaCoor[''.$coordinador->par_identificacion][$o] . "</td>";   
                                             $tot+=$etapaPracticaCoor[''.$coordinador->par_identificacion][$o];
                                            }
                                            else{
                                                echo "<td class='center text-center'>0</td>";
                                            }
                                         } 
                                         ?>
                                        <td class="center text-center"><?php echo $tot; ?></td>

                                       <!-- <td class="center "></td>-->

                                    </tr>
                                    <?php 
                                        foreach($etapaPracticaProg as $cordi=>$eTPrograma){
                                            if($cordi==$coordinador->par_identificacion){
                                                ?>
                                                <tbody class="collapse" id="{{ $coordinador->par_identificacion }}">
                                   <!--                 <td colspan="13" class="center">
                                                        <table class="table table-dark">-->
                                                <?php
                                                foreach($eTPrograma as $indiPro=>$programaCoor){
                                                    //till here 2
                                                    ?>
                                                        
                                                        <tr class="odd" data-toggle="collapse" data-target="#{{ $indiPro }}" aria-expanded="false" aria-controls="{{ $indiPro }}">
                                                            <th class="center " style="width: 10%;">{{ $nombreProgramas[$indiPro] }}</th>
                                                            <?php 
                                                            $totPro=0;
                                                            for($p=1;$p<=11;$p++){
                                                                if(isset($programaCoor[$p])){
                                                                 echo "<td class='center text-center' style='width: 7.5%;vertical-align:middle;'>" . @$programaCoor[$p] . "</td>";   
                                                                 $totPro+=$programaCoor[$p];
                                                                }
                                                                else{
                                                                    echo "<td class='center text-center' style='width: 7.5%;vertical-align:middle;'>0</td>";
                                                                }
                                                             } 
                                                             ?>
                                                            <td class="center text-center" style='width: 7.5%;vertical-align:middle;'><?php echo $totPro; ?></td>
                                                        </tr>
                                                        <tr class="collapse" id="{{ $indiPro }}">
                                                            <td colspan="13" class="center">
                                                                <table class="table table-striped">
                                                            <?php
                                                            //dd($etapaPractica);
                                                            foreach($etapaPractica as $fichasCoordi=>$etpaFicha){
                                                                foreach($etpaFicha as $indEtpFicha=>$valEtpFicha){
                                                                    //dd($indEtpFicha);
                                                                    //dd($fichaPrograma[$indiPro][$indEtpFicha]);
                                                                    if(isset($fichaPrograma[$indiPro][$indEtpFicha])){
                                                                        ?>
                                                                            <tr class="odd">
                                                                                <th class="center " style="width: 13%;">{{ $indEtpFicha }}</th>
                                                                                <?php 
                                                                                $totFic=0;
                                                                                for($f=1;$f<=11;$f++){
                                                                                    if(isset($valEtpFicha[$f])){
                                                                                     echo "<td class='center text-center' style='width: 7.5%;vertical-align:middle;'>" . @$valEtpFicha[$f] . "</td>";   
                                                                                     $totFic+=$valEtpFicha[$f];
                                                                                    }
                                                                                    else{
                                                                                        echo "<td class='center text-center' style='width: 7.5%;vertical-align:middle;'>0</td>";
                                                                                    }
                                                                                 } 
                                                                                 ?>
                                                                                <td class="center text-center" style='width: 7.5%;vertical-align:middle;'><?php echo $totFic; ?></td>
                                                                            </tr>
                                                                        <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                }
                                                ?>
                                               <!--         </table>
                                                    </td>-->
                                                </tbody>
                                                
                                                <?php
                                            }
                                        }
                                    ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    
                </div>
            </div>
            <!--/span-->
        </div>
    </div>
    
    @endif
    
@endsection