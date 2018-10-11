function info(id,val){
$.ajax({
		async:	true, 
		url:	base_url()+"/index.php/transporte/infoSolicitud/"+id,
		dataType:"json",
		success: function(data){
			
			var id_vehiculo=data[0].id_vehiculo;
			
					$.ajax({
						async:	true, 
						url:	base_url()+"index.php/transporte/kilometraje/"+id_vehiculo,
						dataType:"json",
						success: function(date){
							
							if(val==4){
							document.getElementById('kmi').value=date[0].KMinicial;
									}else{
							document.getElementById('kmi').value=date[0].KMFinal;
									}
							datos(data);
							}
					});				

			},
		error:function(data){
			 alertify.alert('Error al cargar datos');
			console.log(data);
			}
		});	
$('#divAccesorios').load(base_url()+"index.php/transporte/accesoriosABordo/"+id+"/"+val);
console.log('cargado');
}

function dialogo(id, val){ //carga la informacion si la 

	$('#id').val(id);
	$('#estado').val(val);


info(id,val);	
}

function datos(data){ //carga informacion en el cuadro de dialogo
	var id=  document.getElementById('id').value;
	var minKM=document.getElementById('kmi').value;
		 	/* echo1="Solicitud numero:<strong>"+id+"</strong><br />"+*/
            var echo1=" Solicitante:<strong> "+data[0].nombre.capitalize()+"</strong><br />"+
                    "Hora de Salida:<strong> "+data[0].salida+"</strong><br />"+
                    "Hora de Regreso:<strong> "+data[0].regreso+"</strong><br />"+
                   " Vehiculo:<strong> "+data[0].modelo+"</strong><br />"+
                    "Placa:<strong> "+data[0].placa+"</strong><br />"+
					 "Kilometraje Recorrido:<strong> "+ minKM+" Kms</strong>";
			 
			 		document.getElementById('gas').value=data[0].gas;
			 		tanque(data[0].gas	);
					document.getElementById('InfoMision').innerHTML=echo1;
					$('#km').destruirValidacion();
					$('#km').validacion({
						req: true,
						numMin: (minKM-0.01)
						});
					
			
	}





/*Funciones para el medidor*/

function tanque(val){  //desde aqui se llma los demas
      	demoGauge.set(parseInt(val));
     	AnimationUpdater.run();	
}
	

 
function update() { //funcion para que se actualisce el valor <.ya estaba hecha.>

    demoGauge.ctx.clearRect(0, 0, demoGauge.ctx.canvas.width, demoGauge.ctx.canvas.height);
    demoGauge.render(); 
}
  
  function initGauge(){ //constructor del medidor

    demoGauge = new Gauge(document.getElementById("canvas-preview"));
    demoGauge.setTextField(document.getElementById("preview-textfield"));
    demoGauge.maxValue = 100;
    demoGauge.set(50);
  };
 
  $(function() {
    var params = {};
    $('.opts input[min], #opts .color').each(function() {
      var val = params[this.name];
      if (val !== undefined) this.value = val;
      this.onchange = update;
    });
 
    initGauge();
    update();
    
  });
  
  
  
 $( "#datos" ).submit(function( event ) { //validaciones antes de enviar el formulario
	//	alertify.alert("obligado");
		
  event.preventDefault();
});

function chekear(k){
var obj = $('.cheke');
var ban= $(k).prop('checked');

	for(i=0;i<obj.length;i++){
		obj[i].checked=ban;
	}
}

 
