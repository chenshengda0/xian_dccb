<?php
defined('YIMAOMONEY') or exit('Access denied');



$pagecount=getnums($_GET["pagecount"],$YCF['pagecount']);
$option=getnums($_GET['option'],1);
$orderby=getnums($_GET['orderby'],1);
$quser=trim($_GET['quser']);

$yimao_arr['yimao_option'].='<a href="'.getqueurl('option',1).'" class="'.geteqval(array($option,1,'yimaoaselbtn','yimaoabtn')).'">今日新增</a><a href="'.getqueurl('option',2).'" class="'.geteqval(array($option,2,'yimaoaselbtn','yimaoabtn')).'">全部</a>';


$yimao_arr['yimao_orderby']='<a href="'.getqueurl('orderby',geteqval(array($orderby,1,'2','1'))).'" class="'.((strstr($orderby,'1')||strstr($orderby,'2'))?"yimaopaixusel":"yimaopaixu").'">'.geteqval(array($orderby,1,'时间↑','时间↓')).'</a>';


if(is_array($YCF['jjname'])){
foreach ($YCF['jjname'] as $k => $v) {
	$yimao_arr['yimao_color'].='<a href="javascript:;" class="yimaocolor levelcolor'.$k.'">'.$v.'</a>';
}
}



$condition=' where 1=1 ';

if($quser){
	if(strstr($quser,'-')){
		$qdate=explode('-',$quser);
		if($qdate[0]) $condition.=" and from_unixtime(uw002,'%Y')=".$qdate[0]."";
		if($qdate[1]) $condition.=" and from_unixtime(uw002,'%m')=".$qdate[1]."";
		if($qdate[2]) $condition.=" and from_unixtime(uw002,'%d')=".$qdate[2]."";
	}else{
		$condition.=" and uw001 like '%".$quser."%'";
	}
}
if($option==1) $condition.=" and from_unixtime(uw002,'%Y-%m-%d')='".date('Y-m-d')."'";

$order='';
if($orderby==1) $order=' order by uw002 asc';
if($orderby==2) $order=' order by uw002 desc';


$yimao_arr['yimao_th']='<th>序号</th><th>会员账号</th><th>姓名</th>';
if(is_array($YCF['jjname'])){
$yimao_arr['yimao_th'].='<th>级别</th>';
}
foreach ($YCF['prizename'] as $k => $v) {
$yimao_arr['yimao_th'].="<th>$v</th>";
}
$yimao_arr['yimao_th'].='<th>详细</th>';
$psql="select count(0) from ymuw $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yimao=prizetotal&pagecount=$pagecount&orderby=$orderby&option=$option&quser=$quser";	  //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymuw  $condition $order limit $offset,$pagecount";
$db->yiquery($sql);
$ii=0;
foreach ($db->rs as $k => $v) {
	$ii++;
	$serialid=($pagearr[2]-1)*$pagecount+$ii;
	$rm=$member->getuserinfo($v['uw001'],1);
	$yimao_arr['yimao_tr'].='<tr class="levelcolor'.$rm['uv006'].'">
						<td><span  title="'.$rm["uv001"].'">'.$serialid.'</span></td>
						<td>'.$v['uw001'].'</td>
						<td>'.$rm['um003'].'</td>';
if(is_array($YCF['jjname'])){						
	$yimao_arr['yimao_tr'].='<td>'.getlevel($rm['uv006']).'</td>';
}						
foreach ($YCF['prizename'] as $kk => $vv) {
	$yimao_arr['yimao_tr'].="<td>".formatrmb($v['uw'.$YCF['prizeval'][$kk]])."</td>";
}				
	$yimao_arr['yimao_tr'].='<td><a href="'.YMCURL.'prizemeiqi&quser='.$rm['um002'].'">查看</a></td></tr>';	
	

}


$yimao_arr['yimao_count']=$pcount;
$yimao_arr['yimao_title']=getmenu($YCF['menu'],'prizetotal');
$yimao_arr['yimao_page']=$pagearr[0];
yimao_admin_two($yimao_arr);


?>