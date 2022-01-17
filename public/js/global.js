$(document).ready(function () {
    $('.js-example-basic-multiple').select2({width:"100%"});
    $('.js-example-basic-single').select2({width:"100%"});
    
    $( "#tabs" ).tabs();
    
    $('body').on('click', '.show-sidebar', function (e) {
		e.preventDefault();
		$('div#main').toggleClass('sidebar-show');
	});

    $('.main-menu').on('click', 'a', function (e) {
        if($(this).hasClass('activa') && $(this).parent().find('ul').is(':visible')){
            $(this).parent().find('ul').slideUp('fast');
            $(this).removeClass('activa');
        }else{
            $(this).addClass('activa');
            $(this).parent().parent().find('ul').css('display', 'none');
            $(this).parent().find('ul').slideDown('fast');
        }
        
        var urlActual = window.location.href;
        var urlDestino = $(this).attr('href');
        if(urlActual == urlDestino || urlActual+'#' == urlDestino){
            e.preventDefault();
            alert('Usted está en la opción seleccionada del menú.');
        }
    });
    
    // Asignar actividades
	$(document).on('change','.asignarActividades',function(){
		var token = $('#urls').attr('data-token'); 
		var url = $(this).attr('data-url'); 
		var valor = $(this).val(); 
		var ficha = $(this).attr('data-ficha'); 
		$.post(url, {'_token':token,'valor':valor,'ficha':ficha}, function(respuesta){
			alert(respuesta);
		});
	});
    
    // actividad
	$(document).on('change','.herramienta',function(){
		var valor = $(this).val();
		$(this).parent().parent().find('.otraHerramienta').css('display','none');
		$(this).parent().parent().find('.explicacion').css('display','none');
		if(valor == 4){
			$(this).parent().parent().find('.otraHerramienta').css('display','block');
			$(this).parent().parent().find('.r1').attr('required',true);
			$(this).parent().parent().find('.r2').attr('required',false);
		}else if(valor == 5){
			$(this).parent().parent().find('.explicacion').css('display','block');
			$(this).parent().parent().find('.r1').attr('required',false);
			$(this).parent().parent().find('.r2').attr('required',true);
		}else{
			$(this).parent().parent().find('.r1').attr('required',false);
			$(this).parent().parent().find('.r2').attr('required',false);
		}
	});
	//
    
    
	// Retardo Inicio
	
	$(document).on('click','.retardo',function(){
		var url = $('#urls').attr('data-url-retardo'); 
		var token = $('#urls').attr('data-token'); 
		var documento = $(this).parent().parent().find('.documento').html();
		var valor = $(this).is(":checked");
		var asistencia = $('[name="'+documento+'"]:checked').val();
		var elemento = $(this).parent().parent().find('.asistencia');
		
		$.post(url, {'_token':token,'valor':valor,'documento':documento}, function(respuesta){
			$('#mensaje').remove();
			$('#notificaciones').append(respuesta);
			$('#mensaje').animate({wordSpacing: "+=2px"});
			$('#mensaje').animate({wordSpacing: "-=2px"});
		});
		
		if(asistencia == 'NO' && valor == true){
			$('#'+documento+'SI').prop('checked',true);
			$('#'+documento+'SI').trigger('click');
		}
	});
	
	// Retardo Fin
    
    $(document).on('click','#checkLlamadoAsistencia',function(){
		var token = $('#_token').val();
		var url = $('#urlInstructor').val(); 
		
		$.post(url, {'_token':token}, function(respuesta){
			$('#contenedorLlamadoAsistencia').hide('slow');
			$('#contenedorAsistencia').show('slow');
			$('#mensaje').remove();
			$('#notificaciones').append(respuesta);
			$('#mensaje').animate({wordSpacing: "+=2px"});
			$('#mensaje').animate({wordSpacing: "-=2px"});
        });
	});
	
    $(document).on('click','.activarModal',function(){
		var ficha = $(this).parent().parent().find('.ficha').html();
		var programa = $(this).parent().parent().find('.programa').html();
		var dia = $(this).parent().parent().find('.dia').html();
		var hora = $(this).parent().parent().find('.hora').html();
		$('#ficha').html(ficha);
		$('#programa').html(programa);
		$('#dia').html(dia);
		$('#hora').html(hora);
	});
	
	$(document).on('click','.asistencia', function(){
		var token = $('#_token').val();
		var ficha = $('#ficha').html();
		var valor = $(this).val();
		var documento = $(this).parent().parent().find('.documento').html();
		var elemento = $(this).parent().parent().find('.retardo');
		var url = $('#url').val(); 
		
		if(valor == 'NO'){
			$(this).parent().parent().css('color','red');
			var validar = $(elemento).is(":checked");
			$(elemento).prop('checked', false);
			if(validar){
				var urlRetardo = $('#urls').attr('data-url-retardo'); 
				$.post(urlRetardo, {'_token':token,'valor':false,'documento':documento}, function(respuesta){
					$('#mensaje').remove();
					$('#notificaciones').append(respuesta);
					$('#mensaje').animate({wordSpacing: "+=2px"});
					$('#mensaje').animate({wordSpacing: "-=2px"});
				});
			}
		}else{
			$(this).parent().parent().css('color','#525252');
		}
		
		$.post(url, {'_token':token,'valor':valor,'documento':documento,'ficha':ficha}, function(respuesta){
			$('#mensaje').remove();
			$('#notificaciones').append(respuesta);
			$('#mensaje').animate({wordSpacing: "+=2px"});
			$('#mensaje').animate({wordSpacing: "-=2px"});
        });
	});
	
	
    $(document).on('click','.cambioInput', function(){
        var id = $(this).attr('data-id');
        var valor = $(this).prev('.valorCambioInput').val();
        var token = $('#urls').attr('data-token');
        var url = $('#urls').attr('data-cambio-input');
        
        $.post(url, {'_token':token,'valor':valor,'id':id}, function(respuesta){
            alert('Modificación exitosa');
        });
    });

    $(document).on('change','.cambioSelect', function(){
        var id = $(this).attr('data-id');
        var valor = $(this).val();
        var token = $('#urls').attr('data-token');
        var url = $('#urls').attr('data-cambio-select');
        
        $.post(url, {'_token':token,'valor':valor,'id':id}, function(respuesta){
            alert('Modificación exitosa');
        });
    });
    
    $(document).on('click','.activarModal', function(){
        var nombreModal = $(this).attr('data-nombre-modal'); 
        var url = $('tbody').attr('data-url');
        var token = $('tbody').attr('data-token');
        var id = $(this).attr('data-id');
        $.post(url, {'_token':token,'id':id}, function(respuesta){
            $('#contenido'+nombreModal).html(respuesta);
			$('#'+nombreModal).modal('show');
        });
    });
    
    $(document).on('submit','.formulario', function(e){
        e.preventDefault();
        var url = $(this).attr('data-url');
        var token = $('tbody').attr('data-token');
        $.ajax({
			cache: false,
			contentType: false, 
			processData: false,
			type: "POST",
			url: url,
			data: new FormData(this),
			success: function(){
                alert('Modificación exitosa');
                window.location.href = window.location.href;
			}
		});
    });
    
    $(document).on('change','#reporteHorasArea', function(){
		var url = $('#formulario').attr('data-url');
		var _token = $('#miToken').val();
		var par_identificacion = $(this).parent().find('#reporteHorasArea').attr('data-id_instructor');
		var valor = $(this).val();
		$.post(url, {'_token':_token,'valor':valor,'par_identificacion':par_identificacion}, function(respuesta){
			//console.log(respuesta);
			$('#notificacion').show('slow');
			setTimeout(function(){ 
				$('#notificacion').hide('slow');
			}, 4000);
		});
	});
    
    $(document).on('change','#par_identificacion_coordinador', function(){
        var valor = $(this).val();
        if(valor == ''){
            $('#contenedor_seleccionar_ficha').show('slow');
            $('#contenedor_seleccionar_area').hide('slow');
            $('#pla_fic_id').attr('required',true);
            $('#todosLosTrimestres').css('display','block');
        }else{
            $('#contenedor_seleccionar_ficha').hide('slow');
            $('#contenedor_seleccionar_area').show('slow');
            $('#pla_fic_id').attr('required',false);
            $('#todosLosTrimestres').css('display','none');
            var valorActual = $('#pla_fec_tri_id').val();
            if(valorActual == 'todos'){
                $('#pla_fec_tri_id').val("");
            }
        }
    });
    
    $(".formularios").submit(function(e){
        e.preventDefault();
        var datos = $(this).serialize();
        var url = $(this).attr('data-url');
        $.post(url, datos, function(respuesta){
            $("#mensaje").html(respuesta);
            $("#mensaje").parent().parent().css('display','block');
            $("#mensaje").parent().animate({borderBottomWidth: "10px"});
            $("#mensaje").parent().animate({borderBottomWidth: "1px"});
			setTimeout(function(){ $("#mensaje").parent().parent().hide(); }, 3000);
        });
    });
    
    $('#myModal5').on('hidden.bs.modal', function () {
        $("#contendor5").css('display','none');
    });
    
    $(document).on('click', ".modificarActividades", function(){
        $("#myModal5").find("#programa").html($(this).attr("data-programa"));
        $("#myModal5").find("#fecInicio").html($(this).attr("data-fec-inicio"));
        $("#myModal5").find("#fecFin").html($(this).attr("data-fec-fin"));
        $("#myModal5").find("#ficha").html($(this).attr("data-ficha"));
        $("#myModal5").find("#trimestre").html($(this).attr("data-trimestre"));
        $("#myModal5").attr("data-pla-fic-id",$(this).attr("data-pla-fic-id"));

        var url = $("#formulario").attr("data-url-modificar-actividades");
        var pla_fic_id = $(this).attr("data-pla-fic-id");
        var trimestre = $(this).attr("data-trimestre");
        var data = {trimestre:trimestre,pla_fic_id:pla_fic_id};
        
        $.ajax({url: url, type: "GET", data: data, success: function(respuesta){
                $("#myModal5").find("#contenido").html(respuesta);
                $("#myModal5").modal();
			}
		});
    });
    
    $("#formRestriccion").submit(function(e){
        e.preventDefault();
        var datos = $(this).serialize();
        var url = $(this).attr('data-url');
        $.post(url, datos, function(respuesta){
            $("#mensaje").html(respuesta);
            $("#notificaciones").parent().css('display','block');
            $("#notificaciones").animate({borderBottomWidth: "10px"});
            $("#notificaciones").animate({borderBottomWidth: "1px"});
        });
    });

    $("#formComplementario").submit(function(e){
        e.preventDefault();
        var datos = $(this).serialize();
        var url = $(this).attr('data-url');
        
        $.post(url, datos, function(respuesta){
            //console.log(respuesta);
            var resultado = JSON.parse(respuesta);
            var mensaje = '';
            $.each(resultado.mensaje, function(key, valor){
                mensaje += valor;
            });

            $("#mensaje").html(mensaje);
            $("#notificaciones").parent().css('display','block');
            $("#notificaciones").animate({borderBottomWidth: "10px"});
            $("#notificaciones").animate({borderBottomWidth: "1px"});
        });
    });
    /* --- */
    if (localStorage.getItem("posicion")) { $('html').scrollTop(localStorage.getItem("posicion")); }
    
    $(document).on('click', ".ambienteModificar", function(){
        var url = $("#url").attr("data-url");
        var estado = $(this).attr("data-amb-estado");
        var sumaHoras = $(this).attr("data-amb-suma-horas");
        var tipo = $(this).attr("data-amb-tipo");
        var ambienteId = $(this).attr("data-amb-id");
        var coordinador = $(this).attr("data-id-coordinador");
        $.get(url, 'estado='+estado+'&sumaHoras='+sumaHoras+'&tipo='+tipo+'&ambienteId='+ambienteId+'&coordinador='+coordinador, function(respuesta){
            $("#contenidoModal").html(respuesta);
            $("#modalAmbiente").modal();
        });
    });

    $("#formularioModalAmbiente").submit(function(e){
        e.preventDefault();
        var datos = $(this).serialize();
        var url = $(this).attr('data-url');
        //console.log(url);
        $.post(url, datos, function(respuesta){
            $("#mensaje").html('Los cambios se han guardado exitosamente');
            $("#notificaciones").css('display','block');
            $("#notificaciones").animate({left: '15px'});
            $("#notificaciones").animate({left: '-15px'});
            $("#notificaciones").animate({left: '0px'});
            localStorage.setItem("actualizar", "si");
        });
    });

    $('#modalAmbiente').on('hidden.bs.modal', function () {
        if(localStorage.getItem("actualizar")) {
            window.location.href = window.location.href;
        }
    });
    
    $(document).on('click', ".botonGuardarCambiosComplementario", function(){
        var datos = $("#formularioComplementario").serialize();
        var _token =  $("#modalComplementario").attr("data-token");
        var url = $("#modalComplementario").attr("data-url-guardar-cambios"); 
        var par_identificacion = $("#modalComplementario").find("#par_identificacion").val();
        var fecha_inicio = $("#modalComplementario").find("#fecInicio").html();
        var fecha_fin = $("#modalComplementario").find("#fecFin").html();

        $.post(url, datos+'&_token='+_token+'&par_identificacion='+par_identificacion+'&par_identificacion='+par_identificacion+'&fecha_fin='+fecha_fin+'&fecha_inicio='+fecha_inicio, function(respuesta){
            var resultado = JSON.parse(respuesta);
            var mensaje = '';

            if(resultado.errores == 0){
                mensaje = 'Todas las modificaciones se lograron exitosamente.'; 
            }else{
                $.each(resultado.mensaje, function(i,item){ mensaje += item; });
            }

            $("#modalComplementario").find("#contenidoNotificaciones").html(mensaje);
            $("#modalComplementario").find("#notificaciones").css("display","block");
            $("#modalComplementario").find("#notificaciones").animate({left: '20px'});
            $("#modalComplementario").find("#notificaciones").animate({left: '0px'});

            localStorage.setItem("actualizar", "si");
        });
    });
    
    

    $(document).on('click', ".botonGuardarCambiosRestriccion", function(){
        var datos = $("#formularioRestricciones").serialize();
        var _token =  $("#modalRestricciones").attr("data-token");
        var url = $("#modalRestricciones").attr("data-url-guardar-cambios"); 
        var par_identificacion = $("#modalRestricciones").find("#par_identificacion").val();
        $.post(url, datos+'&_token='+_token+'&par_identificacion='+par_identificacion+'&par_identificacion='+par_identificacion, function(respuesta){
            var resultado = JSON.parse(respuesta);
            var mensaje = '';

            if(resultado.errores == 0){
                mensaje = 'Todas las modificaciones se lograron exitosamente.'; 
            }else{
                $.each(resultado.mensaje, function(i,item){ mensaje += item; });
            }

            $("#modalRestricciones").find("#contenidoNotificaciones").html(mensaje);
            $("#modalRestricciones").find("#notificaciones").css("display","block");
            $("#modalRestricciones").find("#notificaciones").animate({left: '20px'});
            $("#modalRestricciones").find("#notificaciones").animate({left: '0px'});

            localStorage.setItem("actualizar", "si");
        });
    });

    

    $('#modalRestricciones').on('hidden.bs.modal', function () {
        $("#modalRestricciones").find("#notificaciones").css("display","none");
        $("#modalRestricciones").find("#contenidoTabla").html('');
        $("#contenedorGuardarCambiosRestriccion").css('display','none');
        if(localStorage.getItem("actualizar")) { 
            window.location.href = window.location.href;
            localStorage.setItem("posicion", $('html').scrollTop());
        }
    });

    $('#modalComplementario').on('hidden.bs.modal', function () {
        $("#modalComplementario").find("#notificaciones").css("display","none");
        $("#modalComplementario").find("#contenidoTabla").html('');
        $("#contenedorGuardarCambiosComplementario").css('display','none');
        if(localStorage.getItem("actualizar")) { 
            window.location.href = window.location.href;
            localStorage.setItem("posicion", $('html').scrollTop());
        }
    });

    $(document).on('click', ".complementario", function(){
        var cc = $(this).attr('data-cc');
        var fechaInicio = $(this).attr('data-fecha-inicio');
        var fechaFin = $(this).attr('data-fecha-fin');
        var instructor = $(this).attr('data-instructor');
        var url = $('#modalComplementario').attr("data-url");

        $('#modalComplementario').find('#cc').html(cc);
        $('#modalComplementario').find('#fecInicio').html(fechaInicio);
        $('#modalComplementario').find('#fecFin').html(fechaFin);
        $('#modalComplementario').find('#instructor').html(instructor);

        var data = { fecha_inicio:fechaInicio, fecha_fin:fechaFin, cc:cc };
        $.get(url, data, function(respuesta){
            var contador = $(respuesta).find("td").length;
            $('#modalComplementario').find("#contenidoTabla").html(respuesta);

            if(contador > 1){
                $("#contenedorGuardarCambiosComplementario").css('display','block');
            }else{
                $("#contenedorGuardarCambiosComplementario").css('display','none');
            }
            $('#modalComplementario').modal();
        });
    });

    $(document).on('click', ".contenidoRestriccion", function(){
        var cc = $(this).attr('data-cc');
        var fechaInicio = $(this).attr('data-fecha-inicio');
        var fechaFin = $(this).attr('data-fecha-fin');
        var instructor = $(this).attr('data-instructor');
        var url = $('#modalRestricciones').attr("data-url"); 

        $('#modalRestricciones').find('#cc').html(cc);
        $('#modalRestricciones').find('#fecInicio').html(fechaInicio);
        $('#modalRestricciones').find('#fecFin').html(fechaFin);
        $('#modalRestricciones').find('#instructor').html(instructor);

        var data = {fecha_inicio:fechaInicio, fecha_fin:fechaFin, cc:cc};
        $.get(url, data, function(respuesta){
            var contador = $(respuesta).find("td").length;
            $('#modalRestricciones').find("#contenidoTabla").html(respuesta);

            if(contador > 1){
                $("#contenedorGuardarCambiosRestriccion").css('display','block');
            }else{
                $("#contenedorGuardarCambiosRestriccion").css('display','none');
            }
            $('#modalRestricciones').modal();
        });
    });

    $(document).on('click', ".eliminarRestriccion", function(){
        var r = confirm("Estas seguro que deseas eliminar la restricción del instructor?");
        if (r == true) {
            var url = $("#modalRestricciones").attr("data-url-eliminar");
            var _token =  $("#modalRestricciones").attr("data-token");
            var id = $(this).attr("data-id");
            //console.log(url);
            $.post(url, {id:id, _token:_token}, function(respuesta){
                confirm("El resgistro fue eliminado exitosamente.");
                window.location.href = window.location.href;
                localStorage.setItem("posicion", $('html').scrollTop());
            });
        } 
    });
    
    var filaAgregar = "";
    var contadorFilas = 1;
    $(document).on('change', ".agregarActividad", function(){
        var valor = $(this).val();
        var elemento = $(this).parent().parent();
        
        //console.log(elemento);
        if(valor == "SI"){
            elemento.find(".instructorActividades").css("display","block");
            elemento.find(".obligatorio").attr("required",true);
            elemento.find(".obligatorio").prop("disabled",false);
        }else{
            elemento.find(".obligatorio").prop("disabled",true);
            elemento.find(".instructorActividades").css("display","none");
            elemento.find(".obligatorio").attr("required",false);
        }
        //console.log(valor);
        //alert(tabla);
    });
    
    var elementoDuplicar = $('.agregarContenido').parent().parent().parent().parent().html();
    $(document).on('click', '.agregarContenido', function(){
        var contador = $(this).attr('data-fila');
        contador++;
        $('#contenidoModal4').append(elementoDuplicar);
        $(".agregarContenido:last").attr('data-fila',contador);
        $(".competencia:last").attr("name",'competencia['+contador+'][]');
        $(".competencia:last").attr("name",'resultado['+contador+'][]');
        $(".competencia:last").attr("name",'actividad['+contador+'][]');
        $(".competencia:last").attr("name",'horas_presenciales['+contador+'][]');

        $(".pla_tip_id:last").attr("name",'pla_tip_id['+contador+']');
        $(".dia:last").attr("name",'dia['+contador+']'); 
        $(".hora_inicio:last").attr("name",'hora_inicio['+contador+']');
        $(".hora_fin:last").attr("name",'hora_fin['+contador+']');
        $(".par_identificacion:last").attr("name",'par_identificacion['+contador+']');
        $(".pla_amb_id:last").attr("name",'pla_amb_id['+contador+']');
    });

    $(document).on('click', ".eliminarContenido", function(){
        var elemento = $(this).parent().parent().parent();
        var contador = $(".eliminarContenido").length;
        if(contador == 1){
            alert("No se puede eliminar la unica fila.");
        }else{
            elemento.remove();
        }
    });

    $(document).on('click', ".aumentarFila", function(){
        var elemento = $('#aumentarFilaPrimera').html();
        $(elemento).parent().find('.obligatorio').prop('disabled',false);
        $(this).parent().parent().parent().append('<tr>'+elemento+'<td><a class="btn btn-danger btn-xs borrarFila">Eliminar</a></td></tr>');
    });

    $(document).on('click', ".borrarFila", function(){
        $(this).parent().parent().remove();
    });

    $(document).on('change', ".validarHora", function(){
        var hora_inicio = parseInt($('#hora_inicio').val());
        var hora_fin = parseInt($('#hora_fin').val());
        if(hora_inicio != "" && hora_fin != ""){
            if(hora_inicio == hora_fin){ alert('La hora de inicio y fin deben ser diferentes.'); }
            if(hora_inicio > hora_fin){ alert('La hora de inicio no puede ser mayor a la hora de fin.'); }
        }
    });

    $('#myModal2').on('hidden.bs.modal', function () {
        $("#botonModificar").attr("disabled",true);
        $("#myModal2").find("#contenidoModificar").css("display","none");
        $("#myModal2").find("#guardarModificado").css("display","none");
        $(this).find("td").css({"background-color":"white","color":"black"});
        $("#myModal2").find(".filaModificar").prop("checked", false);
        $("#myModal2").find("#notificaciones").css("display","none");
        //alert(localStorage.getItem("actualizar"));
        if(localStorage.getItem("actualizar")) { 
            localStorage.setItem("posicion", $('html').scrollTop());
            window.location.href = window.location.href;
        }
        $("#myModal2").find('#contenido').html('');
        //alert("xd");
    });

    $('#myModal3').on('hidden.bs.modal', function () {
        $("#myModal3").find("#clave").val('');
        if(localStorage.getItem("actualizar")) { 
            localStorage.setItem("posicion", $('html').scrollTop());
            window.location.href = window.location.href;
        }
    });

    $('#myModal4').on('hidden.bs.modal', function () {
        $("#myModal4").find("#contenido").html("");
        $("#myModal4").find("#notificaciones").css("display","none");
        //alert(localStorage.getItem("actualizar"));
        if(localStorage.getItem("actualizar")) { 
            localStorage.setItem("posicion", $('html').scrollTop());
            window.location.href = window.location.href;
        }
    });

    $("#formularioEliminar").submit(function(e){
        e.preventDefault();
        var url = $(this).attr("data-url");
        var datos = $(this).serialize();
        var r = confirm("Estas seguro que desea eliminar todo el horario?");
        if (r == true) {
            $.ajax({url:url, type:"POST", data:datos, success: function(respuesta){
                    if(respuesta == 1){
                        alert('El horario ha sido eliminado exitosamente');
                        localStorage.setItem("posicion", $('html').scrollTop());
                        window.location.href = window.location.href;
                    }else{
                        alert('La contraseña no es válida o el rol no tiene los permisos suficientes.');
                    }
                }
            });
        }
    });
    
    $(document).on('click', "#guardarModificado", function(){
        var url = $("#formularioModificarHorario").attr("data-url");
        var datos = $("#formularioModificarHorario").serialize();
        var fechaInicio = $("#myModal2").find("#fecInicio").html();
        var fechaFin = $("#myModal2").find("#fecFin").html();
        var ficha = $("#myModal2").find("#ficha").html();
        var fic_id = $("#myModal2").attr("data-pla-fic-id");
        localStorage.setItem("actualizar", "si");
        $.ajax({
            url:url,type:"POST",data:datos+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&ficha="+ficha+"&fic_id="+fic_id,
			success: function(respuesta){
                $("#myModal2").find("#contenidoNotificaciones").html(respuesta);
                $("#myModal2").find("#notificaciones").css("display","block");
                $("#myModal2").find("#notificaciones").animate({left: '5px'});
                $("#myModal2").find("#notificaciones").animate({left: '-10px'});
			}
		});
    });

    $(document).on('click', "#botonTrueque", function(){
        $("#myModal2").find("#notificaciones").css("display","none");
        var url = $("#formularioModificarHorario").attr("data-url")+'trueque';
        var datos = $("#formularioModificarHorario").serialize();
        var fechaInicio = $("#myModal2").find("#fecInicio").html();
        var fechaFin = $("#myModal2").find("#fecFin").html();
        var ficha = $("#myModal2").find("#ficha").html();
        var fic_id = $("#myModal2").attr("data-pla-fic-id");
        
        $.ajax({
            url: url,type: "POST",
            data: datos+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&ficha="+ficha+"&trueque=true&fic_id="+fic_id,
			success: function(respuesta){
                $("#myModal2").find("#contenidoNotificaciones").html(respuesta);
                $("#myModal2").find("#notificaciones").css("display","block");
                $("#myModal2").find("#notificaciones").animate({left: '5px'});
                $("#myModal2").find("#notificaciones").animate({left: '-10px'});
                localStorage.setItem("actualizar", "si");
			}
        });
    });

    $(document).on('click', "#botonModificar", function(){
        pla_fic_det = [];
        var visible = $("#myModal2").find("#contenidoModificar").is(':visible');
        var trimestre = $("#myModal2").find("#trimestre").html();
        var url = $("#myModal2").attr("data-url");
        var contador = 0;
        $("#myModal2").find("#notificaciones").css("display","none");
        if(!visible){
            $("#myModal2").find("#contenido").find(".filaModificar").each(function(){
                if($(this).is(":checked")){
                    pla_fic_det[contador] = $(this).val();
                    contador++;
                }
            });

            var data = { pla_fic_det:pla_fic_det, trimestre:trimestre };
            $.ajax({ 
                url: url, type: "GET", data: data, success: function(respuesta){
                    $("#myModal2").find("#contenidoModificar").html(respuesta);
                    $("#myModal2").find("#contenidoModificar").css("display","block");
                    $("#myModal2").find("#guardarModificado").css("display","block");
                    $("#botonTrueque").attr("disabled",true);
                    $('.js-example-basic-single').select2({width:"100%"});
                }   
            });
            
            $("#botonEliminar").attr("disabled",true); 
        }else{
            $("#botonEliminar").attr("disabled",false); 
            $("#myModal2").find("#contenidoModificar").css("display","none");
            $("#myModal2").find("#guardarModificado").css("display","none");
            var contador = 0 ;
            $("#myModal2").find(".filaModificar").each(function(){
                if($(this).is(":checked")){ contador++; }
            }); 
            if(contador == 2){  $("#botonTrueque").attr("disabled",false); }
            else{  $("#botonTrueque").attr("disabled",true); }
        }
    });

    $(document).on('click', "#botonEliminar", function(){
        pla_fic_det = [];
        var url = $("#myModal2").attr("data-url-eliminar");
        var contador = 0;
        var r = confirm("Estas seguro que desea eliminar los registros seleccionados?");
        if (r == true) {
            var _token = $('#token').val();
            $("#myModal2").find("#contenido").find(".filaModificar").each(function(){
                if($(this).is(":checked")){
                    pla_fic_det[contador] = $(this).val();
                    contador++;
                }
            });

            var data = {pla_fic_det:pla_fic_det,_token:_token};
            $.ajax({ 
                url: url, type: "POST", data: data, success: function(){
                    localStorage.setItem("posicion", $('html').scrollTop());
                    alert('Se ha eliminado exitosamente.');
                    window.location.href = window.location.href;
                }
            });
        }
    });

    $(document).on('click', ".eliminarHorario", function(){
        var valor = $(this).attr('data-id');
        $('#id_horario').val(valor);
        $("#myModal3").find("#programa").html($(this).attr("data-programa"));
        $("#myModal3").find("#ficha").html($(this).attr("data-ficha"));
        $('#myModal3').modal();
    });
    
    $(document).on('change', ".espacios2", function(){
        $("#guardarModificado").attr("disabled",false);
    }); 

    $(document).on('change', ".espacios", function(){
        var url = $("#contenidoModificar").attr("date-url-ins-amb");
        var elemento = $(this).parent().parent();
        var det_instructor = elemento.find('#det_instructor').val();
        var pla_fic_det_id = elemento.find('#pla_fic_det_id').val();
        var det_ambiente = elemento.find('#det_ambiente').val();
        var dia = elemento.find('#pla_dia_id').val();
        var hora_inicio = elemento.find('#pla_fic_det_hor_inicio').val();
        var hora_fin = elemento.find('#pla_fic_det_hor_fin').val();
        var fechaFin = $('#myModal2').find('#fecFin').html();
        //alert(fechaFin);
        var _token = $('#token').val();
        var datos = { 
            det_ambiente:det_ambiente,det_instructor:det_instructor,pla_fic_det_id:pla_fic_det_id,
            fechaFin:fechaFin,_token:_token,dia:dia,hora_inicio:hora_inicio,hora_fin:hora_fin 
        };
        elemento.find('#det_ambiente').html('<option value="">Cargando...</option>');
        elemento.find('#det_instructor').html('<option value="">Cargando...</option>');
        $.ajax({url:url, type:'POST', data:datos, dataType:'json', success: function(respuesta){
                if(respuesta.errores){
                    alert(respuesta.errores); 
                    $("#guardarModificado").attr("disabled",true);
                }else{
                    elemento.find('#det_ambiente').html(respuesta.ambientes);
                    elemento.find('#det_instructor').html(respuesta.instructores);
                    $("#guardarModificado").attr("disabled",false);
                }
			}
        });
    }); 

    $(document).on('change', ".filaModificar", function(){
        var valor = $(this).val();
        if(valor == "todo"){
            var elemento = $(this).parent().parent().parent().parent();
            if($(this).is(":checked")){
                $(elemento).find(".filaModificar").prop("checked", true);
            }else{
                $(elemento).find(".filaModificar").prop("checked", false);
                $("#myModal2").find("#notificaciones").css("display","none");
            }
        }else{
            var elemento = $(this).parent().parent();
        }

        if($(this).is(":checked")){
            $(elemento).find("td").css({"background-color":"#087b75","color":"white"});
        }else{
            $(elemento).find("td").css({"background-color":"white","color":"#525252"});
        }

        var contador = 0 ;
        $("#myModal2").find(".filaModificar").each(function(){
            if($(this).is(":checked") && $(this).val() != 'todo'){
                contador++;
            }
        });

        if(contador>0){ 
            $("#botonModificar").attr("disabled",false); 
            $("#botonEliminar").attr("disabled",false);  
        }else{
            $("#botonModificar").attr("disabled",true);
            $("#botonEliminar").attr("disabled",true);
            
            $("#myModal2").find("#contenidoModificar").html("");
            $("#myModal2").find("#guardarModificado").css("display","none");
        }

        if(contador == 2){ $("#botonTrueque").attr("disabled",false); }else{ $("#botonTrueque").attr("disabled",true); }
    });

    $(document).on('click', ".agregar", function(){
        $("#myModal4").find("#programa").html($(this).attr("data-programa"));
        $("#myModal4").find("#fecInicio").html($(this).attr("data-fec-inicio"));
        $("#myModal4").find("#fecFin").html($(this).attr("data-fec-fin"));
        $("#myModal4").find("#ficha").html($(this).attr("data-ficha"));
        $("#myModal4").find("#trimestre").html($(this).attr("data-trimestre"));
        $("#myModal4").attr("data-pla-fic-id",$(this).attr("data-pla-fic-id"));
        $('#myModal4').modal();
    });

    $(document).on('click', ".modificar", function(){
        $("#myModal2").find("#programa").html($(this).attr("data-programa"));
        $("#myModal2").find("#fecInicio").html($(this).attr("data-fec-inicio"));
        $("#myModal2").find("#fecFin").html($(this).attr("data-fec-fin"));
        $("#myModal2").find("#ficha").html($(this).attr("data-ficha"));
        $("#myModal2").find("#trimestre").html($(this).attr("data-trimestre"));
        $("#myModal2").attr("data-pla-fic-id",$(this).attr("data-pla-fic-id"));

        var datos = $(this).attr('data-datos');
        var url = $("#formulario").attr("data-url-modificar");
        var pla_fic_id = $(this).attr("data-pla-fic-id");
        var trimestre = $(this).attr("data-trimestre");
        var data = {trimestre:trimestre,pla_fic_id:pla_fic_id,datos:datos};
        
        $.ajax({url: url, type: "GET", data: data, success: function(respuesta){
                $("#myModal2").find("#contenido").html(respuesta);
                $("#myModal2").modal();
			}
		});
    });

    $(document).on('click', ".actividadInstructor", function() {
        var url = $('#url').attr("data-url");

        var pla_fic_id = $(this).attr("data-pla-fic-id");
        var trimestre = $(this).attr('data-trimestre');
        var cc = $(this).attr("data-cc");
        var programa = $(this).attr('data-programa');
        var ficha = $(this).attr('data-ficha');

        var elemento = $(this).parent().parent().parent();
        var fechaInicio = elemento.attr('data-fecha-inicio');
        var fecha_inicio_actividad = $(this).attr('fecha-inicio-actividad');
        var fechaFin = elemento.attr("data-fecha-fin");
        var instructor = elemento.attr("data-instructor");
        //console.log(fechaInicio);
        $("#myModal").find("#fecInicio").html(fechaInicio);
        $("#myModal").find("#fecFin").html(fechaFin);
        $("#myModal").find("#instructor").html(instructor);
        $("#myModal").find("#cc").html(cc);
        $("#myModal").find("#programa").html(programa);
        $("#myModal").find("#trimestre").html(trimestre);
        $("#myModal").find("#ficha").html(ficha);

        var datos = {cc:cc,pla_fic_id:pla_fic_id,trimestre:trimestre, fecha_inicio:fecha_inicio_actividad};
        $.ajax({url: url, type: "GET", data:datos, success: function(respuesta){
                $("#myModal").find("#contenido").html(respuesta);
                $("#myModal").modal();
            }
        });
    });

    $(document).on('click', ".actividadAmbiente", function() {
        var fecha_inicio_actividad = $(this).attr("fecha_inicio_actividad");
        var url = $('#url').attr("data-url");
        var elemento = $(this).parent().parent().parent();
        var fechaInicio = elemento.attr("data-fecha-inicio");
        var fechaFin = elemento.attr("data-fecha-fin");
        var ambiente = elemento.attr('data-ambiente');
        var pla_fic_id = $(this).attr("data-pla-fic-id");
        var cc = $(this).attr("data-cc");
        var programa = $(this).attr('data-programa');
        var ficha = $(this).attr('data-ficha');
        var trimestre = $(this).attr('data-trimestre');
        
        $("#myModal").find("#fecInicio").html(fechaInicio);
        $("#myModal").find("#fecFin").html(fechaFin);
        $("#myModal").find("#programa").html(programa);
        $("#myModal").find("#ficha").html(ficha);
        $("#myModal").find("#trimestre").html(trimestre);
        $("#myModal").find("#ambiente").html(ambiente);

        var datos = {cc:cc,pla_fic_id:pla_fic_id,trimestre:trimestre, fecha_inicio: fecha_inicio_actividad};
        $.ajax({url: url, type: "GET", data:datos, success: function(respuesta){
                $("#myModal").find("#contenido").html(respuesta);
                $("#myModal").modal();
            }
        });
    });

    $(document).on('click', ".actividad", function() {
        var cc = $(this).attr("data-cc");
        var url = $('#url').attr("data-url");
        var instructor = $(this).attr("data-instructor");

        var elemento = $(this).parent().parent().parent();
        var pla_fic_id = elemento.attr("data-plaFicId");
        var trimestre = elemento.attr("data-trimestre");
        var ficha = elemento.attr("data-ficha");
        
        var fechaInicio = elemento.attr("data-fec-inicio");
        var fechaFin = elemento.attr("data-fec-fin");
        var programa = elemento.attr("data-programa");

        $("#myModal").find("#programa").html(programa);
        $("#myModal").find("#fecInicio").html(fechaInicio);
        $("#myModal").find("#fecFin").html(fechaFin);
        $("#myModal").find("#ficha").html(ficha);
        $("#myModal").find("#trimestre").html(trimestre);
        $("#myModal").find("#instructor").html(instructor);
        $("#myModal").find("#cc").html(cc);

        var data = {cc:cc,pla_fic_id:pla_fic_id,trimestre:trimestre,fecha_inicio:fechaInicio};
        $.ajax({url: url, type: "GET", data: data, success: function(respuesta){
                $("#myModal").find("#contenido").html(respuesta);
                $("#myModal").modal();
            }
        });
    });
    
    localStorage.removeItem("posicion");
    localStorage.removeItem("actualizar");

    $('#miTabla').DataTable({
        
		language: {
			"decimal": "",
			"emptyTable": "No hay información",
			"info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
			"infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
			"infoFiltered": "(Filtrado de _MAX_ total entradas)",
			"infoPostFix": "",
			"thousands": ",",
			"lengthMenu": "Mostrar _MENU_ Entradas",
			"loadingRecords": "Cargando...",
			"processing": "Procesando...",
			"search": "Buscar:",
			"zeroRecords": "Sin resultados encontrados",
			"paginate": {
				"first": "Primero",
				"last": "Ultimo",
				"next": "Siguiente",
				"previous": "Anterior"
			}
		}
    });
    
    $(document).on('keyup', "#queja_aprendiz", function () {

        $("#tabla_queja").html("Cargando ...");
        var url = $(this).attr("data-enlace");
        var cedula = $(this).val();
        $.ajax({
            url: url,
            type: "GET",
            data: "cedula=" + cedula,
            success: function (data) {
                $("#tabla_queja").html(data);
            }
        });
    });

    $(document).on('blur', "#fechaComite", function () {

        var url = $(this).attr("data-enlace");
        var fechaHora = $(this).val();
        $.ajax({
            url: url,
            type: "GET",
            data: "fechaHora=" + fechaHora,
            success: function (data) {
                $("#fecha_incorrecta").html(data);
                validarHorario();
            }
        });
    });
    
    // Plantilla
	$(document).on("click","#generarPlantilla",function(){
		var valor = $("#plantilla").val();
		var url = $("#contenidoPlantilla").attr("data-url");
		// alert(url);
		$.ajax({
			url: url,
			type: "GET",
			data: "valor="+valor,
			success: function(respuesta){
				$("#contenidoPlantilla").html(respuesta);
			}
		});
    });

    $(document).on('keyup', "#comite_implicado", function () {

        $("#tabla_queja").html("Cargando ...");
        var url = $(this).attr("data-enlace");
        var cedula = $(this).val();
        $.ajax({
            url: url,
            type: "GET",
            data: "cedula=" + cedula,
            success: function (data) {
                $("#tabla_queja").html(data);
            }
        });
    });

    $(document).on('click', ".agregarAprendiz", function () {

        var cedula = $(this).attr("data-cedula");
        var text = $(this).attr("data-text");
        if ($("#" + cedula).length <= 0) {
            $("#tabla_queja_final").append("<tr id='" + cedula + "' style='text-transform: uppercase' class='aprendizSelect'><td><small>" + text + "</small><input type='hidden' value='" + cedula + "' data-text='" + text + "' name='aprendices[]' />\n\
            <span data-remove='" + cedula + "' class='removerAprendiz btn btn-danger btn-app-sm' style='float:right'><i class='fa fa-minus-square'></i></span></td></tr>");
        }
        validaTablas();
    });
    $(document).on('click', ".agregarImplicado", function () {

        var cedula = $(this).attr("data-cedula");
        var text = $(this).attr("data-text");
        var rol = $(this).attr("data-rol");
        if ($("#" + cedula).length <= 0) {
            $("#tabla_queja_final").append("<tr id='" + cedula + "' style='text-transform: uppercase' class='implicadoSelect'><td><small>" + text + "</small><code><small>" + rol + "</small></code><input type='hidden' value='" + cedula + "' data-text='" + text + "' name='aprendices[]' />\n\
            <span data-remove='" + cedula + "' class='removerImplicado btn btn-danger btn-app-sm' style='float:right'><i class='fa fa-minus-square'></i></span></td></tr>");
        }
        validaTablas();
    });
    $(document).on('click', ".agregarLiteral", function () {

        var capitulo = $(this).attr("data-capitulo");
        var articulo = $(this).attr("data-articulo");
        var literal = $(this).attr("data-literal");
        var literalCodigo = $(this).attr("data-literalCodigo");

        if ($("#" + literal).length <= 0) {
            $("#tabla_literal_final").append("<tr id='" + literal + "' style='text-transform: uppercase' class='literalSelect'><td><small>" + capitulo + " " + articulo + " Lit. " + literalCodigo + "</small><input type='hidden' value='" + literal + "' data-text='" + capitulo + " " + articulo + " Literal " + literalCodigo + "' name='literales[]' />\n\
            <span data-remove='" + literal + "' class='removerLiteral btn btn-danger btn-app-sm' style='float:right'><i class='fa fa-minus-square'></i></span></td></tr>");
        }
        validaTablas();
    });
    $(document).on('click', ".removerAprendiz", function () {

        var cedula = $(this).attr("data-remove");
        $("#" + cedula).remove();
        validaTablas();
    });
    $(document).on('click', ".removerImplicado", function () {

        var cedula = $(this).attr("data-remove");
        $("#" + cedula).remove();
        validaTablas();
    });
    $(document).on('click', ".removerLiteral", function () {

        var literal = $(this).attr("data-remove");
        $("#" + literal).remove();
        validaTablas();
    });
    
    $(".activeHere").trigger("click", function () {
        $(".otraActive"), addClass('active');
    });
    $("#content").on('change', 'select', function () {

        if ($(this).hasClass("ajax-change")) {

            var url = $(this).attr("data-url");
            $.ajax({
                mimeType: 'text/html; charset=utf-8', // ! Need set mimeType only when run from local file
                url: url,
                type: 'GET',
                data: "id=" + $(this).val(),
                success: function (data) {
                    $('#act_version').html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(errorThrown);
                },
                dataType: "html",
                async: false
            });
        }

    });

    $(document).on('change', "#capitulos", function () {

        $('#tabla_literal').html("Cargando ...");
        var url = $(this).attr("data-url");
        $.ajax({
            mimeType: 'text/html; charset=utf-8', // ! Need set mimeType only when run from local file
            url: url,
            type: 'GET',
            data: "id=" + $(this).val(),
            success: function (data) {
                $('#tabla_literal').html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            },
            dataType: "html",
            async: false
        });


    });

    $(document).on('click', '.cargarAjax', function () {

        var url = $(this).attr("data-url");
        var id = $(this).attr("data-id");
        var estado = $(this).attr("data-estado");
        $('#modalBody').html("Cargando...");
        $.ajax({
            mimeType: 'text/html; charset=utf-8', // ! Need set mimeType only when run from local file
            url: url,
            type: 'GET',
            data: "id=" + id + "&estado=" + estado,
            success: function (data) {
                $('#modalBody').html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            },
            dataType: "html",
            async: false
        });
    });

/*eliminar comite*/
	$(document).on("click","#eliminar",function(){
		
		var index =$(this).attr("data-url");
		
		 if(confirm('¿ Estas seguro Eliminar ?')){
		
			window.location.href=index;
			
		 }
	});
	
	var fila = $("#fila").html();
	$(document).on("click",".agregarFila",function(){
		$("#contenedorFila").append("<div id='fila' class='col-lg-12 fila'>"+fila+"</div>");
	});
	
	$(document).on("click",".eliminarFila",function(){
		var contador = 0;
		$(this).parent().parent().parent().find(".agregarFila").each(function(){
			contador++;
		});
		
		if(contador > 1){
			$(this).parent().parent().remove();
		}else{
			alert("No puedes eliminar la unica fila");
		}
	});
	
	// Contenido modal de las plantillas
	$(document).on('click', ".contenidoPlantilla", function() {
		var valor = $(this).attr("data-id");
		var url = $(this).attr("data-url");
		
		$.ajax({
			url: url,
			type: "GET",
			data: "pla_id="+valor,
			success: function(respuesta){
				$("#contenidoModal").html(respuesta);
			}
		});
	});
});

$(document).ready(function () {
    jQuery.extend(jQuery.validator.messages, {
        required: "Este campo es obligatorio.",
        remote: "Por favor, rellena este campo.",
        email: "Por favor, escribe una dirección de correo válida",
        url: "Por favor, escribe una URL válida.",
        date: "Por favor, escribe una fecha válida.",
        dateISO: "Por favor, escribe una fecha (ISO) válida.",
        number: "Por favor, escribe un número entero válido.",
        digits: "Por favor, escribe sólo dígitos.",
        creditcard: "Por favor, escribe un número de tarjeta válido.",
        equalTo: "Por favor, escribe el mismo valor de nuevo.",
        accept: "Por favor, escribe un valor con una extensión aceptada.",
        maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."),
        minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."),
        rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."),
        range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."),
        max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."),
        min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.")
    });

    $(function () {
        var $win = $(window);
        var $pos = 1;
        
        $win.scroll(function () {

            if ($win.scrollTop() <= $pos)
                $('.boton-flotante').removeClass('fijar').addClass('no-fijar').attr("scroll",$win.scrollTop());
            else {
                $('.boton-flotante').removeClass('no-fijar').addClass('fijar').attr("scroll",$win.scrollTop());
            }
        });
    });
    
    $(document).on("click",".trigger",function(e){
        e.preventDefault();
        
        $($(this).attr("data-target")).trigger("click");
    });
});




