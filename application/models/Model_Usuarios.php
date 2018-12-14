<?

/**
 * 
 */
class Model_Usuarios extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		 $this->constante="FpgH456Gtdgh43i349gjsjf%ttt";

	}
	public function datos_usuario($_ID_Usuario){
		$resultado=$this->db->where("IDUsuario='$_ID_Usuario'")->get("usuario");
		return $resultado->row();
	}
	public function getuserlogin($usuario,$clave){
    	$clave=md5($clave.$this->constante);
    	$resp=$this->db->select("*")->where("Usuario='$usuario' and Clave='$clave'")->get("usuario");
    	if($resp->num_rows()===0){
			return false;
    	}else{
			return $resp->row();
    	}
	}
	public function DatosUsuarious($num,$empresa){
		$sql=$this->db->select("*")->where("Usuario='$num' and IDEmpresa='$empresa'")->get("usuario");
		if($sql->num_rows()===0){
			return false;
		}else{
			return $sql->row();
		}
	}
	public function update_name($_ID_Usario,$_Nombre){
		$array=array("Nombre"=>$_Nombre);
		$this->db->where("IDUsuario='$_ID_Usario'")->update("usuario",$array);
	}
	public function update_apellidos($_ID_Usario,$_Nombre){
		$array=array("Apellidos"=>$_Nombre);
		$this->db->where("IDUsuario='$_ID_Usario'")->update("usuario",$array);
	}
	public function update_correo($_ID_Usario,$_Nombre){
		$array=array("Correo"=>$_Nombre);
		$this->db->where("IDUsuario='$_ID_Usario'")->update("usuario",$array);
	}
	public function update_usuario($_ID_Usario,$_Nombre){
		$array=array("Usuario"=>$_Nombre);
		$this->db->where("IDUsuario='$_ID_Usario'")->update("usuario",$array);
	}
	public function update_puesto($_ID_Usario,$_Nombre){
		$array=array("Puesto"=>$_Nombre);
		$this->db->where("IDUsuario='$_ID_Usario'")->update("usuario",$array);
	}
	public function update_clave($_ID_Usario,$clave,$clave2){
		$clave=md5($clave.$this->constante);
		$clave2=md5($clave2.$this->constante);
		$respuesta=$this->db->select('*')->where("IDUsuario='$_ID_Usario=' and Clave='$clave'")->get("usuario");
		if($respuesta->num_rows()===0){
			return FALSE;
		}else{
			
			$array=array("Clave"=>$clave2);
			$this->db->where("IDUsuario='$_ID_Usario'")->update("usuario",$array);
			return TRUE;
		}

	}
}