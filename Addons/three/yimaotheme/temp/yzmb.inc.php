<?php defined('YIMAOMONEY') or exit('Access denied');?>

<?php


if($_SESSION['bmb']==md5("yimaomian11")){
        $_SESSION['yzmb']=$_GET['m'];
        locationurl(YMINDEX.'?yim='.$_GET['m']);
}

$rr=$config->getmb();

?>
  <div class="right_center">>&nbsp;密保验证</div>

  <div class="indent-r-top">密保验证</div>
<form action="control.php" method="post" onsubmit="return chkmb()"><input type="hidden" name="act" value="yzmb" /><input type="hidden" name="m" value="<?php  echo $_GET['m']?>" /> <input type="hidden" name="yzform" value="ok" />     
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:20px;" class="baitab" align="center">
                    <tbody>

                        <tr>
                            <td width="25%" valign="top" align="right">密保问题：</td>     
                            <td valign="top" align="left">

<select name="mbq" id="mbq" class="reginput" >
<option value="-1">请选择您的密保问题?</option>
<?php 

foreach ($rr as $k => $v) {
?>

<option value="<?php echo $k+1?>"><?php echo $v?></option>
<?php   
}?></td>
                        </tr>
                        <tr>
                            <td width="25%" valign="top" align="right">密保答案：</td>     
                            <td valign="top"><input type="text" name="mba" id="mba" class="reginput"  maxlength="30"></td>
                        </tr>             
                         
                         <tr>
                            <td width="25%" valign="top" align="right" style="height:50px;line-height:50px"></td>     
                            <td valign="top" style="height:50px;line-height:50px"><input type="submit" value="确定" class="btn"></td>
                        </tr>        
                    </tbody>
                </table></form>