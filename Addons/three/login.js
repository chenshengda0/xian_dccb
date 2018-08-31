// JavaScript Document
function checklogin(){
	var d = /^[^,'"]+$/;

	var user=document.form1.user.value;
	var pwd=document.form1.pwd.value;
	var yzm=document.form1.yzm.value;

	if(document.form1.user.value.length == 0){
		alert("\n请输入账号！");
		document.form1.user.focus();
		return false;
	}

	if (user = strim(user), "" == user || null == user) {
		alert("\n请输入账号！");
		document.form1.user.focus();
		return false;
	}

	if(!d.test(user))
	{
		alert("\n账号输入敏感字符！");
		document.form1.user.focus();
		return false;
	}

	if(document.form1.pwd.value.length == 0){
		alert("\n请输入密码！");
		document.form1.pwd.focus();
		return false;
	 }

	if (pwd = strim(pwd), "" == pwd || null == pwd) {
		alert("\n请输入密码！");
		document.form1.pwd.focus();
		return false;
	}

	if(!d.test(pwd))
	{
		alert("\n密码输入敏感字符！");
		document.form1.user.focus();
		return false;
	}


	if(document.form1.yzm.value.length == 0){
		alert("\n请输入验证码！");
		document.form1.yzm.focus();
		return false;
	 }
	if (yzm = strim(yzm), "" == yzm || null == yzm) {
		alert("\n请输入验证码！");
		document.form1.yzm.focus();
		return false;
	}

	 return true;
}

function strim(a) {
	return a.replace(/(^\s*)|(\s*$)/g, "");
}