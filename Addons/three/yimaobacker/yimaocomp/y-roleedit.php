<?php
defined('YIMAOMONEY') or exit('Access denied');



if($_POST['act']=='edit'){
		$pows=$_POST['pows'];
		$rolename=trim($_POST['rolename']);
		$edid=$_POST['edid'];
		$status=$_POST['status'];
		if(empty($edid)) yimao_automsg('数据丢失无法提交',$YCF['curl'],1);
		if(empty($rolename)) yimao_automsg('请输入角色名称',$YCF['curl'],1);
		if(empty($pows)) yimao_automsg('请为角色至少指派一个权限',$YCF['curl'],1);
		//if($edid==1) yimao_automsg('该角色禁止编辑',$YCF['curl'],1);
		$max='';
		foreach ($pows as $k => $v) {
			$s=getmenu($YCF['menu'],$v,1);
			if(!strstr($max,$s)){
				$max.="$s,";
			}
		}
		$min=implode(',',$pows);
		$db->yiquery("select * from ymuo where uo002='$rolename' and uo001!=$edid",2);
		if(!empty($db->rs)){
			 yimao_automsg('角色名称已经存在',$YCF['curl'],1);
		}

		$db->yiquery('select * from ymuo where uo001='.$edid,2);
		if(empty($db->rs)){
			yimao_automsg('找不到此角色！',YMCURL.'rolelist',1);
		}else{
			$uo002=$db->rs["uo002"];
		}

		$db->yiexec("update ymuo set uo002='$rolename',uo003='$max',uo004='$min',uo005=$status where uo001=$edid");
		yimao_writelog('修改角色:'.$uo002);
		yimao_automsg('修改角色成功！',YMCURL.'rolelist');
}

$edid=getnums($_GET['edid'],0);

$db->yiquery('select * from ymuo where uo001='.$edid,2);
if(empty($db->rs)){
	yimao_automsg('找不到此角色！',YMCURL.'rolelist',1);
}

echo '<h3>角色编辑</h3>';
echo '<div class="main-r-body"><form method="post" action="'.geturl().'">
		<div class="main-r-item"><a href="'.YMCURL.'rolelist" class="yimaoabtn">返回角色列表</a><input type="submit" class="yimaoabtn" value="提交"></div>
		<input type="hidden" name="act" value="edit"><input type="hidden" name="edid" value="'.$db->rs['uo001'].'">';
echo '<table class="yimaoregtab yimaotab">';
echo '	<tr>
			<td class="wdb20 alignr paddingr">角色名称</td>
			<td class="alignl paddingl"><input type="text" name="rolename" value="'.$db->rs['uo002'].'" class="yimaoconfig wd400"></td>
		</tr>';
echo '	<tr>
			<td class="wdb20 alignr paddingr">状态</td>
			<td class="alignl paddingl">'.yimao_radio(array('status',$db->rs['uo005']),2).'</td>
		</tr>';		
echo '	<tr>
			<td class="wdb20 alignr paddingr" valign="top" >指派权限</td>
			<td class="alignl paddingl">';
$i=0;
foreach ($YCF['menu'] as $kr => $vr) {
$i++;
echo '<ul class="ulpower"><li style="width:13%;color:#66D9EF;"><label><input type="checkbox" onclick="javascript:checkbox(this,\'a'.$i.'\')" style="vertical-align:middle">'.$kr.'</label></li><li style="width:82%;border-bottom:1px dotted #535548">';
	foreach ($vr as $k => $v) {
		echo '<label><input type="checkbox" name="pows[]" value="'.$v[0].'" class="a'.$i.'" '.getstrval(array($db->rs['uo004'],$v[0],'checked','')).'>&nbsp;'.$k.'</label>&nbsp;';
	}
echo '</ul>';				
}		
echo '		</td>
		</tr>';		
echo '</table>';
echo '</form></div>';		

?>