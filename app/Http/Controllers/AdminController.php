<?php namespace App\Http\Controllers;

/*
 * @author: David Fernando Barona Castrillon
 * @category: Base
 */

class AdminController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Admin Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
            $this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
            
            
            $rol = \Auth::user()->participante->rol_id;
            $id = \Auth::user()->id;
            if($rol == 1){
                return redirect(url("users/users/editprofile/".$id));
            }
            
            return view('admin');
	}

}
