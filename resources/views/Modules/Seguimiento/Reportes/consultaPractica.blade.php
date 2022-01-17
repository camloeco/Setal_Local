@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento ','Etapa practica') !!}
<div class="col-lg-12">
	<div class="col-lg-3">
		<label>Buscar</label>
		<input id="inpConsulta" class="form-control" type="text" placeholder="# Ficha o nombre programa"/>
	</div>
	<div class="col-lg-1" style="padding: 20px 0px 0px 15px;">
		<a class="btn btn-primary btn-xs" data-url-total="{{ url('seguimiento/reportes/inputtotalconsulta') }}" data-url="{{ url('seguimiento/reportes/inputconsulta') }}" id="btnConsulta">Buscar</a>
	</div>
	<div class="col-lg-3">
		<label>Filtrar</label>
		<select data-url="{{ url('seguimiento/reportes/consultafiltro') }}" class="form-control" id="selConsulta">
			<option value="0" > SELECCIONE </option>
			<option value="1" > Tiempo id&oacute;neo</option>
			<option value="5" > &Uacute;ltimo trimestre esta en practica</option>
			<option value="9" > Tiempo m&aacute;ximo cumplido</option>
			<option value="13" > Termina etapa productiva este trimestre</option>
			<option value="16" > Ficha en rango 2 años</option>
			<option value="17" > NINGUNO </option>
		</select>
	</div>
	<div class="col-lg-3">
		<label>Filtrar por instructor</label>
		<!--<select data-url="{{ url('seguimiento/reportes/selinstructor') }}" id="selInstructor" class="form-control">-->
		<input list="browsers" name="browser" data-url-total="{{ url('seguimiento/reportes/selinstructortotal') }}" data-url="{{ url('seguimiento/reportes/selinstructor') }}" id="selInstructor" class="form-control" />
		<datalist id="browsers">
		@foreach($instructores AS $ins)
			<option value="{{$ins->par_identificacion}}">{{$ins->par_nombres}} {{$ins->par_apellidos}}</option>
		@endforeach
		</datalist>
		<!--</select>-->
	</div>
	<div class="col-lg-2">
		@foreach($sqlTotal as $total)
		<label>Total resultados</label>
		<input data-url="{{ url('seguimiento/reportes/inputtotal') }}" id="totalResusltado" value="{{ $total->total }}" class="form-control" type="text" readonly />
		@endforeach
	</div>
</div>
<div class="col-lg-12" style="margin: 10px 3px 0px 0px;">
	<div id="responsive" class="table-responsive" style="border: 1px solid;">
		<table class="table table- table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Ficha</th>
					<th>Nivel formaci&oacute;n</th>
					<th>Programa</th>
					<th>Fecha inicio</th>
					<th>Fecha inicio pr&aacute;ctica</th>
					<th>Fecha fin pr&aacute;ctica</th>
					<th>Ficha terminaci&oacute;n por tiempo fecha</th>
					<th>Localizaci&oacute;n</th>
					<th>Instructor l&iacute;der</th>
					<th>Acci&oacute;n</th>
				</tr>
			</thead>
			<?php $contador = $offset; ?>
			<tbody id="respuesta">
				@foreach($sqlFicha as $ficha) 
				<tr>						  
					<td><?php echo ++$contador; ?></td> 
					<td>{{ $ficha->fic_numero }}</td> 
					<td>{{ $ficha->niv_for_nombre }}</td>  
					<td>{{ $ficha->prog_nombre }}</td>
					<td>{{ $ficha->fic_fecha_inicio }}</td>
					<td>
					<?php
						if($ficha->niv_for_id == 1){
							echo date( "m/d/Y", strtotime( "".$ficha->fic_fecha_inicio." +91 days" ) );
						}else if($ficha->niv_for_id == 2){
							echo date( "m/d/Y", strtotime( "".$ficha->fic_fecha_inicio." +182 days" ) );
						}else if($ficha->niv_for_id == 4){
							echo date( "m/d/Y", strtotime( "".$ficha->fic_fecha_inicio." +547 days" ) );
						}
					?>
					</td>
					<td>{{ $ficha->fic_fecha_fin }}</td>
					<td>{{ $ficha->fecha_terminacion }}</td>
					<td>{{ $ficha->fic_localizacion }}</td>
					<td>{{ $ficha->nombre }}</td>
					<td><a id="ventanaModal" data-url="{{ url('seguimiento/reportes/aprendicesficha')}}" data-ficha="{{ $ficha->fic_numero }}" data-toggle="modal" data-target="#myModal" class=" btn btn-primary btn-sm">Ver</a></td>
				</tr>
				@endforeach
			</tbody>
		</table>
		<div id="paginador" class="pull-right">
			<?php echo $sqlFicha->render() ?>
		</div>
	</div>
	
	<!-- Modal -->
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width:80%">
			<!-- Modal content-->
			<div class="modal-content" id="r">
				
			</div>
		</div>
	</div>
</div>
@endsection
@section('plugins-js')
<script type="text/javascript">
	$(document).ready(function(){
		$(document).on("change","#selConsulta",function(){
			var vIngresado = $(this).val();
			var url = $(this).attr("data-url");
			$.ajax({
				url: url,
				type: "GET",
				data: "vIngresado="+vIngresado,
				success: function(respuesta){
					$("#respuesta").html(respuesta);
				}
			});
			$("#responsive").css({"overflow":"auto","height":"400px"});
			$("#paginador").css("display","none");
			//----Input total ----
			url = $("#totalResusltado").attr("data-url");
			$.ajax({
				url: url,
				type: "GET",
				data: "vIngresado="+vIngresado,
				success: function(respuesta){
					$("#totalResusltado").val(respuesta);
				}
			});
			$("#inpConsulta").val("");
		});
		
		$(document).on("click","#btnConsulta",function(){
			var vIngresado = $("#inpConsulta").val();
			var url = $(this).attr("data-url");
			var urlTotal = $(this).attr("data-url-total");
			$.ajax({
				url: url,
				type: "GET",
				data: "vIngresado="+vIngresado,
				success: function(respuesta){
					$("#respuesta").html(respuesta);
				}
			});
			$("#responsive").css({"overflow":"auto","height":"400px"});
			$("#paginador").css("display","none");
			//----Input total ----
			$.ajax({
				url: urlTotal,
				type: "GET",
				data: "vIngresado="+vIngresado,
				success: function(respuesta){
					$("#totalResusltado").val(respuesta);
				}
			});
		});
		
		$(document).on("change","#selInstructor",function(){
			var vIngresado = $(this).val();
			var url = $(this).attr("data-url");
			var urlTotal = $(this).attr("data-url-total");
			$.ajax({
				url: url,
				type: "GET",
				data: "vIngresado="+vIngresado,
				success: function(respuesta){
					$("#respuesta").html(respuesta);
				}
			});
			$("#responsive").css({"overflow":"auto","height":"400px"});
			$("#paginador").css("display","none");
			//----Input total ----
			$.ajax({
				url: urlTotal,
				type: "GET",
				data: "vIngresado="+vIngresado,
				success: function(respuesta){
					$("#totalResusltado").val(respuesta);
				}
			});
		});
		//---- ----
		$(document).on("click","#ventanaModal",function(){
			var ficha = $(this).attr("data-ficha");
			var url = $(this).attr("data-url");
			$.ajax({
				url: url,
				type: "GET",
				data: "ficha="+ficha,
				success: function(respuesta){
					$("#r").html(respuesta);
				}
			});
		});
		
		$(document).on("change","#estado_apr", function(){
			var estado = $(this).val();
			var ficha = $(this).attr("data-ficha");
			var url = $(this).attr("data-url");
			$.ajax({
				url: url,
				type:"GET",
				data:"estado="+estado+"&ficha="+ficha,
				success: function(respuesta){
					$("#res").html(respuesta);					
				}
			});			
		});
		
	});
</script>
@endsection