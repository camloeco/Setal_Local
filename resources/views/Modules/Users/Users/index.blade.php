@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Gesti&oacute;n de Usuarios','Todos los usuarios') !!}


<section class='content'>



    <div class="row">
        <div class="col-xs-12">
            <div class="box ui-draggable ui-droppable">

                <div class="box-header">
                    <div class="box-name ui-draggable-handle">
                        <i class="fa fa-table"></i>
                        <span>Listado de todos los usuarios</span>
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
                    <div class="row" style="margin: 0px 0px 15px 0px;">
                        <form action="<?php echo url('users/users/index');?>" method="post">
                            <input type='submit' value="Buscar" class="pull-right">
                            <input type="text" name="cedula" placeholder="Digite el valor a buscar" class="form-control pull-right" style="width:15em; margin-right: 3px;">
                            <select class="form-control pull-right" style="width:10em;  margin-right: 3px;" name="campo" required>
                                <option value="">Seleccione...</option>
                                <option value="sep_participante.par_identificacion_actual">Identificaci&oacute;n</option>
                                <option value="par_nombres">Nombre</option><option value="par_apellidos">Apellido</option>
                            </select>
                            <input type="hidden" name="_token" value="<?php echo csrf_token();?>">
                        </form>
                        <p style="display:none;">Listado de todos los <code>usuarios</code> existentes en la base de datos. 
                            <ul style="display:none;">
                                <li><small>Para activar un registro, presione sobre el estado <span class="tag tag-danger" title="Activar">Inactivo</span>
                                        </small>
                                </li>
                                <li><small>Para Inactivar un registro, presione sobre el estado <span class="tag tag-info" title="Inactivar">Activo</span>
                                        </small>
                                </li>
                            </ul>
                        </p>
                    </div>
                    
                    <div style="height:283px;overflow-y:auto;">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th># identificaci&oacute;n</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Email</th>
                                <th><center>Estado</center></th>
                                <th colspan="2" >Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = $offset; ?>

                            @foreach ($users as $user)

                            <tr style="font-size:13px;">
                                <td data-title="count">{{ ++$i }}</td>
                                <td data-title="Numero">{{ $user->par_identificacion_actual }}</td>
                                <td data-title="Nombres">{{ $user->par_nombres }}</td>
                                <td data-title="Apellidos">{{ $user->par_apellidos }}</td>
                                <td data-title="Email">{{ $user->email }}</td>
                                <td data-title="Eliminar">
                                    <a href="#" style="text-decoration: none" data-url="{{ url("users/users/deleted/".$user->id ) }}" data-toggle="modal" data-target="#modalElimina" class="ajax-link">
                                        <center>
                                            {!! ($user->estado == 0)?'<span class="tag tag-danger" title="Activar">Inactivo</span>':'<span title="Inactivar" class="tag tag-info">Activo</span>' !!}
                                        </center>
                                    </a>
                                </td>
                                <td data-title="Ver">
                                    <a href="#" data-url="{{ url("users/users/show/".$user->id ) }}" data-toggle="modal" data-target="#modal" class="ajax-link" title="Ver">
                                        Ver
                                    </a>
                                </td>
                                <td data-title="Editar">
                                    <a href="{{ url("users/users/edit/".$user->id ) }}" class="ajax-link" title="Editar">
                                        Editar
                                    </a>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div class="pull-right">
                        {!! $users->render() !!}
                    </div>
                </div>

            </div>  
        </div>
    </div>
</section>


<div id="modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Detalle</h4>
            </div>
            <div class="modal-body" id="modalBody">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div id="modalElimina" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ url("users/users/deleted") }}" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Eliminar</h4>
                </div>
                <div class="modal-body" id="modalBody">

                </div>
                <div class="modal-footer">
                    ¿Esta seguro que desea <code>activar / Inactivar</code> el usuario?
                    <button type="submit" class="btn btn-success" >Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('plugins-js')
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on("click", "a[data-toggle='modal']", function () {
            //e.prevntDefault();
            var destino = $(this).attr("data-target");
            var url = $(this).attr("data-url");

            $(destino + " .modal-body").html("Cargando ...");

            $.ajax({
                url: url,
                type: "GET",
                success: function (data) {
                    $(destino + " .modal-body").html(data);
                }
            });


        });
    });
</script>
@endsection