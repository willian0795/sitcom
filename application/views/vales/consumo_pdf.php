
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/199s9/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script src="<?php echo base_url()?>js/jquery-1.8.2.js"></script>
    <link href="<?php echo base_url()?>css/style-base.css" rel="stylesheet" type="text/css" />
        <?php

     echo '<link href="'.base_url().'/css/bootstrap2.min.css" rel="stylesheet" type="text/css" />';
  ?>
</head>
<script type="text/javascript">
    function reporte(){
        tabla(<?php echo $j; ?>)      
    } 
</script>
<body onload="reporte();">
<form action="<?php echo base_url();?>index.php/vales/consumo_pdf_dos" method="POST">
    <input type="hidden" id="html" name="html">
    <button type="submit" id="submit">imprimir</button>
</form>
<div id="vista">
<table style="width: 100%;">
        <tr style="font-size: 20px; vertical-align: middle; font-family: "Poppins", sans-serif;">
            <td width="110px" colspan="2"><img src="<?php echo base_url(); ?>img/logo_izquierdo.jpg" width="110px"></td>
            <td align="center" style="font-size: 13px; font-weight: bold; line-height: 1.3;">
                <h6>MINISTERIO DE TRABAJO Y PREVISIÓN SOCIAL</h6>
                <h6>DEPARTAMENTO DE SERVICIOS GENERALES</h6>
                <h6>REPORTE DE VALES DE COMBUSTIBLE</h6>
            </td>
            <td width="110px" align="right"><img src="<?php echo base_url(); ?>img/logo_derecho.jpg"  width="110px"></td>
        </tr>
    </table>
  <div id="datos">
    </div>
</div>
    
</body>
<script language="javascript" >
function imprimir() {
    var html = $("#vista").html();
    $("#html").val(html);
    $("#submit").click();
    setTimeout( function(){ window.close(); } , 500);
}
setTimeout ("imprimir();", 500);
///llamada al finalizar la contrucion del archivo
</script>
</html>
<script src="<?php echo base_url()?>js/views/reporte_consumo.js" type="text/javascript"></script>