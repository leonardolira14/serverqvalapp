<?php
/**
* 
*/
class Model_Cuestionarios extends CI_Model
{
	
	function __construct()
	{
		//parent::__construct();
		$this->load->database();
		//$this->load->helper('ayuda_helper');
	}
	public function getCuestionarios($empresas,$estatus){
		$get=$this->db->select("*")->where("IDEmpresa='$empresas' and Status='$estatus'")->get("cuestionario");
		if($get->num_rows()==0)
		{
			return false;
		}else
		{
			return $get->result();
		}
	}
	public function getCuestionariosHome($empresas,$estatus)
	{
		$get=$this->db->select("cuestionario.IDCuestionario,nombre,Status,PerfilCalifica,PerfilCalificado,TPEmisor,TPReceptor")->from("cuestionario")->join("detallecuestionario","detallecuestionario.IDCuestionario=cuestionario.IDCuestionario")->where("IDEmpresa='$empresas' and Status='$estatus'")->get();
		if($get->num_rows()==0)
		{
			return false;
		}else
		{
			$cuestion=[];
			foreach ($get->result() as $key) {
				array_push($cuestion,array("IDCuestionario"=>$key->IDCuestionario,"Nombre"=>$key->nombre,"Status"=>$key->Status,"Emisor"=>$this->nombreperfil($key->PerfilCalifica,$key->TPEmisor),"Receptor"=>$this->nombreperfil($key->PerfilCalificado,$key->TPReceptor)));
			}
			return $cuestion;
		}
	}
	//funcion para obtener el nombre del perfil para general
	public function nombreperfil($numero,$tipo){
		
			$sql=$this->db->select('Nombre')->where("IDGrupo='$numero' and Tipo='$tipo'")->get("grupos");
			return $sql->result()[0]->Nombre;
		
	}
	//funcion para obtner las preguntas
	public function getPreguntas($empresa,$estado){
		if($estado===3){
			$get=$this->db->select("*")->where("IDEmpresa='$empresa'")->get("preguntas");
		}
		else{
			$get=$this->db->select("*")->where("IDEmpresa='$empresa' and Estado='$estado'")->get("preguntas");
		}
		if($get->num_rows()==0)
		{
			return false;
		}else
		{
			return $get->result();
		}
	}
	//funcion para agregar un cuestionario
	public function addCuestionario($nombre,$idempresa,$email,$wats){
		$array=array("Nombre"=>$nombre,"Status"=>1,"IDEmpresa"=>$idempresa,"Email"=>$email,"Wats"=>$wats);
		$this->db->insert("cuestionario",$array);
		$get=$this->db->select_max('IDCuestionario')->get("cuestionario");
		return $get->result()[0]->IDCuestionario;
	}
	public function adddetallescues($IDCuestionario,$cuestionario,$emisor,$receptor){
		$cues="";
		foreach ($cuestionario as $key) {
			if ($key === end($cuestionario)) {
				$cues.=$key;
			}else{
				$cues.=$key.",";
			}
			
		}
		$em=explode("-",$emisor);
		$rep=explode("-",$receptor);
		$array=array("IDCuestionario"=>$IDCuestionario,"Cuestionario"=>$cues,"PerfilCalifica"=>$em[0],"PerfilCalificado"=>$rep[0],"TPEmisor"=>$em[1],"TPReceptor"=>$rep[1]);
		return $this->db->insert("detallecuestionario",$array);
	}
	//function para ver la configuracion de un cuestionario
	public function checkconfig($empresa,$emisor,$receptor){
		$get=$this->db->select('*')->where("PerfilCalificado='$receptor' and PerfilCalifica='$emisor'")->get('detallecuestionario');
		if($get->num_rows()==0){
			return false;
		}else{
			return true;
		}
	}
	//funcion apra obtner las preguntas de qval
	public function GetPreguntasqval(){
		$get=$this->db->select("*")->get('preguntasqval');
		return $get->result();
	}
	//ultima nomenclatura
	public function ultimanomen($IDEmpresa){
		$get=$this->db->select_max("IDPregunta")->where("IDEmpresa='$IDEmpresa'")->get("preguntas");
		if($get->num_rows()===0){
			return false;
		}
		else{
			$num=$get->result()[0]->IDPregunta;
			$get=$this->db->select("Nomenclatura")->where("IDPregunta='$num'")->get("preguntas");
			if($get->num_rows()===0){
				return false;
			}else{
				return $get->result()[0]->Nomenclatura;
			}
			
		}
	
	}
	//funcion para agregar uan pregunta
	public function AddPrg($pregunta,$forma,$frecuencia,$peso,$respuesta,$estado,$IDEmpresa)
	{
		$letras=$this->ultimanomen($IDEmpresa);
		if($letras===false){
			$Nomenclatura="A";
		}else{
			$Nomenclatura=genenim($letras);
		}
		
		$array=array("Nomenclatura"=>$Nomenclatura,"Pregunta"=>$pregunta,"Forma"=>$forma,"Peso"=>$peso,"Respuesta"=>$respuesta,"Estado"=>1,"IDEmpresa"=>$IDEmpresa,"Frecuencia"=>$frecuencia);
		$get=$this->db->insert("preguntas",$array);
		return $get;
	}
	//cambiar status de una pregunta
	public function ChegPreges($num,$estatus){
		$dat=array("Estado"=>$estatus);
		$this->db->where("IDPregunta='$num'")->update("preguntas",$dat);
	}
	//funcion para obtener los datos de una pregunta
	public function DatPrege($num){
		$get=$this->db->select("*")->where("IDPregunta='$num'")->get("preguntas");
		if($get->num_rows()==0)
		{
			return false;
		}else
		{
			return $get->result();
		}
	}
	public function DatPregenom($num){
		$get=$this->db->select("*")->where("Nomenclatura='$num'")->get("preguntas");
		if($get->num_rows()==0)
		{
			return false;
		}else
		{
			return $get->result();
		}
	}
	//funcio para los datos de una prergunta de qval
	public function DatosPregqval($num){
		$get=$this->db->select("*")->where("IDPregunta='$num'")->get("preguntasqval");
		if($get->num_rows()==0)
		{
			return false;
		}else
		{
			return $get->result();
		}	
	}
	//funcion para los datos de un cuestionario
	public function DatConf($cuestionario){
		$sql=$this->db->select("*")->join('detallecuestionario','detallecuestionario.IDCuestionario=cuestionario.IDCuestionario')->where("cuestionario.IDCuestionario='$cuestionario'")->from("cuestionario")->get();
		if($sql->num_rows()===0){
			return false;
		}else{
			return $sql->result();
		}
	}
	//funcion para modificar un cuestionario
	public function Modicuestion($num,$nombre,$email,$wats){
		$array=array("Nombre"=>$nombre,"Status"=>1,"Email"=>$email,"Wats"=>$wats);
		$get=$this->db->where("IDCuestionario='$num'")->update('cuestionario',$array);
	}
	//funcion para modificar los detalles de un cuestionario
	public function ModDetallesCues($IDCuestionario,$cuestionario,$emisor,$receptor){
		$cues="";
		foreach ($cuestionario as $key) {
			if ($key === end($cuestionario)) {
				$cues.=$key;
			}else{
				$cues.=$key.",";
			}
			
		}
		$em=explode("-",$emisor);
		$rep=explode("-",$receptor);
		$array=array("Cuestionario"=>$cues,"PerfilCalifica"=>$em[0],"PerfilCalificado"=>$rep[0],"TPEmisor"=>$em[1],"TPReceptor"=>$rep[1]);
		return $this->db->where("IDCuestionario='$IDCuestionario'")->update("detallecuestionario",$array);
	}
	public function Dcuestionario($num){
		$sql=$this->db->select("Cuestionario")->where("IDCuestionario='$num'")->get("detallecuestionario");
		if($sql->num_rows()===0){
			return false;
		}else{
			return $sql->result();
		}

	}
	public function AddCuesT($num,$cuestionario){
		$cues="";
		foreach ($cuestionario as $key) {
			if ($key === end($cuestionario)) {
				$cues.=$key;
			}else{
				$cues.=$key.",";
			}
			
		}
		$array=array("Cuestionario"=>$cues);
		return $this->db->where("IDCuestionario='$num'")->update("detallecuestionario",$array);
	}
	public function updateDatPreg($num,$pregunta,$forma,$frecuencia,$puntos,$respuesta){
		$dat=array("Pregunta"=>$pregunta,"Forma"=>$forma,"Frecuencia"=>$frecuencia,"Peso"=>$puntos,"Respuesta"=>$respuesta);
		return $this->db->where("IDPregunta='$num'")->update("Preguntas",$dat);
	}
	//funcion para la app
	//iniciamos con el modo offline donde primero obtenemos los cuestionarios relacionados a ese perfil
	public function offdetalles($conf){
		//obengo los que puede calificar
		$sql=$this->db->select('*')->where("PerfilCalifica='$conf'")->get("detallecuestionario");
		//ahora los que pueden calificarlos
		$sql2=$this->db->select('*')->where("PerfilCalificado='$conf'")->get("detallecuestionario");
		//uno los resultados
		$query = array_merge($sql->result(),$sql2->result());
		//y solo los retorno
		return $query;
	}
	//funcion para obtener los perfiles que puede calificar
	public function offusacalif($conf,$empresa){
		$usuarios=[];
		//obengo los que puede calificar
		$sql=$this->db->select('*')->where("PerfilCalifica='$conf'")->get("detallecuestionario");
		foreach ($sql->result() as $datos) {
			//recorriendo el resultado de los perfiles obtengo el id de la configuracion y de esa manera obtengo los usuarios que calificare;
			if($datos->TPReceptor==="E"){
				$sql=$this->db->select('*')->where("IDConfig='$datos->PerfilCalificado' and IDEmpresa='$empresa'")->get("clientes");
				foreach ($sql->result() as $dat) {
				array_push($usuarios,array("Nombre"=>$dat->Nombre." ".$dat->Apellidos,"IDConfig"=>$dat->IDConfig,"Clave"=>$dat->Clave,"TipoE"=>$datos->TPReceptor,"Usuario"=>$dat->Usuario,"ID"=>$dat->IDCliente));
			}
			}else{
			$sql=$this->db->select('*')->where("IDConfig='$datos->PerfilCalificado' and IDEmpresa='$empresa' ")->get("usuario");
			foreach ($sql->result() as $dat) {
				array_push($usuarios,array("Nombre"=>$dat->Nombre." ".$dat->Apellidos,"IDConfig"=>$dat->IDConfig,"Clave"=>$dat->Clave,"TipoE"=>$datos->TPReceptor,"Usuario"=>$dat->Usuario,"ID"=>$dat->IDUsuario));
			}
			}
			
		}
		return $usuarios;
	}
	//funcion para obtener las preguntas que vamos a usar solo las especificas
	public function offpreguntas($todo){
		$cuestionarion=[];
		$cuestionario=[];
		foreach ($todo as $key ) {
			$cuestion1=explode(",",$key->Cuestionario);
			foreach ($cuestion1 as $nc) {
				if (!in_array($nc,$cuestionarion)){
					array_push($cuestionarion, $nc);
				}
			}
		}
		foreach ($cuestionarion as $pregunta) {
			$preg=$this->DatPregenom($pregunta);
			array_push($cuestionario,$preg[0]);
		}
		return $cuestionario;
	}
	public function relacion($a,$b,$PE,$PR){
		$sql=$this->db->select("*")->where("PerfilCalifica='$a' and PerfilCalificado='$b' and TPEmisor='$PE' and TPReceptor='$PR'")->get("detallecuestionario");
		if($sql->num_rows()!=0){
			return $sql->result()[0];
		}else{
			return false;
		}
	}
	public function obtener_pregunta($nomenclatura=FALSE,$_ID_Pregunta=FALSE){
		if($nomenclatura!==FALSE){
			$sql=$this->db->select('*')->where("Nomenclatura='$nomenclatura' and IDEmpresa='$empresa'")->get('preguntas');
		}
		if($_ID_Pregunta!==FALSE){
			$sql=$this->db->select('*')->where("IDPregunta='$_ID_Pregunta' and IDEmpresa='$empresa'")->get('preguntas');
		}
		
		return $sql->row();
	}
	public function datspregunta($empresa,$nomenclatura){
			if(is_numeric($nomenclatura)){
				$sql=$this->db->select('*')->where("IDPregunta='$nomenclatura' and IDEmpresa='$empresa'")->get('preguntas');
			}else{
				$sql=$this->db->select('*')->where("Nomenclatura='$nomenclatura' and IDEmpresa='$empresa'")->get('preguntas');
			}
			
		return $sql->row();
			
	}
	
	public function CuestionarioApp($_empresa,$cues){
		$cuestionario=[];
			$nomenclaturas=explode(",",$cues);
			
			foreach ($nomenclaturas as $letra) {
				$datospregunta=$this->datspregunta($_empresa,$letra);

				array_push($cuestionario,array("Num"=>$datospregunta->IDPregunta,"Pregunta"=>$datospregunta->Pregunta,"Forma"=>$datospregunta->Forma));
				var_dump($cuestionario);
			}

			return $cuestionario;
	}

}