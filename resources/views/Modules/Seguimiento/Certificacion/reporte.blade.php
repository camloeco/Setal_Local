@extends('templates.devoops')
@section('content')
	{!! getHeaderMod('Certificación','Listado') !!}
	<div class="row" id="urls" data-token="{{ csrf_token() }}" data-cambio-input = "{{ url('seguimiento/horario/modificarnumeroprograma') }}" data-cambio-select = "{{ url('seguimiento/horario/modificartipooferta') }}">
		<div class="col-xs-12 col-sm-12">
			<div class="box ui-draggable ui-droppable">
				<div class="box-header">
					<div class="box-name ui-draggable-handle">
						<span>Certificación</span>
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
						<div class="col-lg-12 col-md-12 col-sm-12" style="text-align: center;">
							<h2 style="font-family: cursive;font-weight: bold;">Reportes certificaci&oacute;n</h2>
						</div><br><br><br>
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="col-lg-4 col-md-4 col-sm-4">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 miBoton miModal">
									<label class="miTitulo miModal">Juicios evaluativos</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width: 92%;">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title text-center">Reporte - Juicios evaluativos</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<iframe width="100%" height="685" src="https://app.powerbi.com/reportEmbed?reportId=bb8bd11c-7097-43de-86bc-99a6f0ca25bc&autoAuth=true&ctid=cbc2c381-2f2e-4d93-91d1-506c9316ace7&config=eyJjbHVzdGVyVXJsIjoiaHR0cHM6Ly93YWJpLXNvdXRoLWNlbnRyYWwtdXMtcmVkaXJlY3QuYW5hbHlzaXMud2luZG93cy5uZXQvIn0%3D" frameborder="0" allowFullScreen="true"></iframe>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="background: #087b76;">
					<button type="button" class="miBotonCerrar" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('plugins-css')
<style>
	.miBotonCerrar{
		background: red;
	    color: white;
	    background: 9px solid;
	    border: 1px solid black;
	    padding: 5px 8px 5px 8px;
	}
	.close{
		color:white;
		opacity: unset;
		font-size: 22px;
	}
	.modal-header{
		background: #087b76;
		padding: 5px;
		font-family: cursive;
		color:white;
	}
	.miTitulo{
		color: white;
	    font-family: cursive;
	    padding: 5px;
	    font-size: 15px;
	}
	.miBoton{
		background: #087b76;
	    text-align: center;
	    color: black;
	    box-shadow: 0px 0px 7px 1px;
	}
	.miBoton:hover{
		background: #ec7114;
		cursor: pointer;
	    color: #ec7114;
	    box-shadow: 0px 0px 7px 1px;
	}
	.miTitulo:hover{
		cursor: pointer;
	}
</style>
@endsection
@section('plugins-js')
	<script type="text/javascript">
		$(document).ready(function () {
			$(document).on('click','.miModal',function(){
				$('#myModal').modal('show');
			});
		});
	</script>
@endsection