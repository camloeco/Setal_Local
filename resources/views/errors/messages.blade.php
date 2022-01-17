
@if($errors->any())
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

            <div class="alert alert-danger">
                
                <i class="icon_error-triangle"> </i>

    Por favor corrige los siguientes errores:
                <ul>
                    <ul>
                        @foreach($errors->all()as $error)
                        <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </ul>
            </div>
        </div>
    </div>
</div>
@endif