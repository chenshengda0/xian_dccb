<?php
defined('YIMAOMONEY') or exit('Access denied');
function fun_deluser($kid){
	global $db,$YCF,$member,$r;
	$karr=array();

	try{
		$db->begintransaction();
		$sql="select uv001,uv002,uv006,uv017,uv018,uv019,uv020,uv021,uv022,uv023,uv024,uv012,uv013 from ymuv where uv006=0 and uv016=0 and uv001 in($kid) order by uv001 asc";
		$db->yiquery($sql);
		$rs=$db->rs;
		foreach ($rs as $k => $v) {
			if($member->downtu_exists($v['uv020'])) a_bck($v['uv002']."的下级还有未开通的会员，请先删除下级");


			$db->yiexec('delete from ymuv where uv001='.$v['uv001']);



			$karr[]=$v["uv002"];
		}
		$db->committransaction();
		$db->yiexec('delete from ymum where um001 in('.$kid.')');
	}catch(PDOException $e){
		$db->rollbacktransaction();
		a_bck("error");
	}	

	return $karr;
}

function fun_openuserkong($kid,$opid,$flag){

}

function fun_openuser($kid,$opid,$flag){
	global $db,$YCF,$member,$r;
	$karr=array();

	try{
		$db->begintransaction();
		$rt=fun_insertTime();

		$sql="select uv001,uv002,uv006,uv017,uv018,uv019,uv020,uv021,uv022,uv023,uv024,uv012,uv013,uv045,uv065,uv015 from ymuv where uv008=0 and uv001 in($kid) order by uv001 asc";
		$db->yiquery($sql);
		$rs=$db->rs;
		foreach ($rs as $k => $v) {

			if(!$flag){
				if($r['uv015']<$v["uv045"]){
					a_bck("您的激活币账户余额不足 ".$v["uv045"]);
					exit;
				}
				yimao_writeaccount(array($opid,"1",3,time(),10,$v["uv045"],'开通'.$v["uv002"]));
			}


			if($member->upperuser_exists($v['uv020'])) a_bck($v['uv002']."的上级还有未开通的会员，请先开通上级");
		
			$kdate=time();
			$ktr=$opid.'|'.$flag;
			$db->yipre("update ymuv set uv008=1,uv004=:t1,uv046=:t1,uv037=:t2,uv043=:t3,uv046=:t1 where uv001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v['uv001'],$kdate,$ktr,$rt['ut001'])));

	

			$db->yipre("update ymuv set uv016=uv016+1 where uv001=:t0",4,array(array(':t0'),array($v['uv018'])));

			$db->yiquery("update ymuv set uv029=uv029+1 where uv001 in(0".$v["uv020"]."0)",4);

			fun_sj($v["uv018"],$v["uv002"]);

			$karr[]=$v["uv002"];
		}

		$db->committransaction();
	}catch(PDOException $e){
		$db->rollbacktransaction();
		a_bck("error");
	}

	return $karr;
}

function fun_touxie($arr){
	global $db,$YCF,$member,$r;

	if($arr["uv007"]==6) return;
	if($arr["uv047"]>=$YCF["jjtxnj"][0]&&$arr["uv047"]<$YCF["jjtxnj"][1]&&$arr["uv007"]<1){
		$db->yiexec("update ymuv set uv007=1 where uv001=".$arr["uv001"]);
	}elseif($arr["uv047"]>=$YCF["jjtxnj"][1]&&$arr["uv047"]<$YCF["jjtxnj"][2]&&$arr["uv007"]<2){
		$db->yiexec("update ymuv set uv007=2 where uv001=".$arr["uv001"]);
	}elseif($arr["uv047"]>=$YCF["jjtxnj"][2]&&$arr["uv047"]<$YCF["jjtxnj"][3]&&$arr["uv007"]<3){
		$db->yiexec("update ymuv set uv007=3 where uv001=".$arr["uv001"]);		
	}elseif($arr["uv047"]>=$YCF["jjtxnj"][3]&&$arr["uv047"]<$YCF["jjtxnj"][4]&&$arr["uv007"]<4){
		$db->yiexec("update ymuv set uv007=4 where uv001=".$arr["uv001"]);
	}elseif($arr["uv047"]>=$YCF["jjtxnj"][4]&&$arr["uv047"]<$YCF["jjtxnj"][5]&&$arr["uv007"]<5){
		$db->yiexec("update ymuv set uv007=5 where uv001=".$arr["uv001"]);
	}elseif($arr["uv047"]>=$YCF["jjtxnj"][5]&&$arr["uv007"]<6){
		$db->yiexec("update ymuv set uv007=6 where uv001=".$arr["uv001"]);
	}

}

function fun_xunhuan(){
	global $db,$YCF,$member,$r;


	try{
		$db->begintransaction();

		$db->yiquery("select * from ymugp where gp011=0 and gp009=0 and (TIMESTAMPDIFF(minute , from_unixtime(gp004,'%Y-%m-%d %H:%i:%S'),  '".date("Y-m-d H:i:s")."')>=".$YCF["jjcunpei"]." and gp017=0) order by gp001 asc");
		$all=$db->rs;
		foreach ($all as $k => $v) {
			fun_bd($v);
		}

		$db->committransaction();
	}catch(PDOException $e){
		$db->rollbacktransaction();
		a_bck("error");
	}	

}

function fun_xunhuan1(){
	global $db,$YCF,$member,$r;


	try{
		$db->begintransaction();

		$db->yiquery("select * from ymugp where gp011=0 and gp009=0 order by gp001 asc");
		$all=$db->rs;
		foreach ($all as $k => $v) {
			fun_bd2($v);
		}

		$db->committransaction();
	}catch(PDOException $e){
		$db->rollbacktransaction();
		a_bck("error");
	}	

}

function fun_xunhuan2($cunid,$quid){
	global $db,$YCF,$member,$r;


	try{
		$db->begintransaction();

		$db->yiquery("select * from ymugp where gp002=".$cunid." and gp011=0 and gp009=0 order by gp001 asc");
		$all=$db->rs;
		foreach ($all as $k => $v) {
			fun_bd4($v,$quid);
		}

		$db->committransaction();
	}catch(PDOException $e){
		$db->rollbacktransaction();
		a_bck("error");
	}	

}

// function fun_jdj($arr){
// 	global $db,$YCF,$member,$r;
// 	$maxceng=max($YCF["jjjdjd"]);
// 	if($maxceng<=0) return;

// 	$sql="select uv001,uv002,uv006 from ymuv where uv001 in(0".$arr[0]."0) order by uv001 desc limit ".($maxceng);
// 	$db->yiquery($sql);
// 	$rss=$db->rs;
// 	foreach ($rss as $k => $v) {
// 		$dai=$YCF["jjjdjd"][$v["uv006"]];
// 		if(($k+1)>$dai) continue;

// 			if($v["uv006"]==0){
// 				$jin=$qian*$YCF["jjjdj1"][$k]/100;
// 			}elseif($v["uv006"]==1){
// 				$jin=$qian*$YCF["jjjdj2"][$k]/100;
// 			}elseif($v["uv006"]==2){
// 				$jin=$qian*$YCF["jjjdj3"][$k]/100;
// 			}elseif($v["uv006"]==3){
// 				$jin=$qian*$YCF["jjjdj4"][$k]/100;
// 			}	
			

// 		$jk=0;
// 		$jin1=$jin-$jk;

// 		if($jin>0){
// 			$db->yipre("update ymuv set uv054=uv054+:t1,uv062=uv062+:t2 where uv001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v['uv001'],$jin,$jin1,$jk)));

// 			fun_jjxx2(array($v['uv001'],$arr[3],time(),54,$jin,'第'.($k+1).'代'.$arr[2].'存款'.$arr[1],$arr[4],$jk,0));
// 		}
// 	}
// }

function fun_jlj($arr){
	global $db,$YCF,$member,$r;

	$jin=$arr[1]*$YCF["jjjlr"][5]/100;
	if($jin<=0) return;

	$sql="select uv001,uv002,uv006,uv022 from ymuv where uv001 in(0".$arr[0]."0) and ((uv029>=".$YCF["jjjlr"][0]." and uv016>=".$YCF["jjjlr"][4]." and uv067>=".$YCF["jjjlr"][3].") or uv032=1) order by uv001 desc limit 1";
	$db->yiquery($sql);
	$rss=$db->rs;
	foreach ($rss as $k => $v) {

		$dai=$arr[5]-$v["uv022"];

		$jk=floor($jin*$YCF["jjxjk"][$v["uv006"]]/100*100)/100;
		$jin1=$jin-$jk;

		// if($jin>0){
			$db->yipre("update ymuv set uv055=uv055+:t1,uv061=uv061+:t2,uv063=uv063+:t3 where uv001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v['uv001'],$jin,$jk,$jin1)));

			fun_jjxx2(array($v['uv001'],$arr[3],time(),55,$jin,'第'.$dai.'代'.$arr[2].'存款'.$arr[1],$arr[4],$jk,0));
		// }
	}
}

function fun_bd($arr){
	global $db,$YCF,$member,$r;

	$arr["gp012"]=$arr["gp005"]-$arr["gp012"];
	$db->yiquery("select * from ymugp where gp009=0 and gp011=1");
	$rs=$db->rs;
	foreach ($rs as $k => $gs) {

		// if(!empty($gs)){
			$yu=$gs["gp005"]-$gs["gp012"];
			if($arr["gp012"]>$yu){

				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($gs["gp001"],$yu,1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1 where gp001=:t0",4,array(array(':t0',':t1'),array($arr["gp001"],$yu)));

				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006,pd012) values(".$arr["gp001"].",".$gs["gp001"].",".$yu.",".time().",1,0)");
				$arr["gp012"]=$arr["gp012"]-$yu;

			}elseif($arr["gp012"]<$yu){

				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$arr["gp012"],1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1 where gp001=:t0",4,array(array(':t0',':t1'),array($gs["gp001"],$arr["gp012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006,pd012) values(".$arr["gp001"].",".$gs["gp001"].",".$arr["gp012"].",".time().",1,0)");			
				return;
			}else{
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$yu,1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($gs["gp001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006,pd012) values(".$arr["gp001"].",".$gs["gp001"].",".$yu.",".time().",1,0)");					
				return;
			}
		// }
	
	}


	//寻找获取帮助里面剩余配对金额和提供帮助金额接近的会员，还有100提供帮助的配对金额找出比100大于或等于的获取帮助金额，
	$db->yiquery("select * from (select *,(gq005-gq012-".$arr["gp012"].") as a from ymugq where  gq002!=".$arr["gp002"]." and gq015!='".$arr["gp015"]."' and gq009=0) as t where t.a>=0 ORDER BY t.a asc,RAND()  limit 1");

	if(count($db->rs)){
		$dz=$db->rs;
		foreach ($dz as $k => $v) {
			$yu=$v["gq005"]-$v["gq012"];
			if($arr["gp012"]==$yu){
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$yu,1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");					
				return;
			}else{
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$arr["gp012"],1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1 where gq001=:t0",4,array(array(':t0',':t1'),array($v["gq001"],$arr["gp012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$arr["gp012"].",".time().",0)");					
				return;
			}	
		}
	}else{
		$db->yiquery("select * from (select *,(gq005-gq012-".$arr["gp012"].") as a from ymugq where   gq002!=".$arr["gp002"]." and gq015!='".$arr["gp015"]."' and gq009=0) as t where t.a<0 ORDER BY t.a desc,RAND()");
		$dz=$db->rs;
		foreach ($dz as $k => $v) {
		
			$yu=$v["gq005"]-$v["gq012"];
			if($arr["gp012"]>$yu){

				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1 where gp001=:t0",4,array(array(':t0',':t1'),array($arr["gp001"],$yu)));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");			

				$arr["gp012"]=$arr["gp012"]-$yu;
			}elseif($arr["gp012"]==$yu){
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$yu,1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");				
				return;
			}else{
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$arr["gp012"],1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1 where gq001=:t0",4,array(array(':t0',':t1'),array($v["gq001"],$arr["gp012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$arr["gp012"].",".time().",0)");				
				return;
			}
		}		
	}


	//时间
	$db->yiquery("select *  from (select *,(gq005-gq012-".$arr["gp012"].") as a from ymugq where    gq009=0 and gq002!=".$arr["gp002"].") as t where t.a>=0 order by t.a asc,t.gq004 asc limit 1");	
	if(count($db->rs)){
		$sj=$db->rs;
		foreach ($sj as $k => $v) {
			$yu=$v["gq005"]-$v["gq012"];
			if($arr["gp012"]==$yu){
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$yu,1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");						
				return;
			}else{
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$arr["gp012"],1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1 where gq001=:t0",4,array(array(':t0',':t1'),array($v["gq001"],$arr["gp012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$arr["gp012"].",".time().",0)");
				return;
			}
		}
	}else{
		$db->yiquery("select *  from (select *,(gq005-gq012-".$arr["gp012"].") as a from ymugq where    gq009=0 and gq002!=".$arr["gp002"].") as t where t.a<0 order by t.a asc,t.gq004 asc");
		$sj=$db->rs;
		foreach ($sj as $k => $v) {
			$yu=$v["gq005"]-$v["gq012"];
			if($arr["gp012"]>$yu){
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1 where gp001=:t0",4,array(array(':t0',':t1'),array($arr["gp001"],$yu)));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");				
				$arr["gp012"]=$arr["gp012"]-$yu;	
			}elseif($arr["gp012"]==$yu){
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$yu,1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");					
				return;
			}else{
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$arr["gp012"],1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1 where gq001=:t0",4,array(array(':t0',':t1'),array($v["gq001"],$arr["gp012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$arr["gp012"].",".time().",0)");					
				return;
			}
		}			
	}

}


function fun_bd2($arr){
	global $db,$YCF,$member,$r;

	$arr["gp012"]=$arr["gp005"]-$arr["gp012"];
	$db->yiquery("select * from ymugp where gp009=0 and gp011=1");
	$rs=$db->rs;
	foreach ($rs as $k => $gs) {

			// if(!empty($gs)){

			$yu=$gs["gp005"]-$gs["gp012"];
			if($arr["gp012"]>$yu){

				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($gs["gp001"],$yu,1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1 where gp001=:t0",4,array(array(':t0',':t1'),array($arr["gp001"],$yu)));

				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$gs["gp001"].",".$yu.",".time().",1)");
				$arr["gp012"]=$arr["gp012"]-$yu;

			}elseif($arr["gp012"]<$yu){

				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$arr["gp012"],1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1 where gp001=:t0",4,array(array(':t0',':t1'),array($gs["gp001"],$arr["gp012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$gs["gp001"].",".$arr["gp012"].",".time().",1)");			
				return;
			}else{
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$yu,1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($gs["gp001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$gs["gp001"].",".$yu.",".time().",1)");					
				return;
			}
		// }
	}

	//寻找获取帮助里面剩余配对金额和提供帮助金额接近的会员，还有100提供帮助的配对金额找出比100大于或等于的获取帮助金额，
	$db->yiquery("select * from (select *,(gq005-gq012-".$arr["gp012"].") as a from ymugq where  gq002!=".$arr["gp002"]." and gq015!='".$arr["gp015"]."' and gq009=0) as t where t.a>=0 ORDER BY t.a asc,RAND()  limit 1");

	if(count($db->rs)){
		$dz=$db->rs;
		foreach ($dz as $k => $v) {
			$yu=$v["gq005"]-$v["gq012"];
			if($arr["gp012"]==$yu){
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$yu,1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");					
				return;
			}else{
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$arr["gp012"],1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1 where gq001=:t0",4,array(array(':t0',':t1'),array($v["gq001"],$arr["gp012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$arr["gp012"].",".time().",0)");					
				return;
			}	
		}
	}else{
		$db->yiquery("select * from (select *,(gq005-gq012-".$arr["gp012"].") as a from ymugq where  gq002!=".$arr["gp002"]." and gq015!='".$arr["gp015"]."' and gq009=0) as t where t.a<0 ORDER BY t.a desc,RAND()");
		$dz=$db->rs;
		foreach ($dz as $k => $v) {
		
			$yu=$v["gq005"]-$v["gq012"];
			if($arr["gp012"]>$yu){

				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1 where gp001=:t0",4,array(array(':t0',':t1'),array($arr["gp001"],$yu)));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");			

				$arr["gp012"]=$arr["gp012"]-$yu;
			}elseif($arr["gp012"]==$yu){
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$yu,1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");				
				return;
			}else{
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$arr["gp012"],1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1 where gq001=:t0",4,array(array(':t0',':t1'),array($v["gq001"],$arr["gp012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$arr["gp012"].",".time().",0)");				
				return;
			}
		}		
	}


	//时间
	$db->yiquery("select *  from (select *,(gq005-gq012-".$arr["gp012"].") as a from ymugq where  gq009=0 and gq002!=".$arr["gp002"].") as t where t.a>=0 order by t.a asc,t.gq004 asc limit 1");	
	if(count($db->rs)){
		$sj=$db->rs;
		foreach ($sj as $k => $v) {
			$yu=$v["gq005"]-$v["gq012"];
			if($arr["gp012"]==$yu){
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$yu,1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");						
				return;
			}else{
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$arr["gp012"],1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1 where gq001=:t0",4,array(array(':t0',':t1'),array($v["gq001"],$arr["gp012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$arr["gp012"].",".time().",0)");
				return;
			}
		}
	}else{
		$db->yiquery("select *  from (select *,(gq005-gq012-".$arr["gp012"].") as a from ymugq where  gq009=0 and gq002!=".$arr["gp002"].") as t where t.a<0 order by t.a asc,t.gq004 asc");
		$sj=$db->rs;
		foreach ($sj as $k => $v) {
			$yu=$v["gq005"]-$v["gq012"];
			if($arr["gp012"]>$yu){
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1 where gp001=:t0",4,array(array(':t0',':t1'),array($arr["gp001"],$yu)));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");				
				$arr["gp012"]=$arr["gp012"]-$yu;	
			}elseif($arr["gp012"]==$yu){
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$yu,1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");					
				return;
			}else{
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$arr["gp012"],1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1 where gq001=:t0",4,array(array(':t0',':t1'),array($v["gq001"],$arr["gp012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$arr["gp012"].",".time().",0)");					
				return;
			}
		}			
	}

}

function fun_bd4($arr,$quid){
	global $db,$YCF,$member,$r;

	$arr["gp012"]=$arr["gp005"]-$arr["gp012"];


	//寻找获取帮助里面剩余配对金额和提供帮助金额接近的会员，还有100提供帮助的配对金额找出比100大于或等于的获取帮助金额，
	$db->yiquery("select * from (select *,(gq005-gq012-".$arr["gp012"].") as a from ymugq where gq002=$quid and gq002!=".$arr["gp002"]." and gq015!='".$arr["gp015"]."' and gq009=0) as t where t.a>=0 ORDER BY t.a asc,RAND()  limit 1");

	if(count($db->rs)){
		$dz=$db->rs;
		foreach ($dz as $k => $v) {
			$yu=$v["gq005"]-$v["gq012"];
			if($arr["gp012"]==$yu){
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$yu,1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");					
				return;
			}else{
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$arr["gp012"],1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1 where gq001=:t0",4,array(array(':t0',':t1'),array($v["gq001"],$arr["gp012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$arr["gp012"].",".time().",0)");					
				return;
			}	
		}
	}else{
		$db->yiquery("select * from (select *,(gq005-gq012-".$arr["gp012"].") as a from ymugq where gq002=$quid and gq002!=".$arr["gp002"]." and gq015!='".$arr["gp015"]."' and gq009=0) as t where t.a<0 ORDER BY t.a desc,RAND()");
		$dz=$db->rs;
		foreach ($dz as $k => $v) {
		
			$yu=$v["gq005"]-$v["gq012"];
			if($arr["gp012"]>$yu){

				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1 where gp001=:t0",4,array(array(':t0',':t1'),array($arr["gp001"],$yu)));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");			

				$arr["gp012"]=$arr["gp012"]-$yu;
			}elseif($arr["gp012"]==$yu){
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$yu,1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");				
				return;
			}else{
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$arr["gp012"],1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1 where gq001=:t0",4,array(array(':t0',':t1'),array($v["gq001"],$arr["gp012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$arr["gp012"].",".time().",0)");				
				return;
			}
		}		
	}


	//时间
	$db->yiquery("select *  from (select *,(gq005-gq012-".$arr["gp012"].") as a from ymugq where gq002=$quid and gq009=0 and gq002!=".$arr["gp002"].") as t where t.a>=0 order by t.a asc,t.gq004 asc limit 1");	
	if(count($db->rs)){
		$sj=$db->rs;
		foreach ($sj as $k => $v) {
			$yu=$v["gq005"]-$v["gq012"];
			if($arr["gp012"]==$yu){
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$yu,1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");						
				return;
			}else{
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$arr["gp012"],1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1 where gq001=:t0",4,array(array(':t0',':t1'),array($v["gq001"],$arr["gp012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$arr["gp012"].",".time().",0)");
				return;
			}
		}
	}else{
		$db->yiquery("select *  from (select *,(gq005-gq012-".$arr["gp012"].") as a from ymugq where gq002=$quid and  gq009=0 and gq002!=".$arr["gp002"].") as t where t.a<0 order by t.a asc,t.gq004 asc");
		$sj=$db->rs;
		foreach ($sj as $k => $v) {
			$yu=$v["gq005"]-$v["gq012"];
			if($arr["gp012"]>$yu){
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1 where gp001=:t0",4,array(array(':t0',':t1'),array($arr["gp001"],$yu)));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");				
				$arr["gp012"]=$arr["gp012"]-$yu;	
			}elseif($arr["gp012"]==$yu){
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$yu,1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gq001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$yu.",".time().",0)");					
				return;
			}else{
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gp001"],$arr["gp012"],1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1 where gq001=:t0",4,array(array(':t0',':t1'),array($v["gq001"],$arr["gp012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$arr["gp001"].",".$v["gq001"].",".$arr["gp012"].",".time().",0)");					
				return;
			}
		}			
	}

}

function fun_bd1($arr){
	global $db,$YCF,$member,$r;


	//寻找获取帮助里面剩余配对金额和提供帮助金额接近的会员，还有100提供帮助的配对金额找出比100大于或等于的提供帮助金额，
	$db->yipre("select * from (select *,(gp005-gp012-:t1) as a from ymugp where  gp002!=".$arr["gq002"]." and  gp011=0 and gp015!=:t0 and gp009=0 ) as t where t.a>=0 ORDER BY t.a asc,RAND()  limit 1",1,array(array(':t0',':t1'),array($arr["gq015"],$arr["gq005"])));

	if(count($db->rs)){
		$dz=$db->rs;
		foreach ($dz as $k => $v) {
			$yu=$v["gp005"]-$v["gp012"];

			if($arr["gq005"]==$yu){

				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gq001"],$yu,1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gp001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$v["gp001"].",".$arr["gq001"].",".$yu.",".time().",0)");					
				return;
			}else{
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gq001"],$arr["gq005"],1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1 where gp001=:t0",4,array(array(':t0',':t1'),array($v["gp001"],$arr["gq005"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$v["gp001"].",".$arr["gq001"].",".$arr["gq005"].",".time().",0)");				
				return;
			}	
		}
	}else{
		$arr["gq012"]=$arr["gq005"];
		$db->yipre("select * from (select *,(gp005-gp012-:t1) as a from ymugp where gp002!=".$arr["gq002"]." and  gp011=0 and gp015!=:t0 and gp009=0) as t where t.a<0 ORDER BY t.a desc,RAND()",1,array(array(':t0',':t1'),array($arr["gq015"],$arr["gq005"])));
		$dz=$db->rs;
		foreach ($dz as $k => $v) {
			$yu=$v["gp005"]-$v["gp012"];
			if($arr["gq012"]>$yu){
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gp001"],$yu,1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1 where gq001=:t0",4,array(array(':t0',':t1'),array($arr["gq001"],$yu)));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$v["gp001"].",".$arr["gq001"].",".$yu.",".time().",0)");						
				$arr["gq012"]=$arr["gq012"]-$yu;	
			}elseif($arr["gq012"]==$yu){
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gq001"],$yu,1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gp001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$v["gp001"].",".$arr["gq001"].",".$yu.",".time().",0)");				
				return;
			}else{
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gq001"],$arr["gq012"],1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1 where gp001=:t0",4,array(array(':t0',':t1'),array($v["gp001"],$arr["gq012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$v["gp001"].",".$arr["gq001"].",".$arr["gq012"].",".time().",0)");				
				return;
			}
		}		
	}


	//时间
	$db->yiquery("select *  from (select *,(gp005-gp012-".$arr["gq012"].") as a from ymugp where  gp002!=".$arr["gq002"]." and  gp011=0 and gp009=0) as t where t.a>=0 order by t.a asc,t.gp004 asc limit 1");
	if(count($db->rs)){
		$sj=$db->rs;
		foreach ($sj as $k => $v) {
			$yu=$v["gp005"]-$v["gp012"];
			if($arr["gq012"]==$yu){
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gq001"],$yu,1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gp001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$v["gp001"].",".$arr["gq001"].",".$yu.",".time().",0)");					
				return;
			}else{
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gq001"],$arr["gq012"],1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1 where gp001=:t0",4,array(array(':t0',':t1'),array($v["gp001"],$arr["gq012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$v["gp001"].",".$arr["gq001"].",".$arr["gq012"].",".time().",0)");						
				return;
			}
		}
	}else{
		$db->yiquery("select *  from (select *,(gp005-gp012-".$arr["gq012"].") as a from ymugp where  gp002!=".$arr["gq002"]." and  gp011=0 and gp009=0) as t where t.a<0 order by t.a asc,t.gp004 asc");
		$sj=$db->rs;
		foreach ($dz as $k => $v) {
			$yu=$v["gp005"]-$v["gp012"];
			if($arr["gq012"]>$yu){
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gp001"],$yu,1,time())));
				$db->yipre("update ymugq set gq012=gq012+:t1 where gq001=:t0",4,array(array(':t0',':t1'),array($arr["gq001"],$yu)));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$v["gp001"].",".$arr["gq001"].",".$yu.",".time().",0)");					
				$arr["gq012"]=$arr["gq012"]-$yu;	
			}elseif($arr["gq012"]==$yu){
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gq001"],$yu,1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1,gp009=:t2,gp010=:t3 where gp001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v["gp001"],$yu,1,time())));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$v["gp001"].",".$arr["gq001"].",".$yu.",".time().",0)");				
				return;
			}else{
				$db->yipre("update ymugq set gq012=gq012+:t1,gq009=:t2,gq010=:t3 where gq001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($arr["gq001"],$arr["gq012"],1,time())));
				$db->yipre("update ymugp set gp012=gp012+:t1 where gp001=:t0",4,array(array(':t0',':t1'),array($v["gp001"],$arr["gq012"])));
				$db->yiexec("insert into ymupd(pd002,pd003,pd004,pd005,pd006) values(".$v["gp001"].",".$arr["gq001"].",".$arr["gq012"].",".time().",0)");				
				return;
			}
		}			
	}

}


function fun_ks(){
	global $db,$YCF;
	$jin=$YCF['jjks']/100;
	$db->yiexec("update ymuv set uv050=uv051+uv052+uv053+uv054+uv055+uv056+uv057+uv058+uv059 where uv008>0 and (uv051!=0 or uv052!=0 or uv053!=0 or uv054!=0 or uv055!=0 or uv056!=0 or uv057!=0 or uv058!=0 or uv059!=0 )");

}


function fun_insertTime(){
	global $db,$YCF;
	$x=date("Y-m-d",time());
	$x=strtotime($xian." 23:59:59");

	$db->yiquery("select * from ymut order by ut001 desc limit 1",2);
	if(empty($db->rs)){
		$db->yiexec("insert into ymut(ut002) values(".$x.")");
	}else{
		if($x>$db->rs['ut002']){
			$db->yiexec("insert into ymut(ut002) values(".$x.")");
			$db->yiexec("update ymuv set uv070=0 where uv070>0");
		}
	}
	$db->yiquery("select * from ymut order by ut001 desc limit 1",2);
	return $db->rs;
}



function fun_totalPrice(){
	global $db,$YCF,$member,$r;


	$db->yiquery("select * from ymut order by ut001 desc limit 1",2);	
	$rt=$db->rs;
	
	$wsql=" where (uv050!=0 or uv051!=0 or uv052!=0 or uv053!=0 or uv054!=0 or uv055!=0 or uv056!=0 or uv057!=0 or uv058!=0 or uv059!=0 or uv060!=0 or uv061!=0 or uv062!=0) and uv008>0";
	$sql="select uv050,uv051,uv052,uv053,uv054,uv055,uv056,uv057,uv058,uv059,uv060,uv061,uv062,uv063,uv001,uv002,uv070,uv071,uv006 from ymuv ".$wsql;
	$db->yiquery($sql);
	$rs=$db->rs;
	foreach ($rs as $k => $v) {

		$uv050=getfnum($v["uv050"]);
		$uv051=getfnum($v["uv051"]);
		$uv052=getfnum($v["uv052"]);
		$uv053=getfnum($v["uv053"]);
		$uv054=getfnum($v["uv054"]);
		$uv055=getfnum($v["uv055"]);
		$uv056=getfnum($v["uv056"]);
		$uv057=getfnum($v["uv057"]);
		$uv058=getfnum($v["uv058"]);
		$uv059=getfnum($v["uv059"]);
		$uv060=getfnum($v["uv060"]);
		$uv061=getfnum($v["uv061"]);
		$uv062=getfnum($v["uv062"]);
		$uv063=getfnum($v["uv063"]);




		if(empty($v["uv070"])){
			$sql="insert into ymup(up050,up051,up052,up053,up054,up055,up056,up057,up058,up059,up060,up061,up062,up063,up002,up003,up004,up005) values(:t1,:t2,:t3,:t4,:t5,:t6,:t7,:t8,:t9,:t10,:t11,:t12,:t13,:t14,:tt1,:tt2,:tt3,:tt4)";
			$db->yipre($sql,5,array(array(':t1',':t2',':t3',':t4',':t5',':t6',':t7',':t8',':t9',':t10',':t11',':t12',':t13',':t14',':tt1',':tt2',':tt3',':tt4'),array($uv050,$uv051,$uv052,$uv053,$uv054,$uv055,$uv056,$uv057,$uv058,$uv059,$uv060,$uv061,$uv062,$uv063,$v['uv001'],time(),$rt['ut001'],$rt['ut002'])));				
			$sql="update ymuv set uv070=:t1 where uv001=:t0";
			$db->yipre($sql,4,array(array(':t0',':t1'),array($v['uv001'],$db->fval)));
		}else{
			$sql="update ymup set up050=up050+:t1,up051=up051+:t2,up052=up052+:t3,up053=up053+:t4,up054=up054+:t5,up055=up055+:t6,up056=up056+:t7,up057=up057+:t8,up058=up058+:t9,up059=up059+:t10,up060=up060+:t11,up061=up061+:t12,up062=up062+:t13,up063=up063+:t14 where up001=:t0";
			$db->yipre($sql,4,array(array(':t0',':t1',':t2',':t3',':t4',':t5',':t6',':t7',':t8',':t9',':t10',':t11',':t12',':t13',':t14'),array($v["uv070"],$uv050,$uv051,$uv052,$uv053,$uv054,$uv055,$uv056,$uv057,$uv058,$uv059,$uv060,$uv061,$uv062,$uv063)));

		}


		// $sql="select uw001 from ymuw where uw001=:t1";
		// $db->yipre($sql,2,array(array(':t1'),array($v['uv002'])));

		if(empty($v["uv071"])){
			$sql="insert into ymuw(uw050,uw051,uw052,uw053,uw054,uw055,uw056,uw057,uw058,uw059,uw060,uw061,uw062,uw063,uw001,uw002) values(:t1,:t2,:t3,:t4,:t5,:t6,:t7,:t8,:t9,:t10,:t11,:t12,:t13,:t14,:tt1,:tt2)";

			$db->yipre($sql,4,array(array(':t1',':t2',':t3',':t4',':t5',':t6',':t7',':t8',':t9',':t10',':t11',':t12',':t13',':t14',':tt1',':tt2'),array($uv050,$uv051,$uv052,$uv053,$uv054,$uv055,$uv056,$uv057,$uv058,$uv059,$uv060,$uv061,$uv062,$uv063,$v['uv002'],time())));	
		
			$sql="update ymuv set uv071=1 where uv001=:t0";
			$db->yipre($sql,4,array(array(':t0'),array($v['uv001'])));				
		}else{
			$sql="update ymuw set uw050=uw050+:t1,uw051=uw051+:t2,uw052=uw052+:t3,uw053=uw053+:t4,uw054=uw054+:t5,uw055=uw055+:t6,uw056=uw056+:t7,uw057=uw057+:t8,uw058=uw058+:t9,uw059=uw059+:t10,uw060=uw060+:t11,uw061=uw061+:t12,uw062=uw062+:t13,uw063=uw063+:t14 where uw001=:t0";
			$db->yipre($sql,4,array(array(':t0',':t1',':t2',':t3',':t4',':t5',':t6',':t7',':t8',':t9',':t10',':t11',':t12',':t13',':t14'),array($v["uv002"],$uv050,$uv051,$uv052,$uv053,$uv054,$uv055,$uv056,$uv057,$uv058,$uv059,$uv060,$uv061,$uv062,$uv063)));
		}

		
		$sql="update ymuv set uv011=uv011+:t1,uv013=uv013+:t2,uv014=uv014+:t3,uv064=uv064+:t4 where uv001=:t0";
		$db->yipre($sql,4,array(array(':t0',':t1',':t2',':t3',':t4'),array($v['uv001'],0,$uv062,$uv063,$uv061)));				
	}
	$wsql=" where (uv050!=0 or uv051!=0 or uv052!=0 or uv053!=0 or uv054!=0 or uv055!=0 or uv056!=0 or uv057!=0 or uv058!=0 or uv059!=0 or uv060!=0 or uv061!=0 or uv062!=0) and uv008>0";
	$db->yiexec("update ymuv set uv050=0,uv051=0,uv052=0,uv053=0,uv054=0,uv055=0,uv056=0,uv057=0,uv058=0,uv059=0,uv060=0,uv061=0,uv062=0,uv063=0 ".$wsql);
}


function fun_sj($uv001,$uv002){
	global $db,$YCF,$member,$r;
	if(empty($uv001)) return;
	$db->yiquery("select uv006,uv001,uv002,uv069 as uv016 from ymuv where uv001=$uv001",2);
	$rs=$db->rs;
	switch ($rs["uv006"]) {
		case 0:
			if($rs["uv016"]>=$YCF["jjsjt"][1]){
				$db->yiexec("update ymuv set uv006=1 where uv001=".$rs["uv001"]);

				yimao_writeaccount(array($rs["uv001"],"0",6,time(),9,0,'推荐'.$uv002.'达到条件升级到'.$YCF["jjname"][1]));
			}
			break;
		case 1:
			if($rs["uv016"]>=$YCF["jjsjt"][2]){
				$db->yiexec("update ymuv set uv006=2 where uv001=".$rs["uv001"]);
				yimao_writeaccount(array($rs["uv001"],"0",6,time(),9,0,'推荐'.$uv002.'达到条件升级到'.$YCF["jjname"][2]));
			}
			break;
		case 2:
			// if($rs["uv016"]>=$YCF["jjsjt"][3]){
			// 	$db->yiexec("update ymuv set uv006=3 where uv001=".$rs["uv001"]);
			// 	yimao_writeaccount(array($rs["uv001"],"0",6,time(),9,0,'推荐'.$uv002.'达到条件升级到'.$YCF["jjname"][3]));
			// }
			// break;
		case 3:

			break;						
	}


}



function fun_jjxx($arr){
	global $db,$YCF,$member,$r;
	$sql="insert into ymud(ud002,ud003,ud004,ud005,ud006,ud007) values(:t1,:t2,:t3,:t4,:t5,:t6)";

	$db->yipre($sql,4,array(array(':t1',':t2',':t3',':t4',':t5',':t6'),array($arr[0],$arr[1],$arr[2],$arr[3],$arr[4],$arr[5])));	
}

function fun_jjxx1($arr){
	global $db,$YCF,$member,$r;
	$sql="insert into ymud(ud002,ud003,ud004,ud005,ud006,ud007,ud008,ud009) values(:t1,:t2,:t3,:t4,:t5,:t6,:t7,:t8)";

	$db->yipre($sql,4,array(array(':t1',':t2',':t3',':t4',':t5',':t6',':t7',':t8'),array($arr[0],$arr[1],$arr[2],$arr[3],$arr[4],$arr[5],$arr[6],$arr[7])));	
}

function fun_jjxx2($arr){
	global $db,$YCF,$member,$r;
	$sql="insert into ymud(ud002,ud003,ud004,ud005,ud006,ud007,ud008,ud009,ud010) values(:t1,:t2,:t3,:t4,:t5,:t6,:t7,:t8,:t9)";

	$db->yipre($sql,4,array(array(':t1',':t2',':t3',':t4',':t5',':t6',':t7',':t8',':t9'),array($arr[0],$arr[1],$arr[2],$arr[3],$arr[4],$arr[5],$arr[6],$arr[7],$arr[8])));	
}

function fun_rfh(){
	global $db,$YCF,$member,$r;
	if(is_array($YCF['jjfhz'])){
		if(in_array(date('w'),$YCF['jjfhz'])==false)
			return;
	}else{

		if(strpos($YCF['jjfhz'],date('w'))===false)
			return;
	}
	
	try{
		$db->begintransaction();
		$rt=fun_insertTime();
		if($rt['ut003']==1){
			$db->committransaction();
			return '';
		}


		$sql="select gp006,gp005,gp001,gp013,gp020,gp002,gp001 from ymugp where gp009<=1 and  FROM_UNIXTIME(gp004,'%Y-%m-%d')<'".date("Y-m-d",time())."' and gp011=0 and gp018=0  and  gp006=0 and  gp020<".$YCF["jjlxt"][0];

		$db->yiquery($sql);
		$rs=$db->rs;
		foreach ($rs as $k => $v) {
			$rsm=$member->getuv1($v["gp002"],"uv010");
			if($rsm["uv010"]==1) continue;
						
			$qian=$YCF['jjlx'][$v['gp006']]/100*$v["gp005"];
			$jk=$qian*$YCF["jjxjk"][$v["gp006"]]/100;
			$jin=$qian-$jk;
			
			$ci=$v['gp020']+1;

			if($ci==$YCF["jjlxt"][0]) $db->yipre("update ymugp set gp018=1 where gp001=:t0",4,array(array(':t0'),array($v['gp001'])));

			$db->yipre("update ymugp set gp020=gp020+1,gp019=gp019+:t1,gp021=gp021+:t2 where gp001=:t0",4,array(array(':t0',':t1',':t2'),array($v['gp001'],$jin,$jk)));

			if($jin>0){

				$sql="update ymuv set uv051=uv051+:t1,uv041=uv041+:t1,uv042=uv042+1,uv061=uv061+:t2,uv062=uv062+:t3 where uv001=:t0";
				$db->yipre($sql,4,array(array(':t0',':t1',':t2',':t3'),array($v['gp002'],$qian,$jk,$jin)));
				fun_jjxx1(array($v['gp002'],$rt['ut001'],time(),51,$qian,'提供帮助编号'.$v["gp013"].'第'.$ci.'次',$v["gp001"],$jk));	
				fun_tuiguang(array($v['gp002'],$v['gp001'],$rt["ut001"]));	
			}
		}


		$sql="select gp006,gp005,gp001,gp013,gp020,gp002,gp001 from ymugp where gp009<=1 and  FROM_UNIXTIME(gp004,'%Y-%m-%d')<'".date("Y-m-d",time())."' and gp011=0 and gp018=0  and  gp006=1 and  gp020<".$YCF["jjlxt"][1];

		$db->yiquery($sql);
		$rs=$db->rs;
		foreach ($rs as $k => $v) {
			$rsm=$member->getuv1($v["gp002"],"uv010");
			if($rsm["uv010"]==1) continue;

			$qian=$YCF['jjlx'][$v['gp006']]/100*$v["gp005"];
			$jk=$qian*$YCF["jjxjk"][$v["gp006"]]/100;
			$jin=$qian-$jk;
			
			$ci=$v['gp020']+1;

			if($ci==$YCF["jjlxt"][1]) $db->yipre("update ymugp set gp018=1 where gp001=:t0",4,array(array(':t0'),array($v['gp001'])));

			$db->yipre("update ymugp set gp020=gp020+1,gp019=gp019+:t1,gp021=gp021+:t2 where gp001=:t0",4,array(array(':t0',':t1',':t2'),array($v['gp001'],$jin,$jk)));

			if($jin>0){

				$sql="update ymuv set uv051=uv051+:t1,uv041=uv041+:t1,uv042=uv042+1,uv061=uv061+:t2,uv062=uv062+:t3 where uv001=:t0";
				$db->yipre($sql,4,array(array(':t0',':t1',':t2',':t3'),array($v['gp002'],$qian,$jk,$jin)));
				fun_jjxx1(array($v['gp002'],$rt['ut001'],time(),51,$qian,'提供帮助编号'.$v["gp013"].'第'.$ci.'次',$v["gp001"],$jk));	
				fun_tuiguang(array($v['gp002'],$v['gp001'],$rt["ut001"]));	
			}
		}		


		$sql="select gp006,gp005,gp001,gp013,gp020,gp002,gp001 from ymugp where gp009<=1 and  FROM_UNIXTIME(gp004,'%Y-%m-%d')<'".date("Y-m-d",time())."' and gp011=0 and gp018=0  and  gp006=2 and  gp020<".$YCF["jjlxt"][2];

		$db->yiquery($sql);
		$rs=$db->rs;
		foreach ($rs as $k => $v) {
			$rsm=$member->getuv1($v["gp002"],"uv010");
			if($rsm["uv010"]==1) continue;

			$qian=$YCF['jjlx'][$v['gp006']]/100*$v["gp005"];
			$jk=$qian*$YCF["jjxjk"][$v["gp006"]]/100;
			$jin=$qian-$jk;
			
			$ci=$v['gp020']+1;

			if($ci==$YCF["jjlxt"][2]) $db->yipre("update ymugp set gp018=1 where gp001=:t0",4,array(array(':t0'),array($v['gp001'])));

			$db->yipre("update ymugp set gp020=gp020+1,gp019=gp019+:t1,gp021=gp021+:t2 where gp001=:t0",4,array(array(':t0',':t1',':t2'),array($v['gp001'],$jin,$jk)));

			if($jin>0){

				$sql="update ymuv set uv051=uv051+:t1,uv041=uv041+:t1,uv042=uv042+1,uv061=uv061+:t2,uv062=uv062+:t3 where uv001=:t0";
				$db->yipre($sql,4,array(array(':t0',':t1',':t2',':t3'),array($v['gp002'],$qian,$jk,$jin)));
				fun_jjxx1(array($v['gp002'],$rt['ut001'],time(),51,$qian,'提供帮助编号'.$v["gp013"].'第'.$ci.'次',$v["gp001"],$jk));	
				fun_tuiguang(array($v['gp002'],$v['gp001'],$rt["ut001"]));	
			}
		}	


		$sql="select gp006,gp005,gp001,gp013,gp020,gp002,gp001 from ymugp where gp009<=1 and  FROM_UNIXTIME(gp004,'%Y-%m-%d')<'".date("Y-m-d",time())."' and gp011=0 and gp018=0  and  gp006=3 and  gp020<".$YCF["jjlxt"][3];

		$db->yiquery($sql);
		$rs=$db->rs;
		foreach ($rs as $k => $v) {
			$rsm=$member->getuv1($v["gp002"],"uv010");
			if($rsm["uv010"]==1) continue;

			$qian=$YCF['jjlx'][$v['gp006']]/100*$v["gp005"];
			$jk=$qian*$YCF["jjxjk"][$v["gp006"]]/100;
			$jin=$qian-$jk;
			
			$ci=$v['gp020']+1;

			if($ci==$YCF["jjlxt"][3]) $db->yipre("update ymugp set gp018=1 where gp001=:t0",4,array(array(':t0'),array($v['gp001'])));

			$db->yipre("update ymugp set gp020=gp020+1,gp019=gp019+:t1,gp021=gp021+:t2 where gp001=:t0",4,array(array(':t0',':t1',':t2'),array($v['gp001'],$jin,$jk)));

			if($jin>0){

				$sql="update ymuv set uv051=uv051+:t1,uv041=uv041+:t1,uv042=uv042+1,uv061=uv061+:t2,uv062=uv062+:t3 where uv001=:t0";
				$db->yipre($sql,4,array(array(':t0',':t1',':t2',':t3'),array($v['gp002'],$qian,$jk,$jin)));
				fun_jjxx1(array($v['gp002'],$rt['ut001'],time(),51,$qian,'提供帮助编号'.$v["gp013"].'第'.$ci.'次',$v["gp001"],$jk));
				fun_tuiguang(array($v['gp002'],$v['gp001'],$rt["ut001"]));	
			}
		}			
		fun_ks();
		fun_totalPrice();
		$db->yiexec('update ymut set ut003=1 where ut001='.$rt['ut001']);
		$db->committransaction();
		fun_autoshouyi();
	}catch(PDOException $e){
		$db->rollbacktransaction();
		a_bck("error");
	}

}

function fun_jdjshouyi($gp001){
	global $db,$YCF,$member,$r;

	$sql="select * from ymugp where gp009=1 and gp001=".$gp001;
	$db->yiquery($sql);
	$rs=$db->rs;
	foreach ($rs as $k => $v) {

      $db->yiquery("select ud002,sum(ud006) ud006,sum(ud009) ud009 from ymud where ud010=0 and ud005!=51 and ud005!=54 and ud008=".$v["gp001"]." group by ud002");
      $all=$db->rs;
	   $db->yipre("update ymud set ud010=1 where ud010=0 and ud005!=51 and ud005!=54 and ud008=:t0",4,array(array(':t0'),array($v["gp001"])));      
      foreach ($all as $kk => $vv) {
	
          $us=$member->getuserinfo($vv["ud002"]);
          $jin=($vv["ud006"]-$vv["ud009"]);
          if($jin>0){
          	$db->yipre("update ymuv set uv011=uv011+:t1 where uv001=:t0",4,array(array(':t0',':t1'),array($vv["ud002"],$jin)));	
	      yimao_writeaccount(array($vv["ud002"],"1",2,time(),19,$jin,'转入现金钱包'));
	      yimao_writeaccount(array($vv["ud002"],"0",0,time(),19,$jin,'佣金钱包转入'));  
	      yimao_writeaccount(array($vv["ud002"],"1",5,time(),16,$vv["ud009"],'转入小金库'));  
	      yimao_writeaccount(array($vv["ud002"],"0",4,time(),16,$vv["ud009"],'冻结小金库转入'));  
	  	  }
      }      
	}

}

function fun_autoshouyi(){
	global $db,$YCF,$member,$r;

	$sql="select * from ymugp where gp018=1 and gp007=0";
	$db->yiquery($sql);
	$rs=$db->rs;
	foreach ($rs as $k => $v) {
		$db->yiexec("update ymugp set gp007=1 where gp001=".$v["gp001"]);

      $jin=$v["gp019"]+$v["gp005"];
      $jk=$v["gp021"];

      if($jin>0){
		

	 $db->yipre("update ymuv set uv011=uv011+:t1 where uv001=:t0",4,array(array(':t0',':t1'),array($v["gp002"],$jin)));	

      yimao_writeaccount(array($v["gp002"],"1",1,time(),16,($jin),'转入现金钱包'));
      yimao_writeaccount(array($v["gp002"],"0",0,time(),16,($jin),'收益钱包转入'));	
      }

          if($jk>0){

            yimao_writeaccount(array($v["gp002"],"1",5,time(),16,$jk,'转入小金库'));
            yimao_writeaccount(array($v["gp002"],"0",4,time(),16,$jk,'冻结小金库转入'));
          }

      $db->yiquery("select ud002,sum(ud006) ud006,sum(ud009) ud009 from ymud where ud010=0 and  ud005=54 and ud008=".$v["gp001"]." group by ud002");
      $all=$db->rs;
		  $db->yipre("update ymud set ud010=1 where ud010=0 and ud005=54 and ud008=:t0",4,array(array(':t0'),array($v["gp001"])));	 
      foreach ($all as $kk => $vv) {
      	
          $us=$member->getuserinfo($vv["ud002"]);
          $jin=($vv["ud006"]-$vv["ud009"]);
         if($jin>0){
    	 $db->yipre("update ymuv set uv011=uv011+:t1 where uv001=:t0",4,array(array(':t0',':t1'),array($v["gp002"],$jin)));	     	
	      yimao_writeaccount(array($vv["ud002"],"1",2,time(),16,$jin,'转入现金钱包'));
	      yimao_writeaccount(array($vv["ud002"],"0",0,time(),16,$jin,'佣金钱包转入'));  
	      yimao_writeaccount(array($vv["ud002"],"1",5,time(),16,$vv["ud009"],'转入小金库'));  
	      yimao_writeaccount(array($vv["ud002"],"0",4,time(),16,$vv["ud009"],'冻结小金库转入'));  
         }

	     //  if($vv["ud009"]>0){
	     //  	yimao_writeaccount(array($v["gp002"],"0",3,time(),17,$vv["ud009"],'其它奖小金库'));
	     //  	$db->yiexec("update ymuv set uv048=uv048-".$vv["ud009"]." where uv001=".$vv["ud002"]);
	      	
	  	  // }

      }          
	}
}

function fun_tjj($arr){
	global $db,$YCF,$member,$r;

		$qian=$arr[1];	

		$sql="select uv001,uv002,uv006,uv020 from ymuv where uv001 in(0".$arr[0]."0) order by uv001 desc limit 20";
		$db->yiquery($sql);
		$rss=$db->rs;
		foreach ($rss as $k => $v) {		
			if($v["uv006"]==0){
				$lv=$YCF["jjtjj"][$k];
				$jin=$qian*$YCF["jjtjj"][$k]/100;
			}elseif($v["uv006"]==1){
				$lv=$YCF["jjtjj2"][$k];
				$jin=$qian*$YCF["jjtjj2"][$k]/100;
			}elseif($v["uv006"]==2){
				$lv=$YCF["jjtjj3"][$k];
				$jin=$qian*$YCF["jjtjj3"][$k]/100;
			}elseif($v["uv006"]==3){
				$lv=$YCF["jjtjj4"][$k];
				$jin=$qian*$YCF["jjtjj4"][$k]/100;
			}		
			
			$jk=floor($jin*$YCF["jjxjk"][$v["uv006"]]/100*100)/100;
			$jin1=$jin-$jk;
			if($jin>0){
				$sql="update ymuv set uv052=uv052+:t1,uv061=uv061+:t2,uv063=uv063+:t3 where uv001=:t0";
				$db->yipre($sql,4,array(array(':t0',':t1',':t2',':t3'),array($v['uv001'],$jin,$jk,$jin1)));
				fun_jjxx1(array($v['uv001'],$arr[3],time(),52,$jin,'第'.($k+1).'代'.$arr[2].'存款'.$qian.'的'.$lv.'%',$arr[4],$jk));		
			}
		}

}

function fun_jdj($arr){
	// global $db,$YCF,$member,$r;
	// $maxceng=max($YCF["jjjdjd"]);
	// if($maxceng<=0) return;

	// $sql="select uv001,uv002,uv006 from ymuv where uv001 in(0".$arr[0]."0) order by uv001 desc limit ".($maxceng);
	// $db->yiquery($sql);
	// $rss=$db->rs;
	// foreach ($rss as $k => $v) {
	// 	$dai=$YCF["jjjdjd"][$v["uv006"]];
	// 	if(($k+1)>$dai) continue;

	// 		if($v["uv006"]==0){
	// 			$jin=$qian*$YCF["jjjdj1"][$k]/100;
	// 		}elseif($v["uv006"]==1){
	// 			$jin=$qian*$YCF["jjjdj2"][$k]/100;
	// 		}elseif($v["uv006"]==2){
	// 			$jin=$qian*$YCF["jjjdj3"][$k]/100;
	// 		}elseif($v["uv006"]==3){
	// 			$jin=$qian*$YCF["jjjdj4"][$k]/100;
	// 		}	
			

	// 	$jk=0;
	// 	$jin1=$jin-$jk;

	// 	if($jin>0){
	// 		$db->yipre("update ymuv set uv054=uv054+:t1,uv062=uv062+:t2 where uv001=:t0",4,array(array(':t0',':t1',':t2',':t3'),array($v['uv001'],$jin,$jin1,$jk)));

	// 		fun_jjxx2(array($v['uv001'],$arr[3],time(),54,$jin,'第'.($k+1).'代'.$arr[2].'存款'.$arr[1],$arr[4],$jk,0));
	// 	}
	// }
}

function fun_tuiguang($arr){
	global $db,$YCF,$member,$r;
	$maxceng=max($YCF["jjjdjd"]);

	// if($maxceng<=0) return;

	$sql="select uv001,uv002,uv006,uv020,uv051 from ymuv where uv051>0 and uv001=".$arr[0];
	$db->yiquery($sql);
	$rs=$db->rs;
	foreach ($rs as $kk => $vv) {
		$qian=$vv["uv051"];
		$sql="select uv001,uv002,uv006,uv020 from ymuv where uv001 in(0".$vv["uv020"]."0) order by uv001 desc limit 5";
		$db->yiquery($sql);
		$rss=$db->rs;
		foreach ($rss as $k => $v) {		
	

				$jin=0;
				$lv=0;
				if($v["uv006"]==0){
					$lv=$YCF["jjjdj1"][$k];
					$jin=$qian*$YCF["jjjdj1"][$k]/100;
				}elseif($v["uv006"]==1){
					$lv=$YCF["jjjdj2"][$k];
					$jin=$qian*$YCF["jjjdj2"][$k]/100;
				}elseif($v["uv006"]==2){
					$lv=$YCF["jjjdj3"][$k];
					$jin=$qian*$YCF["jjjdj3"][$k]/100;
				}elseif($v["uv006"]==3){
					$lv=$YCF["jjjdj4"][$k];
					$jin=$qian*$YCF["jjjdj4"][$k]/100;
				}	
				

			$jk=floor($jin*$YCF["jjxjk"][$v["uv006"]]/100*100)/100;
			$jin1=$jin-$jk;

			if($jin>0){

				$sql="update ymuv set uv054=uv054+:t1,uv061=uv061+:t2,uv063=uv063+:t3 where uv001=:t0";
				$db->yipre($sql,4,array(array(':t0',':t1',':t2',':t3'),array($v['uv001'],$jin,$jk,$jin1)));

				fun_jjxx1(array($v['uv001'],$arr[2],time(),54,$jin,'第'.($k+1).'代'.$vv["uv002"].'利息'.$qian.'的'.$lv.'%',$arr[1],$jk));		
			}
		}
	}
		
}


function fun_rfh1(){
	global $db,$YCF,$member,$r;

	try{
		$db->begintransaction();
		$rt=fun_insertTime();

		$sql="select gp006,gp005,gp001,gp013,gp020,gp002,gp001 from ymugp where gp009<=1 and gp011=0 and gp018=0  and  gp006=0 and  gp020<".$YCF["jjlxt"][0];

		$db->yiquery($sql);
		$rs=$db->rs;
		foreach ($rs as $k => $v) {
			$rsm=$member->getuv1($v["gp002"],"uv010");
			if($rsm["uv010"]==1) continue;

			$qian=$YCF['jjlx'][$v['gp006']]/100*$v["gp005"];
			$jk=$qian*$YCF["jjxjk"][$v["gp006"]]/100;
			$jin=$qian-$jk;
			
			$ci=$v['gp020']+1;

			if($ci==$YCF["jjlxt"][0]) $db->yipre("update ymugp set gp018=1 where gp001=:t0",4,array(array(':t0'),array($v['gp001'])));

			$db->yipre("update ymugp set gp020=gp020+1,gp019=gp019+:t1,gp021=gp021+:t2 where gp001=:t0",4,array(array(':t0',':t1',':t2'),array($v['gp001'],$jin,$jk)));

			if($jin>0){

				$sql="update ymuv set uv051=uv051+:t1,uv041=uv041+:t1,uv042=uv042+1,uv061=uv061+:t2,uv062=uv062+:t3 where uv001=:t0";
				$db->yipre($sql,4,array(array(':t0',':t1',':t2',':t3'),array($v['gp002'],$qian,$jk,$jin)));
				fun_jjxx1(array($v['gp002'],$rt['ut001'],time(),51,$qian,'提供帮助编号'.$v["gp013"].'第'.$ci.'次',$v["gp001"],$jk));		
				fun_tuiguang(array($v['gp002'],$v['gp001'],$rt["ut001"]));	
			}
		}

		$sql="select gp006,gp005,gp001,gp013,gp020,gp002,gp001 from ymugp where gp009<=1 and  gp011=0 and gp018=0  and  gp006=1 and  gp020<".$YCF["jjlxt"][1];

		$db->yiquery($sql);
		$rs=$db->rs;
		foreach ($rs as $k => $v) {
			$rsm=$member->getuv1($v["gp002"],"uv010");
			if($rsm["uv010"]==1) continue;			
			$qian=$YCF['jjlx'][$v['gp006']]/100*$v["gp005"];
			$jk=$qian*$YCF["jjxjk"][$v["gp006"]]/100;
			$jin=$qian-$jk;
			
			$ci=$v['gp020']+1;

			if($ci==$YCF["jjlxt"][1]) $db->yipre("update ymugp set gp018=1 where gp001=:t0",4,array(array(':t0'),array($v['gp001'])));

			$db->yipre("update ymugp set gp020=gp020+1,gp019=gp019+:t1,gp021=gp021+:t2 where gp001=:t0",4,array(array(':t0',':t1',':t2'),array($v['gp001'],$jin,$jk)));

			if($jin>0){

				$sql="update ymuv set uv051=uv051+:t1,uv041=uv041+:t1,uv042=uv042+1,uv061=uv061+:t2,uv062=uv062+:t3 where uv001=:t0";
				$db->yipre($sql,4,array(array(':t0',':t1',':t2',':t3'),array($v['gp002'],$qian,$jk,$jin)));
				fun_jjxx1(array($v['gp002'],$rt['ut001'],time(),51,$qian,'提供帮助编号'.$v["gp013"].'第'.$ci.'次',$v["gp001"],$jk));
				fun_tuiguang(array($v['gp002'],$v['gp001'],$rt["ut001"]));		
			}
		}	

		$sql="select gp006,gp005,gp001,gp013,gp020,gp002,gp001 from ymugp where gp009<=1 and  gp011=0 and gp018=0  and  gp006=2 and  gp020<".$YCF["jjlxt"][2];

		$db->yiquery($sql);
		$rs=$db->rs;
		foreach ($rs as $k => $v) {
			$rsm=$member->getuv1($v["gp002"],"uv010");
			if($rsm["uv010"]==1) continue;

			$qian=$YCF['jjlx'][$v['gp006']]/100*$v["gp005"];
			$jk=$qian*$YCF["jjxjk"][$v["gp006"]]/100;
			$jin=$qian-$jk;
			
			$ci=$v['gp020']+1;

			if($ci==$YCF["jjlxt"][2]) $db->yipre("update ymugp set gp018=1 where gp001=:t0",4,array(array(':t0'),array($v['gp001'])));


			$db->yipre("update ymugp set gp020=gp020+1,gp019=gp019+:t1,gp021=gp021+:t2 where gp001=:t0",4,array(array(':t0',':t1',':t2'),array($v['gp001'],$jin,$jk)));

			if($jin>0){

				$sql="update ymuv set uv051=uv051+:t1,uv041=uv041+:t1,uv042=uv042+1,uv061=uv061+:t2,uv062=uv062+:t3 where uv001=:t0";
				$db->yipre($sql,4,array(array(':t0',':t1',':t2',':t3'),array($v['gp002'],$qian,$jk,$jin)));
				fun_jjxx1(array($v['gp002'],$rt['ut001'],time(),51,$qian,'提供帮助编号'.$v["gp013"].'第'.$ci.'次',$v["gp001"],$jk));
				fun_tuiguang(array($v['gp002'],$v['gp001'],$rt["ut001"]));		
			}
		}	

		$sql="select gp006,gp005,gp001,gp013,gp020,gp002,gp001 from ymugp where gp009<=1 and  gp011=0 and gp018=0 and gp006=3 and  gp020<".$YCF["jjlxt"][3];

		$db->yiquery($sql);
		$rs=$db->rs;
		foreach ($rs as $k => $v) {
			$rsm=$member->getuv1($v["gp002"],"uv010");
			if($rsm["uv010"]==1) continue;

			$qian=$YCF['jjlx'][$v['gp006']]/100*$v["gp005"];
			$jk=$qian*$YCF["jjxjk"][$v["gp006"]]/100;
			$jin=$qian-$jk;
			
			$ci=$v['gp020']+1;

			if($ci==$YCF["jjlxt"][3]) $db->yipre("update ymugp set gp018=1 where gp001=:t0",4,array(array(':t0'),array($v['gp001'])));

			$db->yipre("update ymugp set gp020=gp020+1,gp019=gp019+:t1,gp021=gp021+:t2 where gp001=:t0",4,array(array(':t0',':t1',':t2'),array($v['gp001'],$jin,$jk)));

			if($jin>0){

				$sql="update ymuv set uv051=uv051+:t1,uv041=uv041+:t1,uv042=uv042+1,uv061=uv061+:t2,uv062=uv062+:t3 where uv001=:t0";
				$db->yipre($sql,4,array(array(':t0',':t1',':t2',':t3'),array($v['gp002'],$qian,$jk,$jin)));
				fun_jjxx1(array($v['gp002'],$rt['ut001'],time(),51,$qian,'提供帮助编号'.$v["gp013"].'第'.$ci.'次',$v["gp001"],$jk));

				fun_tuiguang(array($v['gp002'],$v['gp001'],$rt["ut001"]));		
			}
		}						

		
		fun_ks();
		fun_totalPrice();
		
		//$db->yiexec('update ymut set ut003=1 where ut001='.$rt['ut001']);
		$db->committransaction();
		fun_autoshouyi();
	}catch(PDOException $e){
		$db->rollbacktransaction();
		a_bck("error");
	}

}


function fun_rfh2($usid,$rt){
	global $db,$YCF,$member,$r;



		$sql="select gp006,gp005,gp001,gp013,gp020,gp002,gp001 from ymugp where gp009<=1 and gp011=0 and gp018=0  and gp001=$usid";

		$db->yiquery($sql);
		$rs=$db->rs;
		foreach ($rs as $k => $v) {
			$rsm=$member->getuv1($v["gp002"],"uv010");
			if($rsm["uv010"]==1) continue;

			$qian=$YCF['jjlx'][$v['gp006']]/100*$v["gp005"];
			$jk=$qian*$YCF["jjxjk"][$v["gp006"]]/100;
			$jin=$qian-$jk;
			
			$ci=$v['gp020']+1;

			if($ci==$YCF["jjlxt"][0]) $db->yipre("update ymugp set gp018=1 where gp001=:t0",4,array(array(':t0'),array($v['gp001'])));

			$db->yipre("update ymugp set gp020=gp020+1,gp019=gp019+:t1,gp021=gp021+:t2 where gp001=:t0",4,array(array(':t0',':t1',':t2'),array($v['gp001'],$jin,$jk)));

			if($jin>0){

				$sql="update ymuv set uv051=uv051+:t1,uv041=uv041+:t1,uv042=uv042+1,uv061=uv061+:t2,uv062=uv062+:t3 where uv001=:t0";
				$db->yipre($sql,4,array(array(':t0',':t1',':t2',':t3'),array($v['gp002'],$qian,$jk,$jin)));
				fun_jjxx1(array($v['gp002'],$rt['ut001'],time(),51,$qian,'提供帮助编号'.$v["gp013"].'第'.$ci.'次',$v["gp001"],$jk));
	
				fun_tuiguang(array($v['gp002'],$v['gp001'],$rt["ut001"]));	
			}
		}

	

}
?>