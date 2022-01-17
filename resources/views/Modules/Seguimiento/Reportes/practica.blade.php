@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento ','Etapa practica') !!}

<div class="row">
	<div class="col-sm-6">		
		<div class="box box-pricing">
			<div class="well">
				<div class="box-name">
					<h4 >Programas en etapa practica</h4>   
				</div>
				<div class="no-move"></div>
			</div>
			<div class="row-fluid centered" style="margin-left:10px;">
					Genera reporte  de <code> fichas y programa de formaci&oacute;n </code> en etapa practica<br><br>
			</div>
			<div class="row-fluid bg-default">
				<div class="col-sm-6"><kbd>excel</kbd> <kbd>pdf</kbd> <kbd><i class="fa fa-bar-chart-o"></i></kbd></div>
				<div class="col-sm-6">
					<a href="{{ url('/seguimiento/reportes/consulta') }}" class="ajax-link"><button class="btn btn-primary btn-block" type="button">Generar reporte</button></a>
				</div>
				<div class="clearfix"></div>
			</div>
         </div>
	</div>
    <div class="col-sm-6">		
		<div class="box box-pricing">
			<div class="well">
					<div class="box-name">
						<h4 >Programas que van a salir a etapa practica</h4>   
					</div>
					<div class="no-move"></div>
			</div>
			<div class="row-fluid centered" style="margin-left:10px;">
					Genera reporte de<code>fichas y programas de formaci&oacute;n </code> que salen  a etapa practica <code>el proximo trimestre</code>.
			</div>
			<div class="row-fluid bg-default">
				<div class="col-sm-6"><kbd>excel</kbd> <kbd>pdf</kbd> <kbd><i class="fa fa-bar-chart-o"></i></kbd></div>
				<div class="col-sm-6">
					<a href="{{ url('/seguimiento/reportes/consulta') }}" class="ajax-link"><button class="btn btn-primary btn-block" type="button">Generar reporte</button></a>
				</div>
				<div class="clearfix"></div>
			</div>
         </div>
	</div>
	<div class="col-sm-6">		
		<div class="box box-pricing">
			<div class="well">
				<div class="box-name">
					<h4 >Programas que terminaron su etapa practica</h4>   
				</div>
				<div class="no-move"></div>
			</div>
			<div class="row-fluid centered" style="margin-left:10px;">
					Genera reporte de<code>fichas y programas de formaci&oacute;n </code> que terminaron su <code> etapa practica </code>.
			</div>
			<div class="row-fluid bg-default">
			<div class="col-sm-6"><kbd>excel</kbd> <kbd>pdf</kbd> <kbd><i class="fa fa-bar-chart-o"></i></kbd></div>
				<div class="col-sm-6">
					<a href="{{ url('/seguimiento/reportes/consulta') }}" class="ajax-link"><button class="btn btn-primary btn-block" type="button">Generar reporte</button></a>
				</div>
				<div class="clearfix"></div>
			</div>
         </div>
	</div>
</div>
@endsection