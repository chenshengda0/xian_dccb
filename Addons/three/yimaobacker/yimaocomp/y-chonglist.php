<?php
defined('YIMAOMONEY') or exit('Access denied');


if($_POST['submit']=='确认'){
	if(empty($_POST["cbid"])) yimao_automsg('请先选择要确认的充值！',$YCF['curl'],1);
	$u_id=implode(",",$_POST["cbid"]);

	$db->yiquery("select * from ymug where ug008=0 and ug001 in($u_id)");
	if(empty($db->rs)) yimao_automsg('操作无效！',$YCF['curl'],1);
	foreach ($db->rs as $k => $v) {
		$sql='update ymug set ug008=1,ug004='.time().' where ug001='.$v['ug001'];
		$jine=$v["ug005"];
		$db->yiexec($sql);

		yimao_writeaccount(array($member->getusid($v['ug002']),"0",0,time(),2,$jine,""));

	}

	yimao_writelog('确认充值');
	yimao_automsg('确认充值成功！',$YCF['curl']);
}

if($_POST['submit']=='撤销'){
	if(empty($_POST["cbid"])) yimao_automsg('请先选择要撤销的充值！',$YCF['curl'],1);
	$u_id=implode(",",$_POST["cbid"]);

	$db->yiexec("update ymug set ug008=2,ug004=".time()." where ug008=0 and ug001 in($u_id)");
	if(empty($db->rownums)) yimao_automsg('操作无效！',$YCF['curl'],1);

	yimao_writelog('撤销充值');
	yimao_automsg('充值撤销成功！',$YCF['curl']);
}


$pagecount=getnums($_GET["pagecount"],$YCF['pagecount']);
$option=getnums($_GET['option'],1);
$orderby=getnums($_GET['orderby'],1);
$quser=trim($_GET['quser']);

$yimao_arr['yimao_option'].='<input type="submit"  onclick="return boxcheck(\'请先选择要确认的记录\',\'您确定要确认吗?\',\'确认正在执行中,请耐心等待！\');" value="确认" name="submit" class="yimaoabtn"><input type="submit"  onclick="return boxcheck(\'请先选择要撤销的记录\',\'您确定要撤销吗?\',\'撤销正在执行中,请耐心等待！\');"  name="submit" value="撤销" class="yimaoabtn"><a href="'.getqueurl('option',1).'" class="'.geteqval(array($option,1,'yimaoaselbtn','yimaoabtn')).'">今日新增未确认</a><a href="'.getqueurl('option',2).'" class="'.geteqval(array($option,2,'yimaoaselbtn','yimaoabtn')).'">所有未确认</a><a href="'.getqueurl('option',3).'" class="'.geteqval(array($option,3,'yimaoaselbtn','yimaoabtn')).'">所有已撤销</a><a href="'.getqueurl('option',4).'" class="'.geteqval(array($option,4,'yimaoaselbtn','yimaoabtn')).'">全部</a>';


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
		if($qdate[0]) $condition.=" and from_unixtime(ug003,'%Y')=".$qdate[0]."";
		if($qdate[1]) $condition.=" and from_unixtime(ug003,'%m')=".$qdate[1]."";
		if($qdate[2]) $condition.=" and from_unixtime(ug003,'%d')=".$qdate[2]."";
	}else{
		$condition.=" and ug002 like '%".$quser."%'";
	}
}

if($option==1) $condition.=" and from_unixtime(ug003,'%Y-%m-%d')='".date('Y-m-d')."' and ug008=0";
if($option==2) $condition.=' and ug008=0';
if($option==3) $condition.=' and ug008=2';


$order='';
if($orderby==1) $order=' order by ug003 desc';
if($orderby==2) $order=' order by ug003 asc';


$yimao_arr['yimao_th']='<th><input type="checkbox" onclick="javascript:checkall(\'cbid[]\')"></th><th>序号</th><th>会员账号</th><th>姓名</th><th>申请时间</th><th>充值金额</th><th>状态</th><th>备注</th>';


$psql="select count(0) from ymug $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yimao=chonglist&pagecount=$pagecount&orderby=$orderby&option=$option&quser=$quser";	  //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymug  $condition $order limit $offset,$pagecount";
$db->yiquery($sql);
$ii=0;
foreach ($db->rs as $k => $v) {
	$ii++;
	$serialid=($pagearr[2]-1)*$pagecount+$ii;
	$rm=$member->getum($v['ug002'],'um003,um004');
	$yimao_arr['yimao_tr'].='<tr class="levelcolor'.$rm['um004'].'">
						<td><input type="checkbox" name="cbid[]" value="'.$v['ug001'].'"></td>
						<td><span  title="'.$v["ug001"].'">'.$serialid.'</span></td>
						<td>'.$v['ug002'].'</td>
						<td>'.$rm['um003'].'</td>
						<td>'.formatdate($v['ug003']).'</td>
						<td align="right">'.formatrmb($v['ug005']).'&nbsp;&nbsp;</td>
						<td>'.getstatutype($v['ug008'],2).'</td>
						<td style="width:260px" title="'.$v['ug009'].'" align="left">&nbsp;<div style="float:left;width:250px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;cursor:pointer">&nbsp;&nbsp;'.$v['ug009'].'</div></td>';

	$yimao_arr['yimao_tr'].='</tr>';
}


$yimao_arr['yimao_count']=$pcount;
$yimao_arr['yimao_title']=getmenu($YCF['menu'],'chonglist');
$yimao_arr['yimao_page']=$pagearr[0];
yimao_admin_two($yimao_arr);

?>