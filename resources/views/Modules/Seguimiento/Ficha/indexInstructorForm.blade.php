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
                                        <input type="radio" name="fic_car_blackboard" id="fic_car_blackboard" value="si"> Si <br>
                                        <input type="radio" name="fic_car_blackboard" id="fic_car_blackboard" value="no"> No
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                                        <label class="control-label">Tipo de formación</label><br>
                                        <input type="radio" name="pla_tip_ofe_id" id="pla_tip_ofe_id" value="abierta"> Abierta <br>
                                        <input type="radio" name="pla_tip_ofe_id" id="pla_tip_ofe_id" value="cerrada"> Cerrada
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <label class="control-label">Nivel de formación</label><br>
                                <input type="radio" name="niv_for_id" id="niv_for_id" value="titulada"> Titulada <br>
                                <input type="radio" name="niv_for_id" id="niv_for_id" value="operario"> Operario <br>
                                <input type="radio" name="niv_for_id" id="niv_for_id" value="tecnico"> Técnico <br>
                                <input type="radio" name="niv_for_id" id="niv_for_id" value="tecnico"> Tecnólogo <br>
                                <!-- <input type="radio" name="niv_for_id" id="niv_for_id" value="tecnologo"> Especialización Tecnológica <br> -->
                                <input type="radio" name="niv_for_id" id="niv_for_id" value="complementaria"> Complementaria
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
                                            </td>
                                            <td>
                                                <select data-rel="chosen" id="" name="" class="js-example-basic-single form-control" required="required">
                                                    <option value="">-- Seleccione el ambiente de formaci&oacute;n --</option>
                                                    @foreach($ambientes as $ambiente)
                                                        <option value="{{ $ambiente->id }}">{{ $ambiente->descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" class="form-control" name="" id=""></td>
                                            <td><input type="time" class="form-control" name="" id=""></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Martes
                                            </td>
                                            <td>
                                                <select data-rel="chosen" id="" name="" class="js-example-basic-single form-control" required="required">
                                                    <option value="">-- Seleccione el ambiente de formaci&oacute;n --</option>
                                                    @foreach($ambientes as $ambiente)
                                                        <option value="{{ $ambiente->id }}">{{ $ambiente->descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" class="form-control" name="" id=""></td>
                                            <td><input type="time" class="form-control" name="" id=""></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Miercoles
                                            </td>
                                            <td>
                                                <select data-rel="chosen" id="" name="" class="js-example-basic-single form-control" required="required">
                                                    <option value="">-- Seleccione el ambiente de formaci&oacute;n --</option>
                                                    @foreach($ambientes as $ambiente)
                                                        <option value="{{ $ambiente->id }}">{{ $ambiente->descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" class="form-control" name="" id=""></td>
                                            <td><input type="time" class="form-control" name="" id=""></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Jueves
                                            </td>
                                            <td>
                                                <select data-rel="chosen" id="" name="" class="js-example-basic-single form-control" required="required">
                                                    <option value="">-- Seleccione el ambiente de formaci&oacute;n --</option>
                                                    @foreach($ambientes as $ambiente)
                                                        <option value="{{ $ambiente->id }}">{{ $ambiente->descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" class="form-control" name="" id=""></td>
                                            <td><input type="time" class="form-control" name="" id=""></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Viernes
                                            </td>
                                            <td>
                                                <select data-rel="chosen" id="" name="" class="js-example-basic-single form-control" required="required">
                                                    <option value="">-- Seleccione el ambiente de formaci&oacute;n --</option>
                                                    @foreach($ambientes as $ambiente)
                                                        <option value="{{ $ambiente->id }}">{{ $ambiente->descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" class="form-control" name="" id=""></td>
                                            <td><input type="time" class="form-control" name="" id=""></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Sabado
                                            </td>
                                            <td>
                                                <select data-rel="chosen" id="" name="" class="js-example-basic-single form-control" required="required">
                                                    <option value="">-- Seleccione el ambiente de formaci&oacute;n --</option>
                                                    @foreach($ambientes as $ambiente)
                                                        <option value="{{ $ambiente->id }}">{{ $ambiente->descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" class="form-control" name="" id=""></td>
                                            <td><input type="time" class="form-control" name="" id=""></td>
                                        </tr>
                                        <!-- <tr>
                                            <td>
                                                Domingo
                                            </td>
                                            <td>
                                                <select data-rel="chosen" id="" name="" class="js-example-basic-single form-control" required="required">
                                                    <option value="">-- Seleccione el ambiente de formaci&oacute;n --</option>
                                                    @foreach($ambientes as $ambiente)
                                                        <option value="{{ $ambiente->id }}">{{ $ambiente->descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="time" class="form-control" name="" id=""></td>
                                            <td><input type="time" class="form-control" name="" id=""></td>
                                        </tr> -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                <label class="control-label">Nombre del instructor</label>
                                <select data-rel="chosen" id="" name="" class="js-example-basic-single form-control" required="required">
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
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="button" class="btn btn-success" id="botonGuardar" value="Guardar cambios">
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

    });

</script>

@endsection
