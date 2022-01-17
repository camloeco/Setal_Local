@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Reporte','Inasistencia') !!}
<div class="row">
	<div class="col-xs-12 col-sm-12">
		<div class="box ui-draggable ui-droppable">
			<div class="box-header">
				<div class="box-name ui-draggable-handle">
					<span>Inasistencia</span>
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
						<form method="POST">

							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="margin-top: 5px;">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="col-lg-4 col-lg-push-4 col-md-6 col-md-push-3 col-sm-12 col-xs-12">
										<label style="margin:0px;">Trimestre: </label><br>
										<small>Año - N&uacute;mero trimestre - Fecha inicio - Fecha fin</small>
										<select class="form-control" name="pla_fec_tri_id" required id="pla_fec_tri_id">
											<option value = "">Seleccione...</option>	
											@if(isset($pla_fec_tri_id))
												@foreach($trimestres as $val)
													<?php $selected = ''; ?>
													@if($pla_fec_tri_id == $val->pla_fec_tri_id)	
													<?php $selected = 'selected'; ?>
													@endif
													<option <?php echo $selected; ?> value="{{ $val->pla_fec_tri_id }}">{{ $val->pla_fec_tri_year }} - {{ $val->pla_fec_tri_trimestre }} - {{ $val->pla_fec_tri_fec_inicio }} - {{ $val->pla_fec_tri_fec_fin }}</option>
												@endforeach
											@else	
												@foreach($trimestres as $val)		
													<option value="{{ $val->pla_fec_tri_id }}">{{ $val->pla_fec_tri_year }} - {{ $val->pla_fec_tri_trimestre }} - {{ $val->pla_fec_tri_fec_inicio }} - {{ $val->pla_fec_tri_fec_fin }}</option>
												@endforeach
											@endif
										</select>
									</div>
								</div>
								<div style="margin-top:15px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<input name="_token" type="hidden" value="{{ csrf_token() }}">
									<button><i class="fa fa-2x fa-download"></i></button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection