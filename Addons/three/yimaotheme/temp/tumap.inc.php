<?php defined('YIMAOMONEY') or exit('Access denied');?>

  <div class="right_center">>&nbsp;推荐系谱</div>

  <div class="indent-r-top">市场管理</div>
<?php
$level=1;
$qid=getnums($_GET["qid"],$_userid);



$sql="select uv001,uv002,uv006,uv008,uv038,uv018,uv016,uv022,uv020 from ymuv where uv001='$qid'";
$db->yiquery($sql,2);
$rs=$db->rs;
if(empty($rs)){
  msg_b('会员不存在');
}

if(!strstr($rs['uv020'],",".$_userid.",") && $rs["uv001"]!=$_userid){
   msg_b('跳转失败!');
}

echo '<script type="text/javascript" src="'.YMTHEME.'js/tree.js"></script>

<div class="tudiv">
  <span class="yellowc">头像信息</span>：[会员账号] [级别] [姓名] [推荐人数]&nbsp;&nbsp;&nbsp;&nbsp;
  <span class="yellowc">图像表示</span>：<img src="'.YMTHEME.'tree/member.gif">&nbsp;会员
</div>
<div id="ztree" style="padding-left:10px"></div>';

?>
<script type="text/javascript">

function InitShowTreeInfo(tree){

<?php 
$ic=gettjicon(array($rs['uv008'],$rs['uv038']));
$m=$member->getum($rs["uv002"],'um003');
echo "tree.nodes['0_".$rs['uv001']."'] = 'text:<span ".$ic[1].">".$rs["uv002"]." [".getlevel($rs["uv006"])."] [".$m["um003"]."] [".$rs['uv016']."]</span>;url:javascript:void(0);method:goUrl();".$ic[0]."'" ."\n";

maketree($rs['uv001']);
?>
}

var tree = new MzTreeView("tree");
  tree.icons["zero"] = "user.gif";//
  tree.icons["member"] = "member.gif";//
  tree.icons["agent"] = "filiale.gif";//
  tree.icons["admin"] = "manager.gif";//
  tree.setIconPath("yimaotheme/tree/"); //可用相对路径
  InitShowTreeInfo(tree);
  tree.setTarget("_self");
  document.getElementById("ztree").innerHTML = tree.toString();   

</script>

<?php
function maketree($vid){
  global $YCF,$db,$member,$level,$rs;

  if($level!=-1){
    $qsql=" and uv022<=".($rs['uv022']+$level);
  }else{
    $qsql="";
  }


  $sql="select uv001,uv002,uv006,uv008,uv038,uv018,uv016,uv022 from ymuv where uv018=$vid  $qsql order by uv001 asc";
  $db->yiquery($sql);
  $row=$db->rs;
  foreach($row as $k=>$v){
    $ic=gettjicon(array($v['uv008'],$v['uv038']));

    $m=$member->getum($v["uv002"],'um003');
    echo "tree.nodes['".$v["uv018"]."_".$v["uv001"]."'] = 'text:<span ".$ic[1].">".$v["uv002"]."</span> <font style=color:#000>[".getlevel($v["uv006"])."] <a href=javascript:wox(\"\")>[".$m['um003']."]</a> [".$v["uv016"]."]</font>;url:".$YCF['curl']."&qid=".$v["uv001"]."&level=".$level.";method:goUrl();".$ic[0]."'"."\n";

    maketree($v["uv001"]); 

  }
}

function gettjicon($arr){
  $icon='';$color;
  if($arr[0]==1){
    $icon="icon:member";
    $color='style=color:#000';
  }else{
    $icon="icon:zero";
    $color='style=color:#000';
  }

  if($arr[1]==2){
    $icon="icon:agent";
    $color='style=color:#000';
  }

  return array($icon,$color);
}


?>