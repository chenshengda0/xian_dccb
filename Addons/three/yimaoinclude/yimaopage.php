<?php
//分页
defined('YIMAOMONEY') or exit('Access denied');

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

		$pagestr.='<li><a href="?page='.$nextpage.$questring.'">下一页</a></li><li><a href="?page='.$pagenums.$questring.'">尾页</a></li><li><input type="text" class="pageinput" name="page"  onkeyup="value=value.replace(/[^\d]/g,\'\')" ></li><li><input type="submit" class="pageinput pagesubmit" value="跳转">'.$form.'</li></ul></form>';
		return array($pagestr,$offset,$page);
}

?>