<?php
require('common.inc.php');
if(empty($_SESSION['userid'])||$_SESSION['userid']<1){
	locationurl('index.php?u=1');
}


getsessiontime();

$_userid=$_SESSION['userid'];
$r=$member->getuserinfo($_userid);
if(empty($r)){
	locationurl('index.php?u=1');
}
if($r["uv010"]&&$r["uv001"]!=1){
	locationurl('index.php?u=1');
}

$_username=$r['uv002'];
$adminra=$member->getua($_username);

if($YCF["syopen"]&&$_userid!=1){
	locationurl('index.php?u=1');
}

chkqucun();
dongjie();

require(YMROOT.'/yimaoinclude/yimaopage.php');

?>