<?php

namespace App\Http\Controllers\Modules\Users;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use \Illuminate\Pagination\LengthAwarePaginator;

// Modelos del modulo usuarios
use App\Http\Models\Modules\Users\User;

class UsersController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('control_roles');
    }
    
    public function getAsignarcoordinador(){
		$coordinadores = DB::select('select par_identificacion, par_nombres, par_apellidos from sep_participante where rol_id = 3 and not par_identificacion = "16759526" order by par_nombres');
		$instructores = DB::select('
			select p.par_identificacion, par_nombres, par_apellidos from sep_participante p, users u
			where rol_id = 2
			and u.par_identificacion = p.par_identificacion and estado = "1"
			order by par_nombres');
		return view('Modules.Users.Users.asignarCoordinador', compact('coordinadores','instructores'));
	}
	
	public function postAsignarcoordinador(){
		extract($_POST);
		foreach($par_identificacion_instructor as $key => $instructor){
			$sql = 'select par_identificacion_instructor from sep_instructor_coordinador where par_identificacion_instructor = "'.$instructor.'" limit 1';
			$validarInstructor = DB::select($sql);
			if(count($validarInstructor)>0){
				$sql = 'update sep_instructor_coordinador set par_identificacion_coordinador = "'.$par_identificacion_coordinador.'" where par_identificacion_instructor = "'.$instructor.'"';
				DB::update($sql);
			}else{
				$sql = 'insert into sep_instructor_coordinador values (default, "'.$par_identificacion_coordinador.'" , "'.$instructor.'", 1)';
				DB::insert($sql);
			}
		}
		echo '<h4 style="color:#087b76;text-align:center;">El registro o la actualización se realizó exitosamente.</h4>';
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getShow($id) {
        $user = User::find($id);
        return view("Modules.Users.Users.show", compact("user"));

    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getShowprofile($id) {
        if(\Auth::user()->id != $id){
            return view("errors.permiso");
        }
        
        $user = User::find($id);
        return view("Modules.Users.Users.show", compact("user"));

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex($id= false, $campo=false) {
        $page = Input::get('page', 1);
        $perPage = 100;
        $offset = ($page * $perPage) - $perPage;
        
        if($id=="")
                {
                    $sql="SELECT * FROM users, sep_participante "
                . "WHERE users.par_identificacion = sep_participante.par_identificacion ORDER BY sep_participante.par_nombres ASC";
                }
            else{
                $sql="SELECT * FROM users, sep_participante "
                . "WHERE users.par_identificacion = sep_participante.par_identificacion AND $campo LIKE '%$id%' 
                   AND NOT sep_participante.par_identificacion=0900700
                   ORDER BY sep_participante.par_nombres ASC";
            }
            //dd($sql);
            $users = DB::select($sql);
       
        
        $users = new LengthAwarePaginator(
                array_slice(
                        $users, $offset, $perPage, true
                ), count($users), $perPage, $page);
        
        $users->setPath("index");
        
        return view("Modules.Users.Users.index", compact("users","offset"));

    }
    
            public function postIndex(){
            return $this->getIndex($_POST['cedula'],$_POST['campo']);
        }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function getCreate() {

        $rolesAll = DB::select("SELECT * FROM sep_roles");

        $roles = array();
        foreach ($rolesAll as $rol) {
            $roles[$rol->id_rol] = $rol->nombre_rol;
        }

        return view("Modules.Users.Users.create", compact("roles"));

    }

    public function postCreate(Request $request) {
        
        // Reglas para validar los direfentes campos
        $reglas = Array(
            'par_identificacion' => 'required | min:7 | unique:users,par_identificacion',
            "par_nombres" => "required",
            "par_apellidos" => "required",
            "par_correo" => "required | unique:users,email",
            "par_telefono" => "required",
            "par_direccion" => "required",
            "gender" => "required",
            "password" => "required | min:6",
            "birthdate" => "required",
            "roles" => "required",
            "rol" => "required",
            "horas_contrato" => "required"
        );
        
        // Mensajes de error para los diferentes campos
        $messages = [
            'par_identificacion.required' => 'El campo n&uacute;mero de identificaci&oacute;n es obligatorio',
            'par_identificacion.min' => 'El campo n&uacute;mero de identificaci&oacute;n debe contener minimo 7 caracteres',
            'par_identificacion.unique' => 'El campo n&uacute;mero de identificaci&oacute;n ya existe en la base de datos',
            "par_nombres.required" => "El campo nombres es obligatorio",
            "par_apellidos.required" => "El campo apellidos es obligatorio",
            "par_correo.required" => "El campo email es obligatorio",
            "par_correo.unique" => "El campo email ya existe en la base de datos",
            "par_telefono.required" => "El campo telefono es obligatorio",
            "par_direccion.required" => "El campo direcci&oacute;n es obligatorio",
            "gender.required" => "El campo genero es obligatorio",
            "password.required" => "El campo contrase&ntilde;a es obligatorio",
            "password.min" => "El campo contrase&ntilde;a debe contener minimo 6 caracteres",
            "birthdate.required" => "El campo fecha de nacimiento es obligatorio",
            "roles.required" => "Debe seleccionar al menos un rol para el usuario",
            "rol.required" => "Debe seleccionar el rol principal del usuario",
            "horas_contrato.required" => "Debe diligenciar la horas horas del contrato"
        ];
        
        // Se ejecutan las reglas para la información recibida por POST
        $validacion = Validator::make($_POST, $reglas, $messages);
        
        
        /*
         * Se verifica si existen errores, en tal caso se redirecciona 
         * a la vista de donde se recibio el POST y muestra el respectivo
         * mensaje de error
         */
                
        if($validacion->fails()){
            // Se crea sesion para mantener los datos del formulario
            //Session::flash('form_barrio',$_POST);
        
            return redirect()->back()
                    ->withErrors($validacion->errors())->withInput();
        }
        
        $id = DB::table('users')->insertGetId(array(
            'par_identificacion' =>  $request->input('par_identificacion'),
            'email' =>  $request->input('par_correo'),
            'password' =>  \Hash::make($request->input('password')),
            'gender' =>  $request->input('gender'),
            'estado' =>  1
        ));
        
        \DB::table('user_profiles')->insert(array(
            'user_id' => $id,
            'birthdate' => $request->input('birthdate'),
            'observations'=> $request->input('observations')
        
        ));
        
        \DB::table('sep_participante')->insert(array(
            'par_identificacion' => $request->input('par_identificacion'),
            'par_nombres' => ucwords(mb_strtolower($request->input('par_nombres'))),
            'par_apellidos'=> ucwords(mb_strtolower($request->input('par_apellidos'))),
            'par_direccion' => ucwords(mb_strtolower($request->input('par_direccion'))),
            'par_telefono' => $request->input('par_telefono'),
            'par_correo' => $request->input('par_correo'),
            'rol_id' => $request->input('rol'),
            'par_horas_semanales' => $request->input('horas_contrato')
        ));
        
        foreach($request->input('roles') as $rol){
            \DB::table('sep_detalle_usuario_rol')->insert(array(
                    'id_rol' => $rol,
                    'id_usuario' => $request->input('par_identificacion')
            ));
        }
        
        return Redirect::to(url('users/users/index'));
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        //

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getEditprofile($id) {
        if(\Auth::user()->id != $id){
            return view("errors.permiso");
        }
        
        /*
         * Para la creación
         */
        $rolesAll = DB::select("SELECT * FROM sep_roles");

        $roles = array();
        foreach ($rolesAll as $rol) {
            $roles[$rol->id_rol] = $rol->nombre_rol;
        }

        /*
         * Datos propios del usuario a editar
         */
        $user = User::find($id);
        
        $rolesSelect = DB::select("SELECT * FROM sep_detalle_usuario_rol where id_usuario = ?",array($user->par_identificacion));
        $rolesSel = array();
        
        foreach($rolesSelect as $rol){
            $rolesSel[] = $rol->id_rol;
        }
        
        $estilosp = DB::select("SELECT * FROM sep_estilos_aprendizaje");
        
        $estilos = array();
        foreach($estilosp as $estilo){
            $estilos[$estilo->est_apr_id] = $estilo->est_apr_descripcion;
        }
        
        return view("Modules.Users.Users.editprofile", compact("roles","user", "rolesSel", "estilos"));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getEdit($id) {
        /*
         * Para la creación
         */
        $rolesAll = DB::select("SELECT * FROM sep_roles");

        $roles = array();
        foreach ($rolesAll as $rol) {
            $roles[$rol->id_rol] = $rol->nombre_rol;
        }

        /*
         * Datos propios del usuario a editar
         */
        $user = User::find($id);
        
        $horas = DB::select('select par_horas_semanales from sep_participante where par_identificacion = '.$user->par_identificacion);
        $horas = $horas[0]->par_horas_semanales;
        $rolesSelect = DB::select("SELECT * FROM sep_detalle_usuario_rol where id_usuario = ?",array($user->par_identificacion));
        $rolesSel = array();
        
        foreach($rolesSelect as $rol){
            $rolesSel[] = $rol->id_rol;
        }
        $rolLogin = \Auth::user()->participante->rol_id;
        //dd($rol);
        $estilosp = DB::select("SELECT * FROM sep_estilos_aprendizaje");
        
        $estilos = array();
        foreach($estilosp as $estilo){
            $estilos[$estilo->est_apr_id] = $estilo->est_apr_descripcion;
        }
        
        return view("Modules.Users.Users.edit", compact("rolLogin","horas","roles","user", "rolesSel", "estilos"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function getDeleted($id) {
        $user = User::find($id);
        
        return view("Modules.Users.Users.deleted", compact("user"));
    }
    
    public function postDeleted(Request $request){
        $rol = \Auth::user()->participante->rol_id;
        if($rol == 0 or $rol == 3 or $rol == 5 or $rol == 8){
            $estado = DB::select("SELECT estado FROM users WHERE id = ".$request->input('id'));
            
            if($estado[0]->estado==0){
                $estado = 1;
            }else{
                $estado = 0;
            } 
            
            DB::table('users')
                ->where("id", $request->input('id'))
                ->update(array(
                'estado' => $estado 
            ));
        }
        
        return Redirect::to(url('users/users/index'));
    }
    
    public function postEdit(Request $request) {
        
        // Reglas para validar los direfentes campos
        $reglas = Array(
            'par_identificacion' => 'required | min:6',
            "par_nombres" => "required",
            "par_apellidos" => "required",
            "par_correo" => "required",
            "par_telefono" => "required",
            "par_direccion" => "required",
            "gender" => "required",
            "birthdate" => "required",
            "roles" => "required",
            "rol" => "required",
            "password" => "min:6"
        );
        
        // Mensajes de error para los diferentes campos
        $messages = [
            'par_identificacion.required' => 'El campo n&uacute;mero de identificaci&oacute;n es obligatorio',
            'par_identificacion.min' => 'El campo n&uacute;mero de identificaci&oacute;n debe contener minimo 7 caracteres',
            "par_nombres.required" => "El campo nombres es obligatorio",
            "par_apellidos.required" => "El campo apellidos es obligatorio",
            "par_correo.required" => "El campo email es obligatorio",
            "par_telefono.required" => "El campo telefono es obligatorio",
            "par_direccion.required" => "El campo direcci&oacute;n es obligatorio",
            "gender.required" => "El campo genero es obligatorio",
            "birthdate.required" => "El campo fecha de nacimiento es obligatorio",
            "roles.required" => "Debe seleccionar al menos un rol para el usuario",
            "rol.required" => "Debe seleccionar el rol principal del usuario",
            "rol.min" => "El campo contrase&ntilde;a debe contener minimo 6 caracteres"
        ];
        
        // Se ejecutan las reglas para la información recibida por POST
        $validacion = Validator::make($_POST, $reglas, $messages);
        
        
        /*
         * Se verifica si existen errores, en tal caso se redirecciona 
         * a la vista de donde se recibio el POST y muestra el respectivo
         * mensaje de error
         */
                
        if($validacion->fails()){
            // Se crea sesion para mantener los datos del formulario
            //Session::flash('form_barrio',$_POST);
        
            return redirect()->back()
                    ->withErrors($validacion->errors())->withInput();
        }
        
        DB::table('users')
            ->where("id", $request->input('id'))
            ->update(array(
            'email' =>  $request->input('par_correo'),
            'gender' =>  $request->input('gender')
        ));
        
        if($request->input('password') && $request->input('password') !=''){
            DB::table('users')
                ->where("id", $request->input('id'))
                ->update(array(
                'password' =>  \Hash::make($request->input('password'))
            ));
        }
        
        \DB::table('user_profiles')
            ->where("id", $request->input('id'))
            ->update(array(
            'birthdate' => $request->input('birthdate'),
            'observations'=> $request->input('observations')
        
        ));
        
        if($request->input('rol') == 2){
            \DB::table('sep_participante')
                ->where("par_identificacion", $request->input('par_identificacion'))
                ->update(array(
                'par_nombres' => $request->input('par_nombres'),
                'par_apellidos'=> $request->input('par_apellidos'),
                'par_direccion' => $request->input('par_direccion'),
                'par_telefono' => $request->input('par_telefono'),
                'par_correo' => $request->input('par_correo'),
                'est_apr_id' => $request->input('est_apr_id'),
                'rol_id' => $request->input('rol'),
                'par_horas_semanales' => $request->input('horas_contrato')
            ));
        }else{
            \DB::table('sep_participante')
                ->where("par_identificacion", $request->input('par_identificacion'))
                ->update(array(
                'par_nombres' => $request->input('par_nombres'),
                'par_apellidos'=> $request->input('par_apellidos'),
                'par_direccion' => $request->input('par_direccion'),
                'par_telefono' => $request->input('par_telefono'),
                'par_correo' => $request->input('par_correo'),
                'est_apr_id' => $request->input('est_apr_id'),
                'rol_id' => $request->input('rol')
            ));
        }
        
        DB::delete("DELETE FROM sep_detalle_usuario_rol WHERE id_usuario = ".$request->input('par_identificacion'));
        foreach($request->input('roles') as $rol){
            \DB::table('sep_detalle_usuario_rol')
                    ->insert(array(
                    'id_rol' => $rol,
                    'id_usuario' => $request->input('par_identificacion')
            ));
        }
        
        return Redirect::to(url('users/users/edit/'.$request->input('id')));
        
    }
    
    public function postEditprofile(Request $request) {
        
        // Reglas para validar los direfentes campos
        $reglas = Array(
            'par_identificacion' => 'required | min:6',
            "par_nombres" => "required",
            "par_apellidos" => "required",
            "par_correo" => "required",
            "par_telefono" => "required",
            "par_direccion" => "required",
            "gender" => "required",
            "birthdate" => "required",
        );
        
        // Mensajes de error para los diferentes campos
        $messages = [
            'par_identificacion.required' => 'El campo n&uacute;mero de identificaci&oacute;n es obligatorio',
            'par_identificacion.min' => 'El campo n&uacute;mero de identificaci&oacute;n debe contener minimo 7 caracteres',
            "par_nombres.required" => "El campo nombres es obligatorio",
            "par_apellidos.required" => "El campo apellidos es obligatorio",
            "par_correo.required" => "El campo email es obligatorio",
            "par_telefono.required" => "El campo telefono es obligatorio",
            "par_direccion.required" => "El campo direcci&oacute;n es obligatorio",
            "gender.required" => "El campo genero es obligatorio",
            "birthdate.required" => "El campo fecha de nacimiento es obligatorio"
        ];
        
        // Se ejecutan las reglas para la información recibida por POST
        $validacion = Validator::make($_POST, $reglas, $messages);
        
        
        /*
         * Se verifica si existen errores, en tal caso se redirecciona 
         * a la vista de donde se recibio el POST y muestra el respectivo
         * mensaje de error
         */
                
        if($validacion->fails()){
            // Se crea sesion para mantener los datos del formulario
            //Session::flash('form_barrio',$_POST);
        
            return redirect()->back()
                    ->withErrors($validacion->errors())->withInput();
        }
        
        DB::table('users')
            ->where("id", $request->input('id'))
            ->update(array(
            'email' =>  $request->input('par_correo'),
            'gender' =>  $request->input('gender')
        ));
        
        if($request->input('password') && $request->input('password') !=''){
            DB::table('users')
                ->where("id", $request->input('id'))
                ->update(array(
                'password' =>  \Hash::make($request->input('password'))
            ));
        }
        
        \DB::table('user_profiles')
            ->where("id", $request->input('id'))
            ->update(array(
            'birthdate' => $request->input('birthdate'),
            'observations'=> $request->input('observations')
        
        ));
        
        \DB::table('sep_participante')
            ->where("par_identificacion", $request->input('par_identificacion'))
            ->update(array(
            'par_nombres' => ucwords(mb_strtolower($request->input('par_nombres'))),
            'par_apellidos'=> ucwords(mb_strtolower($request->input('par_apellidos'))),
            'par_direccion' => ucwords(mb_strtolower($request->input('par_direccion'))),
            'par_telefono' => $request->input('par_telefono'),
            'par_correo' => $request->input('par_correo'),
            'est_apr_id' => $request->input('est_apr_id')
        ));
        
        return Redirect::to(url('users/users/editprofile/'.$request->input('id')));
        
    }
    
}
