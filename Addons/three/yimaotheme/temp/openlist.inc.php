<?php defined('YIMAOMONEY') or exit('Access denied');?>
<style type="text/css">
.aui_footer{display: none}
</style>
<?php

if(empty($_SESSION["yzmm"])||!strstr($_SESSION["yzmm"],'openlist')){
    $_GET["m"]="openlist";
    @require(ROOTTHEMETEMP."yzmm.inc.php");
    exit;    
}
if(!isset($_GET['templist'])){

if($_POST['act']=="open"){
  $kid=$_POST['kid'];

  $karr=fun_openuser($kid,$_userid,0);


  yimao_writelog("开通会员:".$_username,0);
  msg_l("o_O ~ 开通会员成功 ",YMINDEX."?yim=openlist"); 
} 

if($_POST['act']=="删除"){

  $kid=$_POST['kid'];
  fun_deluser($kid);


  yimao_writelog("删除会员，id为".$kid,0);
  msg_l("o_O ~ 删除会员成功 ",YMINDEX."?yim=openlist"); 

}
?>

  <div class="right_center">>&nbsp;开通会员</div>

  <div class="indent-r-top">财务管理</div>

<div class="bt"> <div style="float:right;padding:0px 5px;"><a href="<?php echo $YCF['curl']?>&templist=1" class="lbtn">查看开通记录</a></div></div>


<table width="100%" border="0" cellpadding="0" cellspacing="1" style="margin-top:5px;" class="biaotab" align="center">
  <tbody>
    <tr>
        <th>序号</th>     
        <th>账号</th>
        <th>姓名</th>
        <th>级别</th>
        <th>联系电话</th>
        <th>推荐人</th>
<?php if($YCF['reganl']){?>        
        <th>安置人</th>
<?php }?>
        <th>注册时间</th>
        <th>操作</th>
    </tr>
<?php

$pagecount=10;
$condition=" where uv008=0 and uv018=$_userid";
$psql="select count(0) from ymuv $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yim=openlist&templist=1";    //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymuv $condition order by uv001 asc limit $offset,$pagecount";
$db->yiquery($sql);
$rs=$db->rs;
$ii=0;
foreach ($rs as $k => $v) {
  $ii++;
  $serialid=($pagearr[2]-1)*$pagecount+$ii;
  $rm=$member->getum($v['uv002'],'um003,um010');
?><form action="<?php echo $YCF['curl']?>" method="post"><input type="hidden" name="act" value="open"><input type="hidden" name="kid" value="<?php echo $v['uv001']?>"><input type="hidden" name="kulevel" value="<?php echo $v['uv006']?>"><input type="hidden" name="kuserid" value="<?php echo $v['uv002']?>">
    <tr>
        <td ><?php echo $serialid?></td>  
        <td><?php echo $v['uv002']?></td> 
        <td><?php echo $rm['um003']?></td> 
        <td><?php echo getlevel($v['uv006'])?></td> 
        <td><?php echo $rm['um010']?></td> 
        <td><?php echo $member->getuserid($v['uv018'])?></td> 
<?php if($YCF['reganl']){?>              
        <td><?php echo $member->getuserid($v['uv019']).' '.$YCF['anrea'][$v['uv024']]?></td>
<?php }?>        
        <td><?php echo formatdate($v['uv003'])?></td> 
        <td><input type="submit" name="" value="开通" onclick="return boxcheck('系统将会扣您的 <?php echo $YCF['jjrmb'][$v["uv006"]]?> 激活币,您确定要审核开通吗?','审核正在执行中,请耐心等待！');">&nbsp;&nbsp;<input type="submit" value="删除" name="act"  onclick="return boxcheck('您确定要删除<?php echo $v['uv002']?>吗?','删除正在执行中,请耐心等待！');"></td> 
    </tr></form>
<?php
}
?>    
  </tbody>
</table>
<?php if(($pcount/$pagecount)>1){?>
<div class="pagediv"><?php echo $pagearr[0]?></div>
<?php
  }
?>
<?php if($pcount==0){?>
<div class="pagediv" style="text-align:center;color:#E47501">--> 暂无数据</div>
<?php
}

}else{ 
?>

  <div class="right_center">>&nbsp;我的开通记录</div>

  <div class="indent-r-top">财务管理</div>              
<div class="bt"> <div style="float:right;padding:0px 5px;"><a href="<?php echo $YCF['curl']?>"  class="lbtn">返回 >></a></div></div>


<table width="100%" border="0" cellpadding="0" cellspacing="1" style="margin-top:5px;" class="biaotab" align="center">
  <tbody>
    <tr>
        <th>序号</th>     
        <th>账号</th>
        <th>姓名</th>
        <th>级别</th>
        <th>联系电话</th>
        <th>推荐人</th>
<?php if($YCF['reganl']){?>        
        <th>安置人</th>
<?php }?>
        <th>注册时间</th>
        <th>开通时间</th>
    </tr>
<?php

$pagecount=10;
$condition=" where uv008=1 and uv037='$_userid|0'";
$psql="select count(0) from ymuv $condition";
$db->yiquery($psql,3);
$pcount=$db->fval;
$pagecount=$pagecount;   //每页条数 
$astr="&yim=openlist&templist=1";    //Url
$pagearr=pagelist($pagecount,$pcount,$astr);
$offset=$pagearr[1];

$sql="select * from ymuv $condition order by uv001 desc limit $offset,$pagecount";
$db->yiquery($sql);
$rs=$db->rs;
$ii=0;
foreach ($rs as $k => $v) {
  $ii++;
  $serialid=($pagearr[2]-1)*$pagecount+$ii;
  $rm=$member->getum($v['uv002'],'um003,um010');
?>
    <tr>
        <td ><?php echo $serialid?></td>  
        <td><?php echo $v['uv002']?></td> 
        <td><?php echo $rm['um003']?></td> 
        <td><?php echo getlevel($v['uv006'])?></td> 
        <td><?php echo $rm['um010']?></td> 
        <td><?php echo $member->getuserid($v['uv018'])?></td> 
<?php if($YCF['reganl']){?>              
        <td><?php echo $member->getuserid($v['uv019']).' '.$YCF['anrea'][$v['uv024']]?></td>
<?php }?>        
        <td><?php echo formatdate($v['uv003'])?></td> 
        <td><?php echo formatdate($v['uv003'])?></td> 
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