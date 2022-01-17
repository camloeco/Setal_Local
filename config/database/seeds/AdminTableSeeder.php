<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Comando - Ejecutar composer dump-autoload
use Illuminate\Database\Seeder;
/**
 * Description of UserTableSeeder
 *
 * @author dbarona
 */
class AdminTableSeeder extends Seeder{
    
    // Comando - Ejecutar php artisan db:seed
    public function run(){
        
        /*
         * Usuario Administrador e instructor 
         */
        \DB::table('users')->insert(array(
            'par_identificacion' =>  1111111111,
            'email' =>  'admin@cdtiapps.com',
            'password' =>  \Hash::make('123456'),
            'gender' =>  'male',
            'estado' =>  '1'
        ));
        
        \DB::table('sep_participante')->insert(array(
            'par_identificacion' => 1111111111,
            'par_nombres' => 'SUPER',
            'par_apellidos'=> 'ADMINISTRADOR',
            'par_direccion' => 'CRA 34 56 78',
            'par_telefono' => '3185555555',
            'par_correo' => 'admin@cdtiapps.com',
            'rol_id' => 0
        ));
        
        \DB::table('user_profiles')->insert(array(
            'user_id' => 1,
            'birthdate' => '1989/05/14',
            'observations'=> 'Usuario Super Administrador'
        
        ));
        
        \DB::table('sep_detalle_usuario_rol')->insert(array(
                'id_rol' => '0',
                'id_usuario' => "1111111111"
        ));
        
    } // run
    
}
