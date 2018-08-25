<?php
require("yimaoini.php"); //中
$backurl=geturls();

$act=$_POST["act"];
switch ($act) {
	case 'modinfo':
		yi_modinfo();
	break;
	case 'modpwd':
		yi_modpwd();
	break;	
	case 'modmb':
		yi_modmb();
	break;	
	case 'bankadd':
		yi_bankadd();
	break;	
	case 'tiqu':
		yi_tiqu();
	break;	
	case 'chong':
		yi_chong();
	break;	
	case 'zhuan':
		yi_zhuan();
	break;	
	case 'huan':
		yi_huan();
	break;	
	case 'sendemail':
		yi_sendemail();
	break;	
	case 'huifu':
		yi_huifu();
	break;		
	case 'uplevel':
		yi_uplevel();
	break;	
	case 'open':
		yi_open();
	break;	
	case 'houlogin':
		yi_houlogin();
	break;	
	case 'shenbd':
		yi_shenbd();
	break;	
	case 'yzmm':
		yi_yzmm();		
	break;	
	case 'yzmbm':
		yi_yzmbm();		
	break;	
	case 'yzmb':
		yi_yzmb();		
	break;		
	case 'cunqian':
		yi_cunqian();		
	break;
	case 'quqian':
		yi_quqian();		
	break;	
	case 'huan1':
		yi_huan1();
	break;										
}


function yi_huan(){
	global $db,$YCF,$_username,$r,$member,$backurl,$_userid;

	$jine=trim($_POST['jine']);
	$ztype=trim($_POST['ztype']);

	if(!is_numeric($jine)) msg_b("o_O ~ 您输入的转换金额不是一个数字");
	if($jine<$YCF['huanmin']) msg_b("o_O ~ 您输入的转换金额不能少于".$YCF['huanmin']);

	if($YCF['huanopen']){
		msg_b("o_O ~ 转换币功能暂时关闭！");
	}

	if($r['um023']>0) msg_b("o_O ~ 您已经被禁止转换货币\\n\\n请联系管理员查看原因");
	if($YCF['huanbs']==0){
		if($jine < $YCF['huanmin']) msg_b("o_O ~ 单次转换最小金额为 ".$YCF['huanmin']."");
		if(($jine % $YCF['huanbsz'])!=0) msg_b("o_O ~ 转换必须是".$YCF['huanbsz']."的倍数");
	}else{
		if($jine < $YCF['huanmin']) msg_b("o_O ~ 单次转换最小金额为 ".$YCF['huanmin']."");
	}

	if($ztype==0&&$r['uv012']<$jine){
		msg_b("o_O ~ 您的现金钱包余额不足".$jine);
	}else{
		if($r['uv065']<=0) msg_b("o_O ~ 您的小金库账户不足!");

		$ban=$r['uv065']*$YCF["jjtx"][1]/100;
		if($ban!=$jine) msg_b("o_O ~ 您只能转换小金库金额".$ban);
		$jine=$ban;
	}

	$db->yiquery("select count(0) as a from ymuj where uj002='$_username' and FROM_UNIXTIME(uj003,'%Y%m%d')=".date('Ymd',time()),2);
	if($db->rs['a']>=$YCF['huanci']){
		msg_b("o_O ~ 您今日已经转币".$db->rs['a'].'次,已经达到最大值');
	}
	$arr['uj002']=$_username;
	$arr['uj003']=time();
	$arr['uj004']=$jine;
	$arr['uj005']=$ztype;

	$sql=getinsertsql($arr,"ymuj",'uj004,uj005');
	$db->yiexec($sql);



		yimao_writeaccount(array($_userid,"1",4,time(),8,$jine,""));
		yimao_writeaccount(array($_userid,"0",1,time(),8,$jine,""));


	
	

	yimao_writelog("转换货币".$jine,0);
	msg_l("o_O ~ 转换货币成功 ",$backurl."/home.php?yim=xiaojinlist&templist=1");		
		
}

function yi_huan1(){
	global $db,$YCF,$_username,$_userid,$r,$member,$backurl;

	$jine=trim($_POST['jine']);
	$ztype=trim($_POST['ztype']);

	if(!is_numeric($jine)) msg_b("o_O ~ 您输入的转换金额不是一个数字");
	if($jine<$YCF['huanmin']) msg_b("o_O ~ 您输入的转换金额不能少于"+$YCF['huanmin']);

	if($YCF['huanopen']){
		msg_b("o_O ~ 转换币功能暂时关闭！");
	}

	if($r['um023']>0) msg_b("o_O ~ 您已经被禁止转换货币\\n\\n请联系管理员查看原因");
	if($YCF['huanbs']==0){
		if($jine < $YCF['huanmin']) msg_b("o_O ~ 单次转换最小金额为 ".$YCF['huanmin']."");
		if(($jine % $YCF['huanbsz'])!=0) msg_b("o_O ~ 转换必须是".$YCF['huanbsz']."的倍数");
	}else{
		if($jine < $YCF['huanmin']) msg_b("o_O ~ 单次转换最小金额为 ".$YCF['huanmin']."");
	}

	if($ztype==0&&$r['uv012']<$jine){
		msg_b("o_O ~ 您的现金币余额不足".$jine);
	}

	$db->yiquery("select count(0) as a from ymuj where uj002='$_username' and uj005=0 and FROM_UNIXTIME(uj003,'%Y%m%d')=".date('Ymd',time()),2);
	if($db->rs['a']>=$YCF['huanci']){
		msg_b("o_O ~ 您今日已经转币".$db->rs['a'].'次,已经达到最大值');
	}
	$arr['uj002']=$_username;
	$arr['uj003']=time();
	$arr['uj004']=$jine;
	$arr['uj005']=$ztype;

	$sql=getinsertsql($arr,"ymuj",'uj004,uj005');
	$db->yiexec($sql);

	if($ztype==0){
		yimao_writeaccount(array($_userid,"1",0,time(),8,$jine,""));
		yimao_writeaccount(array($_userid,"0",3,time(),8,$jine,""));
	}
	


	yimao_writelog("转换货币".$jine,0);
	msg_l("o_O ~ 转换货币成功 ",$backurl."/home.php?yim=huanlist");	
}


function yi_quqian(){
	global $db,$YCF,$r,$adminra,$_userid,$_username,$backurl;
	$jine=trim($_POST['jine']);
	$pwd=md5(md5($_POST['pwd'].YMJIAMI));

	if(!is_numeric($jine)) msg_b("o_O ~ 您输入的获取帮助金额不是一个数字");
	if(empty($pwd)) msg_b("o_O ~ 请输入密码");
	

	if($jine < $YCF['qkmin']) msg_b("o_O ~ 单次获取帮助最小金额为 ".$YCF['qkmin']."");
	if(($jine % $YCF['qkbs'])!=0) msg_b("o_O ~ 获取帮助必须是".$YCF['qkbs']."的倍数");
	if($jine > $YCF['qkmax']) msg_b("o_O ~ 单次获取帮助最大金额为 ".$YCF['qkmax']."");	

	if($r['um006']!=$pwd) msg_b("o_O ~ 您输入的密码不正确");

	if(empty($r["um018"])||empty($r["um019"])||empty($r["um020"])){
		 msg_b("o_O ~ 请先填写好您的地址信息");
	}

	$kou=$jine*$YCF["jjax"]/100;
	$quk=$jine+$kou;

	if($jine>$r["uv012"]) msg_b("o_O ~ 您的现金钱包剩余".$r["uv012"]."，，不足提现！");

	$db->yiquery("select count(gq001) a,sum(gq005) b from ymugq where gq011=0 and gq002=$_userid and FROM_UNIXTIME(gq004,'%Y')=".date("Y")." and FROM_UNIXTIME(gq004,'%m')=".date("m"),2);

	$ci=$YCF["jjqkci"][$r["uv006"]];
	if($_userid!=1){
	if($db->rs["a"]>=$ci){
		 msg_b("o_O ~ 您本月已经取款".$ci.'次，不能再取款了!');
	}

	$xian=$YCF["jjqkxian"][$r["uv006"]];
	if(($db->rs["b"]+$jine)>$xian){
		 $yu=$xian-$db->rs["b"];
		 if($yu<=0) $yu=0;
		 msg_b("o_O ~ 您本月已经取款".$db->rs["b"].'，您还能取款的金额 '.$yu.'!');
	}
	}


	try{
		$db->begintransaction();

		$arr["gq002"]=$_userid;
		$arr["gq003"]=$_username;	
		$arr["gq004"]=time();
		$arr["gq005"]=$jine;
		$arr["gq013"]=$_username.date("YmdHis");
		$arr["gq014"]=$r["um018"];
		$arr["gq015"]=$r["um019"];
		$arr["gq016"]=$r["um020"];
		$arr["gq012"]=0;
		$arr["gq024"]=$kou;


		$sql=getinsertsql($arr,'ymugq',"gq005");
		$db->yiquery($sql,5);
		$arr["gq001"]=$db->fval;

		//fun_bd1($arr);


		$db->yiexec("update ymuv set uv047=uv047+$jine where uv002='$_username'");
	
		yimao_writeaccount(array($_userid,"1",0,time(),10,$quk,'获取帮助编号'.$arr["gq013"]));



		$r["uv047"]=$r["uv047"]+$jine;
		fun_touxie($r);

		$db->committransaction();
	}catch(PDOException $e){
		$db->rollbacktransaction();
		a_bck("error");
	}	

	yimao_writelog("添加获取帮助",0);
	msg_l("o_O ~ 获取帮助成功!",$backurl."/home.php?yim=qukuan");
}


function yi_cunqian(){
	global $db,$YCF,$r,$adminra,$_userid,$_username,$backurl;
	$jine=trim($_POST['jine']);
	$pwd=md5(md5($_POST['pwd'].YMJIAMI));

	if(!is_numeric($jine)) msg_b("o_O ~ 您输入的提供帮助金额不是一个数字");
	if(empty($pwd)) msg_b("o_O ~ 请输入密码");
	$ckmax=$YCF['cqmax']+$r["uv068"];

	if($jine < $YCF['cqmin']) msg_b("o_O ~ 单次提供帮助最小金额为 ".$YCF['cqmin']."");
	if(($jine % $YCF['cqbs'])!=0) msg_b("o_O ~ 提供帮助必须是".$YCF['cqbs']."的倍数");
	if($jine > $ckmax) msg_b("o_O ~ 单次提供帮助最大金额为 ".$ckmax."");	

	if($r['um006']!=$pwd) msg_b("o_O ~ 您输入的密码不正确");

	if(empty($r["um018"])||empty($r["um019"])||empty($r["um020"])){
		 msg_b("o_O ~ 请先填写好您的地址信息");
	}
	

	try{
		$db->begintransaction();
		$rt=fun_insertTime();

		$arr["gp002"]=$_userid;
		$arr["gp003"]=$_username;	
		$arr["gp004"]=time();
		$arr["gp005"]=$jine;
		$arr["gp013"]=$_username.date("YmdHis");
		$arr["gp014"]=$r["um018"];
		$arr["gp015"]=$r["um019"];
		$arr["gp016"]=$r["um020"];
		$arr["gp012"]=0;
		$arr["gp006"]=$r["uv006"];


		$sql=getinsertsql($arr,'ymugp',"gp005");
		$db->yiquery($sql,5);
		$arr["gp001"]=$db->fval;

		$db->yiexec("update ymuv set uv046=".time()." where uv002='$_username'");

		yimao_writeaccount(array($_userid,"0",1,time(),23,$jine,'提供帮助编号'.$arr["gp013"]));

		if(!empty($r["uv018"]))
			fun_tjj(array($r["uv020"],$jine,$r["uv002"],$rt["ut001"],$arr["gp001"]));

		// if(!empty($r["uv020"]))
		// 	fun_jdj(array($r["uv020"],$jine,$r["uv002"],$rt["ut001"],$arr["gp001"]));

		if(!empty($r["uv020"]))
			fun_jlj(array($r["uv020"],$jine,$r["uv002"],$rt["ut001"],$arr["gp001"],$r["uv022"]));		

		fun_rfh2($arr["gp001"],$rt);

		fun_ks();
		fun_totalPrice();	

		$db->committransaction();
	}catch(PDOException $e){
		$db->rollbacktransaction();
		a_bck("error");
	}	

	yimao_writelog("添加提供帮助",0);
	msg_l("o_O ~ 提供帮助成功!",$backurl."/home.php?yim=cunkuan");
}

function yi_yzmm(){
	global $db,$YCF,$r,$adminra,$_userid,$_username,$backurl;
	if($_POST['yzform']=='ok'){
	    $pwd=$_POST['pwd'];
	    $murl=$_POST['m'];
	    
	    if($pwd==""){
	        msg_b('o_O ~ 请输入密码');
	    }

	    $pwd=getpwd($pwd);
	    if($r['um006']==$pwd){
	        $_SESSION['yzmm']=$murl;
	        locationurl(YMINDEX.'?yim='.$murl);
	        exit;
	    }else{
	         msg_b('o_O ~ 密码验证失败');
	    }

	}	
}

function yi_yzmbm(){
	global $db,$YCF,$r,$adminra,$_userid,$_username,$backurl;
	if($_POST['yzform']=='ok'){
	    $pwd=$_POST['pwd'];
	    $mbq=$_POST['mbq'];
	    $mba=checkstr($_POST['mba']);
	    $murl=$_POST['m'];

	    if($pwd==""){
	        msg_b('o_O ~ 请输入密码');
	    }
	    if($mbq==-1){
	        msg_b('o_O ~ 请选择密保问题');
	    }
	    if($mba==""){
	        msg_b('o_O ~ 请输入密保答案');
	    }
	    $pwd=getpwd($pwd);

	    if($mbq==$r['um008']&&$mba==$r['um009']&&$r['um006']==$pwd){
	        $_SESSION['yzmbm']=$murl;
	        locationurl(YMINDEX.'?yim='.$murl);
	        exit;
	    }else{
	         msg_b('o_O ~ 密保验证失败');
	    }

	}
}

function yi_yzmb(){
	global $db,$YCF,$r,$adminra,$_userid,$_username,$backurl;
	if($_POST['yzform']=='ok'){
	    $mbq=$_POST['mbq'];
	    $mba=checkstr($_POST['mba']);
	    $murl=$_POST['m'];

	    if($mbq==-1){
	        msg_b('o_O ~ 请选择密保问题');
	    }
	    if($mba==""){
	        msg_b('o_O ~ 请输入密保答案');
	    }

	    if($mbq==$r['um008']&&$mba==$r['um009']){
	        $_SESSION['yzmb']=$murl;
	        locationurl(YMINDEX.'?yim='.$murl);
	        exit;
	    }else{
	         msg_b('o_O ~ 密保验证失败');
	    }

	}	
}


function yi_houlogin(){
	global $db,$YCF,$r,$adminra,$_userid,$_username,$backurl;
	$pwd=$_POST['pwd'];

	if(empty($pwd)) msg_b("o_O ~ 请先输入密码");

	if(empty($adminra)) msg_b("o_O ~ 您不是管理员");
	if($adminra["ua005"]) msg_b("o_O ~ 您已经被禁止登录");
	if($adminra["u0005"]) msg_b("o_O ~ 您已经被禁止登录");

	if($adminra['ua009']!=getpwd($pwd))  msg_b("o_O ~ 您输入的密码不正确");
	$_SESSION["houid"]=$r['uv001'];
	$_SESSION["houname"]=$r['uv002'];

	$loginip=$_SERVER['REMOTE_ADDR'];
	$db->yiexec("insert into ymul(ul002,ul003,ul004,ul005) values('".$r['uv002']."',".time().",1,'$loginip')");
	$db->yiexec("update ymua set ua006=".time().",ua007='".$loginip."' where ua002='$_username'");
	locationurl(YMADMINDEX);
}

function yi_modinfo(){
	global $db,$YCF,$_userid,$r,$backurl;
	if($r['um024']) msg_b("o_O ~ 您已经被禁止修改资料");
	$arr=array();
	foreach ($_POST['ptd'] as $k => $v) {
		yimao_editinfo($v,checkstr($_POST["$v"]),$arr);
	}
	$sql=getupdatesql($arr,'ymum',' um001='.$_userid,'um008,um016');
	$db->yiexec($sql);
	yimao_writelog("修改资料",0);
	msg_l("o_O ~ 修改资料成功!",$backurl."/home.php?yim=myinfo");
}

function yi_modpwd(){
	global $db,$YCF,$_userid,$r,$backurl;
	$mtype=$_POST['mtype'];
	$ypwd=$_POST['ypwd'];
	$npwd=$_POST['npwd'];

	if(is_null($mtype)){
		msg_b('o_O ~ 请选择密码类型');
	}
	if($ypwd==''||(strlen($ypwd)<8||strlen($ypwd)>20)){
		msg_b('o_O ~ 原密码不正确');
	}
	if($npwd==''||(strlen($npwd)<8||strlen($npwd)>20)){
		msg_b('o_O ~ 新密码不正确');
	}
	if($ypwd==$npwd){
		msg_b('o_O ~ 新密码不能和原密码一样');
	}
	$ypwd=md5(md5($ypwd.YMJIAMI));
	$npwd=md5(md5($npwd.YMJIAMI));
	if($mtype=="rgpwd1"){
		if($ypwd!=$r['um005']) msg_b('o_O ~ 您输入的原密码不正确');
		$db->yiexec("update ymum set um005='$npwd' where um001=$_userid");
	}elseif($mtype=="rgpwd2"){
		if($ypwd!=$r['um006']) msg_b('o_O ~ 您输入的原密码不正确');
		$db->yiexec("update ymum set um006='$npwd' where um001=$_userid");
	}elseif($mtype=="rgpwd3"){
		if($ypwd!=$r['um007']) msg_b('o_O ~ 您输入的原密码不正确');
		$db->yiexec("update ymum set um007='$npwd' where um001=$_userid");
	}
	yimao_writelog("修改密码",0);
	msg_l("o_O ~ 修改密码成功 ",$backurl."/home.php?yim=modpwd");	
}

function yi_modmb(){
	global $db,$YCF,$_userid,$r,$backurl;
	$mbq=$_POST['mbq'];
	$mba=checkstr($_POST['mba']);

	if(getcharnums($mba)>25)  msg_b("o_O ~ 密保答案输入过多内容，请重新输入25个字符以下.");

	if($mbq==-1){
		msg_b('o_O ~ 请选择密保问题');
	}
	if($mba==""){
		msg_b('o_O ~ 请输入密保答案');
	}

	$db->yiexec("update ymum set um008=$mbq,um009='$mba' where um001=$_userid");
	yimao_writelog("修改密保",0);
	msg_l("o_O ~ 修改密保成功 ",$backurl."/home.php?yim=modmb");	
}



function yi_tiqu(){

}


function yi_chong(){
	global $db,$YCF,$_username,$r,$backurl;
	$jine=trim($_POST['jine']);
	$tbz=checkstr($_POST['tbz']);

	if(getcharnums($tbz)>150)  msg_b("o_O ~ 充值备注输入过多内容，请重新输入150个字符以下.");

	if(!is_numeric($jine)) msg_b("o_O ~ 您输入的充值金额不是一个数字");
	if($jine<$YCF['chongmin']) msg_b("o_O ~ 您输入的充值金额不能少于"+$YCF['chongmin']);



	if($YCF['chongopen']){
		msg_b("o_O ~ 充值功能暂时关闭！");
	}

	$arr['ug002']=$_username;
	$arr['ug003']=time();
	$arr['ug005']=$jine;
	$arr['ug009']=$tbz;

	$db->yiquery("select ug001 from ymug where ug002='$_username' and ug008=0 limit 1",2);
	if(!empty($db->rs)){
		msg_b("o_O ~ 您有未审核通过的的充值，无法再次申请\\n\\n请联系管理员查看原因");
	}

	$sql=getinsertsql($arr,"ymug",'ug005');
	$db->yiexec($sql);

	yimao_writelog("申请充值".$jine,0);
	msg_l("o_O ~ 申请充值成功");	
}

function yi_zhuan(){
	global $db,$YCF,$_username,$r,$member,$backurl,$_userid;
	$username=checkstr($_POST['username']);
	$jine=trim($_POST['jine']);
	$tbz=checkstr($_POST['tbz']);
	$ztype=trim($_POST['ztype']);


	$tbz=checkstr($_POST['tbz']);

	if(getcharnums($tbz)>150)  msg_b("o_O ~ 转账备注输入过多内容，请重新输入150个字符以下.");

	if(!chkusername($username)) msg_b("o_O ~ 您输入的对方帐号不正确");
	if(!is_numeric($jine)) msg_b("o_O ~ 您输入的转账金额不是一个数字");

	if($YCF['zhuanbs']==0){
		if($jine < $YCF['zhuanmin']) msg_b("o_O ~ 单次转账最小金额为 ".$YCF['zhuanmin']."");
		if(($jine % $YCF['zhuanbsz'])!=0) msg_b("o_O ~ 转账必须是".$YCF['zhuanbsz']."的倍数");
	}else{
		if($jine < $YCF['zhuanmin']) msg_b("o_O ~ 单次转账最小金额为 ".$YCF['zhuanmin']."");
	}


	if($YCF['zhuanopen']){
		msg_b("o_O ~ 转账功能暂时关闭！");
	}
	
	if($r['um022']>0) msg_b("o_O ~ 您已经被禁止转账\\n\\n请联系管理员查看原因");

	if($username==$_username){
		msg_b("o_O ~ 您重新输入一个新的对方帐号");
	}	

	$rs=$member->user_exists($username);
	if(empty($rs)){
		msg_b("o_O ~ 您输入的对方帐号不存在");
	}

	if($YCF['zhuanopen']){
	
	}else{
		if(strpos($rs['uv020'],",".$_userid.",")===false&&strpos($r['uv020'],",".$rs["uv001"].",")===false){
			msg_b("o_O ~ ".$username."和您不在同一个部门，转账失败!");
		}
	}

	$ks=$jine*$YCF['zhuanks']/100; 
	$ks=-$ks;
	$qian=$jine+$ks; 
	
	if($ztype==0&&$r['uv015']<$jine){
		msg_b("o_O ~ 您的激活币余额不足".$jine);
	}


	$arr['uz002']=$_username;
	$arr['uz003']=$username;
	$arr['uz004']=time();
	$arr['uz005']=$jine;
	$arr['uz006']=$ks;
	$arr['uz007']=$tbz;
	$arr['uz008']=$ztype;


	$sql=getinsertsql($arr,"ymuz",'uz005,uz006,uz008');
	$db->yiexec($sql);


		yimao_writeaccount(array($_userid,"1",3,time(),3,$jine,'转账给 '.$username.',扣税'.$ks));
		yimao_writeaccount(array($rs["uv001"],"0",3,time(),3,$qian,'来自于'.$_username.'的转账,扣税'.$ks));


	yimao_writelog("转账 ".$jine." 给".$username,0);
	msg_l("o_O ~ 转账成功 ",$backurl."/home.php?yim=zhuanlist");		
}


function yi_sendemail(){
	global $db,$YCF,$_username,$_userid,$r,$member,$backurl;

	$username=checkstr($_POST["username"]);
	$lx=$_POST["lx"];
	$lytitle=checkstr($_POST["lytitle"]);
	$emtype=checkstr($_POST["emtype"]);
	$lycontent=checkstr($_POST["lycontent"]);


	if($lx==0){
		$mubiao=$member->getuserid(1);
	}else{
		if(empty($username)) msg_b("请输入收件人");
		$rs=$member->getum($username,'um001');
		if(empty($rs)){
			msg_b("o_O ~ 收件人不存在");
		}
		$mubiao=$username;
	}

	if(empty($lytitle)) msg_b("请输入留言标题");
	if(empty($lycontent)) msg_b("请输入留言内容");

	if(getcharnums($lycontent)>450)  msg_b("o_O ~ 留言内容输入过多，请重新输入450个字符以下.");
	
	// $db->yiquery("select ue004 from ymue where ue002='$_username' order by ue001 desc limit 1",2);
	// if(!empty($db->rs) && $_userid!=1){
	// 	if((time()-$db->rs['ue004'])<60) msg_b("o_O ~ 请等待1分钟后再次发送邮件");
	// }


	$sql="insert into ymue(ue002,ue003,ue004,ue005,ue006,ue010) values('$_username','$mubiao',".time().",'".$lytitle."','".$lycontent."',$emtype)";
	$db->yiexec($sql);

	yimao_writelog("发送留言:".$lytitle,0);
	msg_l("o_O ~ 留言成功 ",$backurl."/home.php?yim=liuyan");
}

function yi_huifu(){
	global $db,$YCF,$_username,$_userid,$r,$member,$backurl;

	$arr['ue006']=checkstr($_POST["emcontent"]);
	$edid=$_POST['edid'];
	if(empty($arr['ue006'])) msg_b("请输入回复内容");

	if(getcharnums($arr['ue006'])>450)  msg_b("o_O ~ 回复内容输入过多，请重新输入450个字符以下.");

	$db->yiquery("select * from ymue where (ue002='$_username' or ue003='$_username') and (ue008<=2) and ue011=0 and ue001=$edid",2);
	if(empty($db->rs)){
		msg_b("找不到此邮件");
	}else{
		$ue005=$db->rs["ue005"];
	}


	$arr['ue010']=0;
	$arr['ue004']=time();
	$arr['ue005']="回复";
	$arr['ue006']=$arr['ue006'];
	$arr['ue002']=$_username;
	if($_username==$db->rs['ue002']){
		$arr['ue003']=$db->rs['ue003'];
	}else{
		$arr['ue003']=$db->rs['ue002'];
	}
		
	$arr['ue011']=$edid;

	$sql=getinsertsql($arr,'ymue','ue010,ue004,ue011');
	$db->yiexec($sql);

	yimao_writelog("回复邮件:".$ue005,0);
	msg_l("o_O ~ 回复邮件成功 ",$_POST["xxurl"]);
}


function yi_uplevel(){

}

function yi_open(){
	global $db,$YCF,$_username,$_userid,$r,$member;
	$kid=$_POST['kid'];

	$karr=fun_openuser($kid,$_userid,0);

	foreach ($karr as $k => $v) {
		yimao_writelog("开通会员".$v,0);
	}
	
	msg_l("o_O ~ 开通会员成功 ",$backurl."/home.php?yim=openlist");	
}

function yi_shenbd(){
	global $db,$YCF,$_username,$_userid,$r,$member,$backurl;

	if($r['uv038']==1) msg_b('您已经申请了报单中心，请等待审核');
	if($r['uv038']==2) msg_b('您已经是报单中心');

	$db->yiexec("update ymuv set uv038=1,uv039=".time()." where uv001=$_userid");

	yimao_writelog("申请服务中心",0);
	msg_l("o_O ~ 申请成功 ",$backurl."/home.php?yim=bdshen");		
}
?>