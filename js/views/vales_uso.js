// JavaScript Document
$(document).ready(function(){
	var tiempo = new Date();
	newfec=new Date(tiempo.getFullYear(), tiempo.getMonth(), tiempo.getDate(), tiempo.getHours(), tiempo.getMinutes());
	
	$('#wizard').smartWizard();
	
	$("#seccion").validacion({
    men: "Campo no puede ir vacio"
	});

});
