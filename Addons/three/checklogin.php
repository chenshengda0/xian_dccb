<?php
require('common.inc.php');

if($_POST["action"]=="save"){
	$user=checkstr(trim($_POST["user"]));
	$pwd=checkstr(trim($_POST["pwd"]));
	$yzm=checkstr(trim($_POST["yzm"]));
	
	if(empty($user)) msg_b("请输入账号");
	if(empty($pwd)) msg_b("请输入密码");
	if(empty($yzm)) msg_b("请输入验证码");
		

	if($_SESSION["code"]!=$yzm) msg_l("您输入的验证码不正确","login.php");

	//检测用户
	$row=$member->getuserinfo($user,1);

	if(empty($row)) msg_l("您输入的账户不正确","login.php");
	if($row['um005']!=getpwd($pwd)) msg_l("您输入的密码不正确","login.php");
	if($row['uv008']==0)  msg_l("抱歉，您的账号还未审核通过","login.php");

	
	if($row['uv001']!=1){
		//检测系统是否开启

		if($YCF['syopen']){
			echo '<div style="width:400px;margin:0px auto;font-size:20px;font-weight:bold;margin-top:200px">';
			echo $YCF['syopens']."</div>";
			exit;
		}

		if($YCF['sytime'][1]>$YCF['sytime'][0]){
			if(strtotime(date('H:i'))<strtotime($YCF['sytime'][0])||strtotime(date('H:i'))>strtotime($YCF['sytime'][1])){
				msg_l("系统开放访问时间".$YCF['sytime'][0]."到".$YCF['sytime'][1],"login.php");
			}
		}else{
			if(strtotime(date('H:i'))>strtotime($YCF['sytime'][1])&&strtotime(date('H:i'))<strtotime($YCF['sytime'][0])){
				msg_l("系统开放访问时间".$YCF['sytime'][0]."到".$YCF['sytime'][1],"login.php");
			}
		}				
	}

	if($row["uv010"]&&$row["uv001"]!=1) msg_l("您的帐号已经被冻结，无法登录","login.php");
	
	$loginip=getrealip();
	$garr=explode('.',$loginip);
	$garrs[0]=$garr[0].".*.*.*";
	$garrs[1]=$garr[0].".".$garr[1].".*.*";
	$garrs[2]=$garr[0].".".$garr[1].".".$garr[2].".*";

	$db->yiquery("select uk001 from ymuk",2);
	if(!empty($db->rs)){
		if(strpos("=".$db->rs['uk001'],$loginip)>0) msg_l("系统维护中","login.php");
		if(strpos("=".$db->rs['uk001'],$garrs[0])>0) msg_l("系统维护中","login.php");
		if(strpos("=".$db->rs['uk001'],$garrs[1])>0) msg_l("系统维护中","login.php");
		if(strpos("=".$db->rs['uk001'],$garrs[2])>0) msg_l("系统维护中","login.php");
	}
	

	//保存session
	$_SESSION["userid"]=$row['um001'];
	$_SESSION["savetime"]=time();
	$db->yiexec("update ymum set um026=".time().",um027='$loginip' where um001=".$row['um001']);
	$db->yiexec("insert into ymul(ul002,ul003,ul004,ul005) values('$user',".time().",0,'$loginip')");
	//go登录
	echo "<script>location.href='home.php';</script>";
}		

echo "<script>location.href='index.php';</script>";
?>