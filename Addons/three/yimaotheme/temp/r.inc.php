<?php defined('YIMAOMONEY') or exit('Access denied');?>
<?php
$sql="select * from ymur where  ur006=0 order by ur005 asc";
$db->yiquery($sql);
$rr=$db->rs;

if(empty($_GET['tid'])){
// $sql="select um002 from ymum where um001=1";
// $db->yiquery($sql,2);

// $tname=$db->rs["um002"];
$tname="";
}else{
$tid=checkstr($_GET['tid']);
$sql="select um002 from ymum where um002='$tid' ";
$users=$member->getuserinfo($tid,1);
if(empty($users)){
    $sql="select um002 from ymum where um001=1";
    $db->yiquery($sql,2);

    $tname=$db->rs["um002"];
}else{
    if($users["uv008"]==0){
        $sql="select um002 from ymum where um001=1";
        $db->yiquery($sql,2);

        $tname=$db->rs["um002"];
    }else{
        $tname=$users["um002"];
    }
    
}

}


$rg=getregform(array('class'=>'reginput','bdname'=>$_GET['anname'],'tuname'=>$tname,'anname'=>$_GET['anname'],'anrea'=>$_GET['anseat']));

?>
<link href="<?php echo YMTHEME;?>css/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo YMTHEME;?>js/jquery-1.6.4.min.js"></script>
<script type="text/javascript" src="<?php echo YMTHEME;?>js/main.js"></script>
<script type="text/javascript" src="<?php echo YMTHEME;?>art/artDialog.source.js?skin=simple"></script> 
<script type="text/javascript">
var ymurl="<?php echo YMURL;?>";
</script>
<script type="text/javascript" src="<?php echo YMTHEME;?>js/reg.js"></script>
<script type="text/javascript" src="<?php echo YMTHEME;?>js/tip.js"></script>
<script type="text/javascript">
    var bd=<?php echo $YCF['regbd']?>,an=<?php echo $YCF['regan']?>;

    var rgprovince=1,rgpwd1=1, rgpwd2=1,rgpwd3=1,rgmbq=1,rgmba=1,rgmobile=1,rgemail=1,rgcard=1,rgweixin=1,rgqq=1,rgfax=1,rgprovince=1,rgaddress=1;

</script>
<style type="text/css">
    .baitab td{color:#fff;}
    body{background:#2B0000;}
</style>
<title><?php echo $YCF['sytitle']?></title>
<div style="background:#2B0000;">
            <div class="bt" style="font-size:20px">注册会员</div><div style="width:1000px;background:#2B0000;margin:0px auto"><form action="regs.php" method="post" onsubmit="return register()">
                <input type="hidden" name="act" value="add">
                <input type="hidden" name="ulevel" value="0">                
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:5px;" class="baitab" align="center">
                    <tbody>

                        <tr>
                            <td width="35%" valign="middle" align="right"><span class="regc">*&nbsp;</span>账号：</td>     
                            <td valign="middle" align="left"><input type="text" value="<?php echo getrand()?>" name="userid" id="userid" class="reginput">&nbsp;<input type="button" value="获取" class="btns" style="font-size:13px;"  onclick="getuserid('userid')"></td>
                        </tr>
                        <tr>
                            <td width="25%" valign="middle" align="right"><span class="regc">*&nbsp;</span>姓名：</td>     
                            <td valign="middle"><input type="text" name="username" id="username" class="reginput"></td>
                        </tr>
<?php
$ispwd=0;
foreach ($rr as $k => $v) {
    $input='<input type="text" name="'.$v['ur001'].'" class="">';

?>
<script type="text/javascript">
    var <?php echo $v['ur001']?>=<?php echo $v['ur007']?>

    var <?php echo $v['ur001']?>xx="<?php echo $v['ur002']?>"

</script>
                        <tr>
                            <td width="25%" valign="middle" align="right"><?php if($v['ur007']==0){?><span class="regc">*&nbsp;<?php }?></span><?php echo $v['ur002'];?>：</td>     
                            <td valign="middle"><?php
if($v['ur004']==0){
?>
                            <input type="text" name="<?php echo $v['ur001']?>" value="<?php echo $v['ur003']?>" id="<?php echo $v['ur001']?>" class="reginput">
<?php
}elseif($v['ur004']==1){
    if($v['ur001']=='rgsex'){

?>

<label><input type="radio" name="sex" value="0" checked>&nbsp;男</label>&nbsp;<label><input type="radio" name="sex" value="1">&nbsp;女</label>&nbsp;<label><input type="radio" name="sex" value="2">&nbsp;未知</label>
<?php
    }
}elseif($v['ur004']==2){
    if($v['ur001']=='rgprovince'){
?>
<script type="text/javascript" src="<?php echo YMTHEME;?>js/pcasunzip.js"></script> 
<select name="province" id="province" class="regselect"></select>&nbsp;<select name="city" id="city" class="regselect"></select>&nbsp;<select name="area" id="area"  class="regselect"></select>
        <script type="text/javascript">
        new PCAS("province","city","area","","");
        </script>
<?php
    }elseif($v['ur001']=='rgmbq'){
$rmbq=explode('|',$v['ur003']);
?>
<select name="mbq" id="mbq" class="reginput" >
<option value="-1">请选择您的密保问题?</option>
<?php foreach ($rmbq as $kk => $vv) {
?>

<option value="<?php echo $kk?>"><?php echo $vv?></option>
<?php   
}?>
</select>
<?php        
    }
?>

<?php
}elseif($v['ur004']==3){
$ispwd=1;
?>
<script type="text/javascript">
    var <?php echo $v['ur001']?>name="<?php echo $v['ur002']?>"
</script>
<input type="password" name="<?php echo $v['ur001']?>" value="<?php echo $v['ur003']?>" id="<?php echo $v['ur001']?>" class="reginput">
<?php
}
?></td>
                        </tr>     
<?php 
if($ispwd){
$ispwd=0;    
?>
                        <tr>
                            <td width="25%" valign="middle" align="right"><?php if($v['ur007']==0){?><span class="regc">*&nbsp;<?php }?></span>确认<?php echo $v['ur002'];?>：</td>        
                            <td valign="middle"><input type="password" name="<?php echo $v['ur001']?>1"  value="<?php echo $v['ur003']?>" id="<?php echo $v['ur001']?>1" class="reginput">
                            </td>
                        </tr>
<?php
}
}?>            
<?php
if($YCF['regbd']){ 
?>
                        <tr>
                            <td width="25%" valign="middle" align="right"><span class="regc">*&nbsp;</span>报单中心：</td>     
                            <td valign="middle"><?php echo $rg['bdname']?></td>
                        </tr>
<?php }?>                        
                        <tr>
                            <td width="25%" valign="middle" align="right"><span class="regc">*&nbsp;</span>推荐人：</td>     
                            <td valign="middle"><?php echo $rg['tuname']?></td>
                        </tr>    
<?php
if($YCF['regan']){ 
?>                         
                        <tr>
                            <td width="25%" valign="middle" align="right"><span class="regc">*&nbsp;</span>安置人：</td>     
                            <td valign="middle"><?php echo $rg['anname']?></td>
                        </tr>
                        <tr>
                            <td width="25%" valign="middle" align="right"><span class="regc">*&nbsp;</span>区域：</td>     
                            <td valign="middle">
                                <?php echo $rg['anrea']?><span id="anreatxt"></span>
                            </td>
                        </tr>    
<?php }?>                           
                        <tr>
                            <td width="25%" valign="middle" align="right"></td>     
                            <td valign="middle">
                    <label></label><input id="mobileAcceptIpt" name="mobileAcceptIpt" type="checkbox" checked="checked" tabindex="-1"> 同意<a href="agreement.html" target="_blank" tabindex="-1" style="color:#f00">"注册服务协议"</a>和<a href="agreement_game.html"  target="_blank" tabindex="-1" style="color:#f00">"隐私条款"</a>
                            </td>
                        </tr>                                       
                        <tr>
                            <td width="25%" valign="middle" align="right" style="height:50px;line-height:50px"></td>     
                            <td valign="middle" style="height:50px;line-height:50px"><input type="submit" value="注册" class="btn"></td>
                        </tr>        
                    </tbody>
                </table></form></div>
</div>                
<script type="text/javascript" src="<?php echo YMTHEME;?>js/checkreg.js"></script>                 