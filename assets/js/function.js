class Help{
	Constructor(){

	}
	mensaje(mensaje){
		console.log(mensaje);
	}
	send(texto,id){
		var cadena='<div class="col-12">';
        	cadena+='<div class="card">'
        	cadena+='<div class="card-body">'
        	cadena+='<h5>Soporte Tecnico:</h5>'
        	cadena+='<br><p>'+texto+'</p></div></div></div>'
		sendws("MSG|"+texto+"|"+id)
		console.log(id);
		$("#page-content-wrapper .contenedor[llc='"+id+"'] .listmsg .list_mensjes").append(cadena);

		$(".list_mensjes").animate({ scrollTop: $('.list_mensjes')[0].scrollHeight}, 1000);
	}
	muestra_cont(div){
		$(".contenedor").removeClass("active")
		$("#page-content-wrapper div[llc='"+div+"']").addClass("active")
	}
}
let help=new Help();
$(function(){
	conectaws("Soporte Tecnico","cabina","regresa");
})
$(document).on("click","#sidebar-wrapper a[llc='mes']",function(){
	$("#sidebar-wrapper a[llc='mes']").removeClass("active");
	$(this).removeClass("new");
	$(this).addClass("active");
	console.log($(this).attr("key"))
	help.muestra_cont($(this).attr("key"));
})
$(document).on("click","button[llp='enviarmsg']",function(){
	var mensaje=$("input[llt='"+$(this).attr("llc")+"']").val();
	help.send(mensaje,$(this).attr("llc"));
	$("input[llt='"+$(this).attr("llc")+"']").val("");
})
