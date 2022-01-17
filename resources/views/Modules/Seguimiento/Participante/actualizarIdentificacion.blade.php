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
				@if(session()->get('editarDocumento') != "")
					<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						<strong>Actualización exito</strong> 
					</div>
					{{--*/ session()->forget('editarDocumento'); /*--}}
				@endif
                <form method="POST">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="row">
						<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
							<input required placeholder="Digite el valor a buscar" class="form-control" type="number" name="identificacion_ficha">
						</div>
						<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
							<select required class="form-control" name="buscarPor">
								<option value="1">Identificaci&oacute;n</option>
								<option value="2">Ficha</option>
							</select>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
							<input class="btn btn-primary btn-xs" type="submit" value="Buscar">
						</div>
					</div>
                </form>
				<div class="table-responsive">
					<table class="table table-condensed table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Ficha</th>
								<th>Identificaci&oacute;n</th>
								<th>Nombre</th>
								<th>Apellido</th>
								<th>Correo</th>
								<th>Tel&eacute;fono</th>
								<th>Acci&oacute;n</th>
							</tr>
						</thead>
						<tbody>
							@if(isset($participante))
								@if(count($participante)>0)
									@foreach($participante as $par)
										<tr>
											<td>{{ ++$contador }}</td>
											<td>{{ $par->fic_numero }}</td>
											<td>{{ $par->par_identificacion_actual }}</td>
											<td>{{ $par->par_nombres }}</td>
											<td>{{ $par->par_apellidos }}</td>
											<td>{{ $par->par_correo }}</td>
											<td>{{ $par->par_telefono }}</td>
											<td>
												<a href="{{ url('seguimiento/participante/cambiardocumento') }}?par_identificacion={{ $par->par_identificacion }}" class="btn btn-primary btn-xs">Editar</a>
											</td>
										</tr>
									@endforeach
								@else
									<tr>
										<td colspan="8">No se encontraron registros</td>
									</tr>
								@endif
							@endif
						</tbody>
					</table>
				</div>
            </div>
        </div>
    </div>
</div>

@endsection