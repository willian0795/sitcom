<script>

	var permiso=<?php echo $id_permiso;?>;
	estado_transaccion='<?php echo $estado_transaccion;?>';
	estado_correcto='La requisición se ha guardado correctamente';
	estado_incorrecto='Error al intentar guardar la requisición: No se pudo conectar al servidor. Por favor vuelva a intentarlo.';

    <?php if($accion==0) {  ?>
        estado_correcto='La requisición ya fue procesada';
    <?php } ?>

</script>

<script src="<?php echo base_url()?>js/views/entrega_vales_plan.js" type="text/javascript"></script>
<section>
    <h2>Ingreso de Uso</h2>
</section>
<style>
.k-multiselect {
	display: inline-block;
}
</style>
<form name="form_mision" method="post" id="form_mision" action="<?php echo base_url()?>index.php/vales/guardar_uso">
    <?=llaveform()?>
	<div id="wizard" class="swMain">
        <ul>
            <li>
                <a href="#step-1">
                    <span class="stepNumber">1<small>er</small></span>
                    <span class="stepDesc">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paso<br/>
                        <small>&nbsp;Datos de uso</small>
                    </span>
                </a>
            </li>
        </ul>                 

        <div id="step-1">
            <h2 class="StepTitle">Ingrese los datos del uso</h2>
           
           <input type="hidden" name="id_seccion">

           <br><br>

            <p>
                <label for="seccion" id="lseccion">Nombre del Uso </label>
                <input style="width:255px;" type="text" tabindex="1" id="seccion" name="seccion"/>               
            </p>
            
      	</div>

    </div>
</form>

