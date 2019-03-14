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
			
			$_Tipo_Perfil_Relacion=$_Relacion['TPReceptor'];
			$_Perfil_Relacion=$_Relacion['PerfilCalificado'];
			
			$data["empresas"]=$this->Model_Clientes->buscarrazon($datos[0]->palabra,$datos[0]->empresa,$datos[0]->usuario,$datos[0]->tipo);
			
			
			$this->response($data);
		}
	}
	
}