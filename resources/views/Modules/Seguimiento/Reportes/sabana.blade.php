@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Evaluaci&oacute;n y seguimiento etapa lectiva') !!}

<div class="row">

    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="glyphicon glyphicon-edit"></i> 
                    <span>Reporte participante por documento de identidad</span>
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
                <p>En el siguiente formulario podras generar todos los formatos necesarios por <code>aprendiz</code> y n&uacute;mero de <code>ficha</code>.</p>
                {!! Form::open(array("url" => url("Modules/Seguimiento/Reportes/Sabana/sabana.php"), "method"=>"post", "class"=>"form-horizontal")) !!}

                <div class="form-group">
                    {!! Form::label("ficha","N&uacute;mero de ficha",array("for"=>"ficha", "class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-4">
                        {!! Form::number("ficha", null, array("id"=>"ficha", "class"=>"form-control", "placeholder"=>"Digite n&uacute;mero de ficha")) !!}
                        <p class="help-block">Digite el n&uacute;mero de ficha sin puntos ni comas</p>
                    </div>

                </div>
                <input type="hidden" name="docHash" id="docHash" value="{{ time()}}">
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">			
                        {!! Form::submit("Generar reportes", array("class"=>"btn btn-success control-label ajax-sabana", "data-ruta"=>asset('Modules/Seguimiento/Reportes/Sabana'))) !!} 
                    </div>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
    <!--/span-->
</div>

<div class="row" id="progreso-sabana" style="display:none">
    <div class="col-xs-12 col-sm-12">
        <h4>Generando reportes <small>Evaluaci&oacute;n y seguimiento etapa lectiva</small><code id="nombre-sabana"></code></h4>
    </div>
    <div class="col-xs-12 col-sm-12">
        <div class="progress progress-striped active">
            <div id="barra_progreso" style="width: 0%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="0" role="progressbar" class="progress-bar progress-bar-success">
                <span>0% Completo</span>
            </div>
        </div>
    </div>

</div>

@endsection

@section('plugins-js')

<script type="text/javascript">

    $(document).ready(function() {
        // Formularios AJAX SABANA
        $(document).on('click', '.ajax-sabana', function(e) {
            e.preventDefault();
            
            var url = $(this).parents('form').attr('action');
            var ruta = $(this).attr("data-ruta");

            if (window.FormData) {
                var data = new FormData($(this).parents('form')[0]);
            }
            //alert(url);
            LoadAjaxContentPOSTSABANA(url, data, ruta);

        });
    });



    function LoadAjaxContentPOSTSABANA(url, datos, ruta) {

        $("#nombre-sabana").text($("#ficha").val() + ".zip");
        $('#progreso-sabana').show();
        bIniciado = true;
        bFinalizado = false;
        $(".ajax-sabana").attr('disabled', true);
        $.ajax({
            url: url,
            type: 'POST',
            data: datos,
            processData: false,
            contentType: false,
            success: function() {
                bFinalizado = true;
                mostrarProgreso(100);
                $(".ajax-sabana").attr('disabled', false);
                $("#nombre-sabana").html("<a href='/Modules/Seguimiento/Reportes/Sabana/temp/" + $("#ficha").val() + ".zip'>" + $("#ficha").val() + ".zip</a>");
            },
            error: function() {
                bFinalizado = true;
                mostrarProgreso(100);
                $(".ajax-sabana").attr('disabled', false);
                $("#nombre-sabana").text("La sabana no se pudo generar de la forma correcta, por favor vuelve a intentar");
            }
        });

        getProgreso(ruta);
    }


    function mostrarProgreso(porcentaje) {
        $("#barra_progreso").css("width", porcentaje + "%");
        $("#barra_progreso span").text(porcentaje + "% Completo");
        $("#barra_progreso").attr("aria-valuenow", porcentaje);
    }

    function getProgreso(ruta) {

        $.ajax({
            url: ruta + "/progreso.php",
            data: "docHash="+$("#docHash").val(),
            success: function(data) {
                var nProgreso = parseInt(data, 10);
                if (data == "finish" || bIniciado) {
                    nProgreso = 0;
                    bIniciado = false;
                } else {
                    mostrarProgreso(nProgreso);
                }
                if (nProgreso < 100 && data != "finish") {
                    setTimeout(getProgreso(ruta), 100);
                }
            },
            error: function() {
                setTimeout(getProgreso(ruta), 100);
            }
        });
    }

</script>

@endsection