<?php
defined('YIMAOMONEY') or exit('Access denied');

$option=getnums($_GET['option'],'1');
if($option==2){
	fun_xunhuan1();
	yimao_writelog('计算配对');
	yimao_automsg('计算配对！',$YCF['curl']);
}
if($option==3){
	fun_rfh1();
	yimao_writelog('计算分红');
	yimao_automsg('计算分红！',$YCF['curl']);
}


echo '<h3>'.getmenu($YCF['menu'],'handwork').'</h3>';
echo '<br><a href="'.getqueurl('option',2).'" class="yimaoabtn" onclick="if(confirm(\'您确定要计算配对吗?\')){artload(\'计算配对正在执行中,请耐心等待！\')}else{return false}">计算配对</a>&nbsp;&nbsp;<a href="'.getqueurl('option',3).'" class="yimaoabtn" onclick="if(confirm(\'您确定要计算利息吗?\')){artload(\'计算利息正在执行中,请耐心等待！\')}else{return false}">计算利息</a>';

?>