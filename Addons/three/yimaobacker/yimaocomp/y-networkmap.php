<?php
defined('YIMAOMONEY') or exit('Access denied');

$option=getnums($_GET['option'],1);

echo '<h3>'.getmenu($YCF['menu'],'networkmap').'</h3>';
echo '<div class="main-r-body">
		<div class="main-r-item"><a href="'.$YCF['curl'].'&option=1" class="'.geteqval(array($option,1,'yimaoaselbtn','yimaoabtn')).'">推荐关系图</a><a href="'.$YCF['curl'].'&option=3" class="'.geteqval(array($option,3,'yimaoaselbtn','yimaoabtn')).'">推荐关系表</a></div>';
if($option==1){
	@include('y-tjmap.php');
}elseif($option==2){
	include('y-anmap.php');
}elseif($option==3){
	include('y-tjbiaomap.php');
}elseif($option==4){
	include('y-anbiaomap.php');
}

echo '</div>';		

?>