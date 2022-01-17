@extends('templates.devoops')
@section('content')
<style>
    .activo{
        color:white;
        background:#0CB7F2;
    }
    .otro{
        border:2px solid #0CB7F2;
    }
</style>
<section class='content'>
    <div class="row">
        <div class="col-xs-12">
            <div class="box ui-draggable ui-droppable">
                 <div class="box-content">
                    <table class="table table-striped table-hover">
                        <h3 class="text-center"><b>{{$coordinador}}</b></h3><br>
                        <center>
                        <div class="row">
                            <input type="hidden" name="opt" id="opt" value={{$opt}} data-url="{{url('seguimiento/proyecto/detallelineas')}}">
                            <button class="btn nivel otro activo" value="4" id="4">Tecnologo</button>
                            <button class="btn nivel otro" value="2" id="2">Tecnico</button>
                            @if($cedula == 67020609)
                            <button class="btn nivel otro" value="1" id="1">Operario</button>
                            @endif
                        </div>
                        <center>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Codigo</th>
                                <th>Nombre del proyecto</th>
                                <th>Fichas asociadas en etapa lectiva</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                        <?php $contador=1; ?>
                            @foreach($proyectos_nivel as $pro)
                                <tr>
                                    <td>{{$contador++}}</td>
                                    <td>{{$pro->pro_codigo}}</td>
                                    <td>{{$pro->pro_nombre}}</td>
                                    <?php $c=0; ?>
                                        @foreach($fichas as $fic)
                                                @if($fic->fic_proyecto == $pro->pro_codigo)
                                                <?php $c++; ?>
                                                @endif
                                        @endforeach
                                    <td class="text-center">
                                        @if($c == 0)
                                            0
                                        @else
                                            <a id="lin" data-url="{{url('seguimiento/proyecto/showlineastec')}}" coordinador ="{{$cedula}}" data-proyecto="{{$pro->pro_codigo}}" data-toggle="modal" data-target="#modalLineasTec" class="ajax-link" title="Ver" style="cursor: pointer;">{{$c}}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<div id="modalLineasTec" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-success" >
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="text-center"><strong>Fichas</strong></h3>
            </div>
            <div class="modal-body" id="modalBody">

            </div>
            <div class="modal-footer bg-success">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection