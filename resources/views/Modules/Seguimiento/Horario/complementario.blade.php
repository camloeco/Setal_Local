@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Complementario','Horarios') !!}

<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Complementario</span>
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
                <form id="formComplementario" method="POST" data-url="{{ url('seguimiento/horario/complementario') }}">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token">
                   <div class="row">
                        <div class="col-lg-10 col-lg-push-1 col-md-10 col-md-push-1 col-sm-12 col-xs-12">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>Ambiente</label>
                                <select required class="form-control js-example-basic-single" name="amb_id">
                                    <option value="">Seleccione...</option>
                                    <option value="88">Ambiente externo / Virtual</option>
                                    @foreach($ambientes as $val)
                                    <option value="{{ $val->pla_amb_id }}">{{ $val->pla_amb_descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <label>Instructor</label>
                                <select required class="form-control js-example-basic-single" name="par_identificacion">
                                    <option value="">Seleccione...</option>
                                    @foreach($instructores as $val)
                                    <option value="{{ $val->par_identificacion }}">{{ $val->par_nombres }} {{ $val->par_apellidos }}</option>
                                    @endforeach
                                </select>
                            </div>
						</div>
                        <div class="col-lg-10 col-lg-push-1 col-md-10 col-md-push-1 col-sm-12 col-xs-12">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Fecha inicio</label>
                                <input type='date' class="form-control" name="fecha_inicio" required>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Fecha fin</label>
                                <input type='date' class="form-control" name="fecha_fin" required>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>D&iacute;a</label>
                                <select required class="form-control" name="dia_id">
                                    <option value="">Seleccione...</option>
                                    @foreach($dias as $key => $val)
                                    <option value="{{ $key }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-10 col-lg-push-1 col-md-10 col-md-push-1 col-sm-12 col-xs-12">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Hora Inicio</label>
                                <select required class="form-control" name="hora_inicio">
                                    <option value="">Seleccione...</option>
                                    @for($i=6; $i<=21; $i++)
                                    <option value="{{ $i }}">{{ $i }}:00</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Hora Fin</label>
                                <select required class="form-control" name="hora_fin">
                                    <option value="">Seleccione...</option>
                                    @for($i=7; $i<=22; $i++)
                                    <option value="{{ $i }}">{{ $i }}:00</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label>Descripci&oacute;n del complementario</label>
                                <textarea required class="form-control" placeholder="Escriba..." maxlength="1000" rows="5" name="descripcion_complementario"></textarea>
                            </div>
                        </div>
                        <div style="padding-top:10px;" class="col-lg-12 col-md-12 col-sm-2 col-xs-12 text-center">
                            <input type="submit" class="btn btn-success btn-xs" value="Registrar">
                        </div>
                    </div>
                    <div class="row" style="display:none;">
                        <div id="notificaciones" style="background:#efefef;border: solid 1px;border-radius: 10px;" class="col-lg-6 col-lg-push-3 col-md-6 col-md-push-3 col-sm-8 col-md-push-2 col-xs-12">
                            <div style="margin: 10px 0px 10px 0px;" id="mensaje">
                                <h1>xdd</h1>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection