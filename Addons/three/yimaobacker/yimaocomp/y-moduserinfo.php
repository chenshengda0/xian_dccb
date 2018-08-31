<?php
defined('YIMAOMONEY') or exit('Access denied');



if($_POST['act']=='edit'){
		$userid=$_POST['userid'];
		$edid=$_POST['edid'];
		$arr['um003']=trim($_POST['username']);
		foreach ($_POST['ptd'] as $k => $v) {
			yimao_editinfo($v,trim($_POST[$v]),$arr);
		}

		if(trim($_POST['rgpwd1'])){
			$p1=chkpwd(array(trim($_POST['rgpwd1'])));
			if($p1!='true'){
				a_bck('密码不能小于8位');
				exit;				
			}
			$arr['um005']=getpwd(trim($_POST['rgpwd1']));
		}
		if(trim($_POST['rgpwd2'])){
			$p2=chkpwd(array(trim($_POST['rgpwd2'])));
			if($p2!='true'){
				a_bck('密码不能小于8位');
				exit;				
			}
			$arr['um006']=getpwd(trim($_POST['rgpwd2']));
		}	
		if(trim($_POST['rgpwd3'])){
			$p3=chkpwd(array(trim($_POST['rgpwd3'])));
			if($p3!='true'){
				a_bck('密码不能小于8位');
				exit;				
			}
			$arr['um007']=getpwd(trim($_POST['rgpwd3']));
		}

		$sql=getupdatesql($arr,'ymum',' um001='.$edid,'um008,um016');
		$db->yiexec($sql);
		yimao_writelog('修改'.$userid.'会员资料');
		yimao_automsg('修改成功！',YMCURL.'formallist');
}

$edid=getnums($_GET['edid'],0);

$rs=$member->getuserinfo($edid);
if(empty($rs)){
	yimao_automsg('找不到此会员！',YMCURL.'formallist',1);
}

$sql="select * from ymur where ur006=0 order by ur005 asc";
$db->yiquery($sql);
$rr=$db->rs;

$rgform=getregform(array('mbq'=>$rs['um008'],'sex'=>$rs['um016'],'sclass'=>'yimaoselect','province'=>$rs['um018'],'city'=>$rs['um019'],'area'=>$rs['um020']));

echo '<script type="text/javascript" src="yimaores/js/pcasunzip.js"></script><h3>修改资料</h3>';
echo '<div class="main-r-body"><form method="post" action="'.geturl().'">
		<div class="main-r-item"><a href="'.YMCURL.'formallist" class="yimaoabtn">返回正式会员列表</a><input type="submit" class="yimaoabtn" value="提交"></div>
		<input type="hidden" name="act" value="edit"><input type="hidden" name="edid" value="'.$rs['uv001'].'">
		<input type="hidden" name="userid" value="'.$rs['uv002'].'">
	<table class="yimaoregtab yimaotab">
		<tr>
			<td class="wdb20 alignr paddingr">会员账号</td>
			<td class="alignl paddingl">'.$rs['uv002'].'</td>
		</tr>';
if(is_array($YCF['jjname'])){
echo '	<tr>
			<td class="wdb20 alignr paddingr">级别</td>
			<td class="alignl paddingl">'.$YCF['jjname'][$rs['uv006']].'</td>
		</tr>';
}		
echo '		
		<tr>
			<td class="wdb20 alignr paddingr">注册日期</td>
			<td class="alignl paddingl">'.formatdate($rs['uv003']).'</td>
		</tr>
		<tr>
			<td class="wdb20 alignr paddingr">开通日期</td>
			<td class="alignl paddingl">'.formatdate($rs['uv004']).'</td>
		</tr>	
		<tr>
			<td class="wdb20 alignr paddingr">注册人</td>
			<td class="alignl paddingl">'.$member->getreguser($rs['uv036']).'</td>
		</tr>	
		<tr>
			<td class="wdb20 alignr paddingr">开通人</td>
			<td class="alignl paddingl">'.$member->getreguser($rs['uv037']).'</td>
		</tr>	
		<tr>
			<td class="wdb20 alignr paddingr">推荐人数</td>
			<td class="alignl paddingl">'.$rs['uv016'].'</td>
		</tr>	
		<tr>
			<td class="wdb20 alignr paddingr">推荐人</td>
			<td class="alignl paddingl">'.$member->getuserid($rs['uv018']).'</td>
		</tr>
		<tr>
			<td class="wdb20 alignr paddingr">总奖金</td>
			<td class="alignl paddingl">'.$rs['uv011'].'</td>
		  </tr>											
		';
if($YCF['regan']){
	echo '<tr>
			<td class="wdb20 alignr paddingr">安置人</td>
			<td class="alignl paddingl">'.$member->getuserid($rs['uv019']).' '.$YCF['anrea'][$rs['uv024']].'</td>
		  </tr>';
}
if($YCF['regbd']){
	echo '<tr>
			<td class="wdb20 alignr paddingr">服务中心</td>
			<td class="alignl paddingl">'.$member->getuserid($rs['uv017']).'</td>
		  </tr>';
}
	echo '<tr>
			<td class="wdb20 alignr paddingr">姓名</td>
			<td class="alignl paddingl">'.yimao_input(array('username',$rs['um003'],'wd400'),1).'</td>
		  </tr>';
foreach ($rr as $k => $v) {
	echo '<input type="hidden" name="ptd[]" value="'.$v['ur001'].'">';
	if($v['ur004']==0){
	echo '<tr>
			<td class="wdb20 alignr paddingr">'.$v['ur002'].'</td>
			<td class="alignl paddingl">'.yimao_input(array($v['ur001'],yimao_autoinfo($v['ur001'],$rs),'wd400'),1).'</td>
		  </tr>';
	}elseif($v['ur004']==1){
	echo '<tr>
			<td class="wdb20 alignr paddingr">'.$v['ur002'].'</td>
			<td class="alignl paddingl">'.$rgform['sex'].'</td>
		  </tr>';
	}elseif($v['ur004']==2){
		if($v['ur001']=='rgmbq'){
			echo '<tr>
					<td class="wdb20 alignr paddingr">'.$v['ur002'].'</td>
					<td class="alignl paddingl">'.$rgform['mbq'].'</td>
				  </tr>';
		}elseif($v['ur001']=='rgprovince'){
			echo '<input type="hidden" name="ptd[]" value="province">';
			echo '<input type="hidden" name="ptd[]" value="city">';
			echo '<input type="hidden" name="ptd[]" value="area">';
			echo '<tr>
					<td class="wdb20 alignr paddingr">'.$v['ur002'].'</td>
					<td class="alignl paddingl">'.$rgform['province'].'</td>
				  </tr>';
		}
	}elseif($v['ur004']==3){
	echo '<tr>
			<td class="wdb20 alignr paddingr">'.$v['ur002'].'</td>
			<td class="alignl paddingl"><input type="text" name="'.$v['ur001'].'" value="" class="yimaoconfig wd400"></td>
		  </tr>';
	}

}

echo '</table>
	</form></div>';		

?>