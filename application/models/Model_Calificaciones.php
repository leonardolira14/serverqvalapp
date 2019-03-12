<?

/**
 * 
 */
class Model_Calificaciones extends CI_Model
{
	
	function __construct()
	{
		 header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
    	 header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
   		 header("Access-Control-Allow-Origin: *");
		parent::__construct();
		$this->load->database();
		 $this->constante="FpgH456Gtdgh43i349gjsjf%ttt";

	}
	
	public function cuestionarios_download($_ID_Empresa,$_ID_Usuario,$_ID_perfil_usuario,$_Tipo){
		if($_Tipo==="recibe"){
			$sql=$this->db->select("cuestionario.IDCuestionario,PerfilCalifica,PerfilCalificado,TPEmisor,TPReceptor,Cuestionario")->where("IDEmpresa='$_ID_Empresa' and Status='1' and detallecuestionario.PerfilCalificado='$_ID_perfil_usuario'")->join('detallecuestionario',"cuestionario.IDCuestionario=detallecuestionario.IDCuestionario")->get('cuestionario');
		}else{
			$sql=$this->db->select("cuestionario.IDCuestionario,PerfilCalifica,PerfilCalificado,TPEmisor,TPReceptor,Cuestionario")->where("IDEmpresa='$_ID_Empresa' and Status='1' and detallecuestionario.PerfilCalifica='$_ID_perfil_usuario'")->join('detallecuestionario',"cuestionario.IDCuestionario=detallecuestionario.IDCuestionario")->get('cuestionario');
		}
		if($sql->num_rows()===0){
			return "";
		}else{
			return $_data["cues_realiza"]=$sql->result();
		}
	}
	//funcion para obtener los datos de los clienes-proveedores o usuarios que tienen relacion con el usuario
	public function clientes_relacion($_ID_Perfil,$_ID_Empresa,$_Tipo){
		if($_Tipo==="I"){
			$sql=$this->db->select("IDUsuario as IDCliente,concat_ws(' ', Nombre,Apellidos) as Nombre ,IDConfig,Usuario,Clave")->where("IDConfig='$_ID_Perfil'")->get('usuario');
		}else{
			$sql=$this->db->select("IDCliente,Nombre,IDConfig,Usuario,Clave")->where("IDConfig='$_ID_Perfil'")->get('clientes');
		}
		return $sql->result();
	}
	public function datos_de_relacion($_ID_Empresa,$_ID_Usuario,$_ID_perfil_usuario){
		$realaciones=[];
		$_data["cues_recibe"]=$this->cuestionarios_download($_ID_Empresa,$_ID_Usuario,$_ID_perfil_usuario,"recibe");
		$_data["cues_realiza"]=$this->cuestionarios_download($_ID_Empresa,$_ID_Usuario,$_ID_perfil_usuario,"realiza");
		//ahora obtengo las preguntas
		$sql=$this->db->select('IDPregunta,Nomenclatura,Pregunta,Forma')->where("IDEmpresa='$_ID_Empresa' and Estado='1'")->get("preguntas");
		$_data["preguntas"]=$sql->result();
		foreach ($_data["cues_recibe"] as $perfiles) {
			array_push($realaciones,array("recibe"=>$this->clientes_relacion($perfiles->PerfilCalifica,$_ID_Empresa,$perfiles->TPEmisor),"Tipo"=>$perfiles->TPEmisor));	
		}
		foreach ($_data["cues_realiza"] as $perfiles) {
			array_push($realaciones,array("realiza"=>$this->clientes_relacion($perfiles->PerfilCalificado,$_ID_Empresa,$perfiles->TPReceptor),"Tipo"=>$perfiles->TPReceptor));	
		}
		$_data["relaciones"]=$realaciones;
		return $_data;
	}
	public function datos_cuestionario($perfilemisor,$perfilreceptor,$T_Receptor){
		$cues=$this->db->select("*")->where("PerfilCalifica='$perfilemisor' and PerfilCalificado='$perfilreceptor' and TPReceptor='$T_Receptor'")->get('detallecuestionario');
		if($cues->num_rows()===0){
			return FALSE;
		}else{
			return $cues->row();
		}
	}
	public function cuestionario($_IDEMpresa,$perfilemisor,$perfilreceptor,$TEmisor){
		$dcuestionario=$this->datos_cuestionario($perfilemisor,$perfilreceptor,$TEmisor);

		if($dcuestionario===FALSE){
			return false;
		}else{
			$cuestionario=[];
			
			$nomenclaturas=explode(",",$dcuestionario->Cuestionario);
			
			foreach ($nomenclaturas as $letra) {
				$datospregunta=$this->datspregunta($_IDEMpresa,$letra);
				
				try {
					array_push($cuestionario,array("Num"=>$datospregunta->IDPregunta,"Pregunta"=>$datospregunta->Pregunta,"Forma"=>$datospregunta->Forma,"Respuesta"=>$datospregunta->Respuesta));
				} catch (Exception $e) {
					vdebug($cuestionario);
				}
				
				
			}

			return $cuestionario;
		}
	}
	//funcion para obtener los datos de un calificador
	public function datosempresa($_Perfil,$_ID){
		$datos=[];
		if($_Perfil==="I"){
			$sql=$this->db->select('*')->where("IDUsuario='$_ID'")->get('usuario');
			foreach ($sql->result() as $dat) {
				array_push($datos,array("Num"=>$dat->IDUsuario,"Nombre"=>$dat->Nombre." ".$dat->Apellidos,"IDConfig"=>$dat->IDConfig,"Correo"=>$dat->Correo,"perfil"=>"I"));
			}
		}else{
			$sql=$this->db->select('*')->where("IDCliente='$_ID'")->get('clientes');
			foreach ($sql->result() as $dat) {
				array_push($datos,array("Num"=>$dat->IDCliente,"Nombre"=>$dat->Nombre,"IDConfig"=>$dat->IDConfig,"Correo"=>$dat->Correo,"perfil"=>"E"));
			}
		}
		return $datos;
	}
	//funcion paraagregar una calificacion
	public function addcalificacion($_ID_Emisor,$_ID_Receptor,$_TEmisor,$_TReceptor){
		
		//ahora obtengo los datos del emiso y del receptor
		$datosrecepor=$this->datosempresa($_TReceptor,$_ID_Receptor);
		$datosemisor=$this->datosempresa($_TEmisor,$_ID_Emisor);
		$cuestionario=$this->datos_cuestionario($datosemisor[0]["IDConfig"],$datosrecepor[0]["IDConfig"],$datosrecepor[0]["perfil"]);
		$array=array("Calificacion"=>0,"IDCuestionario"=>$cuestionario->IDCuestionario,"IDEmisor"=>$datosemisor[0]["Num"],"IDReceptor"=>$datosrecepor[0]["Num"],"TEmisor"=>$datosemisor[0]["perfil"],"TReceptor"=>$datosrecepor[0]["perfil"]);
		$this->db->insert("tbcalificaciones",$array);
		return $this->db->insert_id();
	}
	public function adddetallecalificacion($_cuestionario,$_ID_Valora){
			$pp=0;
			$po=0;
			
		foreach ($_cuestionario as $_pregunta) {
			$array=[];
			//obtengo los datos de la pregunta
			if(gettype($_pregunta->repuesta)=="array"){
				$respuesta=json_encode($_pregunta->repuesta);
			}else{
				$respuesta=$_pregunta->repuesta;
			}
			
			$datos_pregunta=$this->obtener_pregunta("",FALSE,$_pregunta->pregunata);
			$calif=_is_respcorrect($datos_pregunta->Respuesta,$_pregunta->repuesta,$datos_pregunta->Peso,$datos_pregunta->Forma);
			$array=array("IDValora"=>$_ID_Valora,"IDPregunta"=>$datos_pregunta->IDPregunta,"Respuesta"=>$respuesta,"Calificacion"=>$calif);
			$this->db->insert("detallecalificacion",$array);
			$pp=$pp+(float)$datos_pregunta->Peso;
			$po=$po+$calif;
			
		}		
		$media= _media_puntos($po,$pp);
		return $media["num"];
	}
	public function modpromedio($_Promedio,$_ID_Valora){
		$array=array("Calificacion"=>$_Promedio);
		$this->db->where("IDCalificacion='$_ID_Valora'")->update("tbcalificaciones",$array);
	}
	public function datspregunta($empresa,$nomenclatura){
			if(is_numeric($nomenclatura)){
				$sql=$this->db->select('*')->where("IDPregunta='$nomenclatura' and IDEmpresa='$empresa'")->get('preguntas');
			}else{
				$sql=$this->db->select('*')->where("Nomenclatura='$nomenclatura' and IDEmpresa='$empresa'")->get('preguntas');
			}
			
		return $sql->row();
			
	}
	public function obtener_pregunta($_IDEmpresa,$nomenclatura=FALSE,$_ID_Pregunta=FALSE){
		
		if($nomenclatura!==FALSE){
			if(is_numeric($nomenclatura)) {
				$sql=$this->db->select('*')->where("IDPregunta='$nomenclatura' ")->get('preguntas');
			}else{
					$sql=$this->db->select('*')->where("Nomenclatura='$nomenclatura' and IDEmpresa='$_IDEmpresa'")->get('preguntas');
			}
			
		}
		if($_ID_Pregunta!==FALSE){
			$sql=$this->db->select('*')->where("IDPregunta='$_ID_Pregunta'")->get('preguntas');
		}
		
		return $sql->row();
	}

}