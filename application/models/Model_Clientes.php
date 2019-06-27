<?
class Model_Clientes extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		 $this->constante="FpgH456Gtdgh43i349gjsjf%ttt";

	}

	public function buscarrazon($palabra,$empresa,$usuario,$_Tipo,$_IDConfig){
		$resultados=[];
		
		if($_Tipo==="E"){
			$sql=$this->db->select("Nombre,IDCliente,NombreComercial,IDConfig")->like("Nombre",$palabra)->where("IDEmpresa=$empresa and IDConfig='$_IDConfig'")->get("clientes");
			
			foreach ($sql->result() as $resultado) {
				array_push($resultados,array("Nombre"=>$resultado->Nombre,"NC"=>$resultado->NombreComercial,"Num"=>$resultado->IDCliente,"config"=>$_Tipo,"numconfig"=>$resultado->IDConfig));
			}
		}
		if($_Tipo==="I"){
			$sql=$this->db->select("IDUsuario,Nombre,Apellidos,Puesto,IDConfig")->like("Nombre",$palabra)->where("IDEmpresa=$empresa and IDConfig='$_IDConfig'" )->get("usuario");
			foreach ($sql->result() as $resultado) {
				array_push($resultados,array("Nombre"=>$resultado->Nombre." ".$resultado->Apellidos ,"NC"=>$resultado->Puesto,"Num"=>$resultado->IDUsuario,"config"=>$_Tipo,"numconfig"=>$resultado->IDConfig));
			}
		}
		return $resultados;
	}
	public function getDatos($_ID_Empresa){
		$sql=$this->db->select("*")->where("IDEmpresa='$_ID_Empresa'")->get("clientes");
		if($sql->num_rows()===0){
			return false;
		}else{
			return $sql->row();
		}
	}
	public function getDatosCliente($_ID_Empresa){
		$sql=$this->db->select("*")->where("IDCliente='$_ID_Empresa'")->get("clientes");
		if($sql->num_rows()===0){
			return false;
		}else{
			return $sql->row();
		}
	}
	public function ReadClieusu($num,$empresa){
		$sql=$this->db->select("*")->where("Usuario='$num' and IDEmpresa='$empresa'")->get("clientes");
		return $sql->result()[0];
	}
	public function checkpassword($IDEmpresa,$Password){
		$sql=$this->db->select("*")->where("IDCliente='$IDEmpresa' and Clave='$Password'")->get("clientes");
		if($sql->num_rows()===0){
			return false;
		}else{
			return true;
		}
	}
	public function updatepassword($IDEmpresa,$Password){
		$sql=$this->db->where("IDCliente='$IDEmpresa'")->update("clientes",array("Clave"=>$Password));
		
	}
}