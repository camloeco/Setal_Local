<?php


/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

Route::get('/', 'AdminController@index');
Route::get('admin', 'AdminController@index');
Route::get('home', 'AdminController@index');

Route::controllers([
    'users/users' => 'Modules\Users\UsersController',
    'users/rol' => 'Modules\Users\RolController',
    'seguimiento/participante' => 'Modules\Seguimiento\ParticipanteController',
    'seguimiento/reportes' => 'Modules\Seguimiento\ReportesController',
    'seguimiento/ficha' => 'Modules\Seguimiento\FichaController',
    'seguimiento/programa' => 'Modules\Seguimiento\ProgramaController',
    'seguimiento/horario' => 'Modules\Seguimiento\HorarioController',
    'seguimiento/actualizar' => 'Modules\Seguimiento\ActualizarController',
    'seguimiento/educativa' => 'Modules\Seguimiento\EducativaController',		
    'seguimiento/practica' => 'Modules\Seguimiento\PracticaController',
    'seguimiento/plantilla' => 'Modules\Seguimiento\PlantillaController',
    'seguimiento/cartaslaborales' => 'Modules\Seguimiento\CartaslaboralesController',
    'seguimiento/ingreso' => 'Modules\Seguimiento\IngresoController',
    'seguimiento/certificacion' => 'Modules\Seguimiento\CertificacionController',
    'seguimiento/proyecto' => 'Modules\Seguimiento\ProyectoController',
    'seguimiento/bienestar' => 'Modules\Seguimiento\BienestarController',
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);


/*Route::get('prueba',function(){
    $rutaXML = base_path() . '/app/Http/Base/capitulos3y4.xml';
    $xmlModulos = simplexml_load_file($rutaXML);
    //dd($xmlModulos);
    foreach ($xmlModulos as $capitulos) {
        $sql = "INSERT INTO sep_edu_capitulo(cap_codigo,cap_descripcion) "
                . "VALUES('" . $capitulos[0]->nombre . "','" . $capitulos[0]->descripcion . "')";
        \DB::insert($sql);
        foreach ($capitulos[0]->articulo as $articulos) {
            $sql = "INSERT INTO sep_edu_articulo(art_codigo,art_descripcion,cap_codigo) "
                    . "VALUES('" . $articulos[0]->nombre . "','" . $articulos[0]->descripcion . "','" . $capitulos[0]->nombre . "')";
            \DB::insert($sql);
            foreach ($articulos[0]->literal as $literales) {
                $sql = "INSERT INTO sep_edu_literal(lit_codigo,lit_descripcion,art_codigo) "
                        . "VALUES('" . $literales[0]->nombre . "','" . $literales[0]->descripcion . "','" . $articulos[0]->nombre . "')";
                \DB::insert($sql);
            }
        }
    }
});*/