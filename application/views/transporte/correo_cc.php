<?php  

$usuarioe= md5($id_usuario);
$solicitude= md5($id_solicitud_transporte);
/*$url=base_url()."index.php/peticiones/aprobar_solicitud/".$usuarioe."/".$solicitude;
$aprobar=md5(1);
$denegar=md5(0);
*/
?>

<html>	
<body>
	<div style="background-color:#F5F6F8; padding: 15px;">

		<div style="background-color: #FFFFFF; max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 4px; border: 1px solid #e1e1e1;">
			<h2 style="font-family: arial,verdana,sans-serif; margin-top: 0;">¡Bienvenida/o al Sistema de Transporte!</h2>
			<p>
            <?php
			if($bandera==1)
			{
			?>
			<p style="font-family: arial,verdana,sans-serif;">
				Se le notifica que la solicitud N&deg;<strong> <?php echo $id_solicitud_transporte; ?> </strong> realizada por <strong><?php echo ucwords($solicitud['nombre']); ?> </strong>
				para el d&iacute;a <strong><? echo $solicitud['fecha_mision']?></strong>  en horario de <strong><?=$solicitud['hora_salida']?></strong> a <strong><?=$solicitud['hora_entrada']?></strong>.
				Ha sido enviada al Jefe de Transporte directamente, para su próxima asignación de vehículo y motorista(en caso sea requerido).
			</p>
			<?php
			}
			elseif($bandera==2)
			{
			?>
			<p style="font-family: arial,verdana,sans-serif;">
            	Se le notifica que ha sido creada la solicitud de transporte N&deg;<strong> <?php echo $id_solicitud_transporte; ?> </strong> realizada por <strong><?php echo ucwords($solicitud['nombre']); ?> </strong>
				para el d&iacute;a <strong><? echo $solicitud['fecha_mision']?></strong>  en horario de <strong><?=$solicitud['hora_salida']?></strong> a <strong><?=$solicitud['hora_entrada']?></strong>.
			</p>
            <?php
			}
			?>
            </p>
			<p>

			
			<p style="font-family: arial,verdana,sans-serif;">Acompañante(s):</p>

				<ul style="font-family: arial,verdana,sans-serif;">
					<?php
						foreach($a as $acompa) {
							echo "<li>".ucwords($acompa->nombre)."</li>";
						}
					?>
					<!-- <li><?= $acompanante ?></li> -->
					<?php
						if(count($acompa)==0 && $acompanante=="")
							echo '<p style="font-family: arial,verdana,sans-serif;">No especificado</p>';
					?>
				</ul>
			</p>

			<p style="font-family: arial,verdana,sans-serif;">Destino(s):</p>
			<table style="font-family: arial,verdana,sans-serif; text-align: center; border-collapse: collapse; border: #26C6DA 1px solid;">
				<tbody>
					<tr style="background-color: #26C6DA; color: #FFFFFF; font-size: 13px;">
						<th style="padding: 5px; width: 100px;">Municipio</th>
						<th style="padding: 5px; width: 125px;">Lugar de destino</th>
						<th style="padding: 5px; width: 250px;">Dirección</th>
						<th style="padding: 5px; width: 100px;">Misión encomendada</th>
					</tr>

					<?php  

						foreach($f as $r) {
							echo '<tr style="font-size: 13px;"><td style="padding: 5px; width: 100px; border: #26C6DA 1px solid;">'.utf8_decode(ucwords($r->municipio)).'</td>';
							echo '<td style="padding: 5px; width: 100px; border: #26C6DA 1px solid;">'.utf8_decode($r->destino)."</td>";
							echo '<td style="padding: 5px; width: 100px; border: #26C6DA 1px solid;">'.utf8_decode($r->direccion)."</td>";
							echo '<td style="padding: 5px; width: 100px; border: #26C6DA 1px solid;">'.utf8_decode($r->mision)."</td></tr>";
						}
					?>
				</tbody>	
			</table>

			<p style="font-family: arial,verdana,sans-serif;">Observaciones:</p>
			<ul style="font-family: arial,verdana,sans-serif;">		
			<?php
				if(count($observaciones)!=0)
					foreach($observaciones as $val) {
						switch($val['quien_realiza']) {
							case 1:
								$quien="Por parte del solicitante";
								break;
							case 2:
								$quien="Por parte del Jefe de Departamento o Secci&oacute;n";
								break;
							case 3:
								$quien="Por parte del Jefe de Servicios Generales";
								break;
							default:
								$quien="General";
						}
						echo "<li>" . $quien.":".$val['observacion'].".</li>";					
					}
				if(count($observaciones)==0)
					echo '<p style="font-family: arial,verdana,sans-serif;">No hay observaciones</p>';
			?>
			</ul>
			
			<p style="font-family: arial,verdana,sans-serif;">Atentamente,</p>
			<p style="font-family: arial,verdana,sans-serif;">Departamento de Transporte</p>
			<p style="font-family: arial,verdana,sans-serif;">Dirección Administrativa</p>
			<img style="height: 106px; width: 173px;" src="<?= base_url() ?>img/logo_izquierdo.jpg">

		</div>
	</div>

</body>
</html>