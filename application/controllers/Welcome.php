<?php
defined('BASEPATH') OR exit('No direct script access allowed');


require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;

class Welcome extends REST_Controller {

	function __construct()
	{
		header("Access-Control-Allow-Methods: GET");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
		parent::__construct();
		$this->load->model("Model_Calificaciones");
	}
	public function index_get(){
		$relaciones["adf"]="asdf"
		$this->response($relaciones);
	}
	
	public function downloaddate_post(){
		$datos= $this->post();
		if (isset($datos)) {

			$datos=json_decode($datos["datos"]);
			$_ID_Usuario=$datos->datos->IDUsuario;
			$_ID_Empresa=$datos->datos->IDEmpresa;
			$_ID_perfil_usuario=$datos->datos->IDConfig;
			//obtengo las relaxiones que tiene el usuario para que pueda calificar
			$relaciones=$this->Model_Calificaciones->datos_de_relacion($_ID_Empresa,$_ID_Usuario,$_ID_perfil_usuario);
			$this->response($relaciones);
			//vdebug($relaciones);
		}else{

		}

	}
	public function update_post(){
		$datos= $this->post();
		if (isset($datos)) {
			$datos=json_decode($datos["datos"]);
			foreach ($datos as $calificacion) {
			
				$_empresa_emisora=$calificacion->datos_calificacion[0]->datos_emisor;
				$_empresa_receptora=$calificacion->datos_calificacion[0]->datos_receptora;	
				
				if($calificacion->datos_calificacion[0]->tipo==="realiza"){
					$_ID_Emisor=$_empresa_emisora->usuario;
					$_ID_Receptor=$_empresa_receptora->empresa;
					$_TEmisor=$_empresa_emisora->perfil;
					$_TReceptor=$_empresa_receptora->perfil;
				}else if($calificacion->datos_calificacion[0]->tipo==="recibe"){
					$_ID_Emisor=$_empresa_emisora->empresa;
					$_ID_Receptor=$_empresa_receptora->usuario;
					$_TEmisor=$_empresa_emisora->perfil;
					$_TReceptor=$_empresa_receptora->perfil;
				}else{
					exit();
				}
				$cuestionario=$calificacion->cuestionario;
				$_ID_Valora=$this->Model_Calificaciones->addcalificacion($_ID_Emisor,$_ID_Receptor,$_TEmisor,$_TReceptor);
				$promedio=$this->Model_Calificaciones->adddetallecalificacion($cuestionario,$_ID_Valora);
				$this->Model_Calificaciones->modpromedio($promedio,$_ID_Valora);
			}
			$data=array("pass"=>1,"mensaje"=>"ok");
			$this->response($data);
			
		}
	}
}
