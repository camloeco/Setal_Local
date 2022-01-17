<div class="row">
    <?php if (isset($comi)) {
        ?>
        <div class="col-xs-12">
        <?php } else { ?>
            <div class="col-xs-6">
            <?php } ?>
        <div class="box">
            <div class="box-content">
                Instructor: <strong>{{ $instructor[0]->par_nombres." ".$instructor[0]->par_apellidos }}</strong>
            </div>
        </div>
    </div>
            
    <?php if (isset($comi)) {
        ?> 

        <div class="col-xs-6">
            <div class="box">
                <div class="box-content">
                    <div class="col-xs-12">
                        <b>Tipo de Comit&eacute;:</b>
                        <?php echo $comi[0]->edu_tipo_com_descripcion; ?>
                        <address>
                            <br />
                            <strong>Hora del Comit&eacute;:</strong>
                            <div><?php echo $comi[0]->edu_comite_hora; ?></div>
                        </address>
                        <address>
                            <strong>Fecha del Comit&eacute;:</strong><br>
                            <div><?php echo $comi[0]->edu_comite_fecha; ?></div>
                        </address>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    <?php } ?>
   
        <div class="col-xs-6">
   
            <div class="box">
                <div class="box-content">
                    <div class="col-xs-12">
                        <b>Tipo de Falta:</b>
                        <?php echo $queja[0]->edu_tipo_falta_descripcion; ?>
                        <address>
                            <br />
                            <strong>Descripci&oacute;n de la falta:</strong>
                            <div><?php echo $queja[0]->edu_falta_descripcion; ?></div>
                        </address>
                        <address>
                            <strong>Evidencia de la falta:</strong><br>
                            <div><?php echo $queja[0]->edu_falta_evidencia; ?></div>
                        </address>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-content">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>C&oacute;digo capitulo</th>
                                <th>Capitulo</th>
                                <th>C&oacute;digo articulo</th>
                                <th>Articulo</th>
                                <th>Literal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($literales as $lit)
                            <tr>
                                <td>{{ $lit->cap_codigo }}</td>
                                <td>{{ $lit->cap_descripcion }}</td>
                                <td>{{ $lit->art_codigo }}</td>
                                <td>{{ $lit->art_descripcion }}</td>
                                <td>{{ $lit->lit_descripcion }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>APRENDIZ</th>
                        <th># FICHA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(count($aprendicesQueja)==0){
                        echo "<code>El programa de formación de los aprendices no existe en nuestra base de datos, por favor contactar a los apoyos a coordinación</code>";
                    }else{
                        foreach ($aprendicesQueja as $aprendiz) {
                            echo "<tr>";
                            echo "<td class='m-ticker'><b>" . $aprendiz->par_nombres
                            . " " . $aprendiz->par_apellidos . "</b><span>Documento Identidad: <code>"
                            . $aprendiz->par_identificacion . "</code></span>"
                            . "<span>" . $aprendiz->prog_nombre . "</span></td>";
                            echo "<td><span class='tag tag-info'>" . $aprendiz->fic_numero . "</span></td>";
                            echo "</tr>";
                        }
                    }
                    ?>


                </tbody>
            </table>
        </div>
    </div>