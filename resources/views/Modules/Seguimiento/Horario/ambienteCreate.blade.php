@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Crear','Ambiente') !!}
	<div class="row">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Ambiente</span>
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
					<form method="POST">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
								<div class="col-lg-4 col-lg-push-2 col-md-4 col-md-push-2">
									<div class="col-lg-12">
										<label>Descripci&oacute;n:</label>
										<input class="form-control" type="text" name="pla_amb_descripcion" required>
									</div>	
									<div class="col-lg-12">
										<label>Tipo:</label>
										<select class="form-control" name="pla_amb_tipo" required>
											<option value="">Seleccione</option>
											<option value="Interno">Interno</option>
											<option value="Externo">Externo</option>
											<option value="Restriccion">Restricci&oacute;n</option>
										</select>
									</div>
								</div>
								<div class="col-lg-4 col-lg-push-2 col-md-4 col-md-push-2">
									<div class="col-lg-12">
										<label>Coordinador:</label>
										<select class="form-control" name="par_id_coordinador" required>
											<option value="">Seleccione</option>
										@foreach($coordinadores as $coordinador)
											<option value="{{ $coordinador->par_identificacion }}">{{ $coordinador->nombre_coordinador }}</option>
										@endforeach
										</select>
									</div>
									<div class="col-lg-12">
										<label>Suma horas al instructor?</label>
										<select class="form-control" name="pla_amb_suma_horas" required>
											<option value="">Seleccione</option>
											<option value="SI">SI</option>
											<option value="NO">NO</option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
								<div class="col-lg-4 col-lg-push-4 col-md-4 col-md-push-4">
									<a class="btn btn-danger btn-xs" href="{{ url('seguimiento/horario/ambiente') }}" style="margin:15px 5px 0px 0px">Cancelar</a>
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<button class="btn btn-success btn-xs"  style="margin:15px 0px 0px 5px">Registrar</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection