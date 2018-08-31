<?php
session_start();
@set_time_limit(0);
error_reporting(E_ALL& ~E_NOTICE); //E_ALL& ~E_NOTICE
header("Content-Type:text/html;charset=utf-8");

define('YIMAOMONEY',true);
define('YMROOT',str_replace('\\', '/', dirname(__FILE__)));

require(YMROOT.'/yimaoinclude/yimaodata.php');
require(YMROOT.'/yimaoinclude/yimaodatabase.php');
require(YMROOT.'/yimaoinclude/yimaocommon.fun.php');
require(YMROOT.'/yimaoinclude/yimaomobile.php');
require(YMROOT.'/yimaodebug.php');


define('ROOTTHEMETEMP',YMROOT.'/yimaotheme/temp/');
define('ROOTTHEME',YMROOT.'/yimaotheme/');
define('YMTHEME', YMURL.'yimaotheme/');

require(YMROOT.'/yimaoinclude/yimaocommon.db.php');
require(YMROOT.'/yimaoinclude/yimaomysql.php');
require(YMROOT.'/yimaoinclude/yimaomember.php');
require(YMROOT.'/yimaoinclude/yimaoconfig.php');
require(YMROOT.'/yimaoinclude/yimaocomputing.php');


$YCF['pagecount']=13;


$db=new mysql(array($YCF['dbhost'],$YCF['dbname'],$YCF['dbuser'],$YCF['dbpwd'],$YCF['debug']));
$member=new member();
$config=new config();

getconfig();


?>