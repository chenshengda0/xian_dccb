<?php
defined('YIMAOMONEY') or exit('Access denied');



$edid=getnums($_GET['edid'],0);

$db->yiquery("select * from ymue where ue011=0 and ue001=$edid",2);
if(empty($db->rs)){
	yimao_automsg('找不到此邮件！',YMCURL.'sjxlist',1);
}


echo '<h3>阅读邮件</h3>';
echo '<div class="main-r-body"><form method="post" action="'.geturl().'">
		<div class="main-r-item"><a href="'.YMCURL.'sjxlist" class="yimaoabtn">返回收件箱</a><a href="'.YMCURL.'fjxlist" class="yimaoabtn">返回发件箱</a><a href="'.YMCURL.'sendemail" class="yimaoabtn">返回发送邮件</a></div>
		<input type="hidden" name="act" value="add"><input type="hidden" name="edid" value="'.$db->rs['ue001'].'">';

echo '<table class="yimaoregtab yimaotab mb10">';
echo '	<tr>
			<td class="wdb23 alignr paddingr" valign="top">'.$db->rs['ue002'].' 于 '.formatdate($db->rs['ue004']).' 发送</td>
			<td class="alignl paddingl">'.$db->rs['ue006'].'</td>
		</tr>';	
echo '</table>';

$db->yiquery("select * from ymue where ue011=$edid");

foreach ($db->rs as $k => $v) {
echo '<table class="yimaoregtab yimaotab mt10">';
echo '	<tr>
			<td class="wdb23 alignr paddingr" valign="top">'.$v['ue002'].' 于 '.formatdate($v['ue004']).' 回复</td>
			<td class="alignl paddingl">'.$v['ue006'].'</td>
		</tr>';	
echo '</table>';
}

echo '</form></div>';		


?>