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
class RolTableSeeder extends Seeder {

    // Comando - Ejecutar php artisan db:seed
    public function run() {
        \DB::table('sep_roles')->insertGetId(array(
            'id_rol' => "0",
            'nombre_rol' => "Administrador"
        ));
        \DB::table('sep_roles')->insertGetId(array(
            'id_rol' => "1",
            'nombre_rol' => "Aprendiz"
        ));
        \DB::table('sep_roles')->insertGetId(array(
            'id_rol' => "2",
            'nombre_rol' => "Instructor",
        ));
        \DB::table('sep_roles')->insertGetId(array(
            'id_rol' => "3",
            'nombre_rol' => "Coordinador",
        ));
        \DB::table('sep_roles')->insertGetId(array(
            'id_rol' => "4",
            'nombre_rol' => "Administrativo",
        ));

    }

}

// run
    

