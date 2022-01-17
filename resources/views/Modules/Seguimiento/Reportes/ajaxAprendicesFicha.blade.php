
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">&times;</button>
	<h4 class="modal-title">
		Aprendices de la ficha: <code>{{$ficha}}</code></br>
		Programa de formaci&oacute;n: <code>{{$programa[0]->prog_nombre}}</code></br>
		Total aprendices matriculados: <code>{{$totalAprendices[0]->total}}</code></br>
	</h4>
</div>
<div class="modal-body">
	<div class="table-responsive" style="overflow-x:auto">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>N&uacute;mero identicaci&oacute;n</th>
					<th>Nombre(s)</th>
					<th>Apellido(s)</th>
					<th>Tel&eacute;fono</th>
					<th>Correo electr&oacute;nico</th>
					<th>Estado</th>
					<th>Alternativa</th>
				</tr>
			</thead>
			<tbody id="res">
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
					<td>
					<?php
					if(array_key_exists($apr->par_identificacion,$arrOpcEtapa)){
						echo $arrOpcEtapa[$apr->par_identificacion];
					}else{
						echo "Sin alternativa";
					}
					?>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>

