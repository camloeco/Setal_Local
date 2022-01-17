@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Fichas asignadas para seguimiento practica') !!}

<div class="col-lg-12">
	<!--<div class="col-lg-4">
		<label>Buscar</label>
		<input data-url-total="{{ url('seguimiento/ficha/ajaxconsulta') }}" data-url="{{ url('seguimiento/ficha/ajaxconsulta') }}" class="form-control" type="text" id="inpConsulta" placeholder="N&uacute;mero de ficha o nombre de programa" />
	</div>-->
	<div class="col-lg-2">
		<label>Identificacion: </label><br>
		<input type="text" value="{{ Auth::user()->participante->par_identificacion}}" readonly >
	</div>
	<div class="col-lg-2">
		<label>Nombre: </label><br>
		<input type="text" value="{{ Auth::user()->participante->par_nombres}}" readonly >
	</div>
	<div class="col-lg-2">
		<label>Total resultados</label>
		<input data-url="{{ url('seguimiento/reportes/inputtotal') }}" id="totalResusltado" value="{{$totalResultados[0]->total}}" class="form-control" type="text" readonly />
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
					<!--<th>Fecha inicio pr&aacute;ctica</th>
					<th>Fecha fin pr&aacute;ctica</th>
					<th>Fecha terminaci&oacute;n por tiempo fecha</th>-->
					<th>Localizaci&oacute;n</th>
					<th>Instructor l&iacute;der</th>
					<th>Acci&oacute;n</th>
				</tr>
			</thead>
			<?php $contador = 0; ?>
			<tbody id="respuesta">
				@foreach($instructor as $ficha) 
				<tr>						  
					<td><?php echo ++$contador; ?></td> 
					<td>{{ $ficha->fic_numero }}</td>  
					<td>{{ $ficha->niv_for_nombre }}</td>  
					<td>{{ $ficha->prog_nombre }}</td>
					<td>{{ $ficha->fic_fecha_inicio }}</td>
					<!--<td>{{ $ficha->fic_fecha_fin }}</td>
					<td>{{ $ficha->fic_fecha_fin }}</td>
					<td>{{ $ficha->fecha_terminacion }}</td>-->
					<td>{{ $ficha->fic_localizacion }}</td>
					<td>{{ $ficha->nombre }}</td>
					<td><a href="<?php echo url('seguimiento/ficha/consulta?id='.$ficha->fic_numero) ?>" class=" btn btn-primary btn-sm">Ver</a></td>
				</tr>
				@endforeach
			</tbody>
		</table>
		<div id="paginador" class="pull-right">
		
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
		
		$(document).on("keyup","#inpConsulta",function(){
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