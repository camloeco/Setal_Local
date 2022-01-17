@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Todos los participantes') !!}

<section class='content'>

    <div class="row">
        <div class="col-xs-12">
            <div class="box ui-draggable ui-droppable">

                <div class="box-header">
                    <div class="box-name ui-draggable-handle">
                        <i class="fa fa-table"></i>
                        <span>Listado de todos los participantes</span>
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

                    <p>Listado de todos los <code>participantes</code> existentes en la base de datos. </p>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Identificaci&oacute;n</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Direcci&oacute;n</th>
                                <th>Tel&eacute;fono</th>
                                <th>Correo</th>
                                <th colspan="3" >Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = $offset; ?>
                            @foreach ($participantes as $participante)

                            <tr>
                                <td data-title="count">{{ ++$i }}</td>
                                <td data-title="Identificacion">{{ $participante->par_identificacion }}</td>
                                <td data-title="Nombres">{{ $participante->par_nombres }}</td>
                                <td data-title="Apellidos">{{ ($participante->par_apellidos) }}</td>
                                <td data-title="Direccion">{{ $participante->par_direccion }}</td>
                                <td data-title="Telefono">{{ $participante->par_telefono }}</td>
                                <td data-title="Email">{{ $participante->par_correo }}</td>
                                {!! 
                                    acciones(
                                                url("seguimiento/participante/show/".$participante->par_identificacion ), 
                                                url("seguimiento/participante/edit/".$participante->par_identificacion),
                                                url("seguimiento/participante/deleted/".$participante->par_identificacion)
                                            ) 
                                !!}
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pull-right">
                        {!! $participantes->render() !!}
                    </div>
                </div>

            </div>  
        </div>
    </div>
</section>

<script type="text/javascript">
    $(".modal-ajax").click(function(e){
        e.preventDefault();
        
        var url = $(this).attr('href');
        var titulo = $(this).attr('data-titulo');
        
        $.ajax({
		mimeType: 'text/html; charset=utf-8', // ! Need set mimeType only when run from local file
		url: url,
		type: 'GET',
		success: function(data) {
			var inner = $(data);
			OpenModalBox(titulo, inner);
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
		},
		dataType: "html",
		async: false
	});
        
    });
    
</script>

@endsection