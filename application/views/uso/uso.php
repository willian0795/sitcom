<script>
	estado_transaccion='<?php echo $estado_transaccion?>';
	estado_correcto='<?php echo $mensaje; ?>';
	estado_incorrecto='Error de conexión al servidor. Por favor vuelva a intentarlo.';
</script>
<section>
    <h2>Usos Varios</h2>
</section>
<button style="width:200px" type="button" onclick="window.open('<?php echo base_url()."index.php/vales/nuevo_uso" ?>','_parent')" name="btnNuevo">Nuevo Artículo</button>
<table class="grid">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th width="100px">Opción</th>
      </tr>
     </thead>
     <tbody>
    <?php 
	foreach ($usos as $fila) {
    ?>
        <tr align="center">
            <td><?php echo $fila['id_seccion_adicional']?></td>
            <td><?php echo $fila['nombre_seccion_adicional']?></td>
            <td>
            	<!-- <a rel="leanModal" title="Ver información detallada del artículo" href="#ventana" onclick="dialogo(<?php echo $fila['id_articulo'] ?>)"><img  src="<?php echo base_url()?>img/lupa.gif"/></a>
            	<a rel="leanModal" title="Modificar información del artículo" href="<?php echo base_url()."index.php/vehiculo/nuevo_articulo/".$fila['id_articulo'] ?>" ><img src="<?php echo base_url()?>img/editar.png"/></a>
                <a rel="leanModal" title="Cargar artículo" href="<?php echo base_url()."index.php/vehiculo/cargar_articulo/".$fila['id_articulo'] ?>" ><img src="<?php echo base_url()?>img/compra.png" width="25px"/></a> -->
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>


<div id="ventana" style="height:600px; width:650px">
    <div id='signup-header'>
        <h2>Información del Artículo</h2>
        <a id="cerrar" class="modal_close"></a>
    </div>
    <div id='contenido-ventana'>
    </div>
</div>

<script language="javascript" >
function dialogo(id)
{
	$('#contenido-ventana').load(base_url()+'index.php/vehiculo/ventana_articulo/'+id);
	return false;
}
function cerrar_vent()
{
	$('#cerrar').click();
}
</script>
