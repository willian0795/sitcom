<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/199s9/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <?php if($base){
     echo '<link href="'.base_url().'/css/bootstrap2.css" rel="stylesheet" type="text/css" />';
 } ?>

</head>
<body>
    <table align="center" border="0" cellspacing="0" style="width:100%;">
        <tr>
            <td align="left" id="imagen">
          <img src="<?php if($base){ echo base_url();} ?>img/mtps_report2.jpg" width="175" height="106" />
            </td>
            <td align="right">
                <h3>REPORTE DE VALES DE COMBUSTIBLE</h3>
                <h6>Fecha: <strong><?php echo date('d-m-Y'); ?></strong> </h6>
            </td>
        </tr>
        <tr>
          <td colspan="2" align="center">
              <strong id="titulo">Liquidacion del mes de <?php echo $mesn; ?></strong><br>
              <strong id="titulo2"> <?php echo $seccion; ?></strong>
            </td>
        </tr>
    </table>
    <br>
<!---------------------------------------------------------------------------------- -->
<table align="center" class='table_design' border="1" width="100%">

  <thead>
  <tr style="background-color: #001434;">
    <th style="color: white; padding: 4px;">VALES RECIBIDOS <?php echo strtoupper($mesn); ?></th>
    <th style="color: white; padding: 4px;">DEL</th>
    <th style="color: white; padding: 4px;">AL</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($s_entregada as $key) {  ?>
  <tr>
    <td style="padding: 4px;" aling="center"><?php echo $key['cant']; ?></td>
    <td style="padding: 4px;"><?php echo $key['inicial']; ?></td>
    <td style="padding: 4px;"><?php echo $key['final']; ?></td>
  </tr>
  <?php }
      if(sizeof($s_entregada)==0) echo $vacio;
  ?>
  </tbody>
  <thead>
  <tr style="background-color: #001434;">
    <th style="color: white; padding: 4px;">SOBRANTES <?php echo $mesn1; ?></th>
    <th style="color: white; padding: 4px;"></th>
    <th style="color: white; padding: 4px;"></th>
  </tr>
  </thead>
  <tbody>
  <?php  $tsobrant=0;// foreach ($recibidos as $key) {  ?>
  <?php foreach ($s_sobrante as $key) {  
      $tsolicit+=$key['cant'];
    ?>
  <tr>
    <td style="padding: 4px;" aling="center"><?php echo $key['cant']; ?></td>
    <td style="padding: 4px;"><?php echo $key['inicial']; ?></td>
    <td style="padding: 4px;"><?php echo $key['final']; ?></td>
  </tr>
  <?php }
    if(sizeof($s_sobrante)==0) echo $vacio;
  ?>
  </tbody>
  </table>
  
<!---------------------------------------------------------------------------------- -->
  <br>
  <table align="left" class='table_design' style="width:400px" id="utiliza" border="1">
  <thead>
  <tr style="background-color: #001434;">
    <th style="color: white; padding: 4px;" colspan="2">DETALLE DE VALES UTILIZADOS</th>
  </tr>
  <tr style="background-color: #001434;">
    <th style="color: white; padding: 4px;">APLICADO EN</th>
    <th style="color: white; padding: 4px;">CANTIDAD</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($consumo_clase as $key) { $tconsumido+=$key['cant']; ?>
  <tr>
    <td style="padding: 4px;"><?php echo $key['clase']; ?></td>
    <td style="padding: 4px;"><?php echo $key['cant']; ?></td>
  </tr>
  <?php }?>
  <tr style="background-color: #b5dff9;">
    <th style="padding: 4px;" aling="center"><strong>TOTAL</strong></th>
    <th style="padding: 4px;"><strong><?php echo $tconsumido; ?></strong></th>
  </tr>
  </tbody>
</table>
<br>
<!---------------------------------------------------------------------------------- -->
<table align="center" class='table_design' border="1" width="100%">

  <thead>
  <tr style="background-color: #001434;">
    <th style="color: white; padding: 4px;">VALES UTILIZADOS</th>
    <th style="color: white; padding: 4px;">DEL</th>
    <th style="color: white; padding: 4px;">AL</th>
  </tr>
  </thead>
  <tbody>
  <?php
     foreach ($s_consumida as $key) {  ?>
  <tr>
    <td style="padding: 4px;" aling="center"><?php echo $key['cant']; ?></td>
    <td style="padding: 4px;"><?php echo $key['inicial']; ?></td>
    <td style="padding: 4px;"><?php echo $key['final']; ?></td>
  </tr>
  <?php }
    if(sizeof($s_consumida)==0) echo $vacio;
  ?>
  </tbody>
  <thead>
  <tr style="background-color: #001434;">
    <th style="color: white; padding: 4px;"> VALES SOBRANTES</th>
    <th style="color: white; padding: 4px;"></th>
    <th style="color: white; padding: 4px;"></th>
  </tr>
  </thead>
  <tbody>
  <?php 
    $tso=0;
  foreach ($s_sobrante2 as $key) {  
    $tso+=$key['cant'];
    ?>
  <tr>
    <td style="padding: 4px;" aling="center"><?php echo $key['cant']; ?></td>
    <td style="padding: 4px;"><?php echo $key['inicial']; ?></td>
    <td style="padding: 4px;"><?php echo $key['final']; ?></td>
  </tr>
  <?php }
    if(sizeof($s_sobrante2)==0) echo $vacio;
  ?>

 
  </tbody>
  </table>
  
<!---------------------------------------------------------------------------------- -->
  <br>
  <table class="table_design" style="width:300px;" id="final" border="1">
  <tr>
    <td style="padding: 4px;">SOBRANTES DE <?php echo strtoupper($mesn);?></td>
    <td style="padding: 4px;"><?php echo $tso; ?></td>
  </tr>
  <tr>
    <td style="padding: 4px;">SOLICITADOS PARA <?php echo strtoupper($mesn2);?></td>
    <td style="padding: 4px;"><?php $tsolicit=$asignado-$tso; echo $tsolicit; ?></td>
  </tr>
  <tr style="background-color: #b5dff9;">
    <th style="padding: 4px;">TOTAL DE VALES</th>
    <th style="padding: 4px;"><strong><?php echo $tsolicit+$tso; ?></strong></th>
  </tr>
</table>
