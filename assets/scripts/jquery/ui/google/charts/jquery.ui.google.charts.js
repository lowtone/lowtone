;(function($, g) {

	$.fn.drawChart = function() {}; // No google fix
	
	if (g == undefined) return;
	
	g.load('visualization', '1', {packages : ['corechart']});
	
	$.fn.drawChart = function(dataArray, opts) {
		var chart = new g.visualization.ColumnChart($(this).get(0)),
			data = new google.visualization.arrayToDataTable(dataArray),
			options = $.extend(true, null, {
				animation : {
					duration : 1000
				},
				hAxis : {
					title : 'Date',
					titleTextStyle : {
						color : '#21759b'
					}
				},
				vAxis : {
					logScale : false
				},
				colors : ['#21759b', '#5892ac', '#9f3242', '#ccc'],
				legend : {
					position : 'bottom'
				},
				backgroundColor : '#f5f5f5'
			}, opts);

		chart.draw(data, options);
	};
	
})(jQuery, google);