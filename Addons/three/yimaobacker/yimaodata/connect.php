<?php
define('EBAK_PATH',dirname(__FILE__));
define('EBAK_ADMIN',substr(dirname(__FILE__),0,-9));
require_once(EBAK_ADMIN."yimaoini.php");
require(EBAK_PATH.'\config.php');
require(EBAK_PATH.'\dbsql.php');
require(EBAK_PATH.'\functions.php');

$editor=0;
if($php_outtime){
	$php_outtime=(int)$php_outtime;
	@set_time_limit($php_outtime);
}

//中文
function db_connect(){
	global $phome_db_server,$phome_db_username,$phome_db_password,$phome_db_dbname,$phome_db_port,$phome_db_char,$phome_db_ver,$editor,$fun_r;
	$dblocalhost=$phome_db_server;
	if($phome_db_port){
		$dblocalhost.=":".$phome_db_port;
	}
	$link=@mysql_connect($dblocalhost,$phome_db_username,$phome_db_password);
	mysql_select_db($phome_db_dbname);
	if(empty($link)){
		if(empty($fun_r['ConntConnectDb'])){
			if($editor==1){$a="../";}
			elseif($editor==2){$a="../../";}
			elseif($editor==3){$a="../../../";}
			else{$a="";}
			@include_once $a.LoadLang('f.php');
		}
		echo $fun_r['ConntConnectDb'];
		exit();
	}
	DoSetDbChar($phome_db_char);
	if($phome_db_ver>='5.0'){
		@mysql_query("SET sql_mode=''");
	}
	return $link;
}

function DoSetDbChar($dbchar){
	if($dbchar&&$dbchar!='auto'){
		@mysql_query('set character_set_connection='.$dbchar.',character_set_results='.$dbchar.',character_set_client=binary;');
	}
}

function db_close(){
	global $link;
	@mysql_close($link);
}

function Ebak_GetMysqlVerForDb(){
	$sql=mysql_query("select version() as version");
	$r=mysql_fetch_array($sql);
	return Ebak_ReturnMysqlVer($r['version']);
}

function Ebak_ReturnMysqlVer($dbver){
	if(empty($dbver)){
		return '';
	}
	if($dbver>='6.0'){
		$dbver='6.0';
	}elseif($dbver>='5.0'){
		$dbver='5.0';
	}elseif($dbver>='4.1'){
		$dbver='4.1';
	}else{
		$dbver='4.0';
	}
	return $dbver;
}

function esetcookie($var,$val,$life=0){
	global $phome_cookiedomain,$phome_cookiepath,$phome_cookievarpre;
	return setcookie($phome_cookievarpre.$var,$val,$life,$phome_cookiepath,$phome_cookiedomain);
}

function getcvar($var){
	global $phome_cookievarpre;
	$tvar=$phome_cookievarpre.$var;
	return $_COOKIE[$tvar];
}

function LoadLang($file){
	global $ebaklang;
	return $file;
}

function RepPostVar($val){
	$val=str_replace(" ","",$val);
	$val=str_replace("'","",$val);
	$val=str_replace("\"","",$val);
	$val=addslashes(stripSlashes($val));
	return $val;
}

function LoadAdminTemp($file){
	global $ebaklang;
	return $file;
}
function HeaderIeChar(){
	global $ebaklangchar;
	@header('Content-Type: text/html; charset='.$ebaklangchar);
}

function ReturnUseEbakLang(){
	global $langcharr;
	$loginlangid=(int)getcvar('loginlangid');
	if($langcharr[$loginlangid]){
		$lr=explode(',',$langcharr[$loginlangid]);
		$r['lang']=$lr[0];
		$r['langchar']=$lr[1];
	}else{
		$r['lang']='utf8';
		$r['langchar']='utf8';
	}
	return $r;
}
?>