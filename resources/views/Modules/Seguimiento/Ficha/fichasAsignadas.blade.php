@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Seguimiento a proyectos','Fichas asignadas para seguimiento practica') !!}
	<div class="box ui-draggable ui-droppable">
		<div class="box-header">
			<div class="box-name ui-draggable-handle">
				<span>Ficha - Etapa pr&aacute;ctica</span>
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
			<div class="row">
			    <div class="col-md-2">
					<label>Total fichas: </label> {{$totalResultados}}<br>
				</div>
				<div class="col-md-8">
					<center><div class="alert alert-danger" style="overflow-y:auto;height: 85px;font-size:14px;padding: 0px;border-color:#999999; background-color:#f1f1f1;color:black">
						<strong style="color:#de1d19;">Alertas!</strong><br>
						<ol>
						    @if(isset($alertas))
        						@foreach($alertas as $alert)
        							@if($alert !="")
        								<li><?php echo $alert; ?></li>
        							@endif																						
        						@endforeach	
						    @else
					    	    Usted no tiene fichas asignadas
					    	@endif
						</ol>
					</div></center>
				</div><br>
			</div>
			<center>
			<hr style="border:1px solid black;"><h4><b>Indicador de colores</b></h4><br>
			<div class="row text-right">
				<div class="col-md-4">
					<label>Ficha aún mes de finalizar por fecha:</label>
				</div>
				<div class="col-md-1" style='background-color:#4250E5;width:10px;'>&nbsp;</div>
			   <div class="col-md-3">
					<label>Ficha Termina por fecha:</label>
				</div>
				<div class="col-md-1" style='background-color:#563838;width:10px;'>&nbsp;</div>
				<div class="col-md-3">
					<label>Ficha Termina por Tiempo:</label>
				</div>
				<div class="col-md-1" style='background-color:#BF0101;width:10px;'>&nbsp;</div>
			</div>
			</center><hr style="border:1px solid black;"><br>
			<div id="responsive" class="table-responsive">
				<table class="table" style="border: 1px solid #bbbbbb;">
					<thead>
						<tr>
							<th>#</th>
							<th>Nivel formaci&oacute;n</th>
							<th>Programa</th>
							<th>Ficha</th>
							<th>Acci&oacute;n</th>
						</tr>
					</thead>
					<?php $contador = 0; ?>
					<tbody id="respuesta">
						@foreach($instructor as $key => $ficha) 
						<?php if($array[$key] != ""){ ?>
		                	@if($alertas[$contador]!="")
    						   <?php
    						   	if (preg_match("/Falta un mes/i", $alertas[$contador])) {
    								$clase=" class='por-cerrar' data-bs-toggle='tooltip' data-bs-placement='top' title='Le falta un mes para que se cierre la ficha'";
    							} else if(preg_match("/terminada por fecha/i", $alertas[$contador])) {
    								$clase=" class='cerradas' data-bs-toggle='tooltip' data-bs-placement='top' title='Ficha cerrada por fechas'";
    							} else if(preg_match("/terminada por tiempo/i", $alertas[$contador])) {
    								$clase=" class='caducada' data-bs-toggle='tooltip' data-bs-placement='top' title='Ficha finalizada por tiempo'";
    							}else if(preg_match("/tiempo en un mes/i", $alertas[$contador])){
    								$clase=" class='caducada' data-bs-toggle='tooltip' data-bs-placement='top' title='Termina por tiempo en un mes'";
    							}
    							?>
 						   @else
							<?php $clase="";?>
						   @endif	
						<tr <?php echo $clase; ?>>						  
							<td><?php echo ++$contador; ?></td> 
							<td>{{ $ficha->niv_for_nombre }}</td>  
							<td>{{ $ficha->prog_nombre }}</td>
							<td>{{ $ficha->fic_numero }}</td>  
							<td><a href="<?php echo url('seguimiento/ficha/consulta?id='.$ficha->fic_numero) ?>" class="miBoton">Ver</a></td>
						</tr>
						<?php } ?>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection
@section('plugins-css')
<style>
	.miBoton{
		background: #087b76;
	    padding: 5px 10px 5px 10px;
	    color: white !important;
	    border-radius: 5px;
	    text-decoration: none !important;
	}
	.miBoton:hover{
		background: #ec7114;
	    padding: 5px 10px 5px 10px;
	    color: white;
	    text-decoration: none;
	}
	th, td{
		text-align: center !important;
	}
	th{
		text-align: center !important;
		font-size: 14px !important;
	}
	td{
		vertical-align: middle !important;
		white-space: pre !important;
		font-size: 12px !important;
	}
	table tr:hover{
		background-color:rgba(175,230,252);
		color:black;
	}    
    .por-cerrar{
		background-color:#4250E5;
		color:white;
	}
	.cerradas{
		background-color:#563838;
		color:white;
	}
	.caducada{
		background-color:#BF0101;
		color:white;
	}	
</style>
@endsection