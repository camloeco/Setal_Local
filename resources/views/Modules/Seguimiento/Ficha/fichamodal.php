<div class="row">
 
    <div class="col-md-12">
        <div class="box-body">
            <dl class="dl-horizontal">
                <dt>Programa de formaci&oacute;n :</dt>
                <dd><?php echo $ficha[0]->prog_codigo . " " . $ficha[0]->prog_nombre; ?></dd>

                <dt>N&uacute;mero de la ficha :</dt>
                <dd><?php echo $ficha[0]->fic_numero; ?></dd>

                <dt>Instructor L&iacute;der :</dt>
                <dd><?php echo $ficha[0]->par_nombres . " " . $ficha[0]->par_apellidos; ?></dd>

                <dt>Fecha inicio :</dt>
                <dd><?php echo $ficha[0]->fic_fecha_inicio; ?></dd>

                
                <dt>Fecha fin :</dt>
                <dd><?php echo $ficha[0]->fic_fecha_fin; ?></dd>
                

                
                <dt>Localizaci&oacute;n :</dt>
                <dd><?php echo $ficha[0]->fic_localizacion; ?></dd>
                

                
                <dt>Versi&oacute;n planeaci&oacute;n :</dt>
                <dd><?php echo $ficha[0]->fic_version_matriz; ?></dd>
                
                
            </dl>
        </div><!-- /.box-body -->
    </div>
</div>

