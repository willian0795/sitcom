<form id='form' method='post'>
    <input type='hidden' id='resp' name='resp' />

    <fieldset>      
        <legend align='left'>Generalidades del vehículo</legend>
            <?php 
                foreach($datos as $v)
                {
					$id_vehiculo=$v->id_vehiculo;
                    $placa=$v->placa;
					$marca=$v->marca;
					$id_marca=$v->id_marca;
					$modelo=$v->modelo;
					$id_modelo=$v->modelo;
					$condicion=$v->condicion;
					$id_condicion=$v->id_condicion;
					$clase=$v->clase;
					$id_clase=$v->id_clase;
					$kilometraje=$v->kilometraje;
					$motorista=ucwords($v->motorista);
					$id_motorista=$v->id_empleado;
					$anio=$v->anio;
					$fuente_fondo=$v->fuente_fondo;
					$id_fuente_fondo=$v->id_fuente_fondo;
                    $seccion=ucwords($v->seccion);
					$id_seccion=$v->id_seccion;
					$imagen=$v->imagen;
					$estado=$v->estado;
                }
            
				echo "<img src='".base_url()."fotografias_vehiculos/".$imagen."' align='center' width='200px'></img><br/>";
                echo "Placa: <strong>".$placa."</strong> <br>
                Marca: <strong>".$marca."</strong> <br>
                Modelo: <strong>".$modelo."</strong> <br>
				</fieldset>
    <br />";
	?>
    	<fieldset>
        <legend align='left'>Asignaciones</legend>
		<?php
			 echo "Sección: <strong>".$seccion."</strong> <br>
                Motorista: <strong>".$motorista."</strong> <br>
                Fuente de fondo: <strong>".$fuente_fondo."</strong> <br></fieldset>
    <br />";
		?>
        </fieldset>

    <fieldset>
        <legend align='left'>Estado</legend>
        <?php
		if($estado==1) $msj="Activo";
		else if($estado==2) $msj="En Reparación(Taller Interno)";
		else if($estado==3) $msj="En Reparación(Taller Externo)";
		else if($estado==4) $msj="Robado";
		else if($fila->estado==5) $msj="Extraviado";
		else if($estado==0) $msj="Inactivo";
			 echo "Condición del vehículo: <strong>".$condicion."</strong> <br>
                Estado actual: <strong>".$msj."</strong> <br>
                Kilometraje recorrido: <strong>".$kilometraje." km</strong> <br></fieldset>
    <br />";
		?>
    </fieldset>
    <p style='text-align: center;'>
        <button type="button" id="aceptar" onclick="cerrar_vent()" name="Aceptar">Aceptar</button>
    </p>
</form>