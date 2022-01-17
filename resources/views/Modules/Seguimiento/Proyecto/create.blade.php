@extends('templates.devoops')

@section('content')
{!! getHeaderMod('Seguimiento a productos','Creaci&oacute;n del programa de formaci&oacute;n') !!}
<style>
textarea{
    resize:none;
}
</style>
    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Creaci&oacute;n de productos</span>
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

                @if(isset($messages)) 
                    <?php if(isset($messages['mal'])){ ?> 
                        <div id="alert" class="alert alert-danger alert-dismissible  show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <li>{{ $messages['mal']}}</li>   
                        </div>                    
                    <?php }else{ ?>
                        <div id="alert" class="alert alert-success alert-dismissible  show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <li>{{ $messages['ok']}}</li> 
                    <?php } ?>   
                        </div> 
                @endif

                <form action="createproyecto" method="post">
                    <div class="form-group">
                        <label class="control-label col-md-2">C&oacute;digo del proyecto</label>
                        <div class="col-sm-4">
                            <input  class="col-md-4 form-control" type="number" name="pro_codigo" id="pro_codigo" placeholder="Ingrese un codigo del proyecto">
                            <div id="error1"></div>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                            <input  type="hidden" name="_token" id="token" value="<?php echo csrf_token();?>">
                        <label class="control-label col-md-2">Nombre del proyecto</label>
                        <div class="col-sm-12">
                            <textarea class="col-md-4 form-control" type="text" name="pro_nombre" id="pro_nombre" placeholder="Ingrese el nombre del proyecto">{{old('pro_nombre')}}</textarea>
                            <div id="error2"></div><br>
                        </div>
                    </div>
                    <br><br><br><br>
                    <div class="form-group">
                        <label class="control-label col-md-2">Problema</label>
                        <div class="col-sm-12">
                            <textarea  name="pro_problema" id="pro_problema" class="form-control"  rows="4" placeholder="Ingrese el problema del proyecto"></textarea>
                            <div id="error3"></div><br>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">Objectivo general</label>
                        <div class="col-sm-12">
                            <textarea  name="pro_obj_general" id="pro_obj_general" class="form-control" rows="4" placeholder="Ingrese el objetivo general del proyecto"></textarea>
                            <div id="error5"></div><br>
                        </div>
                    </div>
                    <br><br><br><br><br><br>
                    <br><br><br><br><br><br>
                    <div class="form-group">
                        <label class="control-label col-md-2">Objectivo espec&iacute;fico</label>
                        <div class="col-sm-12">
                            <textarea  name="pro_obj_especifico" id="pro_obj_especifico" class="form-control" rows="4" placeholder="Ingrese el objetivo especifico del proyecto"></textarea>
                            <div id="error4"></div><br>
                        </div>
                    </div>
                    <br><br><br><br><br><br><br>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre de Producto</th>
                                <th >Acciones</th>
                            </tr>
                        </thead>
                        <tbody id='tbody_pr'>
                            <tr id='trProducto' style="display:none">
                                <td data-title="count">
                                    <textarea  class="col-md-4 form-control" type="text" name="nombre[]" id="prod_nombre" rows="4" placeholder="Ingrese el producto"></textarea>  
                                </td data-title="count">
                                <td>
                                    <button type="button" class="btn btn-success" id="agregar">+</button>
                                    <button type="button" class="btn btn-danger quitar" id="btn_eliminar_1">-</button>
                                </td>
                            </tr>
                            <?php if(isset($prueba)){  
                                    $arr=count($prueba);
                                }else{
                                    $arr=1;
                                    $val=$arr;
                                    $prueba[0]="";
                                }
                                for ($y=0; $y<$arr; $y++){
                            ?>
                                <tr>
                                    <td data-title="count">
                                        <?php echo $y+1; ?>
                                    </td>
                                    <td data-title="count">
                                        <textarea  class="col-md-4 form-control" type="text" name="nombre[]" id="prod_nombre" rows="4" placeholder="Ingrese el producto">{{$prueba[$y]}}</textarea>  
                                        @if($prueba[$y]=="" && !isset($val))
                                            <div><strong class="text-danger">*Debe de llenar este campo</strong></div><br>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-success" id="agregar">+</button>
                                        @if(isset($y) && $y>0)
                                            <button type="button" class="btn btn-danger quitar" id="btn_eliminar_1">-</button>
                                        @endif
                                    </td>
                                </tr>
                            <?php 
                            }
                            ?>
                        </tbody>
                    </table>
                    <p align="center">
                        <input  class="btn btn-success" type="submit" name="enviar" value="Cargar">
                        <a type="submit" class="btn btn-default" href="">Reiniciar Campos</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    @section('plugins-js')

    <script type="text/javascript">
        $(document).ready(function (){       
            <?php
                if(isset($antiguos)){
                    $f=array();
                    for($r=0;$r<count($antiguos);$r++){
                        if($antiguos[$r]==""){
                            $f[$r]="<strong class='text-danger'>*Debe llenar este campo</strong>";
                        }else{
                            $f[$r]="<strong></strong>";
                        }
                    }
            ?>      
             
                    $('#pro_codigo').attr("value","<?php echo $antiguos[0]; ?>");
                    $("#error1").html("<?php echo $f[0]; ?>");
                    $('#pro_nombre').val("<?php echo $antiguos[1]; ?>");
                    $("#error2").html("<?php echo $f[1]; ?>");
                    $('#pro_problema').val("<?php echo $antiguos[2]; ?>");
                    $("#error3").html("<?php echo $f[2]; ?>");
                    $('#pro_obj_general').val("<?php echo $antiguos[3]; ?>");
                    $("#error4").html("<?php echo $f[3]; ?>");
                    $('#pro_obj_especifico').val("<?php echo $antiguos[4]; ?>");
                    $("#error5").html("<?php echo $f[4]; ?>");
            <?php 
                } 
            ?>

            var fila=$('#trProducto').html();
            $('#trProducto').remove();
            $(document).on('click','#agregar',function(){
                cantidad=$('#tbody_pr').find('tr').length              
                if(cantidad < 6){
                    cantidad++;
                    var columnaContador = '<td id="contadorFila" data-title="count">'+cantidad+'</td>';
                    $('#tbody_pr').append("<tr>" +columnaContador+fila+ "</tr>");
                }            
	        });

            $(document).on("click",".quitar",function(){              
                $(this).parent().parent().remove();
            });
        
        });
    </script>
    @endsection
@endsection