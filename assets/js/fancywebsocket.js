// JavaScript Document
// conectaws funcion de recepcion, app,usuario, nombre, codigo

var wsuser="";
var wsapp="";
var serverUrl = 'https://admyo.com:9000';
var socket = new WebSocket(serverUrl);
var wsfunc="";
var Nolista=0;
var tlista="";

function ping(){
   try {
        socket.send("P");
        console.log("ping");
        myVar = setTimeout(ping, 600000);
   } catch(err) {
        console.log("sin Conexion");
   }
}

function conectaws(app1,user1,funciona) {
    console.log("Iniciando WS");
    wsuser=user1;
    wsapp=app1;
    wsfunc=funciona;
    socket.binaryType = 'blob';
    socket = new WebSocket(serverUrl);
        
        
    socket.onopen = function(msg) {
        console.log("Ingresando WebSocket");
        register_user();
        
        return true;
    };
    socket.onmessage = function(msg) {
        console.log ("-->"+msg.data);
        l=msg.data;
        a=l.split("|");
        funcws=wsfunc+"('"+a[0]+"','"+a[1]+"','"+a[2]+"');";
        console.log(funcws);
        eval(funcws);
        return true;
    };
    socket.onclose = function(msg) {
        console.log("Disconnected");
        setTimeout(function(){conectaws(wsapp,wsuser,wsfunc); },5000); 
        return true;
    };
    function register_user(){
        socket.send("REG|"+wsapp+"|"+wsuser);
        ping ();
    }
}
// Texto, para, tipo U usuario G grupo , "MSG=mensaje PSH = Push"
function sendws(txt){
       console.log("->>>>>"+txt);
    
        socket.send(txt);
};
function activalista(lista){
	if(Nolista!=lista.length){
		var cade="";
		lista.forEach((lista)=>{
			if(lista.app!="cabina"){
				cade+="<li><a href='#' llc='mes' key='"+lista.keyid+"'>"+lista.usr+"</a></li>"
			}
			console.log($("#page-content-wrapper div[key='"+lista.keyid+"']").length);
			if ($("#page-content-wrapper .contenedor[llc='"+lista.keyid+"']").length == 0 && lista.app!=="cabina" ) {
				$("#page-content-wrapper").append('<div class="contenedor" llc="'+lista.keyid+'"><div class="container-fluid listmsg"><div class="row list_mensjes"></div></div><div class="container-fluid text-msg"><div class="row"><div class="col-10"><input type="text" llt="'+lista.keyid+'" class="form-control"></div><div class="col-2"><button llp="enviarmsg" llc="'+lista.keyid+'" class="btn btn-primary">Enviar</button></div></div></div></div>')
			}

		});	
		Nolista=lista.length
	}

	
	$(".sidebar-nav").html(cade);
}
function regresa(a){
	var datos=JSON.parse(a);
	if(datos.funcion==="LSIT"){
		activalista(datos.mensaje)
		if(datos.delete!=""){
			$("#page-content-wrapper .contenedor[llc='"+datos.delete+"']").remove();
		}
	}
	if(datos.funcion==="MSG"){
		if(!$("ul.sidebar-nav a[key='"+datos.key+"']").hasClass("active")){
				$("ul.sidebar-nav a[key='"+datos.key+"']").addClass("new");
		}
		var cadena='<div class="col-12">';
        	cadena+='<div class="card">'
        	cadena+='<div class="card-body">'
        	cadena+='<h5>'+datos.nombre+':</h5>'
        	cadena+='<br><p>'+datos.mensaje+'</p></div></div></div>'
        	console.log(cadena);
		$("#page-content-wrapper .contenedor[llc='"+datos.key+"'] .listmsg .list_mensjes").append(cadena);
		$(".list_mensjes").animate({ scrollTop: $('.list_mensjes')[0].scrollHeight}, 1000);
		console.log(datos.mensaje);
	}
console.log( datos)
}