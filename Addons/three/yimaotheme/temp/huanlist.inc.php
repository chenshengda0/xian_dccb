<?php defined('YIMAOMONEY') or exit('Access denied');?>
<?php 
if(empty($_SESSION["yzmm"])||!strstr('='.$_SESSION["yzmm"],'='.'huanlist')){
    $_GET["m"]="huanlist";
    @require(ROOTTHEMETEMP."yzmm.inc.php");
    exit;    
}
if(!isset($_GET['templist'])){
?>


  <div class="right_center">>&nbsp;货币转换</div>

  <div class="indent-r-top">财务管理</div>

<div class="bt"> <div style="float:right;padding:0px 5px;"><a href="<?php echo $YCF['curl']?>&templist=1" class="lbtn">查看转换记录</a></div></div>


<form action="control.php" method="post" onsubmit="return chkhuan()"><input type="hidden" name="act" value="huan1">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:5px;" class="baitab" align="center">
                    <script type="text/javascript">
                    huanmin=<?php echo $YCF['huanmin']?>;
                    </script>
                    <tbody>
                        <tr>
                            <td width="25%" align="right"></td>     
                            <td style="color:#f00">注：转换最小金额<?php echo $YCF['huanmin']?>起</td>
                        </tr>  

                        <tr>
                            <td width="25%" align="right"><span class="regc">*&nbsp;</span>现金钱包：</td>     
                            <td><?php echo $r['uv012']?></td>
                        </tr> 
                        <tr>
                            <td width="25%" align="right"><span class="regc">*&nbsp;</span>激活币：</td>     
                            <td><?php echo $r['uv015']?></td>
                        </tr>       
                        <tr>
                            <td width="25%" valign="top" align="right"><span class="regc">*&nbsp;</span>转换类型：</td>     
                            <td valign="top"><label><input type="radio" name="ztype" value="0" checked="">现金钱包转激活币</label></td>
                        </tr>                                               
                        <tr>
                            <td width="25%" align="right"><span class="regc">*&nbsp;</span>转换金额：</td>     
                            <td><input type="text" name="jine" id="jine" maxlength="8" class="reginput" onkeyup="value=value.replace(/[^\d]/g,'');"></td>
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

  <div class="right_center">>&nbsp;我的转换记录</div>

  <div class="indent-r-top">财务管理</div>              
<div class="bt"> <div style="float:right;padding:0px 5px;"><a href="<?php echo $YCF['curl']?>"  class="lbtn">返回 >></a></div></div>


<table width="100%" border="0" cellpadding="0" cellspacing="1" style="margin-top:5px;" class="biaotab" align="center">
  <tbody>
    <tr>
        <th>序号</th>     
        <th>转换金额</th>
        <th>转换类型</th>
        <th>转换时间</th>
    </tr>
<?php

$pagecount=10;
$condition=" where uj002='$_username' and uj005=0";
$psql="select count(0) from ymuj $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yim=huanlist&templist=1";    //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymuj $condition order by uj001 desc limit $offset,$pagecount";
$db->yiquery($sql);
$rs=$db->rs;
$ii=0;
foreach ($rs as $k => $v) {
  $ii++;
  $serialid=($pagearr[2]-1)*$pagecount+$ii;
?>
    <tr>
        <td ><?php echo $serialid?></td>  
        <td style="text-align:right"><?php echo formatrmb($v['uj004'])?>&nbsp;</td> 
        <td><?php echo gethuantype($v['uj005'])?></td> 
        <td><?php echo formatdate($v['uj003'])?></td> 
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