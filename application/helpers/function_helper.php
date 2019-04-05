<?if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if(!function_exists("_compracion"))
{
		function _comparacion($numero1,$numero2)
		{
			if($numero1===$numero2){
				return 1;
			}else if($numero1>$numero2){
				return 2;
			}elseif($numero1<$numero2){
				return 3;
			}
		}

}
if(!function_exists("validar_clave")){
	function validar_clave($clave){
   if(strlen($clave) < 6){
   	  $_data["pass"]=0;
      $_data["mensaje"] = "La clave debe tener al menos 6 caracteres";
   }else if(strlen($clave) > 16){
     $_data["pass"]=0;
      $_data["mensaje"] = "La clave no puede tener más de 16 caracteres";
    
   }else if (!preg_match('`[a-z]`',$clave)){
     $_data["pass"]=0;
      $_data["mensaje"] ="La clave debe tener al menos una letra minúscula";
     
   }else if (!preg_match('`[A-Z]`',$clave)){
     $_data["pass"]=0;
      $_data["mensaje"] ="La clave debe tener al menos una letra mayúscula";
    
   }else if (!preg_match('`[0-9]`',$clave)){
      $_data["pass"]=0;
      $_data["mensaje"] = "La clave debe tener al menos un caracter numérico";
     
   }else{
   	 $_data["pass"]=1;
   }
   return $_data;
   
}
}
if(!function_exists("limpiar_array")){
	function limpiar_array($array){
		foreach ($arry as $item) {
			
		}
	}	
}
if(!function_exists("_media_puntos"))
{
	function _media_puntos($_puntos_obtenidos,$_puntos_posibles){
		
		if(bccomp($_puntos_obtenidos, $_puntos_posibles) == 0){
			$num=0;
		}else{
			$num=round(($_puntos_obtenidos/$_puntos_posibles)*10,2);
		}

		if($num===0){
				$_data["class"]="text-blue";
		}else if($num>0){
				$_data["class"]="text-success";
		}else if($num<0){
				$_data["class"]="text-red";
		}
		$_data["num"]=$num;
		return $_data;
	}
}
if(!function_exists('_increment'))
{
	function _increment($a,$b,$c)
	{

		
		$num=0;
		$_data=[];
		
		if(bccomp($a, $b)===0){
			$num=0;
		}else if((int)$b===0){
			$num=100;
		}else if((int)$a===0){
			$num=-100;
		}else{
			
			$num=round((((float)$a-(float)$b)/(float)$b)*100,2);
		}

		if($c==="imagen"){
			if($num===0){
				$_data["class"]="text-blue";
			}else if($num>0){
				$_data["class"]="text-success";
			}else if($num<0){
				$_data["class"]="text-red";
			}
			$_data["num"]=$num."%";

		}else{
			if($num===0){
				$_data["class"]="text-blue";
			}else if($num<0){
				$_data["class"]="text-success";
			}else if($num>0){
				$_data["class"]="text-red";
			}
			$_data["num"]=$num."%";

		}
		
		return $_data;
	}
}
if(!function_exists('_build_joson'))
{
	function _build_json($_status=FALSE,$_data=FALSE,$_controller=FALSE)
	{
		$CI= &get_instance();
		if(!(boolean)$_status)
		{
			if(isset($_data['message_identifier']))
			{
				if((boolean)$_controller)
					$_data["message"]=$CI->lang->line($CI->data["controller"].$_data["message_identifier"]);
				else
					$_data["message"]=$CI->lang->line($_data["message_identifier"]);
			}
			else
			{
					$_data["message"]=$CI->lang->line("_cannot_complete");
			}
		}
		$_data["status"]=$_status;
		exit(json_encode($_data));
	}
}
if(!function_exists('_is_ajax_request'))
{
	function _is_ajax_request()
	{
		$CI= &get_instance();
		if(!$CI->input->is_ajax_request())
			_build_json();
	}
}
if(!function_exists('_is_post'))
{
	function _is_post()
	{
		if($_SERVER['REQUEST_METHOD']!=='POST')
			_build_json();
	}	
}
if(!function_exists("_media_puntos"))
{
	function _media_puntos($_puntos_obtenidos,$_puntos_posibles){
		
		if($_puntos_obtenidos===0 && $_puntos_posibles===0){
			$num=0;
		}else{
			$num=round(($_puntos_obtenidos/$_puntos_posibles)*10,2);
		}

		if($num===0){
				$_data["class"]="text-blue";
		}else if($num>0){
				$_data["class"]="text-success";
		}else if($num<0){
				$_data["class"]="text-red";
		}
		$_data["num"]=$num;
		return $_data;
	}
}
if(!function_exists("_is_respcorrect"))
{
	function _is_respcorrect($respuesta_correcta,$respuesta,$calificacion,$tipopregunta){
		if($tipopregunta==="AB" || $tipopregunta==="FECHA" || $tipopregunta==="HORA" || $tipopregunta==="F/H" || $tipopregunta==="NUMERO" || $tipopregunta==="START" ){
			if($respuesta!=="" || $respuesta!==false){
				$_calificacion=$calificacion;
			}else{
				$_calificacion=0;
			}	
			return $_calificacion;
		}
		if($tipopregunta==="DESLIZA" ||  $tipopregunta==="CARGA"){
			return $calificacion;
		}
		
		if($tipopregunta==="SI/NO" || $tipopregunta==="SI/NO/NA" || $tipopregunta==="SI/NO/NS"){
			if($respuesta==="NA" || $respuesta==="NS"){
				return $_calificacion=0;
			}else if($respuesta_correcta==="SR"){
				return $calificacion;
			}else{
				if($respuesta_correcta!==$respuesta){
					return $_calificacion=0;
				}else{
					return $_calificacion=$calificacion;
				}
			}
		}
		if($tipopregunta==="MLC"){
			if(count($respuesta)===0){
				return $_calificacion=0;
			}else if($respuesta_correcta==="SR"){
				return $calificacion;
			}else{
				return $calificacion;
			}

		}
		if($tipopregunta==="ML"){
			if($respuesta===false || $respuesta!==$respuesta_correcta){
				return $_calificacion=0;
			}else if($respuesta_correcta==="SR"){
				return $calificacion;
			}else{
				if($respuesta_correcta!==$respuesta){
					return $_calificacion=0;
				}else{
					return $_calificacion=$calificacion;
				}
			}
		}
	}	
}
