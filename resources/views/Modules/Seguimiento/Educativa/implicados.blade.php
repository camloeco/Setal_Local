@if(isset($aprendices))
    @foreach($aprendices as $cedula=>$nombre)
        <tr>
            <td style="text-transform: uppercase">
                <span class="col-lg-10">
                <small>{{ $nombre['nombre'] }}</small><br />
                <code><small>Rol: {{ $nombre['rol'] }}</small></code></span>
                <span data-cedula="{{ $cedula }}" 
                      data-text="{{$nombre['nombre']}}" 
                      data-rol="{{$nombre['rol']}}" 
                      class="col-lg-2 agregarImplicado btn btn-primary btn-app-sm" style="float:right">
                    <i class="fa fa-plus-circle"></i>
                </span>
            </td>
        </tr>
    @endforeach
@else
    <tr><td>No hay datos</td></tr>
@endif