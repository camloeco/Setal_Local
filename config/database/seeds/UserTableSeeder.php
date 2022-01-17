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
class UserTableSeeder extends Seeder {

    // Comando - Ejecutar php artisan db:seed
    public function run() {

        $faker = Faker::create();

        for ($i = 0; $i <= 60; $i++) {

            $genderOption = array('male', 'female');
            $gender = $genderOption[rand(0, 1)];
            $email = $faker->unique()->email;
            $identificacion = $faker->phoneNumber;
            
            $id = \DB::table('users')->insertGetId(array(
                'par_identificacion' => $identificacion,
                'email' => $email,
                'password' => \Hash::make('123456'),
                'gender' => $gender,
                'estado' => 1
            ));
            
            \DB::table('user_profiles')->insert(array(
                'user_id' => $id,
                'birthdate' => $faker->dateTimeBetween('-45 years', '-15 years')->format('Y-m-d'),
                'observations' => $faker->text()
            ));
            
            \DB::table('sep_participante')->insert(array(
                'par_identificacion' => $identificacion,
                'par_nombres' => $faker->firstName($gender),
                'par_apellidos' => $faker->lastName,
                'par_direccion' => 'CRA 34 56 78',
                'par_telefono' => $faker->phoneNumber,
                'par_correo' => $email,
                'rol_id' => 0
            ));
            
        }

    }

// run

}
