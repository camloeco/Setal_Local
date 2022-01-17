@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Participante','Modificar identificaci&oacute;n') !!}

<div class="row">

    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Modificar identificaci&oacute;n </span>
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
				@if(count($errors)>0)
					<div class="alert alert-danger">
						<button data-dismiss="alert" class="close" type="button">×</button>
						<strong>El número de documento ya se encuentra registrado o el número es muy corto</strong>
					</div>
				@endif
			
                <form action="" method="POST">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="row">
						@if(isset($participante))
							<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
								<label>Ficha</label>
								<input value="{{ $participante[0]->fic_numero }}" disabled class="form-control" type="text">
								<label>Identificaci&oacute;n actual</label>
								<input value="{{ $participante[0]->par_identificacion_actual }}" disabled class="form-control" type="number">
								<label>Nombre</label>
								<input value="{{ $participante[0]->par_nombres }}" disabled class="form-control" type="text">
								<label>Apellido</label>
								<input value="{{ $participante[0]->par_apellidos }}" disabled class="form-control" type="text">
							</div>
							<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12" style="margin: 20px 0px 0px 0px;">
								<label>Nueva Identificaci&oacute;n</label>
								<input autofocus required name="par_identificacion_actual" class="form-control" type="number">
								<input value="{{ $participante[0]->par_identificacion }}" name="par_identificacion" class="form-control" type="hidden">
								<input style="margin: 10px 0px 0px 0px;" class="btn btn-primary btn-xs" type="submit" value="Guardar documento">
								<a style="margin: 10px 0px 0px 0px;" href="{{ url('seguimiento/participante/actualizaridentificacion') }}" class="btn btn-danger btn-xs">Cancelar</a>
							</div>
						@endif
					</div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection