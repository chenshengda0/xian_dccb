<?php defined('YIMAOMONEY') or exit('Access denied');?>
<?php

if(!isset($_GET["veid"])){
?>
  <div class="right_center">>&nbsp;奖金明细</div>

  <div class="indent-r-top">财务管理</div>
<table width="100%" border="0" cellpadding="0" cellspacing="1" style="margin-top:5px;" class="biaotab" align="center">
  <tbody>
    <tr>
        <th>序号</th>     
        <th valign="top">日期</th>
<?php
foreach ($YCF['prizename'] as $k => $v) {
echo '<th valign="top">'.$v.'</th>';
}
?>
        <th valign="top">详细</th>
    </tr>
<?php

$pagecount=10;
$condition=" where up002=$_userid";
$psql="select count(0) from ymup $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yim=prizelist";    //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymup $condition order by up001 desc limit $offset,$pagecount";
$db->yiquery($sql);
$rs=$db->rs;
$ii=0;
foreach ($rs as $k => $v) {
  $ii++;
  $serialid=($pagearr[2]-1)*$pagecount+$ii;
?>
    <tr>
        <td ><?php echo $serialid?></td>     
        <td valign="top"><?php echo formatdate($v['up005'],1)?></td>
<?php
foreach ($YCF['prizename'] as $kk => $vv) {
    echo "<td>".formatrmb($v['up'.$YCF['prizeval'][$kk]])."</td>";
}   
?>
        <td ><a href="<?php echo $YCF['curl']?>&veid=<?php echo $v['up004']?>" class="viewkan">查看</a></td>     
    </tr>
<?php
}
?>    
  </tbody>
</table>
<?php if(($pcount/$pagecount)>1){?>
<div class="pagediv"><?php echo $pagearr[0]?></div>
<?php }?>
<?php if($pcount==0){?>
<div class="pagediv" style="text-align:center;color:#E47501">--> 暂无数据</div>
<?php }?>
<?php 
}else{ 
?>

  <div class="right_center">>&nbsp;奖金详细  </div>

  <div class="indent-r-top">财务管理</div>

<div class="bt"><div style="float:right;padding:0px 5px;"><a href="<?php echo $YCF['curl']?>"  class="lbtn">返回 >></a></div></div>


<table width="100%" border="0" cellpadding="0" cellspacing="1" style="margin-top:5px;" class="biaotab" align="center">
  <tbody>
    <tr>
        <th>序号</th>     
        <th valign="top" style="width:160px">时间</th>
        <th valign="top">奖项</th>
        <th valign="top">金额</th>
        <th valign="top">备注</th>
    </tr>
<?php
$veid=getnums($_GET['veid'],0);

if(empty($veid)) locationurl($YCF['curl']);


$pagecount=10;
$condition=" where ud002=$_userid and ud003=$veid";
$psql="select count(0) from ymud $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yim=prizelist&veid=$veid";    //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymud $condition order by ud001 desc limit $offset,$pagecount";
$db->yiquery($sql);
$rs=$db->rs;
$ii=0;
foreach ($rs as $k => $v) {
  $ii++;
  $serialid=($pagearr[2]-1)*$pagecount+$ii;

?>
    <tr>
        <td ><?php echo $serialid?></td>     
        <td valign="top"><?php echo formatdate($v['ud004'])?></td>
        <td valign="top"><?php echo getjjtype($v['ud005'])?></td>
        <td valign="top"><?php echo formatrmb($v['ud006'])?></td>
        <td valign="top" style="text-align:left">&nbsp;<?php echo $v['ud007']?></td>
    </tr>
<?php
}
?>    
  </tbody>
</table>
<?php if(($pcount/$pagecount)>1){?>
<div class="pagediv"><?php echo $pagearr[0]?></div>
<?php }?>
<?php if($pcount==0){?>
<div class="pagediv" style="text-align:center;color:#E47501">--> 暂无数据</div>
<?php }?>
<?php
}
?>