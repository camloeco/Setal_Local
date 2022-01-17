<div id="sidebar-left" class="col-xs-2 col-sm-2">
    <ul class="nav main-menu">
        <li>
            <a style="text-align: center;">
                <span class="hidden-xs"  style="font-size:13px;">Dashboard</span>
            </a>
        </li>
        <?php

        $url = explode("/", $_SERVER['REQUEST_URI']);
        if(isset($url[1])) {
            $moduloUrl = $url[1];
        }
        if(isset($url[2])) {
            $controladorUrl = $url[2];
        }
        if(isset($url[3])) {
            $funcionUrl = $url[3];
        }
        ?>
        @foreach($menu as $modulo=>$controladores)

        @foreach($controladores as $controlador=>$funciones)
        <li class="dropdown">
            <?php

            $class = "";
            if (in_array(substr($_SERVER['REQUEST_URI'], 1, strlen($_SERVER['REQUEST_URI'])) . "/", $funciones)) {
                $class = " active-parent active activeHere";
            }

            ?>
            <a style="padding:10px 0px 0px 5px;" href="#" class="dropdown-toggle {{ $class }}">
                <!-- <i class="glyphicon glyphicon-chevron-right"></i> -->
                <img src="{{ asset('img/flecha-derecha.png') }}">
                <span class="hidden-xs">{{ ucfirst(mb_strtolower($controlador)) }}</span>
            </a>
            <ul class="dropdown-menu">
                @foreach($funciones as $funcion=>$href)
                <?php

                $hrefUrl = explode("/", $href);
                $moduloHref = $hrefUrl[0];
                $controladorHref = $hrefUrl[1];
                $funcionHref = $hrefUrl[2];
                
                $classHija = "";
                if (($moduloUrl == $moduloHref) && ($controladorUrl == $controladorHref) && ($funcionUrl == $funcionHref)) {
                    $classHija = " active-parent active otraActive";
                }

                ?>
                <li><a  style="font-size:13px;" class="ajax-link {{ $classHija }}" href="{{ url($href) }}">{{ ucwords(mb_strtolower($funcion)) }}</a></li>
                @endforeach
            </ul>
        </li>
        @endforeach
        @endforeach

    </ul>
</div>

