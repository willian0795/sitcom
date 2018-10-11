// JavaScript Document
$(document).ready(function()
{
	function startChange()
	{
		var startDate = start.value(),
		endDate = end.value();
	
		if (startDate) 
		{
			//startDate = new Date(2014,07,01);
			startDate.setDate(startDate.getDate());
			end.min(startDate);
		}
		else if (endDate)
		{
			start.max(new Date(endDate));
		}
		else
		{
			endDate = new Date();
			start.max(endDate);
			end.min(endDate);
		}
	}
	
	function endChange()
	{
		var endDate = end.value(),
		startDate = start.value();
	
		if (endDate)
		{
			endDate = new Date(endDate);
			endDate.setDate(endDate.getDate());
			start.max(endDate);
		}
		else if (startDate)
		{
			end.min(new Date(startDate));
		}
		else
		{
			endDate = new Date();
			start.max(endDate);
			end.min(endDate);
		}
	}
	
	var start = $("#fecha_inicial").kendoDatePicker({
		change: startChange,
		format: "dd-MM-yyyy"		 
	}).data("kendoDatePicker");

	var end = $("#fecha_final").kendoDatePicker({
		change: endChange,
		format: "dd-MM-yyyy" 
	}).data("kendoDatePicker");

	start.max(end.value());
	end.min(start.value());
});

$("#Filtrar").click(function()
{             
	var formu = $('#filtro').serializeArray();
	//    console.log(formu); 
	reporte(formu);
});

$("#imp1").click(function()
{             
	var formu = $('#filtro').serializeArray();
	reporte(formu);
	setTimeout ("filtro_submit();", 5000);
});

function filtro_submit()
{
	document.getElementById('filtro').submit();
}

function reporte(formu)
{  
	$.ajax({  //para articulos
		async: true, 
		url: base_url()+"index.php/vehiculo/reporte_presupuesto_json",
		dataType: "json",
		type: "POST",
		data: formu,
		success: function(data)
		{
			tabla(data), 
			grafico(data)
		},
		error:function(data)
		{
			alertify.alert('Error al cargar datos de los presupuestos');
		}
	});
}

function tabla(json)
{
	var cont='';
	var n=json.length;
	var filtro=document.getElementById('mtto');
	var filtro2=document.getElementById('id_vehiculo');
	$('#tabla_resultado').html("");
	
	if(filtro.value!='' && filtro.value!=0)
	{
		cont=cont+'<table class="table_design" align="center" cellpadding="0" cellspacing="0">';
		cont=cont+'<thead>';
		cont=cont+'<tr>';
		cont=cont+'<th align="center">ID</th>';
		cont=cont+'<th align="center">Presupuesto($)</th>';
		cont=cont+'<th align="center">Gasto($)</th>';
		cont=cont+'</tr>';
		cont=cont+'</thead>';
		cont=cont+'<tbody>';
		
		for(i=0;i<n;i++)
		{
			cont=cont+'<tr>';
			cont=cont+'<td align="center">'+json[i].id_presupuesto+'</td>';
			cont=cont+'<td align="right">'+json[i].presupuesto+'</td>';
			cont=cont+'<td align="right">'+json[i].gasto+'</td>';
			cont=cont+'</tr>';
		}
		
		cont=cont+'</tbody>';
		cont=cont+'</table>';
	}
	else
	{
		cont=cont+'<table class="table_design" align="center" cellpadding="0" cellspacing="0">';
		cont=cont+'<thead>';
		cont=cont+'<tr>';
		cont=cont+'<th align="center">ID</th>';
		cont=cont+'<th align="center">Presupuesto($)</th>';
		cont=cont+'<th align="center">Gasto en Mtto. Interno($)</th>';
		cont=cont+'<th align="center">Gasto en Mtto. Externo($)</th>';
		cont=cont+'<th align="center">Gasto Total($)</th>';
		if(filtro2.value!=0 && filtro2.value!='')cont=cont+'<th align="center">Placa</th>';
		cont=cont+'</tr>';
		cont=cont+'</thead>';
		cont=cont+'<tbody>';
		
		for(i=0;i<n;i++)
		{
			cont=cont+'<tr>';
			cont=cont+'<td align="center">'+json[i].id_presupuesto+'</td>';
			cont=cont+'<td align="right">'+json[i].presupuesto+'</td>';
			cont=cont+'<td align="right">'+json[i].gasto_interno+'</td>';
			cont=cont+'<td align="right">'+json[i].gasto_externo+'</td>';
			cont=cont+'<td align="right">'+json[i].total_gasto+'</td>';
			if(filtro2.value!=0 && filtro2.value!='')cont=cont+'<td align="center">'+json[i].placa+'</td>';
			cont=cont+'</tr>';
		}
		
		cont=cont+'</tbody>';
		cont=cont+'</table>';
	}
	$('#tabla_resultado').html(cont);	
}

function grafico(chartData)
{
	var color1 = "#ADD981";
	var color2 ="#27c5ff";
	var filtro=document.getElementById('mtto');
	var filtro2=document.getElementById('id_vehiculo');
	var fecha_inicial = document.getElementById('fecha_inicial').value;
	var fecha_final = document.getElementById('fecha_final').value;
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
	
	var presupuesto = document.getElementsByName('mtto_input').item(0).value;
	var vehiculo = document.getElementsByName('id_vehiculo_input').item(0).value;
	
	// SERIAL CHART
	chart = new AmCharts.AmSerialChart();
	chart.dataProvider = chartData;
	if(filtro.value!='' && filtro.value!=0)
	{
		titulo='PRESUPUESTO DE MANTENIMIENTOS';
		chart.categoryField = "id_presupuesto";
	}
	else
	{
		titulo='PRESUPUESTO DE MANTENIMIENTOS';
		chart.categoryField = "id_presupuesto";
	}
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
	valueAxis.title = titulo;
	valueAxis.minorGridEnabled = true;
	valueAxis.minorGridAlpha = 0.08;
	
	valueAxis.gridAlpha = 0.15;
	chart.addValueAxis(valueAxis);
	
	// GRAPHS
	// column graph
	if(filtro.value!='' && filtro.value!=0)
	{
		var graph3 = new AmCharts.AmGraph();
		graph3.type = "column";
		graph3.title = "Presupuesto";
		graph3.valueField = "presupuesto";
		graph3.lineAlpha=0;
		graph3.fillColors = color1
		graph3.fillAlphas = 0.8;
		graph3.balloonText = "<span style='font-size:13px;'>[[title]] en [[category]]:<b>[[value]]</b></span>";
		chart.addGraph(graph3)
		
		var graph2 = new AmCharts.AmGraph();
		graph2.type = "column";
		graph2.title = "Cantidad gastada";
		graph2.valueField = "gasto";
		graph2.lineAlpha=0;
		graph2.fillColors = color2
		graph2.fillAlphas = 0.8;
		graph2.balloonText = "<span style='font-size:13px;'>[[title]] en [[category]]:<b>[[value]]</b></span>";
		chart.addGraph(graph2);
	}
	else
	{	
		// column graph
		
		var graph3 = new AmCharts.AmGraph();
		graph3.type = "column";
		graph3.title = "Gasto Interno";
		graph3.valueField = "gasto_interno";
		graph3.lineAlpha=0;
		graph3.fillColors = color1
		graph3.fillAlphas = 0.8;
		graph3.balloonText = "<span style='font-size:13px;'>[[title]] en [[category]]:<b>[[value]]</b></span>";
		chart.addGraph(graph3)
		
		var graph2 = new AmCharts.AmGraph();
		graph2.type = "column";
		graph2.title = "Gasto Externo";
		graph2.valueField = "gasto_externo";
		graph2.lineAlpha=0;
		graph2.fillColors = color2
		graph2.fillAlphas = 0.8;
		graph2.balloonText = "<span style='font-size:13px;'>[[title]] en [[category]]:<b>[[value]]</b></span>";
		chart.addGraph(graph2);
	}
	
	
	// LEGEND
	var legend = new AmCharts.AmLegend();
	legend.useGraphSettings = true;
	chart.addLegend(legend, 'legenddiv');
	
	chart.creditsPosition = "top-right";
	
	// WRITE
	chart.write("chartdiv");
}