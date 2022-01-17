@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos',$programa->prog_nombre) !!}

<div class="col-xs-12 col-sm-12">
    <div class="box ui-draggable ui-droppable">
        <div class="box-header">
            <div class="box-name ui-draggable-handle">
                <i class="fa fa-code"></i>
                <span>Competencias y resultados</span>
            </div>
            <div class="box-icons pull-right">
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
        <div id="accordion" class="box-content">
            
            @foreach($programa->competencias as $competencia)
                <h3>{{ $competencia->com_nombre }}</h3>
                <div><p>
                    <table class="table table-hover">
                        <thead>
                                <tr>
                                        <th>Resultados</th>
                                </tr>
                        </thead>
                        <tbody>
                            @foreach($competencia->resultados as $key=>$resultado)
                                <tr>
                                        <td class="m-ticker"><strong>Resultado #{{$key+1}}</strong>
                                            <span>{{ $resultado->res_nombre }}</span>
                                        </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table></p>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@section('plugins-js')
<script type="text/javascript">
    $(document).ready(function () {

        var icons = {
            header: "ui-icon-circle-arrow-e",
            activeHeader: "ui-icon-circle-arrow-s"
        };
        // Make accordion feature of jQuery-UI
        $("#accordion").accordion({icons: icons});

    });
</script>
@endsection