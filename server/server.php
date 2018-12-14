<?php
// prevent the server from timing out
set_time_limit(0);

$mysqli = new mysqli('187.157.170.83','samtec','sam33','canal');
$sql="truncate wsocket.usuarios";
$result=$mysqli->query($sql);
$mysqli->close();

// include the web sockets server script (the server is started at the far bottom of this file)
require 'class.PHPWebSocket.php';

// when a client sends data to the server
function wsOnMessage($clientID, $message, $messageLength, $binary) {

	global $Server;
	
	$ip = long2ip( $Server->wsClients[$clientID][6] );
	$Server->log( "$ip ($clientID) -->".$message );
	// check if message length is 0
	if ($messageLength == 0) {
		$Server->wsClose($clientID);
		return;
	}
	$k=explode("|",$message);
	if ($k[0]=="REG") {
		$mysqli = new mysqli('187.157.170.83','samtec','sam33','canal');
		$sql="insert into wsocket.usuarios (app,usr,keyid) values ('".$k[2]."','".$k[1]."','".$clientID."')";
    	$result=$mysqli->query($sql);
    	$Server->wsSend($clientID, "REG|Session Iniciada|Servidor");
        //enviamos los mensajes que estan como pendientes (sin enviar)
        $sql="select fecha, mensage, de from wsocket.mensaje where para='".$k[1]."' and status='P'";
        
        $result=$mysqli->query($sql);
		if ($result->num_rows>0) { 
           while($row = $result->fetch_assoc()){
               $from=$row["de"];
                $sql="select keyid,ifnull((select nombre from canal.usuarios where user='".$from."'),'Servidor') as  nombre from wsocket.usuarios where app='".$k[2]."' and usr='".$k[1]."'";
				$result1=$mysqli->query($sql);
		    	if ($result1->num_rows>0) {
					while($row1 = $result1->fetch_assoc()){
					    //si esta conectado se envia  la notificacion
					    $id=$row1["keyid"];
                        $from1=$row1["nombre"];
					    $Server->wsSend($id, "MSG|".$row["mensage"]."|".$from1."|".$row["fecha"]);
                    }
                }
           }
        }
        
        
	}
	if ($k[0]=="MSG") {
		$mysqli = new mysqli('187.157.170.83','samtec','sam33','canal');
		$sql="select usr,app from wsocket.usuarios where keyid='".$clientID."'";
		$result=$mysqli->query($sql);
		if ($result->num_rows>0) {
			$row = $result->fetch_assoc();
			$from=$row["usr"];
			$app=$row["app"];
			if ($k[3]=='U'){
				$sql="select usr,app,keyid,(select nombre from canal.usuarios where user='".$from."') as  nombre, now() as fecha from wsocket.usuarios where app='".$app."' and usr='".$k[2]."'";
///				$Server->log($sql);
				$result1=$mysqli->query($sql);
				if ($result1->num_rows>0) {
                    $stt="E";
					while($row1 = $result1->fetch_assoc()){
					//si esta conectado
					//se envia  la notificacion
					$id=$row1["keyid"];
                    $from1=$row1["nombre"];
					$Server->wsSend($id, "MSG|".$k[1]."|".$from1."|".$row1["fecha"]);
                    }
				} else {
					$stt="P";
				}
                // almacenamos el mensaje
			    $sql="insert into wsocket.mensaje (fecha, de, para,status, mensage, app) values ";
			    $sql.="(now(), '".$from."', '".$k[2]."','".$stt."', '".$k[1]."', '".$app."')";
                
                $Server->log($sql);
			    $result=$mysqli->query($sql);
			    $mysqli->close();


			}
            // grupo
            if ($k[3]=='G'){
                $mysqli = new mysqli('187.157.170.83','samtec','sam33','canal');
                $gr=explode(",",base64_decode($k[2]));
                foreach ($gr as &$grupo) {
                    $sql="select cadena from wsocket.grupos where grupo='".$grupo."'";
                    $Server->log($sql);
                    $result1=$mysqli->query($sql);
				    if ($result1->num_rows>0) {
                        $row1 = $result1->fetch_assoc();
                        $cd=$row1["cadena"];
                        $us=explode("|",$cd);
                        foreach ($us as &$user) {
                           // $mysqli = new mysqli('187.157.170.83','samtec','sam33','canal');
                            $sql="select usr,app,keyid,now() as fecha from wsocket.usuarios where app='".$app."' and usr='".$user."'";
                            $Server->log($sql);
				            $result2=$mysqli->query($sql);
                            
				            if ($result2->num_rows>0) {
                                $stt="E";
					            while($row2 = $result2->fetch_assoc()){
					                //si esta conectado
					                //se envia  la notificacion
					                $id=$row2["keyid"];
                                    $from1="Server";
                                    $men=base64_decode($k[1]);
                                    $m=explode("|",$men);
                                    if (count($m)>1){
                                        $tit=$m[0];
                                        $men=$m[1];
                                    } else {
                                        $tit="Sin Asunto";
                                        $men=$m[0];
                                    }
					                $Server->wsSend($id, "MSG|".$tit."|".$from1."|".$row2["fecha"]);
                                }
				            } else {
					        $stt="P";
				            }
                            // almacenamos el mensaje
                           // $mysqli = new mysqli('187.157.170.83','samtec','sam33','canal');
			                $sql="insert into wsocket.mensaje (fecha, de, para,status, mensage, app) values ";
			                $sql.="(now(), 'Servidor', '".$user."','".$stt."', '".$k[1]."', '".$app."')";
                            $Server->log($sql);
			                $result=$mysqli->query($sql);
			                //$mysqli->close();
                        }
                    }
                }
			}
		}
	}
  
	//The speaker is the only person in the room. Don't let them feel lonely.
	//if ( sizeof($Server->wsClients) == 1 ){
	//	print_r($message);

	//	$Server->wsSend($clientID, "There isn't anyone else in the room, but I'll still listen to you. --Your Trusty Server");
	//}else{
	//	//Send the message to everyone but the person who said it
	//	foreach ( $Server->wsClients as $id => $client )
	//		if ( $id != $clientID )
	//			$Server->wsSend($id, "Visitor $clientID ($ip) said \"$message\"");
	//}
}

// when a client connects
function wsOnOpen($clientID)
{
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	$Server->log( "$ip ($clientID) has connected." );

	//Send a join notice to everyone but the person who joined
}

// when a client closes or lost connection
function wsOnClose($clientID, $status) {
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	$Server->log( "$ip ($clientID) has disconnected." );

        $mysqli = new mysqli('187.157.170.83','samtec','sam33','canal');
		$sql="delete from wsocket.usuarios where keyid='".$clientID."'";
    	$result=$mysqli->query($sql);
    	$mysqli->close();
	//Send a user left notice to everyone in the room
	//foreach ( $Server->wsClients as $id => $client )
	//	$Server->wsSend($id, "Visitor $clientID ($ip) has left the room.");
}

// start the server
$Server = new PHPWebSocket();
$Server->bind('message', 'wsOnMessage');
$Server->bind('open', 'wsOnOpen');
$Server->bind('close', 'wsOnClose');
// for other computers to connect, you will probably need to change this to your LAN IP or external IP,
// alternatively use: gethostbyaddr(gethostbyname($_SERVER['SERVER_NAME']))
$Server->wsStartServer('0.0.0.0', 9000);

?>