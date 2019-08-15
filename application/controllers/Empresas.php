<?
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Empresas extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
		parent::__construct();
		$this->load->model("Model_Clientes");
		$this->load->model("Model_Usuarios");
		$this->load->model("Model_Cuestionarios");
		
	}
	public function buscar_post(){
		$datos= $this->post();
		if (isset($datos)) {
			$datos=json_decode($datos[0]);
			$data["error"]=FALSE;
			
			$_palabra=$datos[0]->palabra;
			$_empresa=$datos[0]->empresa;
			$_ID_usuario=$datos[0]->usuario;
			$_ID_tipo=$datos[0]->tipo;
			
			//ahora obtengo los datos del usuario;
			$_Datos_Usuario=$this->Model_Usuarios->datos_usuario($_ID_usuario);
			$_Perfil=$_Datos_Usuario->IDConfig;

			//obtengo el tipo de relacion que tenga
			$_Relacion=$this->Model_Cuestionarios->relacion_buscar($_ID_tipo,$_Perfil);
			
			//ya que tengo las relaciones tengo que meter las empresas
			$d=[];
			foreach($_Relacion as $relacion){
				if($_ID_tipo==="recibe"){
					$resp=$this->Model_Clientes->buscarrazon($_palabra,$_empresa,$_ID_usuario,$relacion["TPEmisor"],$relacion["PerfilCalifica"]);	
				}
				if($_ID_tipo==="realiza"){
					$resp=$this->Model_Clientes->buscarrazon($_palabra,$_empresa,$_ID_usuario,$relacion["TPReceptor"],$relacion["PerfilCalificado"]);
				}
				if(count($resp)!==0){
					foreach($resp as $datos){
						array_push($d,$datos);
					}
								
				}
				
			}
			$unique = array_map('unserialize', array_unique(array_map('serialize', $d)));
			$data["empresas"]=$unique;
			$this->response($data);
			//;
			
			
			
			
			
			
			
		}
	}
	
}