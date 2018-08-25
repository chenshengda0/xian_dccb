<?php defined('YIMAOMONEY') or exit('Access denied');?>



  <div class="right_center">>&nbsp;提供帮助申请投诉</div>

  <div class="indent-r-top">财务管理</div>
<?php

        if($_GET["gpid"]<1) msg_l("o_O ~ 操作失败!","home.php?yim=cunkuan&templist=1");
        $db->yiquery("select * from ymugp where gp002=$_userid and gp001=".$_GET["gpid"],2);
        if(empty($db->rs)) msg_l("o_O ~ 操作失败!","home.php?yim=cunkuan&templist=1");
        $rs=$db->rs;
        $cha=(time()-$db->rs['gp004'])/(24*3600);
        if($cha<=$YCF['pdday'])  msg_l("o_O ~ 操作失败!","home.php?yim=cunkuan&templist=1");
          

?>

<form action="control.php" method="post" ><input type="hidden" name="act" value="shents" />     <input type="hidden" name="gpid" value="<?php echo $rs["gp001"]?>" />
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:20px;" class="baitab" align="center">
                    <tbody>
                        <tr>
                            <td width="25%" valign="top" align="right">提供帮助编号：</td>     
                            <td valign="top"><?php echo $rs["gp013"]?></td>
                        </tr>   
                        <tr>
                            <td width="25%" valign="top" align="right">提供帮助金额：</td>     
                            <td valign="top"><?php echo $rs["gp005"]?></td>
                        </tr>                                                     
                        <tr>
                            <td width="25%" valign="top" align="right">投诉内容：</td>     
                            <td valign="top"><textarea name="content" id="content"  maxlength="450" style="margin-top:5px;outline:0px" rows="5" cols="50"></textarea></td>
                        </tr>             
                         
                         <tr>
                            <td width="25%" valign="top" align="right" style="height:50px;line-height:50px"></td>     
                            <td valign="top" style="height:50px;line-height:50px"><input type="submit" value="确定" class="btn"></td>
                        </tr>        
                    </tbody>
                </table></form>