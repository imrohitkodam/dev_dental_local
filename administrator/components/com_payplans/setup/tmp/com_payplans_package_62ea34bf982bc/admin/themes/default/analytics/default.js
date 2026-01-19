FD.require()
.script('vendor/chart')
.done(function() {

	PayPlans.require()
	.done(function($) {

		function toggleLoader(show, wrapper) {
			var el = '[data-fd-loader-wrapper]';

			if (wrapper) {
				return wrapper.find(el).toggleClass('t-hidden', show);
			}

			return $(el).toggleClass('t-hidden', show);
		}

		function renderChartGraph(chartFigure, chartTitle, type) {
			toggleLoader(true);

			var data = [];
			var data2 = [];
			var datasetsTooltips = [];
			var dateLabels = [];

			var total = 0;

			if (chartFigure) {
				$.each(chartFigure, function(key, value) {
					dateLabels.push(key);
					data.push(value.total_1);

					datasetsTooltips[key] = value;
					total++;

					if (type == 'growth') {
						data2.push(value.total_2);
					}
				});
			}

			if (total == 1) {
				data.splice(0, 0, '');
				data.splice(2, 0, '');

				dateLabels.splice(0, 0, '');
				dateLabels.splice(2, 0, '');

				datasetsTooltips.splice(0, 0, '');
				datasetsTooltips.splice(2, 0, '');
			}

			datasets = [{
				backgroundColor: "rgba(220,237,200, 0)",
				borderColor: FD.getStyleToken('--fd-primary-500', {method: 'rgb', opacity: '.85'}),
				borderWidth: 1.3,
				data: data,
				fill: 'start',
				pointRadius: 2,
				pointBackgroundColor: FD.getStyleToken('--fd-primary-500', {method: 'rgb', opacity: '.85'}),
			}]

			if (data2) {
				var dataset2 = {
					backgroundColor: "rgba(239,154,154, 0.2)",
					borderColor: 'rgba(211,47,47, 0.6)',
					borderWidth: 1.3,
					data: data2,
					fill: 'start',
					pointRadius: 2,
					pointBackgroundColor: FD.getStyleToken('--fd-primary-500', {method: 'rgb', opacity: '.85'}),
				}

				datasets.push(dataset2);
			}

			var options = {
				maintainAspectRatio: false,
				spanGaps: false,
				elements: {
					line: {
						tension: 0.000001
					}
				},
				scales: {
					yAxes: [{
						gridLines: {
							drawBorder: true,
							color: '#f4f4f4',
							zeroLineColor: '#f4f4f4',
						},
						ticks: {
							min: 0,
							beginAtZero: true,
							userCallback: function(label, index, labels) {
								// when the floored value is the same as the value we have a whole number
								if (Math.floor(label) === label) {
									return label;
								}
							},
						}
					}],
					xAxes: [{
						gridLines: {
							drawBorder: false,
							zeroLineColor: '#f4f4f4',
							display: false,
							align: 'start'
						},
						ticks: {
							maxTicksLimit: 8,
						}
					}]
				},
				tooltips: {
					xPadding: 16,
					yPadding: 10,
					cornerRadius: 0,
					titleMarginBottom: 10,
					callbacks: {
						title: function(tooltipItem, data) {
							var tooltipData = datasetsTooltips[tooltipItem[0].xLabel];
							return tooltipData.tooltip_title;
						},
						label: function(tooltipItem, data) {
							var tooltipData = datasetsTooltips[tooltipItem.xLabel];
							return tooltipData.tooltip_text;
						}
					}
				},
				legend: {
					display: false
				},
				title: {
					display: true,
					text: chartTitle
				}
			};

			window.myLine = new Chart('chart-revenue', {
				type: 'line',
				data: {
					labels: dateLabels,
					datasets: datasets
				},
				options: options
			});
		}

		function renderPlans(plansFigure, chartTitle) {
			toggleLoader(true);

			planData = [];
			planColor = [];

			planDataTooltips = [];
			planLabels = [];

			if (plansFigure) {
				var index = 0;

				$.each(plansFigure, function(key, value) {
					planLabels.push(value.shortTitle);
					planData.push(value.total_2);
					planColor.push(value.background_color);


					planDataTooltips[index] = value;
					index++;
				});
			} else {
				var wrapper = $('[data-pp-analytics-plan]');
				wrapper.addClass('is-empty');
				wrapper.find('#canvas-holder').addClass('t-hidden');
				return;
			}

			var config = {
				type: 'doughnut',
				data: {
					labels: planLabels,
					datasets: [{
						data: planData,
						backgroundColor: planColor
					}]
				},
				options: {
					cutoutPercentage: 80,
					responsive: true,
					legend: {
						position: 'bottom',
						fullWidth: true,

						labels: {
							boxWidth: 26
						}
					},
					title: {
						display: false,
						text: chartTitle
					},
					animation: {
						animateScale: true,
						animateRotate: true
					},
					tooltips: {
						xPadding: 16,
						yPadding: 10,
						cornerRadius: 0,
						titleMarginBottom: 10,
						callbacks: {
							title: function(tooltipItem) {
								var tooltipData = planDataTooltips[tooltipItem[0].index];
								return tooltipData.title;
							},
							label: function(tooltipItem, data) {
								var tooltipData = planDataTooltips[tooltipItem.index];
								return tooltipData.tooltip_text;
							}
						}
					}
				}
			};

			var ctx = document.getElementById('chart-area').getContext('2d');
			window.myDoughnut = new Chart(ctx, config);
		}

		function renderStatistic() {
			$('[data-analytics-chart-plans]').removeClass('is-empty');
			toggleLoader(false);

			// Destroy previous chart if exists
			destroyStatistic();

			$('[data-chart-listings]').html('');

			PayPlans.ajax('admin/controllers/analytics/getStatistic', {
				"duration" : "<?php echo $duration; ?>",
				"type": "<?php echo $type; ?>",
				"customStartDate": "<?php echo $customStartDate; ?>",
				"customEndDate": "<?php echo $customEndDate; ?>",
				"dummyData" : "<?php echo $dummyData; ?>"
			}).done(function(chartData) {

				var type = '<?php echo $type; ?>';

				renderChartGraph(chartData.chartFigure, chartData.chartTitle, type);
				renderPlans(chartData.plansFigure, type);

				var chartLabel = $('[data-pp-analytics-label]');
				chartLabel.html(chartData.chartFigureLabel);

				$('[data-chart-listings]').html(chartData.listings)
			});
		}

		function startRebuild(current, totalDays, rebuildLimit, form) {
			PayPlans.ajax('admin/controllers/analytics/rebuildStat', {
				'current' : current,
				'totalDays' : totalDays,
				'rebuildLimit' : rebuildLimit,
				'type': '<?php echo $type; ?>'
			}).done(function(message) {
				current = current + rebuildLimit;

				var percentage = current / totalDays * 100;
				form.progressBar().css('width', percentage + '%');
				form.progressCounter().html(message);

				if (percentage > 99) {
					rebuildComplete(form);
				} else {
					startRebuild(current, totalDays, rebuildLimit, form);
				}
			});
		}

		function rebuildComplete(form) {
			form.progressInfo().addClass('t-hidden');
			form.startButton().addClass('t-hidden');
			form.finishInfo().removeClass('t-hidden');

			// Override close button
			form.cancelButton().on('click', function() {
				renderStatistic();
			});
		}

		function rebuildStat() {

			// Render confirmation dialog
			PayPlans.dialog({
				"content": PayPlans.ajax('admin/views/analytics/confirmRebuildStat'),
				"bindings": {
					"{startButton} click": function() {
						var totalDays = parseInt(this.form().find('[data-total-days]').val());
						var rebuildLimit = parseInt(this.form().find('[data-rebuild-limit]').val());

						var progressBar = this.progressBar();
						this.confirmationInfo().addClass('t-hidden');
						this.progressWrapper().removeClass('t-hidden');

						var current = 0;

						startRebuild(current, totalDays, rebuildLimit, this);
					}
				}
			});
		}

		function destroyStatistic() {
			if (window.myLine) {
				window.myLine.destroy();
			}

			if (window.myDoughnut) {
				window.myDoughnut.destroy();
			}
		}

		renderStatistic();

		function showSalesTooltip(activeElements) {
			window.myLine.tooltip._active = activeElements != undefined ? activeElements : [];
			window.myLine.tooltip.update(true);
			window.myLine.draw();
		}

		$(document).on('mouseover.pp.analytics', '[data-chart-list]', function() {
			var index = $(this).data('index');
			var requestedElem = window.myLine.getDatasetMeta(0).data[index];

			showSalesTooltip([requestedElem]);
		});

		$(document).on('mouseleave.pp.analytics', '[data-chart-list]', function() {
			showSalesTooltip();
		});

		$.Joomla('submitbutton', function(task) {

			if (task == 'updateStat') {
				renderStatistic();
			}

			if (task == 'rebuildStat') {
				rebuildStat();
			}
		});

	})
});
