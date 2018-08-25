//dom加载完成后执行的js
;
$(function(){
	
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
	    if($(this).hasClass('confirm')){//判断是否需要确认
	    	 var nead_confirm = true;
	    }else{
	    	 var nead_confirm = false;
	    }
	    
	    var target = ($(this).attr('href'))||($(this).attr('url'));
	    
	    if (($(this).attr('type') == 'submit') || target) {
	    	var form = $('.' + target_form);
	    	
	        if (form.get(0) == undefined) {
	            return false;
	        } else if (form.get(0).nodeName == 'FORM') {
	            if ($(this).attr('url') !== undefined) {
	                target = $(this).attr('url');
	            } else {
	                target = form.get(0).action;
	            }
	            data = form.serialize();
	        } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
	        	data = form.serialize();
	        } else {
	        	data = form.find('input,select,textarea').serialize();
	        }
	        if(nead_confirm){
	        	layer.confirm('确定执行该操作？', function(index){
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
$('#submit').click(function(){
    //$("#username").trigger("blur");
	$("#submit").addClass('ajax-post');
	
	$(".ajax-post").trigger("click");
	return false;
	if($('#submit').val()=='Login'){
		
		$('.mask').show();
		setTimeout(function(){
			$('.mask').hide();
		},1200)
		return false;
    }else{
    	
    	return true;
    }
})
function tartet_success(data) {
    if (data.status) {
    	layer.msg(data.info,3,1);
    	setTimeout(function () {
    		var index = layer.load('正在跳转...');
            window.open(data.url);
            layer.close(index);
        }, 2000);
    } else {
    	layer.msg(data.info,3,3);
    }
}
//导航高亮
function highlight_subnav(url) {
	var base = $('#sidebar').find('a[href="' + url + '"]');
	submenu = base.parents('.submenu');
	if(submenu.hasClass('submenu')){
		base.parents('ul').show();
		base.parents('.submenu').siblings('li').removeClass('active');
		base.addClass('current');
		base.parents('.submenu').addClass('active');
		base.parent('li').attr('style','background-color:#6ccac9;');
	}else{
		base.parents('li').addClass('active');
		base.addClass('current');
		base.parents('li').siblings('li').removeClass('active');
	}
}

moduleManager = {
    'install': function (id) {
        $.post(U('admin/module/install'),{id:id},function(msg){
            handleAjax(msg);
        })
    },
    'uninstall': function (id) {
        $.post(U('admin/module/uninstall'),{id:id},function(msg){
            handleAjax(msg);
        })

    }

}