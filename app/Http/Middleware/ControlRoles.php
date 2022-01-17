<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use App\Http\Models\Modules\Users\DetalleUsuarioRol;
//use App\Http\Models\Modules\Configuracion\Permisos;
use DB;
use Illuminate\Support\Facades\Auth;

class ControlRoles {

    private $auth;    
        
    public function __construct(Guard $auth) {
        $this->auth = $auth;
    }
    /**
    * Handle an incoming request.
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle($request, Closure $next)
    {   
        /*
         * Se identifica la funcion
         */
        $action = app('request')->route()->getAction();
        
        /*
         * Se identifica el modulo
         */
        $module = explode("\\", $action['controller']);
        $module = strtolower($module[count($module)-2]);
        
        /*
         * Se identifica el controlador
         */
        $controller = class_basename($action['controller']);
        list($controller, $action) = explode('@', $controller);
    
        list($action, $nameAction) = explode('_', snake_case($action));
        
        /*
         * Se indentifica el id del usuario
         */
        $idUser = Auth::user()->par_identificacion;
        
        /*
         * Se indentifican los roles del usuario
         */
        $objDetalleUsuarioRol = new DetalleUsuarioRol();
        
        $rolUser = $objDetalleUsuarioRol->select('id_rol')
                ->where('id_usuario','=', $idUser)
                ->get()
                ->lists('id_rol','id_rol');
        
        $roles = implode(" or permisos.id_rol = ", $rolUser);
        
        if($roles !=""){
            $permiso =DB::select(
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
                and funcion.nombre_funcion = ?
                and funcion.id_controlador = control.id_controlador
                and control.nombre_controlador = ?
                and control.id_modulo = modulo.id_modulo
                and modulo.nombre_modulo = ?",
                array($nameAction,$controller,$module));
        }else{
            $permiso = array();
        }
        
        
        
        if($permiso){
            return $next($request);
        }else{
            $funcion = DB::select(
            "SELECT 
                modulo.id_modulo, 
                control.id_controlador, 
                funcion.id_funcion  
            FROM 
                sep_funciones as funcion, 
                sep_controladores as control, 
                sep_modulos as modulo
            WHERE 
                funcion.nombre_funcion = ?
                and funcion.id_controlador = control.id_controlador
                and control.nombre_controlador = ?
                and control.id_modulo = modulo.id_modulo
                and modulo.nombre_modulo = ?",
                array($nameAction,$controller,$module));
            
            if($funcion){
                return view("errors.permiso");
            }else{
                return $next($request);
            }
            
        }
    
    }//hannddle

 }         