@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Horarios','Transversal') !!}
	<div class="row" id="urls" data-token="{{ csrf_token() }}" data-cambio-input = "{{ url('seguimiento/horario/modificarnumeroprograma') }}" data-cambio-select = "{{ url('seguimiento/horario/modificartipooferta') }}">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Transversal</span>
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
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="alert alert-success">
								<strong>Actualizaci&oacute;n exitosa!</strong>
							</div>
						</div>
						<?php session()->forget('mensaje');?>
						@endif
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<form method="POST">
								<table class="table table-bordered table-striped">
									<thead>
										<tr>
											<th style="border-bottom: 2px solid;">#</th>
											<th style="border-bottom: 2px solid;">Diseño curricular</th>
											<th style="border-bottom: 2px solid;">Nivel</th>
											<th style="border-bottom: 2px solid;width: 430px;">Transversales permitidas por trimestre</th>
											<th style="border-bottom: 2px solid;">Transversales <strong>no</strong> permitidas</th>
										</tr>
									</thead>
									<tbody>
										<?php $contadorCiclo = 1; ?>
										@foreach($arrayAsignado as $key1 => $asi)
											<?php $cantidadTrimestres = $asi['cantidadTrimestres']; unset($asi['cantidadTrimestres']); ?>
											<?php
												$style="";
												if($contadorCiclo == $contadorCiclo){
													$style="border-bottom: 2px solid;";
												}
											?>
											@foreach($asi as $key2 => $asi2)
												<tr>
													<td style="{{ $style }}vertical-align: middle; border-top: 2px solid; border-left: 2px solid;" rowspan="{{ $cantidadTrimestres }}"><h5>{{ $contadorCiclo++ }}</h5></td>
													<td style="{{ $style }}vertical-align: middle; border-top: 2px solid" rowspan="{{ $cantidadTrimestres }}"><h5 class="diseno text-center">{{ $key2 }}</h5></td>
													<td style="{{ $style }}vertical-align: middle; border-top: 2px solid" rowspan="{{ $cantidadTrimestres }}"><h5 class="nivel text-center">{{ $asi2['nivel'] }}</h5></td>
													<td class="text-center funciones-disponibles funcion-ok funcion-{{ $contadorCiclo }}" diseno="{{ $key2 }}" nivel="{{ $asi2['nivel'] }}" trimestre="1">
														Trimestre 1<br>
														@if($asi2['trimestre'][1] != '')
															@foreach($arrayTransersal as $id_transversal => $transversal)
																@if(in_array($id_transversal, $asi2['trimestre'][1]))
																	<span class="label label-success funciones funciones-{{ $contadorCiclo }} styleSpan" data-cont="{{ $contadorCiclo }}" id="funcion-{{ $contadorCiclo }}">
																		{{ $transversal }}
																		<input name="transversal[]" value="{{ $id_transversal }}" type="checkbox" checked="true" style="display:none;">
																		<input name="trimestre[]" value="1" type="checkbox" checked="true" style="display:none;">
																		<input name="nivel[]" value="{{ $asi2['nivel'] }}" type="checkbox" checked="true" style="display:none;">
																		<input name="disenoCurricular[]" value="{{ $key2 }}" type="checkbox" checked="true" style="display:none;">
																	</span>
																@endif
															@endforeach
														@endif
													</td>

													<td class="funciones-disponibles funcion-nok funcion-{{ $contadorCiclo }}"  diseno="{{ $key2 }}" nivel="{{ $asi2['nivel'] }}" trimestre="1" style="vertical-align: middle;border-right: 2px solid;border-bottom: 2px solid;" rowspan="{{ $cantidadTrimestres }}">
													@if(isset($asi2['transversalProgramada']))
														@foreach($arrayTransersal as $id_transversal => $transversal)
															@if(!in_array($id_transversal, $asi2['transversalProgramada']))
															<span class="label label-danger funciones funciones-{{ $contadorCiclo }} styleSpan" data-cont="{{ $contadorCiclo }}" id="funcion-{{ $contadorCiclo }}">
																{{ $transversal }}
																<input name="transversal[]" value="{{ $id_transversal }}" type="checkbox" style="display:none;">
																<input name="trimestre[]" type="checkbox" style="display:none;">
																<input name="nivel[]" type="checkbox" style="display:none;">
																<input name="disenoCurricular[]" type="checkbox" style="display:none;">
															</span>
															@endif
														@endforeach
													@else
														@foreach($arrayTransersal as $id_transversal => $transversal)
															<span class="label label-danger funciones funciones-{{ $contadorCiclo }} styleSpan" data-cont="{{ $contadorCiclo }}" id="funcion-{{ $contadorCiclo }}">
																{{ $transversal }}
																<input name="transversal[]" value="{{ $id_transversal }}" type="checkbox" style="display:none;">
																<input name="trimestre[]" type="checkbox" style="display:none;">
																<input name="nivel[]" type="checkbox" style="display:none;">
																<input name="disenoCurricular[]" type="checkbox" style="display:none;">
															</span>
														@endforeach
													@endif
													</td>
												</tr>
												@for($k=2; $k<=$cantidadTrimestres; $k++)
												<?php
													$style="";
													if($k == $cantidadTrimestres){
														$style="border-bottom: 2px solid;";
													}
												?>
												<tr>
													<td class="text-center funciones-disponibles funcion-ok funcion-{{ $contadorCiclo }}" diseno="{{ $key2 }}" nivel="{{ $asi2['nivel'] }}" trimestre="{{ $k }}" style="{{ $style }}">
														Trimestre {{ $k }}<br>
														@if($asi2['trimestre'][$k] != '')
															@foreach($arrayTransersal as $id_transversal => $transversal)
																@if(in_array($id_transversal, $asi2['trimestre'][$k]))
																	<span class="label label-success funciones funciones-{{ $contadorCiclo }} styleSpan" data-cont="{{ $contadorCiclo }}" id="funcion-{{ $contadorCiclo }}">
																		{{ $transversal }}
																		<input name="transversal[]" value="{{ $id_transversal }}" type="checkbox" checked="true" style="display:none;">
																		<input name="trimestre[]" value="{{ $k }}" type="checkbox" checked="true" style="display:none;">
																		<input name="nivel[]" value="{{ $asi2['nivel'] }}" type="checkbox" checked="true" style="display:none;">
																		<input name="disenoCurricular[]" value="{{ $key2 }}" type="checkbox" checked="true" style="display:none;">
																	</span>
																@endif
															@endforeach
														@endif
													</td>
												</tr>
												@endfor
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
table tr th{
	text-align: center;
	vertical-align: middle;
}
.funciones{
	cursor: move;
}
.styleSpan{
	padding: 4px;
	margin-bottom:2px;
	border: 1px solid black;
	display: inline-block;
}
</style>
@endsection

@section('plugins-js')
<script src="{{ asset('/plugins/jQueryUI/jquery-ui-1.10.3.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
$(function () {
    validarPermisos();
    $(".funciones").each(function () {
        $(this).draggable({revert: "invalid"});
        var controlador = $(this).attr("data-cont");
        $(".funcion-" + controlador).droppable({
            accept: ".funciones-" + controlador,
            drop: function (event, ui) {
				var diseno = event.target.attributes[1].value;
				var nivel = event.target.attributes[2].value;
				var trimestre = event.target.attributes[3].value;
				console.log(event.target.attributes);
                if ($(this).hasClass('funcion-nok')) {
                    ui.draggable.attr("style", "padding: 4px; margin: 0px 2px 2px 0px; border: 1px solid black; display:inline-block;").removeClass("label-success").addClass("label-danger").appendTo(this);
                    ui.draggable.find("input").prop("checked", false);
                    validarPermisos();
                } else if ($(this).hasClass('funcion-ok')) {
                    ui.draggable.attr("style", "padding: 4px; margin: 0px 2px 2px 0px; border: 1px solid black; display:inline-block;").removeClass("label-danger").addClass("label-success").appendTo(this);
                    ui.draggable.find("input").prop("checked", true);
                    ui.draggable.find("input[name='disenoCurricular[]']").val(diseno);
                    ui.draggable.find("input[name='nivel[]']").val(nivel);
                    ui.draggable.find("input[name='trimestre[]']").val(trimestre);
                    validarPermisos();
                }
                ui.draggable.draggable({revert: "invalid"});
            }
        });
    });

    function validarPermisos() {
        if ($(".funcion-ok span").length == 0) {
            $("#validacion").val("");
        }else{
            $("#validacion").val("1");
        }
    }
});
</script>
<script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {

    $.validator.setDefaults({
        ignore: []
    });

    $('#formRoles').validate({
        rules: {
            validacion: {
                required: true
            }
        },
        messages: {
            validacion: {
                required: "Por favor seleccionar al menos un permiso para este rol"
            }
        }
    });
});
</script>
@endsection