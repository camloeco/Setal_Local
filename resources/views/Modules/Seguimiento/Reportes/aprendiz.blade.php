@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Reporte aprendiz por cedula', array(array('seguimiento/reportes/participantes','','Reporte de participantes'))) !!}

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
                {!! Form::open(array("url" => url("seguimiento/reportes/aprendiz"), "method"=>"post", "class"=>"form-horizontal")) !!}

                <div class="form-group">
                    {!! Form::label("cedula","Numero de documento",array("for"=>"cedula", "class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-4">
                        {!! Form::text("cedula", null, array("id"=>"cedula", "class"=>"form-control", "placeholder"=>"Digite n&uacute;mero de documento")) !!}
                        <p class="help-block">Digite su numero de documento sin puntos ni comas</p>
                    </div>

                </div>

                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">			
                        {!! Form::submit("Buscar", array("class"=>"btn btn-success ajax-link control-label")) !!} 
                    </div>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
    <!--/span-->
</div>


@if (isset($etapaPractica))
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
            <div class="box-content">
                <table class="table table-striped">
                    <thead>
                        <tr role="row">

                            <th class="sorting_asc" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 214px;" aria-sort="ascending" aria-label="Username: activate to sort column descending">C&oacute;digo</th>
                            <th class="sorting_asc" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 214px;" aria-sort="ascending" aria-label="Username: activate to sort column descending">N&uacute;mero de documento</th>
                            <th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 180px;" aria-label="Date registered: activate to sort column ascending">Nombres</th>
                            <th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 97px;" aria-label="Role: activate to sort column ascending">Apellidos</th>
                            <th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 96px;" aria-label="Status: activate to sort column ascending">Tel&eacute;fono</th>
                            <th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 392px;" aria-label="Actions: activate to sort column ascending">Correo</th>
                            <th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 392px;" aria-label="Actions: activate to sort column ascending">Opci&oacute;n etapa</th>
                            <th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 392px;" aria-label="Actions: activate to sort column ascending">Fecha inicio</th>
                            <th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 392px;" aria-label="Actions: activate to sort column ascending">Fecha fin</th>
                        </tr>
                    </thead>

                    <tbody role="alert" aria-live="polite" aria-relevant="all">
                        <?php $count = 1; ?>
                        @foreach ($etapaPractica as $etapa)
                        <tr class="odd">
                            <td class="  sorting_1">{{ $count++ }}</td>
                            <td class="center ">{{ $etapa['participante']['par_identificacion'] }}</td>
                            <td class="center ">{{ $etapa['participante']['par_nombres'] }}</td>
                            <td class="center ">{{ $etapa['participante']['par_apellidos'] }}</td>
                            <td class="center ">{{ $etapa['participante']['par_telefono'] }}</td>
                            <td class="center ">{{ $etapa['participante']['par_correo'] }}</td>
                            <td class="center ">{{ $etapa['ope_descripcion'] }}</td>
                            <?php

                            $fecha_inicial = str_replace("/", "-", $etapa['etp_fecha_registro']);
                            $fecha_ini = date('d-m-Y', strtotime($fecha_inicial));

                            $fecha_ini = date_create($fecha_ini);
                            date_add($fecha_ini, date_interval_create_from_date_string('6 month'));
                            $fecha_ini = date_format($fecha_ini, 'd-m-Y');

                            ?>
                            <td class="center ">{{ $fecha_inicial }}</td>
                            <td class="center ">{{ $fecha_ini }}</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--/span-->

</div>
@else

@endif

@endsection