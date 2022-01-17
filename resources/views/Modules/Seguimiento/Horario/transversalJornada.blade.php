@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Horarios','Transversal - jornada') !!}
	<div class="row">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Transversal - jornada</span>
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
											<th style="border-bottom: 2px solid; width: 160px;">Jornada</th>
											<th style="border-bottom: 2px solid; width: 160px;">Inicio - fin</th>
											<th style="border-bottom: 2px solid;">Instructor</th>
										</tr>
									</thead>
									<tbody>
									<?php $contador = 1; $imprimir = true; ?>
									@foreach($jornadas as $jornada => $valor)
									<tr>
										<td>{{ $contador++ }}</td>
										<td>{{ $jornada }}</td>
										<td>{{ $jornadas[$jornada]['inicio'] }} - {{ $jornadas[$jornada]['fin'] }}</td>
										<td class="styleSelect">
											<select class="js-example-basic-multiple obligatorio instructor" name="par_identificacion[{{ $jornada }}][]" multiple="multiple">
											@foreach($instructor as $id => $ins)
												<?php $selected = ''; ?>
												@if(isset($jornadas_seleccionadas[$jornada]))
													@if(in_array($ins->par_identificacion, $jornadas_seleccionadas[$jornada]))
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
@section('plugins-js')
<script type="text/javascript">
	$(document).ready(function () {
		$(document).on('keyup','.form-control', function(){
			console.log('Estoy funcionando');
			var elemento = $(this).parent().parent();
			var competencia = elemento.find('.competencia').val();
			var resultado = elemento.find('.resultado').val();
			var actividad = elemento.find('.actividad').val();
			var hora = elemento.find('.hora').val();

			if(competencia != '' || resultado != '' || actividad != '' || hora != ''){
				elemento.find('.competencia').attr('required', true);
				elemento.find('.resultado').attr('required', true);
				elemento.find('.actividad').attr('required', true);
				elemento.find('.hora').attr('required', true);
			}else{
				elemento.find('.competencia').removeAttr('required', false);
				elemento.find('.resultado').removeAttr('required', false);
				elemento.find('.actividad').removeAttr('required', false);
				elemento.find('.hora').removeAttr('required', false);
			}

			console.log(competencia);
		});

		$(document).on('click','.duplicar', function(){
			var fila = $(this).parent().parent().html();
			contador = $(this).parent().parent().parent().find('tr').size();
			contador++;
			$(this).parent().parent().parent().append('<tr>'+fila+'</tr>');
			//$('tr').last().remove('');
			$(this).parent().parent().parent().find( "tr" ).last().find('.form-control').html('');
			$(this).parent().parent().parent().find( "tr" ).last().find('.form-control').val('');
			$(this).parent().parent().parent().find( "tr" ).last().find('.numero').html(contador);
		});

		$(document).on('click','.eliminar', function(){
			contador = $(this).parent().parent().parent().find('tr').size();
			if(contador > 1){
				$(this).parent().parent().remove();
			}else{
				alert('No se puede eliminar la única fila');
			}
		});
	});
</script>
@endsection
