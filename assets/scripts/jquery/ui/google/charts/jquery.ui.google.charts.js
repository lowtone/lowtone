// Generated by CoffeeScript 1.5.0
(function() {
  var $, defaults, drawChart, g;

  $ = jQuery;

  g = google;

  defaults = {
    type: 'ColumnChart',
    animation: {
      duration: 1000
    },
    hAxis: {
      titleTextStyle: {
        color: '#21759b'
      }
    },
    vAxis: {
      logScale: false
    },
    colors: ['#21759b', '#5892ac', '#9f3242', '#ccc'],
    legend: {
      position: 'bottom'
    },
    backgroundColor: '#f5f5f5'
  };

  if (void 0 === g) {
    drawChart = function() {};
  } else {
    g.load('visualization', '1', {
      packages: ['corechart']
    });
    drawChart = function(dataArray, opts) {
      var chart, data, draw, options;
      options = $.extend(true, null, defaults, opts);
      chart = new g.visualization[options.type]($(this).get(0));
      data = new g.visualization.arrayToDataTable(dataArray);
      delete options.type;
      draw = function() {
        return chart.draw(data, options);
      };
      $(window).resize(draw);
      return draw();
    };
  }

  $.fn.extend({
    drawChart: drawChart
  });

}).call(this);
