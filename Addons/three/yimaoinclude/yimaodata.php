<?php
defined('YIMAOMONEY') or exit('Access denied');
ini_set('session.use_only_cookies', 1);
ini_set('date.timezone','Asia/Shanghai');
@set_magic_quotes_runtime(1);


define('YMURL', 'http://'.$_SERVER['HTTP_HOST'].'/');
define('YMADMINURL', YMURL.'yimaobacker/');
define('YMADMINDEX', YMADMINURL.'yimaomain.php');
define('YMINDEX', YMURL.'home.php');
define('YMJIAMI', 'yimao1011');

$YMTIME=time();
$YCF=array();


$YCF['anrea']=array('A组','B组');
$YCF['prizename']=array('利息','领导奖','辅导奖','片区奖','实发总额','小金库','收益钱包','佣金钱包');
$YCF['prizeval']=array('051','052','054','055','050','061','062','063');
$YCF['regbd']=0;
$YCF['regan']=0;
$YCF['regbdl']=0;
$YCF['reganl']=0;
?>