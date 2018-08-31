<?php
defined('YIMAOMONEY') or exit('Access denied');


if($_POST['act']=='edit'){
		$edid=$_POST['edid'];
		$arr['un002']=trim($_POST['atname']);
		$arr['un004']=trim($_POST['attype']);
		$arr['un003']=strtotime($_POST['attime']);
		$arr['un006']=trim($_POST['atding']);
		$arr['un007']=trim($_POST['atshow']);
		$arr['un009']=getnums(trim($_POST['atci']),0);
		$arr['un008']=trim($_POST['atrel']);
		$arr['un005']=htmlspecialchars(trim($_POST['atcontent']));

		if(empty($arr['un002'])) yimao_automsg('请输入文章标题',$YCF['curl'],1);
		if(empty($arr['un005'])) yimao_automsg('请输入文章内容',$YCF['curl'],1);

		$db->yiquery('select * from ymun where un001='.$edid,2);
		if(empty($db->rs)){
			yimao_automsg('找不到此文章！',YMCURL.'articlelist',1);
		}else{
			$un002=$db->rs["un002"];
		}


		$sql=getupdatesql($arr,'ymun',' un001='.$edid,'un004,un006,un009');

		$db->yiexec($sql);
		yimao_writelog('编辑文章:'.$un002.',id为'.$edid);
		yimao_automsg('编辑文章成功！',YMCURL."articlelist");
}

$edid=getnums($_GET['edid'],0);

$db->yiquery('select * from ymun where un001='.$edid,2);
if(empty($db->rs)){
	yimao_automsg('找不到此文章！',YMCURL.'articlelist',1);
}


echo '<h3>文章编辑</h3>';
echo '    <script type="text/javascript" charset="utf-8" src="yimaoedit/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="yimaoedit/ueditor.all.min.js"> </script>
    <script type="text/javascript" charset="utf-8" src="yimaoedit/lang/zh-cn/zh-cn.js"></script>';

echo '<div class="main-r-body"><form method="post" action="'.geturl().'">
		<div class="main-r-item"><a href="'.YMCURL.'articlelist" class="yimaoabtn">返回文章列表</a><input type="submit" class="yimaoabtn" value="修改"></div>
		<input type="hidden" name="act" value="edit"><input type="hidden" name="edid" value="'.$db->rs['un001'].'">';
echo '<div class="h35"><span class="hbase" onclick="qiehuan(1,\'hqu\',\'xx\')" id="xx1">基本信息</span><span class="hcontent" onclick="qiehuan(2,\'hqu\',\'xx\')"  id="xx2">文章内容</span></div>	';		
echo '<table class="yimaoregtab yimaotab hqu1">';
echo '	<tr>
			<td class="wdb20 alignr paddingr">文章标题</td>
			<td class="alignl paddingl"><input type="text" name="atname" value="'.$db->rs['un002'].'" class="yimaoconfig wd400"></td>
		</tr>';
echo '	<tr>
			<td class="wdb20 alignr paddingr">文章类型</td>
			<td class="alignl paddingl">'.getatricletype(array(3,$db->rs['un004'])).'</td>
		</tr>';	
echo '	<tr>
			<td class="wdb20 alignr paddingr">是否置顶</td>
			<td class="alignl paddingl">'.yimao_radio(array('atding',$db->rs['un006']),3).'</td>
		</tr>';	
echo '	<tr>
			<td class="wdb20 alignr paddingr">是否显示</td>
			<td class="alignl paddingl">'.yimao_radio(array('atshow',$db->rs['un007']),1).'</td>
		</tr>';	
echo '	<tr>
			<td class="wdb20 alignr paddingr">浏览次数</td>
			<td class="alignl paddingl"><input type="text" name="atci" value="'.$db->rs['un009'].'" class="yimaoconfig wd400"></td>
		</tr>';
echo '	<tr>
			<td class="wdb20 alignr paddingr">发布人</td>
			<td class="alignl paddingl"><input type="text" name="atrel" value="'.$db->rs['un008'].'" class="yimaoconfig wd400"></td>
		</tr>';	
echo '	<tr>
			<td class="wdb20 alignr paddingr">发布时间</td>
			<td class="alignl paddingl"><input type="text" name="attime" value="'.formatdate($db->rs['un003']).'" class="yimaoconfig wd400"></td>
		</tr>';								
echo '</table>';
echo '<table class="hqu2" style="visibility:hidden;box-shadow:none;">';
echo '	<tr>
			<td><script type="text/plain" name="atcontent" id="atcontent"  style="width:100%;height:400px;box-shadow:none;">'.htmlspecialchars_decode($db->rs['un005']).'</script></td>
		</tr>';						
echo '</table>';

echo '</form></div>';
echo '<script type="text/javascript">
	   var ue = UE.getEditor(\'atcontent\');     
    </script>';	

?>