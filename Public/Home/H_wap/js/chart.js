window.onload=function(){
	var myChart = echarts.init(document.getElementById('main'));
	option = {
	    tooltip: {
	        trigger: 'axis'
	    },
	    
//	    legend: {
//	        data: ['公共利率', '最新利率'],
//	        top:'12%',
//	        textStyle: {
//	                   color:['#fff']
//	                }
//	    },
	    grid: {
	            left: '-10px',
	            right: '4%',
	            bottom: '7%',
	            containLabel: true,
	        },
	    xAxis:  {
	        type: 'category',
	        boundaryGap: false,
	        data: ['<?php echo date("m/d",time()-7)?>','<?php echo date("m/d",time()-6)?>',
				'<?php echo date("m/d",time()-5)?>','<?php echo date("m/d",time()-4)?>',
				'<?php echo date("m/d",time()-3)?>','<?php echo date("m/d",time()-2)?>',
				'<?php echo date("m/d",time()-1)?>'],
	        axisLine: {
	                show: true,
	                lineStyle: {
	                    color: '#434750',
	                    width: '3'
	                }
	            },
	             axisTick: {
	                show: true,
	                inside: true,
	                lineStyle: {
	                    color: '#00ffff',
	                    width: '3',
	                    ype: 'dashed'
	                }
	            },
	            splitLine: {
	                show: true,
	                lineStyle: {
	                    color: '#fff',
	                    type: 'dotted'
	                }
	            },
	            axisLabel: {
	                textStyle: {
	                    color: '#fff'
	                }
	            }
	    },
	    yAxis: {
	            show: false,
	            type: 'value',
	            axisLabel: {
	                show: false,
	                formatter: '{value} %'
	            },
	            axisTick: {
	                show: false
	            },
	            axisLine: {
	                show: false
	            }
	        },
	    series: [
	    {
	            name:'买入',
	            type:'line',
	            stack: '总量',
	                areaStyle: {
	                    normal: {
	                        color: '#fff'
	                    }
	                },
	                label: {
	                    normal: {
	                        show: true,
	                        position: 'bottom'
	                    }
	                },
	            data:[11, 11, 15, 13, 12, 13, 10],
	            
	                lineStyle: {
	                    normal: {
	                        color: '#fff'
	                    }
	                },
	                itemStyle: {
	                    normal: {
	                        color: '#fff'
	                    }
	                }   
	        },
	     {
	            name:'卖出',
	            type:'line',
	            stack: '总量',
	            label: {
	                    normal: {
	                        show: true,
	                        position: 'bottom'
	                    }
	                },
	            data:[1, -2, 2, 5, 3, 2, 1],
	            lineStyle: {
	                    normal: {
	                        color: '#00ffff'
	                    }
	                },
	                itemStyle: {
	                    normal: {
	                        color: '#00ffff'
	                    }
	                }
	           
	       },
	        
	       
	    ]
	};
	myChart.setOption(option);
}