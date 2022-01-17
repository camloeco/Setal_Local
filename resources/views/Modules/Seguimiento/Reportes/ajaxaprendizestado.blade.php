
@if(count($aprendices)>0)
<?php $contador = 0?>
@foreach($aprendices AS $apr)
	<tr>
		<td><?php echo ++$contador;?></td>
		<td>{{ $apr->par_identificacion }}</td>
		<td>{{ $apr->par_nombres }}</td>
		<td>{{ $apr->par_apellidos }}</td>
		<td>{{ $apr->par_telefono }}</td>
		<td>{{ $apr->par_correo }}</td>
		<td>{{ $apr->est_descripcion }}</td>
	</tr>
@endforeach
@else
	<tr>
		<td colspan="7"><h4>No hay registros</h4></td>		
	</tr>
@endif