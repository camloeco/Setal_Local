@if(isset($datosp))
@foreach($datosp as $datos)
<?php $rowspan = count($datos)+1; ?>
<tr>
    <td style="text-transform: uppercase" rowspan="<?php echo $rowspan;?>">
        <span class="col-lg-12">
            <small>{{ $datos[0]->art_codigo }}</small><br />
            <code><small>{{ $datos[0]->art_descripcion }}</small></code></span>
    </td>
    </tr>
@foreach($datos as $literal)
<tr>
    <td style="text-transform: uppercase">
        <span class="col-lg-10">
            <small>{{ $literal->lit_codigo }}</small><br />
            <code><small>{{ $literal->lit_descripcion }}</small></code></span>
        <span data-capitulo="{{ $literal->cap_codigo }}" 
              data-articulo="{{$literal->art_codigo}}" 
              data-literal="{{$literal->lit_id}}" 
              data-literalCodigo="{{$literal->lit_codigo}}" 
              class="col-lg-2 agregarLiteral btn btn-primary btn-app-sm" style="float:right">
            <i class="fa fa-plus-circle"></i>
        </span>
    </td>
</tr>
@endforeach

@endforeach
@else
<tr><td colspan="2">No hay datos</td></tr>
@endif