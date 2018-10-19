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
    <h2>Ingreso de Requisición de Combustible para la Planta Electrica</h2>
</section>
<style>
.k-multiselect {
	display: inline-block;
}
</style>
<form name="form_mision" method="post" id="form_mision" action="<?php echo base_url()?>index.php/vales/guardar_requisicion_planta">
    <?=llaveform()?>
	<div id="wizard" class="swMain">
        <ul>
            <li>
                <a href="#step-1">
                    <span class="stepNumber">1<small>er</small></span>
                    <span class="stepDesc">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paso<br/>
                        <small>&nbsp;Datos de requisición</small>
                    </span>
                </a>
            </li>
            <li>
                <a href="#step-2">
                    <span class="stepNumber">2<small>do</small></span>
                    <span class="stepDesc">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paso<br/>
                        <small>&nbsp;Datos de Factura</small>
                    </span>
                </a>
            </li>
        </ul>                 

        <div id="step-1">	
            <h2 class="StepTitle">Ingrese los datos de la requisición</h2>
           

            <p>
                <label for="cantidad_solicitada" id="lcantidad_solicitada">Cantidad Solicitada </label>
                <input style="width:100px;" type="text" tabindex="1" id="cantidad_solicitada" name="cantidad_solicitada"/>

                <label for="id_fuente_fondo" id="lid_fuente_fondo">Fuente de Financiamento </label>
                <select class="select" style="width:200px;" tabindex="2" id="id_fuente_fondo" name="id_fuente_fondo" >

                    <?php
                        foreach($fuente as $val) {
                    ?>
                       <option value="<?php echo $val['id_fuente_fondo'] ?>"><?php echo $val['nombre_fuente'] ?></option>
                    <?php   
                        }
                    ?>
                </select>
               
            </p>
            
            <br>
            
            <p>

                <label for="mes" id="lmes">Para el mes de </label>
                <select class="select" style="width:200px;" tabindex="5" id="mes" name="mes" >
                    <?php
                        foreach($m as $val) {
                    ?>
                       <option value="<?php echo $val['mes'] ?>"><?php echo strtoupper($val['mes_nombre']) ?></option>
                    <?php   
                        }
                    ?>
                </select>
                        
            </p>
            
            <br>
            
            <p>
            	<label for="justificacion" id="ljustificacion" class="label_textarea">Justificación </label>
              	<textarea class="tam-4" id="justificacion" tabindex="3" name="justificacion"/></textarea>
            </p>

            <br>

            <p>
                <label for="lbl" id="lrefuezo" >Número Inicial: </label>
                <input  id="numero_inicial"  name="numero_inicial" type="hidden" />
                <strong id="lbl"></strong> 
                <label for="lb2" id="lrefuezo" >Número Final: </label>
                <input type="hidden" name="numero_final" id="numero_final">
                <strong id="lb2"></strong>

                <input type="hidden" name="vale" id="vale">
                <input type="hidden" name="restante" id="restante">
            </p>
      	</div>

        <div id="step-2">	
            <h2 class="StepTitle">Ingresar la factura</h2>

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
                <select class="select" style="width:200px;" tabindex="5" id="tipo_gas" name="tipo_gas" >
                    <option value=""></option>
                    <option value="1">Super</option>
                    <option value="2">Regular</option>
                    <option value="3">Diesel</option>
                </select>
            
                <label for="valor" id="lvalor" style="width:20%;">Precio </label>
                $ <input tabindex="4" id="valor" name="valor" type="text" size="5"/> US
            
            </p>

        </div>
    </div>
</form>

