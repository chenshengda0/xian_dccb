<?php defined('YIMAOMONEY') or exit('Access denied');?>
<?php

if(!isset($veid)){
?>
  <div class="right_center">>&nbsp;公司新闻</div>

  <div class="indent-r-top">新闻动态</div>


<div style="margin-top:20px;margin-left:10px">
<?php

$pagecount=10;
$condition=" where un007=0 and un004=0";
$psql="select count(0) from ymun $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yim=newslist";    //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymun $condition order by un006 desc,un003 desc limit $offset,$pagecount";
$db->yiquery($sql);
$rs=$db->rs;
$ii=0;
foreach ($rs as $k => $v) {
  $ii++;
  $serialid=($pagearr[2]-1)*$pagecount+$ii;
?>
                <ul class="ulff ufh35">
                    <li style="width:80%"><span style="color:#ccc;font-size:20px">·</span><span style="color:#E1631A;font-size: 15px"></span><a href="<?php echo YMINDEX.'?yim=newsview&veid='.$v['un001']?>"><?php echo $v['un002'];?></a><li>
                    <li style="width:20%;"><span class="grayc"><?php echo formatdate($v['un003'],1)?></span><li>
                </ul>
<?php 
}
?>
</div>
<?php if(($pcount/$pagecount)>1){?>
<div class="pagediv" style="padding-top:20px;margin:0px 0px 20px 0px;clear:both;"><?php echo $pagearr[0]?></div>
<?php }?>
<?php if($pcount==0){?>
<div class="pagediv" style="text-align:center;color:#E47501">--> 暂无新闻</div><br>
<?php }?>
<?php 
}else{ 
?>

<?php
}
?>