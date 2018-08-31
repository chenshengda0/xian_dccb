<?php
/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */
function create_sign($time){
	$sign['t'] = $time;
	$sign['s'] = md5($time.SERVER_KEY);
	return $sign;
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}


/**
 * 生成系统AUTH_KEY
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function build_auth_key(){
    $chars  = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   // $chars .= '`~!@#$%^&*()_+-=[]{};:"|,.<>/?';
    $chars  = str_shuffle($chars);
    return substr($chars, 0, 40);
}

function  getState($state){

	$text = "" ;

	switch ($state){
		case 0: $text = "未激活" ; break ;
		case 1: $text = "已激活" ; break ;
		// 			case 2: $text = "中级业务员" ; break ;
		// 			case 3: $text = "高级业务员" ; break ;
		// 			case 4: $text = "初级代理商" ; break ;
		// 			case 5: $text = "中级代理商" ; break ;
		// 			case 6: $text = "高级代理商" ; break ;
		// 			case 7: $text = "超级代理商" ; break ;
	}

	return $text ;

}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
	$array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
	if(strpos($string,':')){
		$value  =   array();
		foreach ($array as $val) {
			list($k, $v) = explode(':', $val);
			$value[$k]   = $v;
		}
	}else{
		$value  =   $array;
	}
	return $value;
}

function  relase($a ,$b ){
	return $a - $b ;
}

//在线交易订单支付处理函数
//函数功能：根据支付接口传回的数据判断该订单是否已经支付成功；
//返回值：如果订单已经成功支付，返回true，否则返回false；
function checkorderstatus($ordid){
	$Ord=M('Orderlist');
	$ordstatus=$Ord->where('ordid='.$ordid)->getField('ordstatus');
	if($ordstatus==1){
		return true;
	}else{
		return false;
	}
}
//处理订单函数
//更新订单状态，写入订单支付后返回的数据
function orderhandle($parameter){
	$ordid=$parameter['out_trade_no'];
	$data['payment_trade_no']      =$parameter['trade_no'];
	$data['payment_trade_status']  =$parameter['trade_status'];
	$data['payment_notify_id']     =$parameter['notify_id'];
	$data['payment_notify_time']   =$parameter['notify_time'];
	$data['payment_buyer_email']   =$parameter['buyer_email'];
	$data['ordstatus']             =1;
	$Ord=M('Orderlist');
	$Ord->where('ordid='.$ordid)->save($data);
}

//获取一个随机且唯一的订单号；
function getordcode(){
	$yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
	$orderSn =$yCode[substr(intval(date('Y')) - 1970, -1)] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%d04%d02', rand(1000, 9999),rand(0,99));
	return $orderSn;
}



function getCity1($province,$city,$district){
	//$pro=M('district')->field('name')->where(array('id'=>array('in',array($province,$city,$district))))->select();

	$pro=M('district')->select("$province,$city,$district");

	if(!empty($pro)){
		return $pro[0]['name'].'-'.$pro[1]['name'].'-'.$pro[2]['name'];
	}
	return '无';
}



function get_catname($catid){
	return M('shop_category')->where(array('id'=>$catid,'status'=>1))->getField('title');
}