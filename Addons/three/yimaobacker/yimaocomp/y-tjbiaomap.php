<?php
defined('YIMAOMONEY') or exit('Access denied');

$pagecount=getnums($_GET["pagecount"],$YCF['pagecount']);
$options=getnums($_GET['options'],1);
$orderby=getnums($_GET['orderby'],1);
$quser=trim($_GET['quser']);


$yimao_arr['yimao_option'].='<a href="'.getqueurl('options',1).'" class="'.geteqval(array($options,1,'yimaoaselbtn','yimaoabtn')).'">所有推荐</a>';

if(is_array($YCF['jjname'])){
foreach ($YCF['jjname'] as $k => $v) {
	$yimao_arr['yimao_option'].='<a href="'.getqueurl('options',$k+2).'" class="'.geteqval(array($options,$k+2,'yimaoaselbtn','yimaoabtn')).'">'.$v.'</a>';
}
}

$yimao_arr['yimao_orderby']='<a href="'.getqueurl('orderby',geteqval(array($orderby,1,'2','1'))).'" class="'.((strstr($orderby,'1')||strstr($orderby,'2'))?"yimaopaixusel":"yimaopaixu").'">'.geteqval(array($orderby,1,'时间↓','时间↑')).'</a>';


$yimao_arr['yimao_color']='<a href="javascript:;" class="yimaocolor statuscolor0">未正式</a><a href="javascript:;" class="yimaocolor statuscolor1">已正式</a><a href="javascript:;" class="yimaocolor statuscolor2">空单</a>';



$qid=getnums($_GET["qid"],$_houid);

$quser=trim($_POST["quser"]);
if(!empty($quser)){
  $r=$member->user_exists($quser);
  if(empty($r)){
  	$condition=' where uv018='.$qid;
  }else{
 	 $qid=$r['uv001'];
  	 $condition=' where uv001='.$qid;

	if(!strstr($r['uv020'],",".$_houid.",") && $r["uv001"]!=$_houid){
	   yimao_automsg('查询失败!',$YCF['curl'],1,3,0);
	}
  }
}else{
	$condition=' where uv018='.$qid;
}

if($options>1) $condition.=" and  uv006=".($options-2);


$order='';
if($orderby==1) $order=' order by uv003 desc';
if($orderby==2) $order=' order by uv003 asc';


$yimao_arr['yimao_th']='<th>序号</th><th>会员账号</th><th>姓名</th>';
if(is_array($YCF['jjname'])){	
$yimao_arr['yimao_th'].='<th>级别</th>';
}
$yimao_arr['yimao_th'].='<th>推荐数</th><th>推荐人</th>';

if($YCF['regan']) $yimao_arr['yimao_th'].='<th>安置人</th>';
if($YCF['regbd']) $yimao_arr['yimao_th'].='<th>服务中心</th>';

$yimao_arr['yimao_th'].='<th>开通时间</th>';

$psql="select count(0) from ymuv $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yimao=networkmap&pagecount=$pagecount&orderby=$orderby&options=$options&option=$option&quser=$quser";	  //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymuv  $condition $order limit $offset,$pagecount";
$db->yiquery($sql);
$ii=0;
foreach ($db->rs as $k => $v) {

	$ii++;
	$serialid=($pagearr[2]-1)*$pagecount+$ii;
	$yimao_arr['yimao_tr'].='<tr class="statuscolor'.$v['uv008'].'">
						<td><span  title="'.$v["uv001"].'">'.$serialid.'</span></td>
						<td><a href="'.$YCF['curl'].'&option=3&qid='.$v['uv001'].'">'.$v['uv002'].'</a></td>
						<td>'.$member->getusername($v['uv001']).'</td>';
if(is_array($YCF['jjname'])){						
$yimao_arr['yimao_tr'].='<td>'.getlevel($v['uv006']).'</td>';
}						
$yimao_arr['yimao_tr'].='<td>'.$v['uv016'].'</td>	
						<td>'.$member->getuserid($v['uv018']).'</td>';
if($YCF['regan']) $yimao_arr['yimao_tr'].='<td>'.(($anid=$member->getuserid($v['uv019']))?$anid.'&nbsp;'.$YCF['anrea'][$v['uv024']]:'').'</td>';
if($YCF['regbd']) $yimao_arr['yimao_tr'].='<td>'.$member->getuserid($v['uv017']).'</td>';
	$yimao_arr['yimao_tr'].='<td>'.formatdate($v['uv004']).'</td>	
						</tr>';
}


$yimao_arr['yimao_count']=$pcount;
$yimao_arr['yimao_title']=getmenu($YCF['menu'],'articlelist');
$yimao_arr['yimao_page']=$pagearr[0];
yimao_admin_four($yimao_arr);

?>