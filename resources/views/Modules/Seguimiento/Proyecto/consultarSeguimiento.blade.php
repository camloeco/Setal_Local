@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Gesti&oacute;n de Proyectos','Todos los Proyectos') !!}

<style>
textarea{
    resize:none;
}
</style>

<section class='content'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
                        <form action="<?php echo url('seguimiento/proyecto/consultar');?>" method="GET">
                            <input type='submit' value="Buscar" class="pull-right">
                            <input type="text" name="valor" id="valor" placeholder="Digite el valor a buscar" class="form-control pull-right" style="width:15em; margin-right: 3px;">
                            <select class="form-control pull-right" style="width:10em;  margin-right: 3px;" name="filtro" id="filtro" required>
                                <option value="">Seleccione...</option>
                                <option value="1">Ficha</option>
                                <option value="2">Programa</option>
                                <option value="3">Coordinador</option>
                            </select>
                        </form>
                        <form action="<?php echo url('seguimiento/proyecto/consultar');?>" method="POST" id="formExport">
                            <a class="btn btn-default" style="border-radius:0.25em;margin:3px; cursor:pointer;" href="{{ url('seguimiento/proyecto/consultar') }}">Limpiar filtro</a>
                            &nbsp;
                            <input type="hidden" name="_token" id="token" value="<?php echo csrf_token();?>">
                            <input type="submit" class="btn btn-success" value="Exportar Proyectos">
                        </form>
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
                                <th># Id</th>
                                <!-- <th>Codigo</th> -->
                                <th>Coordinador</th>
                                <th>Ficha</th>
                                <th>Programa</th>
                                <th>Proyecto</th>
                                <th>Observaci&oacute;n</th>
                                <th colspan="2" >Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $inicioContador = $contador; ?>
                            @foreach ($planefichas as $plane)
                            <tr style="font-size:13px;">
                                <td data-title="count">{{ $contador++ }}</td>
                                <td data-title="codigo"><label>{{ $plane->par_nombres }}</label>&nbsp;<label>{{ $plane->par_apellidos }}</label></td>
                                <td data-title="codigo">{{ $plane->fic_numero }}</td>
                                <td data-title="codigo">{{ $plane->prog_nombre }}</td>
                                <td data-title="codigo">{{ $plane->pro_nombre }}</td>
                                <td data-title="codigo"><label>{{ $plane->fic_observacion }}</label></td>
                                <td data-title="Ver">
                                    <center>
                                        <a href="#" data-url="{{ url("seguimiento/proyecto/show/".$plane->pla_fic_id) }}" data-toggle="modal" data-target="#modalVer" class="ajax-link" title="Ver">
                                            Ver Detalle
                                        </a>
                                    </center>
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
                                    <a href="{{ url('seguimiento/proyecto/consultar') }}?pagina=<?php echo $i; ?>&valor=<?php echo $valor; ?>&filtro=<?php echo $filtro; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $i }}</button></a>

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
                                    <a href="{{ url('seguimiento/proyecto/consultar') }}?pagina=<?php echo $cantidadPaginas; ?>&valor=<?php echo $valor; ?>&filtro=<?php echo $filtro; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;{{ $style }}">{{ $cantidadPaginas }}</button></a>
                                    <a href=""><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                                @endif
                                @for($i=10; $i>0; $i--)
                                    <?php
                                        $style='';
                                        if($cantidadInicia == $pagina){
                                            $style=";background:#087b76; color:white;";
                                        }
                                    ?>
                                    <a href="{{ url('seguimiento/proyecto/consultar') }}?pagina=<?php echo $cantidadInicia; ?>&valor=<?php echo $valor; ?>&filtro=<?php echo $filtro; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $cantidadInicia }}</button></a>
                                    <?php $cantidadInicia--; ?>
                                @endfor
                                @if($pagina >= 10)
                                    <a href=""><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                                    <a href="{{ url('seguimiento/proyecto/consultar') }}?pagina=1&valor=<?php echo $valor; ?>&filtro=<?php echo $filtro; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">1</button></a> 
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
<div id="modalVer" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><strong>Detalle</strong></h4>
            </div>
            <div class="modal-body" id="modalBody">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('plugins-js')
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on("click", "a[data-toggle='modal']", function () {
            //e.prevntDefault();
            var destino = $(this).attr("data-target");
            var url = $(this).attr("data-url");

            $(destino + " .modal-body").html("Cargando ...");

            $.ajax({
                url: url,
                type: "GET",
                success: function (data) {
                    $(destino + " .modal-body").html(data);
                }
            });
        });

      
        $(document).on("click",".aprobado",function(){
            var vector = new Array();
            var limite = $('#limite').val();

            for (i = 1; i <= limite; i++) {
                var vectores = $("[name='aprobado"+i+"']").map( function(){
                    if( $(this).is(':checked') ){
                        return this.value; 
                    }else{
                        return null;   
                    }  
                }).get();
                vector[i]=vectores;
            }
            $('#evaluacion').attr("value", vector);
        });

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