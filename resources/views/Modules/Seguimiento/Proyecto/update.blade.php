@extends('templates.devoops')

@section('content')
    @foreach($proyectos as $pro)
    
    {!! getHeaderMod('Seguimiento a productos','Editar el proyecto de formaci&oacute;n') !!}
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
                    <span>Editar Proyecto</span>
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
                <form action="edit" method="post">
                    <div class="form-group">
                        <label class="control-label col-md-2">C&oacute;digo del proyecto</label>
                        <input class="col-md-4 form-control" type="hidden" name="pro_id" id="pro_id" value="{{$pro->pro_id}}">      
                        <div class="col-sm-4">
                            <input required class="col-md-4 form-control" type="number" name="pro_codigo" id="pro_codigo" value="{{$pro->pro_codigo}}">
                            @if($pro->pro_codigo=='')
                                <div id="error1" class="text-danger"><strong>El campo c&oacute;digo del proyecto es obligatorio</strong></div><br>
                            @endif
                        </div>
                        <input type="hidden" name="_token" id="token" value="<?php echo csrf_token();?>">
                    </div><br><br>
                    <div class="form-group">
                        <label class="control-label col-md-2">Nombre del proyecto</label>
                        <div class="col-sm-12">
                            <textarea required name="pro_nombre" class="col-md-4 form-control" rows="2">{{$pro->pro_nombre}}</textarea><br><br>
                            @if($pro->pro_nombre=='')
                                <div id="error2" class="text-danger"><strong>El campo nombre del proyecto es obligatorio</strong></div><br>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">Problema</label>
                        <div class="col-sm-12">
                            <textarea required name="pro_problema" id="pro_problema" class="form-control" rows="4">{{$pro->pro_problema}}</textarea>
                            @if($pro->pro_problema=='')
                                <div id="error3" class="text-danger"><strong>El campo problema del proyecto es obligatorio</strong></div><br>
                            @endif
                        </div>    
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">Objetivo general</label>
                        <div class="col-sm-12">
                            <textarea required name="pro_obj_general" id="pro_obj_general" class="form-control" rows="4" >{{$pro->pro_obj_general}}</textarea>
                            @if($pro->pro_obj_general=='')
                                <div id="error5" class="text-danger"><strong>El campo objetivo especifico del proyecto es obligatorio</strong></div><br>
                            @endif
                        </div>
                    </div>                        
                    <div class="form-group">
                        <label class="control-label col-md-2">Objetivo espec&iacute;fico</label>
                        <div class="col-sm-12">
                            <textarea  required name="pro_obj_especifico" id="pro_obj_especifico" class="form-control" rows="4" >{{$pro->pro_obj_especifico}}</textarea>
                            @if($pro->pro_obj_especifico=='')
                                <div id="error4" class="text-danger"><strong>El campo objetivo general del proyecto es obligatorio</strong></div><br>
                            @endif
                        </div>
                    </div>
              
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
                                <textarea required class="col-md-4 form-control" type="text" name="nombre[]" id="prod_nombre" rows="4"></textarea>  
                                <input type="hidden" name="codigo[]" id="prod_codigo" value="">
                            </td>
                            <td>
                                <button type="button" class="btn btn-success" id="agregar">+</button>
                                <button type="button" class="btn btn-danger quitar" name="codigo" id="btn_eliminar_1" value="">-</button>
                            </td>
                        </tr>
                    
                        @if($cc > 0)
                            @foreach($productos as $key => $prod)
                                <tr>
                                    <td id="td" data-title="count">
                                        <span id="contadorViejos">{{($indice=$key+1)}}</span>
                                    </td>
                                    <td data-title="count">
                                        <textarea required class="col-md-4 form-control" type="text" name="nombre[]" id="prod_nombre" rows="4">{{$prod->prod_nombre}}</textarea> 
                                        @if($prod->prod_nombre=='')
                                            <div id="error6" class="text-danger"><strong>*Debe de llenar este campo</strong></div><br>
                                        @endif
                                        <input type="hidden" name="codigo[]" id="prod_codigo" value="{{$prod->prod_codigo}}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-success" id="agregar">+</button>
                                       @if($cc>1 && $key>0)
                                        <button type="button" class="btn btn-danger quitar" name="codigo" id="btn_eliminar_1" value="{{$prod->prod_codigo}}">-</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td id="td" data-title="count">
                                    <span>1</span>
                                </td>
                                <td data-title="count">
                                    <textarea required class="col-md-4 form-control" type="text" name="nombre[]" id="prod_nombre" rows="4"></textarea>
                                    @if($prod->prod_nombre=='')
                                        <div id="error6" class="text-danger"><strong>*Debe de llenar este campo</strong></div><br>
                                    @endif
                                    <input type="hidden" name="codigo[]" id="prod_codigo" value="">
                                <td>
                                    <button type="button" class="btn btn-success" id="agregar">+</button>
                                </td>
                            </tr>
                        @endif
    				</tbody>
                </table>
            </div>
            <p align="center">
                <input class="btn btn-success" type="hidden" name="control_quitar" id="control_quitar" value="">
                <input class="btn btn-success" type="submit" name="enviar" value="Editar">
            </p>
            </form>
        </div>
    </div>
     
    @endforeach
    @section('plugins-js')
    <script type="text/javascript">
        $(document).ready(function (){      
    
            $("#enviar").click(function(){
                var url = $(this).attr('data-url');
                var pro_codigo = $('#pro_codigo').val();
                var pro_nombre = $('#pro_nombre').val();
                var pro_problema = $('#pro_problema').val();
                var pro_justificacion = $('#pro_justificacion').val();
                var pro_obj_general = $('#pro_obj_general').val();
                var pro_obj_especifico = $('#pro_obj_especifico').val();
            
                var productos= $("textarea[name='nombre[]']").map( function(){
                    return this.value;     
                }).get();
    
                var token = $('#token').val();
                var proyecto = new Array;
                    proyecto[0]=pro_codigo;
                    proyecto[1]=pro_nombre;
                    proyecto[2]=pro_problema;
                    proyecto[3]=pro_justificacion;
                    proyecto[4]=pro_obj_general;
                    proyecto[5]=pro_obj_especifico;
    
                $.ajax({
                    url : url,
                    type: "POST",
                    data: "proyecto=" + proyecto + "&productos=" + productos,
                    success:function(data){}
                });
            });
    
            var fila=$('#trProducto').html();
            $('#trProducto').remove();
            $(document).on('click','#agregar',function(){
                var cantidad = $('#tbody_pr').find('tr').length;
                alert(cantidad);
                if(cantidad < 6){
                    cantidad++;
                    var columnaContador = '<td id="contadorFila" data-title="count">'+cantidad+'</td>';
                    $('#tbody_pr').append("<tr>" +columnaContador+fila+ "</tr>");
                }
            });
            
            var list =" ";
            $(document).on("click",".quitar",function(){
                var quitar = $(this).val();
                list = list + quitar + ",";
                $('#control_quitar').attr("value",list);
                $(this).parent().parent().remove();
            });
    
        });
    </script>
    @endsection
@endsection