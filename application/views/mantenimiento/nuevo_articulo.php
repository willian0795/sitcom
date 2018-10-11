<?php
if($bandera=='true')
{
	extract($articulo);
	$action=base_url()."index.php/vehiculo/modificar_articulo";
}
else
{
	$action=base_url()."index.php/vehiculo/guardar_articulo";
	extract($presupuesto);
}
?>
<section>
    <h2><?php if($bandera=='true')echo "Modificar Artículo"; else {?>Nuevo Artículo <?php } ?></h2>
</section>
<form name="form_presupuesto" method="post" action="<?php echo $action; ?>" enctype="multipart/form-data" accept-charset="utf-8">
<?php if($bandera=='true') echo '<input type="hidden" name="id_articulo" value="'.$id_articulo.'" >'; ?>
	<div id="wizard" class="swMain">
        <ul>
            <li>
                <a href="#step-1">
                    <span class="stepNumber">1<small>er</small></span>
                    <span class="stepDesc">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paso<br/>
                        <small>&nbsp;Información del Artículo</small>
                    </span>
                </a>
            </li>
            <?php if($bandera!=NULL && $bandera!='true'){ ?>
            <li>
                <a href="#step-2">
                    <span class="stepNumber">2<small>do</small></span>
                    <span class="stepDesc">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paso<br/>
                        <small>&nbsp;Adquisición del Artículo</small>
                    </span>
                </a>
            </li>
            <?php } ?>
        </ul>
        <div id="step-1">
        	<h2 class="StepTitle">Ingreso de la informaci&oacute;n del artículo a bodega</h2>
            <p>
            	<label for="nombre" id="lnombre">Nombre </label>
                <input type="text" id="nombre" name="nombre" <?php if($bandera=='true') echo "value='".$nombre."'"; ?> size="48">
            </p>
            <p>
            	<label for="id_unidad_medida" id="lunidad_medida">Unidad de medida<font color="#FF0000"> *</font> </label>
                <select class="select" name="id_unidad_medida" id="id_unidad_medida" style="width:150px" multiple="multiple" placeholder="Seleccione...">
               	<?php
				foreach($unidades as $um)
				{
                ?>
                	<option value="<?php echo $um['id_unidad_medida'] ?>"  <?php if($bandera=='true') if($um['id_unidad_medida']==$id_unidad_medida) echo "selected='selected'"; ?> ><?php echo $um['unidad_medida']; ?></option>
                <?php
				}
				?>
                </select>
            </p>
            <p>
            	<label id="lcantidad" for="cantidad">Cantidad </label>
                <input type="text" id="cantidad" name="cantidad" <?php if($bandera=='true') echo "value='".$cantidad."' disabled='disabled'"; ?> size="10">
            </p>
            <p>
            	<label class="label_textarea" for="descripcion" id="ldescripcion">Descripción </label>
                <textarea name="descripcion" id="descripcion" style="resize:none; width:200px"><?php if($bandera=='true') echo $descripcion; ?></textarea>
            </p>
        </div>
        <?php if($bandera!=NULL && $bandera!='true'){ ?>
        <div id="step-2">
        	<h2 class="StepTitle">Información de adquisición del artículo a bodega</h2>
            <p>
            	<label for="adquisicion" id="ladquisicion">Adquisición de el(los) artículo(s) </label>
                <select style="width:150px" class="select" name="adquisicion" id="adquisicion" placeholder="Seleccione...">
                	<option value="comprado">Comprado(s)</option>
                    <option value="donado">Donado(s)</option>
                </select>
                <div id="compra"></div>
            </p>
        </div>
         <?php } ?>
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
				cont='<p><label for="gasto" id="lgasto">Precio de los artículos($) </label> <input type="text" id="gasto" name="gasto" size="10"></p>';
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
	
	$('#nombre').validacion({
		req:true,
		lonMin: 3
	});
	$('#id_unidad_medida').validacion({
		req: true
	})
	$('#cantidad').validacion({
		req:true,
		num: true
	});
	$('#descripcion').validacion({
		req:false,
		lonMin: 10
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