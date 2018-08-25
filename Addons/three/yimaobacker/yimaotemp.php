<?php
function yimao_admin_main(){
global $YCF,$_username;;
echo '<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="renderer" content="webkit"> 
	<title>国际金融</title>
	<link rel="stylesheet" type="text/css" href="yimaores/css/main.css" />
	<script type="text/javascript" src="yimaores/js/jquery-1.6.4.min.js"></script>
	<script type="text/javascript" src="yimaores/art/artDialog.source.js?skin=simple"></script>	
	<script type="text/javascript" src="yimaores/js/onload.js"></script>

</head>
<body>
<div class="main-l">
	<div class="menu">
		<div class="menuh clearmenuh"><span>～欢迎您，'.$_username.'！</span></div>
		<div class="menub" style="display:block">
			<a href="#"  title="当前服务器时间">'.date('Y-m-d H:i:s').'</a><br>
			<a href="'.YMINDEX.'" target="_blank">访问前台</a>
			<a href="'.YMADMINDEX.'">后台首页</a><br>
			<a href="'.YMCURL.'serveenvir">服务环境</a>
			<a href="'.YMCURL.'quicktool">快捷工具</a><br>				
			<a href="index.php?u=1" onclick="return confirm(\'您确定要退出后台吗?\')">退出后台</a>
			<a href="javascript:void(0);" onclick="item_close()">菜单收缩</a><br>						
		</div>
	</div>';
foreach ($YCF['menu'] as $kr => $vr) {
if(!strstr($YCF['adminmenu1'],$kr))	continue;
echo '<div class="menu"><div class="menuh"><span>'.$kr.'</span></div><div class="menub"><ul>';
foreach ($vr as $k => $v) {
	if(!strstr($YCF['adminmenu2'],$v[0]))	continue;
	echo '<li><a href="'.$v[1].'">'.$k.'</a></li>';
}
echo '</ul></div></div>';	
}	
						
echo '<br><br></div>	
<div class="main-r">';
}


function yimao_admin_foot(){
echo '</div>	
</body>
</html>';
}


function yimao_admin_one($title,$arr){
echo '	<h3>'.$title.'</h3>
	<div class="main-r-body">
	<div class="main-r-item">';
$i=0;
foreach ($arr as $key => $value) {
$i++;
if($i==1) $url=$value;
echo '<a href="'.$value.'" class="'.geteqval(array($i,1,'quickasel','quicka')).'" name="quickaa" onclick="huana(this)" target="quickiframe">'.$key.'</a>';
}

echo '	</div>	
	<iframe src="'.$url.'" name="quickiframe" style="width:100%;min-height:570px;margin:0px;padding:0px;"></iframe>
	</div>';
}


function yimao_admin_two($arr){
echo '	<h3>'.$arr['yimao_title'].'</h3>
	<div class="main-r-body"><form method="get" action="?">
	<div class="main-r-item"><input type="submit" class="yimaoabtn" value="查询"><input type="input" class="yimaosearch" name="quser"><span class="paixutxt">排序<span>：'.$arr['yimao_orderby'];
if(!empty($arr["yimao_whereoption"])){
echo '<span class="colortxt">账户类型：<span>'.$arr["yimao_whereoption"];
}	
if(!empty($arr["yimao_color"])){
echo '<span class="colortxt">颜色：<span>'.$arr['yimao_color'];
}
if(!empty($arr["yimao_color1"])){
echo '<span class="colortxt">导出：<span>'.$arr['yimao_color1'];
}
echo '</div>';
		$questr=explode("&",$_SERVER["QUERY_STRING"]);
		for($i=0;$i<count($questr);$i++){
			$questrl=explode("=",$questr[$i]);
			if(count($questrl)>0)
			{
				if($questrl[1]!=""){
					if(!($questrl[0]=="quser")){
						echo '<input type="hidden" name="'.$questrl[0].'" value="'.$questrl[1].'">';
					}
				}
			}
		}
echo '	</form><form method="post" action="'.geturl().'">
	<div class="main-r-item" '.$arr["styles"].'>'.$arr['yimao_option'].'</div>	';

echo '<table class="yimaolist yimaotab">
		<tr>
'.$arr['yimao_th'].'
		</tr>
		<tbody>';
if($arr['yimao_count']>0){
echo $arr['yimao_tr'];
}
echo '	</tbody>					
	</table></form>';
if($arr['yimao_count']==0){
echo '<div class="nodata">→ 暂无数据哟 ~</div>';
}else{
echo '	<div class="pages">
		<div class="floatr">
'.$arr['yimao_page'].'
		</div>
	</div>	';
}

echo '</div>';
}


function yimao_admin_tree($arr){
echo '	<h3>'.$arr['yimao_title'].'</h3>
	<div class="main-r-body">
	<div class="main-r-item">'.$arr['yimao_option'].'</div>';
echo '<table class="yimaotab">
		<tr>
'.$arr['yimao_th'].'
		</tr>
		<tbody>';
if($arr['yimao_count']>0){
echo $arr['yimao_tr'];
}
echo '	</tbody>					
	</table>';
if($arr['yimao_count']==0){
echo '<div class="nodata">→ 暂无数据哟 ~</div>';
}
echo '</div>';
}


function yimao_admin_four($arr){
echo '<div class="main-r-body"><form method="post" action="">
	<div class="main-r-item"><input type="submit" class="yimaoabtn" value="查询"><input type="input" class="yimaosearch" name="quser"><span class="paixutxt">排序<span>：'.$arr['yimao_orderby'];
if(!empty($arr["yimao_color"])){
echo '<span class="colortxt">颜色：<span>'.$arr['yimao_color'];
}
echo '</div>';
		$questr=explode("&",$_SERVER["QUERY_STRING"]);
		for($i=0;$i<count($questr);$i++){
			$questrl=explode("=",$questr[$i]);
			if(count($questrl)>0)
			{
				if($questrl[1]!=""){
					if(!($questrl[0]=="quser")){
						echo '<input type="hidden" name="'.$questrl[0].'" value="'.$questrl[1].'">';
					}
				}
			}
		}
echo '	</form><form method="post" action="'.geturl().'">
	<div class="main-r-item">'.$arr['yimao_option'].'</div>	';

echo '<table class="yimaolist yimaotab">
		<tr>
'.$arr['yimao_th'].'
		</tr>
		<tbody>';
if($arr['yimao_count']>0){
echo $arr['yimao_tr'];
}
echo '	</tbody>					
	</table></form>';
if($arr['yimao_count']==0){
echo '<div class="nodata">→ 暂无数据哟 ~</div>';
}else{
echo '	<div class="pages">
		<div class="floatr">
'.$arr['yimao_page'].'
		</div>
	</div>	';
}

echo '</div>';
}

function yimao_admin_five($arr){
echo '<div class="main-r-body"><form method="get" action="">
	<div class="main-r-item"><input type="submit" class="yimaoabtn" value="查询"><input type="input" class="yimaosearch" name="quser"><span class="paixutxt">排序<span>：'.$arr['yimao_orderby'].'&nbsp;&nbsp;'.$arr['yimao_txt'].'</div>';
		$questr=explode("&",$_SERVER["QUERY_STRING"]);
		for($i=0;$i<count($questr);$i++){
			$questrl=explode("=",$questr[$i]);
			if(count($questrl)>0)
			{
				if($questrl[1]!=""){
					if(!($questrl[0]=="quser")){
						echo '<input type="hidden" name="'.$questrl[0].'" value="'.$questrl[1].'">';
					}
				}
			}
		}
echo '	</form><form method="post" action="'.geturl().'">';

echo '<table class="yimaolist yimaotab">
		<tr>
'.$arr['yimao_th'].'
		</tr>
		<tbody>';
if($arr['yimao_count']>0){
echo $arr['yimao_tr'];
}
echo '	</tbody>					
	</table></form>';
if($arr['yimao_count']==0){
echo '<div class="nodata">→ 暂无数据哟 ~</div>';
}else{
echo '	<div class="pages">
		<div class="floatr">
'.$arr['yimao_page'].'
		</div>
	</div>	';
}

echo '</div>';
}



function yimao_admin_six($arr){
echo '	<h3>'.$arr['yimao_title'].'</h3>
	<div class="main-r-body"><form method="get" action="?">
	<div class="main-r-item"><input type="submit" class="yimaoabtn" value="查询"><input type="input" class="yimaosearch" name="quser"><span class="paixutxt">排序<span>：'.$arr['yimao_orderby'].'</div>';
		$questr=explode("&",$_SERVER["QUERY_STRING"]);
		for($i=0;$i<count($questr);$i++){
			$questrl=explode("=",$questr[$i]);
			if(count($questrl)>0)
			{
				if($questrl[1]!=""){
					if(!($questrl[0]=="quser")){
						echo '<input type="hidden" name="'.$questrl[0].'" value="'.$questrl[1].'">';
					}
				}
			}
		}
echo '	</form><form method="post" action="'.geturl().'">	';

echo '<table class="yimaolist yimaotab">
		<tr>
'.$arr['yimao_th'].'
		</tr>
		<tbody>';
if($arr['yimao_count']>0){
echo $arr['yimao_tr'];
}
echo '	</tbody>					
	</table></form>';
if($arr['yimao_count']==0){
echo '<div class="nodata">→ 暂无数据哟 ~</div>';
}else{
echo '	<div class="pages">
		<div class="floatr">
'.$arr['yimao_page'].'
		</div>
	</div>	';
}

echo '</div>';
}


function yimao_admin_seven($arr){
echo '	<h3>'.$arr['yimao_title'].'</h3>
	<div class="main-r-body"><form method="get" action="?">
	<div class="main-r-item"><input type="submit" class="yimaoabtn" value="查询"><input type="input" class="yimaosearch" name="quser"><span class="paixutxt">排序<span>：'.$arr['yimao_orderby'];
if(!empty($arr["yimao_color"])){
echo '<span class="colortxt">颜色：<span>'.$arr['yimao_color'];
}
echo '</div>';
		$questr=explode("&",$_SERVER["QUERY_STRING"]);
		for($i=0;$i<count($questr);$i++){
			$questrl=explode("=",$questr[$i]);
			if(count($questrl)>0)
			{
				if($questrl[1]!=""){
					if(!($questrl[0]=="quser")){
						echo '<input type="hidden" name="'.$questrl[0].'" value="'.$questrl[1].'">';
					}
				}
			}
		}
echo '	</form><form method="post" action="'.geturl().'">
	<div class="main-r-all">'.$arr['yimao_option'].'</div>';

echo '<table class="yimaolist yimaotab">
		<tr>
'.$arr['yimao_th'].'
		</tr>
		<tbody>';
if($arr['yimao_count']>0){
echo $arr['yimao_tr'];
}
echo '	</tbody>					
	</table></form>';
if($arr['yimao_count']==0){
echo '<div class="nodata">→ 暂无数据哟 ~</div>';
}else{
echo '	<div class="pages">
		<div class="floatr">
'.$arr['yimao_page'].'
		</div>
	</div>	';
}

echo '</div>';
}


function yimao_admin_eight($arr){
echo '	<h3>'.$arr['yimao_title'].'</h3><div class="main-r-body">
	<div class="main-r-item">'.$arr['yimao_option'].'</div>	';

echo '<table class="yimaolist yimaotab">
		<tr>
'.$arr['yimao_th'].'
		</tr>
		<tbody>';
if($arr['yimao_count']>0){
echo $arr['yimao_tr'];
}
echo '	</tbody>					
	</table></form>';
if($arr['yimao_count']==0){
echo '<div class="nodata">→ 暂无数据哟 ~</div>';
}else{
echo '	<div class="pages">
		<div class="floatr">
'.$arr['yimao_page'].'
		</div>
	</div>	';
}

echo '</div>';
}
?>