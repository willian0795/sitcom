// JavaScript Document
$(document).ready(function(){
	var tiempo = new Date();
	newfec=new Date(tiempo.getFullYear(), tiempo.getMonth(), tiempo.getDate(), tiempo.getHours(), tiempo.getMinutes());
	
	$('#wizard').smartWizard();
	
	$("#cantidad_solicitada").validacion({
		numMin: 0,
		ent: true
	});

	$("#justificacion").validacion({
		lonMin: 10
	});

	$("#id_fuente_fondo").validacion({
		men: "Debe seleccionar un item"
	});
	
	$("#mes").validacion({
        men: "Debe seleccionar un item"
	});

	$("#tipo_gas").validacion({
        men: "Debe seleccionar un item"
	});

	$("#numero_factura").validacion({
        numMin:1,
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


	$("#id_fuente_fondo").change(function () {
		
		if ($("#cantidad_solicitada").val() > 0) {
			$.ajax({
				url: base_url()+"/index.php/vales/obtener_numeracion_vales",
				type: "post",
				dataType: "json",
				data: {cantidad: $("#cantidad_solicitada").val(), fuente: $("#id_fuente_fondo").val()},
			})
			.done(function(res){
				result = res;
	
				$("#numero_inicial").val(result.inicial);
				$("#numero_final").val(result.final);
				$("#lbl").html(result.inicial);
				$("#lb2").html(result.final);
				$("#vale").val(result.id_vale);
				$("#restante").val(result.restante);
			});
		}
		
	});

	$("#cantidad_solicitada").change(function () {
		if ($("#id_fuente_fondo").val() > 0) {
			$.ajax({
				url: base_url()+"/index.php/vales/obtener_numeracion_vales",
				type: "post",
				dataType: "json",
				data: {cantidad: $("#cantidad_solicitada").val(), fuente: $("#id_fuente_fondo").val()},
			})
			.done(function(res){
				result = res;
	
				$("#numero_inicial").val(result.inicial);
				$("#numero_final").val(result.final);
				$("#lbl").html(result.inicial);
				$("#lb2").html(result.final);
				$("#vale").val(result.id_vale);
				$("#restante").val(result.restante);

			});
		}
	});

});
