@extends('templates.devoops')

@section('content')

    {!! getHeaderMod('Dashboard','Panel de Control') !!}
    <div class="row">
        <div class="col-md-6">
        <img src="{{url('img/robot.gif')}}" alt="">
        </div>
        <div class="col-md-6" style="padding:10px;">
            <h2>Bienvenido(a) {{ Auth::user()->participante->par_nombres." ".Auth::user()->participante->par_apellidos }}</h2><br>
            @if(date('m') == 12)
                <p>Que la paz , el amor, la prosperidad y la salud reinen en tu casa</p>
                <p>Te deseamos exitos en estas festividades navide&ntilde;as.</p><br>
            @else
                <p>
                    El sena es la instituci&oacute;n m&aacute;s querida por los colombianos, esto es gracias al valioso 
                    aporte de tu trabajo en el d&iacute;a a d&iacute;a.Queremos seguir por el camino correcto por lo cual contamos contigo para que el sena
                    siga creciendo y ayudando a miles de colombianos en su formaci&oacute;n laboral.
                </p>
            @endif
            <center>
                <img src="{{url('img/Sena-orange.png')}}" alt="" style="width: 200px;height: 200px;">
            </center>
        </div>
    </div>
@endsection