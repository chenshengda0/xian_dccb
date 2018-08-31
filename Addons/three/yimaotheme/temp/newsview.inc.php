<?php defined('YIMAOMONEY') or exit('Access denied');?>
<?php
$veid=getnums($_GET['veid'],0);
if(empty($veid)){historygo();}

$db->yiquery("select * from ymun where un007=0 and un001=".$veid,2);
if(empty($db->rs)){historygo();}

if($_SERVER['HTTP_REFERER']){
  $db->yiexec("update ymun set un009=un009+1 where un007=0 and un001=".$veid);
}
?>
  <div class="right_center">>&nbsp;新闻内容</div>

  <div class="indent-r-top">新闻动态</div>
            <div class="bt"> <?php echo $db->rs['un002']?></div>
            <div style="height:35px;line-height:35px;font-size:13px;color:#aaa;margin-bottom:10px;margin-left:15px">发布时间:<?php echo formatdate($db->rs['un003'])?> &nbsp;&nbsp;&nbsp;&nbsp;发布人：<?php echo $db->rs['un008']?></div>
            <div style="margin:0px 15px;"><?php echo htmlspecialchars_decode($db->rs['un005'])?></div>
             <div style="margin:20px 15px"><span onclick="history.go(-1);" style="cursor:pointer;color:#E1631A;">[ 返回 ]</span></div>