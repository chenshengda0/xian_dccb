<?php
defined('YIMAOMONEY') or exit('Access denied');

$pagecount=getnums($_GET["pagecount"],$YCF['pagecount']);
$option=getnums($_GET['option'],1);
$orderby=getnums($_GET['orderby'],1);
$quser=trim($_GET['quser']);
$veid=getnums(trim($_GET['veid']),0);
$vedate=trim($_GET['vedate']);

if(empty($veid)) yimao_automsg('操作失败!',YMCURL.'prizebobi',1);

$yimao_arr['yimao_option'].='<a href="'.YMCURL.'prizebobi" class="yimaoabtn">返回奖金拨比</a><a href="'.getqueurl('option',1).'" class="'.geteqval(array($option,1,'yimaoaselbtn','yimaoabtn')).'">本期全部</a>';

if(is_array($YCF['jjname'])){
foreach ($YCF['jjname'] as $k => $v) {
	$yimao_arr['yimao_option'].='<a href="'.getqueurl('option',$k+2).'" class="'.geteqval(array($option,$k+2,'yimaoaselbtn','yimaoabtn')).'">'.$v.'</a>';
}
}


$yimao_arr['yimao_orderby']='<a href="'.getqueurl('orderby',geteqval(array($orderby,1,'2','1'))).'" class="'.((strstr($orderby,'1')||strstr($orderby,'2'))?"yimaopaixusel":"yimaopaixu").'">'.geteqval(array($orderby,1,'时间↑','时间↓')).'</a>';

if(is_array($YCF['jjname'])){
foreach ($YCF['jjname'] as $k => $v) {
	$yimao_arr['yimao_color'].='<a href="javascript:;" class="yimaocolor levelcolor'.$k.'">'.$v.'</a>';
}
}

$condition=' where uv009=0 and uv043='.$veid;

if($quser){
	if(strstr($quser,'-')){
		$qdate=explode('-',$quser);
		if($qdate[0]) $condition.=" and from_unixtime(uv003,'%Y')=".$qdate[0]."";
		if($qdate[1]) $condition.=" and from_unixtime(uv003,'%m')=".$qdate[1]."";
		if($qdate[2]) $condition.=" and from_unixtime(uv003,'%d')=".$qdate[2]."";
	}else{
		$condition.=" and uv002 like '%".$quser."%'";
	}
}

if($option>1) $condition.=' and  uv007='.($option-2);


$order='';
if($orderby==1) $order=' order by uv003 asc';
if($orderby==2) $order=' order by uv003 desc';


$yimao_arr['yimao_th']='<th>序号</th><th>会员账号</th><th>姓名</th>';
if(is_array($YCF['jjname'])){
$yimao_arr['yimao_th'].='<th>注册级别</th>';
}
$yimao_arr['yimao_th'].='<th>注册人</th><th>开通人</th><th>注册时间</th>';	

$psql="select count(0) from ymuv $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yimao=benqiuser&pagecount=$pagecount&orderby=$orderby&option=$option&quser=$quser&veid=$veid&vedate=$vedate";	  //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymuv  $condition $order limit $offset,$pagecount";
$db->yiquery($sql);
$ii=0;
foreach ($db->rs as $k => $v) {
	$ii++;
	$serialid=($pagearr[2]-1)*$pagecount+$ii;
	$rm=$member->getum($v['uv002'],'um003');
	$yimao_arr['yimao_tr'].='<tr class="levelcolor'.$v['uv007'].'">
						<td><span  title="'.$v["uv001"].'">'.$serialid.'</span></td>
						<td>'.$v['uv002'].'</td>
						<td>'.$rm['um003'].'</td>';
if(is_array($YCF['jjname'])){						
	$yimao_arr['yimao_tr'].='<td>'.getlevel($v['uv007']).'</td>';
}						
	$yimao_arr['yimao_tr'].='<td>'.$member->getreguser($v['uv036']).'</td>		
						<td>'.$member->getreguser($v['uv037']).'</td>		
						<td>'.formatdate($v['uv003']).'</td>	
						</tr>';
}


$yimao_arr['yimao_count']=$pcount;
$yimao_arr['yimao_title']=formatdate($vedate,1)."期 注册用户";
$yimao_arr['yimao_page']=$pagearr[0];
yimao_admin_two($yimao_arr);


?>