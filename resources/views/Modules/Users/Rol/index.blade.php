@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Gesti&oacute;n de roles','Todos los roles') !!}


<section class='content'>

    <div class="row">
        <div class="col-xs-12">
            <div class="box ui-draggable ui-droppable">

                <div class="box-header">
                    <div class="box-name ui-draggable-handle">
                        <i class="fa fa-table"></i>
                        <span>Listado de todos los roles</span>
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

                    <p>Listado de todos los <code>roles</code> existentes en la base de datos. </p>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nombre</th>
                                <th colspan="2" width="20%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            @foreach ($roles as $rol)

                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ strtoupper($rol->nombre_rol) }}</td>
                                <td data-title="Asignar permisos">
                                    <a href="{{ url("users/rol/permisos/".$rol->id_rol ) }}" class="ajax-link" title="Asignar permisos">
                                        Asignar permisos
                                    </a>
                                </td>
<!--                                <td data-title="Editar">
                                    <a href="{{ url("users/rol/edit/".$rol->id_rol ) }}" class="ajax-link" title="Editar">
                                        <i class="fa fa-edit text-blue fa-2x"></i>
                                    </a>
                                </td>-->
                                <td data-title="Eliminar">
                                    <a href="#" data-url="{{ url("users/rol/deleted/".$rol->id_rol) }}" data-toggle="modal" data-target="#modalElimina" title="Eliminar">
                                        <i class="fa fa-remove text-red fa-2x"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pull-right">
                        {!! $roles->render() !!}
                    </div>
                </div>

            </div>  
        </div>
    </div>
</section>

<div id="modalElimina" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ url("users/rol/deleted") }}" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Eliminar</h4>
                </div>
                <div class="modal-body" id="modalBody">

                </div>
                <div class="modal-footer">
                    ¿Esta seguro que desea <code>Eliminar</code> el rol?
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