@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Cargue de informaci&oacute;n') !!}

<div class=" row">
    <div class="col-md-3 col-sm-3 col-xs-6">
        <a href="{{ url('/seguimiento/reportes/aprendiz') }}" class="well" top-block="" title="" data-toggle="tooltip" data-original-title="6 new members.">
            <i class="glyphicon glyphicon-user blue"></i>

            <div>Aprendiz por cedula</div>
            <small>Reporte de aprendiz por cedula, genera archivo excel para exportar</small>
        </a>
    </div>

    <div class="col-md-3 col-sm-3 col-xs-6">
        <a href="{{ url('/seguimiento/reportes/ficha') }}" class="well" top-block="" title="" data-toggle="tooltip" data-original-title="4 new pro members.">
            <i class="glyphicon glyphicon-star green"></i>

            <div>Opci&oacute;n de etapa por ficha</div>
            <small>Reporte de la ficha indicando participantes, genera archivo excel para exportar</small>
        </a>
    </div>

</div>

@endsection