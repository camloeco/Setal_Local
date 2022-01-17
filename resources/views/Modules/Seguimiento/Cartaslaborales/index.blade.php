@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Cartas laborales','Cartas laborales') !!}
<style>
    .fa{ transform:scale(1.2); }
    .cuadro{
        background:rgb(255, 0, 0, 0.9);
        width: 280px;
        border-radius: 5px;
        box-shadow: 2px 5px 10px black;
        float:right;
        z-index: 10;
        position: fixed;
        margin-left: 60%;
        color:white;
    }
    .cuadro-contenido{
        padding:15px;
    }
    .cuadro-header{
        border-width:1px;
        border-height: 1px;
        padding: 5px;
    }
    .cuadro-header span{
        float:right;
        cursor:pointer;
    }
    .cuadro-header span:hover{
        color:black;
    }
    .cuadro-mensaje{
        padding: 5px;
        word-wrap: break-word;
    }
    .cuadro-footer{
        border: 1px solid black;
        width:100%;
        height:15px;
        background:rgba(255,255,255,0.7);
        border-radius: 5px;
    }
    .barra{
        border-radius: 1px;
        background:rgba(0,0,255, 0.5);
        height:13px;
        width:0%;
        animation: barra 5s linear alternate both;
    }
    @keyframes barra {
    0%{
        width:0%;
    }
    25%{
        width:25%;
    }
    50%{
        width:50%;
    }
    75%{
        width:75%;
    }
    100%{
        width:100%;
    }
}
</style>
<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-table"></i>
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
            @if(isset($_SESSION['mensaje']))
            <div class="cuadro">
                <div class="cuadro-contenido">
                    <div class="cuadro-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                        </svg>
                        <label>Setalpro le informa que:</label>
                        <span id="cuadro-closed">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="cuadro-mensaje">
                        <p>{{$_SESSION['mensaje']}}</p>
                    </div>
                    <div class="cuadro-footer">
                        <div class="barra">&nbsp;</div>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
            @endif
            <div class="box-content">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-6" style="float:right;padding-bottom: 10px;">
                                <form method="GET" action="{{ url('seguimiento/cartaslaborales/index') }}">
                                    <div class="input-group input-group-xs">
                                    <span style="cursor:pointer;" class="input-group-addon"><a href="{{ url('seguimiento/cartaslaborales/index') }}">Limpiar filtro</a></span>
                                        @if($par_identificacion == '')
                                            <input autocomplete="off" required style="border: 1px solid #ccc;padding: 2px 12px;width: 260px;" class="form-control" list="browsers" name="par_identificacion" placeholder="Escriba nombre o documento...">
                                        @else
                                            <input autocomplete="off" value="{{ $par_identificacion }}" required style="border: 1px solid #ccc;padding: 2px 12px;width: 260px;" class="form-control" list="browsers" name="par_identificacion" placeholder="Escriba nombre o documento...">
                                            <?php $par_identificacion = '&par_identificacion='.$par_identificacion?>
                                        @endif
                                        <datalist id="browsers">
                                            @foreach($participantes as $par)	
                                                <option value="{{$par->par_id_instructor}}">{{$par->nombreCompleto}}</option>
                                            @endforeach
                                        </datalist>
                                        <input style="border: 1px solid #ccc;border-radius: 0px 4px 4px 0px;background: #eee;height: 26px;color: #087b76;" type="submit" value="Buscar">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="padding:1px;">#</th>
                                    <th style="padding:0px 0px 0px 5px;font-size:13px;">Documento</th>
                                    <th style="padding:1px;font-size:13px;text-align:center;">Contratista</th>
                                    <th style="padding:1px;font-size:13px;text-align:center;">Año</th>
                                    <th style="padding:1px;font-size:13px;text-align:center;" colspan="2">Descargar</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($contratos as $con)
                                <tr>
                                    <th style="padding:5px;font-size:13px;">{{ $contador++ }}</th>
                                    <td style="padding:0px 0px 0px 5px;font-size:13px;vertical-align: middle;">{{ $con->par_id_instructor }}</td>
                                    <td style="padding:0px 0px 0px 5px;font-size:13px;vertical-align: middle;">{{ $con->nombreCompleto }}</td>
                                    <td style="padding:5px;font-size:13px;vertical-align: middle;width:125px;">
                                    <select class="form-control anio_carta" style="width:125px;" data-url="{{ url('seguimiento/cartaslaborales/descargarcartalaboral') }}?par_identificacion={{ $con->par_id_instructor }}&word=si" data-descarga="{{$contador - 1}}">
                                        <option value="">Seleccione...</option>
                                        @foreach($anios as $an)
                                            <option value="{{$an->con_anio_contrato}}">{{$an->con_anio_contrato}}</option>
                                        @endforeach
                                    </select>
                                    </td>
                                    <td style="padding:5px;text-align:center;"><a style="cursor:pointer;text-decoration:none;" id="descarga_{{$contador - 1}}" class="activarModal is-invalid" title="Debe seleccionar el año">Sin generar</a></td>
                                    <!--<td style="padding:1px;font-size:13px;"><a style="cursor:pointer;" class="activarModal" title="Editar contratos" data-nombre-modal="modalEditarPrograma" data-id=""><i class="fa fa-wrench"></i></a></td>-->
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($cantidadPaginas > 1)
                    @if($cantidadPaginas <= 10)
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            @if($cantidadPaginas > 1 )
                            <small style="float:left;">
                                Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $contadorContratos }} registros
                            </small>
                            @endif
                            @for($i=$cantidadPaginas; $i>0; $i--)
                                <?php
                                    $style='';
                                    if($i == $pagina){
                                        $style=";background:#087b76; color:white;";
                                    }
                                ?>
                                <a href="{{ url('seguimiento/cartaslaborales/index') }}?pagina=<?php echo $i.$par_identificacion; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $i }}</button></a>
                            @endfor
                        </div>
                    </div>
                    @else
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <small style="float:left;">
                                Mostrando {{ $inicioContador }} a {{ --$contador }} de {{ $contadorContratos }} registros
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
                                <a href="{{ url('seguimiento/cartaslaborales/index') }}?pagina=<?php echo $cantidadPaginas.$par_identificacion; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;{{ $style }}">{{ $cantidadPaginas }}</button></a>
                                <a href=""><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                            @endif
                            @for($i=10; $i>0; $i--)
                                <?php
                                    $style='';
                                    if($cantidadInicia == $pagina){
                                        $style=";background:#087b76; color:white;";
                                    }
                                ?>
                                <a href="{{ url('seguimiento/cartaslaborales/index') }}?pagina=<?php echo $cantidadInicia.$par_identificacion; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px{{$style}}">{{ $cantidadInicia }}</button></a>
                                <?php $cantidadInicia--; ?>
                            @endfor
                            @if($pagina >= 10)
                                <a href=""><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">...</button></a>
                                <a href="{{ url('seguimiento/cartaslaborales/index') }}?pagina=1<?php echo $par_identificacion; ?>"><button  style="float:right;border: 1px solid black;margin:0px 1px 0px 0px;">1</button></a> 
                            @endif
                        </div>
                    </div>
                    @endif
				@endif
            </div>
        </div>
    </div>

</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on("change", ".anio_carta", function(){
           var anio = $(this).val();
           var descarga = $(this).attr("data-descarga");
           var url = $(this).attr("data-url")+"&anio="+anio;
           if (anio != "") {
            $("#descarga_"+descarga+"").attr("href",url);
            $("#descarga_"+descarga+"").attr("title","Generar carta laboral en formato Word");
            $("#descarga_"+descarga+"").text("Descargar");
           }else{
            $("#descarga_"+descarga+"").text("Sin generar");
            $("#descarga_"+descarga+"").removeAttr("href");
            $("#descarga_"+descarga+"").attr("title","Debe seleccionar el año");
           }
        });
        $(document).on("click","#cuadro-closed", function(){
          $(".cuadro").css('display','none');
        });
        setTimeout(function() {
        $(".cuadro").fadeOut(1000);
        },5000);
});
</script>
@endsection