@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Restricciones','Horarios') !!}

<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Restricciones</span>
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
                <div class="row">
                    <form method="POST" id="formRestriccion" data-url="{{ url('seguimiento/horario/restriccion') }}">
                        <div class="col-lg-8 col-lg-push-2 col-md-10 col-md-push-1 col-sm-12 col-xs-12 text-center">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <label>Restricci&oacute;n</label>
                                    <select name="amb_id" class="form-control ">
                                        <option value='' >Seleccionar...</option>
                                        @foreach($ambientesRestriccion as $val)
                                            <option value='{{ $val->pla_amb_id }}' >{{ $val->pla_amb_descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <label>Instructor</label>
                                    <select name="par_identificacion" class="form-control js-example-basic-single">
                                        <option value='' >Seleccionar...</option>
                                        @foreach($instructores as $val)
                                            <option value='{{ $val->par_identificacion }}' >{{ $val->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <label>D&iacute;a</label>
                                    <select name="pla_dia_id" class="form-control">
                                        <option value='' >Seleccionar...</option>
                                        @foreach($dias as $val)
                                            <option value='{{ $val->pla_dia_id }}' >{{ $val->pla_dia_descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <label>Hora inicio</label>
                                    <select id="hora_inicio" name="hora_inicio" class="form-control validarHora">
                                        <option value="" >Seleccionar...</option>
                                        @for($i=6; $i<=21; $i++)
                                            <option value='{{ $i }}'>{{ $i }}:00</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <label>Hora fin</label>
                                    <select id="hora_fin" name="hora_fin" class="form-control validarHora">
                                        <option value="" >Seleccionar...</option>
                                        @for($i=7; $i<=22; $i++)
                                            <option value='{{ $i }}'>{{ $i }}:00</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="col-lg-8 col-lg-push-2 col-md-8 col-md-push-2 col-sm-8 col-sm-push-2 col-xs-12 text-center">
                                <label>Trimestre(s): </label><br>
                                <small>Año - N&uacute;mero trimestre - Fecha inicio - Fecha fin</small>
                                <select class="js-example-basic-multiple" name="id[]" multiple="multiple" required>
                                    <option value = "todas">Todas los trimestres</option>
                                    @foreach($trimestres as $val)		
                                        <option value="{{ $val->pla_fec_tri_id }}">{{$val->pla_fec_tri_year}} - {{$val->pla_fec_tri_trimestre}} - {{ $val->pla_fec_tri_fec_inicio }} - {{ $val->pla_fec_tri_fec_fin }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="margin: 10px 0px 0px 0px;">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token">
                            <a href="" class="btn btn-danger btn-xs">Cancelar</a>
                            <button class="btn btn-success btn-xs">Registrar</button>
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
</div>
@endsection