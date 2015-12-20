if (typeof CTtitle !== "undefined") {
	pieChart(CTtitle, CTdata);
}

function pieChart(title, items) {
	var nr = 0;
	var sum = function (a, b) {
		return a + b
	};

	var data = {
		names: [],
		series: []
	}

	for (i = 0; i < items.length; i++) {
		if (i % 2 === 0) {
			data.names.push(items[i]);
		} else {
			data.series.push(items[i]);
		}
	}

	var options = {
		labelInterpolationFnc: function (value) {
			var math = parseFloat((value / data.series.reduce(sum) * 100)).toFixed(2) + '%';
			if ((nr >= data.names.length) == false) {
				pieLegend(nr, math);
				nr += 1;
			}
			return math;
		},
		chartPadding: 0,
		labelOffset: 15,
	};

	var div = ".ct-chart";
	Chartist.Pie(div, data, options);
	var ctChart = $(div);

	ctChart.before('<p style="text-align: center; margin-top: 2em;"><em>' + title + '</em></p>');
	ctChart.after('<div class="ct-list"></div>');
	var legend = $(".ct-list");

	function pieLegend(i, calc) {
		var listItem = "<span class='button ct-series-" + i + "'>" + data.names[i] + " - " + calc + "</span>";
		legend.append(listItem);
	};

};