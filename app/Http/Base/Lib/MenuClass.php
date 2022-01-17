<?php

namespace App\Http\Base\Lib;

use App\Http\Models\Modules\Users\DetalleUsuarioRol;
use DB;
use \Illuminate\Support\Facades\Auth;

class MenuClass {

    function __construct() {

        /*
         * Se indentifica el id del usuario
         */
        //dd(Auth::user());
        $idUser = Auth::user()->par_identificacion;

        /*
         * Se indentifican los roles del usuario
         */
        $objDetalleUsuarioRol = new DetalleUsuarioRol();

        $rolUser = $objDetalleUsuarioRol->select('id_rol')
                ->where('id_usuario', '=', $idUser)
                ->get()
                ->lists('id_rol', 'id_rol');
        
        $roles = implode(" or permisos.id_rol = ", $rolUser);
        if($roles !=""){
            $permisos = $this->getPermisos($roles);
        }else{
            $permisos = array();
        }
        
        $this->menuFinal = $this->getMenu($permisos); 
    }

    function getPermisos($roles=false, $tipo='all') {

        $permisos = DB::select(
                        "SELECT 
                modulo.id_modulo, 
                control.id_controlador, 
                funcion.id_funcion  
            FROM 
                sep_permisos as permisos, 
                sep_funciones as funcion, 
                sep_controladores as control, 
                sep_modulos as modulo
            WHERE 
                (permisos.id_rol = $roles)
                and permisos.id_funcion = funcion.id_funcion
                and funcion.id_controlador = control.id_controlador
                and control.id_modulo = modulo.id_modulo"
        );

        $menus = array();

        if ($permisos) {
            
            if($tipo=='all'){
                foreach ($permisos as $permiso) {
                    $menus[$permiso->id_modulo][$permiso->id_controlador][] = $permiso->id_funcion;
                }
            }elseif($tipo=='modulos'){
                foreach ($permisos as $permiso) {
                    $menus[$permiso->id_modulo] = $permiso->id_modulo;
                }
            }elseif($tipo=='controladores'){
                foreach ($permisos as $permiso) {
                    $menus[$permiso->id_controlador] = $permiso->id_controlador;
                }
            }elseif($tipo=='funciones'){
                foreach ($permisos as $permiso) {
                    $menus[$permiso->id_funcion] = $permiso->id_funcion;
                }
            }
            
        }

        return $menus;

    }

    function getMenu($menus = array()) {

        $funcionesMenu = array("edit", 
            "deleted", 
            "show",
            "aprobarqueja",
            "rechazarqueja",
            "planeacion",
            "permisos",
            "editprofile",
            "showprofile"
            );
        
        $menuFinal = array();

        foreach ($menus as $mod => $menu) {
            $modulo = DB::select(
                            "SELECT modulo.nombre_modulo, modulo.display_modulo FROM sep_modulos as modulo 
                    WHERE modulo.id_modulo = ?", array($mod)
            );
            $moduloDisplay = $modulo[0]->display_modulo;
            $moduloNombre = $modulo[0]->nombre_modulo;

            foreach ($menu as $control => $funciones) {

                $controlador = DB::select(
                                "SELECT control.descripcion_controlador, control.display_controlador, control.nombre_controlador FROM sep_controladores as control
                        WHERE control.id_controlador = ?", array($control)
                );

                $controladorDesc = $controlador[0]->display_controlador;
                $controladorNombre = $controlador[0]->nombre_controlador;

                foreach ($funciones as $funcion) {
                    $funcionFinal = DB::select(
                                    "SELECT funcion.descripcion_funcion, funcion.display_funcion, funcion.nombre_funcion FROM sep_funciones as funcion
                        WHERE funcion.id_funcion = ?", array($funcion)
                    );

                    if (!in_array($funcionFinal[0]->nombre_funcion, $funcionesMenu)) {
                        $menuFinal[$moduloDisplay][$controladorDesc][$funcionFinal[0]->display_funcion] = strtolower($moduloNombre)
                                . "/" . strtolower(str_replace("Controller", "", $controladorNombre))
                                . "/" . $funcionFinal[0]->nombre_funcion . "/";
                    }
                }
            }
        }

        return $menuFinal;

    }

}
