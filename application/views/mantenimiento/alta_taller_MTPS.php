<?php extract($vehiculo) ?>
<section>
    <h2>Dar de Alta a Vehículo</h2>
</section>
<form name="form_alta_taller_MTPS" method="post" action="<?php echo base_url()?>index.php/vehiculo/dar_alta_taller_MTPS" >
<input type="text" name="fecha_max" id="fecha_max" value="<?php echo date('d-m-Y'); ?>" style="display:none" />
<input type="text" name="fecha_min" id="fecha_min" value="<?php if($fecha_min!="") echo $fecha_min; else echo $fecha_recepcion; ?>" style="display:none" />
	<div id="wizard" class="swMain">
        <ul>
            <li>
                <a href="#step-1">
                    <span class="stepNumber">1<small>er</small></span>
                    <span class="stepDesc">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paso<br/>
                        <small>&nbsp;Información del vehículo</small>
                    </span>
                </a>
            </li>
            <li>
                <a href="#step-2">
                    <span class="stepNumber">2<small>do</small></span>
                    <span class="stepDesc">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paso<br/>
                        <small>&nbsp;Entrega del vehículo</small>
                    </span>
                </a>
            </li>
        </ul>
        <div id="step-1">	
           <h2 class="StepTitle">Información de ingreso del vehículo al taller interno</h2>
            <p>
                <label id="lfecha_entrega" for="fecha_entrega">Fecha de entrega </label>
                <input type="text" name="fecha_entrega" id="fecha_entrega" />
            </p>
            <p>
            	<label class="label_textarea">Trabajo solicitado </label>
                <textarea style="width:200px; resize:none;" name="trabajo_solicitado" disabled="disabled"><?php echo $trabajo_solicitado; ?></textarea>
                <label class="label_textarea">Trabajo solicitado en la carrocería </label>
                <textarea style="width:200px; resize:none;" name="trabajo_solicitado_carroceria" disabled="disabled"><?php echo $trabajo_solicitado_carroceria; ?></textarea>
            </p>
            <p>
            	<input type="hidden" name="id_vehiculo" value="<?php echo $id_vehiculo; ?>" />
                <input type="hidden" name="placa" value="<?php echo $placa; ?>" />
                <input type="hidden" name="id_ingreso_taller" value="<?php echo $id_ingreso_taller; ?>" />
                <label style="width:150px">Número de placa </label>
                <strong><?php echo $placa; ?></strong>
                <label style="width:200px" >Fecha de ingreso al taller interno </label>
                <strong><?php echo $fecha_recepcion; ?></strong>
            <div id="info_vehiculo">
            </div>
            </p>
        </div>
        <div id="step-2">	
           <h2 class="StepTitle">Información de entrega del vehículo</h2>
           <p>
            	<label style="width:100px" id="lid_motorista_recibe" for="id_motorista_recibe">Motorista o persona que recibe el vehículo </label>
                <select style="width:300px" class="select" name="id_motorista_recibe" id="id_motorista_recibe" placeholder="Seleccione..." multiple="multiple">
                	<?php foreach($motoristas as $m){ 
					if($m->id_empleado==$id_empleado) $selected='selected="selected"';
					else $selected="";
					?>
                    <option value="<?php echo $m->id_empleado; ?>" <?php echo $selected ?>><?php echo ucwords($m->nombre) ?></option>
                    <?php }?>
                </select>
            </p>
            <p>
            	<label class="label_textarea" id="lnotas" for="notas" style="width:100px">Notas </label>
                <textarea style="width:200px; resize:none;" name="notas" id="notas"></textarea>
            </p>
        </div>
    </div>
</form>
<script type="text/javascript">
$(document).ready(function(){
	$('#wizard').smartWizard();
	
	var fecha = $("#fecha_entrega").kendoDatePicker({
		format: "dd-MM-yyyy"		 
	}).data("kendoDatePicker");
	
	var fecha_max = $("#fecha_max").kendoDatePicker({
		format: "dd-MM-yyyy"		 
	}).data("kendoDatePicker");
	
	var fecha_min = $("#fecha_min").kendoDatePicker({
		format: "dd-MM-yyyy"		 
	}).data("kendoDatePicker");
	
	fecha.max(fecha_max.value());
	fecha.min(fecha_min.value());
	
	$('#fecha_entrega').validacion({
		req:true,
		valFecha:true
	});
	
 	$('#id_motorista_recibe').validacion({
		req:true
	});
	
	$('#notas').validacion({
		req:false,
		lonMin:5
	});
});

function cargar(id)
	{
		$('#info_vehiculo').html("");
		var  dur = "<?php echo base_url()?>index.php/vehiculo/vehiculo_info/"+id+"/2";
		console.log(dur);
		$.ajax({
			async:	true, 
			url:	dur,
			dataType:"json",
			success: function(data) {
				console.log(data);
				json = data;
				var cont="<table align='center' cellspacing='0' cellpadding='0' class='table_design'>";
				cont=cont+"<thead><tr><th colspan='2'>Datos Generales del Vehículo</th></tr></thead>";
				cont=cont+"<tr><td>Marca: <strong>"+json[0]['marca']+"</strong></td><td>Motorista Asignado: <strong>"+json[0]['motorista'].capitalize()+"</strong></td>";
				cont=cont+'</tr><tr>'
				cont=cont+'<td>Modelo: <strong>'+json[0]['modelo']+'</strong></td> <td>Oficina Asiganada: <strong>'+json[0]['seccion']+'</strong></td>';
				cont=cont+'</tr><tr>'
				cont=cont+'<td>Clase: <strong>'+json[0]['clase']+'</strong></td><td>Kilometraje Actual: <strong>'+json[0]['kilometraje']+' km</strong></td>';
				cont=cont+'</tr><tr>'
				cont=cont+'<td>Año: <strong>'+json[0]['anio']+'</strong></td><td>Tipo de Combustible: <strong>'+json[0]['tipo_combustible']+'</strong></td>';
				cont=cont+'</tr>'
				cont=cont+'</table>';
				$('#info_vehiculo').html(cont);
			},
			error:function(data) {
				 alertify.alert('Error al cargar los datos de los vehiculos');
			}
		})
		
	}
</script>
<script>
	var id_v=<?php echo $id_vehiculo; ?>;
	cargar(id_v);
</script>