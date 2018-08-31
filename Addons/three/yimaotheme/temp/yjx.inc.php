<?php defined('YIMAOMONEY') or exit('Access denied');?>


<?php
$veid=getnums($_GET['veid'],0);
$kan=getnums($_GET['kan'],0);

$db->yiquery("select * from ymue where (ue002='$_username' or ue003='$_username') and (ue008<=2) and ue011=0 and ue001=$veid",2);
if(empty($db->rs)){
    msg_b('找不到此邮件！');
}
if(empty($db->rs['ue007'])&&$_username==$db->rs['ue003']){
    $db->yiexec('update ymue set ue007=1 where ue001='.$veid);
}

if(!empty($kan)){
    $db->yiexec("update ymue set ue007=1 where ue003='$_username' and  ue011=$veid");
}

?>

  <div class="right_center">>&nbsp;阅件箱</div>

  <div class="indent-r-top">联系我们</div>
  

<div style="margin:20px 0px 20px 10px">
    <button onclick="window.location='<?php  echo YMINDEX?>?yim=sjx'" class="btt" >收件箱</button>&nbsp;<button onclick="window.location='<?php  echo YMINDEX?>?yim=fjx'" class="btt">发件箱</button>&nbsp;<button onclick="window.location='<?php  echo YMINDEX?>?yim=liuyan'" class="btt">留言反馈</button></div>
<?php
echo '<div style="margin:20px 0px 0px 10px">邮件标题：'.$db->rs['ue005']."</div>";
echo '<table class="baitab" style="border:1px solid #ccc;margin:20px 0px 0px 10px" align="center">';
echo '  <tr>
            <td class="wdb20 alignr paddingr" valign="top">'.$db->rs['ue002'].' 于 '.formatdate($db->rs['ue004']).' 发送:</td>
            <td class="alignl paddingl"><div style="width:550px;word-break:break-all;">'.$db->rs['ue006'].'</div></td>
        </tr>'; 
echo '</table>';

$db->yiquery("select * from ymue where ue011=$veid");

foreach ($db->rs as $k => $v) {
echo '<table  class="baitab" style="border:1px solid #ccc;margin-top:10px" align="center">';
echo '  <tr>
            <td class="wdb20 alignr paddingr" valign="top">'.$v['ue002'].' 于 '.formatdate($v['ue004']).' 回复:</td>
            <td class="alignl paddingl"><div style="width:550px;word-break:break-all;">'.$v['ue006'].'</div></td>
        </tr>'; 
echo '</table>';
}
echo '<form method="post" action="control.php" onsubmit="return chkhuifu()"><input type="hidden" name="act" value="huifu"><input type="hidden" name="edid" value="'.$veid.'"><input type="hidden" name="xxurl" value="'.geturl().'"><table class="baitab" style="border:1px solid #ccc;margin:20px 0px 0px 10px" align="center">';
echo '  <tr>
            <td class="wdb10 alignr paddingr" valign="top">回复内容</td>
            <td class="alignl paddingl"><textarea  rows="6" name="emcontent" id="emcontent" class="yimaotextarea" style="margin-top:5px;outline:0px;width:98%"  maxlength="450"></textarea></td>
        </tr>'; 
echo '  <tr>
            <td class="wdb10 alignr paddingr" valign="top"></td>
            <td class="alignl paddingl"><input type="submit" class="btt" value="回复"></td>
        </tr>';         
echo '</table></form>';

?>