<?php

if($bandera=='true')
{
	foreach($vehiculo_info as $v)
	{
		$id_vehiculo=$v->id_vehiculo;
		$placa=$v->placa;
		$marca2=$v->marca;
		$id_marca=$v->id_marca;
		$modelo2=$v->modelo;
		$id_modelo=$v->id_modelo;
		$condicion2=$v->condicion;
		$id_condicion=$v->id_condicion;
		$clase2=$v->clase;
		$id_clase=$v->id_clase;
		$kilometraje=$v->kilometraje;
		$motorista=ucwords($v->motorista);
		$id_motorista=$v->id_empleado;
		$anio=$v->anio;
		$fuente_fondo2=$v->fuente_fondo;
		$id_fuente_fondo=$v->id_fuente_fondo;
		$seccion2=ucwords($v->seccion);
		$id_seccion=$v->id_seccion;
		$imagen=$v->imagen;
		$estado=$v->estado;	
		$tipo_combustible=$v->tipo_combustible;
	}
	
	$action=base_url()."index.php/vehiculo/modificar_vehiculo/".$id_vehiculo;
}
else $action=base_url()."index.php/vehiculo/guardar_vehiculo";
?>

<style>

input[type="file"]
{
	z-index: 999;
	line-height: 0;
	font-size: 50px;
	position: absolute;
	opacity: 0;
	filter: alpha(opacity = 0);-ms-filter: "alpha(opacity=0)";
	margin: 0;
	padding:0;
	left:0;
}

/* Al label lo convertimos en "boton"
(en apariencia) */
.cargar
{
	display: inline-block;
	margin: 15px auto;
	padding: 5px;
	text-decoration: none;
	text-align: center;
	font: bold 13px Verdana, Arial, Helvetica, sans-serif;
	width: 150px;
	color: #FFF;
	cursor: pointer;
	outline-style: none;
	background-color: #5A5655;
	border: 1px solid #5A5655;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
}

</style>

<section>
    <h2><?php if($bandera=='true')echo "Modificar Vehículo"; else {?>Nuevo Vehículo <?php } ?></h2>
</section>
<form name="form_vehiculo" method="post" action="<?php echo $action; ?>" enctype="multipart/form-data" accept-charset="utf-8">
<?php if($bandera=='true'){ ?> <input type="hidden" name="id_vehiculo" value="<?php echo $id_vehiculo ?>"  /> <?php } ?>
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
                        <small>&nbsp;Adquisición del vehículo</small>
                    </span>
                </a>
            </li>
            <li>
                <a href="#step-3">
                    <span class="stepNumber">3<small>er</small></span>
                    <span class="stepDesc">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paso<br/>
                        <small>&nbsp;Asignación del vehículo</small>
                    </span>
                </a>
            </li>
        </ul>
        <div id="step-1">	
            <h2 class="StepTitle">Ingreso de la informaci&oacute;n del vehículo</h2>
			<p>
                <label id="lplaca" for="placa">Número de placa </label>
                <input type="text" name="placa" id="placa" size="10" <?php if($bandera=='true') echo "value='".$placa."'" ?> />
               
            </p>
            <p>
                <label id="lmarca" for="marca">Marca </label>
                 <select class="select" placeholder="[Seleccione...]" name="marca" id="marca" style="width:150px" multiple="multiple">
                <?php
                foreach($marca as $mar)
                {
					if($bandera=='true')
					{
						if($mar->id_vehiculo_marca==$id_marca)
						{
							echo "<option selected='selected' value='".$mar->id_vehiculo_marca."'>".ucwords($mar->nombre)."</option>";
						}
						else echo "<option value='".$mar->id_vehiculo_marca."'>".ucwords($mar->nombre)."</option>";
						
					}
					else echo "<option value='".$mar->id_vehiculo_marca."'>".ucwords($mar->nombre)."</option>";
                }
				
                ?>
                <option value="0">Otra</option>
                </select>
                <input type="text" name="nmarca" id="nmarca" disabled="disabled"/>
            </p>
            <p>
                <label id="lmodelo" for="modelo">Modelo </label>
                 <select name="modelo" placeholder="[Seleccione...]" id="modelo" class="select" style="width:200px" multiple="multiple">
                <?php
                
                foreach($modelo as $modl)
                {
					if($bandera=='true')
					{
						if($modl->id_vehiculo_modelo==$id_modelo)
						{
							echo "<option selected='selected' value='".$modl->id_vehiculo_modelo."'>".ucwords($modl->modelo)."</option>";
						}
						else echo "<option value='".$modl->id_vehiculo_modelo."'>".ucwords($modl->modelo)."</option>";
					}
					else echo "<option value='".$modl->id_vehiculo_modelo."'>".ucwords($modl->modelo)."</option>";                    
                }
                ?>
                <option value="0">Otro</option>
                </select>
                <input type="text" name="nmodelo" id="nmodelo" disabled="disabled"/>
            </p>
            <p>
                <label id="lclase" for="clase">Clase </label>
                 <select name="clase" id="clase" placeholder="[Seleccione...]" class="select"  style="width:150px" multiple="multiple">
                <?php
                
                foreach($clase as $cla)
                {
					if($bandera=='true')
					{
						if($cla->id_vehiculo_clase==$id_clase) echo "<option selected='selected' value='".$cla->id_vehiculo_clase."'>".ucwords($cla->nombre_clase)."</option>";
						else echo "<option value='".$cla->id_vehiculo_clase."'>".ucwords($cla->nombre_clase)."</option>";
					}
					else echo "<option value='".$cla->id_vehiculo_clase."'>".ucwords($cla->nombre_clase)."</option>";
                }
                ?>
                <option value="0">Otra</option>
                </select>
                <input type="text" name="nclase" id="nclase" disabled="disabled"/>
            </p>
            <p>
            	<label id="lanio" for="anio">Año </label>
                <input type="text" name="anio" id="anio" size="10" <?php if($bandera=='true') echo "value='".$anio."'"; ?> />
            </p>
            <p>
                <label id="luserfile" for="userfile">Fotografía </label>
                
                <label class="cargar" style="width: 190px; height: 29px;">
                <span>
                Seleccione imagen
                <input type="file" name="userfile" id="userfile" disabled="disabled" onchange="validate_fileupload(this.value)" style="height: 20px; width: 200px; " />
                </span>
                </label>
                <input type="text" id="url-archivo" name="url-archivo" disabled="disabled" />
				
				
				<?php if($bandera=='true') {?>
                	<label>Mantener imagen</label>
                    <input type="checkbox" name="img_df" id="img_df" value="si" checked="checked" />
                    <input type="text" name="imagen" value='<?php echo $imagen ?>' readonly="readonly" />
                <?php }else{ ?>
                <label>Imagen por defecto</label>
                <input type="checkbox" name="img_df" id="img_df" value="si" checked="checked"  />
                <?php }?>
            </p>
        </div>
        <div id="step-2">	
            <h2 class="StepTitle">Ingreso de la informaci&oacute;n de adquisición del vehículo</h2>
            <p>
                <label id="lfuente" for="fuente">Fuente de fondo </label>
                <select name="fuente" id="fuente" class="select" placeholder="[Seleccione...]" multiple="multiple" style="width:250px">
                <?php                
					foreach($fuente_fondo as $fue)
					{
						if($bandera=='true')
						{
							if($fue->id_fuente_fondo==$id_fuente_fondo) echo "<option selected='selected' value='".$fue->id_fuente_fondo."'>".ucwords($fue->fuente)."</option>";
							else echo "<option value='".$fue->id_fuente_fondo."'>".ucwords($fue->fuente)."</option>";
							
						}
						else echo "<option value='".$fue->id_fuente_fondo."'>".ucwords($fue->fuente)."</option>";
					}
                ?>
                <option value="0">Otra</option>
                </select>
                <input type="text" name="nfuente" id="nfuente" disabled="disabled"/>
            </p>
            <p>
                <label id="lcondicion" for="condicion">Condición </label>
                 <select name="condicion" id="condicion" class="select" placeholder="[Seleccione...]" style="width:175px" multiple="multiple">
                <?php
                foreach($condicion as $con)
                {
					if($bandera=='true')
					{
						if($con->id_vehiculo_condicion==$id_condicion) echo "<option selected='selected' value='".$con->id_vehiculo_condicion."'>".ucwords($con->condicion)."</option>";
						else echo "<option value='".$con->id_vehiculo_condicion."'>".ucwords($con->condicion)."</option>";
					}
					else echo "<option value='".$con->id_vehiculo_condicion."'>".ucwords($con->condicion)."</option>";
                }
                ?>
                </select>
            </p>
            <?php
			if($bandera=='true')
			{
				?>
				<p>
                    <label id="lestado" for="estado">Estado </label>
                    <select name="estado" id="estado" placeholder="[Seleccione...]" class="select" style="width:150px" multiple="multiple">
                        <option value="0" <?php if($estado==0) echo "selected='selected'"; ?>>Inactivo</option>
                        <option value="1" <?php if($estado==1) echo "selected='selected'"; ?>>Activo</option>
                        <option value="2" <?php if($estado==2) echo "selected='selected'"; ?>>En Taller Interno</option>
                        <option value="3" <?php if($estado==3) echo "selected='selected'"; ?>>En Taller Externo</option>
                        <option value="4" <?php if($estado==4) echo "selected='selected'"; ?>>Robado</option>
                        <option value="5" <?php if($estado==5) echo "selected='selected'"; ?>>Hurtado</option>
                        <option value="6" <?php if($estado==6) echo "selected='selected'"; ?>>Extraviado</option>
                    </select>
            	</p>
				<?php
			}
            ?>
            <p>
            	<label id="ltipo_combustible" for="tipo_combustible">Tipo de combustible </label>
                <select id="tipo_combustible" name="tipo_combustible" class="select" placeholder="[Seleccione...]" style="width:150px" multiple="multiple">
                	<option value="Diesel" <?php if($bandera=='true'){ if(strcmp($tipo_combustible,'Diesel')==0) echo "selected='selected'";} ?>>Diesel</option>
                    <option value="Gasolina" <?php if($bandera=='true'){ if(strcmp($tipo_combustible,'Gasolina')==0) echo "selected='selected'";} ?>>Gasolina</option>
                </select>
            </p>
        </div>
        <div id="step-3">	
            <h2 class="StepTitle">Información de asignación de motorista, oficina y sección del vehículo</h2>
            <p>
                <label id="lseccion" for="seccion">Sección: </label>
                <select name="seccion" id="seccion" class="select" placeholder="[Seleccione...]" style="width:350px" multiple="multiple">
                <?php
                
                foreach($seccion as $sec)
                {
					if($bandera=='true')
					{
						if($sec->id_seccion==$id_seccion) echo "<option selected='selected' value='".$sec->id_seccion."'>".ucwords($sec->seccion)."</option>";
						else echo "<option value='".$sec->id_seccion."'>".ucwords($sec->seccion)."</option>";
					}
					else echo "<option value='".$sec->id_seccion."'>".ucwords($sec->seccion)."</option>";
                    
                }
                ?>
                </select>
            </p>
            <p>
                <label id="lmotorista" for="motorista">Motorista </label>
                <select name="motorista" id="motorista" class="select" placeholder="[Seleccione...]" style="width:300px" multiple="multiple">
				<?php
                
                foreach($motoristas as $mot)
                {
					if($bandera=='true')
					{
						if($mot->id_empleado==$id_motorista) echo "<option selected='selected' value='".$mot->id_empleado."'>".ucwords($mot->nombre)."</option>";
						else echo "<option value='".$mot->id_empleado."'>".ucwords($mot->nombre)."</option>";
					}
					else echo "<option value='".$mot->id_empleado."'>".ucwords($mot->nombre)."</option>";
                }
                ?>
                <option value="0" <?php if($bandera=='true' && $id_motorista==0) echo "selected='selected'"?> >Sin asignación</option>
                </select>
            </p>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
	$('#wizard').smartWizard();
	$('#marca').change(
		function()
		{
			if(Number($(this).val())==0)
			{
				$("#nmarca").attr("disabled",false);
				$('#nmarca').validacion({
					req: true,
					lonMin:3
				});
			}
			else
			{
				$("#nmarca").attr("disabled",true);
				$("#nmarca").val("");
				$('#nmarca').validacion({
					req: false
				});
			}
		}
	);
	$('#modelo').change(
		function()
		{
			if(Number($(this).val())==0)
			{
				$("#nmodelo").attr("disabled",false);
				$('#nmodelo').validacion({
					req: true,
					lonMin:3
				});
			}
			else
			{
				$("#nmodelo").attr("disabled",true);
				$("#nmodelo").val("");
				$('#nmodelo').validacion({
					req: false
				});
			}
		}
	);
	$('#clase').change(
		function()
		{
			if(Number($(this).val())==0)
			{
				$("#nclase").attr("disabled",false);
				$('#nclase').validacion({
					req: true,
					lonMin:3
				});
			}
			else
			{
				$("#nclase").attr("disabled",true);
				$("#nclase").val("");
				$('#nclase').validacion({
					req: false
				});
			}
		}
	);
	$('#fuente').change(
		function()
		{
			if(Number($(this).val())==0)
			{
				$("#nfuente").attr("disabled",false);
				$('#nfuente').validacion({
					req: true,
					lonMin:3
				});
			}
			else
			{
				$("#nfuente").attr("disabled",true);
				$("#nfuente").val("");
				$('#nfuente').validacion({
					req: false
				});
			}
		}
	);
	$('#img_df').change(
		function()
		{
			if($("#img_df").is(':checked'))
			{  
				$("#userfile").attr("disabled",true);
				$("#url-archivo").val("");
				$("#url-archivo").attr("disabled",true);
				$('#url-archivo').validacion({
					req: false
				});
			}
			else
			{  
				$("#userfile").attr("disabled",false);
				$("#url-archivo").attr("disabled",false);
				$('#url-archivo').validacion({
					req: true,
					men: "Debe seleccionar una imagen de formato: .gif | .jpg | .png | .jpeg"
				});
			} 
		}
	);
    function asignacion (id_fuente_fondo) {
        
            $('#seccion_vales').empty();

    }
	$('#placa').validacion({
		req: true
	});
	$('#marca').validacion({
		req: true
	});
	$('#nmarca').validacion({
		req: false,
		lonMin:3
	});
	$('#modelo').validacion({
		req: true
	});
	$('#nmodelo').validacion({
		req: false,
		lonMin:3
	});
	$('#clase').validacion({
		req: true
	});
	$('#nclase').validacion({
		req: false,
		lonMin:3
	});
	$('#anio').validacion({
		req: true,
		ent:true,
		lonMin:4
	});
	$('#fuente').validacion({
		req: true
	});
	$('#nfuente').validacion({
		req: false,
		lonMin:3
	});
	$('#condicion').validacion({
		req: true
	});
	$('#tipo_combustible').validacion({
		req: true
	});
	$('#seccion').validacion({
		req: true
	});
	$('#motorista').validacion({
		req: true
	});
	
	$('#url-archivo').validacion({
		req:false
	});
	$('#userfile').change(function()
	{
		$('#url-archivo').val($(this).val());
	});
	
});

function validate_fileupload(fileName)
{
    var allowed_extensions = new Array("GIF","gif","JPG","jpg","PNG","png","JPEG","jpeg");
    var file_extension = fileName.split('.').pop(); // split function will split the filename by dot(.), and pop function will pop the last element from the array which will give you the extension as well. If there will be no extension then it will return the filename.
    var tipoArchivo= false;
    for(var i = 0; i <= allowed_extensions.length; i++)
    {
        if(allowed_extensions[i]==file_extension)
        {
     		tipoArchivo=true;
            return true; // valid file extension
            
        }
    }
    alertify.alert("Debe seleccionar una imagen de formato: .gif | .jpg | .png | .jpeg");
	tipoArchivo=false;
	
	document.getElementById('userfile').value="";
	
    return false;
}

</script>