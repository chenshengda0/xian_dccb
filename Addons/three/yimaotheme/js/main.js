$(function(){ //哈
	erMenu(); 
	bindLeftMenu();

	//奇数行
	$('.biaotab tbody tr:even').addClass('even');
	//偶数行
	$('.biaotab tbody tr:odd').addClass('odd');
		
});
	
	
function erMenu(){
	$(".navLi").mouseover(function(){
		$(this).find(".ermenu").show();
	})
	$(".navLi").mouseleave(function(){
		$(this).find(".ermenu").hide();
	})
}

function bindLeftMenu()
{
	$(".menu li").click(function(){
		$(".menu li").removeClass("li_active2");
		$(this).addClass("li_active2");
	});
	$(".menu li div").click(function(){
		$(".menu li").find("div").removeClass("li_active");
		$(this).addClass("li_active");
		
	})
}

function arttip(c){
	art.dialog({
		lock: true,
	    icon: 'warning',
	    content: c,
	    okVal: '确定',
	    ok: function () {

	    },
	    cancel: false	    
	}); 
}


function chkpwd(){
	var p1=$.trim($("#ypwd").val());
	var p2=$.trim($("#npwd").val());
    if ($("input[name='mtype']:checked").val()==undefined){
		arttip("请选择密码类型");
		return false;
    }

	if(p1==""){
		arttip("请输入原密码");
		return false;
	}
	if(p1.length<8 || p1.length>20){
		arttip("原密码输入错误");
		return false;
	}
	if(p2==""){
		arttip("请输入新密码");
		return false;
	}	
	if(p2.length<8 || p2.length>20){
		arttip("新密码输入错误");
		return false;
	}
	if(p1==p2){
		arttip("新密码不能和原密码相同");
		return false;
	}	
	return true;
}

function chkmb(){
	var p1=$.trim($("#mbq").val());
	var p2=$.trim($("#mba").val());

	if(p1==-1){
		arttip("请选择密保问题");
		return false;
	}
	if(p2==""){
		arttip("请输入密保答案");
		return false;
	}
	
	return true;
}

function chkmbm(){
	var p1=$.trim($("#mbq").val());
	var p2=$.trim($("#mba").val());
	var p3=$.trim($("#pwd").val());

	if(p3==""){
		arttip("请输入密码");
		return false;
	}	
	if(p1==-1){
		arttip("请选择密保问题");
		return false;
	}
	if(p2==""){
		arttip("请输入密保答案");
		return false;
	}
	
	return true;
}

function chkmm(){
	var p1=$.trim($("#pwd").val());

	if(p1==""){
		arttip("请输入密码");
		return false;
	}	
	
	return true;
}

function shizhuan(){
	var p2=$.trim($("#jine").val());
	var re=/^[1-9]+[0-9]*]*$/;
	if(!re.test(p2))
	{
		$("#shijin").val(0);
	}else{
		z=parseInt(p2)-p2*ks/100;

		$("#shijin").val(z);
	}

}


function chkzhuan(){

	var p2=$.trim($("#jine").val());
	var p3=$.trim($("#username").val());
	var p4=$.trim($("#did").val());


	if(p3==""){
		arttip("请输入对方帐号");
		return false;
	}
	if(p4==0){
		arttip("对方帐号不存在");
		return false;
	}		
	if(p2==""){
		arttip("请输入转账金额");
		return false;
	}	
	var re=/^[1-9]+[0-9]*]*$/;
	if(!re.test(p2))
	{
		arttip("转账金额请输入数字");
		return false;
	}
	if(p2<zhuanmin){
		arttip("转账最小金额"+zhuanmin+"起");
		return false;
	}
	return true;
}

function chkhuifu(){
	var p1=$.trim($("#emcontent").val());

	if(p1==""){
		arttip("请输入回复内容");
		return false;
	}	
	
	return true;
}

function chkbank(){
	var p1=$.trim($("#bankname").val());
	var p2=$.trim($("#bankcard").val());
	var p3=$.trim($("#bankuser").val());


	if(p1==""){
		arttip("请输入银行名称");
		return false;
	}
	if(p2==""){
		arttip("请输入银行卡号");
		return false;
	}
	if(p3==""){
		arttip("请输入银行姓名");
		return false;
	}

	return true;
}

function shitiqu(){
	var p2=$.trim($("#jine").val());
	var re=/^[1-9]+[0-9]*]*$/;
	if(!re.test(p2))
	{
		$("#shijin").val(0);
	}else{
		z=parseInt(p2)-p2*ks/100;

		$("#shijin").val(z);
	}

}

function chktiqu(){
	var p1=$.trim($("#bank").val());
	var p2=$.trim($("#jine").val());
	var p3=$.trim($("#pwd").val());

	if(p1==""){
		arttip("请选择一个银行");
		return false;
	}
	if(p2==""){
		arttip("请输入提现金额");
		return false;
	}	
	var re=/^[1-9]+[0-9]*]*$/;
	if(!re.test(p2))
	{
		arttip("提现金额请输入数字");
		return false;
	}

	if(p3==""){
		arttip("请输入密码");
		return false;
	}
	return true;
}

function chkchong(){

	var p2=$.trim($("#jine").val());

	if(p2==""){
		arttip("请输入充值金额");
		return false;
	}	
	var re=/^[1-9]+[0-9]*]*$/;
	if(!re.test(p2))
	{
		arttip("充值金额请输入数字");
		return false;
	}
	if(p2<chongmin){
		arttip("充值最小金额"+chongmin+"起");
		return false;
	}
	return true;
}


function chkcq(){

	var p2=$.trim($("#jine").val());

	if(p2==""){
		arttip("请输入提供帮助金额");
		return false;
	}	
	var re=/^[1-9]+[0-9]*]*$/;
	if(!re.test(p2))
	{
		arttip("提供帮助金额请输入数字");
		return false;
	}
	if(p2<cqmin){
		arttip("存钱最小金额"+cqmin+"起");
		return false;
	}
	if(p2%cqbs!=0){
		arttip("存钱必须是"+cqbs+"整数倍");
		return false;
	}

	var p3=$.trim($("#pwd").val());


	if(p3==""){
		arttip("请输入密码");
		return false;
	}
			
	return true;
}

function chkqk(){

	var p2=$.trim($("#jine").val());

	if(p2==""){
		arttip("请输入获取帮助金额");
		return false;
	}	
	var re=/^[1-9]+[0-9]*]*$/;
	if(!re.test(p2))
	{
		arttip("获取帮助金额请输入数字");
		return false;
	}
	if(p2<qkmin){
		arttip("获取帮助最小金额"+qkmin+"起");
		return false;
	}
	if(p2%qkbs!=0){
		arttip("获取帮助必须是"+qkbs+"整数倍");
		return false;
	}

	var p3=$.trim($("#pwd").val());


	if(p3==""){
		arttip("请输入密码");
		return false;
	}
			
	return true;
}


function chkhuan2(){


	var p2=$.trim($("#jine").val());

	if(p2==""){
		arttip("请输入转换金额");
		return false;
	}	
	var re= /^[0-9]+.?[0-9]*$/;
	if(!re.test(p2))
	{
		arttip("转换金额请输入数字");
		return false;
	}
	if(p2<huanmin){
		arttip("转换最小金额"+huanmin+"起");
		return false;
	}
	return true;

}



function chkhuan(){

	var p2=$.trim($("#jine").val());

	if(p2==""){
		arttip("请输入转换金额");
		return false;
	}	
	var re=/^[1-9]+[0-9]*]*$/;
	if(!re.test(p2))
	{
		arttip("转换金额请输入数字");
		return false;
	}
	if(p2<huanmin){
		arttip("转换最小金额"+huanmin+"起");
		return false;
	}
	return true;
}


function getusername(){
   var s = $("#username").attr("value");
  $.ajax({
    type: "get",
    url: "ajax.php?act=getusername&username="+s,
    dataType: "json",
    success: function(json){ 
	  if($.trim(json.names)!=""){
		$('#ztxt').html(json.names);
		$('#did').val(1);

	  }else{
		$('#ztxt').html("");
		$('#did').val(0);

	  }
    },
    error: function(){
     
    }
  })
}



function chksend(){

	var p1=$.trim($("#username").val());
	var p2=$.trim($("#lytitle").val());
	var p3=$.trim($("#lycontent").val());

    if ($("input[name='lx']:checked").val()==1){
		if(p1==""){
			arttip("请输入收件人");
			return false;
		}	
    }

	if(p2==""){
		arttip("请输入留言标题");
		return false;
	}	
	if(p3==""){
		arttip("请输入留言内容");
		return false;
	}	

	return true;
}

function boxcheck(t1,t2){

	if(confirm(t1)){
		artload(t2)
		return true;
	}else{
		return false
	}

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

function artload1(c){

   art.dialog({
    cancel:true,
    lock: true,
    dbclickHide:false, 
    content: '<span style="font-size:16px">'+c+'</span>'
});

}