<?php
defined('YIMAOMONEY') or exit('Access denied');

if($_POST['submit']=='取消服务中心'){
	if(empty($_POST["cbid"])) yimao_automsg('请先选择要取消的记录！',$YCF['curl'],1);
	$u_id=implode(",",$_POST["cbid"]);

	$db->yiexec("update ymuv set uv038=0,uv039=null where uv001 in($u_id)");
	yimao_writelog('取消服务中心');
	yimao_automsg('取消成功！',$YCF['curl']);
}



$pagecount=getnums($_GET["pagecount"],$YCF['pagecount']);
$option=getnums($_GET['option'],1);
$orderby=getnums($_GET['orderby'],1);
$quser=trim($_GET['quser']);

$yimao_arr['yimao_option'].='<input type="submit" name="submit"   onclick="return boxcheck(\'请先选择要取消的记录\',\'您确定要取消服务中心资格吗?\',\'取消正在执行中,请耐心等待！\');"  value="取消服务中心" class="yimaoabtn"><a href="'.getqueurl('option',1).'" class="'.geteqval(array($option,1,'yimaoaselbtn','yimaoabtn')).'">今日新增</a><a href="'.getqueurl('option',2).'" class="'.geteqval(array($option,2,'yimaoaselbtn','yimaoabtn')).'">全部</a>';
if(is_array($YCF['jjname'])){
foreach ($YCF['jjname'] as $k => $v) {
	$yimao_arr['yimao_option'].='<a href="'.getqueurl('option',$k+3).'" class="'.geteqval(array($option,$k+3,'yimaoaselbtn','yimaoabtn')).'">'.$v.'</a>';
}
}

$yimao_arr['yimao_orderby']='<a href="'.getqueurl('orderby',geteqval(array($orderby,1,'2','1'))).'" class="'.((strstr($orderby,'1')||strstr($orderby,'2'))?"yimaopaixusel":"yimaopaixu").'">'.geteqval(array($orderby,1,'时间↓','时间↑')).'</a>';

if(is_array($YCF['jjname'])){
foreach ($YCF['jjname'] as $k => $v) {
	$yimao_arr['yimao_color'].='<a href="javascript:;" class="yimaocolor levelcolor'.$k.'">'.$v.'</a>';
}
}



$condition=' where uv038=2 ';

if($quser){
	if(strstr($quser,'-')){
		$qdate=explode('-',$quser);
		if($qdate[0]) $condition.=" and from_unixtime(uv039,'%Y')=".$qdate[0]."";
		if($qdate[1]) $condition.=" and from_unixtime(uv039,'%m')=".$qdate[1]."";
		if($qdate[2]) $condition.=" and from_unixtime(uv039,'%d')=".$qdate[2]."";
	}else{
		$condition.=" and uv002 like '%".$quser."%'";
	}
}


if($option==1) $condition.=" and from_unixtime(uv039,'%Y-%m-%d')='".date('Y-m-d')."'";
if($option>2) $condition.=" and uv006=".($option-3);

$order='';
if($orderby==1) $order=' order by uv039 desc';
if($orderby==2) $order=' order by uv039 asc';


$yimao_arr['yimao_th']='<th><input type="checkbox" onclick="javascript:checkall(\'cbid[]\')"></th><th>序号</th><th>账号</th><th>姓名</th><th>级别</th><th>推荐人</th><th>推荐数</th><th>开通数</th><th>奖金币</th><th>电子币</th>';

$psql="select count(0) from ymuv $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yimao=bdlist&pagecount=$pagecount&orderby=$orderby&option=$option&quser=$quser";	  //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymuv  $condition $order limit $offset,$pagecount";
$db->yiquery($sql);
$ii=0;
foreach ($db->rs as $k => $v) {
	$ii++;
	$serialid=($pagearr[2]-1)*$pagecount+$ii;
	$rm=$member->getum($v['uv002'],'um003,um004');
	$yimao_arr['yimao_tr'].='<tr class="levelcolor'.$rm['um004'].'">
						<td><input type="checkbox" name="cbid[]" value="'.$v['uv001'].'"></td>
						<td><span  title="'.$v["uv001"].'">'.$serialid.'</span></td>
						<td>'.$v['uv002'].'</td>
						<td>'.$rm['um003'].'</td>
						<td>'.getlevel($v['uv006']).'</td>
						<td>'.$member->getuserid($v['uv018']).'</td>
						<td>'.$v['uv016'].'</td>
						<td>'.$member->getkainums($v['uv001']).'</td>
						<td>'.formatrmb($v['uv012']).'</td>
						<td>'.formatrmb($v['uv013']).'</td>
						</tr>';
}


$yimao_arr['yimao_count']=$pcount;
$yimao_arr['yimao_title']=getmenu($YCF['menu'],'bdlist');
$yimao_arr['yimao_page']=$pagearr[0];
yimao_admin_two($yimao_arr);


?>