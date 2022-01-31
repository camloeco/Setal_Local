@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Ficha','Listar caracterizaci&oacute;n de fichas') !!}

<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Caracterizaci&oacute;n de ficha</span>
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
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-bottom: 20px;">


                        <!-- FILTRO -->
                        <!-- <form id="form-filtros" method="GET" action="">
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label>Instructor</label>
                                <div class="input-group input-group-xs filtro" name="par_identificacion_instructor">
                                    <input autocomplete="off" required style="border: 1px solid #ccc;padding: 2px 12px;width: 260px;" class="form-control" list="browsers" name="par_identificacion"placeholder="Escriba el documento o nombre...">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <label>Estado</label>
                                <select class="form-control filtro" name="edu_est_id">
                                    <option value=''>Todos...</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 pull-right">
                                <label>Limpiar</label>
                                <a href=""><span style="cursor:pointer;border:1px solid;padding:4px;" class="input-group-addon">Limpiar filtro</span></a>
                            </div>
                        </form> -->
                        <!-- /FILTRO -->

                        <table class="table table-striped table-responsive">
                            <thead class="thead-inverse">
                                <tr>
                                    <th>ID</th>
                                    <th>Instructor</th>
                                    <th>Oferta</th>
                                    <th>Nivel de formacion</th>
                                    <th>Estado</th>
                                    <th>Fecha diligenciada</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1 ?>
                                @if (!empty($data[0]))
                                    @foreach ($data as $fic)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $fic->par_nombres }} {{ $fic->par_apellidos }}</td>
                                            <td>{{ $fic->pla_tip_ofe_descripcion }}</td>
                                            <td>{{ $fic->niv_for_nombre }}</td>
                                            <td>
                                                @if ($fic->fic_car_est_id == 1)
                                                    <span class="tag tag-info">Solicitada</span>
                                                @elseif ($fic->fic_car_est_id == 2)
                                                    <span class="tag tag-danger">Rechazada</span>
                                                @elseif ($fic->fic_car_est_id == 3)
                                                    <span class="tag tag-warning">Aporación 1</span>
                                                @elseif ($fic->fic_car_est_id == 4)
                                                    <span class="tag tag-warning">Aporación 2</span>
                                                @elseif ($fic->fic_car_est_id == 5)
                                                    <span class="tag tag-success">Creada</span>
                                                @endif
                                            </td>
                                            <td>{{ $fic->fic_car_fec_diligenciada }}</td>
                                                <td>
                                                    <a id="botonVer" data-url="horarios" data-id="{{ $fic->fic_car_id }}" data-prog-codigo="{{ $fic->prog_codigo }}" data-prog-version="{{ $fic->prog_codigo_version }}" data-prog-nombre="{{ $fic->prog_nombre }}" data-blackboard="{{ $fic->fic_car_blackboard }}" data-tip-ofe="{{ $fic->pla_tip_ofe_descripcion }}" data-niv-for="{{ $fic->niv_for_nombre }}" data-par-id="{{ $fic->par_identificacion }}" data-par-nombre="{{ $fic->par_nombres }}" data-par-telefono="{{ $fic->par_telefono }}" data-par-correo="{{ $fic->par_correo }}" data-par-apellidos="{{ $fic->par_apellidos }}" data-fec-dil="{{ $fic->fic_car_fec_diligenciada }}" style="cursor: pointer; margin-right: 60px;">Ver</a>
                                                    @if ($rol == 2) <!-- Rol de instructor -->
                                                        @if ($fic->fic_car_est_id == 2)
                                                            <button id="botonReenviar" data-url="accion" data-acc="rs" data-id="{{ $fic->fic_car_id }}" class="btn btn-info btn-xs" style="margin: 0px;">Reenviar</button>
                                                        @endif
                                                    @elseif ($rol == 3) <!-- Rol de coordinador -->
                                                        @if ($fic->fic_car_est_id == 1)
                                                            <button id="botonAprobar" data-url="accion" data-id="{{ $fic->fic_car_id }}" data-acc="a1" class="btn btn-success btn-xs" style="margin: 0px;">Aprobar</button>
                                                            <button id="botonRechazar" data-url="accion" data-id="{{ $fic->fic_car_id }}" data-acc="r" class="btn btn-danger btn-xs" style="margin: 0px;">Rechazar</button>
                                                        @endif
                                                    @elseif ($rol == 20)  <!-- Rol de Stephanie-->
                                                        @if ($fic->fic_car_est_id == 3)
                                                            <button id="botonAprobar" data-url="accion" data-id="{{ $fic->fic_car_id }}" data-acc="a2" class="btn btn-success btn-xs" style="margin: 0px;">Aprobar</button>
                                                            <button id="botonRechazar" data-url="accion" data-id="{{ $fic->fic_car_id }}" data-acc="r" class="btn btn-danger btn-xs"  style="margin: 0px;">Rechazar</button>
                                                        @endif
                                                    @else   <!-- PENDIENTE AVERIGÜAR EL ROL DE ANA DOLORES Y EL ID (Creación de fichas)-->
                                                        @if ($fic->fic_car_est_id == 4)
                                                            <button id="botonAprobar" data-url="accion" data-id="{{ $fic->fic_car_id }}" data-acc="c" class="btn btn-success btn-xs" style="margin: 0px;">Aprobar</button>
                                                            <button id="botonRechazar" data-url="accion" data-id="{{ $fic->fic_car_id }}" data-acc="r" class="btn btn-danger btn-xs"  style="margin: 0px;">Rechazar</button>
                                                        @endif
                                                    @endif
                                                    
                                                </td>
                                            <td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7"><p class="text-center">Ningún registro añadido</p></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- MODAL VER-->
<div id="modalVer" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Detalle</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-responsive">
                    <tbody class="thead-inverse">
                        <tr>
                            <td><strong>Programa</strong></td>
                            <td colspan="3"><span id="prog_nombre"></span></td>
                            <td><strong>Versión</strong></td>
                            <td style="text-align: center;"><span id="prog_codigo_version"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Territorium</strong></td>
                            <td style="text-align: center;"><span id="fic_car_blackboard"></span></td>
                            <td><strong>Tipo de formación</strong></td>
                            <td style="text-align: center;"><span id="pla_tip_ofe_descripcion"></span></td>
                            <td><strong>Nivel</strong></td>
                            <td style="text-align: center;"><span id="niv_for_nombre"></span></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="text-align: center;"><strong>HORARIO</strong></td>
                        </tr>
                        <tr>
                            <td rowspan="2" style="text-align: center; vertical-align: middle;"><strong>DÍA</strong></td>
                            <td colspan="3" rowspan="2" style="text-align: center; vertical-align: middle;"><strong>AMBIENTE DE FORMACIÓN</strong></td>
                            <td colspan="2" style="text-align: center;"><strong>HORAS (24 horas)</strong></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;"><strong>INICIO</strong></td>
                            <td style="text-align: center;"><strong>FIN</strong></td>
                        </tr>
                        <tbody id="horarios"></tbody>
                        <tr>
                            <td colspan="6" style="text-align: center;"><strong>INFORMACIÓN DEL INSTRUCTOR</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Instructor</strong></td>
                            <td colspan="3" style="text-align: center;"><span id="par_nombres_apellidos"></span></td>
                            <td><strong>C.C</strong></td>
                            <td  style="text-align: center;"><span id="par_identificacion"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Teléfono</strong></td>
                            <td colspan="3" style="text-align: center;"><span id="par_telefono"></span></td>
                            <td><strong>Email</strong></td>
                            <td  style="text-align: center;"><span id="par_correo"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                <a style="margin:0px;" class="btn btn-info btn-xs pull-left btn-listado">Descargar</a>
                <button style="margin:0px;" class="btn btn-danger btn-xs" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL RECHAZAR-->
<div id="modalRechazar" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Observación</h4>
            </div>
            <div class="modal-body">
                <label>Por favor añadir una breve observación del motivo del rechazo</label>
                <textarea id="fic_car_observacion" cols="77" rows="4"></textarea>
                <input type="hidden" id="url">
                <input type="hidden" id="fic_car_id">
                <input type="hidden" id="accr">
            </div>
            <div class="modal-footer">
                <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                <input type="button" id="botonEnviarObservacion" class="btn btn-warning btn-xs pull-left" value="Enviar">
                <button style="margin:0px;" class="btn btn-danger btn-xs" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL REENVIAR-->
<div id="modalReenviar" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Cargar nuevamente el archivo</h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                <button style="margin:0px;" class="btn btn-danger btn-xs" data-dismiss="modal">Cerrar</button>
                <input type="button" id="botonReenviarArchivo" class="btn btn-success btn-xs pull-left" value="Enviar">
            </div>
        </div>
    </div>
</div>


@endsection

@section('plugins-js')

<script type="text/javascript">

    $(document).ready(function () {

        // MODAL VER
        $(document).on('click','#botonVer',function(){
            $('#modalVer').modal();
            var prog_codigo = $(this).attr('data-prog-codigo');
            var prog_codigo_version = $(this).attr('data-prog-version');
            var prog_nombre = $(this).attr('data-prog-nombre');
            var fic_car_blackboard = $(this).attr('data-blackboard');
            var pla_tip_ofe_descripcion = $(this).attr('data-tip-ofe');
            var niv_for_nombre = $(this).attr('data-niv-for');
            var par_identificacion = $(this).attr('data-par-id');
            var par_nombres = $(this).attr('data-par-nombre');
            var par_apellidos = $(this).attr('data-par-apellidos');
            var par_telefono = $(this).attr('data-par-telefono');
            var par_correo = $(this).attr('data-par-correo');
            var fic_car_fec_diligenciada = $(this).attr('data-fec-dil');
            $('#prog_codigo').html(prog_codigo);
            $('#prog_codigo_version').html(prog_codigo_version);
            $('#prog_nombre').html(prog_nombre);
            $('#fic_car_blackboard').html(fic_car_blackboard);
            $('#pla_tip_ofe_descripcion').html(pla_tip_ofe_descripcion);
            $('#niv_for_nombre').html(niv_for_nombre);
            $('#par_identificacion').html(par_identificacion);
            $('#par_nombres_apellidos').html(par_nombres+' '+par_apellidos);
            $('#par_telefono').html(par_telefono);
            $('#par_correo').html(par_correo);
            $('#fic_car_fec_diligenciada').html(fic_car_fec_diligenciada);
			var url = $(this).attr("data-url");
			var fic_car_id = $(this).attr("data-id");
			$.ajax({
				url:url,
				type:"GET",
				data:"fic_car_id="+fic_car_id,
				success:function (data) {
					$("#horarios").html(data);
					var url = "exportardatos?fic_car_id="+fic_car_id+"";
					$(".btn-listado").attr("href",url);
				}
			});
        });

        // MODAL REENVIAR
        $(document).on('click','#botonReenviar',function(){
            $('#modalReenviar').modal();
        });

        // MODAL APROBAR
        $(document).on('click','#botonAprobar',function () {
            var url = $(this).attr('data-url');
            var fic_car_id = $(this).attr('data-id');
            var acc = $(this).attr('data-acc');
            var _token = $('#_token').val();
            var opcion = confirm('¿Esta seguro de APROBAR la solicitud No. '+fic_car_id+'?');
            if (opcion == true) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: "fic_car_id="+fic_car_id+"&acc="+acc+"&_token="+_token,
                    success: function(data){
                        location.reload('listarCaracterizaciones');
                    }
                });
            }
        });

        // BOTON RECHAZAR
        $(document).on('click','#botonRechazar',function () {
            $('#modalRechazar').modal();
            var url = $(this).attr('data-url');
            var fic_car_id = $(this).attr('data-id');
            var accr = $(this).attr('data-acc');
            $('#url').val(url);
            $('#fic_car_id').val(fic_car_id);
            $('#accr').val(accr);
        });

        // CARGAR
        $(document).on('click','#cargar',function(){
            var url= $(this).attr('data-url');
            var fic_car_nombre= $('#fic_car_nombre').val();
            var _token = $('#_token').val();
            $.ajax({
                url: url, 
                type: "POST",
                data: "fic_car_nombre="+fic_car_nombre+"&_token="+_token,
                success: function(data){
                    location.reload('listarCaracterizaciones');
                }
            });
        });

        // BOTON PARA ENVIAR OBSERVACIÓN
        $(document).on('click','#botonEnviarObservacion',function () {
            var url = $('#url').val();
            var fic_car_id = $('#fic_car_id').val();
            var fic_car_observacion = $('#fic_car_observacion').val();
            var acc = $('#accr').val();
            var _token = $('#_token').val();
            $.ajax({
                url: url,
                type: "POST",
                data: "fic_car_id="+fic_car_id+"&fic_car_observacion="+fic_car_observacion+"&acc="+acc+"&_token="+_token,
                success: function(data){
                    location.reload('listarCaracterizaciones');
                }
            });
        });

    });

</script>

@endsection
