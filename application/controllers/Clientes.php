<?php
defined('BASEPATH') OR exit('No direct script access allowed');


require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;

class Clientes extends REST_Controller {
		function __construct()
			{
				header("Access-Control-Allow-Methods: GET");
		    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		    	header("Access-Control-Allow-Origin: *");
				parent::__construct();
				$this->load->model("Model_Clientes");
				$this->load->model("Model_Usuarios");
			}

		public function checkpass_post(){
			$datos= $this->post();
			//vdebug($datos);
			if($datos["tipo"]==="E"){
				$clave=md5($datos["clave"]);
				$data["ok"]=$this->Model_Clientes->checkpassword($datos["idempresa"],$clave);
			}else{
				$data["ok"]=$this->Model_Usuarios->checkpassword($datos["idempresa"],$datos["clave"]);
			}

			
			$this->response($data);
		} 
		public function updatepass_post(){
			$datos= $this->post();
			$aleatoria="";
			$caracteres = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-.#!';
			for($x = 0; $x < 1; $x++){
				$aleatoria =$aleatoria. substr(str_shuffle($caracteres), 0, 6);
			}
			$clave=md5($aleatoria);
			$this->Model_Clientes->updatepassword($datos["empresa"],$clave);
			$data["clave"]=$aleatoria;
			$this->response($data);
			
		}
}