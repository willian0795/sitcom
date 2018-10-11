function tabla(json)
{
	var cont='';
	var n=json.length;
	$('#tabla_resultado').html("");
	
	if(filtro!='' && filtro!=0)
	{
		var ingresos=0;
		var egresos=0;
		cont=cont+'<table class="table_design" align="center" cellpadding="0" cellspacing="0" style=" width:815px !important">';
		cont=cont+'<thead>';
		cont=cont+'<tr>';
		cont=cont+'<th width="90px" align="center">Fecha</th>';
		cont=cont+'<th width="120px" align="center">Placa de Vehículo</th>';
		if(filtro2==0 || filtro2=='')cont=cont+'<th width="80px" align="center">Entrada</th>';
		cont=cont+'<th width="80px" align="center">Salida</th>';
		if(filtro2==0 || filtro2=='')cont=cont+'<th width="80px" align="center">Existencia</th>';
		cont=cont+'<th>Decripción</th>';
		cont=cont+'</tr>';
		cont=cont+'</thead>';
		cont=cont+'<tbody>';
		
		for(i=0;i<n;i++)
		{
			cont=cont+'<tr>';
			cont=cont+'<td>'+json[i].fecha_transaccion+'</td>';
			cont=cont+'<td>'+json[i].placa+'</td>';
			if(json[i].tipo_transaccion=='ENTRADA')
			{
				if(filtro2==0 || filtro2=='')
				{
					cont=cont+'<td align="rigth">'+json[i].cantidad+'</td>';
					cont=cont+'<td>&nbsp;</td>';
					ingresos=ingresos+parseFloat(json[i].cantidad);
				}
			}
			else if(json[i].tipo_transaccion=='SALIDA')
			{
				if(filtro2==0 || filtro2=='')cont=cont+'<td>&nbsp;</td>';
				cont=cont+'<td align="right">'+json[i].cantidad+'</td>';
				egresos=egresos+parseFloat(json[i].cantidad);
			}
			if(filtro2==0 || filtro2=='')cont=cont+'<td align="right">'+parseFloat(ingresos-egresos)+'</td>';
			cont=cont+'<td>'+json[i].descripcion+'</td>';
			cont=cont+'</tr>';
		}
		
		cont=cont+'</tbody>';
		cont=cont+'</table>';
	}
	else
	{
		var ingresos=0;
		var egresos=0;
		if(filtro2!='' && filtro2!=0)
		{
			cont=cont+'<table class="table_design" align="center" cellpadding="0" cellspacing="0"  style=" width:400px !important">';
			cont=cont+'<thead>';
			cont=cont+'<tr>';
			cont=cont+'<th width="90px" align="center">Artículo</th>';
			cont=cont+'<th width="30px" align="center">Cantidad Usada</th>';
			cont=cont+'<th width="30px" align="center">Unidad de Medida</th>';
			cont=cont+'</tr>';
			cont=cont+'</thead>';
			cont=cont+'<tbody>';
			
			for(i=0;i<n;i++)
			{
				cont=cont+'<tr>';
				cont=cont+'<td>'+json[i].nombre+'</td>';
				cont=cont+'<td align="right">'+json[i].cantidad+'</td>';
				cont=cont+'<td>'+json[i].unidad_medida+'</td>';
				cont=cont+'</tr>';
			}
			
			cont=cont+'</tbody>';
			cont=cont+'</table>';
		}
		else
		{
			cont=cont+'<table class="table_design" align="center" cellpadding="0" cellspacing="0" style=" width:550px !important">';
			cont=cont+'<thead>';
			cont=cont+'<tr>';
			cont=cont+'<th width="120px" align="center">Artículo</th>';
			cont=cont+'<th width="30px" align="center">Existencia Inicial</th>';
			cont=cont+'<th width="30px" align="center">Entradas Totales</th>';
			cont=cont+'<th width="30px" align="center">Salidas Totales</th>';
			cont=cont+'<th width="30px" align="center">Existencia Final</th>';
			cont=cont+'</tr>';
			cont=cont+'</thead>';
			cont=cont+'<tbody>';
			
			for(i=0;i<n;i++)
			{
				cont=cont+'<tr>';
				cont=cont+'<td>'+json[i].nombre+'</td>';
				cont=cont+'<td align="right">'+json[i].existencia_inicial+'</td>';
				cont=cont+'<td align="right">'+json[i].entradas+'</td>';
				cont=cont+'<td align="right">'+json[i].salidas+'</td>';
				cont=cont+'<td align="right">'+json[i].existencia_final+'</td>';
				cont=cont+'</tr>';
			}
			
			cont=cont+'</tbody>';
			cont=cont+'</table>';
		}
	}
	$('#tabla_resultado').html(cont);	
}

function grafico(chartData)
{
	console.log(chartData); 
	var color1 = "#ADD981";
	var color2 ="#27c5ff";
	var titulo;
	var titulo_aux="";
	var chart;
	
	
	/*subtitulo de fechas*/
	if(fecha_inicial!="" && fecha_final!="")
	{
		titulo_aux=" durante el período del "+fecha_inicial+" al "+fecha_final;
	}
	else if(fecha_inicial!="" || fecha_final!="")
	{
		titulo_aux=" en la fecha "+fecha_inicial+" "+fecha_final;
	}
	
	// SERIAL CHART
	chart = new AmCharts.AmSerialChart();
	chart.dataProvider = chartData;
	if(filtro!='' && filtro!='0')
	{
		if(filtro2!='' && filtro2!='0')
		{
			titulo='Kardex de Insumos de "'+articulo+'" por el vehículo '+vehiculo+titulo_aux;
			chart.categoryField = "fecha_transaccion";
		}
		else
		{
			titulo='Kardex de Insumos de "'+articulo+'"'+titulo_aux;
			chart.categoryField = "fecha_transaccion";
		}
	}
	else
	{
		if(filtro2!='' && filtro2!='0')
		{
			titulo='Materiales usados por el vehículo '+vehiculo+titulo_aux;
			chart.categoryField = "nombre";
		}
		else
		{
			titulo='Movimiento de entradas y salidas de los materiales en bodega'+titulo_aux;
			chart.categoryField = "nombre";
		}
	}
	chart.startDuration = 1;
	chart.rotate = true;
	
	// AXES
	// category (Eje Indepediente, donde van las etiquetas)
	var categoryAxis = chart.categoryAxis;
	categoryAxis.gridPosition = "start";
	categoryAxis.axisAlpha = 1; /*Brinda un color a la línea del eje*/
	categoryAxis.dashLength = 0; /*Muestra líneas de control en el eje 0= una sola línea*/
	
	// value (Eje dependiente, donde van los resultados del query)
	var valueAxis = new AmCharts.ValueAxis();
	valueAxis.dashLength = 5; /*Muesta líneas de control en el eje en los valores múltiplos de 5*/
	valueAxis.axisAlpha = 1; /*Proporciona la opacidad a la línea del eje*/
	valueAxis.position = "top";
	valueAxis.title = titulo; /*Muestra el título*/
	valueAxis.minorGridEnabled = true; /*Habilita la presentación de líneas de sub-control del eje*/
	valueAxis.minorGridAlpha = 0.15; /*Muestra líneas verticales pero en el 5 no lo hace*/
	
	valueAxis.gridAlpha = 1;/*opacidad de las líneas de control del eje (1=negro, 0=blanco)*/
	chart.addValueAxis(valueAxis);
	
	// GRAPHS
	// column graph
	if(filtro!='' && filtro!=0)
	{
		var graph1 = new AmCharts.AmGraph();
		graph1.type = "column";
		if(filtro2!='' && filtro2!=0) graph1.title = "Usos";
		else graph1.title = "Existencias";
		graph1.valueField = "cantidad";
		graph1.lineAlpha = 0;
		graph1.fillColors = color1;
		graph1.fillAlphas = 0.8;
		graph1.balloonText = "<span style='font-size:13px;'>[[title]] en [[category]]:<b>[[value]]</b></span>";
		chart.addGraph(graph1);
	}
	else
	{	
		// column graph
		if(filtro2!='' && filtro2!=0)
		{
			var graph1 = new AmCharts.AmGraph();
			graph1.type = "column";
			graph1.title = "cantidad usada";
			graph1.valueField = "cantidad";
			graph1.lineAlpha = 0;
			graph1.fillColors = color1;
			graph1.fillAlphas = 0.8;
			graph1.balloonText = "<span style='font-size:13px;'>[[title]] en [[category]]:<b>[[value]]</b></span>";
			chart.addGraph(graph1);
		}
		else
		{
			var graph3 = new AmCharts.AmGraph();
			graph3.type = "column";
			graph3.title = "Entradas";
			graph3.valueField = "entradas";
			graph3.lineAlpha=0;
			graph3.fillColors = color1 /*Para colorear la gráfica*/
			graph3.fillAlphas = 0.8; /*Para darles opacidad a las gráficas*/
			graph3.balloonText = "<span style='font-size:13px;'>[[title]] en [[category]]:<b>[[value]]</b></span>"; /*Etiqueta que se muestra al sobreponer el mouse*/
			chart.addGraph(graph3)
			
			var graph2 = new AmCharts.AmGraph();
			graph2.type = "column";
			graph2.title = "Salidas";
			graph2.valueField = "salidas";
			graph2.lineAlpha=0;
			graph2.fillColors = color2
			graph2.fillAlphas = 0.8;
			graph2.balloonText = "<span style='font-size:13px;'>[[title]] en [[category]]:<b>[[value]]</b></span>";
			chart.addGraph(graph2);
		}
	}
	
	
	// LEGEND
	var legend = new AmCharts.AmLegend();
	legend.useGraphSettings = true;
	chart.addLegend(legend, 'legenddiv');
	
	chart.creditsPosition = "top-right";
	
	// WRITE
	chart.write("chartdiv");
}