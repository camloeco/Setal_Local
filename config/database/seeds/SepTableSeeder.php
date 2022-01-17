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
class SepTableSeeder extends Seeder{
    
    // Comando - Ejecutar php artisan db:seed
    public function run(){
        
        
        /*
         * Registros para los diferentes estados para educacion administrativa
         */
        
        $tiposComite = getTipoComite();
        
        foreach($tiposComite as $key=>$tipoComite){
            
            \DB::table('sep_edu_tipo_com')->insert(array(
                'edu_tipo_com_id' =>  ($key+1),
                'edu_tipo_com_descripcion' =>  $tipoComite
            ));
            
        } // foreach
        
        /*
         * Registros para los diferentes estados para educacion administrativa
         */
        
        $estilos = getEstilosAprendizaje();
        
        foreach($estilos as $key=>$estilo){
            
            \DB::table('sep_estilos_aprendizaje')->insert(array(
                'est_apr_id' =>  ($key+1),
                'est_apr_descripcion' =>  $estilo
            ));
            
        } // foreach
        
        /*
         * Registros para los diferentes estados para educacion administrativa
         */
        
        $estadoEdu = getEstadoEdu();
        
        foreach($estadoEdu as $key=>$estado){
            
            \DB::table('sep_edu_estado')->insert(array(
                'edu_est_id' =>  ($key+1),
                'edu_est_descripcion' =>  $estado
            ));
            
        } // foreach
        
        /*
         * Registros para los diferentes tipos de falta para educacion administrativa
         */
        
        $tipoFaltas = getTipoFaltas();
        
        foreach($tipoFaltas as $key=>$falta){
            
            \DB::table('sep_edu_tipo_falta')->insert(array(
                'edu_tipo_falta_id' =>  ($key+1),
                'edu_tipo_falta_descripcion' =>  $falta
            ));
            
        } // foreach
        
        /*
         * Registros para los diferentes estados
         */
        
        $estados = getEstados();
        
        foreach($estados as $key=>$estado){
            \DB::table('sep_estado')->insert(array(
                'est_id' =>  ($key+1),
                'est_descripcion' =>  $estado
            ));
            
        } // foreach
        
        /*
         * Registros para las diferentes fases
         */
        $fases = getFases();
        
        foreach($fases as $key=>$fase){
            \DB::table('sep_fase')->insert(array(
               'fas_id' =>  ($key+1),
               'fas_descripcion' =>  $fase
            ));
        } // foreach 
        
        /*
         * Registros para las diferentes fases
         */
        $opcionEtapa = getOpcionEtapa();
        
        foreach($opcionEtapa as $key=>$opcion){
            \DB::table('sep_opcion_etapa')->insert(array(
               'ope_id' =>  ($key+1),
               'ope_descripcion' => $opcion
            ));
        } // foreach 
        
        \DB::table('sep_centro')->insert(array(
            'cen_codigo' =>  1,
            'cen_nombre' => "CENTRO DE DISENO TECNOLOGICO INDUSTRIAL",
            'cen_sigla' => "CDTI",
            'reg_codigo' => "76"
         ));
        
    } // run
    
}
