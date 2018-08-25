<?php defined('YIMAOMONEY') or exit('Access denied');?>

<?php



$rr=$config->getmb();
?>

  <div class="right_center">>&nbsp;密保修改</div>

  <div class="indent-r-top">会员中心</div>
<form action="control.php" method="post" onsubmit="return chkmb()"><input type="hidden" name="act" value="modmb">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:5px;" class="baitab" align="center">
                    <tbody>

                        <tr>
                            <td width="25%" valign="top" align="right"><span class="regc">*&nbsp;</span>新密保问题：</td>     
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
                            <td width="25%" valign="top" align="right"><span class="regc">*&nbsp;</span>新密保答案：</td>     
                            <td valign="top"><input type="text" name="mba" id="mba" class="reginput"  maxlength="30"></td>
                        </tr>             
                         
                         <tr>
                            <td width="25%" valign="top" align="right" style="height:50px;line-height:50px"></td>     
                            <td valign="top" style="height:50px;line-height:50px"><input type="submit" value="修改" class="btn"></td>
                        </tr>        
                    </tbody>
                </table></form>