<?php defined('YIMAOMONEY') or exit('Access denied');?>
<div class="main-l">

    <div class="left_center">个人信息</div>
    <div class="bd2" style="height:310px">
  <table border="0" class="persontab" align="left"><tr><td width="80" valign="middle"><img src="<?php echo YMTHEME;?>/images/13.jpg" style=""></td><td><table class="personinfo" border="0" >
  <tr><td>&nbsp;欢迎您 <?php echo $r['um002']?></td></tr>
  <tr><td class="ptd">&nbsp;<a href="#">级别：<?php echo getlevel($r["uv006"])?></a>&nbsp;</td></tr>
  <tr><td class="ptd">&nbsp;<a href="#">头衔：<?php echo ($r['uv007']==0?"":$YCF["jjtxn"][$r['uv007']-1])?></a>&nbsp;</td></tr>
  <tr><td class="ptd">&nbsp;<a href="#">职称：<?php 

  echo getchicheng($r)?></a>&nbsp;</td></tr>
  <tr><td class="ptd"><a href="index.php?u=out" style="color:#f00;font-size:16px;margin-top:10px;text-decoration:underline" onclick="return confirm('您确定要退出吗')" >&nbsp;安全退出</a></td></tr>
  </table></td></tr></table> 

 <table border="0" class="persontab1" align="right"><tr><td></td><td><table class="personinfo1" border="0" >
  <tr><td class="ptd">&nbsp;<a href="#">总奖金：<?php echo formatrmb($r["uv011"])?></a>&nbsp;</td></tr>
  <tr><td class="ptd">&nbsp;<a href="#">现金钱包：<?php echo formatrmb($r["uv012"])?></a>&nbsp;</td></tr>
  <tr><td class="ptd">&nbsp;<a href="#">收益钱包：<?php echo formatrmb($r["uv013"])?></a>&nbsp;</td></tr>
  <tr><td class="ptd">&nbsp;<a href="#">佣金钱包：<?php echo formatrmb($r["uv014"])?></a>&nbsp;</td></tr> 
  <tr><td class="ptd">&nbsp;<a href="#">激活币：<?php echo formatrmb($r["uv015"])?></a>&nbsp;</td></tr> 
  <tr><td class="ptd">&nbsp;<a href="#">小金库：<?php echo formatrmb($r["uv065"]+$r["uv064"])?>(冻结<?php echo formatrmb($r["uv064"])?>)</a>&nbsp;</td></tr> 
  <tr><td class="ptd">&nbsp;<a href="#">累计存款：<?php echo formatrmb($r["uv067"])?></a>&nbsp;</td></tr>    
  <tr><td class="ptd">&nbsp;<a href="#">未阅邮件：<?php 
                                $db->yiquery("select count(ue001) from ymue where ue011=0 and ue003='".$r["uv002"]."' and (ue008=0 or ue008=1) and ue007=0 ",3);
                                if($db->fval>0){
                                  echo "<a href='home.php?yim=sjx'><span style='color:#F5E22E'>".$db->fval." 封</span></a>";
                                }else{
                                  echo $db->fval;
                                }
                                
                               ?></a>&nbsp;</td></tr>   
  </table></td></tr></table>   
    </div>  
    <div class="menu bd2"><h3><a href="<?php echo YMINDEX?>">控制面板</a></h3></div>    
    <div class="menu bd2"><h3  onclick="turnit(Content1,tag1)" id="tag1">新闻动态</h3>
    <div id="Content1" style="display:<?php echo $menu1?>">
    <div class="cai"><a href="<?php echo YMINDEX?>?yim=newslist">最新公告</a></div>
    </div>  
    </div>  


    <div class="menu bd2"><h3 onclick="turnit(Content2,tag2)" id="tag2">会员中心</h3>
    <div id="Content2" style="display:<?php echo $menu2?>">
    <div class="cai bd1"><a href="<?php echo YMINDEX?>?yim=myinfo">个人资料</a></div>
    <div class="cai bd1"><a href="<?php echo YMINDEX?>?yim=mybank">银行资料</a></div>
    <div class="cai bd1"><a href="<?php echo YMINDEX?>?yim=modpwd">密码修改</a></div>
    <div class="cai bd1"><a href="<?php echo YMINDEX?>?yim=modmb">密保修改</a></div>

    

<?php

if(!empty($adminra)&&$adminra['ua005']==0){

?>    
    <div class="cai"><a href="<?php echo YMINDEX?>?yim=houlogin">登录后台</a></div>
<?php }?> 
    </div>
    </div>

    <div class="menu bd2"><h3   onclick="turnit(Content3,tag3)" id="tag3">市场管理</h3>
    <div id="Content3" style="display:<?php echo $menu3?>">
    <div class="cai bd1"><a href="<?php echo YMINDEX?>?yim=register">创建账户</a></div>
    <div class="cai bd1"><a href="<?php echo YMINDEX?>?yim=openlist">开通会员</a></div>
    <div class="cai"><a href="<?php echo YMINDEX?>?yim=tumap">推荐系谱</a></div>
    </div>
    </div>

    <div class="menu bd2"><h3  onclick="turnit(Content4,tag4)" id="tag4" >财务管理</h3>
    <div id="Content4" style="display:<?php echo $menu4?>">    
    <div class="cai bd1"><a href="<?php echo YMINDEX?>?yim=shouyi">提供帮助收益</a></div>
    <div class="cai bd1"><a href="<?php echo YMINDEX?>?yim=prizelist">奖金明细</a></div>
    <?php if($YCF['chongopen']==0){?>
    <div class="cai bd1"><a href="<?php echo YMINDEX?>?yim=chonglist" class="sm4">货币充值</a></div>
    <?php }?>
    <div class="cai bd1"><a href="<?php echo YMINDEX?>?yim=xiaojinlist">小金库转换</a></div>
    <div class="cai bd1"><a href="<?php echo YMINDEX?>?yim=huanlist">货币转换</a></div>
    <div class="cai bd1"><a href="<?php echo YMINDEX?>?yim=zhuanlist">货币转账</a></div>
    </div>

    </div>

    <div class="menu"><h3><a href="<?php echo YMINDEX?>?yim=liuyan">联系我们</a></h3></div>
    <br><br><br><br><br>
</div>  
<div class="main-r">
