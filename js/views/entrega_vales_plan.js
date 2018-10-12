// JavaScript Document
$(document).ready(function(){
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

	$("#id_fuente_fondo").change(function () {
		console.log($(this).val());
		$.ajax({
			url: base_url()+"/index.php/vales/obtener_numeracion_vales",
			type: "post",
			dataType: "json",
			data: {cantidad: $("#cantidad_solicitada").val(), fuente: $("#id_fuente_fondo").val()},
		})
		.done(function(res){
			result = res[0];


			$("#numero_inicial").val(result.inicial);
			$("#numero_final").val(result.final);
			$("#lbl").html(result.inicial);
      		$("#lb2").html(result.final);
		});
	});

});


function info(id1,id2, data2){
$.ajax({
        async:  true, 
        url:    base_url()+"/index.php/vales/consultar_consumo/"+id1+"/"+id2,
        dataType:"json",
        success: function(data){
        	console.log(data);
        		$('#restante').val(data.restante);
	        	if(data.asignado!=null){
	        		$('#lbl2').html(data2.asignado);
	        		$('#lbl3').html(data.restante);

	        	}else{
	        		$('#lbl2').html("(No tiene ninguna asignación)");
	        		$('#lbl3').html("(No tiene ninguna asignación)");
	        	}
			    
			    
			    if(document.getElementById('refuerzo').value!=0) {  

				    $("#cantidad_solicitada").prop('readonly', false);
				    

					///Lo maximo que se puede pedir es la misma cantidad asignada

			    	$('#justificacion').val("");

			    }else{

	//			    $("#cantidad_solicitada").prop('readonly', true); //temporalmente habilitado
					$("#cantidad_solicitada").val(data2.peticion);
				    var txt = $("#id_seccion option:selected").text();

				    if(txt=="") {txt=document.getElementById('nombre').value;}
				    $('#justificacion').val("CUOTA MENSUAL ASIGNADA A "+ txt);
				   

				    }
		   	$("#cantidad_solicitada").destruirValidacion();					
		    $('#cantidad_solicitada').validacion({    
					req: true,
					numMin: 0,
					numMax: Number(data2.peticion) +1
					});


            },
        error:function(data){
             alertify.alert('Error al cargar datos');
            console.log(data);
            }
        }); 
}



