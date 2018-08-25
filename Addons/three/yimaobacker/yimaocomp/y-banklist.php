<?php
defined('YIMAOMONEY') or exit('Access denied');

if($_POST['submit']=='删除'){
	if(empty($_POST["cbid"])) yimao_automsg('请先选择要删除的记录！',$YCF['curl'],1);
	$u_id=implode(",",$_POST["cbid"]);

	$db->yiexec("delete from ymub where ub001 in($u_id)");

	yimao_writelog('删除银行信息');
	yimao_automsg('删除成功！',$YCF['curl']);
}

$pagecount=getnums($_GET["pagecount"],$YCF['pagecount']);
$option=getnums($_GET['option'],1);
$orderby=getnums($_GET['orderby'],1);
$quser=trim($_GET['quser']);

$yimao_arr['yimao_option'].='<input type="submit" name="submit"   onclick="return boxcheck(\'请先选择要删除的记录\',\'您确定要删除吗?\',\'删除正在执行中,请耐心等待！\');"  value="删除" class="yimaoabtn"><a href="'.getqueurl('option',1).'" class="'.geteqval(array($option,1,'yimaoaselbtn','yimaoabtn')).'">今日新增</a><a href="'.getqueurl('option',2).'" class="'.geteqval(array($option,2,'yimaoaselbtn','yimaoabtn')).'">全部</a>';

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
		if($qdate[0]) $condition.=" and from_unixtime(ub003,'%Y')=".$qdate[0]."";
		if($qdate[1]) $condition.=" and from_unixtime(ub003,'%m')=".$qdate[1]."";
		if($qdate[2]) $condition.=" and from_unixtime(ub003,'%d')=".$qdate[2]."";
	}else{
		$condition.=" and ub002 like '%".$quser."%'";
	}
}
if($option==1) $condition.=" and from_unixtime(ub003,'%Y-%m-%d')='".date('Y-m-d')."'";


$order='';
if($orderby==1) $order=' order by ub003 desc';
if($orderby==2) $order=' order by ub003 asc';


$yimao_arr['yimao_th']='<th><input type="checkbox" onclick="javascript:checkall(\'cbid[]\')"></th><th>序号</th><th>账号</th><th>姓名</th><th>银行名称</th><th>银行卡号</th><th>银行姓名</th><th>开户地址</th><th>备注</th><th style="width:160px">添加日期</th><th>操作</th>';

$psql="select count(0) from ymub $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yimao=banklist&pagecount=$pagecount&orderby=$orderby&option=$option&quser=$quser";	  //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymub  $condition $order limit $offset,$pagecount";
$db->yiquery($sql);
$ii=0;
foreach ($db->rs as $k => $v) {
	$ii++;
	$serialid=($pagearr[2]-1)*$pagecount+$ii;
	$rm=$member->getum($v['ub002'],'um003,um004');
	$yimao_arr['yimao_tr'].='<tr class="levelcolor'.$rm['um004'].'">
						<td><input type="checkbox" name="cbid[]" value="'.$v['ub001'].'"></td>
						<td><span  title="'.$v["ub001"].'">'.$serialid.'</span></td>
						<td>'.$v['ub002'].'</td>
						<td>'.$rm['um003'].'</td>
						<td align="left" >&nbsp;&nbsp;'.$v['ub004'].'</td>
						<td align="left" >&nbsp;&nbsp;'.$v['ub005'].'</td>
						<td align="left" >&nbsp;&nbsp;'.$v['ub006'].'</td>
						<td align="left" >&nbsp;&nbsp;'.$v['ub007'].'</td>
						<td align="left" style="width:160px" title="'.$v['ub008'].'">&nbsp;<div style="float:left;width:150px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;cursor:pointer">&nbsp;&nbsp;'.$v['ub008'].'</div></td>
						<td align="left" title="'.formatdate($v['ub003']).'">&nbsp;&nbsp;'.formatdate($v['ub003'],1).'</td>
						<td align="center" ><a href="'.YMCURL.'bankedit&edid='.$v['ub001'].'">修改</a></td>
						</tr>';
}


$yimao_arr['yimao_count']=$pcount;
$yimao_arr['yimao_title']=getmenu($YCF['menu'],'banklist');
$yimao_arr['yimao_page']=$pagearr[0];
yimao_admin_two($yimao_arr);

?>