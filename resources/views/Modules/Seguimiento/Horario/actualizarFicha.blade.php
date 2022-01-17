@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Actualizar ficha','Horarios') !!}

<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Actualizar ficha</span>
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
                    <form method="POST" data-url="{{ url('seguimiento/horario/actualizarficha') }}" class="formularios">
                        <div class="col-lg-8 col-lg-push-2 col-md-10 col-md-push-1 col-sm-12 col-xs-12 text-center">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label>Grupos sin asignaci&oacute;n de ficha:</label>
                                    <select required name="fic_numero_vieja" class="form-control js-example-basic-single">
                                        <option value='' >Seleccionar...</option>
                                        @foreach($fichas as $val)
                                            <option value='{{ $val->fic_numero }}' >{{ $val->fic_numero }} - {{ $val->prog_nombre }} - {{ $val->pla_fra_descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label>Fichas sin horario: </label>
                                    <select required name="fic_numero_nueva" class="form-control js-example-basic-single">
                                        <option value='' >Seleccionar...</option>
                                        @foreach($fichasSinHorario as $val)
                                            <option value='{{ $val->fic_numero }}' >{{ $val->fic_numero }} - {{ $val->prog_nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center" style="margin: 10px 0px 0px 0px;">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token">
                            <button class="btn btn-success btn-xs">Actualizar ficha</button>
                        </div>
                        <div class="row" style="display:none;">
                            <div id="notificaciones" style="background:#efefef;border: solid 1px;border-radius: 10px;" class="col-lg-6 col-lg-push-3 col-md-6 col-md-push-3 col-sm-8 col-md-push-2 col-xs-12">
                                <div style="margin: 10px 0px 10px 0px;" id="mensaje">
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