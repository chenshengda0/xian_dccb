<?php defined('YIMAOMONEY') or exit('Access denied');?>
<?php
if(empty($_SESSION["yzmm"])||!strstr($_SESSION["yzmm"],'chonglist')){
    $_GET["m"]="chonglist";
    @require(ROOTTHEMETEMP."yzmm.inc.php");
    exit;  
}
if(!isset($_GET['templist'])){
?>



  <div class="right_center">>&nbsp;货币充值</div>

  <div class="indent-r-top">财务管理</div>

<div class="bt"><div style="float:right;padding:0px 5px;"><a href="<?php echo $YCF['curl']?>&templist=1" class="lbtn">查看充值记录</a></div></div>

<form action="control.php" method="post" onsubmit="return chkchong()"><input type="hidden" name="act" value="chong">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:5px;" class="baitab" align="center">
                    <script type="text/javascript">
                    chongmin=<?php echo $YCF['chongmin']?>;
                    </script>
                    <tbody>
                        <tr>
                            <td width="25%" align="right"></td>     
                            <td style="color:#f00">注：充值最小金额<?php echo $YCF['chongmin']?>起</td>
                        </tr>  
                        <tr>
                            <td width="25%" align="right"><span class="regc">*&nbsp;</span>充值金额：</td>     
                            <td><input type="text" name="jine" id="jine" maxlength="8" class="reginput" onkeyup="value=value.replace(/[^\d]/g,'');"></td>
                        </tr>  
                        <tr>
                            <td width="25%" align="right">充值备注：</td>     
                            <td><textarea name="tbz" style="margin-top:5px;outline:0px" rows="4" cols="30"  maxlength="150"></textarea></td>
                        </tr>       
                               
                        <tr>
                            <td width="25%" align="right" style="height:50px;line-height:50px"></td>     
                            <td style="height:50px;line-height:50px"><input type="submit" value="确定" class="btn"></td>
                        </tr>        
                    </tbody>
                </table></form>
<?php 
}else{ 
?>                



  <div class="right_center">>&nbsp;我的充值记录 </div>

  <div class="indent-r-top">财务管理</div>

<div class="bt"><div style="float:right;padding:0px 5px;"><a href="<?php echo $YCF['curl']?>"  class="lbtn">返回 >></a></div></div>

<table width="100%" border="0" cellpadding="0" cellspacing="1" style="margin-top:5px;" class="biaotab" align="center">
  <tbody>
    <tr>
        <th>序号</th>     
        <th>状态</th>
        <th>充值金额</th>
        <th>申请时间</th>
        <th>审核时间</th>
    </tr>
<?php

$pagecount=10;
$condition=" where ug002='$_username'";
$psql="select count(0) from ymug $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yim=chonglist&templist=1";    //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymug $condition order by ug001 desc limit $offset,$pagecount";
$db->yiquery($sql);
$rs=$db->rs;
$ii=0;
foreach ($rs as $k => $v) {
  $ii++;
  $serialid=($pagearr[2]-1)*$pagecount+$ii;
?>
    <tr>
        <td ><?php echo $serialid?></td>
        <td><?php echo getstatutype($v['ug008'],2)?></td> 
        <td style="text-align:right;"><?php echo formatrmb($v['ug005'])?>&nbsp;</td>     
        <td><?php echo formatdate($v['ug003'],0)?></td> 
        <td><?php echo formatdate($v['ug004'],0)?></td> 
    </tr>
<?php
}
?>    
  </tbody>
</table>
<?php if(($pcount/$pagecount)>1){?>
<div class="pagediv"><?php echo $pagearr[0]?></div>
<?php }?>
<?php if($pcount==0){?>
<div class="pagediv" style="text-align:center;color:#E47501">--> 暂无数据</div>

<?php
}
}
?>