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
		
	}
	public function buscar_post(){
		$datos= $this->post();
		if (isset($datos)) {
			$datos=json_decode($datos[0]);
			$data["error"]=FALSE;
			$data["empresas"]=$this->Model_Clientes->buscarrazon($datos[0]->palabra,$datos[0]->empresa,$datos[0]->usuario,$datos[0]->tipo);
			$this->response($data);
		}
	}
	
}