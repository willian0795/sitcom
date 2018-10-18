
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
      
       //grafico(<?php echo $j; ?>,"seccion");//contructor del grafico
        tabla(<?php echo $j; ?>)      
        } 
</script>
<body onload="reporte();">
<form action="<?php echo base_url();?>index.php/vales/consumo_pdf_dos" method="POST">
    <input type="text" id="html" name="html">
    <button type="submit" id="submit">imprimir</button>
</form>
  <div id="datos">
    </div>
    <br>
<div style="height: 700px; background:#FFFFFF;" id="chartdiv">
</div>
    
</body>
<script language="javascript" >
function imprimir() {
    var html = $("#datos").html();
    $("#html").val(html);
    //$("#submit").click();
}
setTimeout ("imprimir();", 500);
///llamada al finalizar la contrucion del archivo
</script>
    <!------------------------------------------Plantilla de carga de grafico y tabla----------------------------------------------------------------------- -->
    


    <!----------------------------------------------------------------------------------------------------------------- -->
</html>
<script src="<?php echo base_url()?>js/views/reporte_consumo.js" type="text/javascript"></script>