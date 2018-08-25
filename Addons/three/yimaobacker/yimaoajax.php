<?php
require("yimaoini.php");
if(empty($_SERVER['HTTP_REFERER'])) exit;

$act=trim($_GET['act']);
switch ($act) {
	case 'getusername':
		getusername();
	break;
	case 'quickset':
		getquickset();
	break;	
}

function getusername(){
	$arr['name']=getrand();
	echo json_encode($arr);	
	exit;
}

function getquickset(){
	global $db,$member,$YCF;
	$optkey=$_POST['optkey'];
	$_username=$_POST['username'];
	switch ($optkey) {
		case 'lock':
			$rs=$member->getuv($_username);
			if($rs['uv010']){
				$db->yiexec("update ymuv set uv010=0,uv046=".time()." where uv002='$_username'");
				echo json_encode(array('name'=>'+锁定'));
				exit;
			}else{
				$db->yiexec("update ymuv set uv010=1,uv049='后台操作冻结' where uv001!=1 and uv002='$_username'");
				echo json_encode(array('name'=>'- 锁定'));
				exit;
			}
		break;
		case 'jl':
			$rs=$member->getuv($_username);
			if($rs['uv032']){
				$db->yiexec("update ymuv set uv032=0 where uv002='$_username'");
				echo json_encode(array('name'=>'+经理奖'));
				exit;
			}else{
				$db->yiexec("update ymuv set uv032=1 where uv002='$_username'");
				echo json_encode(array('name'=>'- 经理奖'));
				exit;
			}
		break;		
		case 'bd':
			$rs=$member->getuv($_username);
			if($rs['uv038']==2){
				$db->yiexec("update ymuv set uv038=0,uv039=null where uv002='$_username'");
				echo json_encode(array('name'=>'+服务中心'));
				exit;
			}else{
				$db->yiexec("update ymuv set uv038=2,uv039=".time()." where uv002='$_username'");
				echo json_encode(array('name'=>'- 服务中心'));
				exit;
			}
		break;			
		case 'tiqu':
			$rm=$member->getum($_username);
			if($rm['um021']){
				$db->yiexec("update ymum set um021=0 where um002='$_username'");
				echo json_encode(array('name'=>'+提现'));
				exit;
			}else{
				$db->yiexec("update ymum set um021=1 where um002='$_username'");
				echo json_encode(array('name'=>'- 提现'));
				exit;
			}
		break;	
		case 'zhuan':
			$rm=$member->getum($_username);
			if($rm['um022']){
				$db->yiexec("update ymum set um022=0 where um002='$_username'");
				echo json_encode(array('name'=>'+转账'));
				exit;
			}else{
				$db->yiexec("update ymum set um022=1 where um002='$_username'");
				echo json_encode(array('name'=>'- 转账'));
				exit;
			}
		break;	
		case 'huan':
			$rm=$member->getum($_username);
			if($rm['um023']){
				$db->yiexec("update ymum set um023=0 where um002='$_username'");
				echo json_encode(array('name'=>'+转账'));
				exit;
			}else{
				$db->yiexec("update ymum set um023=1 where um002='$_username'");
				echo json_encode(array('name'=>'- 转账'));
				exit;
			}
		break;
		case 'info':
			$rm=$member->getum($_username);
			if($rm['um024']){
				$db->yiexec("update ymum set um024=0 where um002='$_username'");
				echo json_encode(array('name'=>'+资料'));
				exit;
			}else{
				$db->yiexec("update ymum set um024=1 where um002='$_username'");
				echo json_encode(array('name'=>'- 资料'));
				exit;
			}
		break;	
		case 'amap':
			$rm=$member->getum($_username);
			if($rm['um025']){
				$db->yiexec("update ymum set um025=0 where um002='$_username'");
				echo json_encode(array('name'=>'+网络图'));
				exit;
			}else{
				$db->yiexec("update ymum set um025=1 where um002='$_username'");
				echo json_encode(array('name'=>'- 网络图'));
				exit;
			}
		break;	

		case 'pwd':
			$p=chkpwd(array($YCF['syuserpwd'][0]));
			if($p!='true'){
				echo json_encode(array('name'=>'请在系统参数设置中设置注册默认密码，不能小于8位','statu'=>0));
				exit;				
			}		
			$p=chkpwd(array($YCF['syuserpwd'][1]));
			if($p!='true'){
				echo json_encode(array('name'=>'请在系统参数设置中设置注册默认密码，不能小于8位','statu'=>0));
				exit;				
			}	
			$p=chkpwd(array($YCF['syuserpwd'][2]));
			if($p!='true'){
				echo json_encode(array('name'=>'请在系统参数设置中设置注册默认密码，不能小于8位','statu'=>0));
				exit;				
			}				
			$m1=getpwd($YCF['syuserpwd'][0]);
			$m2=getpwd($YCF['syuserpwd'][1]);
			$m3=getpwd($YCF['syuserpwd'][2]);


			$db->yiexec("update ymum set um005='$m1',um006='$m2',um007='$m3' where um002='$_username'");
			echo json_encode(array('name'=>'新密码重置成功','statu'=>1));
			exit;
	
		break;														
	}
}

?>