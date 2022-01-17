@extends('templates.devoops')
@section('content')
{!! getHeaderMod('Seguimiento a proyectos','Importar plantilla') !!}
<div class="row">
    @if (isset($mensaje))
    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i>Mensaje de respuesta</i>
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
                @if(isset($mensaje['error']))
                    <div class="alert alert-danger" style="margin: 0px;">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        <strong>Solucione los siguientes errores!</strong>
                        <ol>
                            @foreach($mensaje['error'] as $msg)
                            <li><?php echo $msg; ?></li>
                            @endforeach
                        </ol>
                    </div>
                @else
                    <div class="alert alert-success" style="margin: 0px;">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        <strong>Exito!</strong> <?php echo $mensaje['exito'] ?>
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
                    <i>Importar plantilla</i>
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
                {!! Form::open(array("url" => url("seguimiento/programa/create"), "method"=>"post", "files"=> true, "class"=>"form-horizontal", "id"=>"createPrograma")) !!}

                <div class="form-group has-success has-feedback">
                    {!! Form::label("archivoCsv","Cargar archivo (Plan de trabajo)",array("for"=>"archivoCsv", "class"=>"control-label col-md-3")) !!}
                    <div class="col-sm-4">
                        {!! Form::file("archivoCsv",array("id"=>"archivoCsv","required"=>"required")) !!}
                        <p class="help-block">Cargar archivo en formato EXCEL.</p>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        {!! Form::submit("Cargar", array("class"=>"btn btn-success ajax-link")) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('plugins-js')
<script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $.validator.setDefaults({
            ignore: []
        });
        $('#createPrograma').validate();
    });
</script>
@endsection