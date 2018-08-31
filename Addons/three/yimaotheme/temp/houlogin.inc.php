<?php defined('YIMAOMONEY') or exit('Access denied');?>

<?php
if(empty($_SESSION["yzmm"])||strpos('='.$_SESSION["yzmm"],'=houlogin')===false){
    $_GET["m"]="houlogin";
    @require(ROOTTHEMETEMP."yzmm.inc.php");
    exit;   
}

if(empty($adminra)||$adminra['ua005']==1){ msg_b('访问失败');}
?>
  <div class="right_center">>&nbsp;后台登录</div>

  <div class="indent-r-top">会员中心</div>
<form action="control.php" method="post" onsubmit="return chkmm()"><input type="hidden" name="act" value="houlogin">
                <table width="100%" border="0" cellpadding="0" cellspacing="0"  style="margin-top:20px;"  class="baitab" align="center">
                    <tbody>

                        <tr>
                            <td width="25%" valign="top" align="right">后台登录密码：</td>     
                            <td valign="top"><input type="password" name="pwd" id="pwd" class="reginput" maxlength="30"></td>
                        </tr>          
                         
                         <tr>
                            <td width="25%" valign="top" align="right" style="height:50px;line-height:50px"></td>     
                            <td valign="top" style="height:50px;line-height:50px"><input type="submit" value="登录" class="btn">密码 11112222</td>
                        </tr>        
                    </tbody>
                </table>
				<!---此处可以删除 ，只是说明文字--->  yimaotheme\temp\houlogin.inc.php</form>