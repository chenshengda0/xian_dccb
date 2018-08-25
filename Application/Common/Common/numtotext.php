<?php
function get_user_status($state){
	$text = "" ;
	switch ($state){
		case -1: $text = "冻结" ; break ;
		case 0: $text = "未激活" ; break ;
		case 1: $text = "正常" ; break ;
	}
	return $text ;
}
function get_daili($state){
    $text = "" ;
    switch ($state){
        case 0: $text = "已拒绝" ; break ;
        case 1: $text = "申请中" ; break ;
        case 2: $text = "已通过" ; break ;
    }
    return $text ;
}
function get_vip($state){
  	$time = $state-time();
  	$danwei = 24*3600;
  	$cha = floor($time/$danwei);
  	if($cha<=0){
    	$cha=0;
    }
    $text = $cha.'天';
    return $text ;
}
//城市
function getCity($province,$city,$district){
	//$pro=M('district')->field('name')->where(array('id'=>array('in',array($province,$city,$district))))->select();

	$pro=M('district')->select("$province,$city,$district");

	if(!empty($pro)){
		return $pro[0]['name'].'-'.$pro[1]['name'].'-'.$pro[2]['name'];
	}
	return '无';
}
/**
 * 会员性别
 * @param unknown $state
 * @return string
 */
function getSex($state){
	$text = "" ;
	switch ($state){
		case 0: $text = "男" ; break ;
		case 1: $text = "女" ; break ;
	}
	return $text ;
}
function get_live($state){
    $text = 720-$state;
    return $text ;
}
function getmoney($money){
    $text =sprintf("%.4f",$money);
   
    return $text ;
}
function getshenpi($state){
	$text = "" ;
	if($state==3){
      $text = "已激活" ;
    }else{
      $text = "未激活" ; 
    }
	return $text ;
}
function get_hour($state){
	
	$hour =  $state/3600;
	return $hour ;
}


function zhuangtai($state){
    $text = "" ;
    switch ($state){
        case 4: $text = "卖家撤销" ; break ;
        case 5: $text = "正常完成" ; break ;
        case 6: $text = "冻结" ; break ;
    }
    return $text ;
}
function  get_mmm($res){
    $text = "" ;
	switch ($res){
		case 1: $text = "买入" ; break ;
		case 2: $text = "卖出" ; break ;
	}
	return $text ;
}
function  get_shenpi($res){
    $text = "" ;
    switch ($res){
        case 4: $text = "拒批未通过" ; break ;
        case 3: $text = "审批通过" ; break ;
    }
    return $text ;
}
function  get_jystatus($res){
    $text = "" ;
    switch ($res){
        case 2: $text = "等待付款" ; break ;
        case 3: $text = "已经付款" ; break ;
        case 4: $text = "已经收款" ; break ;
         
    }
    return $text ;
}
function  set_sstatus($res){
    $text = "" ;
    switch ($res){
        case 1: $text = "正在运行" ; break ;
        case 2: $text = "已停止" ; break ;
        case 3: $text = "已停止运行" ; break ;
       
    }
    return $text ;
}
function  get_sstatus($res){
    $text = "" ;
    switch ($res){
        case 1: $text = "等待中" ; break ;
        case 2: $text = "已匹配" ; break ;
        case 3: $text = "已付款" ; break ;
        case 4: $text = "已撤销" ; break ;
        case 5: $text = "已完成" ; break ;
        case 6: $text = "已冻结" ; break ;
        case 7: $text = "买家取消" ; break ;
    }
    return $text ;
}
function  get_mm($res){
    $text = "" ;
    switch ($res){
        case 1: $text = "买入" ; break ;
        case 2: $text = "卖TA" ; break ;
       
    }
    return $text ;
}
function get_price($state){
    $text = "" ;
    $jiangjin = get_bonus_rule('youhui_jjb')*$state*0.01;
    $jifen = get_bonus_rule('youhui_jifen')*$state*0.01;
    $text = $text.'奖金币'.$jiangjin.';积分'.$jifen;
    return $text ;
}
/**
 * 奖金变更类型
 * @param unknown $state
 * @return string
 */
function getChangeType($state){
	$text = "" ;
	$change_type = C('CHANGETYPE');
	$text = $change_type[$state];
	return $text ;
}

/**
 * 变更状态
 * @param unknown $state
 * @return string
 */
function getChange($state){
	$text = "" ;
	switch ($state){
		case 0: $text = "↓↓" ; break ;
		case 1: $text = "<span style='color:red;'>↑↑</span>" ; break ;
        //case 2: $text = "↓↓" ; break ;
	}
	return $text ;
}
/**
 * 钱币类型
 * @param unknown $state
 * @return string
 */
function getMoneyType($state){
	$text = "" ;
	$money_type = C('MONEYTYPE');
	$text = $money_type['title'][$state];
	$color = $money_type['color'][$state];
	$str = "<span style='color:%s;'>%s</span>";
	$text = sprintf($str,$color,$text);
			
	return $text ;
}
function getMoneyType1($state){
	$text = "" ;
	$money_type = C('MONEYTYPE');
	$text = $money_type['title'][$state];
		
	return $text ;
}

/**
 * 用户级别
 * @param unknown $id
 * @return unknown
 */
function get_userrank($state){
  
	$text = "" ;
	$name = C('URANK_NAME');
  
	$color = M('RankColor')->where(array('rank'=>$state,'types'=>0))->getField('fontcolor');
	$text = "<span style='color:{$color};'>{$name[$state]}</span>";
  
	return $text ;
}
function get_bill($state){
    $text = "" ;
    $name = C('URANK_NAME');
    $color = M('RankColor')->where(array('rank'=>$state,'types'=>0))->getField('fontcolor');
    $text = "<span style='color:{$color};'>{$name[$state]}</span>";
    return $text ;
}
function get_rank($str){
	$arr = array(
		'铜牌股东'=>1,
		'银牌股东'=>2,
		'金牌股东'=>3,
		'钻石股东'=>4,
		'翡翠股东'=>5,
		'优惠会员'=>6,
	) ;
	return $arr[$str] ;
}
/**
 * 用户职称
 * @param unknown $id
 * @return unknown
 */
function get_regtype($state,$color=''){
	$text = "" ;
	$name = C('REGTYPE_NAME');
	$color = M('RankColor')->where(array('rank'=>$state,'types'=>1))->getField('fontcolor');
	$text = "<span style='color:{$color};'>{$name[$state]}</span>";
	return $text ;
}

/**
 * 报单中心
 */
function get_billcenter($state){
	$text = "" ;
	switch ($state){
		case 0: $text = "否" ; break ;
		case 1: $text = "市级报单中心" ; break ;
		case 2: $text = "县级报单中心" ; break ;
	}
	return $text ;
}

/**
 * 状态
 * @param unknown $id
 * @return unknown
 */
function get_status($state){
	$text = "" ;
	switch ($state){
		case 0: $text = "不是报单中心" ; break ;
		case 1: $text = "一级报单中心" ; break ;
		case 2: $text = "二级报单中心" ; break ;
	}
	return $text ;
}
/**
 * 转账类型
 * @param unknown $id
 * @return unknown
 */
function getTftype($state){
	$text = "" ;
	switch ($state){
		case 0: $text = "转出" ; break ;
		case 1: $text = "<span style='color:red;'>转入</span>" ; break ;
	}
	return $text ;
}

function get_usernumber($uid){
	if($uid==0){
		$res = '公司';
	}else{
		$res = M('Member')->where(array('uid'=>$uid))->getField('usernumber');
		$res = query_user('usernumber',$uid);
		if($uid == is_login()){
			$res = "<span style='color:#F086E8'>{$res}</span>";
		}
	}
	
	return $res;
}

function get_com(){
	$res['uid']=0;
	$res['usernumber']='0';
	return $res;
}

/**
 * 加法
 * @param unknown $a
 * @param unknown $b
 * @return string
 */
function get_sum($a,$b){
	$res = sprintf("%0.2f", round($a+$b,2));
	$res = format_num($res);
	return $res;
}
function get_sum02($a,$b){
	$res = sprintf("%0.2f", round($a+$b,2));
	return $res;
}

/**
 * 乘法
 * @param unknown $a
 * @param unknown $b
 * @return string
 */
function  get_take($a, $b){
	$res = sprintf("%0.2f", round($a*$b,2));
	//$res = format_num($res);
	$jiangjin = get_bonus_rule('youhui_jjb')*$res*0.01;
	$jifen = get_bonus_rule('youhui_jifen')*$res*0.01;
	$text = $text.'奖金币'.$jiangjin.';积分'.$jifen;
	return $text;
}

/**
 * 减法
 * @param unknown $a
 * @param unknown $b
 * @return string
 */
function  get_sub($a, $b){
	$res = sprintf("%0.3f", round($a-$b,2));
	$res = format_num($res);
	return $res;
}

function format_num($num){
	$text = '';
	$num = sprintf("%0.3f", $num);
	if($num>0){
		$text = '<span style="color:red;display:inline;">'.$num.'</span>';
	}else{
		$text = $num;
	}
	return $text;
}
function get_order_status($status){
	$text = "" ;
	switch ($status){
		case 0: $text = "待付款" ; break ;
		case 1: $text = "待发货" ; break ;
		case 2: $text = "待确认" ; break ;
		default:$text = "已结束";
	}
	return $text ;
}
function get_pay_type($status){
	$text = "" ;
	switch ($status){
		case 1: $text = "支付宝" ; break ;
		case 2: $text = "奖金币" ; break ;
		case 5: $text = "复消币" ; break ;
		case 4: $text = "购物币" ; break ;
		default:$text = "未知";
	}
	return $text ;
}
/**
 * 用户级别
 * @param unknown $id
 * @return unknown
 */
function get_userrank2($a){

    $member=D("member")->where(array("uid"=>$a))->find();
    $text = "" ;
    $name = C('URANK_NAME');
    $bonusRule=get_bonus_rule();


        if ($member["hasbill"]>=0&&$member["hasbill"]<=$bonusRule["chia"]){
            $uplevel=1;
        }elseif($member["hasbill"]>$bonusRule["chia"]&&$member["hasbill"]<=$bonusRule["chib"]){
            $uplevel=2;
        }elseif($member["hasbill"]>$bonusRule["chib"]&&$member["hasbill"]<=$bonusRule["chic"]){
            $uplevel=3;
        }elseif($member["hasbill"]>$bonusRule["chic"]&&$member["hasbill"]<=$bonusRule["chid"]){
            $uplevel=4;
        }elseif($member["hasbill"]>$bonusRule["chid"]){
            $uplevel=5;
        }


    
    $color = M('RankColor')->where(array('rank'=>$uplevel,'types'=>0))->getField('fontcolor');
    $text = "<span style='color:{$color};'>{$name[$uplevel]}</span>";
  
	return $text ;
}