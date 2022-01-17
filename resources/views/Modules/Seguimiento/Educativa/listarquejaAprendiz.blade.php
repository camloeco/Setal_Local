@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Generar falta o informe') !!}

<div class="row">

    @if (isset($mensaje))
    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Mensaje de respuesta</span>
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
                @if (isset($mensaje['exito']))
                <div class="alert alert-success">
                    <button data-dismiss="success" class="close" type="button">×</button>
                    {{ $mensaje['exito'] }}
                </div>
                @endif

                @if (isset($mensaje['errores']))
                <div class="alert alert-danger">
                    <button data-dismiss="alert" class="close" type="button">×</button>

                    <ul>
                        @foreach ($mensaje['errores'] as $key=>$msg)
                        <li>{{ $msg }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif


    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Listado de faltas o informes por aprendiz</span>
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
            <div class="box-content" >
			<form method='GET' action='{{Url("seguimiento/educativa/listarquejaaprendiz")}}'>
					<div class='col-lg-1 pull-right'>					
						<button  class='btn btn-success btn-xs' value='Buscar'>Buscar</button>
					</div>
					<div class='col-lg-3 pull-right'>					
						<input type='text' name='identificacion' class='form-control' placeholder='Ingresa numero de Identificacion'>
					</div>
				</form>
			
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Identificacion</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
							<th>Tipo</th>
                            <th>Fecha</th>
                            <th colspan='2'><center>Instructor</center></th>                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = $offset ?>
                        @foreach($select as $comuna)
                        <tr>
                            <td>{{ ++$count }}</td>
                            <td>{{ $comuna->par_identificacion }}</td>
                            <td>{{ $comuna->par_nombres }}</td>
                            <td>{{ $comuna->par_apellidos }}</td>
							<td><code>{{ $comuna->edu_tipo_falta_descripcion }}</code></td>
                            <td>{{ $comuna->edu_falta_fecha }}</td>
                           <td>{{ $comuna->instru }}</td>
                           
                        </tr>
                        @endforeach
                    </tbody>

                </table>

                <div class="pull-right">
                    <?php echo $select->render(); ?>
                </div>

            </div>
        </div>
    </div>
    <!--/span-->

    <div id="modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Detalle</h4>
                </div>
                <div class="modal-body" id="modalBody">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection
