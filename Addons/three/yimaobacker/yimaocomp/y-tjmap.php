<?php
defined('YIMAOMONEY') or exit('Access denied');



if($_GET["level"]!=-1&&!empty($_GET["level"])){
  $level=getnums($_GET["level"],1);
}elseif($_GET["level"]!=-1){
  $level=getnums($_GET["level"],1);
}else{
  $level=$_GET["level"];
}

$qid=getnums($_GET["qid"],$_houid);


$user=trim($_POST["user"]);
if(!empty($user)){
  $r=$member->user_exists($user);
  if(empty($r)) $qid=$_houid;
  $qid=$r['uv001'];
}

$sql="select uv001,uv002,uv006,uv008,uv038,uv018,uv016,uv022,uv020 from ymuv where uv001='$qid'";
$db->yiquery($sql,2);
$rs=$db->rs;
if(empty($rs)){
  yimao_automsg('会员不存在',$YCF['curl'],1,3,0);
}

  
if(!strstr($rs['uv020'],",".$_houid.",") && $rs["uv001"]!=$_houid){
   yimao_automsg('跳转失败!',$YCF['curl'],1,3,0);
}

echo '<script type="text/javascript" src="yimaores/js/tree.js"></script>
<div class="main-r-item">
  <form name="form" action="" method="post">
    <button  onClick="form.submit()" type="submit" class="yimaoabtn">跳 转</button>
    <input name="user" type="text" id="user" style="width:100px;" class="yimaoconfig">
    <label>显示代数
        <select name="level" class="yimaoselect" onChange="window.location.href=\''.$YCF['curl'].'&user='.$rs['uv002'].'&level=\'+this.value;">
          <option value="1" '.geteqval(array($level,1,'selected','')).'>1代</option>
          <option value="2" '.geteqval(array($level,2,'selected','')).'>2代</option>
          <option value="3" '.geteqval(array($level,3,'selected','')).'>3代</option>
          <option value="4" '.geteqval(array($level,4,'selected','')).'>4代</option>
          <option value="5" '.geteqval(array($level,5,'selected','')).'>5代</option>
          <option value="6" '.geteqval(array($level,6,'selected','')).'>6代</option>
          <option value="7" '.geteqval(array($level,7,'selected','')).'>7代</option>
          <option value="8" '.geteqval(array($level,8,'selected','')).'>8代</option>
          <option value="9" '.geteqval(array($level,9,'selected','')).'>9代</option>
          <option value="10" '.geteqval(array($level,10,'selected','')).'>10代</option>
          <option value="-1" '.geteqval(array($level,-1,'selected','')).'>显示所有</option>
        </select></label>
    <label><a href="'.$YCF['curl'].'&level='.$level.'&qid='.($rs['uv018']>$_userid?$rs['uv018']:$_userid).'" class="yimaoabtn">返回上一代</a><a href="'.$YCF['curl'].'" class="yimaoabtn">返回自己</a></label>
  </form>
</div>
<div class="main-r-item tjmaptips">
  <span class="yellowc">头像信息</span>：[会员账号] [级别] [姓名] [推荐人数]&nbsp;&nbsp;&nbsp;&nbsp;
  <span class="yellowc">图像表示</span>：<img src="yimaores/tree/filiale.gif">服务中心 <img src="yimaores/tree/member.gif">正式会员 <img src="yimaores/tree/user.gif">临时会员
</div>
<div id="ztree" style="margin-top:5px;"></div>';
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
  tree.setIconPath("yimaores/tree/"); //可用相对路径
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

    $m=$member->getum($v["uv002"],'um003'); //color:#333
    echo "tree.nodes['".$v["uv018"]."_".$v["uv001"]."'] = 'text:<span ".$ic[1].">".$v["uv002"]."</span> <font style=color:#ddd>[".getlevel($v["uv006"])."] <a href=javascript:wox(\"\")>[".$m['um003']."]</a> [".$v["uv016"]."]</font>;url:".$YCF['curl']."&qid=".$v["uv001"]."&level=".$level.";method:goUrl();".$ic[0]."'"."\n";

    maketree($v["uv001"]); 

  }
}

function gettjicon($arr){
  $icon='';$color;
  if($arr[0]==1){
    $icon="icon:member";
    $color='style=color:#ddd'; //color:#333
  }else{
    $icon="icon:zero";
    $color='style=color:#f00';
  }

  if($arr[1]==2){
    $icon="icon:agent";
    $color='style=color:#F4E000';//color:#72A4E7
  }

  return array($icon,$color);
}


?>