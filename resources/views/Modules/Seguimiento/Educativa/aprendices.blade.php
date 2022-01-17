@if(isset($aprendices))
    @foreach($aprendices as $cedula=>$nombre)
        <tr>
            <td style="text-transform: uppercase;">
                <span class="col-lg-10" style="margin-top:18px;margin-bottom:10px;">
                    <small>{{ $nombre['nombre'] }}</small><br />
                    <code>Ficha: {{ $nombre['ficha'] }}</code></span>
                    @if(isset($beneficiario[$cedula]))
                        <small class="col-lg-10 bg-danger text-white" style="padding:10px;border-radius:10px;">
                            Este aprendiz tiene los siguientes beneficios sena:<span><br><br>
                            <?php echo $beneficiario[$cedula];?>
                        </small>
                    @endif
                    <span data-cedula="{{ $cedula }}" 
                        data-text="{{$nombre['nombre']}}" 
                        class="col-lg-2 agregarAprendiz btn btn-primary btn-app-sm" style="float:right;margin-top:20px">
                        <i class="fa fa-plus-circle"></i>
                    </span>
                </span>  
            </td>
        </tr>
    @endforeach
@else
    <tr><td>No hay datos</td></tr>
@endif