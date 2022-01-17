@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Asignar coordinación','Usuario') !!}

<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Asignar coordinación</span>
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
                <form class="formularios" data-url="{{ url('users/users/asignarcoordinador') }}">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token">
                    <div class="row">
                        <div class="col-lg-10 col-lg-push-1 col-md-10 col-md-push-1 col-sm-12 col-xs-12">
                            <div class="col-lg-6 col-md-8 col-sm-10 col-xs-12">
                                <label>Coordinador</label>
                                <select required class="form-control js-example-basic-single" name="par_identificacion_coordinador">
                                    <option value="">Seleccione...</option>
                                    @foreach($coordinadores as $val)
                                    <option value="{{ $val->par_identificacion }}">{{ $val->par_nombres }} {{ $val->par_apellidos }}</option>
                                    @endforeach
                                </select>
                            </div>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label>Instructor(es)</label>
                                <select required class="form-control js-example-basic-multiple" name="par_identificacion_instructor[]" multiple="multiple">
                                    <option value="">Seleccione...</option>
                                    @foreach($instructores as $val)
                                    <option value="{{ $val->par_identificacion }}">{{ $val->par_nombres }} {{ $val->par_apellidos }}</option>
                                    @endforeach
                                </select>
                            </div>
							<div style="padding-top:10px;" class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
                                <input type="submit" class="form-control btn btn-success" value="Actualizar">
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