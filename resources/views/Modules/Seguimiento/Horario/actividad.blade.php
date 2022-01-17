@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Listar','Actividades') !!}
	<div class="row">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Actividades</span>
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
					<form action="{{ url('seguimiento/horario/actividadinstructor') }}" method='POST'>
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="row">
									@if(session()->get('exito') != null)
									<div style="margin: -15px 0px 0px 15px;position:absolute;" class="alert alert-success">
										Cambios realizados exitosamente.
									</div>
									<?php session()->forget('exito'); ?>
									@endif
									<div class="col-lg-2 col-lg-push-10 col-md-3 col-md-push-9 col-sm-4 col-sm-push-8 col-xs-4 col-xs-push-8">
								
										<input value="Guardar cambios" class="form-control btn btn-success" type="submit">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
								<table class="table table-bordered table-hover" style="font-size:10.5px;">
									<thead>
										<tr>
											<th class="text-center">Ficha</th>
											<th class="text-center">Programa</th>
											<th class="text-center">Resultado</th>
											<th class="text-center">Actividad</th>
											<th style="min-width:145px;" class="text-center">Herramienta</th>
											<th style="min-width:140px;" class="text-center">Otra herramienta</th>
											<th style="min-width:140px;" class="text-center">Explicaci&oacute;n</th>
											<th style="min-width:140px;" class="text-center">Observaciones</th>
										</tr>
									</thead>
									<tbody>
										@foreach($fichas as $fic)
										<tr>
											<td class="text-center">{{ $fic->fic_numero }}</td>
											<td class="text-center">{{ $fic->prog_nombre }}</td>
											<td class="text-center">{{ $fic->resultado }}</td>
											<td class="text-center">{{ $fic->actividad }}</td>
											<td class="text-center">
												<select required name="herramienta[]" class="herramienta form-control" data-id="{{ $fic->act_ins_id }}">
													<option value="">Seleccione...</option>
													@foreach($herramienta as $val)
													<?php
													$selected = '';
													$id_herramienta = $actividadesCargadas[$fic->act_ins_id]['id_herramienta'];
													if($id_herramienta == $val->act_her_id){
														$selected = 'selected';
													}
													?>
													<option <?php echo $selected; ?> value="{{ $val->act_her_id }}">{{ $val->act_her_descripcion}}</option>
													@endforeach
												</select>
											</td>
											<td class="text-center">
												<?php
													$style = 'display:none;';
													$required = '';
													$validar = $actividadesCargadas[$fic->act_ins_id]['otra_herramienta'];
													if($id_herramienta == 4){
														$style = '';
														$required = 'required';
													}
												?>
												<textarea maxlength="100" <?php echo $required; ?> rows="5" name="otraHerramienta[]" style="<?php echo $style; ?>" class="form-control otraHerramienta r1">{{ $validar }}</textarea>
												<small  class="otraHerramienta" style="<?php echo $style; ?>">(limite 100 caracteres)</small>
											</td>
											<td class="text-center">
												<?php
													
													$style = 'display:none;';
													$required = '';
													$validar = $actividadesCargadas[$fic->act_ins_id]['explicacion'];
													if($id_herramienta == 5){
														$style = '';
														$required = 'required';
													}
												?>
												<textarea maxlength="500" <?php echo $required; ?> rows="5" name="explicacion[]" style="<?php echo $style; ?>" class="form-control explicacion r2">{{ $validar }}</textarea>
												<small class="explicacion" style="<?php echo $style; ?>">(limite 500 caracteres)</small>
											</td>
											<td class="text-center">
												<textarea maxlength="500" rows="5" name="observaciones[]" class="form-control">{{ $actividadesCargadas[$fic->act_ins_id]['observaciones'] }}</textarea>
												<small>(limite 500 caracteres)</small>
												<input type="hidden" name="act_ins_id[]" value="{{ $fic->act_ins_id }}">
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="row">
									<div class="col-lg-2 col-lg-push-10 col-md-3 col-md-push-9 col-sm-4 col-sm-push-8 col-xs-4 col-xs-push-8">
										<input value="Guardar cambios" class="form-control btn btn-success" type="submit">
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div id="modalAmbiente" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Modificar ambiente</h4>
				</div>
				<form id="formularioModalAmbiente" data-url="{{ url('seguimiento/horario/ambientemodalmodificar') }}">
					<div class="modal-body" id="contenidoModal">
						<p>Some text in the modal.</p>
					</div>
				</form>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				</div>
			</div>

		</div>
	</div>
@endsection