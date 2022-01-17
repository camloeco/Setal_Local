@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Gesti&oacute;n de Proyectos','Todos los Proyectos') !!}


<section class='content'>

    <div class="row">
        <div class="col-xs-12">
            <div class="box ui-draggable ui-droppable">

                <div class="box-header">
                    <div class="box-name ui-draggable-handle">
                        <i class="fa fa-table"></i>
                        <span>Listado de todos los Proyectos</span>
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
                 
                    <div class="row" style="margin: 0px 0px 15px 0px;">
                        <form action="<?php echo url('seguimiento/proyecto/index');?>" method="GET">
                            <input type='submit' value="Buscar" class="pull-right">
                            <input type="text" name="valor" id="valor" placeholder="Digite el valor a buscar" class="form-control pull-right" style="width:15em; margin-right: 3px;">
                            <select class="form-control pull-right" style="width:10em;  margin-right: 3px;" name="filtro" id="filtro" required>
                                <option value="">Seleccione...</option>
                                <option value="1">Codigo</option>
                                <option value="2">Nombre</option>
                            </select>
                            <input type="hidden" name="_token" value="<?php echo csrf_token();?>">
                        </form>
                        <span style="border-radius:0.25em; width:10em; height:1.90em;margin-right: 3px; cursor:pointer;" class="input-group-addon"><a href="{{ url('seguimiento/proyecto/index') }}">Limpiar filtro</a></span>
            
                        <p style="display:none;">Listado de todos los <code>Proyectos</code> existentes en la base de datos. 
                            <ul style="display:none;">
                                <li><small>bara activar un registro, presione sobre el estado <span class="tag tag-danger" title="Activar">Inactivo</span>
                                    </small>
                                </li>
                                <li><small>bara Inactivar un registro, presione sobre el estado <span class="tag tag-info" title="Inactivar">Activo</span>
                                    </small>
                                </li>
                            </ul>
                        </p>
                    </div>
                    
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>C&oacute;digo</th>
                                    <th>Nombre del Proyecto</th>
                                    <th>Productos</th>
                                    <th colspan="2" >Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $inicioContador = $contador; ?>
                                @foreach ($proyectos as $pro)

                                <tr style="font-size:13px;">
                                    <td data-title="count">{{ $contador++ }}</td>
                                    <td data-title="codigo">{{ $pro->pro_codigo }}</td>
                                    <td data-title="Nombre">{{ $pro->pro_nombre }}</td>

                                    <?php $c=0; ?>

                                    @foreach ($productos as $prod)
                                        @if($pro->pro_id == $prod->pro_id)
                                        
                                        <?php $c++; ?>

                                        @endif
                                    @endforeach

                                    <td data-title="Nombre" class="text-center">
                                        <?php echo $c; ?>
                                    </td>
                                    <td data-title="Editar" class="text-center">
                                        <a href="{{ url("seguimiento/proyecto/edit/".$pro->pro_id ) }}" class="ajax-link" title="Editar">
                                            Ver / Editar
                                        </a>
                                    </td>
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
                                            Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $contadorProyectos }} registros
                                        </small>
                                    @endif
                                    @for($i=$cantidadPaginas; $i>0; $i--)
                                        <?php
                                            $style='';
                                            if($i == $pagina){
                                                $style=";background:#087b76; color:white;";
                                            }
                                        ?>
                                        <a href="{{ url('seguimiento/proyecto/index') }}?pagina=<?php echo $i; ?>&valor=<?php echo $valor; ?>&filtro=<?php echo $filtro; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $i }}</button></a>
                                    @endfor
                                </div>
                            </div>
                            @else
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <small style="float:left;">
                                        Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $contadorProyectos }} registros
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
                                        <a href="{{ url('seguimiento/proyecto/index') }}?pagina=<?php echo $cantidadPaginas; ?>&valor=<?php echo $valor; ?>&filtro=<?php echo $filtro; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;{{ $style }}">{{ $cantidadPaginas }}</button></a>
                                        <a href=""><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                                    @endif
                                    @for($i=10; $i>0; $i--)
                                        <?php
                                            $style='';
                                            if($cantidadInicia == $pagina){
                                                $style=";background:#087b76; color:white;";
                                            }
                                        ?>
                                        <a href="{{ url('seguimiento/proyecto/index') }}?pagina=<?php echo $cantidadInicia; ?>&valor=<?php echo $valor; ?>&filtro=<?php echo $filtro; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $cantidadInicia }}</button></a>
                                        <?php $cantidadInicia--; ?>
                                    @endfor
                                    @if($pagina >= 10)
                                        <a href=""><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                                        <a href="{{ url('seguimiento/proyecto/index') }}?pagina=1<?php echo $pro_id; ?>&valor=<?php echo $valor; ?>&filtro=<?php echo $filtro; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">1</button></a> 
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
@section('plugins-js')
<script type="text/javascript">
    $(document).ready(function () {

       //sostener el filtrado

       var filtro = parseInt(<?php echo $filtro; ?>);
       
        for (j = 0; j<=filtro; j++) {
           if(j == filtro){
            $("#filtro option[value="+ j +"]").attr("selected",true);
           }
        }

        var valor = "<?php echo $valor; ?>";
         $("#valor").attr("value", valor);

       // 
    });
</script>
@endsection