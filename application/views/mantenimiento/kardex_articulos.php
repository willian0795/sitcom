<section>
    <h2>Kardex de Artículos</h2>
</section>
<form id="filtro" action="<? echo base_url()?>index.php/vehiculo/kardex_pdf" target="_blank" method="post">
<input type="hidden" id="titulo" name="titulo" value="" />
<input type="hidden" id="subtitulo" name="subtitulo" value="" />
<table align="center" width="100%">
    <tr>
        <td width="50%">
        <label style="width:100px">Artículo: </label>
            <select class="select" style="width:300px" name="id_articulo" id="id_articulo" placeholder="Seleccione..." multiple="multiple">
                <option value="0" selected="selected">[Todos]</option>
                <?php
                    foreach($articulos as $art)
                    {
                        echo "<option value='".$art['id_articulo']."'>".$art['nombre']."</option>";
                    }
                ?>
            </select>
        </td>
        <td>
            <label>Vehículo: </label>
            <select class="select" style="width:120px" name="id_vehiculo" id="id_vehiculo" placeholder="Seleccione..." multiple="multiple">
                <option value="0" selected='selected'>[Todos]</option>
                <?php
                    foreach($vehiculos as $v)
                    {
                        echo "<option value='".$v->id."'>".$v->placa."</option>";
                    }
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <label style="width:100px">Fecha Inicial: </label>
            <input type="text" name="fecha_inicial" id="fecha_inicial">
        </td>
        <td>
            <label>Fecha Final: </label>
            <input type="text" name="fecha_final" id="fecha_final">
        </td>
    </tr>
</table>
<p align="center">
        <button type="button" id="Filtrar" class="button tam-1" >Filtrar</button>                    
        <button type="button"   id="imp1" class="button tam-1" >Imprimir</button>

</p>
</form>
<br><br>
<div style=" height:700px; background:#FFFFFF;" id="chartdiv">
</div>
<div id="legenddiv" style="position: relative; background:#FFFFFF;" align="center"></div>
<br><br>
<div id="tabla_resultado">
</div>

<script language="javascript" src="<?php echo base_url()?>js/views/kardex_vehiculo.js"></script>