;$(function(){
	/*全选实现*/
	$(".check-all").click(function () {
	    $(".ids").prop("checked", this.checked);
	});
	$(".ids").click(function () {
	    var option = $(".ids");
	    option.each(function (i) {
	        if (!this.checked) {
	            $(".check-all").prop("checked", false);
	            return false;
	        } else {
	            $(".check-all").prop("checked", true);
	        }
	    });
	});
	
	//重新加载
	$('.re-load').click(function () {
	    var target;
	    if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {
	    	var index = layer.load('正在跳转');
	    	window.location.href = target;
	    }
	});
	
	//ajax get请求
	$('.ajax-get').click(function () {
	    var target;
	    if($(this).hasClass('confirm')){//判断是否需要确认
	    	 var nead_confirm = true;
	    }else{
	    	 var nead_confirm = false;
	    }
	    if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {
	    	 if(nead_confirm){
		        	layer.confirm('确定执行该操作？', function(index){
		        		var index = layer.load('正在执行，请耐心等待');
		               $.get(target,success, "json");
		        	});   
		        }else{
		        	var index = layer.load('正在执行，请耐心等待');
		           $.get(target,success, "json");
		        }
	    }
	    return false;
	});
	
	/*post提交数据*/
	$('.ajax-post').click(function () {
	    var data;//提交数据
	    var target_form = $(this).attr('target-form');
	    var msg = '确定执行该操作吗？';
	    msg = $(this).attr('data-msg')?($(this).attr('data-msg')+',确定执行该操作吗？'):msg;
	    if($(this).hasClass('confirm')){//判断是否需要确认
	    	 var nead_confirm = true;
	    }else{
	    	 var nead_confirm = false;
	    }
	    
	    var flag = true;
	   
	    var target = ($(this).attr('href'))||($(this).attr('url'));
	   
	    if (($(this).attr('type') == 'submit') || target) {
	    	var form = $(this).parents('.' + target_form);
	    	
	        if (form.get(0) == undefined) {
	            return false;
	        } else if (form.get(0).nodeName == 'FORM') {
	            if ($(this).attr('url') !== undefined) {
	                target = $(this).attr('url');
	            } else {
	                target = form.get(0).action;
	            }
	           
	            /*判断必填表单是否为空*/
	            form.find('.req').each(function(){
	            	p_ipt = $(this).parent('label').next('.formRight');
	            	this_ipt = (p_ipt.find('input[type="text"]'))||(p_ipt.find('input[type="password"]'));
	            	if(this_ipt.val() == false){
	            		this_ipt.focus();
	            		this_ipt.removeClass('form-success');
	            		this_ipt.addClass('form-error');
	            		form.find('input[target-form]').hide();
	            		flag = false;
	            		return false;
	            	}
	            });
	            if(flag==false){return false;}
	            data = form.serialize();
	            
	        } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
	        	data = form.serialize();
	        } else {
	        	data = form.find('input,select,textarea').serialize();
	        }
	        
	        if(nead_confirm){
	        	
	        	layer.confirm(msg, function(index){
	        		var index = layer.load('正在提交，请耐心等待');
	     	        $.post(target,data,success, "json");
	        	});   
	        }else{
	        	var index = layer.load('正在提交，请耐心等待');
	        	$.post(target,data,success, "json");
	        }
	    }
	    return false;
	});
})

function success(data) {
	if (data.status) {
		layer.msg(data.info,3,1);
		setTimeout(function () {
			var index = layer.load('正在跳转...');
	        window.location.href = data.url
	    }, 2000);
		 return false;
	} else {
		layer.msg(data.info,3,3);
		 return false;
	}
}

//导航高亮
function highlight_subnav(url) {
	$('#menu').find('a[href="' + url + '"]').parents('.sub').show();
	$('#menu').find('a[href="' + url + '"]').parent('li').addClass('this');
    $('#menu').find('a[href="' + url + '"]').parents('.sub').prev('a').addClass('active check');
    $('#menu').find('a[href="' + url + '"]').parents('.sub').parent('li').siblings('li').find('a').removeClass('active check');
    $('#menu').find('a[href="' + url + '"]').parents('.sub').parent('li').siblings('li').find('ul').hide();
}