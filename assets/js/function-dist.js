"use strict";function _classCallCheck(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var _createClass=function(){function a(e,t){for(var n=0;n<t.length;n++){var a=t[n];a.enumerable=a.enumerable||!1,a.configurable=!0,"value"in a&&(a.writable=!0),Object.defineProperty(e,a.key,a)}}return function(e,t,n){return t&&a(e.prototype,t),n&&a(e,n),e}}(),Help=function(){function e(){_classCallCheck(this,e)}return _createClass(e,[{key:"Constructor",value:function(){}},{key:"mensaje",value:function(e){console.log(e)}},{key:"send",value:function(e,t){var n='<div class="col-12">';n+='<div class="card">',n+='<div class="card-body">',n+="<h5>Soporte Tecnico:</h5>",n+="<br><p>"+e+"</p></div></div></div>",sendws("MSG|"+e+"|"+t),console.log(t),$("#page-content-wrapper .contenedor[llc='"+t+"'] .listmsg .list_mensjes").append(n),$(".list_mensjes").animate({scrollTop:$(".list_mensjes")[0].scrollHeight},1e3)}},{key:"muestra_cont",value:function(e){$(".contenedor").removeClass("active"),$("#page-content-wrapper div[llc='"+e+"']").addClass("active")}}]),e}(),help=new Help;$(function(){conectaws("Soporte Tecnico","cabina","regresa")}),$(document).on("click","#sidebar-wrapper a[llc='mes']",function(){$("#sidebar-wrapper a[llc='mes']").removeClass("active"),$(this).removeClass("new"),$(this).addClass("active"),console.log($(this).attr("key")),help.muestra_cont($(this).attr("key"))}),$(document).on("click","button[llp='enviarmsg']",function(){var e=$("input[llt='"+$(this).attr("llc")+"']").val();help.send(e,$(this).attr("llc")),$("input[llt='"+$(this).attr("llc")+"']").val("")});