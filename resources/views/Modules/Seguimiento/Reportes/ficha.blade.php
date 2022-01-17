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
                <div class="box-content">
                        <table class="table table-striped">
                            <thead>
                                <tr role="row">
                                    @foreach ($opcionEtapa as $optEtapa)
                                    <th class="sorting_asc" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 214px;" aria-sort="ascending" >{{ $optEtapa['ope_descripcion'] }}</th>
                                    @endforeach
                                    <th class="sorting_asc" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 214px;" aria-sort="ascending" >DISPONIBLES</th>
                                    <th class="sorting_asc" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 214px;" aria-sort="ascending" >TOTAL</th>
                                </tr>
                            </thead>

                            <tbody role="alert" aria-live="polite" aria-relevant="all">
                                <?php $disponibles = $totalMatriculados; ?>
                                <tr class="odd">
                                    @foreach ($opcionEtapa as $optEtapa)
                                    @if(isset($etapaPractica[$optEtapa['ope_id']])) 
                                    <?php $disponibles -= (int) $etapaPractica[$optEtapa['ope_id']] ?>
                                    <td class="center ">{{ $etapaPractica[$optEtapa['ope_id']] }}</td>
                                    @else
                                    <td class="center ">0</td>
                                    @endif
                                    @endforeach
                                    <td class="center ">{{ $disponibles }}</td>
                                    <td class="center ">{{ $totalMatriculados }}</td>
                                </tr>
                            </tbody>
                        </table>
                </div>
            </div>
            <!--/span-->
        </div>
    </div>
    
    @endif
    
@endsection