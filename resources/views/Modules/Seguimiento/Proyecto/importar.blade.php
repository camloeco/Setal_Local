@extends('templates.devoops')

@section('content')

{!! getHeaderMod('Seguimiento a proyectos','Importar proyectos de formaci&oacute;n') !!}

<div class="row">

    <div class="col-xs-12 col-sm-12">
        <div class="box ui-draggable ui-droppable">
            <div class="box-header">
                <div class="box-name ui-draggable-handle">
                    <i class="fa fa-search"></i>
                    <span>Importar Proyectos</span>
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
                <div class="row">
                    <!-- Aqui se visualiza la validacion del Registro Importado  -->
                    <?php 
                        if (isset($_SESSION['result'])){
                    ?>
                        <div id="alert" class="alert alert-success alert-dismissible  show" role="alert">
                            <?php echo $_SESSION['result'];?><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                    <?php 
                        }unset($_SESSION['result']);
                    ?>
                    <form action="{{url('seguimiento/proyecto/importar')}}" enctype="multipart/form-data"  method="post">
                        <input type="hidden" name="_token" id="token" value="<?php echo csrf_token();?>">
                        <div class="form-group">
                            <div align="right" class="col-md-6">
                                <input type="file" name="archivo" id="importar" class="form-control" />
                            </div>
                            <div align="right" class="col-md-4">
                                <button id="btn_lectura" class="btn btn-success">Importar</button>
                            </div>
                        </div>
                    </form>
                    <div align="right" class="col-md-1">
                        <a href="{{url('Formato_proyecto/Proyectos.xlsx')}}" type="button" class="btn btn-danger">Exportar Formato</a>
                    </div>
                </div>
           </div>
        </div>
    </div>
</div>
@endsection

@section('plugins-js')

<script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>

@endsection

