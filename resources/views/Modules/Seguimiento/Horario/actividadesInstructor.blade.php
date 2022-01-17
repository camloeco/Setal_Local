
@if(count($actividades)>0)
	@foreach($actividades as $act)
		<tr>
			<td style="font-size:12px;text-align:center;vertical-align:middle;">{{ $fase[$act->fas_id] }}</td>
			<td style="font-size:12px;"><?php echo $act->pla_fic_act_competencia; ?></td>
			<td style="font-size:12px;"><?php echo $act->pla_fic_act_resultado ?></td>
			<td style="font-size:12px;"><?php echo $act->pla_fic_act_actividad ?></td>
			<td style="font-size:12px;text-align:center;vertical-align:middle;">{{ $act->pla_fic_act_horas }}</td>
		</tr>
	@endforeach
@else
	<tr>
		<td style="font-size:12px;text-align:center;" colspan="6">El instructor no tiene actividades registradas.</td>
	</tr>
@endif