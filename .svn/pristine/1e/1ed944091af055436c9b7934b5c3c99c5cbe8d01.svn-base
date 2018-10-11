<?php
foreach($vehiculo as $v)
{
	$id_vehiculo=$v->id;
	$placa=$v->placa;
	$fecha_recepcion=$v->fecha_recepcion;
}
if($bandera2=='true')
{
	extract($info);
	$action=base_url()."index.php/vehiculo/modificar_taller_ext";
}
else $action=base_url()."index.php/vehiculo/guardar_taller_ext";
?>
<script>
	estado_transaccion='<?php echo $estado_transaccion?>';
	estado_correcto='Se ha ingresado el vehículo a taller externo, éxitosamente';
	estado_incorrecto='Error de conexión al servidor. Por favor vuelva a intentarlo.';
</script>
<section>
    <h2>Ingreso de Vehículo al Taller Externo</h2>
</section>
<form name="form_taller_ext" method="post" action="<?php echo $action; ?>" >
<input type="text" name="fecha_max" id="fecha_max" value="<?php echo date('d-m-Y') ?>" style="display:none" />
<input type="text" name="fecha_min" id="fecha_min" value="<?php if($fecha_min!="") echo $fecha_min; else echo $fecha_recepcion; ?>" style="display:none" />
<?php
if($bandera2=='true')
{
	echo '<input type="hidden" name="id_ingreso_taller_ext" value="'.$id_ingreso_taller_ext.'" />';
	echo '<input type="hidden" name="id_ingreso_taller_original" value="'.$id_ingreso_taller.'" />';
	echo '<input type="hidden" name="id_vehiculo_original" value="'.$id_vehiculo.'" />';
}
?>
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
                        <small>&nbsp;Información de taller externo</small>
                    </span>
                </a>
            </li>
        </ul>
        <div id="step-1">	
           <h2 class="StepTitle">Datos de verificación del vehículo</h2>
          	<?php if($bandera=='false' || $bandera2=='true'){ ?>
            <p>
            	<input type="hidden" name="pantalla" value="1" />
                <input type="hidden" name="placa" value="" />
                <label style="width:150px" id="lid_vehiculo" for="id_vehiculo">Número de placa </label>
                <select class="select" style="width:100px" onchange="cargar(this.value)" name="id_vehiculo" id="id_vehiculo" multiple="multiple" placeholder="Seleccione...">
                	<?php
					foreach($vehiculos as $v)
					{
						$aux="";
						if($bandera2=='true' && $id_vehiculo==$v->id) $aux="selected='selected'";
						echo "<option value='".$v->id."' ".$aux.">".$v->placa."</option>";
					}
                    ?>
                </select>
            <?php }else{ ?>
            <p>
            	<input type="hidden" name="pantalla" value="2" />
            	<input type="hidden" name="id_vehiculo" id="id_vehiculo" value="<?php echo $id_vehiculo; ?>" />
                <label style="width:150px">Número de placa: </label>
                <input type="hidden" name="placa" value="<?php echo $placa; ?>" />
                <strong><?php echo $placa; ?></strong>
                <label style="width:200px" >Fecha de ingreso a taller interno </label>
                <strong><?php echo $fecha_recepcion; ?></strong>
            </p>
			<?php } ?>
            <br />
            <p>
           <div id="info_vehiculo">
           </div>
           </p>
       </div>
       <div id="step-2">	
           <h2 class="StepTitle">Información de envío al taller externo</h2>
           	 <p>
                <label style="width:100px" id="lfecha_recepcion" for="fecha_recepcion">Fecha de envío al taller externo </label>
                <input type="text" name="fecha_recepcion" id="fecha_recepcion" value="<?php if($bandera2=='true') echo $fecha_recepcion ?>" />
            </p>
            <p>
            	<label class="label_textarea" style="width:100px" id="ltrabajo_solicitado" for="trabajo_solicitado">Trabajo solicitado </label>
                <textarea style="width:200px; resize:none;" name="trabajo_solicitado" id="trabajo_solicitado"><?php if($bandera2=='true') echo $trabajo_solicitado ?></textarea>
            </p>
            <p>
              <label style="width:100px" id="lid_taller_externo" for="id_taller_externo">Taller externo </label>
                <select class="select" style="width:100px" name="id_taller_externo" id="id_taller_externo" multiple="multiple" placeholder="Seleccione...">
                	<?php
					foreach($talleres as $ta)
					{
						$aux="";
						if($bandera2=='true' && $id_taller_externo==$ta['id_taller_externo']) $aux="selected='selected'";
						echo "<option value='".$ta['id_taller_externo']."' ".$aux.">".$ta['taller']."</option>";
					}
                    ?>
                    <option value="0">Otro</option>
                </select>
                <input type="text" name="ntaller_ext" id="ntaller_ext" disabled="disabled"/>
            </p>
       </div>
    </div>
</form>
<script type="text/javascript">
$(document).ready(function(){
	$('#wizard').smartWizard();
	 <?php if($bandera=='false'){ ?>
	$('#id_vehiculo').validacion({
		req:true
	});
	 <?php } ?>
	$('#trabajo_solicitado').validacion({
		req:true,
		lonMin:5
	});
	$('#id_taller_externo').validacion({
		req:true
	});
	
	var fecha = $("#fecha_recepcion").kendoDatePicker({
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
	
	$('#fecha_recepcion').validacion({
		req:true,
		valFecha:true
	});
	
	$('#id_taller_externo').change(
		function()
		{
			if(Number($(this).val())==0)
			{
				$("#ntaller_ext").attr("disabled",false);
				$('#ntaller_ext').validacion({
					req: true,
					lonMin:3
				});
			}
			else
			{
				$("#ntaller_ext").attr("disabled",true);
				$("#ntaller_ext").val("");
				$('#ntaller_ext').validacion({
					req: false
				});
			}
		}
	);
});

function cargar(id)
	{
		if(id=='') return 0;
		
		$('#info_vehiculo').html("");
		var  dur = "<?php echo base_url()?>index.php/vehiculo/vehiculo_info/"+id+"/<?php if($bandera2=='true') echo NULL; else echo "2/1"; ?>";
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
				
				if(json[0]['id_ingreso_taller']!='0')
				{
					cont=cont+'<input type="hidden" name="id_ingreso_taller" value="'+json[0]['id_ingreso_taller']+'">';
					//document.getElementById('fecha_min').value='16-08-2015'
				}
				else cont=cont+'<input type="hidden" name="id_ingreso_taller" value="NULO">';
				
				$('#info_vehiculo').html(cont);
			},
			error:function(data) {
				 alertify.alert('Error al cargar los datos de los vehiculos');
			}
		})
		
	}
</script>
<?php
if($bandera=='true')
{
?>
	<script>
		var id_v=<?php echo $id_vehiculo; ?>;
		cargar(id_v);
    </script>
<?php
}
?>
