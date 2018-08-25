<?php
defined('YIMAOMONEY') or exit('Access denied');
function yimao_writelog($c,$t=1){
	global $db,$_username;
	$url=basename($_SERVER['PHP_SELF']);
	if(!empty($_SERVER["QUERY_STRING"])) $url.="?".$_SERVER["QUERY_STRING"];
	$db->yiexec("insert into ymuh(uh002,uh003,uh004,uh005,uh006) values('".$_username."',".time().",$t,'$c','$url')");
}

function getconfig(){
	global $db;	
	$db->yiquery("select * from ymus");
	foreach ($db->rs as $k => $v) {
		if(strstr($v['us004'],'|')){
			$arr=explode('|',$v['us004']);
			$GLOBALS['YCF'][$v['us001']]=$arr;
		}else{
			$GLOBALS['YCF'][$v['us001']]=$v['us004'];
		}
	}

	// $db->yiquery("select * from ymus");
	// $ws=array();
	// foreach ($db->rs as $k => $v) {
	// 	if(strstr($v['us004'],'|')){
	// 		$ws[$v['us001']]=explode('|',$v['us004']);
	// 	}else{
	// 		$ws[$v['us001']]=$v['us004'];
	// 	}
	// }
	// write_static_cache($ws);
}

//手松转收益 
function chkzhuanshouyi($arr){
	global $YCF;	
	if($arr['gp018']==0&&$arr['gp020']>=$YCF["jjsyt"]&&$arr["gp005"]==$arr["gp023"]){
            // $cha=(time()-$arr['gp004'])/(24*3600);
            // if($cha>=$YCF['pdday']){
            	return true;
            // }
	}
	return false;
}


function getchicheng($arr){
	global $YCF;	

	if(($arr["uv029"]>=$YCF["jjjlr"][0]&&$arr["uv016"]>=$YCF["jjjlr"][4]&&$arr["uv067"]>=$YCF["jjjlr"][3])||$arr["uv032"]==1){
		return $YCF["jjjlr"][2];
	}else{
		return $YCF["jjjlr"][1];
	}
}


//提供帮助倒计时
function chkcunkdaojishi(){
	global $db,$YCF,$member;	

	try{
		$db->begintransaction();

	$db->yiquery("select pd002 from ymupd where  pd011=0 and TIMESTAMPDIFF(minute , from_unixtime(pd005,'%Y-%m-%d %H:%i:%S'),  '".date("Y-m-d H:i:s")."')>=".$YCF["jjqkok"]." and pd009=0 and pd008 is null");
	$rs=$db->rs;
	foreach ($rs as $k => $v) {

		$db->yipre("select pd002,pd003,pd001,pd004 from ymupd where pd011=0 and pd002=:t0",1,array(array(':t0'),array($v["pd002"])));
		$rss=$db->rs;
		foreach ($rss as $key => $value) {

			//更新配对信息已作废
			$db->yipre("update ymupd set pd011=1 where pd011=0 and pd001=:t0",4,array(array(':t0'),array($value["pd001"])));

			//获取提供帮助信息
			$db->yipre("select * from ymugp where gp005!=gp023 and gp009<2 and gp001=:t0",2,array(array(':t0'),array($value["pd002"])));
			$gps=$db->rs;

			if(!empty($gps)){
			//冻结提供帮助人
			$db->yipre("update ymuv set uv010=1,uv049='提供帮助未上传汇款证明' where uv001=:t0",4,array(array(':t0'),array($gps["gp002"])));


			//更新提供帮助记录已冻结
			$db->yipre("update ymugp set gp009=2 where gp001=:t0",4,array(array(':t0'),array($gps["gp001"])));	

		      $jin=$gps["gp019"]+$gps["gp005"];
		      $jk=$gps["gp021"];
		      $bz='存款编号'.$gps["gp013"];
		      if($jin>0)
		      yimao_writeaccount(array($gps["gp002"],"1",1,time(),20,$jin,'存款编号'.$gps["gp013"]));		
          if($jk>0){
             yimao_writeaccount(array($gps["gp002"],"1",5,time(),20,$jk,'存款编号'.$gps["gp013"]));

          }
		      
		      $db->yiquery("select ud002,sum(ud006) ud006,sum(ud009) ud009 from ymud where ud010=0 and ud005!=51 and ud008=".$gps["gp001"]." group by ud002");
		      $all=$db->rs; 
			  $db->yipre("update ymud set ud010=1 where ud010=0 and ud005!=51 and ud008=:t0",4,array(array(':t0'),array($gps["gp001"])));	
		      foreach ($all as $kk => $vv) {
	
		          $us=$member->getuserinfo($vv["ud002"]);
		          $jin=($vv["ud006"]-$vv["ud009"]);
		         if($jin>0)
			      yimao_writeaccount(array($vv["ud002"],"1",2,time(),20,$jin,$bz));
			   yimao_writeaccount(array($vv["ud002"],"1",5,time(),20,$vv["ud009"],$bz));

			     //  if($vv["ud009"]>0){
			     //  	yimao_writeaccount(array($v["gp002"],"0",7,time(),18,$vv["ud009"],''));
			  	  // }

		      }   

			}

			//更新获取帮助
			$db->yipre("update ymugq set gq012=gq012-:t1,gq009=0 where gq001=:t0",4,array(array(':t0',':t1'),array($value["pd003"],$value["pd004"])));	



		     //  if($jk>0){
		     //  	yimao_writeaccount(array($v["gp002"],"1",7,time(),18,$jk,''));

		  	  // }




						
		}
	
	}	

		$db->committransaction();
	}catch(PDOException $e){
		$db->rollbacktransaction();
		a_bck("error");
	}	

}

//获取帮助倒计时
function chkqukdaojishi(){
	global $db,$YCF;	


	try{
		$db->begintransaction();

	$db->yiquery("select pd003 from ymupd where pd011=0 and pd009=0 and pd010 is null and pd008 is not null and TIMESTAMPDIFF(minute , from_unixtime(pd008,'%Y-%m-%d %H:%i:%S'),  '".date("Y-m-d H:i:s")."')>=".$YCF["jjqkok"]." ");
	$rs=$db->rs;
	foreach ($rs as $k => $v) {

		$db->yipre("select  *  from ymupd where pd011=0 and pd003=:t0",1,array(array(':t0'),array($v["pd003"])));
		$rss=$db->rs;
		foreach ($rss as $key => $value) {
			//获取获取帮助信息
			if($value["pd006"]!=1){
			$db->yipre("select * from ymugq where gq005!=gq023 and gq009!=2 and  gq001=:t0",2,array(array(':t0'),array($value["pd003"])));
			$gps=$db->rs;

			if(!empty($gps)){

			//冻结获取帮助人
			$db->yipre("update ymuv set uv010=1,uv049='提供帮助人未确认汇款' where uv001=:t0",4,array(array(':t0'),array($gps["gq002"])));

			//更新获取帮助记录已冻结
			$db->yipre("update ymugq set gq009=2 where gq001=:t0",4,array(array(':t0'),array($gps["gq001"])));	
			}
			//更新配对信息已作废
			$db->yipre("update ymupd set pd011=1 where pd011=0 and pd003=:t0",4,array(array(':t0'),array($value["pd003"])));	
			// //更新提供帮助
			// $db->yipre("update ymug set gq012=0 where gq001=:t0",4,array(array(':t0'),array($value["pd003"])));	
			}else{
			$db->yipre("select * from ymugp where gq005!=gq023 and gq009!=2 and   gp001=:t0",2,array(array(':t0'),array($value["pd003"])));
			$gps=$db->rs;


			if(!empty($gps)){
			//冻结获取帮助人
			// $db->yipre("update ymuv set uv010=1,uv049='提供帮助人未确认汇款' where uv001=:t0",4,array(array(':t0'),array($gps["gq002"])));

			// yimao_writeaccount(array($gps["gq002"],"1",5,time(),20,1,'提供帮助人未确认汇款'));
			//更新获取帮助记录已冻结
			$db->yipre("update ymugp set gp009=2 where gp001=:t0",4,array(array(':t0'),array($gps["gp001"])));	
			}
			
			//更新配对信息已作废
			$db->yipre("update ymupd set pd011=1 where pd011=0 and pd003=:t0",4,array(array(':t0'),array($value["pd003"])));	
			// //更新提供帮助
			// $db->yipre("update ymug set gq012=0 where gq001=:t0",4,array(array(':t0'),array($value["pd003"])));
			}			
		}
	
	}
		$db->committransaction();
	}catch(PDOException $e){
		$db->rollbacktransaction();
		a_bck("error");
	}	

}

function dongjie(){
	global $db,$YCF;	

	// $db->yiexec("update ymuv set uv010=1,uv049='".$YCF["jjdj"]."分钟不存钱，也不直推会员' where uv010=0 and uv016=0 and uv003=uv046 and TIMESTAMPDIFF(minute , from_unixtime(uv046,'%Y-%m-%d %H:%i:%S'),  '".date("Y-m-d H:i:s")."')>=".$YCF["jjdj"]);
	$db->yiquery("select uv001 from ymuv where uv010=0 and uv016=0 and uv003=uv046 and TIMESTAMPDIFF(minute , from_unixtime(uv046,'%Y-%m-%d %H:%i:%S'),  '".date("Y-m-d H:i:s")."')>=".$YCF["jjdj"]);
	foreach ($db->rs as $k => $v) {
		$db->yiexec("update ymuv set uv010=1,uv049='".$YCF["jjdj"]."分钟不存钱，也不直推会员' where uv001=".$v["uv001"]);
		yimao_writeaccount(array($v["uv001"],"1",5,time(),20,1,$YCF["jjdj"].'分钟不存钱，也不直推会员'));
	}
}

function chkqucun(){
	global $db,$YCF,$_userid,$username;
	if($r["uv006"]<2) return;

	try{
		$db->begintransaction();
		
	$db->yiqury("select gq010 from ymugq where gq002=$_userid order by gq001 desc limit 1",2);
	if(empty($db->rs)) return;
	$rs=$db->rs;

	$db->yiqury("select gp004 from ymugp where gp011=0 and gp002=$_userid order by gp001 desc limit 1",2);
	if(empty($db->rs)){
		$cha=(time()-$rs["gq010"])/(24*3600)*(24)*(60);

		if($cha>$YCF["jjqc"]){
			$db->yiexec("udpate ymuv set uv010=1,uv011='获取帮助完成后未提供帮助' where uv001=$_userid");

			locationurl('index.php?u=1');
		}
	}else{
		if($db->rs["gp004"]>$rs["gq010"]){
			$db->yiexec("udpate ymuv set uv010=1,uv011='获取帮助完成后未提供帮助' where uv001=$_userid");

			locationurl('index.php?u=1');
		}
	}

		$db->committransaction();
	}catch(PDOException $e){
		$db->rollbacktransaction();
		a_bck("error");
	}	

}

function getlevel($v,$t=0){
	global $YCF;
	
	if(is_array($YCF["jjname"])){
		if($t){
			return $YCF["jjrmb"][$v];
		}else{
			return $YCF["jjname"][$v];
		}
		
	}else{
		if($t){
			return $YCF["jjrmb"];
		}else{
			return $YCF["jjname"];
		}		
		
	}
}
function getnews(){
	global $db;
	$db->yiquery('select * from ymun where un007=0 and un004=3 order by un006 desc,un003 desc limit 1',2);
	$rn=$db->rs;

	$db->yiquery('select * from ymun where un007=0 order by un006 desc,un003 desc limit 6');
	$rns=$db->rs;
	return array($rn,$rns);
}

function yimao_editinfo($k,$v,&$arr){

	switch ($k) {
		case 'rgmbq':
			$arr['um008']=$v;
		break;
		case 'rgmba':
			$arr['um009']=$v;
		break;		
		case 'rgmobile':
			$arr['um010']=$v;
		break;
		case 'rgemail':
			$arr['um011']=$v;
		break;
		case 'rgcard':
			$arr['um012']=$v;
		break;	
		case 'rgaddress':
			$arr['um013']=$v;
		break;	
		case 'rgweixin':
			$arr['um014']=$v;
		break;
		case 'rgqq':
			$arr['um015']=$v;
		break;	
		case 'rgsex':
			$arr['um016']=$v;
		break;	
		case 'rgfax':
			$arr['um017']=$v;
		break;	
		case 'province':
			$arr['um018']=$v;
		break;	
		case 'city':
			$arr['um019']=$v;
		break;	
		case 'area':
			$arr['um020']=$v;
		break;																		
	}

}

//
function yimao_writeaccount($arr){
	global $db,$YCF,$member;
	$us=$member->getuvinfo($arr[0],"uv001,uv002,uv012,uv013,uv014,uv015,uv065,uv066,uv048");
	$jin=$arr[5];

	if($arr[1]==0){
		if($arr[2]==0){
			$sql="update ymuv set uv012=uv012+:t1 where uv001=:t0";
			$yu=$us["uv012"]+$jin;
		}elseif($arr[2]==1){
			$sql="update ymuv set uv013=uv013+:t1 where uv001=:t0";
			$yu=$us["uv013"]+$jin;
		}elseif($arr[2]==2){
			$sql="update ymuv set uv014=uv014+:t1 where uv001=:t0";
			$yu=$us["uv014"]+$jin;
		}elseif($arr[2]==3){
			$sql="update ymuv set uv015=uv015+:t1 where uv001=:t0";
			$yu=$us["uv015"]+$jin;
		}elseif($arr[2]==4){
			$sql="update ymuv set uv065=uv065+:t1 where uv001=:t0";
			$yu=$us["uv065"]+$jin;
		}elseif($arr[2]==5){
			$sql="update ymuv set uv064=uv064+:t1 where uv001=:t0";
			$yu=$us["uv066"]+$jin;
		}elseif($arr[2]==6){
			$sql="update ymuv set uv065=uv065+:t1 where uv001=:t0";
			$yu=0;
		}elseif($arr[2]==7){
			$sql="update ymuv set uv048=uv048+:t1 where uv001=:t0";
			$yu=$us["uv048"]+$jin;
		}
		$db->yipre($sql,4,array(array(':t0',':t1'),array($us["uv001"],$jin)));	


		$sql="insert into ymuf(uf010,uf002,uf003,uf004,uf005,uf007,uf008,uf009) values(:t1,:t2,:t3,:t4,:t5,:t6,:t7,:t8)";
		$db->yipre($sql,4,array(array(':t1',':t2',':t3',':t4',':t5',':t6',':t7',':t8'),array($us["uv001"],$us["uv002"],$arr[3],$arr[4],$jin,$yu,$arr[2],$arr[6])));	
	}else{
		if($arr[2]==0){
			$sql="update ymuv set uv012=uv012-:t1 where uv001=:t0";
			$sqll="update ymuv set uv012=0 where uv001=:t0 and uv012<0";
			$yu=$us["uv012"]-$jin;
			if($yu<0) $yu=0;
		}elseif($arr[2]==1){
			$sql="update ymuv set uv013=uv013-:t1 where uv001=:t0";
			$sqll="update ymuv set uv013=0 where uv001=:t0 and uv013<0";
			$yu=$us["uv013"]-$jin;
			if($yu<0) $yu=0;
		}elseif($arr[2]==2){
			$sql="update ymuv set uv014=uv014-:t1 where uv001=:t0";
			$sqll="update ymuv set uv014=0 where uv001=:t0 and uv014<0";
			$yu=$us["uv014"]-$jin;
			if($yu<0) $yu=0;
		}elseif($arr[2]==3){
			$sql="update ymuv set uv015=uv015-:t1 where uv001=:t0";
			$sqll="update ymuv set uv015=0 where uv001=:t0 and uv015<0";
			$yu=$us["uv015"]-$jin;
			if($yu<0) $yu=0;
		}elseif($arr[2]==4){
			$sql="update ymuv set uv065=uv065-:t1 where uv001=:t0";
			$yu=$us["uv065"]-$jin;
			if($yu<0) $yu=0;
		}elseif($arr[2]==5){
			$sql="update ymuv set uv064=uv064-:t1 where uv001=:t0";
			$yu=$us["uv066"]-$jin;	
			if($yu<0) $yu=0;		
		}elseif($arr[2]==6){
			$sql="update ymuv set uv065=uv065-:t1 where uv001=:t0";
			$yu=0;
		}elseif($arr[2]==7){
			$sql="update ymuv set uv048=uv048-:t1 where uv001=:t0";
			$yu=$us["uv048"]-$jin;
		}
		$db->yipre($sql,4,array(array(':t0',':t1'),array($us["uv001"],$jin)));	
		if(!empty($sqll))
		$db->yipre($sqll,4,array(array(':t0'),array($us["uv001"])));	


		$sql="insert into ymuf(uf010,uf002,uf003,uf004,uf006,uf007,uf008,uf009) values(:t1,:t2,:t3,:t4,:t5,:t6,:t7,:t8)";
		$db->yipre($sql,4,array(array(':t1',':t2',':t3',':t4',':t5',':t6',':t7',':t8'),array($us["uv001"],$us["uv002"],$arr[3],$arr[4],$jin,$yu,$arr[2],$arr[6])));	
	}
}	

function yimao_autoinfo($k,$arr){
	switch ($k) {
		case 'rgmba':
			return $arr['um009'];
		break;
		case 'rgmobile':
			return $arr['um010'];
		break;
		case 'rgemail':
			return $arr['um011'];
		break;
		case 'rgcard':
			return $arr['um012'];
		break;	
		case 'rgaddress':
			return $arr['um013'];
		break;	
		case 'rgweixin':
			return $arr['um014'];
		break;
		case 'rgqq':
			return $arr['um015'];
		break;	
		case 'rgsex':
			return getsex($arr['um016']);
		break;	
		case 'rgfax':
			return $arr['um017'];
		break;	
		case 'rgprovince':
			return $arr['um018'].$arr['um019'].$arr['um020'];
		break;														
	}

}

function getrand(){
	global $db;
	for($i=0;$i<6;$i++){
		$string.=rand(0,9);
	}
	$string="Y".$string;
	$db->yiquery("select um001 from ymum where um002='$string'",1);
	if(!empty($db->rs))
	{
		getrand();
	}else{
		return $string;
	}	
}

function getregform($r){
	global $YCF,$db,$config;

	
	$rr=$config->getregconfig();


	$s='';
	foreach ($YCF['anrea'] as $k => $v) {
		$s.='<label><input type="radio" name="anrea" value="'.$k.'" '.(($r['anrea']==="$k")?'checked':'').'>&nbsp;'.$v.'</label>&nbsp;&nbsp;';
	}
	$arr['anrea']=$s;

	$s='';
	if(is_array($YCF['jjname'])){
		foreach ($YCF['jjname'] as $k => $v) {
			$s.='<label><input type="radio" name="ulevel" value="'.$k.'">&nbsp;'.$v.'('.$YCF['jjrmb'][$k].')</label>&nbsp;&nbsp;';
		}
	}else{
			$s.='<label><input type="radio" name="ulevel" value="0">&nbsp;'.$YCF['jjname'].'('.$YCF['jjrmb'].')</label>&nbsp;&nbsp;';
	}
	$arr['ulevel']=$s;

	$arr['bdname']='<input type="text" name="bdname" id="bdname" value="'.$r['bdname'].'" class="'.$r['class'].'" maxlength="15">';
	$arr['tuname']='<input type="text" name="tuname" id="tuname" value="'.$r['tuname'].'" class="'.$r['class'].'" maxlength="15">';
	$arr['anname']='<input type="text" name="anname" id="anname" value="'.$r['anname'].'" class="'.$r['class'].'" maxlength="15">';

	$arr['sex']='<label><input type="radio" name="rgsex" value="0" '.(($r['sex']==0)?'checked':'').'>&nbsp;男</label>&nbsp;&nbsp;<label><input type="radio" name="rgsex" value="1" '.(($r['sex']==1)?'checked':'').'>&nbsp;女</label>&nbsp;&nbsp;<label><input type="radio" name="rgsex" value="2" '.(($r['sex']==2)?'checked':'').'>&nbsp;未知</label>';
	
	if(!empty($rr['rgmbq'])){
	$arr['mbq']='<select name="rgmbq" class="'.$r['sclass'].'"><option value="0">请选择您的密保问题?</option>';
	foreach ($rr['rgmbq'] as $k => $v) {
		$arr['mbq'].='<option value="'.($k+1).'" '.($r['mbq']==($k+1)?"selected":"").'>'.$v.'</option>';
	}
	$arr['mbq'].='</select>';	
	}

	$arr['province']='<select name="province" id="province"  class="'.$r['sclass'].'"></select>&nbsp;<select name="city" id="city"  class="'.$r['sclass'].'"></select>&nbsp;<select name="area" id="area"  class="'.$r['sclass'].'"></select>
		<script type="text/javascript">
		new PCAS("province","city","area","'.$r["province"].'","'.$r["city"].'","'.$r["area"].'");
		</script>';
	return $arr;
}



function getanpoint($p,$anseat){
	return ($p*3)+$anseat-1;
}



function chkreginfo(&$arr){
	global $member;
	$s='';
	$d=array();
	foreach ($arr as $k => $v) {
		switch ($k) {
			case 'uv002':
				if(empty($v)){
					$s='请输入会员账号';
					break 2;
				}
				if(!chkusername($v)){
					$s='会员账号必须是以字母开头，长度是4-15位的字母，数字和下划线的组合';
					break 2;
				}
				$r=$member->user_exists($v);
				if(!empty($r)){
					$s='会员账号已经被使用';
					break 2;
				}				
			break;
			case 'uv017':
				if(empty($v)){
					$s='请输入服务中心';
					break 2;
				}
				if(!chkusername($v)){
					$s='服务中心输入不正确';
					break 2;
				}
				$r=$member->bd_exists($v);
				if(empty($r)){
					$s='服务中心不存在';
					break 2;
				}
				$arr['uv017']=$r['uv001'];
			break;
			case 'uv018':
				if(empty($v)){
					$s='请输入推荐人';
					break 2;
				}
				if(!chkusername($v)){
					$s='推荐人输入不正确';
					
				}
				$r=$member->tu_exists($v);
				if(empty($r)){
					$s='推荐人不存在或者未开通';
					break 2;
				}
				$arr['uv018']=$r['uv001'];
				$arr['uv020']=$r['uv020'].$r['uv001'].',';
				$arr['uv022']=$r['uv022']+1;
				
				$d['uv018']=$r['uv001'];			
			break;	

			case 'uv006':
				if($v===''){
					$s='请选择注册级别';
					break 2;
				}
			break;							
		}
	}
	return $s;
}

function getjjtype($s){
	global $YCF;
	$a='';
	foreach ($YCF['prizeval'] as $k => $v) {
		if($s==$v){
			$a=$YCF['prizename'][$k];
			break;
		}
	}
	return $a;
}


function gethbtype(){
	return array('1'=>'手工充值','2'=>'申请充值','3'=>'转账','8'=>'转换','9'=>'升级','23'=>'存款','10'=>'取款','11'=>'手动转收益','16'=>'自动转收益','17'=>'会员取消存款','18'=>'手动取消存款','20'=>'存款超时取消','24'=>'会员取消取款','22'=>'取消取款','19'=>'佣金自动转现金');
}


function getpwd($s){

	return md5(md5($s.YMJIAMI));
}
?>