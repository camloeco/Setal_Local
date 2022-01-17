<?php
    namespace App\Http\Controllers\Modules\Seguimiento;

    use PhpOffice\PhpWord\TemplateProcessor;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use DB;

    class CartaslaboralesController extends Controller {
        public function __construct(){
            $this->middleware('auth');
            $this->middleware('control_roles');
        }

        public function getImportardatos(){
            return view('Modules.Seguimiento.Cartaslaborales.importarDatos');
        }

        public function postImportardatos(Request $request){
            // ¿Se ha cargado el archivo CSV?
            if($request->hasFile('archivo')) {
                $archivo = $request->file('archivo');
                // ¿El archivo cumple con el formato esperado - EXCEL (xls, xlsx) ?
                if($archivo->getClientOriginalExtension() == 'xls' || $archivo->getClientOriginalExtension() == 'xlsx') {
                    $filename = time() . '-' . $archivo->getClientOriginalName();
                    // Configuracion del directorio multimedia
                    $pathCsv = getPathUploads() . '/CSV/CartaLaboral';
                    // Se mueve el archivo CSV al directorio multimedia
                    $archivo->move($pathCsv, $filename);
                    
                    $mensajes = validarDatosContratos($pathCsv, $filename);
                }else{
                    $mensajes['formato'] = 'El archivo no cumple con el formato esperado - CSV, por favor cargar un formato valido';
                } 
            }else{
                $mensajes['archivo'] = 'No se adjunto ning&uacute;n archivo';
            }
            session()->put('mensajes', $mensajes);
            return redirect(url('seguimiento/cartaslaborales/importardatos'));
        }

        public function getIndex(){
            extract($_GET);
            // Paginado
            $resgistroPorPagina = 15;
            $limit = $resgistroPorPagina;
            if(isset($pagina)){
                $hubicacionPagina = $resgistroPorPagina*($pagina-1);
                $limit = $hubicacionPagina.','.$resgistroPorPagina;
            }else{
                $pagina = 1;
            }

            // Validar busqueda del participante
            $concatenarInstructorPrimeraSQL = '';
            $concatenarInstructorSegundaSQL = '';
            if(isset($_GET['par_identificacion'])){
                $concatenarInstructorPrimeraSQL = ' and par.par_identificacion = "'.$par_identificacion.'" ';
                $concatenarInstructorSegundaSQL = ' where par_id_instructor = "'.$par_identificacion.'" ';
            }else{
                $par_identificacion = '';
            }

            // Consulta de todos los participantes
            $sql ='
                select  distinct(par_id_instructor) as par_id_instructor, 
                        concat(par_nombres," ",par_apellidos) as nombreCompleto
                from    sep_contrato con, sep_participante par
                where   con.par_id_instructor = par.par_identificacion 
                order by par_nombres';
            $participantes = DB::select($sql);

            $sql = '
                select  distinct(par_id_instructor) as par_id_instructor, 
                        concat(par_nombres," ",par_apellidos) as nombreCompleto
                from    sep_contrato con, sep_participante par
                where   con.par_id_instructor = par.par_identificacion '.$concatenarInstructorPrimeraSQL.'
                order by par_nombres limit '.$limit;
            
            $sqlContador = '
                select  count(distinct(par_id_instructor)) as total
                from    sep_contrato con, sep_participante par
                where   con.par_id_instructor = par.par_identificacion '.$concatenarInstructorPrimeraSQL;
            //dd($sqlContador);
            // Paginado
            $contratos = DB::select($sql);
            $contadorContratos = DB::select($sqlContador);
            $contadorContratos = $contadorContratos[0]->total;
            $cantidadPaginas = ceil($contadorContratos/$resgistroPorPagina);
            $contador = (($pagina-1)*$resgistroPorPagina)+1;
            $inicioContador = $contador;
            /*echo $contadorContratos;
            echo '<br>';
            echo $limit;
            dd($contador);*/

           //años de contratos registrados
           $sql="select con_anio_contrato
                 from sep_contrato
                 group by con_anio_contrato
                 order by con_anio_contrato asc";
            $anios = DB::select($sql);
            return view('Modules.Seguimiento.Cartaslaborales.index',compact('pagina','inicioContador','par_identificacion','participantes','contratos','contadorContratos','contador','cantidadPaginas','anios'));
        }

        public function getDescargarcartalaboral(){
            $rol = \Auth::user()->participante->rol_id;
            $anio_carta = $_GET['anio'];
            if($rol != 0 and $rol != 9){
                echo '<h1>Señor(a) Usted no esta autorizado(a) para ingresar a este espacio.</h1>';
                die();
            }
            if(!is_numeric($anio_carta)){
                echo '<h1>El campo del a&ntilde;o debe ser numerico.</h1>';
                die();
            }
            
            //validar que la existencia de la carta del año requerido
            $exite_anio=0;
            $sql="select con_anio_contrato
                 from sep_contrato
                 group by con_anio_contrato
                 order by con_anio_contrato asc";
            $anios = DB::select($sql);
            foreach ($anios as $an) {
                if ($an->con_anio_contrato == $anio_carta) {
                    $exite_anio++;
                }
            }

            if($exite_anio == 0){
                echo '<h1>El a&ntilde;o seleccionado no existe</h1>';
                die();
            }

            //Inicia creacion de word y su contenido
            $par_identificacion = $_GET['par_identificacion'];
            $documentoLogiado = \Auth::user()->par_identificacion;
            $logiado = DB::select('select par_nombres, par_apellidos from sep_participante where par_identificacion = "'.$documentoLogiado.'" limit 1');
            $elaboro = ucwords(strtolower($logiado[0]->par_nombres.' '.$logiado[0]->par_apellidos));
            $consecutivo = DB::insert('insert into sep_contrato_consecutivo (con_con_identificacion_elaboro) values("'.$documentoLogiado.'")');
            $consecutivo = DB::getPdo()->lastInsertId();
            //dd($elaboro);
            \PhpOffice\PhpWord\Autoloader::register();
            $templateWord = new TemplateProcessor(getPathUploads() . '/plantillas/formatoCartaLaboral.docx');
            //$mesEnLetras = array(1=>'enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre');
            $mesEnLetras = array('01'=>'enero','02'=>'febrero','03'=>'marzo','04'=>'abril',
                                '05'=>'mayo','06'=>'junio','07'=>'julio','08'=>'agosto',
                                '09'=>'septiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre');
            $sql = '
                select  par_id_instructor, concat(par_nombres," ",par_apellidos) as nombreCompleto, 
                        con_num_contrato, con_objeto, obli_descripcion, con_horas, con_anio_contrato,
                        con_fec_inicio,con_fec_fin,con_val_mensual,con_val_total, ciudad_expedicion
                from    sep_contrato con, sep_participante par, sep_obligaciones obli
                where   con.par_id_instructor = par.par_identificacion and par_identificacion = "'.$par_identificacion.'"
                and     con.obli_id = obli.obli_id and con.con_anio_contrato = '.$anio_carta.'
                order by con_anio_contrato, con_fec_inicio';
            $instructor = DB::select($sql);
            if (count($instructor) == 0) {
                $_SESSION['mensaje']='El instructor no tiene contrato registrado en el a&ntilde;o '.$anio_carta;
                echo "<script> window.history.back(); </script>";
                die();
            }
            $anioActual = date('Y');
            $mesActual = date('m');
            $diaActual = date('j');
            
            $saltoLinea = "\r<w:br/>";
            $texto = '';
            $dolar = '\$';
            $contador = 1;
            $ciudad_expedicion = $instructor[0]->ciudad_expedicion;
            $contadorContratos = count($instructor);
            foreach($instructor as $val){
                $textoHoras="";
                if ($val->con_horas != "" && $val->con_horas != 0) {
                    $textoHoras=" (".$val->con_horas." horas por mes).";
                }
                $texto .= $contador.'. Número y Fecha del Contrato: '.$val->con_num_contrato.' de '.$val->con_anio_contrato.$saltoLinea.$saltoLinea;
                $texto .= 'Objeto: '.$val->con_objeto.$saltoLinea.$saltoLinea;
                $texto .= 'Plazo de ejecución: '.$this->plazoLetras($val->con_fec_inicio, $val->con_fec_fin).$textoHoras.$saltoLinea.$saltoLinea;
                $texto .= 'Fecha de Inicio de Ejecución: '.$this->fechaConMesEnLetras($val->con_fec_inicio).$saltoLinea.$saltoLinea;
                $texto .= 'Fecha de Terminación de Contrato: '.$this->fechaConMesEnLetras($val->con_fec_fin).$saltoLinea.$saltoLinea;
                $texto .= 'Obligaciones Específicas del Contrato:  '.$saltoLinea.$saltoLinea.$val->obli_descripcion.$saltoLinea.$saltoLinea;
                $texto .= 'Valor del contrato: '.$this->numeroALetras($val->con_val_total).' ('.$dolar.str_replace(',', '.', number_format($val->con_val_total)).') ';
                if($contador < $contadorContratos){
                 $texto .= $saltoLinea.$saltoLinea;
                }
                $contador++;
            }

            $letras = array(1=>'uno','dos','tres','cuatro','cinco','seis','siete','ocho','nueve','diez',
                'once','doce','trece','catorce','quince','dieciséis','diecisiete','dieciocho','diecinueve','veinte',
                'veintiuno','veintidos','veintitres','veinticuatro','veinticinco','veintiseís','veintisiete','veintiocho','veintinueve','treinta','treinta y uno');
            $diaActualEnLetras = $letras[$diaActual];
            $documento = number_format($instructor[0]->par_id_instructor);
            $documento = str_replace(',', '.', $documento);
            $templateWord->setValue('consecutivo', $consecutivo);
            $templateWord->setValue('nombre', ucwords(strtolower($instructor[0]->nombreCompleto)));
            $templateWord->setValue('documento', $documento);
            $templateWord->setValue('anioActual', $anioActual);
            $templateWord->setValue('mesActual', $mesEnLetras[$mesActual]);
            $templateWord->setValue('diaActual', $diaActual);
            $templateWord->setValue('diaActualEnLetras', $diaActualEnLetras);
            $templateWord->setValue('texto', $texto);
            $templateWord->setValue('elaboro', $elaboro);
            $templateWord->setValue('ciudad_expedicion', $ciudad_expedicion);

            $ruta = public_path() . '/Modules/Seguimiento/Educativa/cartaLaboral/cartaLaboral-'.time().'.docx';
            $templateWord->saveAs($ruta);
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            //header("Pragma: no-cache");
            //unlink($ruta);
            //clearstatcache();
            return response()->download($ruta);
        }

        public function fechaConMesEnLetras($fecha){
            $arrayFecha = explode("-",$fecha);
            $mesEnLetras = array('01'=>'enero','02'=>'febrero','03'=>'marzo','04'=>'abril','05'=>'mayo','06'=>'junio','07'=>'julio','08'=>'agosto','09'=>'septiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre');
            $resultado = $arrayFecha[2].' de '.$mesEnLetras[$arrayFecha[1]].' de '.$arrayFecha[0];

            return $resultado;
        }

        public function numeroALetras($numero){
            $letras = array();
            $letras[0] = array('','Un','Dos','Tres','Cuatro','Cinco','Seis','Siete','Ocho','Nueve');
            $letras[1] = array('','Diez','Veinte','Treinta','Cuarenta','Cincuenta','Sesenta','Setenta','Ochenta','Noventa');
            $letras[2] = array('','Ciento','Doscientos','Trescientos','Cuatrocientos','Quinientos','Seiscientos','Setecientos','Ochocientos','Novecientos');

            $cantidadDeDigitos = strlen($numero);
            $cantidadDeIteraciones = ceil($cantidadDeDigitos/3);
            $numerosAgrupados = array();
            if($cantidadDeDigitos == 2){
                $resta = 2;
            }else if($cantidadDeDigitos == 1){
                $resta = 1;
            }else{
                $resta = 3;
            }
            $inicio = $cantidadDeDigitos-$resta;
            $fin = 3;

            for($i=1; $i<=$cantidadDeIteraciones; $i++){
                $numerosAgrupados[] = substr($numero,$inicio,$fin);
                $inicio-=3;
                if($inicio < 0){
                    $fin = $inicio+3;
                    $inicio = 0;
                }
            }

            $puntosMiles = array('Pesos', 'Mil', 'Millones');
            $concatenandoLetras = '';
            foreach($numerosAgrupados as $key => $val){
                $contadorLetrasRecorre = strlen($val);
                $contadorEnReversa = $contadorLetrasRecorre;
                if($contadorLetrasRecorre == 3){
                    $posiones = array(2,1,0);
                }else if ($contadorLetrasRecorre == 2){
                    $posiones = array(1,0);
                }else{
                    $posiones = array(0);
                }
                for($i=0; $i<$contadorLetrasRecorre; $i++){
                    $numeroInterno = substr($val,'-'.$contadorEnReversa,1);
                    if($letras[$posiones[$i]][$numeroInterno].' ' != ' '){
                        $concatenandoLetras .= $letras[$posiones[$i]][$numeroInterno].' ';
                    }
                    if($i != ($contadorLetrasRecorre-1) and $i != 0 and $numeroInterno > 2){
                        $concatenandoLetras .= 'Y ';
                    }
                    $contadorEnReversa--;
                }
                
                $concatenandoLetras .= $puntosMiles[$key];
                $concatenandoLetras = str_replace("Ciento Mil", "Cien Mil", $concatenandoLetras);
                $concatenandoLetras = str_replace("Diez Un", "Once", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Diez Dos", "Doce", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Diez Tres", "Trece", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Diez Cuatro", "Catorce", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Diez Cinco", "Quince", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Diez Seis", "Dieciséis", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Diez Siete", "Diecisiete", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Diez Ocho", "Dieciocho", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Diez Nueve", "Diecinueve", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Veinte Un", "Veintiun", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Veinte Dos", "Veintidos", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Veinte Tres", "Veintitres", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Veinte Cuatro", "Veinticuatro", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Veinte Cinco", "Veinticinco", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Veinte Seis", "Veintiseís", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Veinte Siete", "Veintisiete", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Veinte Ocho", "Veintiocho", $concatenandoLetras); 
                $concatenandoLetras = str_replace("Veinte Nueve", "Veintinueve", $concatenandoLetras);
                $concatenandoLetras = str_replace("Ciento Mil", "Cien Mil", $concatenandoLetras);
                $concatenandoLetras = str_replace("Un Pesos", "Un Peso", $concatenandoLetras);
                $concatenandoLetras = str_replace("Y Pesos", "Pesos", $concatenandoLetras);
                $concatenandoLetras = str_replace("Ciento Pesos", "Cien Pesos", $concatenandoLetras);
                $concatenandoLetras = str_replace("Y Mil", "Mil", $concatenandoLetras);

                $numeroEnLetras[] = $concatenandoLetras;
                $concatenandoLetras = '';
            }
            
            $numeroEnLetras = array_reverse($numeroEnLetras, true);
            $resultadoNumeroEnLetras = implode(" ",$numeroEnLetras);
           
            return $resultadoNumeroEnLetras;
        }

        public function plazoLetras($fechaInicio, $fechaFin){
            /*$fechaInicio = '2019-11-20';
            $fechaFin = '2019-12-01';*/

            // Validaciones
            if($fechaInicio > $fechaFin){
                echo "<h1>La fecha inicio del contrato <strong>$fechaInicio</strong> es mayor a la fecha de fin del contrato <strong>$fechaFin</strong>.</h1>";
                dd();
            }else if($fechaInicio == $fechaFin){
                echo "<h1>La fecha inicio del contrato <strong>$fechaInicio</strong> y la fecha de fin del contrato <strong>$fechaFin</strong> no pueden ser iguales.</h1>";
                dd();
            }
            
            $fecha_fin_oyio = substr($fechaFin, 0, 4);
            $fecha_fin_mes = substr($fechaFin, 5, 2);
            $fecha_fin_dia = substr($fechaFin, 8, 2);
            $fecha_inicio_dia = substr($fechaInicio, 8, 2);
            
            $meses = 0;
            $dias = 0;
            while($fechaInicio < $fechaFin){
                $fechaRecorreMasUnMes = date("Y-m-d", strtotime($fechaInicio." + 1 month"));
                if($fechaRecorreMasUnMes <= $fechaFin ){
                    $meses++;
                }else{
                    $diasMesFechaFin = cal_days_in_month(CAL_GREGORIAN, $fecha_fin_mes, $fecha_fin_oyio);
                    if($fecha_fin_dia == $diasMesFechaFin and $fecha_inicio_dia == '01'){
                        $meses++;
                    }else{
                        $dias = DB::select('select timestampdiff(day, "'.$fechaInicio.'", "'.$fechaFin.'") as dias');
                        $dias = $dias[0]->dias;
                    }
                }
                $fechaInicio = date("Y-m-d", strtotime($fechaInicio." + 1 month"));
            }

            $resultado = '';
            if($meses > 0 ){
                if($meses == 1){ $resultado = $meses.' mes'; }else{ $resultado = $meses.' meses'; }
            }

            if($dias >0){
                if($meses > 0){ $resultado .= ' y '; }
                if($dias == 1){ $resultado .= $dias.' día'; }else{ $resultado .= $dias.' días'; }
            }
            //dd($resultado);
            return $resultado;
        }
    }
?>