<?php
require("common.inc.php"); //中
$backurl=geturls();
if(trim($_POST["act"])!="add") locationurl("index.php");

$areg=$config->getregmust();

$arr['uv002']=checkstr($_POST['userid']);

if($YCF['regbd']){
		$arr['uv017']=checkstr($_POST['bdname']);
}

		$arr['uv018']=checkstr($_POST['tuname']);	

if($YCF['regan']){	
		$arr['uv019']=checkstr($_POST['anname']);
		$arr['uv024']=checkstr($_POST['anrea']);
}

		$arr['uv006']=0;
		$arr['uv007']=$arr['uv006'];
		$arr['uv045']=getlevel($arr['uv006'],1); //新增




		$s=chkreginfo($arr);
		if(!empty($s)) msg_b($s);


		$arr['uv003']=$YMTIME;
		$arr['uv005']=$YMTIME;
		$arr['uv036']='0|0';



		

		$arm['um002']=$arr['uv002'];
		$arm['um003']=trim($_POST['username']);
		$arm['um004']=$arr['uv006'];
		$arm['um005']=md5(md5($_POST['rgpwd1'].YMJIAMI));
		$arm['um006']=md5(md5($_POST['rgpwd2'].YMJIAMI));
		$arm['um007']=md5(md5($_POST['rgpwd3'].YMJIAMI));
		$arm['um008']=is_null($_POST['mbq'])?0:$_POST['mbq'];
		$arm['um009']=checkstr($_POST['rgmba']);
		$arm['um010']=checkstr($_POST['rgmobile']);
		$arm['um011']=checkstr($_POST['rgemail']);
		$arm['um012']=checkstr($_POST['rgcard']);
		$arm['um013']=checkstr($_POST['rgaddress']);
		$arm['um014']=checkstr($_POST['rgweixin']);
		$arm['um015']=checkstr($_POST['rgqq']);
		$arm['um016']=checkstr($_POST['sex']);
		$arm['um017']=checkstr($_POST['rgfax']);
		$arm['um018']=checkstr($_POST['province']);
		$arm['um019']=checkstr($_POST['city']);
		$arm['um020']=checkstr($_POST['area']);

		if(isset($areg['rgmbq'])&&$areg['rgmbq']==0){
			if($arm['um008']==-1) msg_b("请选择密保问题");
		}
		if(isset($areg['rgprovince'])&&$areg['rgprovince']==0){
			if(empty($arm['um018'])) msg_b("请选择省份");
			if(empty($arm['um019'])) msg_b("请选择城市");
			if(empty($arm['um020'])) msg_b("请选择地区");
		}
		if(isset($areg['rgmobile'])&&$areg['rgmobile']==0){
			if(empty($arm['um010'])) msg_b("请输入手机号码");
			if($member->mobile_exists($arm['um010']))  msg_b("手机号码已被使用");
		}
		if(isset($areg['rgcard'])&&$areg['rgcard']==0){
			if(empty($arm['um012'])) msg_b("请输入身份证号");
			if($member->card_exists($arm['um012']))  msg_b("身份证号已被使用");
		}
		if(isset($areg['rgemail'])&&$areg['rgemail']==0){
			if(empty($arm['um011'])) msg_b("请输入电子邮箱");
			if($member->email_exists($arm['um011']))  msg_b("电子邮箱已被使用");
		}

	try{
		$db->begintransaction();
		$rt=fun_insertTime();

		$arr['uv043']=$rt["ut001"];
		$sql=getinsertsql($arr,'ymuv','uv006,uv007');
		$db->yiquery($sql,5);

		$arm['um001']=$db->fval;

		$sql=getinsertsql($arm,'ymum','um004,um016,um008');
		$db->yiquery($sql,5);


		yimao_writelog("注册会员".$arr['uv002'].',id为'.$arm['um001'],0);



		$db->committransaction();
		
		if($YCF['syregbank']==0){
			$banktxt="\\n\\n以下是公司汇款银行信息：\\n\\n银行：".$YCF['ritbankname']."\\n\\n卡号：".$YCF['ritbankcard']."\\n\\n姓名：".$YCF['ritbankuser']."\\n\\n";
		}
	
		msg_l("注册 ".$arm['um002']." 成功!".$banktxt,'login.php');		

	}catch(PDOException $e){
		$db->rollbacktransaction();
		msg_b("注册失败，请重新注册");
	}	
?>