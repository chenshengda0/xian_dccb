<?php
defined('YIMAOMONEY') or exit('Access denied');



if($_POST['act']=='edit'){

		$edid=$_POST['edid'];

		$arr['ub004']=checkstr($_POST['bankname']);
		$arr['ub005']=checkstr($_POST['bankcard']);
		$arr['ub006']=checkstr($_POST['bankuser']);
		$arr['ub007']=checkstr($_POST['bankaddress']);
		$arr['ub008']=checkstr($_POST['bankbz']);

		$sql=getupdatesql($arr,"ymub","ub001=".$edid);
		$db->yiexec($sql);
		
		yimao_writelog('修改银行id'.$edid);
		yimao_automsg('修改成功！',YMCURL.'banklist');
}

$edid=getnums($_GET['edid'],0);

$db->yiquery('select * from ymub where ub001='.$edid,2);
if(empty($db->rs)){
	yimao_automsg('找不到此银行！',YMCURL.'banklist',1);
}

$sybank=explode("-",$YCF["sybank"]);
                              foreach ($sybank as $k => $v) {
                            	
                               $ss.='<option value="'.$v.'" '.(($v==$db->rs["ub004"])?"selected":"").'>'.$v.'</option>';
                        
                                }

echo '<h3>银行编辑</h3>';
echo '<div class="main-r-body"><form method="post" action="'.geturl().'">
		<div class="main-r-item"><a href="'.YMCURL.'banklist" class="yimaoabtn">返回银行列表</a><input type="submit" class="yimaoabtn" value="提交"></div>
		<input type="hidden" name="act" value="edit"><input type="hidden" name="edid" value="'.$db->rs['ub001'].'">';
echo '<table class="yimaoregtab yimaotab">';
echo '	<tr>
			<td class="wdb20 alignr paddingr">银行名称</td>
			<td class="alignl paddingl"><select name="bankname" style="color:#fff;text-indent:0;background:#272822;line-height:30px;height:30px" id="bankname" class="yimaoconfig wd400">'.$ss.'				
			</select></td>
		</tr>';
echo '	<tr>
			<td class="wdb20 alignr paddingr">银行卡号</td>
			<td class="alignl paddingl"><input type="text" name="bankcard" id="bankcard" class="yimaoconfig wd400" maxlength="30" value="'.$db->rs["ub005"].'"></td>
		</tr>';		
echo '	<tr>
			<td class="wdb20 alignr paddingr">银行姓名</td>
			<td class="alignl paddingl"><input type="text" name="bankuser" id="bankuser" class="yimaoconfig wd400" maxlength="15" value="'.$db->rs["ub006"].'"></td>
		</tr>';	
echo '	<tr>
			<td class="wdb20 alignr paddingr">开户地址</td>
			<td class="alignl paddingl"><input type="text" name="bankaddress" id="bankaddress" class="yimaoconfig wd400" maxlength="40" value="'.$db->rs["ub007"].'"></td>
		</tr>';	
echo '	<tr>
			<td class="wdb20 alignr paddingr">描述</td>
			<td class="alignl paddingl"><textarea name="bankbz" style="margin-top:5px;outline:0px;height:100px" rows="4" cols="30" maxlength="80" class="yimaoconfig wd400">'.$db->rs["ub008"].'</textarea></td>
		</tr>';							
echo '</table>';
echo '</form></div>';		

?>