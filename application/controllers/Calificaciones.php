<?
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Calificaciones extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
		parent::__construct();
		$this->load->model("Model_Calificaciones");
		$this->load->model("Model_Clientes");
        $this->load->model("Model_Usuarios");
         $this->load->model("Model_Cuestionarios");
	}
	public function realiza_post(){
		$datos= $this->post();
		if (isset($datos)) {
			$request = json_decode($datos[0]);
			$datos_empresa_receptora=$request[0]->datos_receptora;
			$datos_empresa_emisora=$request[0]->datos_emisor; 

			//obtengo los datos de la empresa que realiza
			if($request[0]->tipo==='realiza'){
				$_datos_Usuario_emisor=$this->Model_Usuarios->datos_usuario($datos_empresa_emisora->usuario);
				$cuestionario=$this->Model_Calificaciones->cuestionario($datos_empresa_emisora->empresa,$_datos_Usuario_emisor->IDConfig,$datos_empresa_receptora->IDPerfil,$datos_empresa_receptora->perfil);
			}else{
				$_datos_Usuario_receptor=$this->Model_Usuarios->datos_usuario($datos_empresa_receptora->usuario);
				$cuestionario=$this->Model_Calificaciones->cuestionario($datos_empresa_receptora->empresa,$datos_empresa_emisora->IDPerfil,$_datos_Usuario_receptor->IDConfig,$datos_empresa_receptora->perfil);
			}
	
			//obtengo el cuestionario que se va a realizar
			
			if($cuestionario===FALSE){
				$data["pass"]=0;
				$data["Mensaje"]="Sin relación";
			}else{
				$data["pass"]=1;
				$data["Mensaje"]=$cuestionario;
			}
			$this->response($data);
		}
	}
	//funcion para recibir un cuestionario y ponerlo en la base de datos
	public function addcuestioario_post(){
		$_variables= $this->post();
		$cuestionario=json_decode($_variables["cuestioario"]);
		$datoscalifica=json_decode($_variables["datos"]);
		$_empresa_emisora=$datoscalifica[0]->datos_emisor;
		$_empresa_receptora=$datoscalifica[0]->datos_receptora;
		if($datoscalifica[0]->tipo==="realiza"){
			$_ID_Emisor=$_empresa_emisora->usuario;
			$_ID_Receptor=$_empresa_receptora->empresa;
			$_TEmisor=$_empresa_emisora->perfil;
			$_TReceptor=$_empresa_receptora->perfil;
		}else if($datoscalifica[0]->tipo==="recibe"){
			$_ID_Emisor=$_empresa_emisora->empresa;
			$_ID_Receptor=$_empresa_receptora->usuario;
			$_TEmisor=$_empresa_emisora->perfil;
			$_TReceptor=$_empresa_receptora->perfil;
		}else{
			exit();
		}
		
		//ahora agrego la calificacion
		$_ID_Valora=$this->Model_Calificaciones->addcalificacion($_ID_Emisor,$_ID_Receptor,$_TEmisor,$_TReceptor);
		//ahora inserto el cuestionario en detalle de calificaciones
		$promedio=$this->Model_Calificaciones->adddetallecalificacion($cuestionario,$_ID_Valora);
		$this->Model_Calificaciones->modpromedio($promedio,$_ID_Valora);
		$data=array("pass"=>1,"mensaje"=>"ok");
		$this->response($data);
		
	}
	 public function realizaqr_post(){
	 	$_variables= $this->post();
	 	$datos=json_decode($_variables["datos"]);
	 	$datclie=explode("|",$datos->cliente);        
        $bandera=false;
       // vdebug($datos);
        //primero busco si el cliente que viene es valido
      	if($datclie[0]==="E" || $datclie[0]==="I" ){
                $bandera=true;
        }else{
             $data["pass"]=0;
             $data["mensaje"]="Este Código no es valido";
        }

        if($bandera===true){
	 	   //verifico si es interno o externo
        	if($datclie[0]==="E" ){
                 $datcliente=$this->Model_Clientes->ReadClieusu($datclie[1],$datos->empresa);
                  if($datcliente!==false){
                 	$data["cliente"]=array("ID"=>$datcliente->IDCliente,"Clave"=>$datcliente->Clave,"Nombre"=>$datcliente->Nombre." ".$datcliente->Apellidos,"conficlie"=>$datcliente->IDConfig,"Usuario"=>$datcliente->Usuario,"TipoE"=>"E");
                 	 //ahora busco una relacion de cuestionarios
            		$dats_Cuest=$this->Model_Cuestionarios->relacion($datos->idconfiguracion,$datcliente->IDConfig,"I",$datclie[0]);
			            if($dats_Cuest!=false){
			             //ahora busco coloco las preguntas
			                $data["DCuestionario"]=$dats_Cuest;
			                $data["cuestionario"]=$this->Model_Cuestionarios->CuestionarioApp($datos->empresa,$dats_Cuest->Cuestionario);
			                 $data["pass"]=1;              
			            }else{
			             $data["pass"]=0;
			             $data["mensaje"]="Sin relación con este cliente.";
			            }
             		}else{
             			$data["pass"]=0;
             			$data["mensaje"]="Este Código no es valido";
             		}
            }else if($datclie[0]==="I"){
                 $datcliente=$this->Model_Usuarios->DatosUsuarious($datclie[1],$datos->empresa);
                 if($datcliente!==false){
                 	 $data["cliente"]=array("ID"=>$datcliente->IDUsuario,"Clave"=>$datcliente->Clave,"Nombre"=>$datcliente->Nombre. " ".$datcliente->Apellidos,"Usuario"=>$datcliente->Usuario,"TipoE"=>"I","conficlie"=>$datcliente->IDConfig);
                 	  //ahora busco una relacion de cuestionarios
           			 $dats_Cuest=$this->Model_Cuestionarios->relacion($datos->idconfiguracion,$datcliente->IDConfig,"I",$datclie[0]);
           			 if($dats_Cuest!=false){
		             //ahora busco coloco las preguntas
		                $data["DCuestionario"]=$dats_Cuest;
		                $data["cuestionario"]=$this->Model_Cuestionarios->CuestionarioApp($dats_Cuest->Cuestionario);
		                 $data["pass"]=1;  

		            }else{
		             $data["pass"]=0;
		             $data["mensaje"]="Sin relación con este cliente.";
		            }
                 }else{
                 	$data["pass"]=0;
                 	$data["mensaje"]="Este Código no es valido";
                 }
                
            }
        }
        //vdebug($data);
        $this->response($data);     
        
    }
    public function recibeapp_post(){
        $_variables= $this->post();
	 	$datos=json_decode($_variables["datos"]);
	 	$datclie=explode("|",$datos->cliente);        
        $bandera=false;
        //primero busco si el cliente que viene es valido
        $datclie=explode("|",$datos->cliente);
        if($datclie[0]==="E" || $datclie[0]==="I" ){
                $bandera=true;
                
        }else{
             $data["pass"]=0;
             $data["mensaje"]="Este Código no es valido";
        }
        //reviso la banderera
        if($bandera===true){
            //verifico si es interno o externo
            if($datclie[0]==="E" ){
                 $datcliente=$this->Model_Clientes->ReadClieusu($datclie[1],$datos->empresa);
                  $data["cliente"]=array("ID"=>$datcliente->IDCliente,"Clave"=>$datcliente->Clave,"Nombre"=>$datcliente->Nombre." ".$datcliente->Apellidos,"Usuario"=>$datcliente->Usuario,"TipoE"=>"E");
                  
            }else if($datclie[0]==="I"){
                 $datcliente=$this->Model_Usuarios->DatosUsuarious($datclie[1],$datos->empresa);
                 $data["cliente"]=array("ID"=>$datcliente->IDUsuario,"Clave"=>$datcliente->Clave,"Nombre"=>$datcliente->Nombre. " ".$datcliente->Apellidos,"Usuario"=>$datcliente->Usuario,"TipoE"=>"I");
                 
            }
            //ahora busco una relacion de cuestionarios
            $dats_Cuest=$this->Model_Cuestionarios->relacion($datcliente->IDConfig,$datos->idconfiguracion,$datclie[0],"I");
            if($dats_Cuest!=false){
             //ahora busco coloco las preguntas
                $data["DCuestionario"]=$dats_Cuest;
                vdebug($data);
                $data["cuestionario"]=$this->Model_Cuestionarios->CuestionarioApp($dats_Cuest->Cuestionario);  
                $data["pass"]=1;            
            }else{
             $data["pass"]=0;
             $data["mensaje"]="Sin relación con este cliente.";
            }
          
        }
        $this->response($data); 
    }
}