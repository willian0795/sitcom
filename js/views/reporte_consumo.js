 
                


function grafico (chartData, label) {

            var color1 = "#ADD981";
            var color2 ="#27c5ff";
            var chart;
             color1 = $("#color1").val();
             color2 = $("#color2").val();


                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData;
                chart.categoryField = label;
                chart.startDuration = 1;
                chart.rotate = true;

                // AXES
                // category
                var categoryAxis = chart.categoryAxis;
                categoryAxis.gridPosition = "start";
                categoryAxis.axisColor = "#DADADA";
                categoryAxis.dashLength = 3;

                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.dashLength = 3;
                valueAxis.axisAlpha = 0.2;
                valueAxis.position = "top";
                valueAxis.title = "vales";
                valueAxis.minorGridEnabled = true;
                valueAxis.minorGridAlpha = 0.08;
                valueAxis.gridAlpha = 0.15;
                chart.addValueAxis(valueAxis);

                // GRAPHS
                // column graph
                var graph1 = new AmCharts.AmGraph();
                graph1.type = "column";
                graph1.title = "Consumido";
                graph1.valueField = "consumidos";
                graph1.lineAlpha = 0;
                graph1.fillColors = color1;
                graph1.fillAlphas = 0.8;
                graph1.balloonText = "<span style='font-size:13px;'>[[title]] en [[category]]:<b>[[value]]</b></span>";
                chart.addGraph(graph1);

                
                // column graph
                var graph2 = new AmCharts.AmGraph();
                graph2.type = "column";
                graph2.title = "Asignado";
                graph2.valueField = "asignado";
                graph2.lineAlpha=0;
                graph2.fillColors = color2
                graph2.fillAlphas = 0.8;
                graph2.balloonText = "<span style='font-size:13px;'>[[title]] en [[category]]:<b>[[value]]</b></span>";
                chart.addGraph(graph2);


                // LEGEND
                var legend = new AmCharts.AmLegend();
                legend.useGraphSettings = true;
                chart.addLegend(legend);

                chart.creditsPosition = "top-right";

                // WRITE
                chart.write("chartdiv");

}

function grafico2 (chartData, label) {//para el  reporte de asignacion

            var color1 = "#ADD981";
            var color2 ="#27c5ff";
            var chart;
             color1 = $("#color1").val();
             color2 = $("#color2").val();


                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData;
                chart.categoryField = label;
                chart.startDuration = 1;
                chart.rotate = true;

                // AXES
                // category
                var categoryAxis = chart.categoryAxis;
                categoryAxis.gridPosition = "start";
                categoryAxis.axisColor = "#DADADA";
                categoryAxis.dashLength = 3;

                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.dashLength = 3;
                valueAxis.axisAlpha = 0.2;
                valueAxis.position = "top";
                valueAxis.title = "vales";
                valueAxis.minorGridEnabled = true;
                valueAxis.minorGridAlpha = 0.08;
                valueAxis.gridAlpha = 0.15;
                chart.addValueAxis(valueAxis);

                // GRAPHS
                // column graph
                var graph1 = new AmCharts.AmGraph();
                graph1.type = "column";
                graph1.title = "Entregado";
                graph1.valueField = "entregado";
                graph1.lineAlpha = 0;
                graph1.fillColors = color1;
                graph1.fillAlphas = 0.8;
                graph1.balloonText = "<span style='font-size:13px;'>[[title]] en [[category]]:<b>[[value]]</b></span>";
                chart.addGraph(graph1);

                
                // column graph
                var graph2 = new AmCharts.AmGraph();
                graph2.type = "column";
                graph2.title = "Asignado";
                graph2.valueField = "asignado";
                graph2.lineAlpha=0;
                graph2.fillColors = color2
                graph2.fillAlphas = 0.8;
                graph2.balloonText = "<span style='font-size:13px;'>[[title]] en [[category]]:<b>[[value]]</b></span>";
                chart.addGraph(graph2);


                // LEGEND
                var legend = new AmCharts.AmLegend();
                legend.useGraphSettings = true;
                chart.addLegend(legend);

                chart.creditsPosition = "top-right";

                // WRITE
                chart.write("chartdiv");

}

function encabezado_tabla(titulo){
  var mes = parseInt(titulo.substr(4));
  if(titulo != ""){
    titulo = "Mes: "+meses[mes]+" "+titulo.substr(0,4);
  }else{
    titulo = "RESUMEN TOTAL DE EN CADA MES";
  }
  var fila = "<table border='1' cellspacing='0' align='center' class='table_design'>"+
  "<thead>"+
  "<tr><th colspan='9'>"+titulo+"</th></tr>"+
     "<tr><th>N°</th>"+
      "<th>Seccion</th>"+
      "<th>Sobrante anterior</th>"+
      "<th>Asignado</th>"+
      "<th>Disponible</th>"+ 
      "<th>Consumido</th>"+
      "<th>Sobrante actual</th>"+
      "<th>Consumido ($)</th>"+             
      "<th width='160px'>Series</th></tr>"+  
  "</thead>"+
  "<tbody>";
  return fila;
}

function encabezado_tabla2(titulo){
    titulo = "RESUMEN TOTAL POR CADA MES";
  var fila = "<table border='1' cellspacing='0' align='center' class='table_design'>"+
  "<thead>"+
  "<tr><th colspan='7'>"+titulo+"</th></tr>"+
     "<tr>"+
      "<th>MES</th>"+
      "<th>Sobrante anterior</th>"+
      "<th>Asignado</th>"+
      "<th>Disponible</th>"+ 
      "<th>Consumido</th>"+
      "<th>Sobrante actual</th>"+
      "<th>Consumido ($)</th>"+             
      "</tr>"+  
  "</thead>"+
  "<tbody>";
  return fila;
}

function pie_tabla(sobrantes_anterior, asignado, consumo, disponibles, sobrantes_despues, total){
  var fila = "<tr>" +
        "<th align='center' colspan='2'>TOTAL</th>" +
        "<th align='center'>" + sobrantes_anterior.toString() + "</th>" +
        "<th align='center'>" + asignado.toString() + "</th>" +
        "<th align='center'>" + disponibles.toString() + "</th>" +
        "<th align='center'>" + consumo + "</th>" +
        "<th align='center'>" + sobrantes_despues.toString() + "</th>" +
        "<th align='center'>" + total.toFixed(2) + "</th>" +
        "<th align='center'></th>" +
      "</tr>";
    return fila+"</tbody></table><br><br>";
}

function pie_tabla2(sobrantes_anterior, asignado, consumo, disponibles, sobrantes_despues, total){
  var fila = "<tr>" +
        "<th align='center'>TOTAL</th>" +
        "<th align='center'>" + sobrantes_anterior.toString() + "</th>" +
        "<th align='center'>" + asignado.toString() + "</th>" +
        "<th align='center'>" + disponibles.toString() + "</th>" +
        "<th align='center'>" + consumo + "</th>" +
        "<th align='center'>" + sobrantes_despues.toString() + "</th>" +
        "<th align='center'>" + total.toFixed(2) + "</th>" +
      "</tr>";
    return fila+"</tbody></table><br><br>";
}

function subtotal_tabla(titulo,sobrantes_anterior, asignado, consumo, disponibles, sobrantes_despues, total){
  mes = parseInt(titulo.substr(4));
  titulo = meses[mes]+" "+titulo.substr(0,4);
  var fila = "<tr>" +
        "<td align='center'>"+titulo+"</td>" +
        "<td align='center'>" + sobrantes_anterior.toString() + "</td>" +
        "<td align='center'>" + asignado.toString() + "</td>" +
        "<td align='center'>" + disponibles.toString() + "</td>" +
        "<td align='center'>" + consumo + "</td>" +
        "<td align='center'>" + sobrantes_despues.toString() + "</td>" +
        "<td align='center'>" + total.toFixed(2) + "</td>" +
      "</tr>";
    return fila;
}

var meses = ["","Enero", "Febrero", "Marzo","Abril","Mayo","Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

function tabla (json) {
  var fila = "";  var subtotales = ""; var cont = 0;
  var sobrantes_anterior = 0, asignado = 0, consumo = 0, disponibles = 0, total = 0, sobrantes_despues = 0; 
  var sobrantes_anterior2 = 0, asignado2 = 0, consumo2 = 0, disponibles2 = 0, total2 = 0, sobrantes_despues2 = 0; 

  $('#datos').html('');

  var mesant = "";    
  
  for (i=0;i<json.length;i++) {
    if(i == 0){
        fila +=encabezado_tabla(json[i].mes);
        mesant = json[i].mes;
    }else if(mesant != json[i].mes){
        fila += pie_tabla(sobrantes_anterior2, asignado2, consumo2, disponibles2, sobrantes_despues2, total2);
        subtotales += subtotal_tabla(mesant, sobrantes_anterior2, asignado2, consumo2, disponibles2, sobrantes_despues2, total2);
        fila +=encabezado_tabla(json[i].mes);
        mesant = json[i].mes;
        sobrantes_anterior2 = 0; asignado2 = 0; consumo2 = 0; disponibles2 = 0; total2 = 0; sobrantes_despues2 = 0;
        cont=0;
    }
    cont++;

    var n = new Number(json[i].dinero);
    sobrantes_anterior += parseInt(json[i].sobrantes_anterior);
    asignado += parseInt(json[i].asignado);
    consumo += parseInt(json[i].consumidos);
    disponibles += parseInt(json[i].disponibles);
    sobrantes_despues += parseInt(json[i].sobrantes_despues);
    total += n;

    sobrantes_anterior2 += parseInt(json[i].sobrantes_anterior);
    asignado2 += parseInt(json[i].asignado);
    consumo2 += parseInt(json[i].consumidos);
    disponibles2 += parseInt(json[i].disponibles);
    sobrantes_despues2 += parseInt(json[i].sobrantes_despues);
    total2 += n;

    fila+= "<tr>" +
      "<td align='center'>" + (cont) + "</td>" +
      "<td align='center'>" + json[i].seccion + "</td>" +
      "<td align='center'>" + json[i].sobrantes_anterior + "</td>" +
      "<td align='center'>" + json[i].asignado + "</td>" +
      "<td align='center'>" + json[i].disponibles + "</td>" +
      "<td align='center'>" + json[i].consumidos + "</td>" +
      "<td align='center'>" + json[i].sobrantes_despues + "</td>" +
      "<td align='center'>" + n.toFixed(2) + "</td>" +
      "<td align='center'>";

      var series1=json[i].inicial.split(",");
      var series2=json[i].final.split(",");
          
      for (var j= 0; j < series1.length; j++) {
          fila+=series1[j]+" - "+ series2[j];
          if(j!=series1.length-1){ fila+="<br>"}
      }
     fila+= "</td>" +
    "</tr>";

    if((i+1) == (json.length)){
        fila += pie_tabla(sobrantes_anterior2, asignado2, consumo2, disponibles2, sobrantes_despues2, total2);
        subtotales += subtotal_tabla(json[i].mes, sobrantes_anterior2, asignado2, consumo2, disponibles2, sobrantes_despues2, total2);
        mesant = json[i].mes;
    }   
  }

  fila +=encabezado_tabla2('');
  fila += subtotales;
  fila += pie_tabla2(sobrantes_anterior, asignado, consumo, disponibles, sobrantes_despues, total);

  $('#datos').append(fila);
}

function tabla1 (json) {
                var fila;

            $('#datos tbody').remove();        
            for (i=0;i<json.length;i++) {   
            var n = new Number(json[i].dinero);
             fila= "<tr>" +
              "<td align='center'>" + json[i].row_number + "</td>" +
              "<td align='center'>" + json[i].fecha + "</td>" +
              "<td align='center'>" + json[i].consumido + "</td>" +
              "<td align='center'>$" + n.toFixed(2) + "</td>" +
            "</tr>";    
                $('#datos').append(fila)    
                }  
}
function tabla2 (json) {
                var fila, total, suma_vales, suma_gal, suma_rec;
                suma_vales=suma_rec=suma_gal=0;

            $('#datos tbody').remove();        
            for (i=0;i<json.length;i++) {   
            var n = new Number(json[i].rendimiento);;
            suma_vales+=Number(json[i].vales);
            suma_gal+=Number(json[i].gal);
            suma_rec+=Number(json[i].recorrido);
             fila= "<tr>" +
              "<td align='center'>" + json[i].placa + "</td>" +
              "<td align='center'>" + json[i].marca + "</td>" +
              "<td align='center'>" + json[i].vales + "</td>" +
              "<td align='center'>" + json[i].gal + "</td>" +
              "<td align='center'>" + json[i].recorrido + "</td>" +
              "<td align='center'>" + n.toFixed(2) + "</td>" +
            "</tr>";    
                $('#datos').append(fila)    
                }  

                         var total= "<tr>" +
              "<th align='center'><strong>TOTAL</strong></th>" +
              "<th align='center'></th>" +
              "<th align='center'><strong>" + suma_vales + "</strong></th>" +
              "<th align='center'><strong>" + suma_gal.toFixed(2) + "</strong></th>" +
              "<th align='center'><strong>" + suma_rec + "<strong></th>" +
              "<th align='center'> </th>" +
            "</tr>";    
            $('#datos').append(total);

            $('#datos3 tbody').remove(); 
            var resumen= "<tr>" +
              "<td align='center'>Consumo vehículos</td>" +
              "<td align='center'><strong>" + suma_vales + "</strong></td>" +
              "<td align='center'><strong>" + suma_gal.toFixed(2) + "</strong></td>" +
            "</tr>";
            $('#datos3').append(resumen);

}
function tabla2_2(json) {
                var fila, total, suma_vales, suma_gal, suma_rec;
                suma_vales=suma_rec=suma_gal=0;

            $('#datos2 tbody').remove();        
            for (i=0;i<json.length;i++) {   
            suma_vales+=Number(json[i].vales);
            var g=Number(json[i].gal);
            suma_gal+=g;

             fila= "<tr>" +
              "<td align='center'>" + json[i].nombre + "</td>" +
              "<td align='center'>" + json[i].seccion + "</td>" +
              "<td align='center'>" + json[i].vales + "</td>" +
              "<td align='center'>" + g.toFixed(2) + "</td>" +
            "</tr>";    
                $('#datos2').append(fila)    
                }  

                var total= "<tr>" +
              "<th align='center'><strong>TOTAL</strong></th>" +
              "<th align='center'></th>" +
              "<th align='center'><strong>" + suma_vales + "</strong></th>" +
              "<th align='center'><strong>" + suma_gal.toFixed(2) + "</strong></th>" +
            "</tr>";    
            $('#datos2').append(total);

            var row_sub = $('#datos3 tbody').find('tr');
            var cells_vehiculo = $(row_sub[0]).find("td");
            var total_vales = parseInt($(cells_vehiculo[1]).text().trim());
            var total_consumo = parseFloat($(cells_vehiculo[2]).text().trim());
            var resumen= "<tr>" +
              "<td align='center'>Consumo Herramientas y otros artículos</td>" +
              "<td align='center'><strong>" + suma_vales + "</strong></td>" +
              "<td align='center'><strong>" + suma_gal.toFixed(2) + "</strong></td>" +
            "</tr>";

            resumen += "<tr>" +
              "<th align='center'>TOTAL</th>" +
              "<th align='center'><strong>" + (suma_vales+total_vales) + "</strong></th>" +
              "<th align='center'><strong>" + (suma_gal+total_consumo).toFixed(2) + "</strong></th>" +
            "</tr>";
            $('#datos3').append(resumen);

}

function tabla3 (json) {
                var fila;

            $('#datos tbody').remove();        
            for (i=0;i<json.length;i++) {   
            
             fila= "<tr>" +
              "<td align='center'>" + (i+1) + "</td>" +
              "<td align='center'>" + json[i].nombre_seccion + "</td>" +
              "<td align='center'>" + json[i].sobrantes_anterior + "</td>" +
              "<td align='center'>" + json[i].asignado + "</td>" +
              "<td align='center'>" + json[i].disponibles + "</td>" +
              "<td align='center'>" + json[i].consumidos + "</td>" +
              "<td align='center'>" + json[i].sobrantes_despues + "</td>" +
              "<td align='center'>"; 
                /*var series1=json[i].inicial.split(",");
                var series2=json[i].final.split(",");
                    
                    for (var j= 0; j < series1.length; j++) {
                        fila+=series1[j]+" - "+ series2[j];
                        if(j!=series1.length-1){ fila+="<br>"}
                    }*/

             fila+= "</td>" +
            "</tr>";    
                $('#datos').append(fila)    
                }  
}
 function startChange() {
                        var startDate = start.value(),
                        endDate = end.value();

                        if (startDate) {
                            startDate = new Date(startDate);
                            startDate.setDate(startDate.getDate());
                            end.min(startDate);
                        } else if (endDate) {
                            start.max(new Date(endDate));
                        } else {
                            endDate = new Date();
                            start.max(endDate);
                            end.min(endDate);
                        }
                    }

                    function endChange() {
                        var endDate = end.value(),
                        startDate = start.value();

                        if (endDate) {
                            endDate = new Date(endDate);
                            endDate.setDate(endDate.getDate());
                            start.max(endDate);
                        } else if (startDate) {
                            end.min(new Date(startDate));
                        } else {
                            endDate = new Date();
                            start.max(endDate);
                            end.min(endDate);
                        }
                    }

                    var start = $("#start").kendoDatePicker({
                        change: startChange,
                         format: "dd-MM-yyyy" 
                    }).data("kendoDatePicker");

                    var end = $("#end").kendoDatePicker({
                        change: endChange,
                         format: "dd-MM-yyyy" 
                    }).data("kendoDatePicker");

                    start.max(end.value());
                    end.min(start.value());
                
                  $("#color1").kendoColorPicker({
                            value: "#8281d9",
                            buttons: false
                        });
                 $("#color2").kendoColorPicker({
                            value: "#ff0040",
                            buttons: false
                       });