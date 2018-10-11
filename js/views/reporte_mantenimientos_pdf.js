// JavaScript Document
function tabla(json)
{
	var cont='';
	var n=json.length;
	
	$('#tabla_resultado').html("");
	
	if(filtro!='' && filtro!=0) cont=cont+'<table class="table_design" align="center" cellpadding="0" cellspacing="0" style=" width:200px !important">';
	else cont=cont+'<table class="table_design" align="center" cellpadding="0" cellspacing="0" style=" width:550px !important">';
	cont=cont+'<thead>';
	cont=cont+'<tr>';
	if(filtro=='' || filtro==0) cont=cont+'<th align="center">Mecánico</th>';
	if((filtro!='' && filtro!=0) || (filtro2!='' && filtro2!=0)) cont=cont+'<th align="center">Placa</th>';
	cont=cont+'<th align="center" style="width: 90px !important;">N° de Mttos.</th>';
	cont=cont+'</tr>';
	cont=cont+'</thead>';
	cont=cont+'<tbody>';
	
	for(i=0;i<n;i++)
	{
		cont=cont+'<tr>';
		if(filtro=='' || filtro==0) cont=cont+'<td>'+json[i].nombre+'</td>';
		if((filtro!='' && filtro!=0) || (filtro2!='' && filtro2!=0))cont=cont+'<td align="center">'+json[i].placa+'</td>';
		cont=cont+'<td align="right">'+json[i].mttos+'</td>';
		cont=cont+'</tr>';
	}
	
	cont=cont+'</tbody>';
	cont=cont+'</table>';
	$('#tabla_resultado').html(cont);	
}

function grafico(chartData)
{
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
			titulo='Mantenimientos realizados por '+mecanico+' al vehículo '+vehiculo+titulo_aux;
		}
		else
		{
			titulo='Mantenimientos realizados por '+mecanico+titulo_aux;
		}
		
		chart.categoryField = "placa";
	}
	else
	{
		if(filtro2!='' && filtro2!='0')
		{
			titulo='Mantenimientos por mecánicos al vehiculo '+vehiculo+titulo_aux;
		}
		else
		{
			titulo='Mantenimientos por mecánicos'+titulo_aux;
		}
		chart.categoryField = "nombre";
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
	valueAxis.dashLength = 1; /*Muesta líneas de control en el eje en los valores múltiplos de 5*/
	valueAxis.axisAlpha = 1; /*Proporciona la opacidad a la línea del eje*/
	valueAxis.position = "top";
	valueAxis.title = titulo; /*Muestra el título*/
	//valueAxis.minorGridEnabled = true; /*Habilita la presentación de líneas de sub-control del eje*/
	//valueAxis.minorGridAlpha = 0.15; /*Muestra líneas verticales pero en el 5 no lo hace*/
	
	valueAxis.gridAlpha = 0;/*opacidad de las líneas de control del eje (1=negro, 0=blanco)*/
	chart.addValueAxis(valueAxis);
	
	// GRAPHS
	// column graph
	var graph1 = new AmCharts.AmGraph();
	graph1.type = "column";
	graph1.title = "Mantenimientos";
	graph1.valueField = "mttos";
	graph1.lineAlpha = 0;
	graph1.fillColors = color1;
	graph1.fillAlphas = 1;
	graph1.balloonText = "<span style='font-size:13px;'>[[title]] en [[category]]:<b>[[value]]</b></span>";
	chart.addGraph(graph1);

	
	// LEGEND
	var legend = new AmCharts.AmLegend();
	legend.useGraphSettings = true;
	chart.addLegend(legend, 'legenddiv');
	
	chart.creditsPosition = "top-right";
	
	// WRITE
	chart.write("chartdiv");
}