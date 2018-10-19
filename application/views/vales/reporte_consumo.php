
<script type="application/javascript" language="javascript">
	estado_transaccion='<?php echo $estado_transaccion?>';
<?php if($accion!="") {?>
	estado_correcto='La solicitud se ha <?php echo $accion?>do exitosamente.';
	estado_incorrecto='Error al intentar <?php echo $accion?>r la solicitud: No se pudo conectar al servidor. Porfavor vuelva a intentarlo.';
<?php }?>

</script>

<section>
    <h2>Consumo de vales y asignación </h2>
</section>


<form name="filtro" method="post" id="filtro" action="<?php echo base_url(); ?>index.php/vales/consumo_pdf" target="_blank">

            <p> 
                <label for="start" >Fecha Inicio:</label><input id="start" name="start" style="width: 200px" tabindex="1"/>
            
                <label for="id_fuente_fondo" id="lid_fuente_fondo">Fuente de Fondo </label>
                    <select class="select" style="width:300px;" tabindex="2" id="id_fuente_fondo" name="id_fuente_fondo">
                        <option value="0">[Todo]</option>  
                    <?php     
                        foreach($fuente as $val) 
                        {                    ?>
                                <option value="<?php echo $val['id_fuente_fondo'] ?>"><?php echo $val['nombre_fuente'] ?></option>                
                    <?php
                        } 
                    ?>
                                                                
                    </select>

            </p>
            <p>
                    <label for="end">Fecha Final:</label><input id="end" name="end" style="width: 200px" tabindex="4"/>                    
                    <label for="id_seccion" id="lservicio_de">Sección</label>
                    <select class="select" style="width:300px;" tabindex="4" id="id_seccion" name="id_seccion" >
                             <option value="0">[Todo]</option>  
                            <?php
                                foreach($oficinas as $val) {
                            ?>
                                    <option value="<?php echo $val['id_seccion'] ?>"><?php echo $val['nombre_seccion'] ?></option>
                            <?php   
                                }
                            ?>
                           
                    </select>               
            </p>
            <p>
                <label for="color1" style="width: 150px">Colores: </label>
                 <input id="color1" style="width: 200px" name="color1"/>
                 <input id="color2" style="width: 200px" name="color2"/>
                 <label for="grupo" style="width: 250px">Ver por</label>
                <select class="select" style="width:200px;" tabindex="5" id="agrupar" name="agrupar" >
                     <option value="2" >Mes</option>
                     <option value="3" >Año</option>                    
                </select>   


            </p>
            <input type="hidden" id="tipo_archivo" name="tipo_archivo">

            <p align="center">
                <button type="button" id="Filtrar" class="button tam-1" >Filtrar</button>                    
                <button id="imp1" type="input" class="button tam-1"  style="display: none;">Exportar Excel</button>
                <button type="button" onclick="export_file('excel');" class="button tam-1" >Exportar Excel</button>
                <button type="button" onclick="export_file('pdf');" class="button tam-1" >Exportar PDF</button>
            </p>
</form>
<br><br>

    <!------------------------------------------Plantilla de carga de grafico y tabla----------------------------------------------------------------------- -->
    <div id="datos">
    </div>
    <br>
<div style="height: 700px; background:#FFFFFF;" id="chartdiv">
</div>


    <!----------------------------------------------------------------------------------------------------------------- -->

<script language="javascript" >

    $("#Filtrar").click(function(){             

            var formu = $('#filtro').serializeArray();
        //    console.log(formu); 

        reporte(formu);
        });

    function export_file(tipo){
        $("#tipo_archivo").val(tipo);
        $("#imp1").click();
    }

    function reporte(formu){  
                $.ajax({
            async:  true, 
            url:    base_url()+"index.php/vales/consumo_json",
            dataType:"json",
             type: "POST",
            data: formu,
            success: function(data){
              console.log(data);
               grafico(data,"seccion");//contructor del grafico
                tabla(data) 

                },
            error:function(data){

                

         //        alertify.alert('Error al cargar datos');
          console.log(data);
                }
        });          
        
    }

setTimeout ("reporte();", 2000);
///llamada al finalizar la contrucion del archivo
</script>

<script src="<?php echo base_url()?>js/views/reporte_consumo.js" type="text/javascript"></script>