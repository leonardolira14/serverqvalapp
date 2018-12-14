<?php
// prevent the server from timing out

set_time_limit(0);

$mysqli = new mysqli('localhost','root','admyo246','chats');
$sql="truncate usuarios";
$result=$mysqli->query($sql);
$mysqli->close();

// include the web sockets server script (the server is started at the far bottom of this file)
require 'clases/class.PHPWebSocket.php';

// when a client sends data to the server
function wsOnMessage($clientID, $message, $messageLength, $binary) 
{
	global $Server;
	$cabina="";
	$ip = long2ip( $Server->wsClients[$clientID][6] );
	$Server->log( "$ip ($clientID) -->".$message );
	
	// check if message length is 0
	if ($messageLength == 0) {
		$Server->wsClose($clientID);
		return;
	}
	$k=explode("|",$message);
	if ($k[0]=="REG") {
		
		if($k[2]==="cabina"){
			$id=Nocabina();
			if($id===false){
				$mysqli = new mysqli('localhost','root','admyo246','chats');
				$sql="insert into usuarios (App,Usr,Keyid) values ('".$k[2]."','".$k[1]."','".$clientID."')";
	    		$result=$mysqli->query($sql);
			}
			
		}else{	
			$mysqli = new mysqli('localhost','root','admyo246','chats');
			$sql="insert into usuarios (App,Usr,Keyid) values ('".$k[2]."','".$k[1]."','".$clientID."')";
	    	$result=$mysqli->query($sql);	
		}
		
    	if($k[2]==="cabina"){
    		$_data["funcion"]="LSIT";
			$_data["mensaje"]=obtener_lista();
			$Server->wsSend($clientID,json_encode($_data));
    	}else{
    		$Server->wsSend($clientID, json_encode(array("funcion"=>"REG","mensaje"=>"Bienvenido a Soporte Tecnico ¿En que te puedo ayudar?")));
    		$lista=obtener_lista();

			//ahora obtnego el id key de cabina
			$id=Nocabina();
			$_data["funcion"]="LSIT";
			$_data["mensaje"]=$lista;
			$_data["delete"]="";
			$Server->wsSend($id,json_encode($_data));
			}
			
    	}
 
	if($k[0]=="MSG"){
		$mysqli = new mysqli('localhost','root','admyo246','chats');
		//ahora obtego los nombre de las partes afectadas
		$sql="select * from usuarios where Keyid='$clientID'";
		$emisor=$mysqli->query($sql);
		 while($row2 = $emisor->fetch_array()){
				 $id_emisor=$row2["Keyid"];
				  $usuario_emisor=$row2["Usr"];
				  $App_emisor=$row2["App"];
		}
		if($App_emisor==="usuario"){
			$id=Nocabina();
		}else{
			$id=$k[2];
		}
		$_data["funcion"]="MSG";
		$_data["mensaje"]=$k[1];
		$_data["nombre"]=$usuario_emisor;
		$_data["key"]=$clientID;
		$Server->wsSend($id,json_encode($_data));
	}
	
}
function Nocabina(){
	$mysqli = new mysqli('localhost','root','admyo246.','chats');

	$sql="select * from usuarios where App='cabina'";
			$result=$mysqli->query($sql);
		if($result->num_rows>0){
			while($row2 = $result->fetch_array()){
				 	 $id=$row2["Keyid"];
			}
			return $id;
		}else{
			return false;
		}
}
// when a client connects
function wsOnOpen($clientID)
{
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	$Server->log( "$ip ($clientID) has connected." );
}

// when a client closes or lost connection
function wsOnClose($clientID, $status) {
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );
	$Server->log( "$ip ($clientID) has disconnected." );
	//$Server->log( "$ip ($clientID) has disconnected." );
       $mysqli = new mysqli('localhost','root','admyo246','chats');
		$sql="delete from usuarios where Keyid='".$clientID."'";
    	$result=$mysqli->query($sql);
    	$lista=obtener_lista();
		//ahora obtnego el id key de cabina
			$id=Nocabina();
			$_data["funcion"]="LSIT";
			$_data["mensaje"]=$lista;
			$_data["delete"]=$clientID;
			$Server->wsSend($id,json_encode($_data));
}
function obtener_lista(){
	$array=[];
	$mysqli = new mysqli('localhost','root','admyo246','chats');
	$sql="select * from usuarios";
	$resultados=$mysqli->query($sql);
	while($row = $resultados->fetch_assoc()){
		array_push($array,array("app"=>$row["App"],"usr"=>$row["Usr"],"keyid"=>$row["Keyid"]));
	}
	return $array;

}
$Server = new PHPWebSocket();
$Server->bind('message', 'wsOnMessage');
$Server->bind('open', 'wsOnOpen');
$Server->bind('close', 'wsOnClose');

$Server->wsStartServer('172.31.11.225',9000);

?>