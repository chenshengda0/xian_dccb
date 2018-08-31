var b=1;
		var timer2=setInterval(function(){		
			if(b==1){			
				b=1.4;
			$('.quan').css({
				'-webkit-transition': 'all 1.5s',
				'-moz-transition': 'all 1.5s',
				'transition': 'all 1.5s',
				'-webkit-transform':'scale('+b+')',
				'-moz-transform':'scale('+b+')',
				'transform':'scale('+b+')',
				'opacity':'.5'
			})
			}else{
				b=1;
			$('.quan').css({
				'-webkit-transition': 'all 1.5s',
				'-moz-transition': 'all 1.5s',
				'transition': 'all 1.5s',
				'-webkit-transform':'scale('+b+')',
				'-moz-transform':'scale('+b+')',
				'transform':'scale('+b+')',
				'opacity':'1'
			})
			}
		},700)
	//设备宽度
	var s = window.screen;
	var width = q.width = s.width;
	var height = q.height;
	var yPositions = Array(300).join(0).split('');
	var ctx = q.getContext('2d');
	var draw = function() {
		ctx.fillStyle = 'rgba(0,0,0,.05)';
		ctx.fillRect(0, 0, width, height);
		ctx.fillStyle = '#00ffff'; /*代码颜色*/
		ctx.font = '10pt Georgia';
		yPositions.map(function(y, index) {
			text = String.fromCharCode(1e2 + Math.random() * 330);
			x = (index * 10) + 10;
			q.getContext('2d').fillText(text, x, y);
			if(y > Math.random() * 1e4) {
				yPositions[index] = 0;
			} else {
				yPositions[index] = y + 10;
			}
		});
	};
	RunMatrix();

	function RunMatrix() {
		Game_Interval = setInterval(draw, 50);
	}
