<?php $contador = 0; ?>
@if($sqlFicha)
	@foreach($sqlFicha as $ficha) 
	<tr>						  
		<td><?php echo ++$contador; ?></td>  
		<td>{{ $ficha->fic_numero }}</td>  
		<td>{{ $ficha->niv_for_nombre }}</td>  
		<td>{{ $ficha->prog_nombre }}</td>
		<td>{{ $ficha->fic_fecha_inicio }}</td>
		<td>{{ $ficha->fic_fecha_fin }}</td>
		<td>{{ $ficha->fecha_terminacion }}</td>
		<td>{{ $ficha->fic_localizacion }}</td>
		<td>{{ $ficha->nombre }}</td>
		<td><a id="ventanaModal" data-url="{{ url('seguimiento/reportes/aprendicesficha')}}" data-ficha="{{ $ficha->fic_numero }}" data-toggle="modal" data-target="#myModal" class=" btn btn-primary btn-sm">Ver</a></td>
	</tr>
	@endforeach
@else
	<tr>						  
		<td colspan="10"><h3>El registro que intenta buscar no existe</h3></td>  
	</tr>
@endif