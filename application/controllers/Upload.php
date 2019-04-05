<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;

class Upload extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
    	parent::__construct();
    }
    public function cargaarchivos_post(){
        if(count($_FILES)!==0){
            $_Imagen=$_FILES["archivo"]["name"];
            $ruta='./assets/archivos/';
            $rutatemporal=$_FILES["archivo"]["tmp_name"];
            $nombreactual=$_FILES["archivo"]["name"];	
            try {
                if(! move_uploaded_file($rutatemporal, $ruta.$nombreactual)){
                    $_data["code"]=1991;
                    $_data["ok"]="ERROR";
                    $_data["result"]="No se puede subir el archivo". $nombreactual;
                }
                $_data["code"]=0;
                $_data["ok"]="SUCCESS";
            } catch (Exception $e) {
						$_data["code"]=1991;
						$_data["ok"]="ERROR";
						$_data["result"]=$e->getMessage();
			}
         }
         $data["response"]=$_data;
		 $this->response($data);
    }
}