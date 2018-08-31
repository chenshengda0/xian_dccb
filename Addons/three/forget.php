

<?php 
    require('common.inc.php');
    $rr=$config->getregconfig();

    $arr['mbq']='&nbsp;<select name="rgmbq" class=""  style="height:30px;line-height:30px;margin-top:10px"><option value="0">请选择您的密保问题?</option>';
    foreach ($rr['rgmbq'] as $k => $v) {
        $arr['mbq'].='<option value="'.($k+1).'" '.($r['mbq']==($k+1)?"selected":"").'>'.$v.'</option>';
    }
    $arr['mbq'].='</select>';   
    
if($_POST["act"]=="yes"){
    $username=checkstr($_POST['username']);
    $t2=checkstr($_POST['t2']);
    $yzm=checkstr($_POST['yzm']);
    $rgmbq=$_POST["rgmbq"];


    if($_SESSION["code"]!=$yzm) msg_l("您输入的验证码不正确","forget.php");
    if(empty($username))  msg_l("请输入用户名","forget.php");
    if(empty($t2))  msg_l("请输入答案","forget.php");
    if($rgmbq<1)  msg_l("请选择密保","forget.php");
    
   $rs=$member->getuserinfo($username,1);
   if(empty($rs)){
        msg_l("信息输入不正确!","forget.php");
   }

   if($rs["um008"]!=$rgmbq||$rs["um009"]!=$t2){
     msg_l("密保信息不正确!","forget.php");
   }

    $m1=getpwd($YCF['syuserpwd'][0]);
    $m2=getpwd($YCF['syuserpwd'][1]);
    $m3=getpwd($YCF['syuserpwd'][2]);
    $db->yiexec("update ymum set um005='$m1',um006='$m2',um007='$m3' where um002='$username'");

    msg_l("您的密码已经恢复默认，登录密码是".$YCF['syuserpwd'][0]."，\\n\\n二级密码是".$YCF['syuserpwd'][1]."!请及时修改密码并牢记","index.php");
  
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <TITLE><?php echo $YCF['sytitle']?></TITLE>

    <script type="text/javascript" src="<?php echo YMTHEME;?>js/jquery-1.6.4.min.js"></script>
    <script type="text/javascript" src="<?php echo YMTHEME;?>js/main.js"></script>
    <script type="text/javascript" src="<?php echo YMTHEME;?>art/artDialog.source.js?skin=simple"></script> 
</head>
<body>

<style type="text/css">
.g-doc{background:url(/images2/bg-0.jpg);}
.g-hd,.g-ft{background:#FFF;}
.g-hd{height:50px;-webkit-box-shadow:0 3px 3px rgba(0,0,0,0.06);-moz-box-shadow:0 3px 3px rgba(0,0,0,0.06);box-shadow:0 3px 3px rgba(0,0,0,0.06);}
.g-bd{padding:30px 0;}
.g-in{width:990px;margin:0 auto;}
.g-ft{position:relative;-webkit-box-shadow:0 -3px 3px rgba(0,0,0,0.06);-moz-box-shadow:0 -3px 3px rgba(0,0,0,0.06);box-shadow:0 -3px 3px rgba(0,0,0,0.06);}
.g-doc{background:url(/images2/bg-0.jpg);}
.g-hd,.g-ft{background:#FFF;}
.g-hd{height:50px;-webkit-box-shadow:0 3px 3px rgba(0,0,0,0.06);-moz-box-shadow:0 3px 3px rgba(0,0,0,0.06);box-shadow:0 3px 3px rgba(0,0,0,0.06);}
.g-bd{padding:30px 0;}
.g-in{width:990px;margin:0 auto;}
.g-ft{position:relative;-webkit-box-shadow:0 -3px 3px rgba(0,0,0,0.06);-moz-box-shadow:0 -3px 3px rgba(0,0,0,0.06);box-shadow:0 -3px 3px rgba(0,0,0,0.06);}
.m-ipt-icon .iptIcon{ position:absolute; width:16px; height:16px; top:50%; left:0; margin-top:-8px;}
.m-ipt-icon .iptIcon-usr{ background-position:-320px 0;}
.m-ipt-icon .iptIcon-pwd{ background-position:-320px -30px;}
.m-ipt-icon .u-ipt input{ width:326px;margin-left:32px;}
.m-ipt-icon .u-ipt label{ width:344px;padding-left:50px;}

</style>
<script type="text/javascript">
// JavaScript Document
function chkyong(){
    var d = /^[^,'"]+$/;

    var user=document.form1.username.value;
    var yzm=document.form1.yzm.value;
    var t2=document.form1.t2.value;
    var rgmbq=document.form1.rgmbq.value;

    if(document.form1.username.value.length == 0){
        alert("\n请输入账号！");
        document.form1.username.focus();
        return false;
    }


    if (user = strim(user), "" == user || null == user) {
        alert("\n请输入账号！");
        document.form1.username.focus();
        return false;
    }

    if(!d.test(user))
    {
        alert("\n账号输入敏感字符！");
        document.form1.username.focus();
        return false;
    }    

    if(rgmbq==0){
        alert("\n请选择密保答案！");

        return false;
    }

    if(document.form1.t2.value.length == 0){
        alert("\n请输入答案！");
        document.form1.t2.focus();
        return false;
    }
    t2 = strim(t2)
    if ("" == t2 ) {
        alert("\n请输入答案！");
        document.form1.t2.focus();
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

}
function strim(a) {
    return a.replace(/(^\s*)|(\s*$)/g, "");
}    

</script>
<div class="g-in"><div class="m-box">
        <h1 class="boxHd">请输入您要找回密码的帐号</h1>
        <div class="boxBd">
            <form method="post" action="?" onsubmit="return chkyong()"  name="form1"  id="form1">

                <input type="hidden" id="act" name="act" value="yes">


                <div class="m-ipt m-ipt-icon">
                    <div class="u-ipt">
                        <div class="iptctn">
                            &nbsp;用户名:<input type="text"  maxlength="15" name="username" id="username" autocapitalize="off" myholder="请输入用户名"  placeholder="请输入用户名" style="height:30px;line-height:30px">
                         
                        </div>
                    </div>
                    <div class="u-ipt">
                        <div class="iptctn">
                            密保问题:&nbsp;<?php echo  $arr['mbq']?>
                         
                        </div>
                    </div>      
                    <div class="u-ipt" style="margin-top:10px">
                        <div class="iptctn">
                            &nbsp;答&nbsp;案:<input type="text" name="t2"  maxlength="30" id="t2" autocapitalize="off" myholder="请输入答案"  placeholder="请输入答案" style="height:30px;line-height:30px">
                         
                        </div>
                    </div>                                  
                     <div class="u-ipt" style="margin-top:10px">
                        <div class="iptctn">
                            &nbsp;验证码:<input type="text" name="yzm" maxlength="5" id="yzm" autocapitalize="off" myholder="请输入验证码"  placeholder="请输入验证码" style="height:30px;line-height:30px;width:100px"><img id="checkCode" width="128" src='yimaoinclude/code.php' onclick='this.src="yimaoinclude/code.php?"+Math.random()'  alt="验证码" title="验证码"  style="height:30px;line-height:30px;vertical-align:middle"
                         
                        </div>
                    </div>
                </div>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <button type="submit" class="u-btn2" style="margin-top:20px;height:30px;line-height:30px;width:100px">确认</button>
            </form>
        </div>
        <div class="ieAlpha"></div>
    </div></div>

</body>
</html> 
