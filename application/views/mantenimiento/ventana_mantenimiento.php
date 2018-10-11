<?php
extract($vehiculo);
?>
<form name="form_mtto_info" method="post">

<fieldset>
	<legend>Información del vehículo</legend>
	<?php 
        echo "<img src='".base_url()."fotografias_vehiculos/".$imagen."' align='center' width='200px'></img><br/>";
        echo "Placa: <strong>".$placa."</strong> <br>
        Marca: <strong>".$marca."</strong> <br>
        Modelo: <strong>".$modelo."</strong> <br>";
		echo "Sección: <strong>".$seccion."</strong> <br>
		Motorista: <strong>".ucwords($motorista)."</strong> <br>
		Fuente de fondo: <strong>".$fuente_fondo."</strong> <br>
		Kilometraje recorrido: <strong>".$kilometraje." km</strong> <br>";
	?>
</fieldset>
<br />
<fieldset>
	<legend>Información de ingreso al taller interno</legend>
    <?php 
        echo "Fecha de recepción: <strong>".$fecha_recepcion."</strong> <br>
        Trabajo solicitado: <strong>".$trabajo_solicitado."</strong> <br>";
        if($trabajo_solicitado_carroceria!=NULL && $trabajo_solicitado_carroceria!='')
		echo "Trabajo solicitado en carrocería: <strong>".$trabajo_solicitado_carroceria."</strong> <br>";
		echo "Accesorios registrados: <br><strong>";
		$i=1;
		$j=1;
		foreach($revision as $r)
		{
			if($r['tipo']=='interno')
			{
				if($i==1) echo "<br>INTERNOS <br>";
				echo $i." - ".$r['revision'];
				if($r['varios']!=0) echo " --- Cantidad: ".$r['varios'];
				echo "<br>";
				$i++;
			}
			elseif($r['tipo']=='externo')
			{
				if($j==1) echo "<br>EXTERNOS <br>";
				echo $j." - ".$r['revision'];
				if($r['varios']!=0) echo " --- Cantidad: ".$r['varios'];
				echo "<br>";
				$j++;
			}
		}
		echo "</strong>";
	?>
</fieldset>
<?php
	if(!empty($mantenimientos))
	{
?>
<fieldset>
	<legend>Mantenimientos realizados</legend>
    <?php
		$x=0;
		$fecha='';
		foreach($mantenimientos as $m)
		{
			if($x==0)
			{
				echo "<fieldset><legend>".$m['fecha']."</legend>";
				$fecha=$m['fecha'];
				$x++;
			}
			if($fecha!=$m['fecha'])
			{
				echo "</fieldset><fieldset><legend>".$m['fecha']."</legend>";
				$fecha=$m['fecha'];
			}
			if($m['tipo']=='mantenimiento')
			{
				echo "<br>Mantenimientos: <br><strong>".$m['reparacion']."</strong><br>";
				if($m['otro_mtto']!=NULL && $m['otro_mtto']!='') echo "Mantenimientos adicionales: <strong>".$m['otro_mtto']."</strong><br>";
			}
			
			if($m['tipo']=='inspeccion')
			{
				echo "<br>Inspecciones o chequeos: <br><strong>".$m['reparacion']."</strong><br>";
				if($m['observaciones']!=NULL && $m['observaciones']!='')echo "Observaciones: <strong>".$m['observaciones']."</strong><br>";
			}
		}
	?>
    </fieldset>
</fieldset>
<?php
	}
?>
<p style='text-align: center;'>
	<button type="button" id="aceptar" onclick="cerrar_vent()" name="Aceptar">Aceptar</button>
</p>
</form>