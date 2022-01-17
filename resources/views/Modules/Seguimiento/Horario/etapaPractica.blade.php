@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Etapa práctica','Horarios') !!}

<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Etapa práctica</span>
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
                <form class="formularios" data-url="{{ url('seguimiento/horario/etapapractica') }}">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token">
                    <div class="row">
                        <div class="col-lg-10 col-lg-push-1 col-md-10 col-md-push-1 col-sm-12 col-xs-12">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <label>Ficha</label>
                                <select required class="form-control js-example-basic-single" name="pla_fic_id">
                                    <option value="">Seleccione...</option>
                                    @foreach($fichas as $val)
                                    <option value="{{ $val->pla_fic_id }}">{{ $val->fic_numero }} - {{ $val->prog_nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <label>Instructor</label>
                                <select required class="form-control js-example-basic-single" name="par_identificacion">
                                    <option value="">Seleccione...</option>
                                    @foreach($instructores as $val)
                                    <option value="{{ $val->par_identificacion }}">{{ $val->par_nombres }} {{ $val->par_apellidos }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <label>Trimestre</label>
                                <select required class="form-control" name="pla_fec_tri_id">
                                    <option value="">Seleccione...</option>
                                    @foreach($trimestres as $val)
                                    <option value="{{ $val->pla_fec_tri_id }}">{{ $val->pla_fec_tri_year }} - {{ $val->pla_fec_tri_trimestre }} - {{ $val->pla_fec_tri_fec_inicio }} - {{ $val->pla_fec_tri_fec_fin }}</option>
                                    @endforeach
                                </select>
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
                            <div style="padding-top:10px;" class="col-lg-12 col-md-12 col-sm-2 col-xs-12 text-center">
                                <input type="submit" class="btn btn-success btn-xs" value="Registrar">
                            </div>
                        </div>
                    </div>
                    <div class="row" style="display:none;">
                        <div id="notificaciones" style="background:#efefef;border: solid 1px;border-radius: 10px;" class="col-lg-6 col-lg-push-3 col-md-6 col-md-push-3 col-sm-8 col-md-push-2 col-xs-12">
                            <div style="margin: 10px 0px 10px 0px;" id="mensaje"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection