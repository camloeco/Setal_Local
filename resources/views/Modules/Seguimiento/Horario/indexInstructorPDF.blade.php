<?php date_default_timezone_set("America/Bogota"); ?>
<link href="{{ asset('devoops/plugins/bootstrap/bootstrap.css') }}" rel="stylesheet">
<style>
	table thead tr th{
		padding: 1px !important;
		font-size: 15px;
	}

	table tbody tr td{
		cursor:pointer;
		width: 180px !important;
		max-width: 180px !important;
		padding: 0px !important;
		font-size: 15px;
	}
	table thead tr th, table tbody tr td, .hora{
		border:1px solid black !important;
		text-align: center;
		vertical-align: middle !important;
		height: 38px !important;
		max-height: 38px !important;
	}

	.hora{
		cursor:pointer;
		width: 110px !important;
		max-width: 120px !important;
		padding: 0px !important;
		font-size: 12px;
		font-weight: bold;
	}
	p{
		font-size:12px;
		margin: 0px;
		vertical-align: middle !important;
	}
</style>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	@if(isset($programacion) and count($programacion)>0)
		@foreach($programacion as $key => $val)
		@foreach($val as $key1 => $val1)
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="height:792px;border:1px solid white;">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
				<h5 style="float: left;font-family: cursive;position: absolute;padding: 55px 0px 0px 10px;">SETALPRO</h5>
				<h4 style="text-align:center;"> {{ $val1["instructor"] }}</h4>
				<h5 style="text-align:center;">
					<h4 style="color:red;"><strong>Horas programadas:</strong> <?php echo (array_sum($val1["hTotales"])-$horasReales[$key][$key1]); ?></h4>
					<strong>Fecha inicio:</strong> {{ $key1 }} 
					<strong>Fecha fin:</strong> {{ $val1['fecha_fin'] }}  
				</h5>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">		
				<table class="table">
					<thead>
						<tr>
							<td class="hora" style="font-size:15px;border:">Hora</td>
							@for($i=0; $i<=5; $i++)
							<th>{{ $diaOrtografia[$i] }}</th>	
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
								<td class="hora">{{ $j }}:00 - {{ ($j+1) }}:00</td>
								@for($i=1; $i<7; $i++)
									@if($horas[$i]['can'] == $horas[$i]['total'])
										<?php
											$control = false;
										?>
										@foreach($val1['hInicio'] as $key2 => $val2)
											@if($val1['dia_id'][$key2] == $i and $val2 == $j)
												<?php 
													$ficha = $val1['ficha'][$key2];
													$programa = $val1['programa'][$key2];
													$ambiente = $val1['ambiente'][$key2];
													$horas_totales = $val1['hTotales'][$key2];
													$numero_trimestre = $val1['trimestre'][$key2];
													$control = true;	
												?>
											@endif
										@endforeach
										
										@if($control == true)
											<td rowspan="{{ $horas_totales }}">
												<p>
													<strong>{{ $ficha }}</strong><br>{{ $programa }}<br><strong>{{ $ambiente }}</strong>
												</p>
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
				<h5 style="float: left;font-family: cursive;">Fecha y hora de creación <?php echo date('Y/m/d h:i:s a');?></h5>
			</div>
		</div>
		@endforeach
		@endforeach
	@else
		@if(isset($par_identificacion))												
		<h5 class="text-center">No se encontraron registros del instructor en el trimestre seleccionado.</h5>																
		@endif													
	@endif
</div>
<script type="text/javascript">
  window.print();
</script>
				