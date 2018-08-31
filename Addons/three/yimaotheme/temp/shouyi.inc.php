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

if(isset($_GET["syid"])){
  if($_GET["syid"]<1) msg_l("o_O ~ 操作失败",$YCF['curl']);
  $db->yiquery("select * from ymugp where gp007=0 and gp002=$_userid and gp018=0 and gp001=".$_GET["syid"],2);
  if(empty($db->rs)) msg_l("o_O ~ 操作失败",$YCF['curl']);
  $rs=$db->rs;
  if(chkzhuanshouyi($rs)){
    try{
      $db->begintransaction();

        $db->yiexec("update ymugp set gp018=2 where gp001=".$rs["gp001"]);

        $jin=$rs["gp019"]+$rs["gp005"];
        $jk=$gps["gp021"];

        if($jin>0){
          $db->yipre("update ymuv set uv011=uv011+:t1 where uv001=:t0",4,array(array(':t0',':t1'),array($_userid,$jin))); 
          yimao_writeaccount(array($_userid,"1",1,time(),11,$jin,'转入现金钱包'));
          yimao_writeaccount(array($_userid,"0",0,time(),11,$jin,'收益钱包转入'));
        }

          if($jk>0){

            yimao_writeaccount(array($_userid,"1",5,time(),11,$jk,'转入小金库'));
            yimao_writeaccount(array($_userid,"0",4,time(),11,$jk,'冻结小金库转入'));
          }

        $db->yiquery("select ud002,sum(ud006) ud006,sum(ud009) ud009 from ymud where  ud010=0 and  ud005=54 and ud008=".$rs["gp001"]." group by ud002");
        $all=$db->rs;
        $db->yipre("update ymud set ud010=1 where ud010=0 and ud005=54 and ud008=:t0",4,array(array(':t0'),array($rs["gp001"])));          
        foreach ($all as $kk => $vv) {

            $us=$member->getuserinfo($vv["ud002"]);
            $jin=($vv["ud006"]-$vv["ud009"]);
           if($jin>0){
            $db->yipre("update ymuv set uv011=uv011+:t1 where uv001=:t0",4,array(array(':t0',':t1'),array($vv["ud002"],$jin)));       
            yimao_writeaccount(array($vv["ud002"],"1",2,time(),11,$jin,'转入现金钱包'));
            yimao_writeaccount(array($vv["ud002"],"0",0,time(),11,$jin,'佣金钱包转入'));  
        yimao_writeaccount(array($vv["ud002"],"1",5,time(),11,$vv["ud009"],'转入小金库'));  
        yimao_writeaccount(array($vv["ud002"],"0",4,time(),11,$vv["ud009"],'冻结小金库转入'));              
           }
         }

         //  if($vv["ud009"]>0){
         //   yimao_writeaccount(array($v["gp002"],"0",3,time(),17,$vv["ud009"],'其它奖小金库'));
         //   $db->yiexec("update ymuv set uv048=uv048-".$vv["ud009"]." where uv001=".$vv["ud002"]);
            
          // }

        // }  


        
        yimao_writelog("收益转入现金钱包",0);

      $db->committransaction();
      msg_l("o_O ~ 转入成功 ",$YCF["curl"]); 
    }catch(PDOException $e){
      $db->rollbacktransaction();
      a_bck("error");
    }
  }
}
?>

  <div class="right_center">>&nbsp;我的提供帮助收益</div>

  <div class="indent-r-top">财务管理</div>
<table width="100%" border="0" cellpadding="0" cellspacing="1" style="margin-top:5px;" class="biaotab" align="center">
  <tbody>
    <tr>
        <th>序号</th>     
        <th valign="top" width="170">提供帮助编号</th>
        <th valign="top" width="160">创建时间</th>
        <th valign="top" width="160">提供帮助金额</th>
        <th valign="top">收益次数</th>
        <th valign="top">收益金额</th>
        <th valign="top">状态</th>
        <th valign="top">操作</th>
    </tr>
<?php

$pagecount=10;
$condition=" where gp002=$_userid";
$psql="select count(0) from ymugp $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yim=mytj";    //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymugp $condition order by gp001 desc limit $offset,$pagecount";
$db->yiquery($sql);
$rs=$db->rs;
$ii=0;
foreach ($rs as $k => $v) {
  $ii++;
  $serialid=($pagearr[2]-1)*$pagecount+$ii;
  $rm=$member->getum($v['uv002']);
?>
    <tr>
        <td ><?php echo $serialid?></td>     
        <td valign="top"><?php echo $v['gp013']?></td>
        <td valign="top"><?php echo formatdate($v['gp004'])?></td>
         <td valign="top"><?php echo $v['gp005']?></td>      
        <td valign="top"><?php echo $v['gp020']?></td>
        <td valign="top"><?php echo $v['gp019']?></td>
        <td valign="top"><?php echo getshouyistatus(array($v['gp009'],$v['gp018']));?></td>

        <td valign="top">
 <?php 
if(chkzhuanshouyi($v)){
      
?>
  <a style="color:blue;text-decoration:underline" href="<?php echo $YCF['curl']?>&syid=<?php echo $v["gp001"]?>" onclick="return boxcheck('您确定现在转入现金钱包吗?\n\n确认后你不再获得此提供帮助剩余的收益！','正在执行中,请耐心等待！');">转入现金钱包</a>
<?php
  }


 ?>         

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
<?php }?>