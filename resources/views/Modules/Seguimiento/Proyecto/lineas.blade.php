
@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Gesti&oacute;n de Programas','Lineas Tecnologicas') !!}

<section class='content'>
    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <div class="box ui-draggable ui-droppable">
                <div class="box-header">
                    <div class="box-name ui-draggable-handle">
                        <i class="fa fa-table"></i>
                        <span>Proyectos por linea tecnologica</span>
                    </div>
                </div>
                 <div class="box-content">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr style="font-weight:bold">
                                    <th colspan=2 >Nivel de formaci&oacute;n</th>
                                    <th class="text-center">
                                        <center><span>Mto Mecatr&oacute;nico</span></center>
                                    </th>
                                    <th class="text-center">
                                        <center>Transversalidad Tecnologica</center>
                                    </th>
                                    <th class="text-center">
                                        <center>Comunicaci&oacute;n Digital</center>
                                    </th>
                                    <th class="text-center">
                                        <center>Vestuario Inteligente</center>
                                    </th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                   <tr>
                                        <?php
                                        $acu_fila=$acu_columna=0;
                                        for ($i=0; $i < (count($vec)-1); $i++) {
                                            echo "<tr>";
                                            echo "<td></td>";
                                            echo "<td>".$nivel_formacion[$i]."</td>";
                                            for ($j=0; $j <= (count($vec[$i])); $j++) {
                                                if (isset($vec[$j][$i])) {
                                                  echo "<td class='text-center'>".$vec[$j][$i]."</td>";
                                                  $acu_fila=$acu_fila+$vec[$j][$i];
                                                }
                                            }
                                            echo "<td>".$acu_fila."</td>";
                                            $acu_columna=$acu_columna+$acu_fila;
                                            $acu_fila=0;
                                            echo "</tr>";
                                        }
                                        ?>
                                   </tr>
                                  <tr style="font-weight:bold">
                                    <td></td>
                                    <td class="font-weight-bold">Total</td>
                                    <?php for ($i=0; $i <count($total); $i++) {
                                      echo "<td class='text-center'>".$total[$i]."</td>";
                                    }
                                    ?>
                                    <td>{{$acu_columna}}</td>
                                    </tr>
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
    <button class="btn btn-success" id="activar">Buscar</button>&nbsp;&nbsp;&nbsp;
    <a href="{{url('seguimiento/proyecto/lineas')}}" class="btn btn-default" id="activar">Limpiar</a>
    <div class="col-md-4">
        <select style="HEIGHT: 40px" class="form-control" name="lineas" id="lineas" data-url="{{ url('seguimiento/proyecto/detallelineas')}}">
            <option value="0">Seleccione Linea Tecnologica</option>
            <option value="1">Mto Mecatr&oacute;nico</option>
            <option value="2">Transversalidad Tecnologica</option>
            <option value="3">Comunicaci&oacute;n Digital</option>
            <option value="4">Vestuario Inteligente</option>
        </select>
   </div>
   <div id="tecnologicas"></div>
</section>
@endsection
@section('plugins-js')
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on("click", "#activar", function () {
            var opcion = parseInt($("#lineas").val());
            var url = $("#lineas").attr("data-url");
            var opt =$("#lineas").val();
            if(opcion >=1 && opcion <=4){
                $.ajax({
                    url: url,
                    type: "GET",
                    data: "opt=" + opt,
                    success: function (data) {
                        $("#tecnologicas").html(data);
                    }
                });
            }else{
                location.reload();
            }   
        });


        $(document).on("click", ".pagina", function () {
            var url = $(this).attr("data-url");
            var data_pagina = $(this).attr("data-pagina");
            var opt2 = $(this).attr("data-opt");
            $.ajax({
                url: url,
                type: "GET",
                data: "pagina=" + data_pagina + "&opt=" + opt2,
                success: function (data) {
                    $("#tecnologicas").html(data);
                }
            });
          
        });

        $(document).on("click", "a[data-toggle='modal']", function () {
            var destino = $(this).attr("data-target");
            var url = $(this).attr("data-url");
            var proyecto = $(this).attr("data-proyecto");
            var coordinador = $(this).attr("coordinador");
            $(destino + " .modal-body").html("Cargando ...");
            $.ajax({
                url: url,
                type: "GET",
                data: "proyecto=" + proyecto+"&cedula="+coordinador,
                success: function (data) {
                    $(destino + " .modal-body").html(data);
                }
            });
        });  

        $(document).on("click", ".pagina2", function () {
            var url = $(this).attr("data-url2");
            var data_pagina = $(this).attr("data-pagina2");
            var opt2 = $(this).attr("data-opt2");
            var cedula = $(this).attr("data-cedula");
            $.ajax({
                url: url,
                type: "GET",
                data: "pagina=" + data_pagina + "&proyecto=" + opt2+"&cedula="+cedula,
                success: function (data) {
                    $("#modalBody").html(data);
                }
            });
        });
  
        $(document).on("click", ".nivel", function (){
            var nivel = $(this).val();
            var opt = $("#opt").val();
            var url = $("#opt").attr("data-url");
            $.ajax({
                url: url,
                type: "GET",
                data: "nivel="+ nivel+"&opt="+opt,
                success: function(data) {
                    $(".nivel").removeClass("activo");
                    $("#"+nivel+"").addClass("activo");
                    $("#tbody").html(data);
                }
            });
        });

    });
</script>
@endsection