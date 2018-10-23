<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/199s9/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <?php if($base){
     echo '<link href="'.base_url().'/css/bootstrap2.min.css" rel="stylesheet" type="text/css" />';
 } ?>
</head>
<body>

    <table style="width: 100%;">
        <tr style="font-size: 20px; vertical-align: middle; font-family: "Poppins", sans-serif;">
            <td width="110px"><img src="<?php if($base){ echo base_url();} ?>img/logo_izquierdo.jpg" width="110px"></td>
            <td align="center" style="font-size: 13px; font-weight: bold; line-height: 1.3;">
                <h6>MINISTERIO DE TRABAJO Y PREVISIÓN SOCIAL</h6>
                <h6>DEPARTAMENTO DE SERVICIOS GENERALES</h6>
                <h6>REPORTE DE VALES DE COMBUSTIBLE</h6>
            </td>
            <td width="110px"><img src="<?php if($base){ echo base_url();} ?>img/logo_derecho.jpg"  width="110px"></td>
        </tr>
    </table>
    <br>
    <table align="center" border="1" cellspacing="0" class='table_design' width="100%">    
        <thead>
        <tr>
            <th colspan="7" align="center" style="padding: 5px; font-size: 16px;">
                Liquidación del mes de <?php echo $mesn ?>
            </th>
        </tr>
        <tr>
            <th style="width: 400px;" align="center">Oficina</th>
            <th align="center">Sobrante  <br><?php echo ucwords(mb_strtolower($mesn1));?></th>
            <th align="center">Entregado <br><?php echo $mesn;?></th>  
            <th align="center">Disponibles</th>
            <th align="center">Consumidos<br> <?php echo $mesn;?></th>
            <th align="center">Sobrante<br>  <?php echo $mesn;?> </th>
            <th align="center">Consumido ($)<br> <?php echo $mesn;?></th>                                                    
        </tr>
        </thead>
        <tbody> 
    <?php 

    foreach ($l as $key ) {
        $s1+=$key['sobrantes_anterior'];
        $s2+=$key['asignado'];
        $s3+=$key['disponibles'];
        $s4+=$key['consumidos'];
        $s5+=$key['sobrantes_despues'];
        $s6+=$key['dinero'];
    ?>          
        <tr>
            <td style="padding: 5px;"><?php echo $key['seccion']; ?></td>
            <td style="padding: 5px;"><?php echo $key['sobrantes_anterior']; ?></td>
            <td style="padding: 5px;"><?php echo $key['asignado']; ?></td>
            <td style="padding: 5px;"><?php echo $key['disponibles']; ?></td>
            <td style="padding: 5px;"><?php echo $key['consumidos']; ?></td>
            <td style="padding: 5px;"><?php echo $key['sobrantes_despues']; ?></td>
            <td style="padding: 5px;"><?php echo $key['dinero']; ?></td>                                 
        </tr>
    <?php 
            }
        ?>
         <tr>
            <th style="padding: 5px;"><strong>TOTAL</strong></th>
            <th style="padding: 5px;"> <strong><?php echo $s1;?></strong></th>
            <th style="padding: 5px;"> <strong><?php echo $s2;?></strong></th>
            <th style="padding: 5px;"> <strong><?php echo $s3;?></strong></th>
            <th style="padding: 5px;"> <strong><?php echo $s4;?></strong></th>
            <th style="padding: 5px;"> <strong><?php echo $s5;?></strong></th>  
            <th style="padding: 5px;"> <strong><?php echo $s6;?></strong></th>                             
        </tr>          
    </tbody>
    </table>
    
</body>
        <br><br>
        <p style="width:650px; margin:auto;"> <?php ?></p>

</html>