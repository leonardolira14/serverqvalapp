<?
class Model_Clientes extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		 $this->constante="FpgH456Gtdgh43i349gjsjf%ttt";

	}

	public function buscarrazon($palabra,$empresa,$usuario,$_Tipo){
		$resultados=[];
		//primero traigo la realcion de este usuario
		$sql=$this->db->select("*")->where("IDUsuario=$usuario")->get("usuario");
		$config=$sql->row()->IDConfig;
		//ahora obtengo con los que tiene realaion
		if($_Tipo==="recibe"){
			$sql=$this->db->select("*")->where("PerfilCalificado='$config'")->get("detallecuestionario");
			if($sql->num_rows()===0){
				$pin_ex=0;
			}else{
				$pin_ex=$sql->row()->TPEmisor;
			}
			
		}else{
			$sql=$this->db->select("*")->where("PerfilCalifica='$config'")->get("detallecuestionario");
		    $pin_ex=$sql->row()->TPReceptor;
		}
		
		if($pin_ex==="E"){
			$sql=$this->db->select("Nombre,IDCliente,NombreComercial,IDConfig")->like("Nombre",$palabra)->where("IDEmpresa=$empresa")->get("clientes");
			foreach ($sql->result() as $resultado) {
				array_push($resultados,array("Nombre"=>$resultado->Nombre,"NC"=>$resultado->NombreComercial,"Num"=>$resultado->IDCliente,"config"=>$pin_ex,"numconfig"=>$resultado->IDConfig));
			}
		}
		if($pin_ex==="I"){
			$sql=$this->db->select("IDUsuario,Nombre,Apellidos,Puesto,IDConfig")->like("Nombre",$palabra)->where("IDEmpresa=$empresa")->get("usuario");
			foreach ($sql->result() as $resultado) {
				array_push($resultados,array("Nombre"=>$resultado->Nombre." ".$resultado->Apellidos ,"NC"=>$resultado->Puesto,"Num"=>$resultado->IDUsuario,"config"=>$pin_ex,"numconfig"=>$resultado->IDConfig));
			}
		}
		$data["Res"]=$resultados;
		return $data;
	}
	public function getDatos($_ID_Empresa){
		$sql=$this->db->select("*")->where("IDEmpresa='$_ID_Empresa'")->get("clientes");
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
}