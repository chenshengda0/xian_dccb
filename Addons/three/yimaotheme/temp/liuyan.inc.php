<?php defined('YIMAOMONEY') or exit('Access denied');?>



  <div class="right_center">>&nbsp;留言反馈</div>

  <div class="indent-r-top">联系我们</div>
<?php

?>
<div style="margin:20px 0px 20px 10px">
    <button onclick="window.location='<?php  echo YMINDEX?>?yim=sjx'" class="btt">收件箱</button>&nbsp;<button onclick="window.location='<?php  echo YMINDEX?>?yim=fjx'" class="btt">发件箱</button>&nbsp;<button onclick="window.location='<?php  echo YMINDEX?>?yim=liuyan'" class="btt"  style="color:#65919C">留言反馈</button></div>
<form action="control.php" method="post" onsubmit="return chksend()"><input type="hidden" name="act" value="sendemail" />     
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:20px;" class="baitab" align="center">
                    <tbody>

                        <tr>
                            <td width="25%" valign="top" align="right">收件人：</td>     
                            <td valign="top" align="left"><input type="text" name="username" id="username" maxlength="8" value="公司" class="reginput" disabled="disabled"><label><input type='radio' name='lx' value='0' onclick="username.value='公司';username.disabled=true;" checked="checked"/>给公司留言</label>
<label><input type='radio' name='lx' value='1' onclick="username.value='';username.disabled=false;"/>给会员留言</label>
</td>
                        </tr>
                        <tr>
                            <td width="25%" valign="top" align="right">留言标题：</td>     
                            <td valign="top" align="left"><input type="text" name="lytitle" id="lytitle" class="reginput" maxlength="30"></td>
                        </tr>
                        <tr>
                            <td width="25%" valign="top" align="right">留言类型：</td>     
                            <td valign="top" align="left"><?php echo getemailtype(array(2))?></td>
                        </tr>                        
                        <tr>
                            <td width="25%" valign="top" align="right">留言内容：</td>     
                            <td valign="top"><textarea name="lycontent" id="lycontent"  maxlength="450" style="margin-top:5px;outline:0px" rows="5" cols="50"></textarea></td>
                        </tr>             
                         
                         <tr>
                            <td width="25%" valign="top" align="right" style="height:50px;line-height:50px"></td>     
                            <td valign="top" style="height:50px;line-height:50px"><input type="submit" value="确定" class="btn"></td>
                        </tr>        
                    </tbody>
                </table></form>