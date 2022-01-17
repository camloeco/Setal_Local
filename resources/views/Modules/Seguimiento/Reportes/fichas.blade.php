@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Reporte de fichas') !!}

<div class="row">
	<div class="col-sm-6">
		
		<div class="box box-pricing">
            <div class="well">
                    <div class="box-name">
                        <h4 >Opci&oacute;n de etapa por ficha</h4>   
                    </div>
                    <div class="no-move"></div>
            </div>
            <div class="row-fluid centered" style="margin-left:10px;">
                    Genera reporte de ficha por <code>opci&oacute;n de etapa practica</code>, <code>disponible</code> y <code>total</code>.
            </div>
            <div class="row-fluid bg-default">
                <div class="col-sm-6"><kbd>excel</kbd> <kbd>pdf</kbd> <kbd><i class="fa fa-bar-chart-o"></i></kbd></div>
                    <div class="col-sm-6">
                        <a href="{{ url('/seguimiento/reportes/ficha') }}" class="ajax-link"><button class="btn btn-primary btn-block" type="button">Generar reporte</button></a>
                    </div>
                    <div class="clearfix"></div>
            </div>
        </div>
	</div>
   <!-- 
    <div class="col-sm-6">
		
		<div class="box box-pricing">
            <div class="well">
                    <div class="box-name">
                        <h4 >Evaluaci&oacute;n de resultados de aprendizaje</h4>   
                    </div>
                    <div class="no-move"></div>
            </div>
            <div class="row-fluid centered" style="margin-left:10px;">
                    Genera reporte de evaluaci&oacute;n resultados de aprendizaje por <code>fase</code>, y <code>general</code>.
            </div>
            <div class="row-fluid bg-default">
                <div class="col-sm-6"><kbd>excel</kbd> <kbd>pdf</kbd> <kbd><i class="fa fa-bar-chart-o"></i></kbd></div>
                    <div class="col-sm-6">
                        <a href="{{ url('/seguimiento/reportes/resultadosgeneral') }}" class="ajax-link"><button class="btn btn-primary btn-block" type="button">Generar reporte</button></a>
                    </div>
                    <div class="clearfix"></div>
            </div>
        </div>
	</div>-->
	
	
</div>
<!--
<div class="row">
	<div class="col-sm-6">
		
		<div class="box box-pricing">
                    <div class="well">
                            <div class="box-name">
                                <h4 >Opci&oacute;n de etapa por Coordinador</h4>   
                            </div>
                            <div class="no-move"></div>
                    </div>
                    <div class="row-fluid centered" style="margin-left:10px;">
                            Genera reporte de Coordinaci&oacute;n por <code>opci&oacute;n de etapa practica</code>, <code>disponible</code> y <code>total</code>.
                    </div>
                    <div class="row-fluid bg-default">
                        <div class="col-sm-6"><kbd>excel</kbd> <kbd>pdf</kbd> <kbd><i class="fa fa-bar-chart-o"></i></kbd></div>
                            <div class="col-sm-6">
                                <a href="{{ url('/seguimiento/reportes/coordinador') }}" class="ajax-link"><button class="btn btn-primary btn-block" type="button">Generar reporte</button></a>
                            </div>
                            <div class="clearfix"></div>
                    </div>
                </div>
	</div>
    	
</div>
-->

@endsection