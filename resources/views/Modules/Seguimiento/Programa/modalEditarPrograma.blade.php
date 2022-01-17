<div class="row">
    @foreach($programa as $pro)
    <div class="col-lg-12 col-smd-12 col-sm-12 col-xs-12">
        <label>C&oacute;digo</label>
        <input required name="prog_codigo" readonly value="{{ $pro->prog_codigo}}" class="form-control" type="text">
        <label>Programa</label>
        <textarea required name="prog_nombre" maxlength="250" class="form-control">{{ $pro->prog_nombre}}</textarea>
        <label>Nivel</label>
        <select required name="niv_for_id" class="form-control">
            <option value="">Seleccione...</option>
            @foreach($niveles_formacion as $niv)
            <?php
                $selected = '';
                if($niv->niv_for_id == $pro->niv_for_id){
                    $selected = 'selected';
                }
            ?>
            <option {{ $selected }} value="{{ $niv->niv_for_id }}">{{ $niv->niv_for_nombre }}</option>
            @endforeach
        </select>
        <label>Sigla</label>
        <input name="prog_sigla" maxlength="20" value="{{ $pro->prog_sigla }}" class="form-control" type="text">
    </div>
    @endforeach
</div>
    