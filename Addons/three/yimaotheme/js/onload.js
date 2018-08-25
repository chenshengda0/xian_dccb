// JavaScript Document
var menu="财务中心,会员中心,订单中心,商品中心,邮箱中心,文章中心,系统中心,管理中心";
var op="";
ms=menu.split(",");
$(document).ready(function(){
	$('.menuh').bind('click', item_open);

	//奇数行
	$('.yimaolist tbody tr:even').addClass('even');
	//偶数行
	$('.yimaolist tbody tr:odd').addClass('odd');

	//鼠标进入或者移出
	$('.yimaolist tbody tr').hover(
		function() {$(this).addClass('highlight').css("cursor","default");},
		function() {$(this).removeClass('highlight');}
	);

	// 复选框 或者 单选
	$('.yimaolist tr:gt(0)').click(
		function() {

			var checkbox = $(this).find('input[type="checkbox"]').attr('checked');
			var data = $(this).find('input[type="checkbox"]').attr('data');

			if (checkbox || $(this).attr("class").indexOf("selected")>=0) {

				$(this).removeClass('selected');
				
				if(data!=1){
				$(this).find('input[type="checkbox"]').removeAttr('checked');
				}
				$(this).find('input[type="checkbox"]').attr('data','0');
			} else {

				$(this).addClass('selected');
				$(this).find('input[type="checkbox"]').attr('checked','checked');
			}

		}
	);

	// 复选框 或者 单选
	$('.yimaolist input[type="checkbox"]').click(
		function() {

			var checkbox = $(this).attr('checked');
			if(checkbox=="checked"){
				$(this).attr('data',"1");
			}else{
				$(this).attr('data',"0");
			}
		}
	);

	//保存打开的菜单
	var cd=$('div .menuh');
	for(var i=0;i<cd.length;i++){
		if(op.indexOf($(cd[i]).find('span').text())!=-1){
			$(cd[i]).next().show();
		}	
	}

})

function item_close(){
	var cd=$('div .menuh');
	for(var i=1;i<cd.length;i++){

		$(cd[i]).next().css("display","none");
		document.cookie=escape($(cd[i]).find('span').text())+"=none";
	}
}

function item_open(){
	if($(this).next().is(":visible")){
		$(this).next().css("display","none");
		$(this).find('i').html("+");
		document.cookie=escape($(this).find('span').text())+"=none";
	}else{
		$(this).next().show();
		$(this).find('i').html("-");
		$(this).css("background-position","left -50px");
		document.cookie=escape($(this).find('span').text())+"="+escape($(this).find('span').text());
	}
}


//读取cookie保存打开的菜单
var strCookie=document.cookie; 
	var arrCookie=strCookie.split("; ");
	for(var i=0;i<arrCookie.length;i++){ 
		var arr=arrCookie[i].split("="); 
		 
		for(var j=0;j<ms.length;j++){
			if(unescape(arr[0])==ms[j]&&arr[1]!="none"){
				op=op+unescape(arr[0]);	
			}
						
		}
}


function checkall(n){

	var a=document.getElementsByName(n);
	var len=a.length;
	for(var i=0;i<len;i++)
	{
	    a[i].checked=!a[i].checked;
	}

}


function boxcheck(t1,t2,t3){
	var num=0;
	num=$("input[name='cbid[]']:checked").length;
	if(num==0){
		alert(t1);
	}else{
		if(confirm(t2)){
			artload(t3)
			return true;
		}else{
			return false
		}
	}
	return false;
}


function huana(t){
	var quick=document.getElementsByName('quickaa');
	for (var i =  0; i < quick.length; i++) {
		quick[i].className='quicka';
	}
	t.className ="quickasel";
}

function artload(c){

   art.dialog({
    cancel:false,
    lock: true,
    dbclickHide:false, 
    content: '<span style="font-size:16px">★ '+c+'</span>'
});

}

function artbck(c){

   art.dialog({
    cancel:function(){history.go(-1);},
    lock: true,
    dbclickHide:true, 
    content: '<span style="font-size:16px">★  '+c+'</span>'
});

}

function artalert(c,t){
	if(t==0){
	art.dialog({
		lock: true,
	    icon: 'error',
	    content: c
	}); 
	}else{
	art.dialog({
		lock: true,
	    icon: 'succeed',
	    content: c
	}); 
	}
}

function checkbox(t,s){
	  var o=$('.'+s);
	  $.each(o,function(){
	  if(t.checked){
	  	 o.attr('checked','checked');
	  }else{
	  	 o.removeAttr('checked');
	  }
     
  });
}

function showhidden(n){
	if($(n).css("display")=="none"){
		$(n).css("display","block");	
	}else{
		$(n).css("display","none");
	} 
}

function qiehuan(v,s,t){
	if(v==1){
		$("."+s+'1').css('display',"");
		$("."+s+'2').css('visibility',"hidden");
		$("#"+t+'1').attr('class',"hbase");
		$("#"+t+'2').attr('class',"hcontent");
	}else{
		$("."+s+'1').css('display',"none");
		$("."+s+'2').css('visibility',"");
		$("#"+t+'1').attr('class',"hcontent");
		$("#"+t+'2').attr('class',"hbase");
	}
	
}