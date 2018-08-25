<?php
defined('YIMAOMONEY') or exit('Access denied');

$veid=getnums($_GET['veid'],0);
if(!empty($veid)){
	$db->yiquery('select * from ymun where un001='.$veid,2);
	if(empty($db->rs)){
		yimao_automsg('文章不存在！',$YCF['curl'],1);
	}

	echo '<h3>文章阅读</h3>';
	echo '<div class="main-r-body"><div class="artview">
		标题：'.$db->rs['un002'].'</div>'.htmlspecialchars_decode($db->rs['un005']).'
	</div><a href="javascript:history.go(-1)" class="artreturn">[ 返回 ]</a>';
		exit;
}

$db->yiquery("select count(0) as a from ymuv where from_unixtime(uv003,'%Y-%m-%d')='".date('Y-m-d')."' and uv001>1",2);
$newuser=$db->rs['a'];
$db->yiquery("select count(0) as a from ymuv where uv008=0",2);
$okuser=$db->rs['a'];

$db->yiquery("select count(0) as a from ymug where from_unixtime(ug003,'%Y-%m-%d')='".date('Y-m-d')."'",2);
$newrech=$db->rs['a'];
$db->yiquery("select count(0) as a from ymug where ug008=0",2);
$okrech=$db->rs['a'];

$db->yiquery("select count(0) as a from ymui where from_unixtime(ui003,'%Y-%m-%d')='".date('Y-m-d')."'",2);
$newtiqu=$db->rs['a'];
$db->yiquery("select count(0) as a from ymui where ui007=0",2);
$oktiqu=$db->rs['a'];

$db->yiquery("select count(0) as a from ymuz where from_unixtime(uz004,'%Y-%m-%d')='".date('Y-m-d')."'",2);
$newzhuan=$db->rs['a'];
$db->yiquery("select count(0) as a from ymuj where from_unixtime(uj003,'%Y-%m-%d')='".date('Y-m-d')."'",2);
$newhuan=$db->rs['a'];

$db->yiquery("select count(0) as a from ymum where from_unixtime(um026,'%Y-%m-%d')='".date('Y-m-d')."'",2);
$newlogin=$db->rs['a'];
$db->yiquery("select count(0) as a from ymue where ue011=0 and from_unixtime(ue004,'%Y-%m-%d')='".date('Y-m-d')."'",2);
$newemail=$db->rs['a'];

echo '<h3>后台首页</h3>';
echo '<div class="main-r-body">
<div style="width:40%;float:left">
<table class="yimaoregtab yimaotab">
	<tr>
		<td class="wdb25 alignr paddingr">今日新增会员</td>
		<td class="wdb25 alignc paddingl">'.$newuser.'</td>
		<td class="wdb25 alignr paddingr" valign="top" >待审核会员</td>
		<td class="wdb25 alignc paddingl">'.redtxt($okuser).'</td>		
	</tr>
	<tr>
		<td class="wdb25 alignr paddingr">今日新增充值</td>
		<td class="wdb25 alignc paddingl">'.$newrech.'</td>
		<td class="wdb25 alignr paddingr" valign="top" >待审核充值</td>
		<td class="wdb25 alignc paddingl">'.redtxt($okrech).'</td>		
	</tr>	
	<tr>
		<td class="wdb25 alignr paddingr">今日新增提现</td>
		<td class="wdb25 alignc paddingl">'.$newtiqu.'</td>
		<td class="wdb25 alignr paddingr" valign="top" >待审核提现</td>
		<td class="wdb25 alignc paddingl">'.redtxt($oktiqu).'</td>		
	</tr>	
	<tr>
		<td class="wdb25 alignr paddingr">今日转账数</td>
		<td class="wdb25 alignc paddingl">'.$newzhuan.'</td>
		<td class="wdb25 alignr paddingr" valign="top" >今日转币数</td>
		<td class="wdb25 alignc paddingl">'.$newhuan.'</td>		
	</tr>	
	<tr>
		<td class="wdb25 alignr paddingr">今日登录数</td>
		<td class="wdb25 alignc paddingl">'.$newlogin.'</td>
		<td class="wdb25 alignr paddingr" valign="top" >今日邮件数</td>
		<td class="wdb25 alignc paddingl">'.$newemail.'</td>		
	</tr>				
</table>


<table class="yimaoregtab yimaotab" style="margin-top:25px">';
$sql="select * from ymun where un007=0  order by un001 desc limit 10";
$db->yiquery($sql);
foreach ($db->rs as $k => $v) {
echo '	<tr>
		<td align="left">&nbsp;&nbsp;●&nbsp;<a href="'.YMADMINDEX.'?veid='.$v['un001'].'" >'.$v['un002'].'</a></td>		
	</tr>	';

}
if(empty($db->rs)){
	echo '	<tr>
		<td align="left">&nbsp;&nbsp;您没有任何公告消息!</td>		
	</tr>	';
}
echo '</table>

</div>
<table class="yimaoregtab yimaotab" style="width:58%;float:left;margin-left:2%">';
$sql="select * from ymue   where ue011=0 and ue003='$_username' and (ue008=0 or ue008=1) order by ue001 desc limit 15";
$db->yiquery($sql);
foreach ($db->rs as $k => $v) {
	$db->yiquery("select count(0) from ymue where ue007=0 and ue003='$_username' and ue011=".$v['ue001'],3);
	$yuehf1=$yuehf=$db->fval;	
echo '	<tr>
		<td align="left">&nbsp;&nbsp;●&nbsp;<a  href="'.YMCURL.'yjxview&edid='.$v['ue001'].'&kan='.$yuehf1.'">'.$v['ue005'].'</a><span style="color:#888">---['.$v['ue002'].']---['.formatdate($v['ue004'],1).']</span></td>		
	</tr>	';

}
if(empty($db->rs)){
	echo '	<tr>
		<td align="left">&nbsp;&nbsp;您没有任何邮件!</td>		
	</tr>	';
}
echo '</table>

</div>';		

function redtxt($v){
	if($v>0){
		return "<span style='color:#f00'>$v</span>";
	}else{
		return $v;
	}
}
?>