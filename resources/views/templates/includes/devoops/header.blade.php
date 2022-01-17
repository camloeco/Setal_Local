<!--Start Header-->
<div id="screensaver">
    <canvas id="canvas"></canvas>
    <i class="fa fa-lock" id="screen_unlock"></i>
</div>
<div id="modalbox">
    <div class="devoops-modal">
        <div class="devoops-modal-header">
            <div class="modal-header-name">
                <span>Basic table</span>
            </div>
            <div class="box-icons">
                <a class="close-link">
                    <i class="fa fa-times"></i>
                </a>
            </div>
        </div>
        <div class="devoops-modal-inner">
        </div>
        <div class="devoops-modal-bottom">
        </div>
    </div>
</div>
<header class="navbar">
    <div class="container-fluid expanded-panel">
        <div class="row">
            <div id="logo" class="col-xs-7 col-sm-8">
                <a href="index.html">
                <img src="{{ url('devoops/img/logo_sena.png') }}" alt="Sena" /> SETALPRO <small class="hidden-xs">/ Seguimiento etapa lectiva y productiva</small></a>
            </div>
            <div id="top-panel" class="col-xs-5 col-sm-4">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 top-panel-right">
                        <ul class="nav navbar-nav pull-right panel-menu">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle account" data-toggle="dropdown">
                                    <div class="avatar">
                                        <img src="{{ getGravatar(Auth::user(), 40) }}" class="img-circle" alt="avatar" />
                                    </div>
                                    <i class="fa fa-angle-down pull-right"></i>
                                    <div class="user-mini pull-right">
                                        <span class="welcome">Bienvenido,</span>
                                        <span>{{ Auth::user()->participante->par_nombres." ".Auth::user()->participante->par_apellidos }}</span>
                                    </div>
                                </a>
                                <ul class="dropdown-menu" style="background-color:#ec7114">
                                    <li style="border-top: 1px solid;border-left: 1px solid #5d3b3b;border-right: 1px solid #060606;border-bottom: 1px solid #040404;">
                                        <a class="ajax-link" href="{{ url("/users/users/showprofile/".Auth::user()->id) }}">
                                            <i class="fa fa-user"></i>
                                            <span>Ver perfil</span>
                                        </a>
                                    </li>
                                    <li style="border-left: 1px solid;border-right: 1px solid;border-bottom: 1px solid;">
                                        <a class="ajax-link" href="{{ url("/users/users/editprofile/".Auth::user()->id) }}">
                                            <i class="fa fa-user"></i>
                                            <span>Editar perfil</span>
                                        </a>
                                    </li>
                                    <li style="border-left: 1px solid;border-right: 1px solid;border-bottom: 1px solid;">
                                        <a href="{{ url('auth/logout') }}">
                                            <i class="fa fa-power-off"></i>
                                            <span>Cerrar sesi&oacute;n</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!--End Header-->