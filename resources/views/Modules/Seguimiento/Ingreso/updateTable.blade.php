@foreach($registros as $reg)
<tr>
	<td>{{ $reg->ing_est_documento }}</td>
	<td>{{ $reg->ing_est_tip_documento }}</td>
	<td>{{ $reg->ing_est_rol }}</td>
	<td>{{ $reg->ing_est_nombre }}</td>
	<td>{{ $reg->ing_det_fecha }}</td>
	<td>{{ $reg->ing_det_hor_ingreso }}</td>
<!--<td>//{{ $reg->ing_det_tem_ingreso }}</td>-->
	<td>{{ $reg->ing_det_hor_salida }}</td>
 <!--<td>//{{ $reg->ing_det_tem_salida }}</td>-->
</tr>
@endforeach