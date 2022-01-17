@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Horarios','Transversal - hora') !!}
	<div class="row">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Transversal - hora</span>
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
						@if(session()->get('mensaje') != null)
							@if(session()->get('mensaje') == 'yes')
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="alert alert-success">
										<strong>Actualizaci&oacute;n exitosa!</strong>
									</div>
								</div>
							@else
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="alert alert-info">
										<strong>Por favor seleccione las 3 columnas deben ser diligenciadas.</strong>
									</div>
								</div>
							@endif
						<?php session()->forget('mensaje');?>
						@endif
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<form method="POST">
								<table class="table table-bordered table-hover">
									<thead>
										<tr>
											<th style="border-bottom: 2px solid;">#</th>
											<th style="border-bottom: 2px solid;">Transversal</th>
											<th style="border-bottom: 2px solid;">Diseño curricular</th>
											<th style="border-bottom: 2px solid;">Operario - Hora(s)</th>
											<th style="border-bottom: 2px solid;">T&eacute;cnio - Hora(s)</th>
											<th style="border-bottom: 2px solid;">Tecn&oacute;logo - Hora(s)</th>
										</tr>
									</thead>
									<tbody>
									<?php $contador = 1; ?>
									@foreach($transversal as $tra)
										@foreach($disenoCurricular as $dis)
											<tr style="cursor: pointer;">
												<td style="vertical-align: middle;">{{ $contador++ }}</td>
												<td style="vertical-align: middle;">{{ $tra->tra_tip_descripcion }}</td>
												<td style="vertical-align: middle;">{{ $dis }}</td>
												<td style="vertical-align: middle;">
													<select name="1[{{ $tra->tra_tip_id }}][{{ $dis }}]" class="valor form-control">
														<option value="">No aplica</option>
														@if(isset($horasAsignadas[1][$tra->tra_tip_id][$dis]))
															@for($i=1; $i<=5; $i++)
															<?php 
																$selected = '';
																if($horasAsignadas[1][$tra->tra_tip_id][$dis] == $i){
																	$selected = 'selected';
																}
															?>
															<option <?php echo $selected; ?> value="{{ $i }}">{{ $i }}</option>
															@endfor
														@else
															@for($i=1; $i<=5; $i++)
															<option value="{{ $i }}">{{ $i }}</option>
															@endfor
														@endif
													</select>
												</td>
												<td style="vertical-align: middle;">
													<select name="2[{{ $tra->tra_tip_id }}][{{ $dis }}]" class="valor form-control">
														<option value="">No aplica</option>
														@if(isset($horasAsignadas[2][$tra->tra_tip_id][$dis]))
															@for($i=1; $i<=5; $i++)
															<?php 
																$selected = '';
																if($horasAsignadas[2][$tra->tra_tip_id][$dis] == $i){
																	$selected = 'selected';
																}
															?>
															<option <?php echo $selected; ?> value="{{ $i }}">{{ $i }}</option>
															@endfor
														@else
															@for($i=1; $i<=5; $i++)
															<option value="{{ $i }}">{{ $i }}</option>
															@endfor
														@endif
													</select>
												</td>
												<td style="vertical-align: middle;">
													<select name="4[{{ $tra->tra_tip_id }}][{{ $dis }}]" class="valor form-control">
														<option value="">No aplica</option>
														@if(isset($horasAsignadas[4][$tra->tra_tip_id][$dis]))
															@for($i=1; $i<=5; $i++)
															<?php 
																$selected = '';
																if($horasAsignadas[4][$tra->tra_tip_id][$dis] == $i){
																	$selected = 'selected';
																}
															?>
															<option <?php echo $selected; ?> value="{{ $i }}">{{ $i }}</option>
															@endfor
														@else
															@for($i=1; $i<=5; $i++)
															<option value="{{ $i }}">{{ $i }}</option>
															@endfor
														@endif
													</select>
												</td>
											</tr>
										@endforeach
									@endforeach
									</tbody>
								</table>
								<div class="text-center">
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<input class="btn btn-success" type="submit" value="Guardar cambios">
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('plugins-css')
<style>
.styleSelect{
	color: blue;
	font-weight: bold;
	border: 1px solid;
}
table tr th, table tr td{
	text-align: center;
	vertical-align: middle;
}
</style>
@endsection
@section('plugins-js')
<script type="text/javascript">
$(document).ready(function () {
	$(document).on('change','.valor',function(){
		var valor = $(this).val();
		console.log(valor);
		if(valor != ''){
			$(this).css({'background':'#309591', 'color':'white'});
		}else{
			$(this).css({'background':'white', 'color':'#555555'});
		}
	});
});
</script>
@endsection