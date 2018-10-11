<?php
extract($articulo);
extract($presupuesto);
?>
<section>
    <h2>Cargar Artículo</h2>
</section>
<form name="form_presupuesto" method="post" action="<?php echo base_url()."index.php/vehiculo/surtir_articulo"; ?>" enctype="multipart/form-data" accept-charset="utf-8">
<input type="hidden" name="id_articulo" value="<?php echo $id_articulo ?>" >
	<div id="wizard" class="swMain">
        <ul>
            <li>
                <a href="#step-1">
                    <span class="stepNumber">1<small>er</small></span>
                    <span class="stepDesc">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paso<br/>
                        <small>&nbsp;Información del artículo</small>
                    </span>
                </a>
            </li>
            <li>
                <a href="#step-2">
                    <span class="stepNumber">2<small>do</small></span>
                    <span class="stepDesc">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paso<br/>
                        <small>&nbsp;Adquisición del artículo</small>
                    </span>
                </a>
            </li>
        </ul>
        <div id="step-1">
        	<h2 class="StepTitle">Verificación del artículo seleccionado</h2>
            <p>
            	<label>Nombre </label>
                <input type="text" name="nombre" <?php echo "value='".$nombre."'"; ?> disabled size="20">
                 <input type="hidden" name="nombre2" <?php echo "value='".$nombre."'"; ?>  />
            </p>
            <p>
            	<label>Cantidad disponible </label>
                <input type="text" name="cantidad2" <?php echo "value='".$cantidad."' disabled "; ?> size="10">
            </p>
            <p>
            	<label class="label_textarea">Descripción </label>
                <textarea name="descripcion" style="resize:none; width:200px" disabled><?php echo $descripcion; ?></textarea>
            </p>
        </div>
       <div id="step-2">
        	<h2 class="StepTitle">Información de adquisición del artículo a bodega</h2>
            <p>
            	<label id="lcantidad" for="cantidad">Cantidad de artículos adquiridos </label>
                <input type="text" name="cantidad" id="cantidad" size="10"> <strong><?php echo $unidad_medida; ?></strong>
                <input type="hidden" name="unidad_medida" value="<?php echo $unidad_medida; ?>" />
                <input type="hidden" name="id_unidad_medida" value="<?php echo $id_unidad_medida ?>" />
            </p>
            <p>
            	<label id="ladquisicion" for="adquisicion">Adquisición de el(los) artículo(s) </label>
                <select style="width:150px" class="select" name="adquisicion" id="adquisicion" placeholder="Seleccione...">
                	<option value="comprado">Comprado(s)</option>
                    <option value="donado">Donado(s)</option>
                </select>
                <div id="compra"></div>
            </p>
        </div>
	</div>
</form>
<script>
$(document).ready(function()
{
	$('#wizard').smartWizard();
	
	$('#adquisicion').change(
		function()
		{
			if(document.getElementById('adquisicion').value=='comprado')
			{
				cont='<p><label id="lgasto" for="gasto">Precio de los artículos($) </label> <input type="text" name="gasto" id="gasto" size="10"></p>';
				$("#compra").html(cont); 
				$('#gasto').validacion({
					req:true,
					valPrecio: true,
					<?php
						if($cantidad_actual<=0.00)
						{
							?>
							men: "Se ha agotado el presupuesto, debe solicitar un refuerzo",
							numMax: <?php echo ($cantidad_actual+0.01); ?>
							<?php
						}
						else
						{
							?>
							men: "Se debe ingresar un precio monetario entre $ 0.01 y $ <?php echo number_format($cantidad_actual,2); ?>",
							numMax: <?php echo ($cantidad_actual+0.01); ?>
							<?php
						}
					?>
				});
			}
			else
			{
				cont="";
				$("#compra").html(cont);
				$('#gasto').validacion({
					req:false
				});
			} 
		}
	);
	$('#cantidad').validacion({
		req:true,
		num: true
	});
	$('#adquisicion').validacion({
		req: true
	})
	$('#gasto').validacion({
		req:true,
		num: true
	});
});
</script>