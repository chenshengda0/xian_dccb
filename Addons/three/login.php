<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>系统登录</title>
<link href="asset/css/login.css" rel="stylesheet" rev="stylesheet" type="text/css" media="all" />
<link href="asset/css/demo.css" rel="stylesheet" rev="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="asset/js/jquery1.42.min.js"></script>


<script>




</script>


</head>

<body>


<div class="banner">


<div style="width:600px;line-height:400px;position:absolute;color:#fff;z-index:9999999; left:16%; top:80px;">

</div>

<div class="login-aside">
  <div id="o-box-up"></div>
  <div id="o-box-down"  style="table-layout:fixed;">
   <div class="error-box" style="text-align:center;color:#fff;margin-left:70px;font-weight:bold;width:100px;margin-top:-20px">3M互助国际金融</div>
   
   <form class="registerform" action="checklogin.php" method="post" id="form1" name="form1" onsubmit="return checklogin();"><input type="hidden" name="action" value="save">

   <div class="fm-item">
	   <label for="logonId" class="form-label">登入系统：</label>
	   <input type="text" value="" placeholder="账号"  maxlength="100" id="user" name="user" class="i-text"   datatype="s6-18" errormsg="警告：用户名至少6个字符,最多18个字符！"  >    
       <div class="ui-form-explain"></div>
  </div>
  
  <div class="fm-item">
	   <label for="logonId" class="form-label">登陆密码：</label>
	   <input type="password" value="" maxlength="100" id="pwd" name="pwd" class="i-text" datatype="*6-16" placeholder="密码"  nullmsg="x" errormsg="警告：密码范围在6~16位之间！">    
       <div class="ui-form-explain"></div>
  </div>
  
  <div class="fm-item pos-r">
	   <label for="logonId" class="form-label">验证码</label>
	   <input type="text" value="" maxlength="100" id="yzm" name="yzm" class="i-text yzm" placeholder="验证码" nullmsg="验证码" >    
       <div class="ui-form-explain"><img src='yimaoinclude/code.php' onclick='this.src="yimaoinclude/code.php?"+Math.random()' class="yzm-img"  style='cursor:pointer;line-height:37px;height:37px' title='看不清楚点击可以刷新' ></div>
  </div>
  
  <div class="fm-item">
	   <label for="logonId" class="form-label"></label>
	   <input type="submit" value="" tabindex="4" id="send-btn" class="btn-login"> 
       <div class="ui-form-explain" style="margin-top:10px;"><a href="r.php" style="color:#0f163c;text-decoration:underline">注册</a>&nbsp;<a href="forget.php" style="color:#0f163c;text-decoration:underline">找回密码</a></div>
  </div>
  
  </form>
  
  </div>

</div>

	<div class="bd">
		<ul>
			<li style="background:url(asset/themes/theme-pic1.jpg) #f7ce38 center 0 no-repeat;"></a></li>

		</ul>
	</div>

	<div class="hd"><ul></ul></div>
</div>


     <script src="login.js"></script>
     <div class="banner-shadow"></div>
     
     <div class="footer">
        <p>Copyright &copy; <script type="text/javascript">var myDate = new Date();document.write(myDate.getFullYear())</script></p>
</div>
</body>
</html>
