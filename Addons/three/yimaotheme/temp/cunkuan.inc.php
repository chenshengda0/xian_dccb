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
  <div class="right_center">>&nbsp;提供帮助</div>

  <div class="indent-r-top">财务管理</div>

<div class="bt"><div style="text-align:center;padding:0px 5px;"><a href="<?php echo $YCF['curl']?>&templist=1" style="font-size:20px;"  class="lbtn">查看提供帮助记录</a></div></div>
<form action="control.php" method="post" onsubmit="return chkcq()"><input type="hidden" name="act" value="cunqian">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:5px;" class="baitab" align="center">
                    <script type="text/javascript">
                    cqmin=<?php echo $YCF['cqmin']?>;
                 cqbs=<?php echo $YCF['cqbs']?>;
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
                            <td width="25%" valign="top" align="right"><span class="regc">*&nbsp;</span>提供帮助金额：</td>     
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
if($YCF["jjcunopen"]==0){

    if(isset($_GET['qxid'])){
    if($_GET['qxid']<1)  msg_l("o_O ~ 操作失败!",$YCF['curl']);


        try{
        $db->begintransaction();

    $db->yiquery("select * from ymugp where gp001=".$_GET['qxid']." and gp012=0");
    if(empty($db->rs))  msg_l("o_O ~ 操作失败!",$YCF['curl']);
    $rs=$db->rs;
    foreach ($rs as $k => $v) {
        $db->yiexec("update ymugp set gp009=3,gp010=".time()." where gp001=".$v["gp001"]." and gp012=0");

        
          $jin=$v["gp019"]+$v["gp005"];
          $jk=$v["gp021"];
          $bz='存款编号'.$v["gp013"];
          if($jin>0)
          yimao_writeaccount(array($v["gp002"],"1",1,time(),17,$jin,'存款编号'.$v["gp013"]));
          if($jk>0){
             yimao_writeaccount(array($v["gp002"],"1",5,time(),17,$jk,'存款编号'.$v["gp013"]));

          }

              $db->yiquery("select ud002,sum(ud006) ud006,sum(ud009) ud009 from ymud where ud010=0 and ud005!=51 and ud008=".$v["gp001"]." group by ud002");
              $all=$db->rs;
              $db->yipre("update ymud set ud010=1 where ud010=0 and ud005!=51 and ud008=:t0",4,array(array(':t0'),array($v["gp001"])));          
          foreach ($all as $kk => $vv) {
                  $us=$member->getuserinfo($vv["ud002"]);
                  $jin=($vv["ud006"]-$vv["ud009"]);
                 
                  if($jin>0)
                    yimao_writeaccount(array($vv["ud002"],"1",2,time(),17,$jin,$bz));
                yimao_writeaccount(array($vv["ud002"],"1",5,time(),17,$vv["ud009"],$bz));


             //  if($vv["ud009"]>0){
             //     yimao_writeaccount(array($v["gp002"],"0",7,time(),18,$vv["ud009"],''));
              // }

          }   
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
  <div class="right_center">>&nbsp;我的提供帮助记录 </div>

  <div class="indent-r-top">财务管理</div>

<div class="bt"><div style="float:right;padding:0px 5px;"><a href="<?php echo $YCF['curl']?>"  class="lbtn">返回 >></a></div></div>

<table width="100%" border="0" cellpadding="0" cellspacing="1" style="margin-top:5px;" class="biaotab" align="center">
  <tbody>
    <tr>
        <th>序号</th>     
        <th valign="top" width="170">提供帮助编号</th>
        <th valign="top" width="150">提供帮助时间</th>
        <th valign="top">提供帮助金额</th>
        <th valign="top">已配对金额</th>
        <th valign="top">状态</th>
        <th valign="top">配对结束日期</th>
        <th valign="top">操作</th>
    </tr>
<?php

$pagecount=10;
$condition=" where gp002=$_userid  and gp011=0";
$psql="select count(0) from ymugp $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yim=cunkuan&templist=1";    //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymugp $condition order by gp001 desc limit $offset,$pagecount";
$db->yiquery($sql);
$rs=$db->rs;
$ii=0;
foreach ($rs as $k => $v) {
  $ii++;
  $serialid=($pagearr[2]-1)*$pagecount+$ii;
?>
    <tr>
        <td ><?php echo $serialid?></td>
        <td valign="top"><?php echo $v['gp013']?></td>
        <td valign="top"><?php echo formatdate($v['gp004'])?></td>
        <td valign="top"><?php echo formatrmb($v['gp005'])?></td>   
        <td valign="top"><?php echo formatrmb($v['gp012'])?></td> 
        <td valign="top"><?php 
        // if($v['gp009']==0){
        //     $cha=(time()-$v['gp004'])/(24*3600);
        //     if($cha>$YCF['pdday']){
        //         echo "配对过期";
             ?>
<!--              ,<a href="home.php?yim=shents&gpid=<?php //echo $v['gp001']?>" style="color:#f00;text-decoration:underline">申请投诉</a> -->
             <?php
        //     }else{
        //         echo getstatutype($v['gp009'],8);
        //     }
        // }else{
                    echo getpeistatus(array($v["gp023"],$v["gp005"],$v['gp009'],$v["gp025"]));
        // }
        ?></td> 
        <td valign="top"><?php echo formatdate($v['gp010'])?></td>  
        <td valign="top"><a href="<?php echo $YCF['curl']?>&templist=2&pdid=<?php echo $v["gp001"]?>">查看</a>
        &nbsp;
<?php 
if($YCF["jjcunopen"]==0){
if($v["gp012"]==0&&$v["gp009"]==0){?>
<a href="<?php echo $YCF['curl']?>&templist=1&qxid=<?php echo $v["gp001"]?>"  onclick="return boxcheck('您确定要取消吗?','取消正在执行中,请耐心等待！');" >取消</a>
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

if($_POST["submit"]=="上传图片"){
        if($_POST["imgid"]<1) msg_l("o_O ~ 操作失败!",$YCF['curl']);
        $db->yiquery("select * from ymupd where pd001=".$_POST["imgid"],2);
        if(empty($db->rs)) msg_l("o_O ~ 操作失败!",$YCF['curl']);
        if(!empty($db->rs["pd007"]))  msg_l("o_O ~ 操作失败!",$YCF['curl']);

        if(!empty($_FILES["simg"]["name"])){
            if(strpos($_FILES["simg"]["name"],"exe")===false&&strpos($_FILES["simg"]["name"],"bat")===false){
                if ((($_FILES["simg"]["type"] == "image/gif") || ($_FILES["simg"]["type"] == "image/jpeg") || ($_FILES["simg"]["type"] == "image/png") || ($_FILES["simg"]["type"] == "image/pjpeg"))){
                      $filename=random_filename();
                      $cimg =$filename.".jpg";
  
                      move_uploaded_file($_FILES["simg"]["tmp_name"],YMROOT."/yimaoupfile/".$cimg);
                }else{
                    msg_l("o_O ~ 只能上传gif或者jpg!",$YCF['curl']);
                }
            }else{
                msg_l("o_O ~ 只能上传gif或者jpg!",$YCF['curl']);
            }
        }else{
            msg_l("o_O ~ 请选择上传图片!",$YCF['curl']);
        }

        $db->yiexec("update ymupd set pd007='".$cimg."' where pd001=".$_POST["imgid"]);
  yimao_writelog("上传图片",0);
  msg_l("o_O ~ 上传成功 ",""); 
}

if($_POST["submit"]=="确认汇款"){
        if($_POST["imgid"]<1) msg_l("o_O ~ 操作失败!",$YCF['curl']);
        $db->yiquery("select * from ymupd where pd001=".$_POST["imgid"],2);
        if(empty($db->rs)) msg_l("o_O ~ 操作失败!",$YCF['curl']);
        if(!empty($db->rs["pd007"])&&!empty($db->rs["pd008"]))  msg_l("o_O ~ 操作失败!",$YCF['curl']);


        $db->yiexec("update ymupd set pd008=".time()." where pd001=".$_POST["imgid"]);
  yimao_writelog("确认汇款",0);
  msg_l("o_O ~ 汇款成功 ",""); 
}

if(!isset($_GET["pdid"])) locationurl("home.php?cunkuan&templist=1"); 
if($_GET["pdid"]<1) locationurl("home.php?cunkuan&templist=1"); 

    $db->yiquery("select * from ymugp where gp002=$_userid and gp001=".$_GET["pdid"],2);
    if(empty($db->rs)) locationurl("home.php?cunkuan&templist=1"); 
    $pds=$db->rs;
    $zuofei=$pds["gp009"];
    $shijian=$pds["gp004"];
    $arrinfo=array();
    $db->yiquery("select * from ymupd where pd002=".$pds["gp001"]." order by pd001 asc");
    $rs=$db->rs;
foreach ($rs as $k => $v) {
    $arrinfo[$k]["jine"]=$v["pd004"];
    $arrinfo[$k]["jiaodate"]=date("Y-m-d H:i:s",$v["pd005"]);
    $arrinfo[$k]["jiaodate1"]=date("Y-m-d H:i:s",$v["pd005"]);
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
        $db->yiquery("select * from ymugp where gp001=".$v["pd002"],2);
        $arrinfo[$k]["status"]=getstatutype($db->rs['gp009'],8);

        $ldrow=array();

        $db->yiquery("select * from ymuii",2);
        $arrinfo[$k]["bank"]=$db->rs["ui001"];

        $arrinfo[$k]["bankuser"]=$db->rs["ui002"];
        $arrinfo[$k]["bankcard"]=$db->rs["ui003"];
        $arrinfo[$k]["bankaddress"]=$db->rs["ui004"];
        $arrinfo[$k]["mobile"]=$db->rs["ui005"];
        $arrinfo[$k]["qq"]=$db->rs["ui006"];
        $arrinfo[$k]["weixin"]=$db->rs["ui007"];
    }else{
        $db->yiquery("select * from ymugq where gq001=".$v["pd003"],2);
        $arrinfo[$k]["status"]=getstatutype($db->rs['gq009'],8);
        
        $us=$member->getuserinfo($db->rs["gq002"]);
        $db->yiquery("select * from ymub where ub009=1 and ub002='".$us["uv002"]."'",2);
        
        $arrinfo[$k]["bank"]=$db->rs["ub004"];
        $arrinfo[$k]["bankuser"]=$db->rs["ub006"];
        $arrinfo[$k]["bankcard"]=$db->rs["ub005"];
        $arrinfo[$k]["bankaddress"]=$db->rs["ub007"];
        $arrinfo[$k]["mobile"]=$us["um010"];
        $arrinfo[$k]["qq"]=$us["um015"];
        $arrinfo[$k]["weixin"]=$us["um014"];


        $ldrow=$member->getldqname($v["pd003"]);        
    }

    $arrinfo[$k]["ldid"]=$ldrow["um002"];
    $arrinfo[$k]["ldmobile"]=$ldrow["um010"];
    $arrinfo[$k]["ldqq"]=$ldrow["um015"];



        $db->yiquery("select * from ymugp where gp001=".$v["pd002"],2);
      
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
  <div class="right_center">>&nbsp;提供帮助配对信息</div>

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
                            <td width="25%" valign="top" align="center" colspan="4" style="background:#552A15;font-size:16px;color:#F0E531">等待配对中</td>                            
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
                            <td valign="top" width="100" align="left">&nbsp;<?php  echo getcunstatus(array("pd009"=>$v["pd009"],"pd008"=>$v["pd008"],"pd011"=>$v["pd011"]));?></td>
                            <td width="25%" valign="top" align="right">上传凭证：</td>     
                            <td valign="top">
<?php
    if(empty($v["img"])&&empty($v["pd008"])&&$v["pd009"]==0&&$v["pd011"]==0){
?>
<form action="home.php?yim=cunkuan&templist=2&pdid=<?php echo $_GET["pdid"]?>" method="post"  enctype="multipart/form-data">
    <input type="file" name="simg" size="30" value="">
    <input type="hidden" name="imgid" size="30" value="<?php echo $v["pdid"]?>">
    <input type="submit" name="submit" value="上传图片">
</form>
<?php    
    }elseif(!empty($v["img"])&&empty($v["pd008"])&&$v["pd009"]==0&&$v["pd011"]==0){
?>
<a href="javascript:artload1('<img src=<?php echo YMURL.'yimaoupfile/'.$v["img"]?>>');">查看上传</a>
<form action="home.php?yim=cunkuan&templist=2&pdid=<?php echo $_GET["pdid"]?>" method="post" >
    <input type="hidden" name="imgid" size="30" value="<?php echo $v["pdid"]?>">
    <input type="submit" name="submit" value="确认汇款">
</form>
<?php    
    }elseif(empty($v["img"])&&$v["pd009"]==1){
?>
&nbsp;未上传图片
<?php    
    }elseif(empty($v["img"])&&$v["pd009"]==0){
?>
&nbsp;未上传图片
<?php    
    }elseif(empty($v["img"])){
?>
&nbsp;未上传图片
 <?php }else{?>
 &nbsp;<a href="javascript:artload1('<img src=<?php echo YMURL.'yimaoupfile/'.$v["img"]?>>');">查看上传</a>
 <?php }?> 
                            </td>   
                                    
                        </tr>                                                                                                                      
<?php
}?>


                          

                        
      
                    </tbody>
                </table>
<?php 
}
?>        