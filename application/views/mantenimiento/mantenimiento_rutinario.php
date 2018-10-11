<?php
extract($vehiculos);
?>
<section>
    <h2>Reparación y Mantenimiento Rutinario</h2>
</section>
<form name="form_taller" method="post" action="<?php echo base_url()?>index.php/vehiculo/guardar_mtto_rutinario" >
<input type="text" name="fecha_max" id="fecha_max" value="<?php echo date('d-m-Y') ?>" style="display:none" />
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
                        <small>&nbsp;Artículos usados</small>
                    </span>
                </a>
            </li>
        </ul>
        <div id="step-1">	
            <h2 class="StepTitle">Datos generales del vehículo</h2>
            <p>
                <label id="lfecha" for="fecha">Fecha de mantenimiento </label>
                <input type="text" name="fecha" id="fecha" />
                
                <label id="lid_empleado_repara" for="id_empleado_repara">Mecánico que revisó el vehículo </label>
                <select style="width:230px" class="select" name="id_empleado_repara" id="id_empleado_repara" placeholder="Seleccione..." multiple="multiple">
                	<?php foreach($mecanicos as $m){?>
                    <option value="<?php echo $m['id_empleado']; ?>"><?php echo ucwords($m['nombre']) ?></option>
                    <?php }?>
                </select>
            </p>
            <p>
            	<input type="hidden" name="id_vehiculo" value="<?php echo $id_vehiculo ?>" />
            	<label id="ltrabajo_realizado" for="trabajo_realizado" class="label_textarea">Trabajo realizado </label>
                <textarea style="width:200px; resize:none;" name="trabajo_realizado" id="trabajo_realizado"></textarea>
            </p>
            <p>
                <label style="width:150px">Número de placa: </label>
                <input type="hidden" name="placa" value="<?php echo $placa ?>" />
                <strong><?php echo $placa ?></strong>
            </p>
            
            <p>
            <table align='center' class='table_design' cellspacing='0' cellpadding='0'>
            <thead>
            	<tr>
                	<th colspan="2">Datos Generales del Vehículo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                	<td>Marca: <strong><?php echo $marca ?></strong></td>
                    <td>Motorista Asignado: <strong><?php echo ucwords($motorista); ?></strong></td>
                </tr>
                <tr>
                    <td>Modelo: <strong><?php echo $modelo ?></strong></td>
                    <td>Oficina Asiganada: <strong><?php echo $seccion ?></strong></td>
                </tr>
                <tr>
                    <td>Clase: <strong><?php echo $clase ?></strong></td>
                    <td>Kilometraje Actual: <strong><?php echo $kilometraje ?>km</strong></td>
                </tr>
                <tr>
                    <td>Año: <strong><?php echo $anio ?></strong></td>
                    <td>Tipo de Combustible: <strong><?php echo $tipo_combustible?></strong></td>
                </tr>
            </tbody>
            </table>
            </p>
        </div>
        <div id="step-2">	
            <h2 class="StepTitle">Artículos que se usaron durante el mantenimiento</h2>
             <p>
                <table align="center" class="table_design" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                    	<th>Nombre</th>
                        <th>Descripción</th>
                        <th>Cantidad Disponible</th>
                        <th>Utilizado</th>
                        <th>Cantidad Utilizada</th>
                    </tr>
                </thead>
                <tbody>
                	<?php
						foreach($inventario as $inv)
						{
							if($inv['cantidad']>0)
							{
								echo "<tr>";
								echo "<td>".$inv['nombre']."</td>";
								echo "<td>".$inv['descripcion']."</td>";
								echo "<td align='right'>".$inv['cantidad']." ".$inv['unidad_medida']."</td>";
								echo "<td><input type='checkbox' value='".$inv['id_articulo']."' name='id_articulo[]' onclick='habilitar(".$inv['id_articulo'].",this.checked)'></td>";
								echo "<td><input type='text' name='cant_usada[]' id='".$inv['id_articulo']."' disabled='disabled' size='1px'></td>";
								echo "<input type='hidden' value='".$inv['cantidad']."' id='c_".$inv['id_articulo']."' >";
								echo "</tr>";
							}
						}
					?>
                </tbody>
                </table>
            </p>
         </div>
    </div>
</form>
<script>
$(document).ready(function(){
	$('#wizard').smartWizard();
	
	$('#id_empleado_repara').validacion({
		req:true
	});
	$('#trabajo_realizado').validacion({
		req:true,
		lonMin:5
	});
	var fecha = $("#fecha").kendoDatePicker({
		format: "dd-MM-yyyy"		 
	}).data("kendoDatePicker");
	
	var fecha_max = $("#fecha_max").kendoDatePicker({
		format: "dd-MM-yyyy"		 
	}).data("kendoDatePicker");
	
	fecha.max(fecha_max.value());
	
	$('#fecha').validacion({
		req:true,
		valFecha:true
	});
});

function art_info(id)
{
		$('#articulo_info').html("");
		var  art = "<?php echo base_url()?>index.php/vehiculo/art_info/"+id;
		console.log(art);
		$.ajax({
			async:	true, 
			url:	art,
			dataType:"json",
			success: function(data) {
				console.log(data);
				json = data;
				var cont="Cantidad Disponible: <strong>"+json[0]['cantidad']+"</strong><br>";
				cont=cont+"Descripción: <strong>"+json[0]['descripcion']+"</strong><br>";
				$('#articulo_info').html(cont);
			},
			error:function(data) {
				 alertify.alert('Error al cargar la información del artículos');
			}
		})
}

function habilitar(id,chk)
{
	var tf = document.getElementById(id);
	var max_cant = parseFloat(document.getElementById('c_'+id).value);
	var defase= parseFloat(0.01);
	var aux = max_cant;
	max_cant=parseFloat(max_cant+defase);
	
	if(chk==true)
	{
		tf.disabled=false;
		$('#'+id).validacion({
			req: true,
			num: true,
			numMax: max_cant,
			numMin: 0,
			men: "La cantidad debe ser mayor que 0 y menor o igual que "+aux
		});
	}
	else
	{
		tf.value="";
		$('#'+id).validacion({
			req: false
		});
		tf.disabled=true;
	}
}

</script>