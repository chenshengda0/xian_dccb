<?php
defined('YIMAOMONEY') or exit('Access denied');



$pagecount=getnums($_GET["pagecount"],$YCF['pagecount']);
$option=getnums($_GET['option'],1);
$orderby=getnums($_GET['orderby'],1);
$quser=trim($_GET['quser']);
$veid=getnums(trim($_GET['veid']),0);
$vedate=trim($_GET['vedate']);
$bkid=getnums(trim($_GET['bkid']),0);
$bkdate=trim($_GET['bkdate']);

if(empty($veid)) yimao_automsg('操作失败!',YMCURL.'prizebobi',1);

$yimao_arr['yimao_option'].='<a href="'.YMCURL.'benqiprize&veid='.$bkid.'&vedate='.$bkdate.'" class="yimaoabtn">返回奖金列表</a><a href="'.getqueurl('option',1).'" class="'.geteqval(array($option,1,'yimaoaselbtn','yimaoabtn')).'">全部</a>';

foreach ($YCF['prizename'] as $k => $v) {
	if($k<(count($YCF['prizename'])-3))
	$yimao_arr['yimao_option'].='<a href="'.getqueurl('option',$YCF['prizeval'][$k]).'" class="'.geteqval(array($option,$YCF['prizeval'][$k],'yimaoaselbtn','yimaoabtn')).'">'.$v.'</a>';
}


$condition=" where ud002=$veid and ud003=$vedate";

if($option>1) $condition.=' and  ud005='.$option;

$order=" order by ud001 desc";

$yimao_arr['yimao_th']='<th>序号</th><th style="width:180px">时间</th><th>类型</th><th>金额</th><th>备注</th>';

$psql="select count(0) from ymud $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yimao=xxprize&pagecount=$pagecount&orderby=$orderby&option=$option&quser=$quser&veid=$veid&vedate=$vedate&bkid=$bkid&bkdate=$bkdate";	  //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymud  $condition $order limit $offset,$pagecount";
$db->yiquery($sql);
$ii=0;
$zong=0;
foreach ($db->rs as $k => $v) {
	$ii++;
	$zong+=$v['ud006'];
	$serialid=($pagearr[2]-1)*$pagecount+$ii;
	$yimao_arr['yimao_tr'].='<tr>
						<td><span  title="'.$v["ud001"].'">'.$serialid.'</span></td>
						<td>'.formatdate($v['ud004']).'</td>
						<td>'.getjjtype($v['ud005']).'</td>
						<td>'.$v['ud006'].'</td>
						<td align="left">&nbsp;'.$v['ud007'].'</td>
						</tr>';	
	

}
	$yimao_arr['yimao_tr'].='<tr>
						<td>统计</td>
						<td></td>
						<td></td>
						<td>'.$zong.'</td>
						<td></td>
						</tr>';	
	

$yimao_arr['yimao_count']=$pcount;
$yimao_arr['yimao_title']=formatdate($bkdate,1)."期 奖金详细 —— ".$quser.'会员';
$yimao_arr['yimao_page']=$pagearr[0];
yimao_admin_eight($yimao_arr);


?>