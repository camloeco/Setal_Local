<div id="modal{{$id}}" class="modal fade" role="dialog" style="width:100%">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Seguimiento Etapa Productiva</h4>
			</div>
			<div class="modal-body" id="modalBody" >
				<form name="resultado{{$id}}" id="resultado{{$id}}" method="post" action="{{ url('Seguimiento/practica/seguimiento') }}" style="width:100%">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="ficha" value="">
					<input type="hidden" name="id" id="id" value="{{ $id }}">
					<table class="table table-bordered" class="table table-hover">
						<tr>
							<td>
								<h4 class="modal-title">
									{{$nombre}}
									{{$apellido}}
								</h4> 									
							</td>
						</tr>
					</table>
					<table class="table table-bordered" class="table table-hover">	
						<tr>		
							<td width='50%'>
								<b>Alternativa Etapa Productiva</b>
								<select <?php echo $disabled; ?> data-rel="chosen" id="ope_id" name="ope_id" class="form-control select2" value="2">
									<option value="" selected>SELECCIONE</option>
									@foreach($alternativas as $key=>$alternativa)
									 	@if($key==$ope_id)
											<option value="{{ $key }}" selected>{{ $alternativa }}</option>
									 	@else	
											<option value="{{ $key }}">{{ $alternativa }}</option>
										@endif 	
									@endforeach
								</select>
							</td>
							<td width='50%'>	
								<b>Nombre de Empresa</b>
								<input <?php echo $disabled; ?> type="text" name="empresa" id="empresa" class="form-control" value="{{ $seg_pro_nombre_empresa }}">										
							</td>
						</tr>							 
						<tr>
							<td width='40%'>
								<b>Fecha Inicio etapa productiva</b>
								<input <?php echo $disabled; ?> type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ $seg_pro_fecha_ini }}">
							</td>
							<td width='40%'>
								<b>Fecha fin etapa productiva</b>
								<input <?php echo $disabled; ?> type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ $seg_pro_fecha_fin }}">
							</td>
						</tr>
					</table>
					<table class="table table-bordered" class="table table-hover">
						<tr>
							<th colspan='3'>
								<center>Visitas</center>
							</th>
						</tr>
						<tr>
							<th>Planeaci&oacute;n</th>
							<th>Seguimiento extraordinario</th>
							<th>Evaluaci&oacute;n</th>                                        
						</tr>								
						<tr>	
							<?php
								for($i=1;$i<=3;$i++){
									if(in_array($i,$visitas)){ 
										echo "
										<td>
											<input $disabled type='checkbox' name='visita$i' id='visita$i' checked>
											<label for='visita$i'>Seleccione</label>
										</td>";
									}else{
										echo "
										<td>
											<input $disabled type='checkbox' name='visita$i' id='visita$i'>
											<label for='visita$i'>Seleccione</label>
										</td>";
									}
								}
							?>			
						</tr>
						<tr>	
							<?php
								for($i=1;$i<=3;$i++){
									$array_key = array_search($i,$visitas);
									if($array_key != ""){ 
										echo "
										<td>
											<input $disabled value='".$fechas[$array_key]."' type='date' name='fecha$i' id='fecha$i' class='form-control'>
										</td>";
									}else{
										echo "
										<td>
											<input $disabled type='date' name='fecha$i' id='fecha$i' class='form-control'>
										</td>";
									}
								}
							?>			
						</tr>
					</table>
					<table class="table table-bordered" class="table table-hover">
						<tr>
							<th colspan='12'>
								<center>Bitacoras</center>
							</th>
							<?php
								for($i=1;$i<=12;$i++){
									echo "<th>$i</th>";
								}
							?> 
						</tr>
						<tr>
							<td colspan='12'></td>
							<?php
								for($i=1;$i<=12;$i++){
									if(in_array($i,$bitacoras))
									{
										echo "<td><input $disabled type='checkbox' name='bitacora[$id][]' value='$i' checked></td>";
									}
									else
									{
										echo "<td><input $disabled type='checkbox' name='bitacora[$id][]' value='$i'></td>";
									}
								}	
							?>
						</tr>
					</table>
					
					<table class="table table-bordered" class="table table-hover">
						<tr>
							<th>
								<center>Observaciones instructor seguimiento</center>
							</th>
							<th>
								<center>Observaciones coordinador etapa productiva</center>
							</th>
						</tr>
						<tr>
							<td>
								<textArea <?php echo $readonly; ?> id="instructorSeguimiento" name='seg_pro_obs_instructor_seguimiento' class="form-control" rows="3" maxlength="500">{{ $seg_pro_obs_instructor_seguimiento }}</textarea>
							</td>
							<td>
							    @if(\Auth::user()->par_identificacion == 14995914)
								<textArea <?php echo $observaciones_lider; ?> name='seg_pro_obs_lider_productiva' class="form-control" rows="3" maxlength="500">{{ $seg_pro_obs_lider_productiva }}</textArea>
							    @else 
							   	<textArea readonly <?php echo $observaciones_lider; ?> name='seg_pro_obs_lider_productiva' class="form-control" rows="3" maxlength="500">{{ $seg_pro_obs_lider_productiva }}</textArea>
							    @endif
							</td>
						</tr>
					</table>
				</form>
				<div class="modal-footer">
				    
					<button type="button" class="btn btn-success guardarResultado" data-dismiss="modal" data-url="{{ url('seguimiento/practica/seguimiento') }}" data-id="{{ $id }}">Guardar</button>
					
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				</div>
			</div>  
		</div>
	</div>
</div>