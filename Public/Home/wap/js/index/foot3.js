var add = $('.mid .dd');
var add1 = $('.dd1');
add.click(function() {
	add.removeClass('active');
	add1.removeClass('cur');
	$(this).addClass('active');
	add1.eq($(this).index()).addClass('cur');
});
var addd = $('.mid .ddd');
var addd1 = $('.ddd1');
addd.click(function() {
	addd.removeClass('active');
	addd1.removeClass('cur');
	$(this).addClass('active');
	addd1.eq($(this).index()).addClass('cur');
});
var myChart = echarts.init(document.getElementById('lineChart1'));
var option1 = {
	tooltip: {
		trigger: 'axis',
		formatter: "Temperature : <br/>{b}km : {c}°C"
	},
	grid: {
		left: '3%',
		right: '4%',
		top: '15%',
		containLabel: true
	},
	xAxis: {
		type: 'category',
		axisLabel: {
			formatter: '{value} °C'
		},
		data: ['0', '10', '20', '30', '40', '50', '60', '70', '80']
	},
	yAxis: {
		type: 'value',
		axisLine: {
			onZero: false
		},
		axisLabel: {
			formatter: '{value} km'
		},
		boundaryGap: false
	},
	series: [{
		name: '',
		type: 'line',
		smooth: true,
		lineStyle: {
			normal: {
				width: 3,
				shadowColor: 'rgba(0,0,0,0.4)',
				shadowBlur: 10,
				shadowOffsetY: 10
			}
		},
		data: [15, -50, -56.5, -46.5, -22.1, -2.5, -27.7, -55.7, -76.5]
	}]
};
myChart.setOption(option1);
//		var getOption = function(chartType) {
//				var chartOption = chartType == 'pie' ? {
//					calculable: false,
//					series: [{
//						name: '访问来源',
//						type: 'pie',
//						radius: '65%',
//						center: ['50%', '50%'],
//						data: [{
//							value: 335,
//							name: '直接访问'
//						}, {
//							value: 310,
//							name: '邮件营销'
//						}, {
//							value: 234,
//							name: '联盟广告'
//						}, {
//							value: 135,
//							name: '视频广告'
//						}, {
//							value: 1548,
//							name: '搜索引擎'
//						}]
//					}]
//				} : {
//					
//					grid: {
//						x: 35,
//						x2: 10,
//						y: 30,
//						y2: 25
//					},
//					toolbox: {
//						show: false,
//						feature: {
//							mark: {
//								show: true
//							},
//							dataView: {
//								show: true,
//								readOnly: false
//							},
//							magicType: {
//								show: true,
//								type: ['line', 'bar']
//							},
//							restore: {
//								show: true
//							},
//							saveAsImage: {
//								show: true
//							}
//						}
//					},
//					calculable: false,
//					xAxis: [{
//						type: 'category',
//						data: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月']
//					}],
//					yAxis: [{
//						type: 'value',
//						splitArea: {
//							show: true
//						}
//					}],
//					series: [{
//						name: '蒸发量',
//						type: chartType,
//						data: [2.0, 4.9, 7.0, 23.2, 25.6, 76.7, 135.6, 162.2, 32.6, 20.0, 6.4, 3.3]
//					}, {
//						name: '降水量',
//						type: chartType,
//						data: [2.6, 5.9, 9.0, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6.0, 2.3]
//					}]
//				};
//				return chartOption;
//			};
//			var byId = function(id) {
//				return document.getElementById(id);
//			};
//			var lineChart = echarts.init(byId('lineChart'));
//			lineChart.setOption(getOption('line'));
//			byId("echarts").addEventListener('tap',function(){
//				var url = this.getAttribute('data-url');
//				plus.runtime.openURL(url);
//			},false);