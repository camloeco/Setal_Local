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
								<div class="col-lg-6 col-lg-push-3 col-md-8 col-md-push-2 col-sm-12 col-xs-12">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<label style="margin:0px;">Fecha inicio: </label><br>
										<input class="form-control" type="date" name="fecha_inicio" min="2020-04-13" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>">
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<label style="margin:0px;">Fecha fin: </label><br>
										<input class="form-control" type="date" name="fecha_fin" min="2020-04-13" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>">
									</div>
								</div>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="margin-top: 5px;">
								<div style="font-size:12px;" class="col-lg-6 col-lg-push-3 col-md-8 col-md-push-2 col-sm-12 col-xs-12">
									<label style="font-size:14px;">Seleccione ficha(s): </label><br>
									<select class="js-example-basic-multiple" id="pla_fic_id" name="pla_fic_id[]" multiple="multiple" required>
										@if(isset($pla_fic_id))
											@foreach($fichas as $val)
												<?php $selected = ''; ?>
												@if(in_array($val->pla_fic_id, $pla_fic_id))
												<?php $selected = 'selected'; ?>
												@endif
												<option <?php echo $selected; ?> value="{{ $val->pla_fic_id }}">{{ $val->fic_numero }} - {{ $val->prog_nombre }} - {{ $val->pla_fra_descripcion }}</option>
											@endforeach
										@else
											@foreach($fichas as $val)
												<option value="{{ $val->pla_fic_id }}">{{ $val->fic_numero }} - {{ $val->prog_nombre }} - {{ $val->pla_fra_descripcion }}</option>
											@endforeach
										@endif
									</select>
								</div>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="margin-top: 5px;">
								<div style="margin-top:15px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<input name="_token" type="hidden" value="{{ csrf_token() }}">
									<button>Descargar</button>
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