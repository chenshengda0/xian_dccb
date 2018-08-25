<?php
defined('YIMAOMONEY') or exit('Access denied');

if($_POST['act']=='删除所选'){
	if(empty($_POST["cbid"])) yimao_automsg('请先选择要删除的邮件！',$YCF['curl'],1);
	$u_id=implode(",",$_POST["cbid"]);

	$db->yiquery("select * from ymue where ue001 in($u_id)");
	foreach ($db->rs as $k => $v) {
		$db->yiexec("delete from ymue where ue001=".$v["ue001"]);
		yimao_writelog('删除邮件:'.$v["ue005"]);
	}

	yimao_automsg('删除邮件成功！',$YCF['curl'],1);
}


$deid=getnums($_GET['deid'],0);
if(!empty($deid)){

	$db->yiquery("select * from ymue where ue001=$deid",2);
	if(empty($db->rs)){
		yimao_automsg('邮件不存在！',$YCF['curl'],1);
	}else{
		$ue005=$db->rs['ue005'];
	}

	$db->yiexec('delete from ymue where ue001='.$deid);
	yimao_writelog('删除邮件:'.$ue005);
	yimao_automsg('删除邮件成功！',$YCF['curl']);
}

$pagecount=getnums($_GET["pagecount"],$YCF['pagecount']);
$option=getnums($_GET['option'],1);
$orderby=getnums($_GET['orderby'],1);
$quser=trim($_GET['quser']);

$arrtype=getemailtype(array(4));

$yimao_arr['yimao_option'].='<input type="submit" name="act" value="删除所选" class="yimaoabtn"   onclick="if(confirm(\'您确定要删除所选邮件吗?\')){artload(\'删除邮件中,请耐心等待！\')}else{return false}"><a href="'.getqueurl('option',1).'" class="'.geteqval(array($option,1,'yimaoaselbtn','yimaoabtn')).'">今日邮件</a><a href="'.getqueurl('option',2).'" class="'.geteqval(array($option,2,'yimaoaselbtn','yimaoabtn')).'">所有邮件</a>';
foreach ($arrtype as $k => $v) {
	$yimao_arr['yimao_option'].='<a href="'.getqueurl('option',$k+3).'" class="'.geteqval(array($option,$k+3,'yimaoaselbtn','yimaoabtn')).'">'.$v.'</a>';
}


$yimao_arr['yimao_orderby']='<a href="'.getqueurl('orderby',geteqval(array($orderby,1,'2','1'))).'" class="'.((strstr($orderby,'1')||strstr($orderby,'2'))?"yimaopaixusel":"yimaopaixu").'">'.geteqval(array($orderby,1,'时间↓','时间↑')).'</a>';


$yimao_arr['yimao_color']='<a href="javascript:;" class="yimaocolor showcolor0">未阅</a><a href="javascript:;" class="yimaocolor showcolor1">已阅</a>';



$condition=" where ue011=0";

if($quser){
	if(strstr($quser,'-')){
		$qdate=explode('-',$quser);
		if($qdate[0]) $condition.=" and from_unixtime(ue004,'%Y')=".$qdate[0]."";
		if($qdate[1]) $condition.=" and from_unixtime(ue004,'%m')=".$qdate[1]."";
		if($qdate[2]) $condition.=" and from_unixtime(ue004,'%d')=".$qdate[2]."";
	}else{
		$condition.=" and (ue003 like '%".$quser."%' or ue002 like '%".$quser."%' or ue005 like '%".$quser."%')";
	}
}

if($option==1) $condition.=" and  from_unixtime(ue004,'%Y-%m-%d')='".date('Y-m-d')."'";
if($option>2) $condition.=' and  ue010='.($option-3);


$order='';
if($orderby==1) $order=' order by ue004 desc';
if($orderby==2) $order=' order by ue004 asc';


$yimao_arr['yimao_th']='<th><input type="checkbox" onclick="javascript:checkall(\'cbid[]\')"></th><th>序号</th><th>邮件标题</th><th>邮件类型</th><th>发送会员</th><th>回复数量</th><th>发送时间</th><th>操作</th>';

$psql="select count(0) from ymue $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yimao=emailmanage&pagecount=$pagecount&orderby=$orderby&option=$option&quser=$quser";	  //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymue  $condition $order limit $offset,$pagecount";
$db->yiquery($sql);
$r=$db->rs;
foreach ($r as $k => $v) {
	$ii++;
	$serialid=($pagearr[2]-1)*$pagecount+$ii;

	$db->yiquery("select count(0) from ymue where ue011=".$v['ue001'],3);
	$yuehf=$db->fval;
	if(!empty($yuehf)){$yuehf='<span style="color:#f00">'.$yuehf.'</span>';}
	$yimao_arr['yimao_tr'].='<tr class="showcolor'.$v['ue007'].'">
						<td><input type="checkbox" name="cbid[]" value="'.$v['ue001'].'"></td>
						<td><span  title="'.$v["ue001"].'">'.$serialid.'</span></td>
						<td align="left">&nbsp;'.$v['ue005'].'</td>
						<td>'.$arrtype[$v['ue010']].'</td>
						<td>'.$v['ue003'].'</td>
						<td align="right">'.$yuehf.'&nbsp;</td>
						<td>'.formatdate($v['ue004']).'</td>		
						<td><a href="'.YMCURL.'yjxviewmag&edid='.$v['ue001'].'">查看</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$YCF['curl'].'&deid='.$v['ue001'].'" onclick="return confirm(\'您确定要删除吗?\')">删除</a></td>	
						</tr>';
}


$yimao_arr['yimao_count']=$pcount;
$yimao_arr['yimao_title']=getmenu($YCF['menu'],'emailmanage');
$yimao_arr['yimao_page']=$pagearr[0];
yimao_admin_two($yimao_arr);

?>