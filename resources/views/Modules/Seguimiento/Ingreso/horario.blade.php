@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Reporte','Inasistencia') !!}
<div class="row">
	<div class="col-xs-12 col-sm-12">
		<div class="box ui-draggable ui-droppable">
			<div class="box-header">
				<div class="box-name ui-draggable-handle">
					<span>Inasistencia</span>
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
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<form method="POST">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="margin-top: 5px;">
								<div class="col-lg-3 col-lg-push-3 col-md-4 col-md-push-2 col-sm-12 col-xs-12">
									<label style="margin:0px;">Desde: </label><br>
									<input class="form-control" type="date" name="desde" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" required>
								</div>
								<div class="col-lg-3 col-lg-push-3 col-md-4 col-md-push-2 col-sm-12 col-xs-12">
									<label style="margin:0px;">Desde: </label><br>
									<input class="form-control" type="date" name="hasta" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" required>
								</div>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="margin-top: 5px;">
								<div style="margin-top:15px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<input name="_token" type="hidden" value="{{ csrf_token() }}">
									<button><i class="fa fa-2x fa-download"></i></button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection