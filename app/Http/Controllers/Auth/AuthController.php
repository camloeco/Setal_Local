<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use DB;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => 'getLogout']);
	}
	
	/*public function getRecuperar()
	{
		return view('auth.recuperar');
	}*/
	public function postDatosrecuperar()
	{
		extract($_POST);	
		
		$sql="select concat(par_nombres,' ',par_apellidos) as nombre, email
			from sep_participante as p, users as u
			where 
			p.par_identificacion=u.par_identificacion
			and u.par_identificacion='$par_identificacion'";
			$usuario=DB::select($sql);
		
		do{
			$codigo=time();/*Se crea codigo*/
			$randon=rand(0,9);		
			$Ncodigo=$par_identificacion.'-'.substr($codigo,-4).$randon;
			
			$select=DB::select("select * from recuperar where rec_codigo='$Ncodigo'");
		}while(count($select)>0);
		
			$insertar=DB::insert("insert into recuperar (rec_codigo,par_identificacion)
				values ('$Ncodigo','$par_identificacion')");
				
				
		$asunto = "SETALPRO Restablecimiento de contraseña ";
		$mensaje ="Señor(a) " . $usuario[0]->nombre . " " 
                . "<br><br> A continuación enviamos de manera confidencial el codigo de recuperación para su cuenta."		
                . "<br><br> $Ncodigo"
				."<br><br>"
                . "Muchas gracias.<br><br>"
                . "Equipo de Desarrollo SETALPRO<br>"
                . "Servicio Nacional de Aprendizaje Sena";

        $mail = new \PHPMailer();
        $mensaje=utf8_decode($mensaje);
        
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host = 'exodo.colombiahosting.com.co';
        $mail->Port = 587;

        $mail->Username = 'seguimiento@cdtiapps.com';
        $mail->Password = 'seguimientoEducativa07';

        $mail->From = 'seguimiento@cdtiapps.com';
        $mail->FromName = utf8_decode($asunto);

//        $mail->AltBody = "gmail.com";

        $mail->Subject = utf8_decode($asunto);
        $mail->isHTML(true);
        
        $mail->AddAddress("seguimientoscdtisena@gmail.com");
		
        $mail->Body = ($mensaje);
		$mail->AddAddress($usuario[0]->email);
        if ($mail->Send()) {
            //echo "Enviado ... ";
        }
		
		/*visualizar correo con caracteres especiales*/	
			$email=strlen($usuario[0]->email);/*Se cuenta todos los caracteres del correo */
			$arroa=strpos($usuario[0]->email,"@");/*Se cuentan los caracteres antes del @*/
			$dominio=substr($usuario[0]->email,$arroa);/*Se extraen los caracteres despues de la @*/
			$nomEmail=substr($usuario[0]->email,0,$arroa);/*Se extraen los caracteres antes de la @*/
			$nomEmail2=substr($usuario[0]->email,0,2);/*Se extraen los dos primeros caracteres */
			$nomEmail3=substr($usuario[0]->email,($arroa-2),2 );/*Se extraen los dos ultimos caracteres*/
			/*Se realiza el ciclo que reeemplaza los carateres por * desde los dos primeros caracteres hasta
			antes de los dos ultimos caracteres antes de la @*/
			$resul=$nomEmail2;
			for ($i=0;$i<($arroa-3) ;$i++)				
			{
				$resul.="*";
			}
			$resul;
			$ultEmail=$resul.$nomEmail3.$dominio;
		return view('auth.datosrecuperar',compact('usuario','ultEmail','par_identificacion'));
		
	}
	public function postRecuperarcontra()
	{
		extract($_POST);
		
		$sql="
		select TIMESTAMPDIFF(hour,rec_fecha,now()) as fecha 
		from recuperar
		where rec_codigo='$Ncodigo' having fecha<12 ";
		$select=DB::select($sql);
		 
		if(count($select)>0)
		{
			echo 1;			
		}else{
			echo 0;
		}
		
	}
	
	public function postContrasena()
	{	
		extract($_POST);
		
		return view('auth.contrasena',compact('id'));
	}
	
	public function postCambio()
	{
		extract($_POST);
				
		if($contra==$recontra){
			
			$Ncontra=bcrypt($contra);
			$sql="update users set password='$Ncontra' where par_identificacion='$id'";			
			$update=DB::update($sql);
			
			echo
				'<script type="text/javascript">
					alert("Cambio de contraseña exitoso");					
				</script>';
				 
				  return \Redirect::to(url('/auth/login'));
		}
		else{
			echo
				'<script type="text/javascript">
					alert("Las contraseñas no coinciden");					
				</script>';
				return view('auth.contrasena',compact('id'));
		}
		
	}
	

}
