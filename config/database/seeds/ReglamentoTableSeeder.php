<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Comando - Ejecutar composer dump-autoload
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


/**
 * Description of UserTableSeeder
 *
 * @author dbarona
 */
class ReglamentoTableSeeder extends Seeder {

    // Comando - Ejecutar php artisan db:seed
    public function run() {
            
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
    }

// run

}
