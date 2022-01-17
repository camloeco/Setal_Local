@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Control seguimiento','etapa productiva') !!}
<section class='content'>
    <div class="row">
        <div class="col-xs-12">
            <div class="box ui-draggable ui-droppable">
                <div class="box-header">
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
        				<form action="<?php echo url('seguimiento/practica/index');?>" method="post">
        					<input type='submit' value="Buscar" class="pull-right">						
        					<input type="text" name="cedula" placeholder="Digite la ficha" class="form-control pull-right" style="width:15em; margin-right: 3px;">
        					<input type="hidden" name="_token" value="<?php echo csrf_token();?>">
        				</form>
								
    			    @if(isset($datos[0]->fic_fecha_inicio))
    				    <div class="col-xs-12 col-sm-12">
    					    @foreach ($datos as $dato)
    						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    							<div class="form-group">
    								<div class="col-sm-2">
    									Numero de Ficha		
    								</div>
    								<div class="col-sm-2">												
    									<input type="text" name="ficha" id="ficha" placeholder="Digite la ficha" class="form-control" value="{{ $id }}" readonly >								 
    								</div>
    							</div>
    						</div>
    						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    							<div class="form-group">
    								<div class="col-sm-2">
    									Fecha Inicio		
    								</div>
    								<div class="col-sm-2">												
    									<input type="text" name="f_inicio" id="f_inicio" placeholder="Digite la ficha" class="form-control" value="<?php echo date("d/m/Y", strtotime($dato->fic_fecha_inicio)); ?>" readonly >								 
    								</div>
    							</div>
    							<div class="form-group">
    								<div class="col-sm-2">
    									Termina por fecha
    								</div>
    								<div class="col-sm-2">												
    									<input type="text" name="f_fin" id="f_fin" placeholder="Digite la ficha" class="form-control" value="<?php echo date("d/m/Y", strtotime($dato->fic_fecha_fin)); ?>" readonly >								 
    								</div>
    							</div>
    							<div class="form-group">
    								<div class="col-sm-2" style="color:red;">
    									Termina por tiempo
    								</div>
    								<div class="col-sm-2">												
    									<input type="text" name="f_fin" id="f_fin" placeholder="Digite la ficha" class="form-control" style="color:red;" value="<?php echo date("d/m/Y", strtotime($fecha_tiempo)); ?>" readonly >								 
    								</div>
    							</div>
    						</div>
    						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    							<div class="form-group">
    								<div class="col-sm-2">
    									Nombre del Programa
    								</div>
    								<div class="col-sm-10">						
    									<input type="text" name="programa" id="programa" placeholder="Programa de Formacion" class="form-control" value="{{ $dato->prog_nombre }}"  readonly>
    								</div>
    							</div>
    						</div>
    						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    							<div class="form-group">
    								<div class="col-sm-2">
    									Instructor Lider lectiva
    								</div>
    								<div class="col-sm-3">								
    									<input type="text" name="instructor_lectiva" id="instructor_lectiva" placeholder="Instrcutor" class="form-control" value="{{$dato->par_nombres. " " . $dato->par_apellidos }}" readonly >
    								</div>
    							</div>
    							<div class="form-group">
    								<div class="col-sm-1">
    									Telefono
    								</div>
    								<div class="col-sm-2">								
    									<input type="text" name="tel" id="tel"  class="form-control" value="{{$dato->par_telefono}}" readonly >
    								</div>
    							</div>
    							<div class="form-group">
    								<div class="col-sm-1">
    									Correo
    								</div>
    								<div class="col-sm-3">								
    									<input type="text" name="correo" id="correo" placeholder="Instrcutor" class="form-control" value="{{$dato->par_correo }}" readonly >
    								</div>
    							</div>
    						</div>
    						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    							<div class="form-group">
    								<div class="col-sm-2">
    									Instructor Lider pr&aacute;ctica
    								</div>
    								<div class="col-sm-3">								
    									<input list="browsers" name="browser" data-ficha="{{ $id }}" data-url="{{ url('seguimiento/practica/ajaxinstructorliderpractica') }}" id="selInstructor" class="form-control" value="{{ $dato->par_identificacion_productiva }}" />
    									<datalist id="browsers">
    									@foreach($instructores AS $ins)
    																	
    										<option value="{{$ins->par_identificacion}}">{{$ins->par_nombres}} {{$ins->par_apellidos}}</option>
    									@endforeach
    									</datalist>
    								</div>
    							</div>
    							<div class="form-group">
    								<div class="col-sm-1">
    									Nombre
    								</div>
    								<div class="col-sm-6">								
    									<input type="text" name="nombre_instructor" id="nombre_instructor"  class="form-control" value="" readonly >
    								</div>
    							</div>
    						</div>
                        </div>
    						 @endforeach
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>N&uacute;mero de identificaci&oacute;n</th>
                                    <th colspan='2'><center>Nombre del Aprendiz</center></th>                                
                                    <th>Acci&oacute;n</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = $offset; ?>
    
                                @foreach ($users as $user)
    
                                <tr>
                                    <td data-title="count">{{ ++$i }}</td>                               
                                    <td data-title="Numero">{{ $user->par_identificacion_actual }}</td>
                                    <td data-title="Nombres">{{ $user->par_nombres }}</td>
                                    <td data-title="Apellidos">{{ $user->par_apellidos }}</td>             
                                    <td style="vertical-align:middle" id="{{ $user->par_identificacion }}" class="resultado"
    									data-target="#modal{{ $user->par_identificacion }}"
    									data-nombre="{{ $user->par_nombres }}" 
    									data-apellido="{{ $user->par_apellidos }}"
    									data-url="{{ url('seguimiento/practica/modal') }}">
    									<!--<div class='external-events'>-->
    									<div><small><!--<div class='fc-event'>-->
    									<div style="cursor:pointer;">Ver</div></small></div>
    								</td>                               							
                                </tr>
                                @endforeach                           
                            </tbody>
                        </table>
                        <div class="pull-right">
                            {!! $users->render() !!}
                        </div>
                    </div>
    				<br><br><br><br>
    			    @endif
    			</div>
            </div>  
        </div>
    </div>
	<!--contiene las modales-->
	<div id="modales">
	
	</div>
</section>
@endsection
@section('plugins-js')
<script type="text/javascript">
    $(document).ready(function () {
		$(document).on("click",".resultado",function(){         
			var elemento=$(this);
			var url=$(this).attr("data-url");
			var idR=$(this).attr("id");
			var nombre=$(this).attr("data-nombre");
			var apellido=$(this).attr("data-apellido");
			
			$.ajax({
				url: url,
				type: 'GET',
				data: "id="+idR+"&nombre="+nombre+"&apellido="+apellido,
				success: function(datos){
					$("#modales").append(datos);
					elemento.attr("data-toggle", "modal");
					elemento.removeAttr("class");
					elemento.trigger("click");
				}
			});
		});
		$(document).on("click",".guardarResultado",function(e){
			e.preventDefault();
			var ficha=$("#ficha").val();
			var id=$(this).attr("data-id");
			var data =  $('#resultado'+id).serialize();
			var ruta = $(this).attr("data-url");
			$.ajax({
				url: ruta,
				type: 'POST',
				data: data+"&ficha="+ficha,
				success: function(datos) {
					$("#modales").append(datos);
					elemento.attr("data-toggle", "modal");
				}
			});
		});
		$(document).on("change","#selInstructor",function(){
			var vIngresado = $(this).val();
			var url = $(this).attr("data-url");
			var ficha = $(this).attr("data-ficha");
			//alert(url);
			$.ajax({
				url: url,
				type: "GET",
				data: "vIngresado="+vIngresado+"&ficha="+ficha,
				success: function(res){
					$("#nombre_instructor").val(res);
				}
			});
		});
		
		$("#selInstructor").trigger("change");
    });
</script>
@endsection

               