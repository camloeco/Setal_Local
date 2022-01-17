<?php

namespace App\Http\Controllers\Modules\Users;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Base\Lib\MenuClass;
// Modelos del modulo de roles
use App\Http\Models\Modules\Users\Rol;
use App\Http\Models\Modules\Users\Modulo;
use App\Http\Models\Modules\Users\Controladores;
use App\Http\Models\Modules\Users\Funcion;
use DB;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use \Illuminate\Pagination\LengthAwarePaginator;

class RolController extends Controller {

    /**
     * Display a listing of the resource.
     * autor: agarcia@vennexgroup.com
     * @return Response
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('control_roles');
        //$this->middleware('control_roles');            

    }

    /**
     * Display the specified resource.
     * autor: agarcia@vennexgroup.com
     * @param  int  $id
     * @return Response
     * */

    /**
     * autor: agarcia@vennexgroup.com
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex() {

        $page = Input::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        $roles = DB::select("SELECT * FROM sep_roles WHERE id_rol > 0");

        $roles = new LengthAwarePaginator(
                array_slice(
                        $roles, $offset, $perPage, true
                ), count($roles), $perPage, $page);

        $roles->setPath("index");

        return view("Modules.Users.Rol.index", compact("roles"));

    }

//getIndex

    /**
     * autor: agarcia@vennexgroup.com
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function getCreate() {
        $modulo = Modulo::all();
        $controlador = Controladores::all();
        $funcion = Funcion::all();
        return view("Modules.Users.Rol.create", compact('modulo', 'controlador', 'funcion'));

    }

//getCreate

    /**
     * autor: agarcia@vennexgroup.com
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postCreate() {
        
        $datos = \Request::all();
        $reglas = array(
            'nombre_rol' => 'required ',
        );
        
        $messages = array(
            'nombre_rol.required'=>"El campo nombre del rol es obligatorio"
        );

        $validaciones = Validator::make($datos, $reglas, $messages);

        if ($validaciones->fails()) {
            return redirect()->back()
                            ->withErrors($validaciones->errors());
        }
        
        $id = DB::select("SELECT MAX(id_rol) as id FROM sep_roles");
        $id = ($id[0]->id)+1;
        
        $objRol = new Rol();
        $objRol->nombre_rol = $datos['nombre_rol'];
        $objRol->id_rol = $id;
        $objRol->save();

        return \Redirect::to('users/rol/permisos/' . $id);

    }

//postCreate

    /**
     * autor: agarcia@vennexgroup.com
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getEdit($id) {
        $rol = Rol::find($id);
        return view('Modules.Usuarios.Rol.edit', compact('rol'));

    }

//getEdit

    /**
     * autor: agarcia@vennexgroup.com
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function postEdit() {

        $id = Request::input('id_rol');
        DB::delete("DELETE FROM sep_permisos WHERE id_rol= $id");

        $funciones = Request::input('funcion');
        foreach ($funciones as $funcion) {
            DB::insert("INSERT INTO sep_permisos (id_rol,id_funcion) VALUES($id,$funcion)");
        }

        return \Redirect::to('users/rol/index');

    }

//postEdit

    /**
     * autor: agarcia@vennexgroup.com
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function getDeleted($id) {
        $rol = Rol::find($id);
        return view('Modules.Users.Rol.deleted', compact('rol'));

    }

//getDeleted
    /**
     * autor: agarcia@vennexgroup.com
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function postDeleted() {
        $post = \Request::all();
        
        DB::select("DELETE FROM sep_roles WHERE id_rol = ".$post['id']);
        DB::select("DELETE FROM sep_detalle_usuario_rol WHERE id_rol = ".$post['id']);
        DB::select("DELETE FROM sep_permisos WHERE id_rol = ".$post['id']);
        
        return \Redirect::to('users/rol/index');

    }

    public function getPermisos($id) {

        // rol
        $rol = Rol::find($id);

        $objPermisos = new MenuClass();

        $permisos = $objPermisos->getPermisos($id, 'funciones');

        //modulos
        $modulos = Modulo::all();

        //controladores
        $controladores = Controladores::all();

        //funcion
        $funciones = Funcion::all();


        $estructura = array();

        foreach ($funciones as $funcion) {

            foreach ($controladores as $controlador) {
                if ($controlador->id_controlador != $funcion->id_controlador)
                    continue;

                foreach ($modulos as $modulo) {
                    if ($controlador->id_modulo != $modulo->id_modulo)
                        continue;

                    $estructura[$modulo->id_modulo]['nombre'] = $modulo->display_modulo;
                    $estructura[$modulo->id_modulo]['controladores'][$funcion->id_controlador]['nombre'] = $controlador->display_controlador;
                    $estructura[$modulo->id_modulo]['controladores'][$funcion->id_controlador]['funciones'][$funcion->id_funcion] = $funcion->display_funcion;
                }
            }
        }
        //dd($estructura);
        return view("Modules.Users.Rol.permisos", compact(
                        'rol', 'permisos', 'estructura'
                )
        );

    }

//getEdit

}
