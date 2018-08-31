<?php
defined('YIMAOMONEY') or exit('Access denied');



if($_POST['act']=='add'){
		$arr['ue006']=htmlspecialchars(trim($_POST['emcontent']));
		$edid=$_POST['edid'];
		if(empty($arr['ue006'])) yimao_automsg('请输入邮件内容',$YCF['curl'],1);

		$db->yiquery("select * from ymue where (ue002='$_username' or ue003='$_username') and (ue008<=2) and ue011=0 and ue001=$edid",2);
		if(empty($db->rs)){
			yimao_automsg('找不到此邮件！',YMCURL.'sjxlist',1);
		}else{
			$ue005=$db->rs["ue005"];
		}


		$arr['ue010']=0;
		$arr['ue004']=time();
		$arr['ue005']="回复";
		$arr['ue006']=$arr['ue006'];
		$arr['ue002']=$_username;
		if($_username==$db->rs['ue002']){
			$arr['ue003']=$db->rs['ue003'];
		}else{
			$arr['ue003']=$db->rs['ue002'];
		}
		
		$arr['ue011']=$edid;

		$sql=getinsertsql($arr,'ymue','ue010,ue004,ue011');
		$db->yiexec($sql);

		yimao_writelog('回复邮件'.$ue005.',id为'.$edid);
		yimao_automsg('回复邮件成功！',YMCURL.'sjxlist');
}

$edid=getnums($_GET['edid'],0);
$kan=getnums($_GET['kan'],0);

$db->yiquery("select * from ymue where (ue002='$_username' or ue003='$_username') and (ue008<=2) and ue011=0 and ue001=$edid",2);
if(empty($db->rs)){
	yimao_automsg('找不到此邮件！',YMCURL.'sjxlist',1);
}
if(empty($db->rs['ue007'])&&$_username==$db->rs['ue003']){
	$db->yiexec('update ymue set ue007=1 where ue001='.$edid);
}

if(!empty($kan)){
	$db->yiexec("update ymue set ue007=1 where ue003='$_username' and  ue011=$edid");
}

echo '<h3>阅读邮件</h3>';
echo '<div class="main-r-body"><form method="post" action="'.geturl().'">
		<div class="main-r-item"><a href="'.YMCURL.'sjxlist" class="yimaoabtn">返回收件箱</a><a href="'.YMCURL.'fjxlist" class="yimaoabtn">返回发件箱</a><a href="'.YMCURL.'sendemail" class="yimaoabtn">返回发送邮件</a></div>
		<input type="hidden" name="act" value="add"><input type="hidden" name="edid" value="'.$db->rs['ue001'].'">';

echo '<table class="yimaoregtab yimaotab mb10">';
echo '	<tr>
			<td class="wdb23 alignr paddingr" valign="top">'.$db->rs['ue002'].' 于 '.formatdate($db->rs['ue004']).' 发送</td>
			<td class="alignl paddingl"><div style="width:550px;word-break:break-all;">'.$db->rs['ue006'].'</div></td>
		</tr>';	
echo '</table>';

$db->yiquery("select * from ymue where ue011=$edid");

foreach ($db->rs as $k => $v) {
echo '<table class="yimaoregtab yimaotab mt10">';
echo '	<tr>
			<td class="wdb23 alignr paddingr" valign="top">'.$v['ue002'].' 于 '.formatdate($v['ue004']).' 回复</td>
			<td class="alignl paddingl"><div style="width:550px;word-break:break-all;">'.$v['ue006'].'</div></td>
		</tr>';	
echo '</table>';
}



echo '<div style="line-height:1px;height:1px;border-bottom:1px dotted #535548;margin:10px 0px;"></div>';

echo '<table class="yimaoregtab yimaotab mb10">';
echo '	<tr>
			<td class="wdb23 alignr paddingr" valign="top">回复内容</td>
			<td class="alignl paddingl"><textarea cols="150" rows="6" name="emcontent" class="yimaotextarea" maxlength="450"></textarea></td>
		</tr>';	
echo '	<tr>
			<td class="wdb23 alignr paddingr" valign="top"></td>
			<td class="alignl paddingl"><input type="submit" class="yimaoabtn" value="回复"></td>
		</tr>';			
echo '</table>';

echo '</form></div>';		


?>