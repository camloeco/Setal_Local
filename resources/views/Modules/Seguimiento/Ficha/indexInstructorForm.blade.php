@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Ficha','Caracterizaci&oacute;n de ficha') !!}

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
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <p>Formulario para la caracterización de fichas</p><br>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                <div class="row">
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                        <label class="control-label">Seleccionar el programa de formaci&oacute;n</label>
                                        <select data-rel="chosen" id="prog_codigo" name="prog_codigo" class="js-example-basic-single form-control" required="required">
                                            <option value="">-- Seleccione el c&oacute;digo del programa de formaci&oacute;n --</option>
                                            @foreach($programas as $programa)
                                                <option value="{{ $programa->prog_codigo }}">{{ $programa->prog_codigo }} - {{ $programa->prog_nombre }}</option>
                                            @endforeach
                                        </select><br>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                                        <label class="control-label">Version del programa</label>
                                        <input type="number" class="form-control" name="prog_codigo_version" id="prog_codigo_version"><br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                        <label class="control-label">¿Requiere espacio en la BlackBoard?</label><br>
                                        <input type="radio" name="fic_car_blackboard" id="fic_car_blackboard" value="1"> Si <br>
                                        <input type="radio" name="fic_car_blackboard" id="fic_car_blackboard" value="2"> No
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                                        <label class="control-label">Tipo de formación</label><br>
                                        <input type="radio" name="pla_tip_ofe_id" id="pla_tip_ofe_id" value="1"> Abierta <br>
                                        <input type="radio" name="pla_tip_ofe_id" id="pla_tip_ofe_id" value="2"> Cerrada
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="control-label">Nivel de formación</label><br>
                                <input type="radio" name="niv_for_id" id="niv_for_id" value="0"> Titulada <br>
                                <input type="radio" name="niv_for_id" id="niv_for_id" value="1"> Operario <br>
                                <input type="radio" name="niv_for_id" id="niv_for_id" value="2"> Técnico <br>
                                <input type="radio" name="niv_for_id" id="niv_for_id" value="4"> Tecnólogo <br>
                                <input type="radio" name="niv_for_id" id="niv_for_id" value="6"> Complementario
                            </div>
                        </div><br>
                        <!-- <div class="row" id="">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <label class="control-label">Nombre del convenio</label>
                                        <select data-rel="chosen" id="" name="" class="js-example-basic-single form-control" required="required">
                                            <option value="">-- Seleccione el c&oacute;digo del programa de formaci&oacute;n --</option>
                                        </select><br>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <label class="control-label">C&oacute;digo</label>
                                        <input type="number" class="form-control" name="" id=""><br>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <label class="control-label">Nombre del programa especial</label>
                                        <select data-rel="chosen" id="" name="" class="js-example-basic-single form-control" required="required">
                                            <option value="">-- Seleccione el c&oacute;digo del programa de formaci&oacute;n --</option>
                                        </select><br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                        <label class="control-label">Fecha de inicio</label>
                                        <input type="date" class="form-control" name="" id=""><br>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                        <label class="control-label">Fecha de terminaci&oacute;n</label>
                                        <input type="date" class="form-control" name="" id=""><br>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                        <label class="control-label">Horas</label>
                                        <input type="number" class="form-control" name="" id=""><br>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                        <label class="control-label">Direccion donde se desarrollara la formacion</label>
                                        <input type="text" class="form-control" name="" id=""><br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                        <label class="control-label">Municipio de realizaci&oacute;n</label>
                                        <input type="text" class="form-control" name="" id=""><br>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                        <label class="control-label">Cupo</label>
                                        <input type="number" class="form-control" name="" id=""><br>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <table class="table table-bordered table-responsive">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th rowspan="2" style="text-align: center; vertical-align: middle;">DIA</th>
                                            <th rowspan="2" style="text-align: center; vertical-align: middle;">NOMBRE DEL AMBIENTE DE APRENDIZAJE</th>
                                            <th colspan="2" class="text-center">HORAS (24 horas)</th>
                                        </tr>
                                        <tr>
                                            <th style="text-align: center; vertical-align: middle;">INICIO</th>
                                            <th style="text-align: center; vertical-align: middle;">FIN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                Lunes
                                                <input type="hidden" name="fic_car_hor_lunes" id="fic_car_hor_lunes" value="Lunes">
                                            </td>
                                            <td>
                                                <select data-rel="chosen" id="pla_amb_id_lunes" name="pla_amb_id_lunes" class="js-example-basic-single form-control" required="required">
                                                    <option value="">-- Seleccione el ambiente de formaci&oacute;n --</option>
                                                    @foreach($ambientes as $ambiente)
                                                        <option value="{{ $ambiente->pla_amb_id }}">{{ $ambiente->pla_amb_descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" class="form-control" name="fic_car_hor_inicio_lunes" id="fic_car_hor_inicio_lunes"></td>
                                            <td><input type="time" class="form-control" name="fic_car_hor_fin_lunes" id="fic_car_hor_fin_lunes"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Martes
                                                <input type="hidden" name="fic_car_hor_dia" id="fic_car_hor_dia" value="Martes">
                                            </td>
                                            <td>
                                                <select data-rel="chosen" id="pla_amb_id" name="pla_amb_id" class="js-example-basic-single form-control" required="required">
                                                    <option value="">-- Seleccione el ambiente de formaci&oacute;n --</option>
                                                    @foreach($ambientes as $ambiente)
                                                        <option value="{{ $ambiente->pla_amb_id }}">{{ $ambiente->pla_amb_descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" class="form-control" name="fic_car_hor_inicio" id="fic_car_hor_inicio"></td>
                                            <td><input type="time" class="form-control" name="fic_car_hor_fin" id="fic_car_hor_fin"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Miercoles
                                                <input type="hidden" name="fic_car_hor_dia" id="fic_car_hor_dia" value="Miercoles">
                                            </td>
                                            <td>
                                                <select data-rel="chosen" id="pla_amb_id" name="pla_amb_id" class="js-example-basic-single form-control" required="required">
                                                    <option value="">-- Seleccione el ambiente de formaci&oacute;n --</option>
                                                    @foreach($ambientes as $ambiente)
                                                        <option value="{{ $ambiente->pla_amb_id }}">{{ $ambiente->pla_amb_descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" class="form-control" name="fic_car_hor_inicio" id="fic_car_hor_inicio"></td>
                                            <td><input type="time" class="form-control" name="fic_car_hor_fin" id="fic_car_hor_fin"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Jueves
                                                <input type="hidden" name="fic_car_hor_dia" id="fic_car_hor_dia" value="Jueves">
                                            </td>
                                            <td>
                                                <select data-rel="chosen" id="pla_amb_id" name="pla_amb_id" class="js-example-basic-single form-control" required="required">
                                                    <option value="">-- Seleccione el ambiente de formaci&oacute;n --</option>
                                                    @foreach($ambientes as $ambiente)
                                                        <option value="{{ $ambiente->pla_amb_id }}">{{ $ambiente->pla_amb_descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" class="form-control" name="fic_car_hor_inicio" id="fic_car_hor_inicio"></td>
                                            <td><input type="time" class="form-control" name="fic_car_hor_fin" id="fic_car_hor_fin"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Viernes
                                                <input type="hidden" name="fic_car_hor_dia" id="fic_car_hor_dia" value="Viernes">
                                            </td>
                                            <td>
                                                <select data-rel="chosen" id="pla_amb_id" name="pla_amb_id" class="js-example-basic-single form-control" required="required">
                                                    <option value="">-- Seleccione el ambiente de formaci&oacute;n --</option>
                                                    @foreach($ambientes as $ambiente)
                                                        <option value="{{ $ambiente->pla_amb_id }}">{{ $ambiente->pla_amb_descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" class="form-control" name="fic_car_hor_inicio" id="fic_car_hor_inicio"></td>
                                            <td><input type="time" class="form-control" name="fic_car_hor_fin" id="fic_car_hor_fin"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Sabado
                                                <input type="hidden" name="fic_car_hor_dia" id="fic_car_hor_dia" value="Sabado">
                                            </td>
                                            <td>
                                                <select data-rel="chosen" id="pla_amb_id" name="pla_amb_id" class="js-example-basic-single form-control" required="required">
                                                    <option value="">-- Seleccione el ambiente de formaci&oacute;n --</option>
                                                    @foreach($ambientes as $ambiente)
                                                        <option value="{{ $ambiente->pla_amb_id }}">{{ $ambiente->pla_amb_descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" class="form-control" name="fic_car_hor_inicio" id="fic_car_hor_inicio"></td>
                                            <td><input type="time" class="form-control" name="fic_car_hor_fin" id="fic_car_hor_fin"></td>
                                        </tr>
                                        <!-- <tr>
                                            <td>
                                                Domingo
                                            </td>
                                            <td>
                                                <select data-rel="chosen" id="pla_amb_id" name="pla_amb_id" class="js-example-basic-single form-control" required="required">
                                                    <option value="">-- Seleccione el ambiente de formaci&oacute;n --</option>
                                                    @foreach($ambientes as $ambiente)
                                                        <option value="{{ $ambiente->pla_amb_id }}">{{ $ambiente->pla_amb_descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" class="form-control" name="fic_car_hor_inicio" id="fic_car_hor_inicio"></td>
                                            <td><input type="time" class="form-control" name="fic_car_hor_fin" id="fic_car_hor_fin"></td>
                                        </tr> -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <label class="control-label">Nombre del instructor</label>
                                <select data-rel="chosen" id="par_identificacion" name="par_identificacion" class="js-example-basic-single form-control" required="required">
                                    <option value="">-- Seleccione el ambiente de formaci&oacute;n --</option>
                                    @foreach($participante as $participante)
                                        <option value="{{ $participante->par_identificacion }}">{{ $participante->par_nombres}} {{ $participante->par_apellidos }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div><br>
                        <!-- <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <p>Datos de la empresa solicitante</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="control-label">Nombre de la empresa</label>
                                <input type="text" class="form-control" name="" id=""><br>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="control-label">N° de NIT</label>
                                <input type="number" class="form-control" name="" id=""><br>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="control-label">Teléfono</label>
                                <input type="number" class="form-control" name="" id=""><br>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="control-label">FAX</label>
                                <input type="number" class="form-control" name="" id=""><br>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="control-label">Dirección</label>
                                <input type="text" class="form-control" name="" id=""><br>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="control-label">Email</label>
                                <input type="email" class="form-control" name="" id=""><br>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="control-label">Nombre de contacto</label>
                                <input type="text" class="form-control" name="" id=""><br>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="control-label">Municipio</label>
                                <input type="text" class="form-control" name="" id=""><br>
                            </div>
                        </div><br> -->
                        <div class="row">
                            <div class="text-center">
								<input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
								<input type="button" class="btn btn-success" data-url="cargar" id="botonGuardar" value="Guardar cambios">
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('plugins-js')

<script type="text/javascript">

    $(document).ready(function () {

        $(document).on('click','#botonGuardar',function () {
            var url = $(this).attr('data-url');


            var prog_codigo = $('#prog_codigo').val();
            var prog_codigo_version = $('#prog_codigo_version').val();
            var fic_car_blackboard = $('#fic_car_blackboard').val();
            var pla_tip_ofe_id = $('#pla_tip_ofe_id').val();
            var niv_for_id = $('#niv_for_id').val();
            var par_identificacion = $('#par_identificacion').val();

            var fic_car_hor_lunes = $('#fic_car_hor_lunes').val();
            var pla_amb_id_lunes = $('#pla_amb_id_lunes').val();
            var fic_car_hor_inicio_lunes = $('#fic_car_hor_inicio_lunes').val();
            var fic_car_hor_fin_lunes = $('#fic_car_hor_fin_lunes').val();


            var _token = $('#_token').val();
            $.ajax({
                url: url,
                type: "POST",
                data: "prog_codigo="+prog_codigo+"&prog_codigo_version="+prog_codigo_version+"&fic_car_blackboard="+fic_car_blackboard+"&pla_tip_ofe_id="+pla_tip_ofe_id+"&niv_for_id="+niv_for_id+"&fic_car_hor_lunes="+fic_car_hor_lunes+"&pla_amb_id="+pla_amb_id+"&fic_car_hor_inicio_lunes="+fic_car_hor_inicio_lunes+"&fic_car_hor_fin_lunes="+fic_car_hor_fin_lunes+"&par_identificacion="+par_identificacion+"&_token="+_token,
                success: function(data){
                    location.reload('listarCaracterizaciones');
                }
            });
        });

    });

</script>

@endsection
