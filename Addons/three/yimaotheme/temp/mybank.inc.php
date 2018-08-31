<?php defined('YIMAOMONEY') or exit('Access denied');?>
<?php

if(isset($_GET["moid"])){
    if($_GET["moid"]<1)  msg_l("操作失败!",$YCF["curl"]);

    $db->yiquery("select * from ymub where ub002='$_username' and ub001=".$_GET["moid"],2);
    if(empty($db->rs))   msg_l("操作失败!",$YCF["curl"]);

    $db->yisqli("update ymub set ub009=0  where ub002='$_username';"."update ymub set ub009=1 where ub001=".$_GET["moid"]);

    yimao_writelog("设置默认银行",0);
    msg_l("o_O ~ 设置成功 ",$YCF["curl"]);     
}

if(!isset($_GET['tempadd'])){
?>

  <div class="right_center">>&nbsp;我的银行资料</div>

  <div class="indent-r-top">会员中心</div>


<table width="100%" border="0" cellpadding="0" cellspacing="1" style="margin-top:5px;" class="biaotab" align="center">
  <tbody>
    <tr>
        <th>序号</th>     
        <th >银行名称</th>
        <th >银行卡号</th>
        <th >银行姓名</th>
        <th >银行地址</th>
        <th >描述</th>
        <th >添加日期</th>
        <th>操作</th>
    </tr>
<?php
$sql="select * from ymub where ub002='$_username' order by ub001 desc";
$db->yiquery($sql);
$rs=$db->rs;
$ii=0;
foreach ($rs as $k => $v) {
  $ii++;
  $serialid=($pagearr[2]-1)*$pagecount+$ii;

?>
    <tr>
        <td ><?php echo $serialid?></td>     
        <td ><?php echo $v['ub004']?></td>
        <td ><?php echo $v['ub005']?></td>
        <td ><?php echo $v['ub006']?></td>
        <td ><?php echo $v['ub007']?></td>
        <td valign="middle" style="width:150px"  align="left"><div style="float:left;width:150px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;cursor:pointer;cursor:pointer" title="<?php echo $v['ub008']?>"><?php echo $v['ub008']?></div></td>


        <td ><?php echo formatdate($v['ub003'],1)?></td>
        <td><form action="" method="post">&nbsp;<?php echo $v["ub009"]?"<span style='color:#f00'>默认银行</span>":"<a href='".$YCF["curl"]."&moid=".$v["ub001"]."' style='text-decoration:underline'>设为默认</a>"?></form></td>        
    </tr>
<?php
}
?>    
  </tbody>
</table>
<?php if($ii==0){?>
<div class="pagediv" style="text-align:center;color:#E47501">--> 暂无数据</div>
<?php }?>
<div class="kuangdiv">
<?php ?>
    <input type="button" name="" onclick="window.location.href='<?php echo $YCF['curl']?>&tempadd=1'" value="添加" class="btn" style="float:right">
    
</div>

<?php 
}else{ 
    $sybank=explode("-",$YCF["sybank"]);
?>

  <div class="right_center">>&nbsp;添加银行信息</div>

  <div class="indent-r-top">会员中心</div>


<form action="control.php" method="post" onsubmit="return chkbank()"><input type="hidden" name="act" value="bankadd">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:5px;" class="baitab" align="center">
                    <tbody>

                        <tr>
                            <td width="25%"  align="right"><span class="regc">*&nbsp;</span>银行名称：</td>     
                            <td valign="top" align="left">
                            <select name="bankname" id="bankname" class="reginput">
                                <option value="">请选择</option>
                                <?php 
                                foreach ($sybank as $k => $v) {
                                ?>
                                <option value="<?php echo $v?>"><?php echo $v?></option>
                                <?php
                                }
                                ?>
                            </select>
                          </td>
                        </tr>
                        <tr>
                            <td width="25%"  align="right"><span class="regc">*&nbsp;</span>银行卡号：</td>     
                            <td ><input type="text" name="bankcard" id="bankcard" class="reginput"  maxlength="30"></td>
                        </tr>             
                        <tr>
                            <td width="25%"  align="right"><span class="regc">*&nbsp;</span>银行姓名：</td>     
                            <td ><input type="text" name="bankuser" id="bankuser" class="reginput"  maxlength="20"></td>
                        </tr>   
                        <tr>
                            <td width="25%"  align="right">银行地址：</td>     
                            <td ><input type="text" name="bankaddress" id="bankaddress" class="reginput"  maxlength="40"></td>
                        </tr>   
                        <tr>
                            <td width="25%"  align="right">描述：</td>     
                            <td ><textarea name="bankbz" style="margin-top:5px;outline:0px" rows="4" cols="30"  maxlength="80"></textarea></td>
                        </tr>   
                         <tr>
                            <td width="25%"  align="right" style="height:50px;line-height:50px"></td>     
                            <td  style="height:50px;line-height:50px"><input type="submit" value="添加" class="btn">&nbsp;<input type="button" value="返回" class="btn" onclick="history.go(-1)"></td>
                        </tr>        
                    </tbody>
                </table></form>
<?php
}
?>