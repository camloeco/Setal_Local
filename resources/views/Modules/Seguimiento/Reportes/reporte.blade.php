@if(count($aprendices)>0)
<input type="hidden" id="n_aprendices" name="n_aprendices" value="{{$totAprendices}}">
<table class="table table-bordered table-hover">
		<thead>
			<tr>
				<th>#</th>
				<th>Ficha</th>
				<th>Identificaci&oacute;n</th>
				<th>Nombre</th>
				<th>Apellido</th>
				<th>Correo</th>
				<th>Etapa Productiva</th>
				<th>Acci&oacute;n</th>
			</tr>
		</thead>
		<?php $inicioContador = $contador; ?>
		<tbody>
			@foreach($aprendices as $apr)
			<tr>
				<td>{{ $contador++ }}</td>
				<td>{{ $apr->fic_numero }}</td>
				<td>{{ $apr->par_identificacion }}</td>
				<td>{{ $apr->par_nombres }}</td>
				<td>{{ $apr->par_apellidos }}</td>
				<td>{{ $apr->par_correo }}</td>
				<td>
				<?php 
				if ($array !="") {
					if(array_key_exists($apr->par_identificacion, $arrayAlternativa)){
						echo $arrayAlternativa[$apr->par_identificacion];
					}else{
						echo "Sin alternativa";
					} 	
				}else{
					echo $apr->ope_descripcion;
				}
				?>
				</td>
				<td><a id="modal" data-url="{{ url('seguimiento/reportes/modal')}}" data-id-aprendiz="{{ $apr->par_identificacion }}" data-nombre="{{ $apr->par_nombres }}" data-apellido="{{ $apr->par_apellidos }}" data-toggle="modal" data-target="#myModal" class=" btn btn-primary btn-xs">Ver</a></td>
			</tr>
			@endforeach
		</tbody>
	</table>
	<div style="padding:10px">
	@if($cantidadPaginas > 1)
		@if($cantidadPaginas <= 10)
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					@if($cantidadPaginas > 1 )
						<small style="float:left;">
							Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $totAprendices }} registros
						</small>
					@endif
					@for($i=$cantidadPaginas; $i>0; $i--)
						<?php
							$style='';
							if($i == $pagina){
								$style=";background:#141E30; color:white;";
							}
						?>
						<button style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;{{$style}}" class="pagina" data-pagina="{{ $i }}">{{ $i }}</button>
					@endfor
				</div>
			</div>
			@else
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<small style="float:left;">
						Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $totAprendices }} registros
					</small>
					<?php
						$style='';
						if($cantidadPaginas == $pagina){
							$style=";background:#087b76; color:white;";
						}
						$cantidadInicia = 10;
						if($pagina >= 10){
							if($pagina == $cantidadPaginas){
								$cantidadInicia = $pagina;
							}else{
								$cantidadInicia = ($pagina+1);
							}
						}
					?>
					@if($pagina < ($cantidadPaginas-1))
						<button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;{{ $style }}" class="pagina" data-pagina="{{ $cantidadPaginas }}">{{ $cantidadPaginas }}</button>
						<button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button>
					@endif
					@for($i=10; $i>0; $i--)
						<?php
							$style='';
							if($cantidadInicia == $pagina){
								$style=";background:#087b76; color:white;";
							}
						?>
						<button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}" class="pagina" data-pagina="{{$cantidadInicia}}">{{ $cantidadInicia }}</button>
						<?php $cantidadInicia--; ?>
					@endfor
					@if($pagina >= 10)
						<button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button>
						<button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;" class="pagina" data-pagina="1">1</button> 
					@endif
				</div>
			</div>
		@endif
	@endif
	</div>				
@else
	<tr>
		<td colspan="8">No hay registros con el filtro seleccionado</td>
	</tr>
@endif