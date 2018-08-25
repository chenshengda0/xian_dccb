<?php defined('YIMAOMONEY') or exit('Access denied');?>
<?php
if($YCF["tqopen"]){
    locationurl('home.php');
}



if(!isset($_GET['templist'])){
    $xianshi="";
    $db->yiquery("select * from ymuj where uj002='$_username' order by uj001 desc limit 1",2);
    if(!empty($db->rs)){
        $kai=$db->rs["uj003"];
        $cha=(time()-$db->rs["uj003"])/(24*3600);

        if($cha<$YCF["jjtx"][0]){
           $xianshi="disabled";
        }

    }


?>

  <div class="right_center">>&nbsp;小金库转换</div>

  <div class="indent-r-top">财务管理</div>

<div class="bt"> <div style="float:right;padding:0px 5px;"><a href="<?php echo $YCF['curl']?>&templist=1" class="lbtn">查看小金库转换记录</a></div></div>

<form action="control.php" method="post" onsubmit="return chkhuan1()"><input type="hidden" name="act" value="huan">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:5px;" class="baitab" align="center">
                    <tbody>

                                         <tr>
                                            <td width="25%" valign="top" align="right"  style="color:#f00">注：</td>             
                                            <td align="left" style="color:#f00" height="25" >只限转换小金库金额的<?php echo $YCF['jjtx'][1]?>%</td>          
                                        </tr>                          


                        <tr>
                            <td width="25%" valign="top" align="right"><span class="regc">*&nbsp;</span>现金钱包：</td>     
                            <td valign="top"><?php echo $r['uv012']?></td>
                        </tr>  
                        <tr>
                            <td width="25%" valign="top" align="right"><span class="regc">*&nbsp;</span>小金库：</td>     
                            <td valign="top"><?php echo $r['uv065']+$r['uv064']?>(冻结金额：<?php echo $r['uv064']?>)</td>
                        </tr>    
                       
                        <tr>
                            <td width="25%" valign="top" align="right"><span class="regc">*&nbsp;</span>转换类型：</td>     
                            <td valign="top"><label><input type="radio" name="ztype" value="1" checked="">小金库转现金钱包</label></td>
                        </tr>        
                        <tr>
                            <td width="25%" valign="top" align="right"><span class="regc">*&nbsp;</span>转换金额：</td>     
                            <td valign="top"><input type="text" name="jine" id="jine" maxlength="10" class="reginput" ></td>
                        </tr>         
                         
                        <tr>
                            <td width="25%" valign="top" align="right" style="height:50px;line-height:50px"></td>     
                            <td valign="top" style="height:50px;line-height:50px"><input type="submit" value="确定" class="btn" <?php echo $xianshi?>></td>
                        </tr>   

                <?php 
if($xianshi!=""){
    ?>
     
                        <tr>
                            <td width="25%" valign="top" align="right" style="height:50px;line-height:50px"></td>     
                            <td valign="top" style="height:50px;line-height:50px;color:#f00"><div class="J_left_time dsj" rel="<?php echo strtotime("+".$YCF["jjtx"][0]." day",$kai)-time()?>" id="dsj" >距离下一次转换时间还有：
                                <span class="s day">--</span>
                                <span class="l">天</span>
                                <span class="s hour">--</span>
                                <span class="l">时</span>
                                <span class="s min">--</span>
                                <span class="l">分</span>
                                <span class="s sec">--</span>
                                <span class="l">秒</span>
                            </div></td>
                        </tr>   
                        <?php }?>                             
                    </tbody>
                </table></form>


                <?php 
if($xianshi!=""){
    ?>

<script type="text/javascript">
    var leftTimeActInv = null;
    jQuery(function(){
        leftTimeAct();
    });
    function leftTimeAct(){
        clearTimeout(leftTimeActInv);
        $(".J_left_time").each(function(){
            var leftTime = parseInt($(this).attr("rel"));
            if(leftTime > 0)
            {
                var day  =  parseInt(leftTime / 24 /3600);
                 var hour = parseInt((leftTime % (24 *3600)) / 3600);
                //var hour = parseInt(leftTime / 3600);
                var min = parseInt((leftTime % 3600) / 60);
                var sec = parseInt((leftTime % 3600) % 60);
                $(this).find(".day").html((day));
                //$(this).find(".day").hide();
                $(this).find(".hour").html((hour));
                $(this).find(".min").html((min));
                $(this).find(".sec").html((sec));
                leftTime--;
                $(this).attr("rel",leftTime);
            }
            else{
                $(this).css("background","none");
                $(this).html('可以转换');
            }
        });
        
        leftTimeActInv = setTimeout(function(){
            leftTimeAct();
        },1000);
    }
</script>  
    <?php
}
                ?>
<?php 
}else{ 
?>  

  <div class="right_center">>&nbsp;小金库转换记录</div>

  <div class="indent-r-top">财务管理</div>              
<div class="bt"> <div style="float:right;padding:0px 5px;"><a href="<?php echo $YCF['curl']?>"  class="lbtn">返回 >></a></div></div>

<table width="100%" border="0" cellpadding="0" cellspacing="1" style="margin-top:5px;" class="biaotab" align="center">
  <tbody>
    <tr>
        <th>序号</th>     
        <th valign="top">转换金额</th>
        <th valign="top">转换类型</th>
        <th valign="top">转换时间</th>
    </tr>
<?php

$pagecount=10;
$condition=" where uj002='$_username' and uj005=1";
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
        <td valign="top" style="text-align:right"><?php echo formatrmb($v['uj004'])?>&nbsp;</td> 
        <td valign="top"><?php echo gethuantype($v['uj005'])?></td> 
        <td valign="top"><?php echo formatdate($v['uj003'])?></td> 
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