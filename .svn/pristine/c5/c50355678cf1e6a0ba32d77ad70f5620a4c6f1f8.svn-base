<?php
header('Content-Type: text/html; charset=UTF-8'); 
?>
<html>
    <head>
        <link href="<?php echo base_url()?>css/style-base.css" rel="stylesheet" type="text/css" />
        <script src="<?php echo base_url()?>js/jquery-1.8.2.js"></script>

<!----------------------------------------------------------------------------------------------------------------- -->
        <script src="<?php echo base_url()?>js/views/kardex_vehiculo_pdf.js" type="text/javascript"></script>
        <script src="<?php echo base_url()?>/js/amcharts.js" type="text/javascript"></script>
        <script src="<?php echo base_url()?>/js/serial.js" type="text/javascript"></script>  

    </head>
    <body>
     <div style="height:100px; background:#FFFFFF;  margin:auto" > 
	<table align="center" border="0" cellspacing="0" style="width:100%;">
        <tr>
            <td align="left" id="imagen">
                <img src="<?php echo base_url()?>/img/mtps_report.jpg" />
            </td>
            <td align="right">
                <h3><?php echo $titulo ?></h3>
                <h6>Fecha: <strong><?php echo date('d-m-Y') ?></strong> </h6>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                &nbsp;
            </td>
        </tr>
    </table>
    </div>
 <!--------------------Plantilla de carga de grafico y tabla------------------------------------------------------------ -->
        <br><br><br>
        <div style=" height:700px; width:1080px; background:#FFFFFF;" id="chartdiv">
		</div>
        <div id="legenddiv" style="position: relative; background:#FFFFFF;" align="center"></div>
        <br>
        <br>
        <center><strong id="titulo"><?php echo $subtitulo; ?></strong></center>
        <br>
        <div id="tabla_resultado">
        </div>
    </body>

</html>
                

<script language="javascript" >

fecha_inicial="<?php if($fecha_inicial!="") echo $fecha_inicial; else echo ""; ?>";
fecha_final="<?php  if($fecha_final!="") echo $fecha_final; else echo ""; ?>";


filtro=<?php echo $id_articulo ?>;
filtro2=<?php echo $id_vehiculo ?>;

if(filtro!="" && filtro!=0) articulo="<?php echo $articulo ?>";
if(filtro2!="" && filtro2!=0) vehiculo="<?php echo $vehiculo ?>";

                data=<?php echo $j; ?>;
                tabla(data);
				grafico(data);//contructor del grafico
                

  

function imprimir() {
if (window.print)
window.print()
}
setTimeout ("imprimir();", 2000);

</script>