<section>
    <h2>Reforzar Presupuesto</h2>
</section>
<form name="form_refuerzo" method="post" action="<?php echo base_url()."index.php/vehiculo/guardar_refuerzo" ?>" enctype="multipart/form-data" accept-charset="utf-8">
<input type="hidden" name="id_presupuesto" value="<?php echo $id_presupuesto ?>" >
	<div id="wizard" class="swMain">
        <ul>
            <li>
                <a href="#step-1">
                    <span class="stepNumber">1<small>er</small></span>
                    <span class="stepDesc">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paso<br/>
                        <small>&nbsp;Refuerzo</small>
                    </span>
                </a>
            </li>
        </ul>
        <div id="step-1">
        	<h2 class="StepTitle">Informaci&oacute;n del refuerzo</h2>
            <p>
                <label style="width:150px">Fecha de Refuerzo: </label>
                <strong><?php echo date('d/m/Y')?></strong>
            </p>
            <p>
            	<label id="lrefuerzo" for="refuerzo" style="width:150px">Cantidad de refuerzo($) </label>
                <input type="text" name="refuerzo" size="10" id="refuerzo">
            </p>
            <p>
            	<label style="width:150px" id="ljustificacion" for="justificacion" class="label_textarea">Justificación </label>
                <textarea name="justificacion" style="resize:none; width:300px" rows="2" id="justificacion"></textarea>
            </p>
        </div>
	</div>
</form>
<script>
$(document).ready(function()
{
	$('#wizard').smartWizard();
	$('#refuerzo').validacion({
		req:true,
		num:true
	});
	$('#justificacion').validacion({
		req:true,
		lonMin: 10
	});
});
</script>