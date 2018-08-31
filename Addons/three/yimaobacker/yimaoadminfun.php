<?php
function pagelist($pagecount,$rcount,$questring){
		$page=empty($_GET["page"])?1:(is_numeric($_GET["page"])?$_GET["page"]:1);
		$page=floor(abs($page));
		if($page<1) $page=1;

		$pagenums=ceil($rcount/$pagecount);
		if($page>$pagenums) $page=$pagenums;
		$upperpage=$page>1?($page-1):1;
		$nextpage=$page<$pagenums?($page+1):$pagenums;
		$offset=($page-1)*$pagecount;
		if($offset<0) $offset=0;

		$pagestr='<form action="?'.$questring.'" method="get"><ul class="pageul"><li><a href="javascript:;">'.$rcount.'</a></li><li><a href="javascript:;">'.$page.'/'.$pagenums.'</a></li><li><a href="?page=1'.$questring.'">首页</a></li><li><a href="?page='.$upperpage.$questring.'">上一页</a></li>';
		if($pagenums<=7||($pagenums>7&&$page<7)){
			$x=$pagenums>7?7:$pagenums;
			for ($i=1; $i <= $x; $i++) {
				if($i==$page){
					$pagestr.='<li><a href="?page='.$i.$questring.'" class="selpage">'.$i.'</a></li>';
				}else{
					$pagestr.='<li><a href="?page='.$i.$questring.'">'.$i.'</a></li>';
				}
			}
		}else{
			
			for ($i=$page-3; $i < $page ; $i++) { 
				$pagestr.='<li><a href="?page='.$i.$questring.'">'.$i.'</a></li>';
			}
			
			$pagestr.='<li><a href="?page='.$page.$questring.'" class="selpage">'.$page.'</a></li>';

			$jia=($page+3)>$pagenums?$pagenums:($page+3);
			for ($i=$page+1; $i <= $jia; $i++) { 
				$pagestr.='<li><a href="?page='.$i.$questring.'">'.$i.'</a></li>';
			}
		}
		$q=explode("&",$questring);

		$form="";
		for($i=0;$i<=count($q);$i++){
			if(!empty($q[$i])){
				if(strstr($q[$i],"=")&&!strstr($q[$i],"pagecount")){
					$n=substr($q[$i],0,strpos($q[$i],"="));
					$v=substr($q[$i],strpos($q[$i],"=")+1);
					$form.='<input type="hidden" name="'.$n.'" value="'.$v.'">';
				}
			}
		}

		$pagestr.='<li><a href="?page='.$nextpage.$questring.'">下一页</a></li><li><a href="?page='.$pagenums.$questring.'">尾页</a></li><li><input type="text" class="pageinput"  name="pagecount" value="'.$pagecount.'"  onkeyup="value=value.replace(/[^\d]/g,\'\')"></li><li><input type="text" class="pageinput" name="page"  onkeyup="value=value.replace(/[^\d]/g,\'\')" ></li><li><input type="submit" class="pageinput pagesubmit" value="跳转">'.$form.'</li></ul></form>';
		return array($pagestr,$offset,$page);
}



function write_static_cache($v)
{
    $cache_file_path =  YMROOT.'/yimaodebug.php';
    $content = "<?php\r\n";
    $content .= "\$YCF['debug'] = " . $v . ";\r\n";
    $content .= "?>";
    file_put_contents($cache_file_path, $content, LOCK_EX);
}

function yimao_automsg($s,$u,$e=0,$t=3,$h=1){
	$arr=array('√','×');
	if($h) echo '<h3>系统提示</h3>';
	echo '<div><div style="font-size:80px;width:50px;padding-left:20px;float:left;">'.$arr[$e].'</div>
		  <div style="margin-left:40px;width:400px;padding:0px 10px 10px;float:left;line-height:30px;height:30px"><p style="font-size:15px">→ '.$s.'</p>
		  <p><span id="bcktime" style="color:#f00">'.$t.'</span> 秒后，系统将自动返回...</p>
		  <p><a href="javascript:history.go(-1)" style="letter-spacing:2px">返回</a></p></div></div>
		  <script type="text/javascript">
			Url="'.$u.'";
			if (!Url){Url=document.referrer}function countDown(secs){document.getElementById("bcktime").innerHTML=secs;if(--secs>0) {setTimeout("countDown("+secs+")",1000);}else {window.location.href=Url;}}countDown('.$t.');setTimeout(function(){window.location.href=Url;},'.(($t+1) * 1000 ).');

		  </script>';
	exit;	  
}



function yimao_input($arr,$flag=0,$tip=''){
	
	if(strstr($arr[1],'|')&&$flag==0){
		$s='';
		if(empty($arr[3])){
			$wd="wd80";
		}else{

			if(strpos($arr[0],"jjtjj")!==false){
			
				$wd="wd50";
			}else{
				$wd="wd80";

			}
		}

		$r=explode('|',$arr[1]);
		foreach ($r as $k => $v) {
			$s.='<input type="text" name="'.$arr[0].'[]" value="'.$v.'" class="yimaoconfig '.$wd.'">';
		}
		return $s.$tip;
	}else{
		if($flag){
			return '<input type="text" name="'.$arr[0].'" value="'.$arr[1].'"  class="yimaoconfig wd400">'.$tip;
		}else{
			return '<input type="text" name="'.$arr[0].'" value="'.$arr[1].'"  class="yimaoconfig '.(empty($arr[2])?'wd80':$arr[2]).'">'.$tip;
		}
	}
}



function yimao_radio($arr,$s=0,$tip=''){
	if($s==1){
		$t=array('是','否');
	}elseif($s==2){
		$t=array('开启','禁用');
	}elseif($s==3){
		$t=array('否','是');	
	}elseif($s==4){
		$t=array('男','女');
	}else{
		$t=array('开启','关闭');
	}

	return '<label><input type="radio" name="'.$arr[0].'" value="0" '.($arr[1]==0?'checked':'').'>&nbsp;'.$t[0].'</label>&nbsp;<label><input type="radio" name="'.$arr[0].'" value="1" '.($arr[1]==1?'checked':'').'>&nbsp;'.$t[1].'</label>&nbsp;'.$tip;

}





function chkpower($s){
	global $YCF;
	if(!strstr($YCF['adminmenu2'],$s)){
		yimao_writelog('试图访问无权限的页面'.$s);
		echo '<div style="padding:50px 5px;width:500px;line-height:45px;border:1px solid #8D907D;margin:0 auto;margin-top:150px;text-align:center;background:#313C57;box-shadow: 1px 1px 5px #888888;font-size:25px"><p>( ิ◕㉨◕ ิ)</p><p style="color:#ff0">→ 抱歉，您没有权限访问！</p></div>';
		exit;
	}
}

function getquicktxt($k,$v){
	switch ($k) {
		case 'lock':
			if($v){
				return "- 锁定";
			}else{
				return "+锁定";
			}
		break;
		case 'tiqu':
			if($v){
				return "- 提取";
			}else{
				return "+提取";
			}
		break;
		case 'zhuan':
			if($v){
				return "- 转账";
			}else{
				return "+转账";
			}
		break;	
		case 'huan':
			if($v){
				return "- 转换";
			}else{
				return "+转换";
			}
		break;	
		case 'info':
			if($v){
				return "- 资料";
			}else{
				return "+资料";
			}
		break;			
		case 'amap':
			if($v){
				return "- 网络图";
			}else{
				return "+网络图";
			}
		break;	
		case 'bd':
			if($v==2){
				return "- 服务中心";
			}else{
				return "+ 服务中心";
			}
		break;	
		case 'jl':
			if($v){
				return "- 经理奖";
			}else{
				return "+经理奖";
			}
		break;										
	}
}


function yimao_login(){
	$lgid=$_GET["lgid"];	
	$_SESSION['userid']=$lgid;
	$_SESSION['bmb']=md5('yimaomian11');
	locationurl(YMINDEX);
	exit;
}


function femail($mubiao,$title,$content){
	global $YCF;
	try { 
		$mail = new PHPMailer(true); 
		$mail->IsSMTP(); 
		$mail->CharSet='UTF-8'; 
		$mail->SMTPAuth = true; 
		$mail->Port = $YCF['emport']; 
		$mail->Host = $YCF['emserver']; 
		$mail->Username = $YCF['emuser']; 
		$mail->Password = $YCF['empwd']; 
	
		$mail->AddReplyTo($YCF['emuser'],""); 
		$mail->From = $YCF['emuser']; 
		$mail->FromName = ""; 
		$to = $mubiao; 
		$mail->AddAddress($to); 
		$mail->Subject = $title; 
		$mail->Body = $content; 
		$mail->AltBody = $content; //当邮件不支持html时备用显示，可以省略 
		$mail->WordWrap = 80; // 设置每行字符串的长度 
		//$mail->AddAttachment("f:/test.png"); //可以添加附件 
		$mail->IsHTML(true); 
		$mail->Send(); 
		//echo '邮件已发送'; 
	} catch (phpmailerException $e) { 
		//echo "邮件发送失败：".$e->errorMessage(); 
	} 
}


function autoreginfo(&$arr){
	global $member;
	$s='';
	$d=array();
	foreach ($arr as $k => $v) {
		switch ($k) {
			case 'uv018':
				if(empty($v)){
					$s='请输入推荐人';
					break 2;
				}
				if(!chkusername($v)){
					$s='推荐人输入不正确';
					
				}
				$r=$member->user_exists($v);
				if(empty($r)){
					$s='推荐人不存在';
					break 2;
				}
				$arr['uv018']=$r['uv001'];
				$arr['uv020']=$r['uv020'].$r['uv001'].',';
				$arr['uv022']=$r['uv022']+1;
				
				$d['uv018']=$r['uv001'];			
			break;	


						
		}
	}
	return $s;
}

?>