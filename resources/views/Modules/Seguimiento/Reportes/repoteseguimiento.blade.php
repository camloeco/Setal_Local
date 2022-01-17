@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Control seguimiento','etapa productiva') !!}
<section class='content'>
    <div class="row">
        <div class="col-xs-12">
            <div class="box ui-draggable ui-droppable"> 
				<div class="box-header" >
                    <div class="box-name ui-draggable-handle">
                        <i class="fa fa-table"></i>
                        <span>Seguimiento Etapa Practica</span>
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
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding: 0px 15px 10px 0px;">
						<div class="col-lg-3 col-md-3 col-sm-3">
							<label>Filtrar por aprendices</label>
							<select name="filtros[]" class="form-control">
								<option value="">SELECCIONE</option>
								<option value="1">Pendientes de la visita de planeación</option>
								<option value="2">Pendientes de revisión de bitácoras</option>
								<option value="3">Pendientes de la visita de evaluación</option>
								<option value="4">Pendientes de cierre un mes</option>
								<option value="5">Con bitácoras pendientes</option>
								<option value="6">Con visitas incumplidas </option>
							</select>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-3">
							<label>Filtrar por alternativa</label>
							<select name="filtros[]" class="form-control">
								<option value="">SELECCIONE</option>
								@foreach($alternativas as $alt)
								<option value="{{ $alt->ope_id }}">{{ $alt->ope_descripcion }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-3">
						<label>Filtar por ficha</label>
						<input type="number" id="numeroFicha" class="form-control" placeholder="Numero de ficha">
						</div>
						<div class="col-lg-1 col-md-1 col-sm-1">
						<br><button class="btn-success filtros" style="border-radius:5px 5px 5px;" data-url="{{url('seguimiento/reportes/filtros')}}">Buscar</button>
						</div>
						<div class ="pull-right">
							<label>Total</label>
							<input class="form-control" id="totalConsulta" type="number" value="{{ $totAprendices }}" disabled style="width:100px;">
						</div>
						<div class="col-lg-3 col-md-3 col-sm-3" style="padding: 21px 0px 0px 0px; display:none; " id="cargando">
							<img src="{{ asset('img/cargando.gif') }}" style="float:  left;height: 25px;"></img>
							<p>&nbsp;Cargando...</p>
						</div>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
						<div class="table-responsive" style="border: solid 1px" id="respuesta">
							<table class="table table-bordered table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>Ficha</th>
										<th>Identificaci&oacute;n</th>
										<th>Nombre</th>
										<th>Apellido</th>
										<th>Correo</th>
										<th>Etapa Productiva</th>
										<th>Acci&oacute;n</th>
									</tr>
								</thead>
								<?php $inicioContador = $contador; ?>
								<tbody>
									@foreach($aprendices as $apr)
									<tr>
										<td>{{ $contador++ }}</td>
										<td>{{ $apr->fic_numero }}</td>
										<td>{{ $apr->par_identificacion }}</td>
										<td>{{ $apr->par_nombres }}</td>
										<td>{{ $apr->par_apellidos }}</td>
										<td>{{ $apr->par_correo }}</td>
										<td>
											<?php if(array_key_exists($apr->par_identificacion, $arrayAlternativa)){
												echo $arrayAlternativa[$apr->par_identificacion];
											}else{
												echo "Sin alternativa";
											} 	?>
										</td>
										<td><a id="modal" data-url="{{ url('seguimiento/reportes/modal')}}" data-id-aprendiz="{{ $apr->par_identificacion }}" data-nombre="{{ $apr->par_nombres }}" data-apellido="{{ $apr->par_apellidos }}" data-toggle="modal" data-target="#myModal" class=" btn btn-primary btn-xs">Ver</a></td>
									</tr>
									@endforeach
								</tbody>
							</table>
							<div style="padding:10px">
							@if($cantidadPaginas > 1)
								@if($cantidadPaginas <= 10)
									<div class="row">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											@if($cantidadPaginas > 1 )
												<small style="float:left;">
													Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $totAprendices }} registros
												</small>
											@endif
											@for($i=$cantidadPaginas; $i>0; $i--)
												<?php
													$style='';
													if($i == $pagina){
														$style=";background:#141E30; color:white;";
													}
												?>
												<button style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;{{$style}}" class="pagina" data-pagina="{{ $i }}">{{ $i }}</button>
											@endfor
										</div>
									</div>
									@else
									<div class="row">
										<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
											<small style="float:left;">
												Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $totAprendices }} registros
											</small>
											<?php
												$style='';
												if($cantidadPaginas == $pagina){
													$style=";background:#087b76; color:white;";
												}
												$cantidadInicia = 10;
												if($pagina >= 10){
													if($pagina == $cantidadPaginas){
														$cantidadInicia = $pagina;
													}else{
														$cantidadInicia = ($pagina+1);
													}
												}
											?>
											@if($pagina < ($cantidadPaginas-1))
												<button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;{{ $style }}" class="pagina" data-pagina="{{ $cantidadPaginas }}">{{ $cantidadPaginas }}</button>
												<button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button>
											@endif
											@for($i=10; $i>0; $i--)
												<?php
													$style='';
													if($cantidadInicia == $pagina){
														$style=";background:#087b76; color:white;";
													}
												?>
												<button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}" class="pagina" data-pagina="{{$cantidadInicia}}">{{ $cantidadInicia }}</button>
												<?php $cantidadInicia--; ?>
											@endfor
											@if($pagina >= 10)
												<button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button>
												<button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;" class="pagina" data-pagina="1">1</button> 
											@endif
										</div>
									</div>
								@endif
							@endif
							</div>			
						</div>
					</div>
					<div style="margin: 16px 0px 0px 0px;" class="col-lg-3 col-md-3 col-sm-3 col-xs-3">	
						<a id="botonReporte" href="{{ asset('seguimiento/reportes/reporteseguimientoexcel?reporte=0&alternativa=0&partes=0') }}" class="btn btn-primary">Exportar a excel</a>					
					</div>		
					</div>
				</div>
			</div>
        </div>
    </div>
	<!-- Modal -->
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width:58%">
			<!-- Modal content-->
			<div class="modal-content" id="r">
				
			</div>
		</div>
	</div>
</section>
@endsection
@section('plugins-js')
<script type="text/javascript">
    $(document).ready(function () {
		$(document).on("click","#modal",function(){
			var id = $(this).attr("data-id-aprendiz");
			var nombre = $(this).attr("data-nombre");
			var apellido = $(this).attr("data-apellido");
			var url = $(this).attr("data-url");
			$.ajax({
				url: url,
				type: "GET",
				data: "id="+id+"&apellido="+apellido+"&nombre="+nombre,
				success : function(respuesta){
					$("#r").html(respuesta);
				}
			});
		});
		
		window.urlBotonReporte = $("#reporte").attr("data-url-reporte");

		$(document).on("click",".pagina",function(){
			var url = $(".filtros").attr("data-url");
			var pagina = $(this).attr("data-pagina");
			var ficha = $("#numeroFicha").val();
			var filtros = $("select[name='filtros[]']").map( function(){
                   if (this.value == "") {
				   	 return "vacio";     
				   }else{
				   return this.value;
				   }
            }).get();
			if (ficha == "") {
				ficha = "vacio";
			}
			filtros = filtros+","+ficha;
			$.ajax({
				url: url,
				type: "GET",
				data: "filtros="+filtros+"&pagina="+pagina,
				success : function(respuesta){
					$("#respuesta").html(respuesta);
				}
			});
		});

		$(document).on("click",".filtros",function(){
			var url = $(this).attr("data-url");
			var ficha = $("#numeroFicha").val();
			var filtros = $("select[name='filtros[]']").map( function(){
                   if (this.value == "") {
				   	 return "vacio";     
				   }else{
				   return this.value;
				   }
            }).get();
			if (ficha == "") {
				ficha = "vacio";
			}
			filtros = filtros+","+ficha; 
			$.ajax({
				url: url,
				type: "GET",
				data: "filtros="+filtros,
				success : function(respuesta){
					$("#respuesta").html(respuesta);
					var aprendices = $("#n_aprendices").val();
					$("#totalConsulta").val(aprendices);
				}
			});
		});

		$(document).on("change","#informePartes",function(){
		    var partes = $(this).val();
		    //alert(partes);
		    var reporte = $("#reporte").val();
		    var alternativa = $("#alternativa").val();
		    
		    if(reporte == ""){ reporte = 0; }
		    if(alternativa == ""){ alternativa = 0; }
		    
		    $("#botonReporte").attr("href", urlBotonReporte+"?reporte="+reporte+"&alternativa="+alternativa+"&partes="+partes);
		});
    });
</script>
@endsection
