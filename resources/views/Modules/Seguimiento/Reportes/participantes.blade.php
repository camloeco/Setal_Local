@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Reporte de participantes') !!}

<div class="row">
	<div class="col-sm-6">
		<div class="box box-pricing">
			<div class="well">
					<div class="box-name">
						<h4 >Participante por documento de identidad</h4>   
					</div>
					<div class="no-move"></div>
			</div>
			<div class="row-fluid centered" style="margin-left:10px;">
					Genera reporte del participante por <code>opci&oacute;n de etapa practica</code>, <code>fecha inicio</code> y <code>fecha fin</code>.
			</div>
			<div class="row-fluid bg-default">
				<div class="col-sm-6"><kbd>excel</kbd> <kbd>pdf</kbd> <kbd><i class="fa fa-bar-chart-o"></i></kbd></div>
					<div class="col-sm-6">
						<a href="{{ url('/seguimiento/reportes/aprendiz') }}" class="ajax-link"><button class="btn btn-primary btn-block" type="button">Generar reporte</button></a>
					</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>

@endsection