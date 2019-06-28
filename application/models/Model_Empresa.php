<?

/**
 * 
 */
class Model_Empresa extends CI_Model
{
	
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		

	}
	//function para obtener los datos de empresa
	public function getEmpresa($_IDEmpresa){
		$respuesta=$this->db->select("*")->where("IDEmpresa='$_IDEmpresa'")->get("empresa");
		return $respuesta->result_array();
	}
}