<?php defined('YIMAOMONEY') or exit('Access denied');?>
<?php

$sql="select * from ymur where  ur006=0 and ur008=0 order by ur005 asc";
$db->yiquery($sql);
$rr=$db->rs;

$rgform=getregform(array('class'=>'reginput','mbq'=>$r['um008'],'sex'=>$r['um016'],'sclass'=>'regselect','province'=>$r['um018'],'city'=>$r['um019'],'area'=>$r['um020']));

?>
<script type="text/javascript" src="<?php echo YMTHEME;?>js/reg.js"></script>
<script type="text/javascript" src="<?php echo YMTHEME;?>js/tip.js"></script>
<script type="text/javascript" src="<?php echo YMTHEME;?>js/checkreg.js"></script> 

  <div class="right_center">>&nbsp;个人资料</div>

  <div class="indent-r-top">会员中心</div>

            <form action="control.php" method="post"><input type="hidden" name="act" value="modinfo">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:5px;" class="baitab" align="center">
                    <tbody>

                        <tr>
                            <td width="25%" valign="top" align="right">账号：</td>     
                            <td valign="top" align="left"><?php echo $r['uv002']?></td>
                        </tr>
                        <tr>
                            <td width="25%" valign="top" align="right">姓名：</td>     
                            <td valign="top"><?php echo $r['um003']?></td>
                        </tr>  
                          
                        <tr>
                            <td width="25%" valign="top" align="right">注册日期：</td>     
                            <td valign="top"><?php echo formatdate($r['uv003'],1)?></td>
                        </tr> 

                        <tr>
                            <td width="25%" valign="top" align="right">推荐人：</td>     
                            <td valign="top"><?php echo $member->getuserid($r['uv018'])?></td>
                        </tr>  
<?php
foreach ($rr as $k => $v) {
    echo '<input type="hidden" name="ptd[]" value="'.$v['ur001'].'">';
    if($v['ur004']==0){
?>
                        <tr>
                            <td width="25%" valign="top" align="right"><?php echo $v['ur002']?>：</td>     
                            <td valign="top"><input type="text" name="<?php echo $v['ur001']?>" value="<?php echo yimao_autoinfo($v['ur001'],$r)?>" class="reginput"  maxlength="30"></td>
                        </tr>  
<?php
    }elseif($v['ur004']==1){
?>
                        <tr>
                            <td width="25%" valign="top" align="right"><?php echo $v['ur002']?>：</td>     
                            <td valign="top"><?php echo $rgform['sex']?></td>
                        </tr>  

<?php        
    }elseif($v['ur004']==2){      
        if($v['ur001']=='rgprovince'){
            echo '<input type="hidden" name="ptd[]" value="province">';
            echo '<input type="hidden" name="ptd[]" value="city">';
            echo '<input type="hidden" name="ptd[]" value="area">';            
?>
<script type="text/javascript" src="<?php echo YMTHEME;?>js/pcasunzip.js"></script> 
                        <tr>
                            <td width="25%" valign="top" align="right"><?php echo $v['ur002']?>：</td>     
                            <td valign="top"><?php echo $rgform['province']?></td>
                        </tr>  

<?php            
        }
    }

}

?>                        
                        <tr>
                            <td width="25%" valign="top" align="right" style="height:50px;line-height:50px"></td>     
                            <td valign="top" style="height:50px;line-height:50px"><input type="submit" value="修改" class="btn" <?php echo $r['um024']?"disabled":""?> ></td>
                        </tr>        
                    </tbody>
                </table></form>