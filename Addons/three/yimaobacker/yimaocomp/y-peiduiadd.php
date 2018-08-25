<?php
defined('YIMAOMONEY') or exit('Access denied');
$fp = fopen(YMROOT."/lock.txt", "w+");
if(flock($fp,LOCK_EX | LOCK_NB))
{
  chkcunkdaojishi();    
  chkqukdaojishi(); 
  fun_xunhuan();
  flock($fp,LOCK_UN);
}
fclose($fp);


if($_POST['act']=='add'){


// $db->yiquery('select * from ymugp where gp011=1 and gp009=0',2);
// if(!empty($db->rs)){
// 	yimao_automsg('公司有未完成的配对金额，暂时无法添加！',"yimaomain.php?yimao=gongsipei");
// }	
if($_POST['jine']<1) 	yimao_automsg('请输入数字！',"yimaomain.php?yimao=peiduiadd");
$jine=floor(abs($_POST['jine']));
		$arr["gp002"]=1;
		$arr["gp003"]=$_username;
		$arr["gp004"]=time();
		$arr["gp005"]=$jine;
		$arr["gp011"]=1;
		$arr["gp013"]=$_username.date("YmdHis");


		$sql=getinsertsql($arr,'ymugp');
		$db->yiexec($sql);
		
		yimao_writelog('公司添加配对金额');
		yimao_automsg('添加成功！',"yimaomain.php?yimao=gongsipei");
}

// $db->yiquery('select * from ymugp where gp011=1 and gp009=0',2);
// if(!empty($db->rs)){
// 	yimao_automsg('公司有未完成的配对金额，暂时无法添加！',"yimaomain.php?yimao=gongsipei");
// }

echo '<h3>添加公司配对金额</h3>';
echo '<div class="main-r-body"><form method="post" action="'.geturl().'">
		<div class="main-r-item"><a href="'.YMCURL.'gongsipei" class="yimaoabtn">返回</a><input type="submit" class="yimaoabtn" value="添加"></div>
		<input type="hidden" name="act" value="add">';
echo '<table class="yimaoregtab yimaotab">';
echo '	<tr>
			<td class="wdb20 alignr paddingr">金额</td>
			<td class="alignl paddingl"><input type="text" name="jine" value="" class="yimaoconfig wd400"></td>
		</tr>';
									
echo '</table>';
echo '</form></div>';		

?>