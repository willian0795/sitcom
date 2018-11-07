<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <table style="width: 100%;">
        <tr style="font-size: 20px; vertical-align: middle; font-family: "Poppins", sans-serif;">
            <td width="110px"><img src="<?php if($base){ echo base_url();} ?>img/logo_izquierdo.jpg" width="110px"></td>
            <td align="center" style="font-size: 18px; font-weight: bold; line-height: 1.3;">
                <h6>MINISTERIO DE TRABAJO Y PREVISIÓN SOCIAL</h6>
                <h6>DEPARTAMENTO DE SERVICIOS GENERALES</h6>
                <h6>SOLICITUD DE USO DE VEHICULO</h6>
            </td>
            <td width="110px"><img src="<?php if($base){ echo base_url();} ?>img/logo_derecho.jpg"  width="110px"></td>
        </tr>
        <tr>
            <td align="right" colspan="3">
                <h5>Solicitud: <strong style="font-size: 18px;"><?php echo $info_solicitud['id_solicitud_transporte']; ?><br></strong> </h5>
            </td>
        </tr>
        <tr>
            <td align="center" colspan="3">
                <strong id="titulo">DATOS DE SOLICITANTE</strong>
            </td>
        </tr>
    </table>
    <table align="center" class="tabla" cellspacing="0">    
        <tr>
        	<td colspan="2" align="center">
            	<?php 
					$fec=explode("/",$info_solicitud['fecha_mision']);
					//switch($fec[1]) {
                    switch(date("m")){
						case 1: 
							$mes="Enero";
							break;
						case 2:
							$mes="Febreo";
							break;
						case 3: 
							$mes="Marzo";
							break;
						case 4: 
							$mes="Abril";
							break;
						case 5: 
							$mes="Mayo";
							break;
						case 6: 
							$mes="Junio";
							break;
						case 7: 
							$mes="Julio";
							break;
						case 8: 
							$mes="Agosto";
							break;
						case 9: 
							$mes="Septiembre";
							break;
						case 10: 
							$mes="Octubre";
							break;
						case 11: 
							$mes="Noviembre";
							break;
						case 12: 
							$mes="Diciembre";
							break;
					}
					
					switch($id_seccion)
					{
						case 52:
						$depto="Ahuachapán";
						break;
						case 53:
						$depto="Cabañas";
						break;
						case 54:
						$depto="Chalatenango";
						break;
						case 55:
						$depto="Cuscatlán";
						break;
						case 56:
						$depto="La Libertad";
						break;
						case 57:
						$depto="La Unión";
						break;
						case 58:
						$depto="Morazán";
						break;
						case 59:
						$depto="San Vicente";
						break;
						case 60:
						$depto="Sonsonate";
						break;
						case 61:
						$depto="Usulután";
						break;
						case 64:
						$depto="Zacatecoluca";
						break;
						case 65:
						$depto="San Miguel";
						break;
						case 66:
						$depto="Santa Ana";
						break;
						default:
						$depto="San Salvador";
						break;
					}
					
				?>
            <?php echo strtoupper($depto); ?>, 
            <?php echo date("d"); //echo $fec[0] ?> DE
            <?php echo strtoupper($mes." ".date("Y")); //$fec[2]?> 
            </td>
        </tr>
        <tr>
        	<td colspan="2" align="left">
            	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NR: <strong><?php echo $info_empleado['nr'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
            	Nombre: <strong><?php echo strtoupper($info_solicitud['nombre']) ?></strong>
            </td>
        </tr>
        <tr>
        	<td align="left">
                <table>
                    <tr>
                        <td colspan="2" style="padding-left: 20px;">
                            Departamento: <strong><?php if($info_empleado['nivel_2']!="") echo $info_empleado['nivel_2']."."; else echo "_____________________________________________________________________________________________________________________";?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-left: 20px;">
                            Secci&oacute;n: <strong><?php if($info_empleado['nivel_1']!="") echo $info_empleado['nivel_1']."."; else echo "____________________________________________________________________________________________________________________________";?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-left: 20px;">
                            Acompa&ntilde;ante(s): 
                            <strong>
                                <?php 
                                    $i=0;
                                    $cadena="";
                                    foreach($acompanantes as $val) {
                                        $x=substr(ucwords($val->nombre), 0, -1);
                                        if($i==1)
                                            $cadena.=", ";
                                        $cadena.=$x;
                                        $i=1;
                                    }
                                    echo strtoupper($cadena);
                                    if($i==1 && $info_solicitud['acompanante']!="")
                                        echo ", ";
                                    echo strtoupper($info_solicitud['acompanante']);
                                    if($i==1 || $info_solicitud['acompanante']!="")
                                        echo ".";
                                    else
                                        echo "NINGUNO.";
                                ?>
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-left: 20px;">
                            Lugar de destino: 
                            <strong>
                                <?php 
                                    if(count($destinos)>1) {
                                        echo "VER ATRÁS";
                                    }
                                    else {
                                        foreach($destinos as $val) {
                                            echo strtoupper($val->destino.", ".$val->municipio).".";
                                        }
                                    }
                                ?>
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-left: 20px;">
                            Misi&oacute;n encomendada:  
                            <strong>
                                <?php 
                                    if(count($destinos)>1) {
                                        echo "VER ATRÁS";
                                    }
                                    else {
                                        foreach($destinos as $val) {
                                            echo strtoupper($val->mision).".";
                                        }
                                    }
                                ?>
                            </strong>
                        </td>
                    </tr>
                </table>
            </td>
        	<td align="left">
            	<table align="right">
                	<tr>
                    	<td width="125">
                        </td>
                    	<td class="titu" align="center" width="90">
                        	SEG&Uacute;N SOLICITADO
                        </td>
                    	<td class="titu" align="center" width="90">
                        	DATOS REALES
                        </td>
                    </tr>
                	<tr>
                    	<td align="right">
                        	Fecha de la Misi&oacute;n:
                        </td>
                    	<td align="center">
                        	<strong><?php echo $info_solicitud['fecha_mision'] ?></strong>
                        </td>
                    	<td align="center">
                        	<strong><?php echo $salida_entrada_real['fecha_mision'] ?></strong>
                        </td>
                    </tr>
                	<tr>
                    	<td align="right">
                        	Hra. Salida a Misi&oacute;n:
                        </td>
                    	<td align="center">
                        	<strong><?php echo $info_solicitud['hora_salida'] ?></strong>
                        </td>
                    	<td align="center">
                        	<strong><?php echo $salida_entrada_real['hora_salida']?></strong>
                        </td>
                    </tr>
                	<tr>
                    	<td align="right">
                        	Hra. Regreso a Misi&oacute;n:
                        </td>
                    	<td align="center">
                        	<strong><?php echo $info_solicitud['hora_entrada'] ?></strong>
                        </td>
                    	<td align="center">
                        	<strong><?php echo $salida_entrada_real['hora_entrada'] ?></strong>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr><td colspan="2">&nbsp;</td></tr>
        <!--<tr>
        	<td align="center" style="width:50%">
            	f. _____________________________________________<br />
            	Solicitado Por <strong><?php echo ucwords($info_solicitud['nombre']) ?></strong><br />&nbsp;
            </td>
        	<td align="center" style="width:50%">
            	f. _____________________________________________<br />
            	Autorizado Por <strong><?php echo ucwords($info_solicitud['nombre2']) ?></strong><br />Jefe de Departamento o Sección
            </td>
        </tr>-->
        <tr>
        	<td colspan="2" align="center">
            	APROBADO POR <strong><?php echo ucwords($info_solicitud['nombre2']) ?></strong><br /><?php echo ucwords($info_empleado2['funcional']) ?><br />Fecha aprobaci&oacute;n: <?php echo $info_solicitud['fecha_aprobacion'] ?> a las <?php echo $info_solicitud['hora_aprobacion'] ?> 
            </td>
        </tr>
    </table>
    <table align="center" class="tabla2" cellspacing="0">
    	<tr><td colspan="2">&nbsp;</td></tr>
    	<tr>
        	<td align="center" style="width:60%">
            	<strong>USO EXCLUSIVO SERVICIOS GENERALES</strong>
                 <table align="center" class="tabla" cellspacing="0" style="width: 80%;">
                 	<tr>
                    	<td style="border-bottom: 1px solid #000;" align="center">
                        	<strong>AUTORIZACION  DE VEHICULO A  MISION  OFICIAL</strong>
                        </td>
                    </tr>
        			<tr><td>&nbsp;</td></tr>
                 	<tr>
                    	<td>
                        	Motorista: <strong><?php echo strtoupper($motorista_vehiculo['nombre']) ?></strong>
                        </td>
                    </tr>
                 	<tr>
                    	<td>
                        	No. placa del veh&iacute;culo: <strong><?php echo $motorista_vehiculo['placa'] ?></strong>
                        </td>
                    </tr>
                 	<tr>
                    	<td>
                        	Clase del veh&iacute;culo: <strong><?php echo strtoupper($motorista_vehiculo['nombre_clase']) ?></strong>
                        </td>
                    </tr>
        			<tr><td>&nbsp;</td></tr>
        			<tr><td>&nbsp;</td></tr>
        			<tr><td>&nbsp;</td></tr>
        			<tr><td>&nbsp;</td></tr>
        			<tr><td>&nbsp;</td></tr>
                    <tr>
                        <td align="center">
                           	<!--f. _____________________________________________<br />-->
                            Asignado por <strong><?php echo ucwords($motorista_vehiculo['nombre2']) ?> </strong><br /><?php echo ucwords($info_empleado3['funcional']) ?><br />Fecha asignaci&oacute;n: <?php echo $motorista_vehiculo['fecha_asignacion'] ?> a las <?php echo $motorista_vehiculo['hora_asignacion'] ?> 
                        </td>
                  	</tr>
                 </table>
            </td>
        	<td align="center" style="width:40%">
                <!--<strong>USO EXCLUSIVO PORTERIA</strong> -->
            	
                 <table align="center"  class="tabla" cellspacing="0" style="width:1000px">
                    <tr>
                    	<td>&nbsp;</td>
                    	<td></td>
                        <td rowspan="9" align="right">
                            
                        	<img src="img/marcador_combustible<?php echo $salida_entrada_real['combustible'] ?>.jpg" style="width:20%;height:auto; margin-right: 10px; padding-top: 10px;" /><br>
                            <span style="font-size: 10px;">Remanente de combustible que queda<br>
                                                            al final de la misión en el vehículo.</span>
                        </td>
                   	</tr>
                 	<tr>
                    	<td >
                        	Kilometraje inicial:
                        </td>
                    	<td style="width:13%;" <?php if($salida_entrada_real['km_inicial']=="") echo 'class="titu"';?>>
                        	<strong><?php echo $salida_entrada_real['km_inicial'] ?></strong>
                            
                        </td>
                    </tr>
                 	<tr>
                    	<td >
                        	kilometraje final:
                        </td>
                    	<td <?php if($salida_entrada_real['km_final']=="") echo 'class="titu"';?>>
                        	<strong><?php echo $salida_entrada_real['km_final'] ?></strong>
                        
                        </td>
                    </tr>
                 	<tr>
                    	<td>
                        	Kms. recorridos:
                        </td>
                    	<td <?php if($salida_entrada_real['km_final']=="") echo 'class="titu"';?>>
                        	<strong><?php echo $salida_entrada_real['total'] ?></strong>
                        </td>
                    </tr>
                    <!-- <tr>
                    	<td>&nbsp;</td>
                    	<td></td>
                   	</tr>
                   <tr>
                        
                    	<td>No. placa veh&iacute;culo:</td>
                    	<td><strong><?php #echo $motorista_vehiculo['placa'] ?></strong></td>
                   	</tr>-->
                    <tr>
                    	<td>&nbsp;</td>
                    	<td></td>
                   	</tr>
                    <tr>
                    	<td>&nbsp;</td>
                    	<td></td>
                   	</tr>
                    <tr>
                    	<td>&nbsp;</td>
                    	<td></td>
                   	</tr>
                    <tr>
                    	<td>&nbsp;</td>
                    	<td></td>
                   	</tr>
                    <tr>
                    
                    	<td colspan="3">&nbsp;</td>
                   	</tr>
                    <tr>
                        <td align="center" colspan="3">
                            <br><br><br>
                            F. _____________________________________________<br />
                            Motorista
                        </td>
                  	</tr>
                 </table>
            </td>
        </tr>
    </table><br>
    <table align="left" border="0" cellspacing="0" style="width:100%;">
        <tr>
            <td align="left" style="width:60%">
                 <table align="left" class="tabla" cellspacing="0">
                    <tr>
                        <td><h5 align="center"><strong>USO EXCLUSIVO SERVICIOS GENERALES</strong></h5></td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #000;" align="center" colspan="2">
                            <strong>AUTORIZACION  DE VEHICULO A  MISION  OFICIAL</strong>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td style="padding-left: 15px;" colspan="2">
                            <br>Motorista: ____________________________________________________________
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-left: 15px; width: 60%">
                            <br>No. placa del veh&iacute;culo: ________________________
                        </td>
                        <td style="padding-left: 15px; width: 40%">
                            <br>Kilometraje inicial: ___________
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-left: 15px; width: 60%">
                            <br>Clase del veh&iacute;culo: ____________________________
                        </td>
                        <td style="padding-left: 15px; width: 40%">
                            <br>Kilometraje final: _____________
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td align="center" colspan="2">
                            <!--f. _____________________________________________<br />-->
                            Asignado por <strong><?php echo ucwords($motorista_vehiculo['nombre2']) ?> </strong><br /><?php echo ucwords($info_empleado3['funcional']) ?><br />Fecha asignaci&oacute;n: <?php echo $motorista_vehiculo['fecha_asignacion'] ?> a las <?php echo $motorista_vehiculo['hora_asignacion'] ?> 
                        </td>
                    </tr>
                 </table>
            </td>
            <td align="center" style="width:40%; height:200px; vertical-align:bottom;">
                F.<img src="img/firma_gerencia.png"/><br />
                <div style="text-decoration: overline ;">Direcci&oacute;n Administrativa</div>
            </td>
        </tr>
    </table><br>
    <table align="center" border="0" cellspacing="0" style="width:100%;">
    	<tr>
        	<td style="vertical-align:top; font-size: 13px;">
				<br>
				<?php
                    foreach($observaciones as $val) {
                        switch($val['quien_realiza']) {
                            case 1:
                                $quien="Observaciones por parte de la persona que solicita";
                                break;
                            case 2:
                                $quien="Observaciones por parte del Jefatura de Departamento o Secci&oacute;n";
                                break;
                            case 3:
                                $quien="Observaciones por parte del Jefatura de Transporte";
                                break;
                            
                            default:
                                $quien="General";
                        }
                        echo $quien.":<br><ul><li><strong>".strtoupper($val['observacion']).".</strong></li></ul>";			
                    }
                ?>
            </td>
        </tr>
    </table>
</body>
</html>

























