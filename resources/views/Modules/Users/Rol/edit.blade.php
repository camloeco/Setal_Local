 @extends('templates.backend')

@section('content')

{!! getHeaderMod('Gesti&oacute;n de Roles','Actualizaci&oacute;n de rol') !!}

<section class="content">
    <!-- form start -->
 
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Actualizaci&oacuten de Roles </h3>
                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-danger">Acciones</button>

                        <button type="button" class="btn btn-danger dropdown-toggle"
                                data-toggle="dropdown">
                            <span class="caret"></span>
                            <span class="sr-only">Desplegar menú</span>
                        </button>

                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url("usuarios/rol/index") }}"><i class="fa fa-list text-blue"></i>Todos los roles</a></li>
                    </div>                          
                    </div><!-- /.box-header -->


                    <div class="box-body">
                         {!! Form::open(array('url' => 'usuarios/rol/edit', 'method' => 'POST'), array('role' => 'form')) !!}
                       
                        <div class="form-group">
                            {!! Form::label("nombre_rol", "Nombre ", array("class"=>"col-sm-2 control-label")) !!}
                            <div class="col-sm-9 input-group">
                                {!! Form::text("nombre_rol", $rol->nombre_rol, array("class"=>"form-control")) !!}
                            </div>
                        </div>
                        <div class="col-sm-3"></div>
                        <div class="col-sm-8 input-group">
                             {!! Form::hidden("id", $rol->id_rol) !!}
                            {!! Form::submit("Actualizar", array("class"=>"btn btn-danger")) !!}
                            {!! Form::reset("Reiniciar campos", array("class"=>"btn btn-default")) !!}
                        </div>
                    </div><!-- /.box-footer -->
            {!! Form::close()!!}


            </div>
        </div>

        

</section>

@endsection
