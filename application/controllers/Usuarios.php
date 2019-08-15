<?
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Usuarios extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
		parent::__construct();
		$this->load->model("Model_Usuarios");
		$this->load->model("Model_Empresa");
	}

	//funcion para el login de la app
	public function loginapp_post(){
		$datos= $this->post();
		if (isset($datos)) {
			$request = json_decode($datos[0]);

			$respuesta=$this->Model_Usuarios->getuserlogin($request->correo,$request->clave);	
			
			if($respuesta!==false){
				$_datos_Empresa=$this->Model_Empresa->getEmpresa($respuesta->IDEmpresa);
				$data["pass"]=1;
				$data["datos"]=$respuesta;
				$data["empresa"]=$_datos_Empresa;
			}else{
				$data["pass"]=0;
				$data["datos"]="Error de usuario y/o contraseña";

			}
			
			$this->response($data);
		}
		
	}
	public function updateinfo_post(){
		$datos=$this->post();
		if(isset($datos)){
			$datos=json_decode($datos["datos"]);
			if($datos->tipo==="n"){
				$this->Model_Usuarios->update_name($datos->idusuario,$datos->dato);
				$_data["pass"]=1;
			}else if($datos->tipo==="a"){
				$this->Model_Usuarios->update_apellidos($datos->idusuario,$datos->dato);
				$_data["pass"]=1;
			}else if($datos->tipo==="ce"){
				$this->Model_Usuarios->update_correo($datos->idusuario,$datos->dato);
				$_data["pass"]=1;
			}else if($datos->tipo==="p"){
				$this->Model_Usuarios->update_usuario($datos->idusuario,$datos->dato);
				$_data["pass"]=1;
			}else if($datos->tipo==="u"){
				$this->Model_Usuarios->update_puesto($datos->idusuario,$datos->dato);
				$_data["pass"]=1;
			}else if($datos->tipo==="clave"){
				if($this->security->xss_clean($datos->clave)!==TRUE && $this->security->xss_clean($datos->clave2)){
					$rclave2=validar_clave($datos->clave2);
					if($rclave2["pass"]===0){
						$_data["pass"]=0;
						$_data["mensaje"]=$rclave2["mensaje"];
					}else{
						$respuesta=$this->Model_Usuarios->update_clave($datos->idusuario,$datos->clave,$datos->clave2);	
						if($respuesta===FALSE){
							$_data["pass"]=0;
							$_data["mensaje"]="La contraseña anterior no coincide.";
						}else{
							$_data["pass"]=1;
						}
					}
					
				}else{
					exit();
				}
			}else{
				exit();
			}
			$_data["datos"]=$this->Model_Usuarios->datos_usuario($datos->idusuario);
			$this->response($_data);
		}
	}
}