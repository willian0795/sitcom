<?php  

$usuarioe= md5($id_usuario);
$solicitude= md5($id_solicitud_transporte);
$url=base_url()."index.php/peticiones/aprobar_solicitud/".$usuarioe."/".$solicitude;
$aprobar=md5(1);
$denegar=md5(0);

?>

<html>	
<body>
	<div style="background-color:#F5F6F8; padding: 15px;">

		<div style="background-color: #FFFFFF; max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 4px; border: 1px solid #e1e1e1;">
			<h2 style="font-family: arial,verdana,sans-serif; margin-top: 0;">¡Bienvenida/o al Sistema de Transporte!</h2>
			<p style="font-family: arial,verdana,sans-serif;">Estimada/o <?= $nombre ?>:</p>
			<p style="font-family: arial,verdana,sans-serif;">
				La solicitud No <?= $id_solicitud_transporte ?> realizada por <?= ucwords($solicitud['nombre']) ?> para el <?= $solicitud['fecha_mision']?> 
				en el horario de <?=$solicitud['hora_salida']?> a <?=$solicitud['hora_entrada']?> requiere su aprobación.
			</p>

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

			<!-- <p style="font-family: arial,verdana,sans-serif;">La solicitud se realizó el 31/08/2018 a las 8:00a.m.</p> -->

			<br>
			<table>
				<tr>
					<td>
						<div style="font-family: arial,verdana,sans-serif; text-align: center; width: 100px; height: 20px; background-color: #1E88E5; border-radius: 4px; border: 1px solid #e1e1e1; padding: 10px; display: block;"><a style="color: #FFFFFF; text-decoration: none;" href="<?= $url.'/'.$aprobar; ?>" title="Validar cuenta de usuario">Aprobar</a>
						</div>
					</td>
					<td>
						<div style="font-family: arial,verdana,sans-serif; text-align: center; width: 100px; height: 20px; background-color: #FC4B6C; border-radius: 4px; border: 1px solid #e1e1e1; padding: 10px; display: block;"><a style="color: #FFFFFF; text-decoration: none;" href="<?= $url.'/'.$denegar; ?>" title="Validar cuenta de usuario">Denegar</a>
						</div>
					</td>
				</tr>
			</table>
			<br><br>

			<p style="font-family: arial,verdana,sans-serif;">Atentamente,</p>
			<p style="font-family: arial,verdana,sans-serif;">Departamento de Transporte</p>
			<p style="font-family: arial,verdana,sans-serif;">Dirección Administrativa</p>
			<img style="height: 106px; width: 173px;" src="<?= base_url() ?>img/logo_izquierdo.jpg">

		</div>
	</div>

</body>
</html>



