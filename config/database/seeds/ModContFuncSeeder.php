<?php

use Illuminate\Database\Seeder;

class ModContFuncSeeder extends Seeder {

    public function run() {
        
        //Verificar la ruta del proyecto antes de ejecutar este seeder
        $rutaModulos = base_path().'/app/Http/Controllers/Modules';
        
        $modulos = $this->getFiles($rutaModulos);
        if($modulos==0){
            die("Error! Verifique las rutas de los modulos");
        }

        $xmlModulos = array();
        foreach ($modulos as $modulo) {
            //Obtengo los archivos de cada modulo
            $modFiles = $this->getFiles($rutaModulos . "/" . $modulo);
            foreach ($modFiles as $file) {
                //El módulo actual tiene el archivo info.xml?
                if ($file == 'info.xml') {
                    $rutaXML = $rutaModulos . "/" . $modulo . "/" . $file;
                    $xmlModulos[] = simplexml_load_file($rutaXML);
                }
            }//foreach
        }//foreach

        $idController = 1;
        $idFunction = 1;

        foreach ($xmlModulos as $modId => $modulo) {
            //Inserción de módulos
            \DB::table('sep_modulos')->insert(array(
                'id_modulo' => ($modId + 1),
                'nombre_modulo' => $modulo->nombre,
                'display_modulo' => $modulo->display,
                'descripcion_modulo' => $modulo->descripcion,
                'estado_modulo' => "1",
                'nombre_carpeta_modulo' => "/" . $modulo->carpeta,
            ));

            foreach ($modulo->controladores->controlador as $controlador) {
                //Insercion de controladores
                \DB::table('sep_controladores')->insert(array(
                    'id_controlador' => $idController,
                    'display_controlador' => $controlador->display,
                    'nombre_controlador' => $controlador->nombre,
                    'descripcion_controlador' => $controlador->descripcion,
                    'id_modulo' => ($modId + 1),
                ));

                foreach ($controlador->funciones->funcion as $funcion) {
                    //Insercion de funciones
                    \DB::table('sep_funciones')->insert(array(
                        'id_funcion' => $idFunction,
                        'display_funcion' => $funcion->display,
                        'nombre_funcion' => $funcion->nombre,
                        'descripcion_funcion' => $funcion->descripcion,
                        'id_controlador' => $idController,
                    ));
                    \DB::table('sep_permisos')->insert(array(
                        'id_funcion' => $idFunction,
                        'id_rol' => 0,
                        'id_funcion' => $idFunction,
                    ));
                    $idFunction++;
                }
                $idController++;
            }
        }//foreach

    }//Run

    public function getFiles($ruta) {
        if (is_dir($ruta)) {
            $directorios = array();
            if ($dh = opendir($ruta)) {
                //Mientras hayan archivos por leer
                while ($file = readdir($dh)) {
                    //solo si el archivo es un directorio, distinto que "." y ".."
                    if ($file != "." && $file != "..") {
                        $directorios[] = $file;
                    }//if
                }//while
            }//if   
            closedir($dh);
            return $directorios;
        }
        //Si falla el proceso, retorna este valor
        return 0;
    }

}
