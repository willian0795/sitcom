<?php
	

		$dir="Combustible_para_todos";
		$label="Guardar";

?>
<style>
	.k-multiselect {
		display: inline-block;
	}
</style>
<form name="formu" id="formu" style="max-width: 600px;" method="post" action="<?php echo base_url(); ?>index.php/vales/<?php echo $dir;?>" >
  	<input type="hidden" id="id" name="id" value="49454"/>

<br><br><br>
	<fieldset>      
        <legend align='left'>Información  general</legend>
        <p>
			Por defecto el sistema no permite que se registre consumo de combustible en aquellos vehículos que se encuentran
			 en taller interno o taller externo, sin embargo en ocasiones se pueden hacer excepciones. Dicha excepciones 
			 se controlan de forma manual, permitiéndolo por un lapso de <strong> 120 minutos </strong> 
			 a partir del momento que se activa, ó desde esta pantalla
        </p>
	</fieldset>
	<fieldset>
		<legend align='left'>Estado de configuración </legend>

		<p>
			<label id="estadol" for="estado"></label>
            <input type="checkbox" name="estado"  id="estado" value="<?php echo $estado;?>"> 
			
        </p>                

	</fieldset>
    
    <p style='text-align: center;'>
        <button type="submit" id="aprobar" class="button tam-1 boton_validador" name="aprobar"><?=$label?></button>
    </p>
</form>
<script>
	$("#estado").change(function (argument) {

		if($(this).is(':checked')){
			$("#estadol").html("Activa");
			$(this).val(1);
		}else{
			$("#estadol").html("Desactivada");
			$(this).val(2);
		}
	})
	
	if($("#estado").val()==1){
		$("#estado").attr("checked",true);
	}else{
		$("#estado").attr("checked",false);
	}
	$('#estado').change();
	
</script>