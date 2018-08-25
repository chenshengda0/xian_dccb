<?php
defined('YIMAOMONEY') or exit('Access denied');


$filesize=ini_get('upload_max_filesize');
if('/'==DIRECTORY_SEPARATOR){
	$ip=$_SERVER['SERVER_ADDR'];
}else{
	$ip=@gethostbyname($_SERVER['SERVER_NAME']);
}
echo '<h3>'.getmenu($YCF['menu'],'serveenvir').'</h3>';
echo '<div class="main-r-body">
<table class="yimaoregtab yimaotab">
	<tr>
		<td class="wdb25 alignr paddingr">服务器操作系统&nbsp;&nbsp;</td>
		<td class=" alignl paddingl">&nbsp;&nbsp;'.PHP_OS.'</td>
	
	</tr>
	<tr>
		<td class="wdb25 alignr paddingr">Web 服务器&nbsp;&nbsp;</td>
		<td class="alignl paddingl">&nbsp;&nbsp;'.$_SERVER['SERVER_SOFTWARE'].'</td>
	</tr>	
	<tr>
		<td class="wdb25 alignr paddingr">PHP 版本&nbsp;&nbsp;</td>
		<td class="alignl paddingl">&nbsp;&nbsp;'.PHP_VERSION.'</td>
	</tr>	
	<tr>
		<td class="wdb25 alignr paddingr">MySQL 版本&nbsp;&nbsp;</td>
		<td class="alignl paddingl">&nbsp;&nbsp;'.$db->yiverson().'</td>
	</tr>	
	<tr>
		<td class="wdb25 alignr paddingr">当前网站域名&nbsp;&nbsp;</td>
		<td class="alignl paddingl">&nbsp;&nbsp;'.$_SERVER["SERVER_NAME"].'</td>
	</tr>
	<tr>
		<td class="wdb25 alignr paddingr">服务器IP&nbsp;&nbsp;</td>
		<td class="alignl paddingl">&nbsp;&nbsp;'.$ip.'</td>
	</tr>	
	<tr>
		<td class="wdb25 alignr paddingr">当前服务器时间&nbsp;&nbsp;</td>
		<td class="alignl paddingl">&nbsp;&nbsp;'.formatdate(time()).'</td>
	</tr>
	<tr>
		<td class="wdb25 alignr paddingr">显示错误信息（display_errors）&nbsp;&nbsp;</td>
		<td class="alignl paddingl">&nbsp;&nbsp;'.showserveenvir("display_errors").'</td>
	</tr>	
	<tr>
		<td class="wdb25 alignr paddingr">自动字符串转义（magic_quotes_gpc）&nbsp;&nbsp;</td>
		<td class="alignl paddingl">&nbsp;&nbsp;'.showserveenvir("magic_quotes_gpc").'</td>
	</tr>	
	<tr>
		<td class="wdb25 alignr paddingr">"<% %>"ASP风格标记（asp_tags）&nbsp;&nbsp;</td>
		<td class="alignl paddingl">&nbsp;&nbsp;'.showserveenvir("asp_tags").'</td>
	</tr>													
</table></div>';



function showserveenvir($varName)
{

	switch($result = get_cfg_var($varName))
	{

		case 0:

			return '<font color="red">×</font>';

		break;
		

		case 1:

			return '<font color="green">√</font>';

		break;
		

		default:

			return $result;

		break;

	}

}

?>