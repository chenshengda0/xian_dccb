<?php
require('connect.php'); //中
require LoadLang('f.php');

$phome=$_GET['phome'];

if(empty($phome)){
	$phome=$_POST['phome'];
}

$rnd=$lur['rnd'];
$link=db_connect();
$empire=new mysqlquery();
$empire2=new mysqlquery();
$mydbname=$phome_db_dbname;
$udb=$empire->query("use `".$phome_db_dbname."`");
$db = $empire;




if($phome=="DoEbak"){
	Ebak_DoEbak($_POST);
}elseif($phome=="BakExe"){
	$t=$_GET['t'];
	$s=$_GET['s'];
	$p=$_GET['p'];
	$mypath=$_GET['mypath'];
	$alltotal=$_GET['alltotal'];
	$thenof=$_GET['thenof'];
	$fnum=$_GET['fnum'];
	$stime=$_GET['stime'];
	Ebak_BakExe($t,$s,$p,$mypath,$alltotal,$thenof,$fnum,$stime);
}elseif($phome=="BakExeT"){
	$t=$_GET['t'];
	$s=$_GET['s'];
	$p=$_GET['p'];
	$mypath=$_GET['mypath'];
	$alltotal=$_GET['alltotal'];
	$thenof=$_GET['thenof'];
	$fnum=$_GET['fnum'];
	$auf=$_GET['auf'];
	$aufval=$_GET['aufval'];
	$stime=$_GET['stime'];
	Ebak_BakExeT($t,$s,$p,$mypath,$alltotal,$thenof,$fnum,$auf,$aufval,$stime);
}
else{
	printerror("ErrorUrl","history.go(-1)");
}
?>