//$('#login').on('tap', function() {
//		if($('.phone').val() == '' && $('.password').val() == '') {
//			alert('请输入手机号码和密码！');
//		} 
//		else if($('.password').val() == '') {
//			alert('请输入密码！');
//		} 
//		else if($('.phone').val() == '') {
//			alert('请输入手机号！');
//		} 		
//		else if($('.password').val() != '' && $('.phone').val() != '') {
//			 var phone = document.getElementById('acc').value;
//				if(!(/^1(3|4|5|7|8)\d{9}$/.test(phone))) {
//			         alert("手机号码有误，请重填");		
//			    }
//			    else{
//					mui.openWindow({
//						url: '../shouye/shouye.html',
//						id: '../shouye/shouye.html',
//						show: {
//							aniShow: 'zoom-fade-out',
//							duration: 300
//						}
//				    });
//				}							
//		}
//		
//		
//})

$('#login').click(function(){
	if($('select').val()!==1){
	$('.mask').show();
	setTimeout(function(){
		$('.mask').hide();
		},1000)
	}
})

