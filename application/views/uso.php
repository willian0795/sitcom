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
    <h2>Ingreso de Requisición de Combustible para la Planta Electrica y Otros</h2>
</section>
<style>
.k-multiselect {
	display: inline-block;
}
</style>
<form name="form_mision" method="post" id="form_mision" action="<?php echo base_url()?>index.php/vales/guardar_requisicion_planta">
    <?=llaveform()?>
  <div id="wizard" class="swMain">
    
  </div>
</form>

