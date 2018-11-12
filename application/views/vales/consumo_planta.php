<script>

	var permiso=<?php echo $id_permiso;?>;
	estado_transaccion='<?php echo $estado_transaccion;?>';
	estado_correcto='La requisición se ha guardado correctamente';
	estado_incorrecto='Error al intentar guardar la requisición: No se pudo conectar al servidor. Por favor vuelva a intentarlo.';

    <?php if($accion==0) {  ?>
        estado_correcto='La requisición ya fue procesada';
    <?php } ?>

</script>

<script src="<?php echo base_url()?>js/views/consumo_vales_planta.js" type="text/javascript"></script>
<section>
    <h2>Ingreso de Consumo de Combustible para la Planta Electrica y Otros</h2>
</section>
<style>
.k-multiselect {
	display: inline-block;
}
</style>
<form name="form_mision" method="post" id="form_mision" action="<?php echo base_url()?>index.php/vales/guardar_consumo_planta">
    <?=llaveform()?>
	<div id="wizard" class="swMain">
        <ul>
            <li>
                <a href="#step-1">
                    <span class="stepNumber">1<small>do</small></span>
                    <span class="stepDesc">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paso<br/>
                        <small>&nbsp;Datos de Factura</small>
                    </span>
                </a>
            </li>
        </ul>

        <div id="step-1">	
            <h2 class="StepTitle">Ingresar la factura</h2>

             <p>
                <label for="id_seccion" id="lid_seccion" >Uso</label>
                <select class="select" id="id_seccion" name="id_seccion" style="width:350px">
                    <?php
                      foreach($secciones as $val) {
                          echo '<option value="'.$val['id_seccion_adicional'].'">'.$val['nombre_seccion_adicional'].'</option>';
                      }
                    ?>
                </select>
            </p>

            <br>

            <p>
                <label for="id_gasolinera" id="lid_gasolinera" >Proveedor</label>
                <select class="select" id="id_gasolinera" name="id_gasolinera" style="width:175px">
                    <?php
                      foreach($gasolineras as $val) {
                          echo '<option value="'.$val['id_gasolinera'].'">'.$val['nombre'].'</option>';
                      }
                    ?>
                </select>
            </p>

            <br>
                 
            <p>
                <label for="numero_factura" id="lnumero_factura">Número de factura </label>
                <input style="width:100px;" type="text" tabindex="1" id="numero_factura" name="numero_factura"/>

                <label for="fecha_factura" >Fecha Factura:</label>
                <input id="fecha_factura" name="fecha_factura" style="width: 200px" tabindex="1"/>

            </p>

            <br>

            <p>

                <label for="tipo_gas" id="ltipo_gas">Tipo de Gasolina </label>
                <select class="select" style="width:100px;" tabindex="5" id="tipo_gas" name="tipo_gas" >
                    <option value=""></option>
                    <option value="1">Super</option>
                    <option value="2">Regular</option>
                    <option value="3">Diesel</option>
                </select>
            
                <label for="valor" id="lvalor" style="width:20%;">Precio </label>
                $ <input tabindex="4" id="valor" name="valor" type="text" size="5"/> US

                <label for="cantidad" id="lcantidad" style="width:20%;">Cantidad </label>
                <input tabindex="4" id="cantidad" name="cantidad" type="text" size="5"/>
            
            </p>

        </div>
    </div>
</form>

