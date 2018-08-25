<?php
defined('YIMAOMONEY') or exit('Access denied');



if($_POST['act']=='edit'){
		$userid=$_POST['userid'];
		$edid=$_POST['edid'];
		$newname=trim($_POST['newname']);
		$arr['uv002']=$newname;
		$s=chkreginfo($arr);
		if(!empty($s)) yimao_automsg($s,geturl(),1);

		$sql="update ymum set um002='$newname' where um001=$edid";
		$sql.=";update ymuv set uv002='$newname' where uv001=$edid";
		$sql.=";update ymua set ua002='$newname' where ua002='$userid'";
		$sql.=";update ymue set ue002='$newname' where ue002='$userid'";
		$sql.=";update ymue set ue003='$newname' where ue003='$userid'";
		$sql.=";update ymui set ui002='$newname' where ui002='$userid'";
		$sql.=";update ymug set ug002='$newname' where ug002='$userid'";
		$sql.=";update ymuz set uz002='$newname' where uz002='$userid'";
		$sql.=";update ymuz set uz003='$newname' where uz003='$userid'";
		$sql.=";update ymuz set uj002='$newname' where uj002='$userid'";
		$sql.=";update ymuf set uf002='$newname' where uf002='$userid'";
		$sql.=";update ymuu set uu002='$newname' where uu002='$userid'";
		$sql.=";update ymuw set uw001='$newname' where uw001='$userid'";
		$sql.=";update ymub set ub002='$newname' where ub002='$userid'";

		$db->yisqli($sql);
		yimao_writelog('修改'.$userid.'会员新账号'.$newname);
		yimao_automsg('修改成功！',YMCURL.'formallist');
}

$edid=getnums($_GET['edid'],0);

$rs=$member->getuserinfo($edid);
if(empty($rs)){
	yimao_automsg('找不到此会员！',YMCURL.'formallist',1);
}


echo '<h3>修改会员账号</h3>';
echo '<div class="main-r-body"><form method="post" action="'.geturl().'">
		<div class="main-r-item"><a href="'.YMCURL.'formallist" class="yimaoabtn">返回正式会员列表</a><input type="submit" class="yimaoabtn" value="提交"></div>
		<input type="hidden" name="act" value="edit"><input type="hidden" name="edid" value="'.$rs['uv001'].'">
		<input type="hidden" name="userid" value="'.$rs['uv002'].'">
	<table class="yimaoregtab yimaotab">
		<tr>
			<td class="wdb20 alignr paddingr">会员账号</td>
			<td class="alignl paddingl">'.$rs['uv002'].'</td>
		</tr>
		<tr>
			<td class="wdb20 alignr paddingr">新账号</td>
			<td class="alignl paddingl">'.yimao_input(array('newname','','wd400'),1).'&nbsp;&nbsp;注:谨慎修改,或可能造成该会员原有的记录丢失</td>
		</tr>
										
		</table>
	</form></div>';		

?>