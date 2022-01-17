@extends('templates.devoops')
@section('content')
<section class='content'>
    <div class="row">
        <div class="col-xs-12">
            <div class="box ui-draggable ui-droppable">
                 <div class="box-content">
                    <table class="table">
                    <h3 class="text-center"><b></b></h3>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ficha</th>
                                <th>Programa de formaci&oacute;n</th>
                            </tr>
                        </thead>
                        <tbody id="fichas2">
                        <?php $inicioContador = $contador?>
                            @foreach($fichasVinculadas as $fic)
                                <td>{{$contador++}}</td>
                                <td>{{$fic->fic_numero}}</td>
                                <td>{{$fic->prog_nombre}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($cantidadPaginas > 1)
                        @if($cantidadPaginas <= 10)
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                @if($cantidadPaginas > 1 )
                                    <small style="float:left;">
                                        Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $contadorProgramas }} registros
                                    </small>
                                @endif
                                @for($i=$cantidadPaginas; $i>0; $i--)
                                    <?php
                                        $style='';
                                        if($i == $pagina){
                                            $style=";background:#087b76; color:white;";
                                        }
                                    ?>
                                    <a  class="pagina2" data-url2="{{ url('seguimiento/proyecto/showlineastec') }}" data-pagina2="<?php echo $i; ?>" data-opt2="<?php echo $proyecto;?>" data-cedula="<?php echo $cedula;?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $i }}</button></a>
                                @endfor
                            </div>
                        </div>
                        @else
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <small style="float:left;">
                                    Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $contadorProgramas }} registros
                                </small>
                                <?php
                                    $style='';
                                    if($cantidadPaginas == $pagina){
                                        $style=";background:#087b76; color:white;";
                                    }
                                    $cantidadInicia = 10;
                                    if($pagina >= 10){
                                        if($pagina == $cantidadPaginas){
                                            $cantidadInicia = $pagina;
                                        }else{
                                            $cantidadInicia = ($pagina+1);
                                        }
                                    }
                                ?>
                                @if($pagina < ($cantidadPaginas-1))
                                    <a  class="pagina2" data-url2="{{ url('seguimiento/proyecto/showlineastec') }}" data-pagina2="<?php echo $cantidadPaginas; ?>"  data-opt2="<?php echo $proyecto;?>" data-cedula="{{$cedula}}"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;{{ $style }}">{{ $cantidadPaginas }}</button></a>
                                    <a><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                                @endif
                                @for($i=10; $i>0; $i--)
                                    <?php
                                        $style='';
                                        if($cantidadInicia == $pagina){
                                            $style=";background:#087b76; color:white;";
                                        }
                                    ?>
                                    <a  class="pagina2" data-url2="{{ url('seguimiento/proyecto/showlineastec') }}" data-pagina2="<?php echo $cantidadInicia; ?>" data-opt2="<?php echo $proyecto;?>" data-cedula="{{$cedula}}"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $cantidadInicia }}</button></a>
                                    <?php $cantidadInicia--; ?>
                                @endfor
                                @if($pagina >= 10)
                                    <a><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                                    <a class="pagina2" data-url2="{{ url('seguimiento/proyecto/showlineastec') }}" data-pagina2="<?php echo $cantidadInicia; ?>" data-opt2="<?php echo $proyecto;?>" data-cedula="{{$cedula}}" ><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">1</button></a> 
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
                </div>
            </div>  
        </div>
    </div>
</section>
@endsection