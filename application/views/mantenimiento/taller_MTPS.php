<?php
extract($vehiculos);
?>
<section>
    <h2>Reparación y Mantenimiento en Taller MTPS</h2>
</section>
<form name="form_taller" method="post" action="<?php echo base_url()?>index.php/vehiculo/guardar_taller" >
<input type="text" name="fecha_max" id="fecha_max" value="<?php echo date('d-m-Y') ?>" style="display:none" />
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
                        <small>&nbsp;Mantenimiento realizado</small>
                    </span>
                </a>
            </li>
            <li>
                <a href="#step-3">
                    <span class="stepNumber">3<small>er</small></span>
                    <span class="stepDesc">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paso<br/>
                        <small>&nbsp;Inspección realizada</small>
                    </span>
                </a>
            </li>
            <li>
                <a href="#step-4">
                    <span class="stepNumber">4<small>to</small></span>
                    <span class="stepDesc">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paso<br/>
                        <small>&nbsp;Artículos usados</small>
                    </span>
                </a>
            </li>
        </ul>
        <div id="step-1">	
            <h2 class="StepTitle">Información de ingreso del vehículo al taller interno</h2>
            <p>
                <label id="lfecha" for="fecha">Fecha de realización del mantenimiento </label>
                <input type="text" name="fecha" id="fecha" />
            	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            	<label id="lid_empleado_mtto" for="id_empleado_mtto">Mecánico que realizó el mantenimiento al vehículo </label>
                <select style="width:300px" class="select" name="id_empleado_mtto" id="id_empleado_mtto" placeholder="Seleccione...">
                	<?php foreach($mecanicos as $m){?>
                    <option value="<?php echo $m['id_empleado']; ?>"><?php echo ucwords($m['nombre']) ?></option>
                    <?php }?>
                </select>
            </p>
            <p>
             	<input type="hidden" name="id_ingreso_taller" value="<?php echo $id_ingreso_taller ?>" />
            	<label class="label_textarea">Trabajo solicitado </label>
                <textarea style="width:200px; resize:none;" name="trabajo_solicitado" disabled><?php echo $trabajo_solicitado; ?></textarea>
                <label class="label_textarea" >Trabajo solicitado en carrocería </label>
                <textarea style="width:200px; resize:none;" name="trabajo_solicitado_carroceria" rows="2" disabled><?php echo $trabajo_solicitado_carroceria; ?></textarea>
            </p>
            <p>
            	<label style="width:150px">Número de placa </label>
                 <input type="hidden" name="id_vehiculo" value="<?php echo $id_vehiculo ?>" />
                <input type="hidden" name="placa" value="<?php echo $placa ?>" />
                <strong><?php echo $placa ?></strong>
                <label style="width:200px" >Fecha de ingreso al taller interno </label>
                <strong><?php echo $fecha_recepcion; ?></strong>
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
            <h2 class="StepTitle">Información del mantenimiento realizado al vehículo</h2>
            <table width="500px" align="center">
            <tr><td>
            <table align="center" class="table_design" cellspacing="0">
            	<thead>
                	<tr>
                    	<th>Mantenimiento</th>
                        <th width="250">Realizado</th>
                    </tr>
                </thead>
            	<tbody>
                <?php
					foreach($reparacion as $re)
					{
						if($re['tipo']=="mantenimiento")
						{
							echo "<tr>";
							echo "<td>".$re['reparacion']."</td>";
							echo "<td><input type='checkbox' name='reparacion1[]' value='".$re['id_reparacion']."' /></td>";
							echo "</tr>";
						}
					}
                ?>
               
                <tr>
                	<td colspan="2">Seleccionar/Deseleccionar Todo: <input type="checkbox" name="selectall1" onclick="select_all(this.checked)" /></td>
                </tr>
                <tr>
                	<td>Otro mantenimiento </td>
                    <td><textarea style="width:200px; resize:none;"  name="otro_mtto" id="otro_mtto"></textarea></td>
                </tr>
               </tbody>
            </table></td></tr></table>
        </div>
        <div id="step-3">	
            <h2 class="StepTitle">Información de inspección o chequeo realizado al vehículo</h2>
             <table width="600px" align="center">
            <tr><td>
            <table align="center" class="table_design" cellspacing="0">
            	<thead>
                	<tr>
                    	<th>Inspección/Chequeo</th>
                        <th width="250px">Realizado</th>
                    </tr>
                </thead>
            	<tbody>
               <?php
					foreach($reparacion as $re)
					{
						if($re['tipo']=="inspeccion")
						{
							echo "<tr>";
							echo "<td>".$re['reparacion']."</td>";
							echo "<td><input type='checkbox' name='reparacion2[]'  value='".$re['id_reparacion']."'></td>";
							echo "</tr>";
						}
					}
                ?>
                <tr>
                	<td colspan="2">Seleccionar/Deseleccionar Todo: <input type="checkbox" name="selectall2" onclick="select_all2(this.checked)" /></td>
                </tr>
                <tr>
                	<td>Observaciones </td>
                    <td><textarea style="width:200px; resize:none;"  name="observaciones" id="observaciones"></textarea></td>
                </tr>
              </tbody>
            </table>
          </td>
         </tr>
        </table>
        </div>
         <div id="step-4">	
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
var band=false;

$(document).ready(function(){
	$('#wizard').smartWizard({
		onFinish: validar
	});
	
	var fecha = $("#fecha").kendoDatePicker({
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
	
	$('#fecha').validacion({
		valFecha:true
	});
	$('#id_empleado_mtto').validacion({
		req: true
	});
	
	function validar (objs, context)
	{
		var r1 = $("[name='reparacion1[]']:checked").length;
		var r2 = $("[name='reparacion2[]']:checked").length;
		var otro_mtto = document.getElementById('otro_mtto').value;
		var observaciones = document.getElementById('observaciones').value;
		
		if(r1==0 && r2==0 && otro_mtto=="" && observaciones=="")
		{
			band=false;
			alertify.alert('Error, no ha seleccionado o descrito ningún mantenimiento u observación');
		}
		else if(band==false)
		{
			band=true;
		}
		else
		{
			band=false;
			enableFinishButton=false;
			document.form_taller.submit();
		}
	}
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
				 alertify.alert('Error al cargar la información de los artículos');
			}
		})
}

function select_all(chk)
{
	var cb = document.getElementsByName('reparacion1[]');

	for (i=0; i<cb.length; i++)
	{
		if(chk == true) cb[i].checked = true;
		else cb[i].checked = false;
	}
}

function select_all2(chk)
{
	var cb = document.getElementsByName('reparacion2[]');
	
	for (i=0; i<cb.length; i++)
	{
		if(chk == true) cb[i].checked = true;
		else cb[i].checked = false;
	}
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