@if(count($fichas)>0)
	<?php 	$contador = 0; ?>
	@foreach($fichas as $ficha) 
		<tr>						  
			<td><?php echo ++$contador; ?></td>  
			<td>{{ $ficha->fic_numero }}</td>  
			<td>{{ $ficha->niv_for_nombre }}</td>  
			<td>{{ $ficha->prog_nombre }}</td>
			<td>{{ $ficha->fic_fecha_inicio }}</td>
			<td>
			<?php
				if($ficha->niv_for_id == 1){
					echo date( "m/d/Y", strtotime( "".$ficha->fic_fecha_inicio." +91 days" ) );
				}else if($ficha->niv_for_id == 2){
					echo date( "m/d/Y", strtotime( "".$ficha->fic_fecha_inicio." +182 days" ) );
				}else if($ficha->niv_for_id == 4){
					echo date( "m/d/Y", strtotime( "".$ficha->fic_fecha_inicio." +547 days" ) );
				}
			?>
			</td>
			<td>{{ $ficha->fic_fecha_fin }}</td>
			<td>{{ $ficha->fecha_terminacion }}</td>
			<td>{{ $ficha->fic_localizacion }}</td>
			<td>{{ $ficha->nombre }}</td>
			<td><a id="ventanaModal" data-url="{{ url('seguimiento/reportes/aprendicesficha')}}" data-ficha="{{ $ficha->fic_numero }}" data-toggle="modal" data-target="#myModal" class=" btn btn-primary btn-sm">Ver</a></td>
		</tr>
	@endforeach
@else
	<tr>
		<td colspan = "11" style="text-align:center;">No se encontraron resultados en la BD</td>
	</tr>
@endif
