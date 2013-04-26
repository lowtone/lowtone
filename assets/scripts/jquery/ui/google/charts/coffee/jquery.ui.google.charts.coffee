$ = jQuery
g = google

defaults = 
	type: 'ColumnChart'
	animation: 
		duration : 1000
	hAxis: 
		titleTextStyle:
			color: '#21759b'
	vAxis: 
		logScale: false
	colors: ['#21759b', '#5892ac', '#9f3242', '#ccc'],
	legend: 
		position : 'bottom'
	backgroundColor: '#f5f5f5'

if undefined == g
	drawChart = ->
else
	g.load 'visualization', '1', {packages : ['corechart']}

	drawChart = (dataArray, opts) -> 
		options = $.extend true, null, defaults, opts

		chart = new g.visualization[options.type] $(this).get(0)
		data = new g.visualization.arrayToDataTable dataArray

		delete options.type

		draw = ->
			chart.draw data, options

		$(window).resize draw

		draw()

$.fn.extend
	drawChart: drawChart