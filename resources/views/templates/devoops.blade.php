<?php 
$menu = App::make('App\Http\Base\Lib\MenuClass')->menuFinal;
?>

@if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Setalpro</title>
        <meta name="description" content="description">
        <meta name="author" content="CDTI - Sena">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="{{ asset('devoops/plugins/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('devoops/plugins/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet">
        <link rel="shortcut icon" href="{{ asset('img/logo_sena.ico') }}">
        <!-- FontAwesome 4.3.0 -->
        <!-- <link href="{{ asset('/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" /> -->
        <!-- <link href="{{ asset('css/all.min.css') }}" rel="stylesheet" type="text/css" />-->

        <!--<link href='http://fonts.googleapis.com/css?family=Righteous' rel='stylesheet' type='text/css'>-->
        <!--<link href="{{ asset('devoops/plugins/fancybox/jquery.fancybox.css')}}" rel="stylesheet">-->
        <!--<link href="{{ asset('devoops/plugins/fullcalendar/fullcalendar.css')}}" rel="stylesheet">-->
        <!--<link href="{{ asset('devoops/plugins/xcharts/xcharts.min.css')}}" rel="stylesheet">-->
        <!--<link href="{{ asset('devoops/plugins/select2/select2.css')}}" rel="stylesheet">-->
        <!--<link href="{{ asset('devoops/plugins/justified-gallery/justifiedGallery.css')}}" rel="stylesheet">-->
        
        @endif
        <!-- Plugins necesarios para la vista -->
        @yield('plugins-css')

        @if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
        
        <link href="{{ asset('css/select2.css')}}" rel="stylesheet">
        <link href="{{ asset('devoops/css/style_v2.css?v=7')}}" rel="stylesheet">
        <!--<link href="{{ asset('devoops/plugins/chartist/chartist.min.css')}}" rel="stylesheet">-->
        <link href="{{ asset('css/setalpro.css')}}" rel="stylesheet">
        <link href="{{ asset('css/notify.setalpro.css')}}" rel="stylesheet">
        <!--<link href="{{ asset('css/jquery.dataTables.css')}}" rel="stylesheet">-->
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="http://getbootstrap.com/docs-assets/js/html5shiv.js"></script>
            <script src="http://getbootstrap.com/docs-assets/js/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        @include('templates.includes.devoops.header')
        <!--Start Container-->
        <div id="main" class="container-fluid {{ ($menu)?"":"sidebar-show" }}">
            <div class="row">
                @include('templates.includes.devoops.main-left')
                <!--Start Content-->
                <div id="content" class="col-xs-12 col-sm-10">
                    <!--<div class="preloader--">
                        <img src="devoops/img/devoops_getdata.gif" class="devoops-getdata" alt="preloader"/>
                    </div>-->
                    <div id="ajax-content">
                        @endif

                        @yield('content')

                        @if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
                    </div>
                </div>
                <!--End Content-->
            </div>
        </div>
        <!--End Container-->
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <!--<script src="{{ asset('devoops/http://code.jquery.com/jquery.js')}}"></script>-->
        <script src="{{ asset('devoops/plugins/jquery/jquery.min.js')}}"></script>
        <script src="{{ asset('devoops/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="{{ asset('devoops/plugins/bootstrap/bootstrap.min.js')}}"></script>
        <!--<script src="{{ asset('js/all.min.js') }}"></script>-->
        <!--<script src="{{ asset('devoops/plugins/justified-gallery/jquery.justifiedGallery.min.js')}}"></script>-->
        <!--<script src="{{ asset('devoops/plugins/tinymce/tinymce.min.js')}}"></script>-->
        <!--<script src="{{ asset('devoops/plugins/tinymce/jquery.tinymce.min.js')}}"></script>-->

        @endif

        <!-- Plugins necesarios para la vista -->
        @yield('plugins-js')

        @if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))

        <!-- All functions for this theme + document.ready processing -->
        <script src="{{ asset('js/jquery.dataTables.js')}}"></script>
        <script src="{{ asset('js/select2.js')}}"></script>
        <script src="{{ asset('js/notify.setalpro.js')}}"></script>
        <!--<script src="{{ asset('devoops/js/devoops.js?v=2')}}"></script>-->
        <script src="{{ asset('js/global.js?v=14')}}"></script>
    </body>
</html>
@endif