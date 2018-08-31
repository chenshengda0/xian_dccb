<?php defined('YIMAOMONEY') or exit('Access denied');?>

<?php

if($_SESSION['bmb']==md5("yimaomian11")){
        $_SESSION['yzmm']=$_GET['m'];
        locationurl(YMINDEX.'?yim='.$_GET['m']);
}


$rd=$config->getpwd();
?>
  <div class="right_center">>&nbsp;密码验证</div>

  <div class="indent-r-top">密码验证</div>

<form action="control.php" method="post" onsubmit="return chkmm()"><input type="hidden" name="act" value="yzmm" /> <input type="hidden" name="m" value="<?php  echo $_GET['m']?>" /> <input type="hidden" name="yzform" value="ok" />     
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:20px;" class="baitab" align="center">
                    <tbody>
                        <tr>
                            <td width="25%" valign="top" align="right"><?php echo $rd['rgpwd2']?>：</td>     
                            <td valign="top"><input type="password" name="pwd" id="pwd" class="reginput"  maxlength="30"></td>
                        </tr>             
                         
                         <tr>
                            <td width="25%" valign="top" align="right" style="height:50px;line-height:50px"></td>     
                            <td valign="top" style="height:50px;line-height:50px"><input type="submit" value="确定" class="btn">密码 11112222</td>
                        </tr>        
                    </tbody>  <!---此处可以删除 ，只是说明文字--->此文件修改地址  yimaotheme\temp\yzmm.inc.php
                </table></form>