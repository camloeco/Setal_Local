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
	@if(isset($horarios))
		@foreach($horarios as $hor)
			@if(isset($programacion[$hor->pla_fic_id]))
				@foreach($programacion[$hor->pla_fic_id] as $key => $pro)
					@foreach($pro["fechas_inicio"] as $key1 => $pro1)
						<?php //echo '<pre>'; print_r($programacion[$hor->pla_fic_id][$key]['hora_inicio']); ?>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-center" style="margin-bottom:4px;margin-top:4px;">
							<h3 style="margin:0px;">
								<label>Ficha:</label> {{ $hor->fic_numero }}
								<label>{{ $hor->prog_sigla }}</label>
							</h3>
							<h5 style="margin:0px;">
							     <label>Modalidad:</label> {{$hor->Modalidad}}
							</h5>
							<h5 style="margin:0px;">
								<label>Lectiva trimestre(s):</label> {{ $hor->pla_fic_can_trimestre }} &nbsp;&nbsp;
                                @if($hor->pla_fic_consecutivo_ficha == "")
					            <label>Consecutivo:</label> 0&nbsp;&nbsp;
                                @else
					            <label>Consecutivo:</label>{{ $hor->pla_fic_consecutivo_ficha }}&nbsp;&nbsp;
                                @endif
							</h5>
							<h5 style="margin:0px;">
								<label>Tipo oferta:</label> {{ $hor->pla_tip_ofe_descripcion }} &nbsp;&nbsp;
								<label>Nivel:</label> {{ $hor->niv_for_nombre }}
							</h5>
							<h5 style="margin:0px;">
								<label>Programa:</label> {{ ucwords(mb_strtolower($hor->prog_nombre)) }}
							</h5>
							<h5 style="margin:0px;">
								<label>Fecha y hora de impresi&oacute;n:</label> <?php echo date('Y/m/d h:i:s a');?>
							</h5>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-center" style="padding-bottom: 5px;">
							<h5>
								<strong style="font-size:24px;">Trimestre ficha # {{ $key }}</strong><br>
								<strong>Fecha inicio:</strong> {{ $pro1 }}
								<strong>Fecha fin:</strong> {{ $pro['fechas_fin'][$key1] }}
							</h5>
						</div>
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
							<table class="table">
								<thead>
									<tr>
										<th>Hora</th>
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
									@if(isset($programacion[$hor->pla_fic_id][$key]['hora_inicio']))
										@for($j=6; $j<22; $j++)
											<tr>
												<td class="hora"> {{ $j.":00 - ".($j+1).":00" }} </td>
												@for($i=1; $i<7; $i++)
													@if($horas[$i]['can'] == $horas[$i]['total'])
														<?php $control = false; ?>
                                                        <?php /* if(isset($programacion[$hor->pla_fic_id][$key]["dia_id"][$key1])){ */?>
														  @foreach($programacion[$hor->pla_fic_id][$key]["dia_id"][$key1] as $key2 => $val2)
														    	<?php
														    	$horaInicio = $programacion[$hor->pla_fic_id][$key]["hora_inicio"][$key1][$key2];
															    if($val2 == $i and $horaInicio == $j){
																    $control = true;
															    	$instructor_cedula = $programacion[$hor->pla_fic_id][$key]["instructor_cedula"][$key1][$key2];
																    $instructor_nombre = $programacion[$hor->pla_fic_id][$key]["instructor_nombre"][$key1][$key2];
																    $ambiente = $programacion[$hor->pla_fic_id][$key]["pla_amb_descripcion"][$key1][$key2];
																    $rowspan = $programacion[$hor->pla_fic_id][$key]["horas_totales"][$key1][$key2];
																    break;
															    }
														    	?>
														  @endforeach
														<?php /*}else{
                                                                    echo "<h2>Esto no existe $hor->pla_fic_id en la posición $key y $key1</h2>";
												        } */?>
														@if($control)
															<td rowspan="{{ $rowspan }}">
																<h6>{{ $instructor_nombre }}</h6>
																<h6>
																	<strong>{{ $ambiente }}</strong>
																</h6>
															</td>
															<?php $horas[$i]['can'] -= $rowspan; ?>
														@else
															<td></td>
															<?php	$horas[$i]['can'] --; ?>
														@endif
													@endif
													<?php $horas[$i]['total']--; ?>
												@endfor
											</tr>
										@endfor
									@else
										@for($j=6; $j<22; $j++)
											<tr>
												<td class="hora">{{ $j.":00 - ".($j+1).":00" }}</td>
												@for($i=1; $i<7; $i++)
													<td></td>
												@endfor
											</tr>
										@endfor
									@endif
								</tbody>
							</table>
						</div>
					@endforeach
				@endforeach
			@endif
		@endforeach
		<script type="text/javascript">
			window.print();
		</script>
	@else
		@if(isset($pla_fic_id))
			<h5 class="text-center">No se encontraron registros de la(s) ficha(s) en el trimestre seleccionado</h5>
		@endif
	@endif
</div>