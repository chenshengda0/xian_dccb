<?php
namespace Home\Controller;
use Think\Pay\PayVo;
use Think\Pay;

class PayController extends HomeController {
	
    public function index() {
        if (IS_POST) {
        	$uid = is_login();
            //页面上通过表单选择在线支付类型，支付宝为alipay 财付通为tenpay
            $paytype = I('post.paytype');
            $money = I('post.money');
            $pay = array(
            		// 收款账号邮箱
            		'email' =>C('ALIPAYEMAIL'),
            		// 加密key，开通支付宝账户后给予
            		'key' => C('ALIPAYKEY'),
            		// 合作者ID，支付宝有该配置，开通易宝账户后给予
            		'partner' => C('ALIPAYPARTNER')
            );
          
            $pay = new Pay($paytype,$pay);
            
           
			$body= "购物充值";//商品描述
			$title=C('SITENAME')."购物币充值";//设置商品名称
			$order_no=create_order();//未付款订单号
           	$pargram = M('Member')->where(array('uid'=>$uid))->field('uid,usernumber,mobile')->find();
            $vo = new PayVo();
            $vo->setBody($body)
            ->setFee($money) //支付金额
            ->setOrderNo($order_no)//订单号
            ->setTitle($title)//设置商品名称
            ->setCallback("Home/Pay/success")/*** 设置支付完成后的后续操作接口 */
            ->setUrl(U("Home/TradingHall/rechargerecord")) /* 设置支付完成后的跳转地址*/
            ->setParam($pargram);
            echo $pay->buildRequestForm($vo);
        }
    }

    /**
     * 订单支付成功
     * @param type $money
     * @param type $param
     */
    public function success($money, $param) {
        if (session("pay_verify") == true) {
            session("pay_verify", null);
	  		//配置邮件提醒   
	  		$uid = $param;
			$mobile=query_user('mobile',$uid);
			//$ratio_rg = M('BonusRole')->where(array('id'=>1))->getField('ratio_rg');
			$uinfo = M('Member')->where(array('uid'=>$uid))->find();
			$frominfo = get_com();
			$elc_money = $money;
			$data['hasbill'] = $uinfo['hasbill']+ $elc_money;
			$data['recharge'] = $uinfo['recharge']+ $elc_money;
			$data['allbill'] = $uinfo['allbill']+ $elc_money;
			$res = M('Member')->where(array('uid'=>$uid))->save($data);
			if($res){
				M("Recharge")->where(array('order_id' => $param['order_id']))->setField('status', 1);
				M("Recharge")->where(array('order_id' => $param['order_id']))->setField('handtime', time());
				moneyChange(1, 21, $uinfo, $frominfo, $elc_money, $data['hasmoney'], 0);
			}
	     	/* if(C('ISSMS')){
				$content = "尊贵的会员，您于".time()."在本公司花费{$money}元充值".$elc_money."电子币[".C('WEB_SITE')."]" ;
		    	//$SMScode = sendSMS($mobile,$content);
		 	} */
			
			return true;
    }

}

/**
 * 支付结果返回
 */
public function notify() {
	$apitype = I('get.apitype');
	
	$pay = array(
			// 收款账号邮箱
			'email' =>C('ALIPAYEMAIL'),
			// 加密key，开通支付宝账户后给予
			'key' => C('ALIPAYKEY'),
			// 合作者ID，支付宝有该配置，开通易宝账户后给予
			'partner' => C('ALIPAYPARTNER')
	);
	
	$pay = new Pay($apitype,$pay);

	if (IS_POST && !empty($_POST)) {
		$notify = $_POST;
	} elseif (IS_GET && !empty($_GET)) {
		$notify = $_GET;
		unset($notify['method']);
		unset($notify['apitype']);
	} else {
		exit('Access Denied');
	}
	//验证
	if ($pay->verifyNotify($notify)) {
		//获取订单信息
		$info = $pay->getInfo();

		if ($info['status']) {
			$payinfo = M("Recharge")->field(true)->where(array('order_id' => $info['out_trade_no']))->find();
			if ($payinfo['status'] == 0 && $payinfo['callback']) {
				session("pay_verify", true);
				$check = R($payinfo['callback'], array('money' => $info['money'], 'param' => $payinfo['userid']));
				if ($check !== false) {
					M("Recharge")->where(array('order_id' => $info['out_trade_no']))->setField(array('handtime' => time(), 'status' => 1));
				}
			}
			if (I('get.method') == "return") {
				redirect($payinfo['url']);
			} else {
				$pay->notifySuccess();
			}
		} else {
			$this->error("支付失败！");
		}
	} else {
		E("Access Denied");
	}
}

	public function over() {
		$this->meta_title = '支付成功';
		$this->display('success');
	}
	
}