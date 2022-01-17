<?php date_default_timezone_set("America/Bogota"); ?>
<link href="{{ asset('devoops/plugins/bootstrap/bootstrap.css') }}" rel="stylesheet">
<style>
	table thead tr th{
		padding: 1px !important;font-size: 15px;
	}
	table tbody tr td{
		cursor:pointer;width: 180px !important;
		max-width: 180px !important;padding: 0px !important;
		font-size: 15px;
	}
	table thead tr th, table tbody tr td, .hora{
		border:1px solid black !important;text-align: center;
		vertical-align: middle !important;height: 38px !important;
		max-height: 38px !important;
	}
	.hora{
		cursor:pointer;width: 110px !important;
		max-width: 120px !important;padding: 0px !important;
		font-size: 12px;font-weight: bold;
	}
	p{
		font-size:12px;margin: 0px;
		vertical-align: middle !important;
	}
</style>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	@if(isset($programacion) and count($programacion)>0)
		@foreach($programacion as $key => $val)
			@foreach($val as $key1 => $val1)
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
					<h5 style="font-weight: bold;margin:0px;">{{ $val1["ambiente"] }}</h5>
					<h6 style="text-align:center;margin:0px 0px 2px 0px;">
						<strong>Fecha inicio:</strong> {{ $key1 }}
						<strong>Fecha fin:</strong> {{ $programacion[$key][$key1]['fecha_fin'] }}
					</h6>
				</div>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="padding: 0px 75px;">
					<table style="margin-bottom:15px;" data-ambiente="{{ $val1['ambiente'] }}" data-fecha-inicio="{{ $key1 }}" data-fecha-fin="{{ $programacion[$key][$key1]['fecha_fin'] }}" class="table table-bordered table-hover">
						<thead>
							<tr>
								<th style="width:78px;padding:2px;font-size:12px;text-align: center;">Hora</th>
								@for($i=0; $i<=5; $i++)
								<th style="min-width:90px;padding:2px;font-size:12px;text-align: center;">{{ $diaOrtografia[$i] }}</th>	
								@endfor
							</tr>
						</thead>
						<tbody>
							<?php
							for($h=1; $h<7; $h++){
								$horas[$h]['can'] = 16;
								$horas[$h]['total'] = 16;
							}
							?>
							@for($j=6; $j<22; $j++)
								<tr>
									<th style='padding:5px;font-size:11px;text-align:center;'>{{ $j }}:00 - {{ ($j+1) }}:00</th>
									@for($i=1; $i<7; $i++)
										@if($horas[$i]['can'] == $horas[$i]['total'])
											<?php	$control = false; ?>
											@foreach($val1['hora_inicio'] as $key2 => $val2)
												@if($val1['dia_id'][$key2] == $i and $val2 == $j)
													<?php
														$ficha = $val1['ficha'][$key2];
														$programa = $val1['programa'][$key2];
														$sigla = $val1['prog_sigla'][$key2];
														$consecutivo = $val1['consecutivo_ficha'][$key2];
														$horas_totales = $val1['horas_totales'][$key2];
														$numero_trimestre = $val1['trimestre'][$key2];
														$pla_fic_id = $val1['pla_fic_id'][$key2];
														$instructor = $val1['instructor'][$key2];
														$instructor_cc = $val1['instructor_cc'][$key2];
														$pla_fic_id = $val1['pla_fic_id'][$key2];
														$fecha_inicio_actividad = $val1['fecha_inicio_actividad'][$key2];
														$control = true;
													?>
												@endif
											@endforeach
											
											@if($control == true)
												<td fecha_inicio_actividad="{{ $fecha_inicio_actividad }}" title="{{ $programa }}" data-pla-fic-id = "{{ $pla_fic_id }}" data-cc="{{ $instructor_cc }}" data-trimestre="{{ $numero_trimestre }}" data-ficha="{{ $ficha }}" data-programa="{{ $programa }}" rowspan="{{ $horas_totales }}" class="text-center actividadAmbiente" style="padding: 0px;vertical-align: middle;cursor:pointer;">
													<h6 style="margin:2px 0px 0px 0px;font-size:11px;font-weight: bold;"><strong style="font-size:9px;">{{ $sigla }} {{ $consecutivo }}</strong> {{ $ficha }}</h6>
													<h6 style="margin:1px 0px 2px 0px;font-size:11px;">
														{{ $instructor }}<br>{{ $instructor_cc }}
													</h6>
												</td>
												<?php $horas[$i]['can'] -= $horas_totales; ?>
											@else
												<td></td>
												<?php $horas[$i]['can']--; ?>
											@endif
										@endif
										<?php $horas[$i]['total']--; ?>
									@endfor
								</tr>
							@endfor
						</tbody>
					</table>
				</div>
			@endforeach
		@endforeach
	@else
		@if(isset($pla_amb_id))
		<h5 class="text-center">No se encontraron registros del ambiente en el trimestre seleccionado.</h5>
		@endif
	@endif
</div>
<script type="text/javascript">
  window.print();
</script>
				