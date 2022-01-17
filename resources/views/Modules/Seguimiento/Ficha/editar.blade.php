@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Creaci&oacute;n de ficha') !!}

<div class="row">
@include('errors.messages')
    @if (isset($mensaje))
        <div class="col-xs-12 col-sm-12">
            <div class="box ui-draggable ui-droppable">
                <div class="box-header">
                    <div class="box-name ui-draggable-handle">
                        <i class="fa fa-search"></i>
                        <span>Mensaje de respuesta</span>
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
                    @if (isset($mensaje['exito']))
                    <div class="alert alert-success">
                        <button data-dismiss="success" class="close" type="button">×</button>
                        {{ $mensaje['exito'] }}
                    </div>
                    @endif

                    @if (isset($mensaje['errores']))
                    <div class="alert alert-danger">
                        <button data-dismiss="alert" class="close" type="button">×</button>

                        <ul>
                            @foreach ($mensaje['errores'] as $key=>$msg)
                            <li>{{ $msg }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Creaci&oacute;n de la ficha</span>
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
            <div class="box-content" >

                {!! Form::open(array("url" => url("seguimiento/ficha/editar"), "method"=>"post", "class"=>"form-horizontal", "id"=>"editarFicha")) !!}
                <div class="form-group">
                    {!! Form::label("fic_numero","N&uacute;mero de la ficha",array("for"=>"fic_numero","class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-6">
                        {!! Form::text("fic_numero", $ficha[0]->fic_numero, array("id"=>"fic_numero","class"=>"col-md-4 form-control","readonly"=>"readonly", "required"=>"required")) !!}
                    </div>
                </div>
                
                <div class="form-group">
                    {!! Form::label("fic_proyecto","C&oacute;digo del proyecto",array("for"=>"fic_proyecto","class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-4">
                        <select data-rel="chosen" id="fic_proyecto" name="fic_proyecto" class="form-control ajax-change" required="required">
                        <option value="">-- Seleccione proyecto de formaci&oacute;n --</option>
                        <?php if(count($proyecto2) == 0){
                            echo "<option value='".$ficha[0]->fic_proyecto."' selected>".$ficha[0]->fic_proyecto."</option>";
                        ?>
                            @foreach($proyecto as $pro)
                                <option value="{{$pro->pro_codigo}}" >{{$pro->pro_codigo}}</option>
                            @endforeach
                        <?php 
                        }else{
                        ?>
                            @foreach($proyecto as $pro)
                                @if($pro->pro_codigo == $ficha[0]->fic_proyecto)
                                    <option value="{{$pro->pro_codigo}}" selected>{{$pro->pro_codigo}}</option>
                                @else
                                    <option value="{{$pro->pro_codigo}}">{{$pro->pro_codigo}}</option>
                                @endif
                            @endforeach
                        <?php 
                        }
                        ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    {!! Form::label("cen_codigo","Centro de formaci&oacute;n",array("for"=>"cen_codigo","class"=>"control-label col-md-3")) !!}
                    
                    <div class="col-sm-6">
                        <select data-rel="chosen" id="cen_codigo" name="cen_codigo" class="" required="required">
                            @foreach($centros as $key=>$centro)
                                <?php if($key==$ficha[0]->cen_codigo){
                                    ?>
                                    <option value="{{ $key }}" selected>{{ $centro }}</option>
                                    <?php
                                } 
                                else{?>
                                    <option value="{{ $key }}">{{ $centro }}</option>
                                <?php }?>
                                
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label("prog_codigo","Programa de formaci&oacute;n",array("for"=>"prog_codigo","class"=>"control-label col-md-3")) !!}
                    
                    <div class="col-sm-6">
                        <select data-rel="chosen" id="prog_codigo" name="prog_codigo" data-url="{{ url("seguimiento/ficha/version") }}" class="ajax-change" required="required">
                            <option value="{{ $key }}">-- Seleccione programa de formaci&oacute;n --</option>
                            @foreach($programas as $key=>$programa)
                                <?php if($key==$ficha[0]->prog_codigo){
                                    ?>
                                    <option value="{{ $key }}" selected>{{ $programa }}</option>
                                    <?php
                                } 
                                else{?>
                                    <option value="{{ $key }}">{{ $programa }}</option>
                                <?php }?>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    {!! Form::label("act_version","Versi&oacute;n",array("for"=>"act_version","class"=>"control-label col-md-3")) !!}
                    
                    <div class="col-sm-6">
                        <select data-rel="chosen" id="act_version" name="act_version" class="" required="required">
                            <?php 
                            for($i=0;$i<=$version[0]->ver;$i++){?>
                                <?php if($i==$ficha[0]->fic_version_matriz){
                                    ?>
                                    <option value="{{ $i }}" selected>Versi&oacute;n {{ $i }}.0</option>
                                    <?php
                                } 
                                else{?>
                                    <option value="{{ $i }}">Versi&oacute;n {{ $i }}.0</option>
                            <?php }
                            
                                }?>
                                
                            
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    {!! Form::label("par_identificacion","Instructor L&iacute;der",array("for"=>"par_identificacion","class"=>"control-label col-md-3")) !!}
                    
                    <div class="col-sm-6">
                        <select data-rel="chosen" id="par_identificacion" name="par_identificacion" class="" required="required">
                            @foreach($instructores as $key=>$instructor)
                            <?php if($key==$ficha[0]->par_identificacion){
                                    ?>
                                    <option value="{{ $key }}" selected>{{ $instructor }}</option>
                                    <?php
                                } 
                                else{?>
                                    <option value="{{ $key }}">{{ $instructor }}</option>
                                <?php }?>
                                
                            @endforeach
                        </select>
                    </div>

                </div>
                
                <div class="form-group">
                    {!! Form::label("par_identificacionC","Coordinador Acad&eacute;mico",array("for"=>"par_identificacionC","class"=>"control-label col-md-3")) !!}
                    
                    <div class="col-sm-6">
                        <select data-rel="chosen" id="par_identificacionC" name="par_identificacionC" class="" required="required">
                            @foreach($coordinadores as $key=>$coordinador)
                            <?php if($key==$ficha[0]->par_identificacion_coordinador){
                                    ?>
                                    <option value="{{ $key }}" selected>{{ $coordinador }}</option>
                                    <?php
                                } 
                                else{?>
                                    <option value="{{ $key }}">{{ $coordinador }}</option>
                                <?php }?>
                                
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="form-group has-error has-feedback">
                    {!! Form::label("duracion","Duraci&oacute;n",array("for"=>"hora","class"=>"control-label col-md-3")) !!}
                            <div class="col-sm-3">
                                <div class='input-group date' id='datetimepicker1'>
                                    <input type='text' class="form-control" name="fic_fecha_inicio" value="{{$ficha[0]->fic_fecha_inicio}}" required="required"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>    
                            </div>
                            <div class="col-sm-3">
                                <div class='input-group date' id='datetimepicker2'>
                                    <input type='text' class="form-control" name="fic_fecha_fin" value="{{$ficha[0]->fic_fecha_fin}}" required="required"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>    
                            </div>
                </div>

                <div class="form-group">
                    {!! Form::label("fic_localizacion","Localizaci&oacute;n",array("for"=>"fic_localizacion","class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-6">
                        {!! Form::text("fic_localizacion", $ficha[0]->fic_localizacion, array("id"=>"fic_localizacion","placeholder"=>"Pradera, Florida, Etc", "class"=>"form-control col-md-4", "required"=>"required")) !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">			
                        {!! Form::submit("Guardar", array("class"=>"btn btn-success control-label ajax-link")) !!}
                    </div>
                </div>


                {!! Form::close() !!}


            </div>
        </div>
    </div>
    <!--/span-->
</div>

@endsection

@section("plugins-css")
<link rel="stylesheet" href="{{ asset("css/bootstrap-datetimepicker.css") }}">
@endsection

@section("plugins-js")
<script type="text/javascript" src="{{ asset("devoops/plugins/moment/moment.min.js") }}"></script>
<script type="text/javascript" src="{{ asset("devoops/plugins/select2/select2.min.js") }}"></script>
<script type="text/javascript" src="{{ asset("js/bootstrap-datetimepicker.js") }}"></script>
  <script type="text/javascript">
            $(function () {
                $('#datetimepicker1').datetimepicker();
            });
            $(function () {
                $('#datetimepicker2').datetimepicker();
            });
        </script>

        
<script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {

    $.validator.setDefaults({
        ignore: []
    });

    $('#editarFicha').validate(/*{
        submitHandler: validarFormTabs()
    }*/);
});
</script>


<script type="text/javascript">

// Run Select2 on element
    function Select2Test() {
        $("#par_identificacion").select2();
        $("#par_identificacionC").select2();
        $("#act_version").select2();
        $("#prog_codigo").select2();
        $("#cen_codigo").select2();
    }
    $(document).ready(function () {
        // Load script of Select2 and run this
        LoadSelect2Script(Select2Test, '<?php echo asset('devoops/plugins/select2/select2.min.js') ?>');
        WinMove();
    });
</script>

@endsection