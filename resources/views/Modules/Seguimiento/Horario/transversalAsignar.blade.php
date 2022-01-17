@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Horarios','Transversal - asignar') !!}
	<div class="row">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Transversal - asignar</span>
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
								<table class="table table-bordered table-striped">
									<thead>
										<tr>
											<th style="border-bottom: 2px solid;">#</th>
											<th style="border-bottom: 2px solid; width: 160px;">Transversal</th>
											<th style="border-bottom: 2px solid;">Ambiente permitido</th>
											<th style="border-bottom: 2px solid;">Instructor permitido</th>
										</tr>
									</thead>
									<tbody>
									<?php $contador = 1; $imprimir = true; ?>
									@foreach($transversal as $tra)
										<tr>
											<td style="vertical-align: middle;">{{ $contador++ }}</td>
											<td style="vertical-align: middle;font-size: 14px;">{{ $tra->tra_tip_descripcion }}</td>
											<td class="styleSelect">
												<select class="js-example-basic-multiple obligatorio ambiente" name="pla_amb_id[{{ $tra->tra_tip_id }}][]" multiple="multiple">
												@foreach($ambiente as $id => $amb)
													<?php $selected = ''; ?>
													@if(isset($transversalAmbiente[$tra->tra_tip_id]))
														@if(in_array($amb->pla_amb_id, $transversalAmbiente[$tra->tra_tip_id]))
														<?php $selected = 'selected'; ?>
														@endif
													@endif
													<option <?php echo $selected; ?> value="{{ $amb->pla_amb_id }}">{{ $amb->pla_amb_descripcion }}</option>
												@endforeach
												</select>
											</td>
											<td class="styleSelect">
												<select class="js-example-basic-multiple obligatorio instructor" name="par_identificacion[{{ $tra->tra_tip_id }}][]" multiple="multiple">
												@foreach($instructor as $id => $ins)
													<?php $selected = ''; ?>
													@if(isset($transversalInstructor[$tra->tra_tip_id]))
														@if(in_array($ins->par_identificacion, $transversalInstructor[$tra->tra_tip_id]))
														<?php $selected = 'selected'; ?>
														@endif
													@endif
													<option <?php echo $selected;?> value="{{ $ins->par_identificacion }}">{{ $ins->par_nombres }} {{ $ins->par_apellidos }}</option>
												@endforeach
												</select>
											</td>
										</tr>
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