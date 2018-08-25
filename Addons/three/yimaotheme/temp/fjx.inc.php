<?php defined('YIMAOMONEY') or exit('Access denied');?>

  <div class="right_center">>&nbsp;发件箱</div>

  <div class="indent-r-top">联系我们</div>
<?php
$deid=getnums($_GET['deid'],0);
if(!empty($deid)){

    $db->yiquery("select * from ymue where ue002='$_username' and ue011=0 and (ue008=0 or ue008=2) and ue001=$deid",2);
    if(empty($db->rs)){
        msg_b('邮件不存在！');
    }

    if($db->rs['ue008']==2){
        $db->yiexec('update ymue set ue008=3 where ue001='.$deid);
    }else{
        $db->yiexec('update ymue set ue008=1 where ue001='.$deid);
    }

    msg_l("o_O ~ 删除邮件成功 ",YMINDEX."?yim=fjx");
}
?>
<div style="margin:20px 0px 20px 10px">
    <button onclick="window.location='<?php  echo YMINDEX?>?yim=sjx'" class="btt">收件箱</button>&nbsp;<button onclick="window.location='<?php  echo YMINDEX?>?yim=fjx'" class="btt"  style="color:#65919C">发件箱</button>&nbsp;<button onclick="window.location='<?php  echo YMINDEX?>?yim=liuyan'" class="btt">留言反馈</button></div>
<table width="100%" border="0" cellpadding="0" cellspacing="1" style="margin-top:5px;" class="biaotab" align="center">
  <tbody>
    <tr>
        <th>序号</th>     
        <th valign="top">收件人</th>
        <th valign="top">留言标题</th>
        <th valign="top">留言时间</th>
        <th valign="top">状态</th>
        <th valign="top">查看</th>
    </tr>
<?php

$pagecount=10;
$condition="  where ue011=0  and ue002='$_username' and (ue008=0 or ue008=2)";
$psql="select count(0) from ymue $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yim=fjx";    //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymue $condition order by ue001 desc limit $offset,$pagecount";
$db->yiquery($sql);
$rs=$db->rs;
$ii=0;
foreach ($rs as $k => $v) {
  $ii++;
  $serialid=($pagearr[2]-1)*$pagecount+$ii;
  $db->yiquery("select count(0) from ymue where ue007=0 and ue003='$_username' and ue011=".$v['ue001'],3);
  $yuehf1=$yuehf=$db->fval;  
?>
    <tr>
        <td ><?php echo $serialid?></td>  
        <td valign="top"><?php echo $v['ue003']?></td> 
        <td valign="top" style="text-align:left">&nbsp;<?php echo $v['ue005']?></td> 
        <td valign="top"><?php echo formatdate($v['ue004'])?></td> 
        <td valign="top"><?php echo getstatutype($v['ue007'],5)?></td> 
        <td valign="top"><a href="<?php  echo YMINDEX?>?yim=yjx&veid=<?php echo $v['ue001']?>&kan=<?php echo $yuehf1?>" class="viewkan">查看</a>&nbsp;<a href="<?php echo $YCF['curl']?>&deid=<?php echo $v['ue001']?>" class="viewkan" onclick="return confirm('您确定要删除吗?')">删除</a></td> 
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