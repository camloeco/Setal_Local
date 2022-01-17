@extends('templates.devoops')

@section('content')
	{!! getHeaderMod('Seguimiento a proyectos','Creaci&oacute;n de plantilla') !!}
	<div class="row">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Crear plantilla</span>
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
					<form action="{{ url('seguimiento/plantilla/create') }}" method="post">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="col-lg-5 col-md-5">
									<label>Programa</label>
									<select required name="prog_codigo" class="form-control">
										<option value="">Seleccione...</option>
										@foreach($programas as $pro)
											<option value="{{ $pro->prog_codigo }}">{{ $pro->prog_nombre }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<hr>
							</div>
							<div id="contenedorFila">	
								<div id="fila" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 fila">
									<div class="col-lg-3 col-md-3">
										<label>Fase</label>
										<select required name="fas_id[]" class="form-control">
											<option value="">Seleccione...</option>
											<option value="1">An&aacute;lisis</option>
											<option value="2">Planeac&oacute;n</option>
											<option value="3">Ejecuci&oacute;n</option>
											<option value="4">Evaluaci&oacute;n</option>
										</select>
									</div>
									<div class="col-lg-3 col-md-3">
										<label>Competencia</label>
										<select required name="com_codigo[]" class="form-control">
											<option value="">Seleccione...</option>
											@foreach($competencias as $com)
												<option value="{{ $com->com_codigo }}">{{ $com->com_nombre }}</option>
											@endforeach
										</select>
									</div>
									<div class="col-lg-3 col-md-3">
										<label>Actividad</label>
										<select required name="act_id[]" class="form-control">
											<option value="">Seleccione...</option>
											@foreach($resultados as $res)
												<option value="{{ $res->res_id }}">{{ $res->res_nombre }}</option>
											@endforeach
										</select>
									</div>
									<div class="col-lg-3 col-md-3">
										<label>Resultado</label>
										<select required name="res_id[]" class="form-control">
											<option value="">Seleccione...</option>
											@foreach($resultados as $res)
												<option value="{{ $res->res_id }}">{{ $res->res_nombre }}</option>
											@endforeach
										</select>
									</div>
									<div class="col-lg-3 col-md-3">
										<label>Cantidad horas</label>
										<input required name="pla_can_hor_total[]" class="form-control" type="number">
									</div>
									<div class="col-lg-3 col-md-3">
										<label>Presencial</label>
										<input required name="pla_can_hor_presenciales[]" class="form-control" type="number">
									</div>
									<div class="col-lg-3 col-md-3">
										<label>Autonoma</label>
										<input required name="pla_can_hor_autonomas[]" class="form-control" type="number">
									</div>
									<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
										<label>Acciones</label></br>
										<a class="agregarFila btn btn-success btn-xs">Añadir fila</a>
										<a class="eliminarFila btn btn-danger btn-xs">Eliminar fila</a>
									</div>
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<hr style="background: black;height: 1px;">
									</div>
								</div>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<input class="form-control btn btn-success" type="submit" value="Guardar">
								<a href="{{ url('seguimiento/plantilla/index') }}" class="form-control btn btn-danger" type="submit">Cancelar</a>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection