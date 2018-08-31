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


		$arr["ui001"]=trim($_POST['ui001']);
		$arr["ui002"]=trim($_POST['ui002']);
		$arr["ui003"]=trim($_POST['ui003']);
		$arr["ui004"]=trim($_POST['ui004']);
		$arr["ui005"]=trim($_POST['ui005']);
		$arr["ui006"]=trim($_POST['ui006']);
		$arr["ui007"]=trim($_POST['ui007']);

		$db->yiquery('select * from ymuii limit 1',2);
		if(!empty($db->rs)){
			$sql=getupdatesql($arr,'ymuii',"1=1");
			$db->yiexec($sql);
		}else{
			$sql=getinsertsql($arr,'ymuii');
			$db->yiexec($sql);
		}

		yimao_writelog('修改银行资料');
		yimao_automsg('修改成功！',$YCF['curl']);
}

$db->yiquery('select * from ymuii limit 1',2);

echo '<h3>编辑公司联系信息</h3>';
echo '<div class="main-r-body"><form method="post" action="'.geturl().'">
		<div class="main-r-item"><a href="'.YMCURL.'gongsipei" class="yimaoabtn">返回</a><input type="submit" class="yimaoabtn" value="修改"></div>
		<input type="hidden" name="act" value="add">';
echo '<table class="yimaoregtab yimaotab">';
echo '	<tr>
			<td class="wdb20 alignr paddingr">银行名称</td>
			<td class="alignl paddingl"><input type="text" name="ui001" value="'.$db->rs["ui001"].'" class="yimaoconfig wd400"></td>
		</tr>';
echo '	<tr>
			<td class="wdb20 alignr paddingr">银行卡号</td>
			<td class="alignl paddingl"><input type="text" name="ui002" value="'.$db->rs["ui002"].'" class="yimaoconfig wd400"></td>
		</tr>';	
echo '	<tr>
			<td class="wdb20 alignr paddingr">银行姓名</td>
			<td class="alignl paddingl"><input type="text" name="ui003" value="'.$db->rs["ui003"].'" class="yimaoconfig wd400"></td>
		</tr>';	
echo '	<tr>
			<td class="wdb20 alignr paddingr">开户地址</td>
			<td class="alignl paddingl"><input type="text" name="ui004" value="'.$db->rs["ui004"].'" class="yimaoconfig wd400"></td>
		</tr>';			
echo '	<tr>
			<td class="wdb20 alignr paddingr">联系电话</td>
			<td class="alignl paddingl"><input type="text" name="ui005" value="'.$db->rs["ui005"].'" class="yimaoconfig wd400"></td>
		</tr>';	
echo '	<tr>
			<td class="wdb20 alignr paddingr">QQ</td>
			<td class="alignl paddingl"><input type="text" name="ui006" value="'.$db->rs["ui006"].'" class="yimaoconfig wd400"></td>
		</tr>';	
echo '	<tr>
			<td class="wdb20 alignr paddingr">微信</td>
			<td class="alignl paddingl"><input type="text" name="ui007" value="'.$db->rs["ui007"].'" class="yimaoconfig wd400"></td>
		</tr>';										
echo '</table>';
echo '</form></div>';		

?>