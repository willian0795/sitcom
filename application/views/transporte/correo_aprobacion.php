<?php  

$usuarioe= md5($id_usuario);
$solicitude= md5($id_solicitud_transporte);
$url=base_url()."index.php/peticiones/aprobar_solicitud/".$usuarioe."/".$solicitude;
$aprobar=md5(1);
$denegar=md5(0);

?>

<html>
	<head>
<style type="text/css">
	.table_design {
	width: 90%;
	border:0;
	outline:0;
	font-size:100%;
	vertical-align:baseline;
	background:transparent;
	font:12px/15px "Helvetica Neue",Arial, Helvetica, sans-serif;
	color: #555;
	overflow:hidden;
	border:1px solid #d3d3d3;
	background:#fefefe;
	-moz-border-radius:5px; /* FF1+ */
	-webkit-border-radius:5px; /* Saf3-4 */
	border-radius:5px;
	-moz-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
	-webkit-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
}

.table_design th, .table_design td {
	padding:5px 10px 5px;
}
button {
	display: inline-block;
	margin: 15px auto;
	padding: 5px;
	text-decoration: none;
	text-align: center;
	font: bold 13px Verdana, Arial, Helvetica, sans-serif;
	width: 150px;
	color: #FFF;
	cursor: pointer;
	outline-style: none;
	background-color: #5A5655;
	border: 1px solid #5A5655;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
}
.table_design th {
	text-shadow: 1px 1px 1px #fff; 
	background:#e8eaeb;
	text-align:center;
	background: -moz-linear-gradient(100% 20% 90deg, #e8eaeb, #ededed);
	background: -webkit-gradient(linear, 0% 0%, 0% 20%, from(#ededed), to(#e8eaeb));
}	

.table_design td {
	border-top:1px solid #e0e0e0; 
	border-right:1px solid #e0e0e0;
	background: -moz-linear-gradient(100% 25% 90deg, #fefefe, #f9f9f9);
	background: -webkit-gradient(linear, 0% 0%, 0% 25%, from(#f9f9f9), to(#fefefe));
}

</style>

	</head>	
			
	
	<body>
			Estimada/Estimado <?php echo $nombre; ?>,<br><br>
				<pre>
				<? //print_r($solicitud); ?>
				</pre>
			<p>
			La solicitud N&deg;<strong> <?php echo $id_solicitud_transporte; ?> </strong> realizada por <strong><?php echo ucwords($solicitud['nombre']); ?> </strong>
			para el d&iacute;a <strong><? echo $solicitud['fecha_mision']?></strong>  en horario de <strong><?=$solicitud['hora_salida']?></strong> a <strong><?=$solicitud['hora_entrada']?></strong>
			 requiere de su autorizaci&oacute;n.
			</p>
			<p>
				      
					<?php 
						echo utf8_decode("Acompañantes: <br>");
						      foreach($a as $acompa)
			        {
			            echo "<strong>".ucwords($acompa->nombre)."</strong> <br />";
			        }
			        echo "<strong>".$acompanante."</strong>";
			        if(count($acompa)==0 && $acompanante=="")
			            echo "<strong>(No especificado)</strong>";
			    ?>	
			</p>

				Destino(s): <br><br>
        <table cellspacing='0' align='center' class='table_design'>
            <thead>
                <th>
                    Municipio
                </th>
                <th>
                    Lugar de destino
                </th>
                <th>
                    Direcci&oacute;n
                </th>
                <th>
                    Misi&oacute;n Encomendada
                </th>
            </thead>
            <tbody>
				<?php  

                foreach($f as $r)
                {
                    echo "<tr><td>".utf8_decode(ucwords($r->municipio))."</td>";
                    echo "<td>".utf8_decode($r->destino)."</td>";
                    echo "<td>".utf8_decode($r->direccion)."</td>";
                    echo "<td>".utf8_decode($r->mision)."</td></tr>";
                }
				?>
            </tbody>
        </table>
			<p>  Observaciones: <br><br>
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
                echo $quien.":<br><li><strong>".$val['observacion'].".</strong></li><br>";					
            }
            if(count($observaciones)==0)
                echo "<strong>(No hay observaciones)</strong>";
		?> </p>
			<p>
				Presione click en el bot&oacute;n "Aprobar" para autorizar la solicitud <br>
				Presione click en el bot&oacute;n "Denegar" para rechazar la solicitud <br>
			</p>

			<a href="<?php echo $url.'/'.$aprobar; ?> " target="blank"><button>Aprobar</button></a>
			<a href="<?php echo $url.'/'.$denegar; ?> " target="blank"><button>Denegar</button></a>

			<br><br>Departamento de Transporte.   Solicitud realizada el<strong> <? echo $solicitud['fecha_creacion']?></strong>

	</body>


</html>



