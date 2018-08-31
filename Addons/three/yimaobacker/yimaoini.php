<?php
session_start();
@set_time_limit(0);
error_reporting(E_ALL& ~E_NOTICE); //E_ALL& ~E_NOTICE
header("Content-Type:text/html;charset=utf-8");

define('YIMAOMONEY',true);
define('YMROOT',str_replace('\\', '/', substr(__FILE__,0,strripos(dirname(__FILE__),'\\'))));
define('YMADMINCOMP', YMROOT.'/yimaobacker/yimaocomp/');
define('YMADMINDATA', YMROOT.'/yimaobacker/yimaodata/');


require(YMROOT.'/yimaoinclude/yimaocommon.fun.php');

if(empty($_SESSION['houid'])||$_SESSION['houid']<1){
	locationurl('index.php?u=1');
}

getsessiontime();

require(YMROOT.'/yimaoinclude/yimaodata.php');
require(YMROOT.'/yimaoinclude/yimaodatabase.php');
require(YMROOT.'/yimaoinclude/yimaocommon.db.php');
require(YMROOT.'/yimaoinclude/yimaomysql.php');
require(YMROOT.'/yimaoinclude/yimaomember.php');
require(YMROOT.'/yimaoinclude/yimaoconfig.php');
require(YMROOT.'/yimaoinclude/yimaocomputing.php');
require(YMROOT.'/yimaodebug.php');

define('YMADMINDATAURL', YMURL.'yimaobacker/yimaodata/');
define('YMCURL', YMADMINDEX.'?yimao=');

require('yimaoadminfun.php');
require('yimaotemp.php');

$YCF['pagecount']=13;


$_houid=$_SESSION['houid'];
$_username=$_SESSION['houname'];
$db=new mysql(array($YCF['dbhost'],$YCF['dbname'],$YCF['dbuser'],$YCF['dbpwd'],$YCF['debug']));
$member=new member();
$config=new config();
$r=$member->getua($_username);
$YCF['adminmenu1']=$r['uo003'];
$YCF['adminmenu2']=$r['uo004'];

if($r['ua005']) msg_l('您没权限访问','index.php?u=1');
if($r['uo005']) msg_l('您没权限访问','index.php?u=1');

getconfig();
chkqucun();
dongjie();

if($YCF["syopen"]&&$_houid!=1){
	locationurl('index.php?u=1');
}




require(YMADMINCOMP.'/y-menu.php');
?>