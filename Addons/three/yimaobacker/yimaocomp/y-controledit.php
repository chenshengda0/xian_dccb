<?php
defined('YIMAOMONEY') or exit('Access denied');



if($_POST['act']=='edit'){
		$role=$_POST['role'];
		$depict=trim($_POST['depict']);
		$edid=$_POST['edid'];
		$pwd=trim($_POST['pwd']);
		$status=$_POST['status'];
		if(empty($edid)) yimao_automsg('数据丢失无法提交',YMCURL.'controllist',1);
		if(empty($role)) yimao_automsg('请选择一个角色',YMCURL.'controllist',1);
		if($edid==1) yimao_automsg('该账号禁止编辑',YMCURL.'controllist',1);
		if($role==1){
			yimao_automsg('不能分派此角色!',$YCF['curl'],1);
		}

		if(!empty($pwd)){
			$p=chkpwd(array($pwd));
			if($p!='true'){
				yimao_automsg($p,$YCF['curl'],1);
			}
			$p=getpwd($pwd);
			$psql=",ua009='$p'";	
		}

		$db->yiquery('select * from ymua where ua001='.$edid,2);
		if(empty($db->rs)){
			yimao_automsg('找不到此管理！',YMCURL.'controllist',1);
		}else{
			$ua002=$db->rs["ua002"];
		}
			
		$db->yiexec("update ymua set ua004=$role,ua005=$status,ua008='$depict' $psql where ua001=$edid");
		yimao_writelog('修改管理'.$ua002);
		yimao_automsg('修改管理成功！',YMCURL.'controllist');
}

$edid=getnums($_GET['edid'],0);

$db->yiquery('select * from ymua where ua001='.$edid,2);
if(empty($db->rs)){
	yimao_automsg('找不到此管理！',YMCURL.'controllist',1);
}
$ua004=$db->rs['ua004'];
echo '<h3>管理编辑</h3>';
echo '<div class="main-r-body"><form method="post" action="'.geturl().'">
		<div class="main-r-item"><a href="'.YMCURL.'controllist" class="yimaoabtn">返回管理列表</a><input type="submit" class="yimaoabtn" value="提交"></div>
		<input type="hidden" name="act" value="edit"><input type="hidden" name="edid" value="'.$db->rs['ua001'].'">';
echo '<table class="yimaoregtab yimaotab">';
echo '	<tr>
			<td class="wdb20 alignr paddingr">会员账号</td>
			<td class="alignl paddingl">'.$db->rs['ua002'].'</td>
		</tr>';
echo '	<tr>
			<td class="wdb20 alignr paddingr">密码</td>
			<td class="alignl paddingl"><input type="password" name="pwd" value="" class="yimaoconfig wd400"> 注:不修改请为空</td>
		</tr>';		
echo '	<tr>
			<td class="wdb20 alignr paddingr">状态</td>
			<td class="alignl paddingl">'.yimao_radio(array('status',$db->rs['ua005']),2).'</td>
		</tr>';			
echo '	<tr>
			<td class="wdb20 alignr paddingr">描述</td>
			<td class="alignl paddingl"><input type="text" name="depict" value="'.$db->rs['ua008'].'" class="yimaoconfig wd400"></td>
		</tr>';		
echo '	<tr>
			<td class="wdb20 alignr paddingr" valign="top" >指派角色</td>
			<td class="alignl paddingl">';
echo '<select name="role" class="yimaoconfig" style="color:#fff;text-indent:0;background:#272822;line-height:30px;height:30px">';
$db->yiquery('select * from ymuo where uo005=0');
foreach ($db->rs as $k => $v) {
if($edid>1){
	if($v['uo001']==1) continue;
}
echo '<option value="'.$v['uo001'].'" '.geteqval(array($ua004,$v['uo001'],'selected','')).'>'.$v['uo002'].'</option>';
}
echo '</select>';
echo '		</td>
		</tr>';		
echo '</table>';
echo '</form></div>';		

?>