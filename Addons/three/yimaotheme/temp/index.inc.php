<?php defined('YIMAOMONEY') or exit('Access denied');?>
<?php
$arrn=getnews();
$rn=$arrn[0];
$rns=$arrn[1];
$db->yiquery("select count(0) from ymuv where uv044=$_userid",3);


?>
 <script type="text/javascript" src="<?php echo YMTHEME;?>js/jquery.zclip.min.js"></script> 
  <div class="right_center">>&nbsp;控制面板</div>

  <div class="indent-r-top">控制面板


  </div>
  <div style="clear:both"></div>


            <div  class="indent-r-kuai-txt"  style="text-indent:5px;background:none;border-bottom:1px solid #ccc;width:auto"><span style='color:#897914;'>&nbsp;&nbsp;&nbsp;我的注册推广链接：<span id="mytext"><?php echo "http://".$_SERVER["HTTP_HOST"].'/r.php?tid='.$r["uv002"];?></span></span> <a href="#" id="copyBtn" style="color:#000" class="copy">[复制内容]</a></div>
                        
  <div class="indent-r-kuai-txt">快捷键</div>
  <div class="indent-r-kuai-image clear">
    <span class="kuai-im im1"  style="margin-left:80px"><a href="<?php echo YMINDEX?>?yim=register" style="margin-left:20px">创建账户</a></span>
    <span class="kuai-im im2"><a href="<?php echo YMINDEX?>?yim=prizelist" style="margin-left:20px">奖金明细</a></span>

    <span class="kuai-im im4"  style="margin-left:80px"><a href="<?php echo YMINDEX?>?yim=cunkuan" style="margin-left:20px">提供帮助</a></span>
    <span class="kuai-im im5"><a href="<?php echo YMINDEX?>?yim=qukuan" style="margin-left:20px">获取帮助</a></span>
  
  </div>

  <div class="indent-r-kuai-news">新闻动态<span class="more"><a href="<?php echo YMINDEX?>?yim=newslist">查看更多</a>&nbsp;&nbsp;</span></div>
  <div class="indent-r-kuai-newslist">
               <?php 
              foreach ($rns as $k => $v) {

              ?>
 
    <div class="bt bd1"><span style="color:#E1631A">[<?php echo getatricletype(array(1,$v['un004']))?>]</span>&nbsp;<a href="<?php echo YMINDEX.'?yim=newsview&veid='.$v['un001']?>"><?php echo $v['un002'];?></a>  </div>
                <?php 
              }
              ?>     

  </div> 



            <script type="text/javascript">  
m();  
function m()  
{  
 
  document.getElementById("zitxt").className="zitxt";  
  setTimeout("e()",400);  
}  
function e()  
{  

  document.getElementById("zitxt").className="zitxt1";    
  setTimeout("m()",300);  
}  
</script> 
<script>
        $('#copyBtn').zclip({
            path: "<?php echo YMTHEME;?>js/ZeroClipboard.swf",
            copy: function(){
                return $('#mytext').text();
  　　　 　　}
        });
</script>
