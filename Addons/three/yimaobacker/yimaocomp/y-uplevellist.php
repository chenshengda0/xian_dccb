<?php
defined('YIMAOMONEY') or exit('Access denied');

if($_POST['submit']=='撤销'){
	if(empty($_POST["cbid"])) yimao_automsg('请先选择要撤销的记录！',$YCF['curl'],1);
	$u_id=implode(",",$_POST["cbid"]);


	$sql="update ymuu set uu006=2 where uu006=0 and uu001 in($u_id)";
	$db->yiexec($sql);

	yimao_writelog('撤销升级');
	yimao_automsg('撤销成功！',$YCF['curl']);
}

if($_POST['submit']=='确认'){
	if(empty($_POST["cbid"])) yimao_automsg('请先选择要确认的记录！',$YCF['curl'],1);
	$u_id=implode(",",$_POST["cbid"]);

	$db->yiquery("select * from ymuu where uu006=0 and uu001 in($u_id)");
	$rs=$db->rs;
	foreach ($rs as $k => $v) {
		$sql="update ymuv set uv006='".$v['uu005']."' where uv002='".$v['uu002']."'";
		$sql.=";update ymum set um004='".$v['uu005']."' where um002='".$v['uu002']."'";
		$sql.=";update ymuu set uu006=1 where uu001=".$v['uu001'];
		$db->yisqli($sql);
		yimao_writelog('确认'.$v["uu002"].'升级'.$YCF["jjname"][$v["uu005"]].'，id为'.$v["uu001"]);
	}
	
	yimao_automsg('确认成功！',$YCF['curl']);
}

$pagecount=getnums($_GET["pagecount"],$YCF['pagecount']);
$option=getnums($_GET['option'],1);
$orderby=getnums($_GET['orderby'],1);
$quser=trim($_GET['quser']);

$yimao_arr['yimao_option'].='<input type="submit" name="submit"   onclick="return boxcheck(\'请先选择要确认的记录\',\'您确定要确认吗?\',\'确认执行中,请耐心等待！\');"  value="确认" class="yimaoabtn"><input type="submit" name="submit"   onclick="return boxcheck(\'请先选择要撤销的记录\',\'您确定要撤销吗?\',\'撤销正在执行中,请耐心等待！\');"  value="撤销" class="yimaoabtn"><a href="'.getqueurl('option',1).'" class="'.geteqval(array($option,1,'yimaoaselbtn','yimaoabtn')).'">待确认升级</a><a href="'.getqueurl('option',2).'" class="'.geteqval(array($option,2,'yimaoaselbtn','yimaoabtn')).'">已确认升级</a><a href="'.getqueurl('option',3).'" class="'.geteqval(array($option,3,'yimaoaselbtn','yimaoabtn')).'">已撤销升级</a><a href="'.getqueurl('option',4).'" class="'.geteqval(array($option,4,'yimaoaselbtn','yimaoabtn')).'">今日新增</a><a href="'.getqueurl('option',5).'" class="'.geteqval(array($option,5,'yimaoaselbtn','yimaoabtn')).'">全部</a>';

$yimao_arr['yimao_orderby']='<a href="'.getqueurl('orderby',geteqval(array($orderby,1,'2','1'))).'" class="'.((strstr($orderby,'1')||strstr($orderby,'2'))?"yimaopaixusel":"yimaopaixu").'">'.geteqval(array($orderby,1,'时间↓','时间↑')).'</a>';

if(is_array($YCF['jjname'])){
foreach ($YCF['jjname'] as $k => $v) {
	$yimao_arr['yimao_color'].='<a href="javascript:;" class="yimaocolor levelcolor'.$k.'">'.$v.'</a>';
}
}


$condition=' where 1=1 ';

if($quser){
	if(strstr($quser,'-')){
		$qdate=explode('-',$quser);
		if($qdate[0]) $condition.=" and from_unixtime(uu003,'%Y')=".$qdate[0]."";
		if($qdate[1]) $condition.=" and from_unixtime(uu003,'%m')=".$qdate[1]."";
		if($qdate[2]) $condition.=" and from_unixtime(uu003,'%d')=".$qdate[2]."";
	}else{
		$condition.=" and uu002 like '%".$quser."%'";
	}
}

if($option==1) $condition.=" and uu006=0";
if($option==2) $condition.=" and uu006=1";
if($option==3) $condition.=" and uu006=2";
if($option==4) $condition.=" and from_unixtime(uu003,'%Y-%m-%d')='".date('Y-m-d')."'";


$order='';
if($orderby==1) $order=' order by uu003 desc';
if($orderby==2) $order=' order by uu003 asc';


$yimao_arr['yimao_th']='<th><input type="checkbox" onclick="javascript:checkall(\'cbid[]\')"></th><th>序号</th><th>账号</th><th>姓名</th><th>申请时间</th><th>原始级别</th><th>申请级别</th><th>状态</th>';

$psql="select count(0) from ymuu $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yimao=uplevellist&pagecount=$pagecount&orderby=$orderby&option=$option&quser=$quser";	  //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymuu  $condition $order limit $offset,$pagecount";
$db->yiquery($sql);
$ii=0;
foreach ($db->rs as $k => $v) {
	$ii++;
	$serialid=($pagearr[2]-1)*$pagecount+$ii;
	$rm=$member->getum($v['uu002'],'um003,um004');
	$yimao_arr['yimao_tr'].='<tr class="levelcolor'.$rm['um004'].'">
						<td><input type="checkbox" name="cbid[]" value="'.$v['uu001'].'"></td>
						<td><span  title="'.$v["uu001"].'">'.$serialid.'</span></td>
						<td>'.$v['uu002'].'</td>
						<td>'.$rm['um003'].'</td>
						<td>'.formatdate($v['uu003']).'</td>
						<td>'.getlevel($v['uu004']).'</td>
						<td>'.getlevel($v['uu005']).'</td>
						<td>'.getstatutype($v['uu006'],2).'</td>
						</tr>';
}


$yimao_arr['yimao_count']=$pcount;
$yimao_arr['yimao_title']=getmenu($YCF['menu'],'uplevellist');
$yimao_arr['yimao_page']=$pagearr[0];
yimao_admin_two($yimao_arr);

?>