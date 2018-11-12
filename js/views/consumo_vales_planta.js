// JavaScript Document
$(document).ready(function(){
	var tiempo = new Date();
	newfec=new Date(tiempo.getFullYear(), tiempo.getMonth(), tiempo.getDate(), tiempo.getHours(), tiempo.getMinutes());
	
	$('#wizard').smartWizard();

	$("#id_seccion").validacion({
	men: "Debe seleccionar un item"
	});

	$("#tipo_gas").validacion({
        men: "Debe seleccionar un item"
	});

	$("#numero_factura").validacion({
        numMin:1,
	ent: true
	});

	// $("#valor").validacion({
	// numMin: 1,
	// ent: true
	// });

	$("#cantidad").validacion({
	numMin: 1,
	ent: true
	});

	var fec_soli=$("#fecha_factura").kendoDatePicker({
		culture: "es-SV",
		format: "dd/MM/yyyy",
		max: newfec
	}).data("kendoDatePicker");

	$("#fecha_factura").validacion({
        valFecha: true
	});

	$("#id_gasolinera").validacion({
        men: "Debe seleccionar un item"
	});

});
