<?php
require('common.inc.php');
if(empty($_SERVER['HTTP_REFERER'])) exit;

$act=trim($_GET['act']);
switch ($act) {
	case 'getuserid':
		getuserid();
	break;
	case 'getusername':
		getusername();
	break;	
	case 'chkuser':
		chkuser();
	break;
	case 'chkbd':
		chkbd();
	break;
	case 'chkan':
		chkan();
	break;	
	case 'chkanrea':
		chkanrea();
	break;	
	case 'chktu':
		chktu();
	break;	
	case 'chkmobile':
		chkmobile();
	break;

}

function chkmobile(){
	global $member;
	$value=checkstr($_GET['mobile']);
	$rs=$member->mobile_exists($value);
	if(!empty($rs)){
		echo json_encode(array('tips'=>"手机号码已经被使用"));
	}else{
		echo json_encode(array('tips'=>'ok'));
	}
	exit;
}


function getuserid(){
	$arr['name']=getrand();
	echo json_encode($arr);	
	exit;
}

function getusername(){
	global $member;
	$value=checkstr($_GET['username']);
	$rs=$member->getum($value,'um003');
	if(!empty($rs)){
		echo json_encode(array('names'=>$rs['um003']));
	}else{
		echo json_encode(array('names'=>''));
	}
	exit;
}

function chkuser(){
	global $member;
	$value=checkstr($_GET['userid']);
	$rs=$member->user_exists($value);
	if(empty($rs)){
		echo json_encode(array('tips'=>'ok'));
	}else{
		echo json_encode(array('tips'=>'账号已经被注册'));	
	}
	exit;
}

function chkbd(){
	global $member;
	$value=checkstr($_GET['userid']);
	$rs=$member->bd_exists($value);
	if(empty($rs)){
		echo json_encode(array('tips'=>'报单中心不存在'));
	}else{
		echo json_encode(array('tips'=>'ok'));
	}
	exit;
}

function chkan(){
	global $member;
	$value=checkstr($_GET['userid']);
	$rs=$member->an_exists($value);
	if(empty($rs)){
		echo json_encode(array('tips'=>'安置人不存在'));
	}else{
		echo json_encode(array('tips'=>'ok'));
	}
	exit;
}

function chkanrea(){
	global $member;
	$value=checkstr($_GET['userid']);
	$anrea=checkstr($_GET['anrea']);
	$rs=$member->an_exists($value);
	if(empty($rs)){
		echo json_encode(array('tips'=>'安置人不存在'));
		exit;
	}

	$rs=$member->ans_exists($rs['uv001'],$anrea);
	if(empty($rs)){
		echo json_encode(array('tips'=>'ok'));
	}else{
		echo json_encode(array('tips'=>'这个位置已经被占用'));
	}
	exit;
}

function chktu(){
	global $member;
	$value=checkstr($_GET['userid']);
	$rs=$member->tu_exists($value);
	if(empty($rs)){
		echo json_encode(array('tips'=>'推荐人不存在'));
	}else{
		echo json_encode(array('tips'=>'ok'));
	}
	exit;
}
?>