@extends('templates.devoops')


@section('content')


@if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))

<div class="col-xs-12 col-sm-12">
    <div class="box ui-draggable ui-droppable">
        <div class="box-header">
            <div class="box-name ui-draggable-handle">
                <i class="fa fa-search"></i>
                <span>Detalle</span>
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
@endif
            <div class="row">
                <div class="col-md-12">
                    <div class="box-body">
                        <dl class="dl-horizontal">
                        <div class="col-lg-12">
                            <p align="right"><strong>&Uacute;ltimo Seguimiento: {{$fecha_vieja}}</strong></p>
                        </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
                            <br>
                                <form action="show" method="post">
                                    <label>Observaciones:</label>
                                    <input type="hidden" name="_token" id="token" value="<?php echo csrf_token();?>">
                                    @foreach($ficha as $fi)
                                        <textarea class="form-control" name="observacion" id="observacion" cols="40" rows="4">{{$fi->fic_observacion}}</textarea><br>
                                        <input type="hidden" name="ficha" value="{{ $fi->fic_numero }}">
                                    @endforeach 
                                    <p align="right"><input type="submit" class="btn btn-success" value="Actualizar Seguimineto"></p>

                                    <input type="hidden" name="evaluacion" id="evaluacion" value="{{$lista3}}">
                                </form>
                                <hr>
                                <p align="left" ><strong>Nivel de formaci&oacute;n: <span class="text-danger" >{{mb_strtoupper($ficha[0]->niv_for_nombre)}}</span></strong></p>
                                <p align="left" ><strong>Programa: <span class="text-danger" >{{$ficha[0]->prog_nombre}}</span></strong></p>
                                <p align="left" ><strong>Ficha: <span class="text-danger" >{{$ficha[0]->fic_numero}}</span></strong></p>
                                <p align="left" ><strong>Trimestre actual de la ficha: <span class="text-danger" >{{$triNombre}}</span></strong></p>
                                <br>
                                <center><h4><strong>Productos</strong></h4></center>
                                <table class="table table-striped table-hover" border="1">
                                    <thead>
                                        <tr>
                                            <th style="Display:none;"></th>
                                            <th>#</th>
                                            <th>Descripcion</th>
                                            <th class="text-center">Aprobado</th>
                                            <th class="text-center">No Aprobado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $c=1;  $p=0; $t=0; ?>
                                        @foreach ($productos as $prod)
                                            <tr style="font-size:13px;">
                                                <?php $p++; ?>
                                                <td data-title="count" style="Display:none;"></td>
                                                <td data-title="count">{{ $c++ }}</td>
                                                <td data-title="count"><label>{{ $prod->prod_nombre }}</label></td>
                                                @if($array3[$t] == "0")
                                                    <td data-title="count" class="text-center"><input type="radio" checked name="aprobado{{$p}}" id="aprobado" class="aprobado" value="0"></td>
                                                    <td data-title="count" class="text-center"><input type="radio" name="aprobado{{$p}}" id="aprobado" class="aprobado" value="1"></td>
                                                @elseif($array3[$t] == "1")
                                                    <td data-title="count" class="text-center"><input type="radio" name="aprobado{{$p}}" id="aprobado" class="aprobado" value="0"></td>
                                                    <td data-title="count" class="text-center"><input type="radio" checked name="aprobado{{$p}}" id="aprobado" class="aprobado" value="1"></td>
                                                @elseif($array3[$t] == "2")
                                                    <td data-title="count" class="text-center"><input type="radio" name="aprobado{{$p}}" id="aprobado" class="aprobado" value="0"></td>
                                                    <td data-title="count" class="text-center"><input type="radio" name="aprobado{{$p}}" id="aprobado" class="aprobado" value="1"></td>
                                                @endif
                                                <?php $t++; ?>
                                            </tr>
                                        @endforeach
                                        <input type="hidden" name="limite" id="limite" value="{{$limite}}">
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
                            <hr>
                                <center><h4><strong>Instructores</strong></h4></center>
                                <table class="table table-striped table-hover" border="1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Cedula</th>
                                            <th>Nombre</th>
                                            <th>Apellidos</th>
                                            <th>Correo Electronico</th>
                                            <th>Telefono</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $y=1; ?>
                                        @foreach ($participantes as $parti)
                                            @if($parti->tipo_instructor == "tecnico" && $parti->estado == '1')
                                            <tr style="font-size:13px;">
                                                <td data-title="count">{{ $y++ }}</td>
                                                <td data-title="count">{{ $parti->par_identificacion }}</td>
                                                <td data-title="count">{{ $parti->par_nombres }}</td>
                                                <td data-title="count">{{ $parti->par_apellidos }}</td>
                                                <td data-title="count">{{ $parti->par_correo }}</td>
                                                <td data-title="count">{{ $parti->par_telefono }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>         
                            </div>
                        </dl>
                    </div><!-- /.box-body -->
                </div>
            </div>
@if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
        </div><!-- /.box-body -->
    </div>
</div>
@endif 
@endsection

