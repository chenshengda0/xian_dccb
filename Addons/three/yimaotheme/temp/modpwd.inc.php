<?php defined('YIMAOMONEY') or exit('Access denied');?>

<?php

$rr=$config->getpwd();
?>
  <div class="right_center">>&nbsp;密码修改</div>

  <div class="indent-r-top">会员中心</div>


<form action="control.php" method="post" onsubmit="return chkpwd()"><input type="hidden" name="act" value="modpwd">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:5px;" class="baitab" align="center">
                    <tbody>

                        <tr>
                            <td width="25%" valign="top" align="right"><span class="regc">*&nbsp;</span>密码类型：</td>     
                            <td valign="top" align="left"><?php
                                foreach ($rr as $k => $v) {
                                   echo '<label><input type="radio" name="mtype" value="'.$k.'">'.$v.'</label>&nbsp;';
                                }                            
                            ?></td>
                        </tr>
                        <tr>
                            <td width="25%" valign="top" align="right"><span class="regc">*&nbsp;</span>原密码：</td>     
                            <td valign="top"><input type="password" name="ypwd" id="ypwd" class="reginput" maxlength="30"></td>
                        </tr>          
                        <tr>
                            <td width="25%" valign="top" align="right"><span class="regc">*&nbsp;</span>新密码：</td>     
                            <td valign="top"><input type="password" name="npwd" id="npwd" class="reginput" maxlength="30"></td>
                         </tr>          
                         
                         <tr>
                            <td width="25%" valign="top" align="right" style="height:50px;line-height:50px"></td>     
                            <td valign="top" style="height:50px;line-height:50px"><input type="submit" value="修改" class="btn"></td>
                        </tr>        
                    </tbody>
                </table></form>