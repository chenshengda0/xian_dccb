<?php
defined('YIMAOMONEY') or exit('Access denied');

$fp = fopen(YMROOT."/lock.txt", "w+");
if(flock($fp,LOCK_EX | LOCK_NB))
{
  chkcunkdaojishi();    
  chkqukdaojishi(); 
  fun_xunhuan();
  flock($fp,LOCK_UN);
}
fclose($fp);


if($_POST["submit"]=="确认收款"){
        if($_POST["imgid"]<1) msg_l("o_O ~ 操作失败!",$YCF['curl']);
        $db->yiquery("select * from ymupd where pd001=".$_POST["imgid"],2);
        $rs=$db->rs;
        if(empty($db->rs)) msg_l("o_O ~ 操作失败!",$YCF['curl']);
        if(!empty($db->rs["pd009"]))  msg_l("o_O ~ 操作失败!",$YCF['curl']);


    try{
        $db->begintransaction();

        $db->yiexec("update ymupd set pd009=1,pd010=".time()." where pd001=".$_POST["imgid"]);
        $db->yiexec("update ymugp set gp023=gp023+".$rs["pd004"]." where gp001=".$rs["pd002"]);


    $db->yiquery("select gp005,gp023,gp003,gp002 from ymugp where gp001=".$rs["pd002"],2);
    $gps=$db->rs;
    if($gps["gp005"]==$gps["gp023"]){
        $db->yiexec("update ymuv set uv067=uv067+".$gps["gp005"]." where uv001=".$gps["gp002"]);
        if($gps["gp002"]!=1){
            $row=$member->getuv($gps["gp003"],'uv018,uv040,uv001,uv002');
            if($row["uv040"]==0){
            $db->yiexec("update ymuv set uv068=uv068+".$YCF["jjcunadd"].",uv069=uv069+1 where uv001=".$row["uv018"]);

            $db->yiexec("update ymuv set uv040=1 where uv040=0 and uv001=".$row["uv001"]);

            fun_sj($row["uv018"],$row["uv002"]);
            }
        }
        
    }
           
        if($rs["pd006"]==0){
            $db->yiexec("update ymugq set gq023=gq023+".$rs["pd004"]." where gq001=".$rs["pd003"]);
        }else{
            $db->yiexec("update ymugp set gp023=gp023+".$rs["pd004"]." where gp001=".$rs["pd003"]);
        }

            fun_jdjshouyi($rs["pd002"]);




        $db->committransaction();
             yimao_writelog("确认收款成功,id为".$_POST["imgid"],0);
          msg_l("o_O ~ 确认收款成功 ","yimaomain.php?yimao=gongsipei");      
    }catch(PDOException $e){
        $db->rollbacktransaction();
        a_bck("error");
    }         
}


$edid=getnums($_GET['edid'],0);

if($edid<1) yimao_automsg('操作失败！',YMCURL.'gongsipei',1);

$db->yiquery("select * from ymugp where gp011=1 and gp001=".$edid,2);

if(empty($db->rs)) yimao_automsg('操作失败！',YMCURL.'gongsipei',1);
$pds=$db->rs;
$zuofei=$pds["gp009"];
$arrinfo=array();
$db->yiquery("select * from ymupd where pd003=".$pds["gp001"]." order by pd001 asc");
$rs=$db->rs;

foreach ($rs as $k => $v) {
    $arrinfo[$k]["jine"]=$v["pd004"];
    $arrinfo[$k]["jiaodate"]=date("Y-m-d H:i:s",$v["pd005"]);

    $arrinfo[$k]["pdid"]=$v["pd001"];
    $arrinfo[$k]["img"]=$v["pd007"];
    $arrinfo[$k]["pd008"]=$v["pd008"];
    $arrinfo[$k]["pd009"]=$v["pd009"];
    $arrinfo[$k]["pd005"]=$v["pd005"];
    $arrinfo[$k]["pd010"]=$v["pd010"];
    $arrinfo[$k]["pd011"]=$v["pd011"];
    $arrinfo[$k]["pd002"]=$member->getgpname($v["pd002"]);
    $arrinfo[$k]["pd003"]="公司";


if($v["pd006"]==1){    

    $ldrow=array();
}else{

    $ldrow=$member->getldqname($v["pd003"]);
}

    $arrinfo[$k]["ldid"]=$ldrow["um002"];
    $arrinfo[$k]["ldmobile"]=$ldrow["um010"];
    $arrinfo[$k]["ldqq"]=$ldrow["um015"];



    $ldrow=$member->getldpname($v["pd002"]);    
    $arrinfo[$k]["ldid1"]=$ldrow["um002"];
    $arrinfo[$k]["ldmobile1"]=$ldrow["um010"];
    $arrinfo[$k]["ldqq1"]=$ldrow["um015"];


        $db->yiquery("select * from ymugp where gp001=".$v["pd002"],2);
        $arrinfo[$k]["status"]=getstatutype($db->rs['gp009'],8);
        
        $us=$member->getuserinfo($db->rs["gp002"]);
        $db->yiquery("select * from ymub where ub009=1 and ub002='".$us["uv002"]."'",2);
        
        $arrinfo[$k]["bank1"]=$db->rs["ub004"];
        $arrinfo[$k]["bankuser1"]=$db->rs["ub006"];
        $arrinfo[$k]["bankcard1"]=$db->rs["ub005"];
        $arrinfo[$k]["bankaddress1"]=$db->rs["ub007"];
        $arrinfo[$k]["qq1"]=$us["um015"];
        $arrinfo[$k]["mobile1"]=$us["um010"];
    

        $db->yiquery("select * from ymuii",2);
        $arrinfo[$k]["bank"]=$db->rs["ui001"];
        $arrinfo[$k]["bankuser"]=$db->rs["ui003"];
        $arrinfo[$k]["bankcard"]=$db->rs["ui002"];
        $arrinfo[$k]["bankaddress"]=$db->rs["ui004"];
        $arrinfo[$k]["mobile"]=$db->rs["ui005"];
        $arrinfo[$k]["qq"]=$db->rs["ui006"];
        $arrinfo[$k]["weixin"]=$db->rs["ui007"];
    
}
echo '<h3>配对信息</h3>';
echo '<div class="main-r-body"><form method="post" action="'.geturl().'">
		<div class="main-r-item"><a href="'.YMCURL.'gongsipei" class="yimaoabtn">返回</a></div>
	
	<table class="yimaoregtab yimaotab">';
?>    
<?php 
if(count($arrinfo)==0){
?>
                        <tr>
                            <td width="25%" valign="top" align="center" colspan="4" style="background:#E5E5E5;font-size:16px;color:blue">暂无配对信息</td>                            
                        </tr>
 
 
<?php

}else{

?>
<?php foreach ($arrinfo as $k => $v) {

?>
                        <tr>
                            <td width="25%" valign="top" align="left" colspan="4" style="background:#2D2E27;font-size:16px;color:#f00">&nbsp;&nbsp;配对目标<?php echo $k+1?></td>                            
                        </tr>
                        <tr>
                            <td width="25%" valign="top" align="right">RMB：</td>     
                            <td valign="top" width="170" align="left">&nbsp;<?php echo $v['jine']?></td>
                            <td width="25%" valign="top" align="right">收款人账户：</td>     
                            <td valign="top">&nbsp;<?php echo $v['bank']?>&nbsp;</td>                            
                        </tr>
                        <tr>
                            <td width="25%" valign="top" align="right">收款人姓名：</td>     
                            <td valign="top" width="100" align="left">&nbsp;<?php echo $v['bankuser']?></td>
                            <td width="25%" valign="top" align="right">收款人帐号：</td>     
                            <td valign="top">&nbsp;<?php echo $v['bankcard']?>&nbsp;</td>                            
                        </tr>  
                        <tr>
                            <td width="25%" valign="top" align="right">收款人编号：</td>     
                            <td valign="top" width="100" align="left">&nbsp;<?php echo $v["pd003"]?></td>
                            <td width="25%" valign="top" align="right">收款人开户地址：</td>     
                            <td valign="top">&nbsp;<?php echo $v['bankaddress']?>&nbsp;</td>                            
                        </tr>  
                        <tr>
                            <td width="25%" valign="top" align="right">收款人QQ：</td>     
                            <td valign="top" width="100" align="left">&nbsp;<?php echo $v['qq']?></td>
                            <td width="25%" valign="top" align="right">收款人电话：</td>     
                            <td valign="top">&nbsp;<?php echo $v['mobile']?>&nbsp;</td>                            
                        </tr> 
                        <tr>
                            <td width="25%" valign="top" align="right">收款人上级帐号：</td>     
                            <td valign="top" width="100" align="left">&nbsp;<?php echo $v['ldid']?></td>
                            <td width="25%" valign="top" align="right">收款人上级电话：</td>     
                            <td valign="top">&nbsp;<?php echo $v['ldmobile']?>&nbsp;</td>                            
                        </tr> 
                        <tr>
                            <td width="25%" valign="top" align="right">收款人上级QQ：</td>     
                            <td valign="top" width="100" align="left">&nbsp;<?php echo $v['ldqq']?></td>
                            <td width="25%" valign="top" align="right"></td>     
                            <td valign="top">&nbsp;</td>                            
                        </tr> 
                        <tr>
                            <td width="25%" valign="top" align="right"></td>     
                            <td valign="top" width="100" align="left"></td>
                            <td width="25%" valign="top" align="right"></td>     
                            <td valign="top">&nbsp;</td>                            
                        </tr>   

                        <tr>
                            <td width="25%" valign="top" align="right" style="color:#04AE0A">付款人账户：</td>     
                            <td valign="top" width="100" align="left" style="color:#04AE0A">&nbsp;<?php echo $v['bank1']?></td>
                            <td width="25%" valign="top" align="right" style="color:#04AE0A">付款人姓名：</td>     
                            <td valign="top" style="color:#04AE0A">&nbsp;<?php echo $v['bankuser1']?>&nbsp;</td>                            
                        </tr>  
                        <tr>
                            <td width="25%" valign="top" align="right" style="color:#04AE0A">付款人帐号：</td>     
                            <td valign="top" width="100" align="left" style="color:#04AE0A">&nbsp;<?php echo $v['bankcard1']?>&nbsp;</td>
                            <td width="25%" valign="top" align="right" style="color:#04AE0A">付款人开户地址：</td>     
                            <td valign="top" style="color:#04AE0A">&nbsp;<?php echo $v['bankaddress1']?>&nbsp;</td>                            
                        </tr>    
                        <tr>
                            <td width="25%" valign="top" align="right" style="color:#04AE0A">付款人编号：</td>     
                            <td valign="top" width="100" align="left" style="color:#04AE0A">&nbsp;<?php echo $v['pd002']?></td>
                            <td width="25%" valign="top" align="right" style="color:#04AE0A"></td>     
                            <td valign="top" style="color:#04AE0A">&nbsp;</td>                            
                        </tr>                          
                        <tr>
                            <td width="25%" valign="top" align="right" style="color:#04AE0A">付款人QQ：</td>     
                            <td valign="top" width="100" align="left" style="color:#04AE0A">&nbsp;<?php echo $v['qq1']?></td>
                            <td width="25%" valign="top" align="right" style="color:#04AE0A">付款人电话：</td>     
                            <td valign="top" style="color:#04AE0A">&nbsp;<?php echo $v['mobile1']?>&nbsp;</td>                            
                        </tr>
                        <tr>
                            <td width="25%" valign="top" align="right" style="color:#04AE0A">付款人上级帐号：</td>     
                            <td valign="top" width="100" align="left" style="color:#04AE0A">&nbsp;<?php echo $v['ldid1']?></td>
                            <td width="25%" valign="top" align="right" style="color:#04AE0A">付款人上级电话：</td>     
                            <td valign="top" style="color:#04AE0A">&nbsp;<?php echo $v['ldmobile1']?>&nbsp;</td>      

                        </tr>  
                 <tr>
                            <td width="25%" valign="top" align="right" style="color:#04AE0A">付款人上级QQ：</td>     
                            <td valign="top" width="100" align="left" style="color:#04AE0A">&nbsp;<?php echo $v['ldqq1']?></td>
                            <td width="25%" valign="top" align="right" style="color:#04AE0A"></td>     
                            <td valign="top" style="color:#04AE0A">&nbsp;</td>                            
                        </tr> 
                        <tr>
                            <td width="25%" valign="top" align="right">交易日期：</td>     
                            <td valign="top" width="100" align="left">&nbsp;<?php echo $v['jiaodate']?></td>
                            <td width="25%" valign="top" align="right">倒计时：</td>     
         
         
<?php if($v["pd009"]==0){
    if(empty($v["pd008"])) 
        $time=$v["pd005"];
    else
        $time=$v["pd008"];
    ?>

                                   <td valign="top"><div class="J_left_time<?php echo $k?> dsj" rel="<?php echo strtotime("+".$YCF["jjqkok"]." minute",$time)-time()?>" id="dsj" >&nbsp;
                                <span class="s day">--</span>
                                <span class="l">天</span>
                                <span class="s hour">--</span>
                                <span class="l">时</span>
                                <span class="s min">--</span>
                                <span class="l">分</span>
                                <span class="s sec">--</span>
                                <span class="l">秒</span>
                            </div></td>   
                                          
<script type="text/javascript">
    var leftTimeActInv<?php echo $k?> = null;
    jQuery(function(){
        leftTimeAct<?php echo $k?>();
    });
    function leftTimeAct<?php echo $k?>(){
        clearTimeout(leftTimeActInv<?php echo $k?>);
        $(".J_left_time<?php echo $k?>").each(function(){
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
                $(this).html('时间已结束');
            }
        });
        
        leftTimeActInv<?php echo $k?> = setTimeout(function(){
            leftTimeAct<?php echo $k?>();
        },1000);
    }
</script>   
<?php }?>

                                        
                        </tr>                  
                        <tr>
                            <td width="25%" valign="top" align="right">状态：</td>     
                            <td valign="top" width="100" align="left">&nbsp;
 <?php 
     echo getcunstatus1(array("pd009"=>$v["pd009"],"pd008"=>$v["pd008"],"pd011"=>$v["pd011"]));   
?>    
                            </td>
                            <td width="25%" valign="top" align="right">上传凭证：</td>     
                            <td valign="top">&nbsp;
<?php    if(!empty($v["img"])){

?>
&nbsp;<a href="javascript:artload1('<img src=<?php echo YMURL.'yimaoupfile/'.$v["img"]?>>');">查看上传</a>
<?php
    }else{
?>
未上传图片
<?php        
    }
    if($v["pd009"]==0&&$v["pd011"]==0){
  ?>
<form action="<?php echo $YCF["curl"]?>" method="post" >

    <input type="hidden" name="imgid" size="30" value="<?php echo $v["pdid"]?>">
    &nbsp;<input type="submit" name="submit" style="color:#000" value="确认收款" onclick="return confirm('您确认了吗?')">
</form>
  <?php      
    }


?>   
                            </td>                            
                        </tr>                                                                                            

                                                                 
<?php
}}?>
<?php

										
echo '		</table>
	</form></div>';		

?>
