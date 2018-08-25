<?php defined('YIMAOMONEY') or exit('Access denied');?>
<?php
$fp = fopen("lock.txt", "w+");
if(flock($fp,LOCK_EX | LOCK_NB))
{
  chkcunkdaojishi();    
  chkqukdaojishi();     
  fun_xunhuan();
  flock($fp,LOCK_UN);
}
fclose($fp);

if(!isset($_GET['templist'])){
        $rr=$config->getpwd();
    $db->yiquery("select * from ymub where ub002='$_username'",2);
    if(empty($db->rs)){
         $_SESSION["gourl"]="home.php";
         msg_l("o_O ~ 您还没有填写银行信息，请先填写!",$backurl."/home.php?yim=mybank");
    }else{
        $address=$db->rs;
    }           
?>
  <div class="right_center">>&nbsp;获取帮助</div>

  <div class="indent-r-top">财务管理</div>

<div class="bt"><div style="text-align:center;padding:0px 5px;"><a href="<?php echo $YCF['curl']?>&templist=1" style="font-size:20px;" class="lbtn">查看获取帮助记录</a></div></div>
<form action="control.php" method="post" onsubmit="return chkqk()"><input type="hidden" name="act" value="quqian">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:5px;" class="baitab" align="center">
                    <script type="text/javascript">
                    qkmin=<?php echo $YCF['qkmin']?>;
                   qkbs=<?php echo $YCF['qkbs']?>;
                    </script>
                    <tbody>

                        <tr>
                            <td width="25%" valign="top" align="right"  style="color:#f00;font-size:16px">注：</td>     
                            <td valign="top" style="color:#f00;font-size:16px">这是金融互助的社区，请注意：只用您非关键的资金参与！</td>
                        </tr>  
                        <tr>
                            <td width="25%" valign="top" align="right" style="color:#f00;font-size:16px">默认银行：</td>     
                            <td valign="top" style="color:#f00;font-size:16px"><?php echo $address["ub004"]?>&nbsp;&nbsp;&nbsp;&nbsp;帐号：<?php echo $address["ub005"]?>&nbsp;&nbsp;&nbsp;&nbsp;姓名：<?php echo $address["ub006"]?></td>
                        </tr> 

                        <tr>
                            <td width="25%" valign="top" align="right"><span class="regc">*&nbsp;</span>获取帮助金额：</td>     
                            <td ><input type="text" name="jine" id="jine" maxlength="8" class="reginput" onkeyup="value=value.replace(/[^\d]/g,'');" ></td>
                        </tr>  
                        <tr>
                            <td width="25%" valign="top" align="right"><span class="regc">*&nbsp;</span><?php echo $rr["rgpwd2"]?>：</td>     
                            <td valign="top"><input type="password" name="pwd" id="pwd" class="reginput"  maxlength="30"></td>
                        </tr>        
                               
                        <tr>
                            <td width="25%" valign="top" align="right" style="height:50px;line-height:50px"></td>     
                            <td valign="top" style="height:50px;line-height:50px"><input type="submit"  value="确定" class="btn"></td>
                        </tr>        
                    </tbody>
                </table></form>
<?php 
}elseif($_GET['templist']==1){ 

if($YCF["jjquopen"]==0){

    if(isset($_GET['qxid'])){
    if($_GET['qxid']<1)  msg_l("o_O ~ 操作失败!",$YCF['curl']);


        try{
        $db->begintransaction();

    $db->yiquery("select * from ymugq where gq001=".$_GET['qxid']." and gq012=0 and gq023=0");
    if(empty($db->rs))  msg_l("o_O ~ 操作失败!",$YCF['curl']);
    $rs=$db->rs;
    foreach ($rs as $k => $v) {
        $db->yiexec("update ymugq set gq009=3,gq010=".time()." where gq001=".$v["gq001"]." and  gq012=0 and gq023=0");

        yimao_writeaccount(array($v["gq002"],"0",0,time(),24,($v["gq005"]),"获取帮助编号".$v["gq013"])); 
    }


    

        $db->committransaction();

        msg_l("o_O ~ 取消成功!",$YCF['curl']);
    }catch(PDOException $e){
        $db->rollbacktransaction();
        a_bck("error");
    }   
        
}   
}     
?>              
  <div class="right_center">>&nbsp;我的获取帮助记录 </div>

  <div class="indent-r-top">财务管理</div>

<div class="bt"><div style="float:right;padding:0px 5px;"><a href="<?php echo $YCF['curl']?>"  class="lbtn">返回 >></a></div></div>

<table width="100%" border="0" cellpadding="0" cellspacing="1" style="margin-top:5px;" class="biaotab" align="center">
  <tbody>
    <tr>
        <th>序号</th>     
        <th valign="top" width="170">获取帮助编号</th>
        <th valign="top" width="150">获取帮助时间</th>
        <th valign="top">获取帮助金额</th>
        <th valign="top">已配对金额</th>
        <th valign="top">状态</th>
        <th valign="top">配对结束日期</th>
        <th valign="top">操作</th>
    </tr>
<?php

$pagecount=10;
$condition=" where gq002=$_userid";
$psql="select count(0) from ymugq $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yim=qukuan&templist=1";    //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymugq $condition order by gq001 desc limit $offset,$pagecount";
$db->yiquery($sql);
$rs=$db->rs;
$ii=0;
foreach ($rs as $k => $v) {
  $ii++;
  $serialid=($pagearr[2]-1)*$pagecount+$ii;

?>
    <tr>
        <td ><?php echo $serialid?></td>
        <td valign="top"><?php echo $v['gq013']?></td>
        <td valign="top"><?php echo formatdate($v['gq004'])?></td>
        <td valign="top"><?php echo formatrmb($v['gq005'])?></td>   
        <td valign="top"><?php echo formatrmb($v['gq012'])?></td> 
        <td valign="top"><?php echo getpeistatus1(array($v["gq023"],$v["gq005"],$v['gq009'],$v['gq025']))?></td>
        <td valign="top"><?php echo formatdate($v['gq010'])?></td>
        <td valign="top"><a href="<?php echo $YCF['curl']?>&templist=2&pdid=<?php echo $v["gq001"]?>">查看</a>
        &nbsp;
<?php 
if($YCF["jjquopen"]==0){
if($v["gq012"]==0&&$v["gq009"]==0&&$v["gq023"]==0){?>
<a href="<?php echo $YCF['curl']?>&templist=1&qxid=<?php echo $v["gq001"]?>"  onclick="return boxcheck('您确定要取消吗?','取消正在执行中,请耐心等待！');" >取消</a>
<?php }}?>
        </td> 
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
}elseif($_GET['templist']==2){

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
        $db->yiexec("update ymugp set gp025=".time()." where gp001=".$rs["pd002"]);        
        $db->yiexec("update ymuv set uv067=uv067+".$gps["gp005"]." where uv001=".$gps["gp002"]);
        if($gps["gp002"]!=1){
            $row=$member->getuv($gps["gp003"],'uv018,uv040,uv001,uv002');
            if($row["uv040"]==0){
            $db->yiexec("update ymuv set uv068=uv068+".$YCF["jjcunadd"].",uv069=uv069+1 where  uv040=0 and  uv001=".$row["uv018"]);
            $db->yiexec("update ymuv set uv040=1 where uv040=0 and uv001=".$row["uv001"]);
            fun_sj($row["uv018"],$row["uv002"]);
            }
        }
        
    }

        if($rs["pd006"]==0){
            $db->yiexec("update ymugq set gq023=gq023+".$rs["pd004"]." where gq001=".$rs["pd003"]);
            $db->yiquery("select * from ymugq where gq001=".$rs["pd003"],2);
            $gqs=$db->rs;
            if($gqs["gq005"]==$gqs["gq023"]){
                $db->yiexec("update ymugq set gq025=".time()." where gq001=".$rs["pd003"]);
            }                
        }else{
            $db->yiexec("update ymugp set gp023=gp023+".$rs["pd004"]." where gp001=".$rs["pd003"]);
            $db->yiquery("select * from ymugp where gp001=".$rs["pd003"],2);
            $gqs=$db->rs;
            if($gqs["gp005"]==$gqs["gp023"]){
                $db->yiexec("update ymugp set gp025=".time()." where gp001=".$rs["pd003"]);
            }    
        }
        
          fun_jdjshouyi($rs["pd002"]);     



        $db->committransaction();
          yimao_writelog("确认收款成功,id为".$_POST["imgid"],0);
          msg_l("o_O ~ 确认收款成功 ","home.php?yim=qukuan&templist=1");    
    }catch(PDOException $e){
        $db->rollbacktransaction();
        a_bck("error");
    }  
              
}

if(!isset($_GET["pdid"])) locationurl("home.php?cunkuan&templist=1"); 
if($_GET["pdid"]<1) locationurl("home.php?cunkuan&templist=1"); 

    $db->yiquery("select * from ymugq where gq002=$_userid and gq001=".$_GET["pdid"],2);
    if(empty($db->rs)) locationurl("home.php?cunkuan&templist=1");
    $pds=$db->rs;
    $zuofei=$pds["gq009"];   
    $arrinfo=array();
    $db->yiquery("select * from ymupd where pd003=".$pds["gq001"]." order by pd001 asc");
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
 
  if($v["pd006"]==1){    
    $arrinfo[$k]["pd003"]="公司";
}else{
    $arrinfo[$k]["pd003"]=$member->getgqname($v["pd003"]);  
}
      
   
if($v["pd006"]==1){    

    $ldrow=array();



}else{
 
    $ldrow=$member->getldqname($v["pd003"]);
}

     $arrinfo[$k]["ldid"]=$ldrow["um002"];
    $arrinfo[$k]["ldmobile"]=$ldrow["um010"];
    $arrinfo[$k]["ldqq"]=$ldrow["um015"];
 
        $db->yiquery("select * from ymugq where gq001=".$v["pd003"],2);       
        $us=$member->getuserinfo($db->rs["gq002"]);
        $db->yiquery("select * from ymub where ub009=1 and ub002='".$us["uv002"]."'",2);
        
        $arrinfo[$k]["bank"]=$db->rs["ub004"];
        $arrinfo[$k]["bankuser"]=$db->rs["ub006"];
        $arrinfo[$k]["bankcard"]=$db->rs["ub005"];
        $arrinfo[$k]["bankaddress"]=$db->rs["ub007"];
        $arrinfo[$k]["mobile"]=$us["um010"];
        $arrinfo[$k]["qq"]=$us["um015"];
        $arrinfo[$k]["weixin"]=$us["um014"];
   
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
        $arrinfo[$k]["mobile1"]=$us["um010"];
        $arrinfo[$k]["qq1"]=$us["um015"];          
}      
?>

  <div class="right_center">>&nbsp;获取帮助配对信息</div>

  <div class="indent-r-top">财务中心</div>

<div class="bt"><div style="float:right;padding:0px 20px;"><a href="javascript:history.go(-1)"  class="lbtn">返回 >></a></div>&nbsp;&nbsp;</div>
                <table width="100%" border="1" style="margin-left:12px;margin-top:10px;border:1px solid #ccc" cellpadding="0" cellspacing="0" style="margin-top:5px;" class="baitab" align="center">
                    <tbody>

<?php 
if(count($arrinfo)==0){
    if($zuofei==3){
 ?>
                         <tr>
                            <td width="25%" valign="top" align="center" colspan="4" style="background:#552A15;font-size:16px;color:#F0E531">已取消</td>                            
                        </tr>
 <?php 

    }else{
?>
                        <tr>
                            <td width="25%" valign="top" align="center" colspan="4" style="background:#E5E5E5;font-size:16px;color:blue">暂无配对信息</td>                            
                        </tr>
<?php
    }
}

foreach ($arrinfo as $k => $v) {
?>
                        <tr>
                            <td width="25%" valign="top" align="left" colspan="4" style="background:#E5E5E5;font-size:16px;color:blue">&nbsp;&nbsp;配对目标<?php echo $k+1?></td>                            
                        </tr>
                        <tr>
                            <td width="25%" valign="top" align="right">收款人编号：</td>     
                            <td valign="top" width="170" align="left">&nbsp;<?php echo $v["pd003"]?></td>
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
                            <td width="25%" valign="top" align="right">RMB：</td>     
                            <td valign="top" width="100" align="left">&nbsp;<?php echo $v['jine']?></td>
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
                            <td width="25%" valign="top" align="right"></td>     
                            <td valign="top" width="100" align="left">&nbsp;</td>
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
                            <td width="25%" valign="top" align="right"  style="color:#04AE0A">付款人帐号：</td>     
                            <td valign="top" width="100" align="left"  style="color:#04AE0A">&nbsp;<?php echo $v['bankcard1']?>&nbsp;</td>
                            <td width="25%" valign="top" align="right" style="color:#04AE0A">付款人开户地址：</td>     
                            <td valign="top" style="color:#04AE0A">&nbsp;<?php echo $v['bankaddress1']?>&nbsp;</td>                            
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
                            <td valign="top" width="100" align="left">&nbsp;<?php  echo getcunstatus1(array("pd009"=>$v["pd009"],"pd008"=>$v["pd008"],"pd011"=>$v["pd011"]));?></td>
                            <td width="25%" valign="top" align="right">上传凭证：</td>     
                            <td valign="top">
<?php 
    if(!empty($v["img"])){
?>
&nbsp;<a href="javascript:artload1('<img src=<?php echo YMURL.'yimaoupfile/'.$v["img"]?>>');">查看上传</a>
<?php
    }else{
?>
未上传图片
<?php        
    }
    if($v["pd009"]==0&&$v["pd011"]==0&&$v["pd011"]==0&&!empty($v["img"])){
  ?>
<form action="home.php?yim=qukuan&templist=2&pdid=<?php echo $_GET["pdid"]?>" method="post" >

    <input type="hidden" name="imgid" size="30" value="<?php echo $v["pdid"]?>">
    &nbsp;<input type="submit" name="submit" value="确认收款" onclick="return confirm('您确认了吗?')">
</form>
  <?php      
    }
?>
                            </td>   
                        </tr>
<?php
}?>                        
                    </tbody>
                </table>
<?php }?>