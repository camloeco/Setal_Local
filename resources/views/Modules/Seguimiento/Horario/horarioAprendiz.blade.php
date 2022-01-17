@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Horarios','Ficha') !!}
	<div class="row">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Horarios por ficha</span>
					</div>
					<div class="box-icons">
						<a class="collapse-link">
							<i class="fa fa-chevron-up"></i>
						</a>
						<a class="expand-link">
							<i class="fa fa-expand"></i>
						</a>
						<a class="close-link">
							<i class="fa fa-times"></i>
						</a>
					</div>
					<div class="no-move"></div>
				</div>
				<div class="box-content">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							@if(isset($horarios) and count($horarios_detalle)>0)
							<form action="{{ url('seguimiento/horario/mieliminarxd') }}" id="formulario" data-url-agregar="{{ url('seguimiento/horario/contenidomodalagregar') }}" data-url-modificar="{{ url('seguimiento/horario/contenidomodalmodificar') }}" method="POST">
								<input name="_token" type="hidden" value="{{ csrf_token() }}">
									@foreach($horarios as $hor)
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
										<h3 style="margin:0px;"><label>Ficha:</label> {{ $hor->fic_numero }}</h3>
										<h5 style="margin:0px;"><label>Programa:</label> {{ ucwords(mb_strtolower($hor->prog_nombre)) }}</h5>
										<h5 style="margin:0px;">
											<label>Lectiva trimestre(s):</label> {{ $hor->pla_fic_can_trimestre }} &nbsp;&nbsp;
											<label>Nivel:</label> {{ $hor->niv_for_nombre }}
										</h5>
										<h5 style="margin:0px;">
											<label>Tipo oferta:</label> {{ $hor->pla_tip_ofe_descripcion }} &nbsp;&nbsp;
											<label>Franja horaria:</label> {{ $hor->pla_fra_descripcion }} &nbsp;&nbsp;
											
										</h5>
										<h5 style="margin:0px;">
											<label>Creado por:</label> {{ ucwords(mb_strtolower($hor->par_nombres)) }} {{ ucwords(mb_strtolower($hor->par_apellidos)) }} &nbsp;&nbsp;
											<label>Fecha creaci&oacute;n:</label> {{ $hor->pla_fic_fec_creacion }}
										</h5>
									</div>
										@if(isset($programacion[$hor->pla_fic_id]))
											<?php $cantidad_trimestres = $hor->pla_fic_can_trimestre; ?>
											@for($l=1; $l<=$cantidad_trimestres; $l++)
												@if(isset($programacion[$hor->pla_fic_id][$l]))
													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="padding: 0px 0px 10px 0px;">
														<h3>Trimestre # {{ $l }}</h3>
														<h5 style="margin:0px;">
															<strong>Ficha</strong>: {{ $hor->fic_numero }}<br>
															<strong>Fecha inicio:</strong> {{ $programacion[$hor->pla_fic_id][$l]["fechas_inicio"] }} 
															<strong>Fecha fin:</strong> {{ $programacion[$hor->pla_fic_id][$l]["fechas_fin"] }}
														</h5>
													</div>
													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
														<table data-plaFicId="{{ $hor->pla_fic_id }}"" data-ficha="{{ $hor->fic_numero }}" data-url="{{ url('seguimiento/horario/contenidomodal') }}" data-trimestre="{{ $l }}" class="table table-bordered table-hover">
															<thead>
																<tr>
																	<th style="text-align: center; max-width: 110px;">Hora</th>
																	@for($i=0; $i<=5; $i++)
																	<th style="text-align: center; max-width: 110px;">{{ $diaOrtografia[$i] }}</th>	
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
																		<th style='width:114px;cursor:pointer;text-align:center;'> {{ $j.":00 - ".($j+1).":00" }} </th>
																		@for($i=1; $i<7; $i++)
																			@if($horas[$i]['can'] == $horas[$i]['total'])
																				<?php $control = false; ?>
																				@foreach($programacion[$hor->pla_fic_id][$l]["dia_id"] as $key1 => $val1)
																					<?php
																					$horaInicio = $programacion[$hor->pla_fic_id][$l]["hora_inicio"][$key1];
																					if($val1 == $i and $horaInicio == $j){
																						$control = true;
																						$instructor_cedula = $programacion[$hor->pla_fic_id][$l]["instructor_cedula"][$key1];
																						$instructor_nombre = $programacion[$hor->pla_fic_id][$l]["instructor_nombre"][$key1];
																						$ambiente = $programacion[$hor->pla_fic_id][$l]["amb_descripcion"][$key1];
																						$rowspan = $programacion[$hor->pla_fic_id][$l]["horas_totales"][$key1];
																						break;
																					}
																					?>
																				@endforeach
		
																				@if($control)	
																					<td data-tipo="ficha" data-cc="{{ $instructor_cedula }}"rowspan="{{ $rowspan }}" class="actividad" style="cursor:pointer; max-width: 110px;"> 
																						<h6>{{ $instructor_nombre }}</h6>
																						<h6><strong>{{ $ambiente }}</strong></h6>
																					</td>
																					<?php	$horas[$i]['can'] -= $rowspan; ?>
																				@else 
																					<td> </td>
																					<?php	$horas[$i]['can'] --; ?>
																				@endif
																			@endif
																			<?php $horas[$i]['total']--; ?>
																		@endfor
																	</tr>
																@endfor
															</tbody>
														</table>
													</div>
												@endif
											@endfor
										@else
										<div class="col-lg-12 text-center">
											<div class="alert alert-success">
												<h4 class="text-center">La ficha seleccionada no ha iniciado o ya termino su etapa lectiva.</h4>	
											</div>	
										</div>								
										@endif
									@endforeach
							</form>
							@else
								@if(isset($pla_fic_id))												
								<h5 class="text-center">No se encontraron registros de la(s) ficha(s) o el trimestre seleccionado</h5>																
								@endif													
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection