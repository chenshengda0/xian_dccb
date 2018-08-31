<?php
defined('YIMAOMONEY') or exit('Access denied');

$deid=getnums($_GET['deid'],0);
if(!empty($deid)){

	$db->yiquery('select * from ymun where un001='.$deid,2);
	if(empty($db->rs)){
		yimao_automsg('文章不存在！',$YCF['curl'],1);
	}

	$db->yiexec('delete from ymun where un001='.$deid);
	yimao_writelog('删除文章:'.$db->rs['un002']);
	yimao_automsg('删除文章成功！',$YCF['curl']);
}

$veid=getnums($_GET['veid'],0);
if(!empty($veid)){
	$db->yiquery('select * from ymun where un001='.$veid,2);
	if(empty($db->rs)){
		yimao_automsg('文章不存在！',$YCF['curl'],1);
	}

	echo '<h3>文章阅读</h3>';
	echo '<div class="main-r-body"><div class="artview">
		标题：'.$db->rs['un002'].'</div>'.htmlspecialchars_decode($db->rs['un005']).'
	</div><a href="javascript:history.go(-1)" class="artreturn">[ 返回 ]</a>';
		exit;
}

$pagecount=getnums($_GET["pagecount"],$YCF['pagecount']);
$option=getnums($_GET['option'],1);
$orderby=getnums($_GET['orderby'],1);
$quser=trim($_GET['quser']);

$arrtype=getatricletype(array(4));

$yimao_arr['yimao_option'].='<a href="'.getqueurl('option',1).'" class="'.geteqval(array($option,1,'yimaoaselbtn','yimaoabtn')).'">所有文章</a>';
foreach ($arrtype as $k => $v) {
	$yimao_arr['yimao_option'].='<a href="'.getqueurl('option',$k+2).'" class="'.geteqval(array($option,$k+2,'yimaoaselbtn','yimaoabtn')).'">'.$v.'</a>';
}


$yimao_arr['yimao_orderby']='<a href="'.getqueurl('orderby',geteqval(array($orderby,1,'2','1'))).'" class="'.((strstr($orderby,'1')||strstr($orderby,'2'))?"yimaopaixusel":"yimaopaixu").'">'.geteqval(array($orderby,1,'时间↓','时间↑')).'</a>';


$yimao_arr['yimao_color']='<a href="javascript:;" class="yimaocolor showcolor0">允许显示</a><a href="javascript:;" class="yimaocolor showcolor1">禁止显示</a>';



$condition=' where 1=1 ';

if($quser){
	if(strstr($quser,'-')){
		$qdate=explode('-',$quser);
		if($qdate[0]) $condition.=" and from_unixtime(un003,'%Y')=".$qdate[0]."";
		if($qdate[1]) $condition.=" and from_unixtime(un003,'%m')=".$qdate[1]."";
		if($qdate[2]) $condition.=" and from_unixtime(un003,'%d')=".$qdate[2]."";
	}else{
		$condition.=" and un002 like '%".$quser."%'";
	}
}

if($option>1) $condition.=' and  un004='.($option-2);


$order='';
if($orderby==1) $order=' order by un003 desc';
if($orderby==2) $order=' order by un003 asc';


$yimao_arr['yimao_th']='<th>序号</th><th>文章标题</th><th>文章类型</th><th>浏览次数</th><th>是否置顶</th><th>发布人</th><th>添加时间</th><th>操作</th>';

$psql="select count(0) from ymun $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yimao=articlelist&pagecount=$pagecount&orderby=$orderby&option=$option&quser=$quser";	  //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymun  $condition $order limit $offset,$pagecount";
$db->yiquery($sql);
$ii=0;
foreach ($db->rs as $k => $v) {
	$ii++;
	$serialid=($pagearr[2]-1)*$pagecount+$ii;
	$yimao_arr['yimao_tr'].='<tr class="showcolor'.$v['un007'].'">
						<td><span  title="'.$v["un001"].'">'.$serialid.'</span></td>
						<td align="left">&nbsp;&nbsp;<a href="'.$YCF['curl'].'&veid='.$v['un001'].'" class="alinktxt">'.$v['un002'].'</a></td>
						<td>'.$arrtype[$v['un004']].'</td>
						<td>'.$v['un009'].'</td>
						<td>'.geteqval(array($v['un006'],0,'<span style="color:#555">否</span>','<span style="color:#f00">是</span>')).'</td>	
						<td>'.$v['un008'].'</td>	
						<td>'.formatdate($v['un003']).'</td>	
						<td><a href="'.YMCURL.'articleedit&edid='.$v['un001'].'">修改</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$YCF['curl'].'&deid='.$v['un001'].'" onclick="return confirm(\'您确定要删除吗?\')">删除</a></td>	
						</tr>';
}


$yimao_arr['yimao_count']=$pcount;
$yimao_arr['yimao_title']=getmenu($YCF['menu'],'articlelist');
$yimao_arr['yimao_page']=$pagearr[0];
yimao_admin_two($yimao_arr);

?>