<?php
defined('YIMAOMONEY') or exit('Access denied');

$pagecount=getnums($_GET["pagecount"],$YCF['pagecount']);
$option=getnums($_GET['option'],1);
$orderby=getnums($_GET['orderby'],1);
$quser=trim($_GET['quser']);
$veid=getnums(trim($_GET['veid']),0);
$vedate=trim($_GET['vedate']);

if(empty($veid)) yimao_automsg('操作失败!',YMCURL.'prizebobi',1);


$yimao_arr['yimao_orderby']='<a href="'.getqueurl('orderby',geteqval(array($orderby,1,'2','1'))).'" class="'.((strstr($orderby,'1')||strstr($orderby,'2'))?"yimaopaixusel":"yimaopaixu").'">'.geteqval(array($orderby,1,'时间↑','时间↓')).'</a>';

$yimao_jjth='';

foreach ($YCF['prizename'] as $k => $v) {
$yimao_jjth.="<th>$v</th>";
}


if(is_array($YCF['jjname'])){
foreach ($YCF['jjname'] as $k => $v) {
	$yimao_arr['yimao_color'].='<a href="javascript:;" class="yimaocolor levelcolor'.$k.'">'.$v.'</a>';
}
}

$sql="select sum(up050) as up050,sum(up051) as up051,sum(up052) as up052,sum(up053) as up053,sum(up054) as up054,sum(up055) as up055,sum(up056) as up056,sum(up057) as up057,sum(up058) as up058,sum(up059) as up059,sum(up060) as up060,sum(up061) as up061,sum(up062) as up062,sum(up063) as up063 from ymup where up004=$veid";
$db->yiquery($sql,2);
$brow=$db->rs;
$sql="select sum(up050) as up050,sum(up051) as up051,sum(up052) as up052,sum(up053) as up053,sum(up054) as up054,sum(up055) as up055,sum(up056) as up056,sum(up057) as up057,sum(up058) as up058,sum(up059) as up059,sum(up060) as up060,sum(up061) as up061,sum(up062) as up062,sum(up063) as up063 from ymup";
$db->yiquery($sql,2);
$arow=$db->rs;

	$yimao_arr['yimao_option']='<table class="yimaolist yimaotab">
		<tr>
		<th>奖金汇总</th>'.$yimao_jjth.'
		</tr>
		<tbody>
		<tr>
			<td>本期奖金</td>';
foreach ($YCF['prizename'] as $k => $v) {
$yimao_arr['yimao_option'].="<td>".$brow['up'.$YCF['prizeval'][$k]]."</td>";
}

	$yimao_arr['yimao_option'].='</tr>
		<tr>
			<td>奖金总和</td>';
foreach ($YCF['prizename'] as $k => $v) {
$yimao_arr['yimao_option'].="<td>".$arow['up'.$YCF['prizeval'][$k]]."</td>";
}

	$yimao_arr['yimao_option'].='</tr>	
		</tbody>					
		</table>';

$condition=' where up004='.$veid;

if($quser){
	$sql="select um001 from ymum where um002 like '%".$quser."%'";
	$db->yiquery($sql);
	$sarr=array();
	foreach ($db->rs as $k => $v) {
		$sarr[]=$v['um001'];
	}
	if(!empty($sarr)){
		$s=implode(',',$sarr);
		$condition.=" and up002 in($s)";
	}
	
}



$order='';
if($orderby==1) $order=' order by up001 asc';
if($orderby==2) $order=' order by up001 desc';


$yimao_arr['yimao_th']='<th>序号</th><th>会员账号</th><th>姓名</th>';
if(is_array($YCF['jjname'])){	
$yimao_arr['yimao_th'].='<th>级别</th>';
}
$yimao_arr['yimao_th'].=$yimao_jjth.'<th>详细</th>';

$psql="select count(0) from ymup $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yimao=benqiprize&pagecount=$pagecount&orderby=$orderby&quser=$quser&veid=$veid&vedate=$vedate";	  //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymup  $condition $order limit $offset,$pagecount";
$db->yiquery($sql);
$ii=0;
foreach ($db->rs as $k => $v) {
	$ii++;
	$serialid=($pagearr[2]-1)*$pagecount+$ii;
	$rm=$member->getuserinfo($v['up002']);
	$yimao_arr['yimao_tr'].='<tr class="levelcolor'.$rm['uv006'].'">
						<td><span  title="'.$v["up001"].'">'.$serialid.'</span></td>
						<td>'.$rm['uv002'].'</td>
						<td>'.$rm['um003'].'</td>';
if(is_array($YCF['jjname'])){							
	$yimao_arr['yimao_tr'].='<td>'.getlevel($rm['uv006']).'</td>';
}

foreach ($YCF['prizename'] as $kk => $vv) {
	$yimao_arr['yimao_tr'].="<td>".formatrmb($v['up'.$YCF['prizeval'][$kk]])."</td>";
}	
	$yimao_arr['yimao_tr'].='<td><a href="'.YMCURL.'xxprize&quser='.$rm['uv002'].'&veid='.$rm['uv001'].'&vedate='.$v['up004'].'&bkid='.$veid.'&bkdate='.$vedate.'" class="bluec">查看</a></td></tr>';
}


$yimao_arr['yimao_count']=$pcount;
$yimao_arr['yimao_title']=formatdate($vedate,1)."期 奖金列表";
$yimao_arr['yimao_page']=$pagearr[0];
yimao_admin_seven($yimao_arr);

?>