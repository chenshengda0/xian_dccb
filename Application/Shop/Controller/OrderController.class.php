<?php
namespace Shop\Controller;

use Common\Api\BonusApi;
class OrderController extends HomeController {
	
	/**
	 * 商城初始化
	 * 
	 * @author 郑钟良<zzl@ourstu.com>
	 */
	public function _initialize() {
		parent::_initialize();
	}


	/**
	 * 生成订单号
	 */
	 public function order() {	 
		/* 创建订单*/
		if(IS_POST){	
			$goodlist=M("shoplist");
			$uid=is_login();			
			$tag=create_order($uid); //标识号	
				
			$goodsids = I('id');
			$nums = I('num');
			$parameters = I('parameters');
			$srots = I('sort');
			$prices = I('price');
			
			$len = count($goodsids);
			for($i=0;$i<$len;++$i){
				$goodlist->where(array('orderid'=>'','uid'=>$uid,'goodid'=>$goodsids[$i]))->delete();
			}
			
			for($i=0;$i<$len;$i++){
				$goodlist->goodid = $goodsids[$i];
				$goodlist->status = 1;
				$goodlist->orderid ='';
				$goodlist->parameters =$parameters[$i];
				$goodlist->sort =$srots[$i];
				$goodlist->num = $nums[$i];
				$goodlist->uid=$uid;
				$goodlist->tag=$tag;//标识号必须相同
				$goodlist->create_time= NOW_TIME;
		        $goodlist->price =$prices[$i];
				$goodlist->total =$nums[$i]*$prices[$i];
				$goodlist->add();
		    } 

			$address=M('Transport')->where("status='1' and uid='$uid'")->find();	
			$address['area'] = $this->areaadd($address['area']);	    
			$shoplist= M('shoplist')->where("tag='$tag'")->select();  
			foreach ($shoplist as $k=>$val) {
				$total += $val['total'] ;   //商品总价
				$species+=$val['num'];		//商品数量		
			}    		
	       			
			//判断是否需要运费，并得到运费//计算所有商品总额
			if($total<C('LOWWEST')){
				$trans=C('SHIPMONEY');
			}else{
				$trans=0;
			}
	
			//获取当前用户的购物币 $uid
			$money=M('member')->where(array('status'=>1,'uid'=>$uid))->find();
			
			$this->assign('address',$address);			
			$this->assign('uid',$uid);
			$this->assign('shoplist',$shoplist);
			$this->assign('tag',$tag);
			$this->assign('num',$species);	
			$this->assign('trans',$trans);
			$this->assign('total',$total);	
			$this->assign('allprice',$total+$trans);	
			$this->assign('money',$money);	
			
		    $this->meta_title = '订单结算';
			$this->display();
		
		}
	}

	  /**
	   * 提交表单并跳转到支付界面
	   * @return [type] [description]
	   */
	 public function createorder() {
	 	if(IS_POST){
	 		//判断用户是否重复提交订单
	 		$uid=is_login();
		 	$order=D("ShopOrder");
		 	$Shoplist = D('Shoplist');
		 	
			$tag=I('post.tag');
			$paytype = I('post.paytype');
			
			$value=$order->where(array('orderid'=>$tag))->getField('id');
			isset($value)&& $this->error('重复提交订单');
			
			//判断用户是否没有选择收货地址
			$addressid=I('post.addressid');
			if(!empty($addressid)&&is_numeric($addressid)){								
				$address=M('transport')->where(array('status'=>1,'id'=>$addressid,'uid'=>$uid))->find();
				if(empty($address)){
					$this->error('请选择一个收货地址');
				}				
			}else{
				$this->error('请选择一个收货地址');
			}
			
			//根据订单id获取购物清单并清空购物车
			$del=$Shoplist->where(array('tag'=>$tag))->select();				
			//遍历购物清单，删除登录用户购物车中的货物id
			$this->delShopCart($del);
			
			//计算提交的订单的商品总额(提交的数据不可信)		
			$total=$Shoplist->getGoodsPrice($tag);//I('totalprice');
			
			//计算提交的订单的商品运费
			if($total<C('LOWWEST')){
				$trans=C('SHIPMONEY');	 
			}else{
			 	$trans=0;
			}
			//计算提交的订单的费用（含运费）
			$xfee=$total+$trans;
			//计算提交的订单的总费用
			$all=$total+$trans;//-$ratio-$decfee;
			
			//查询折扣规则
			
			
			$data['total']=$total;//商品金额
			$data['shipprice']=$trans;//运费		
			$data['pricetotal']=$all;//总金额（带运费）
			
			$data['addressid']=$addressid;//总金额（带运费）
			
			$data['orderid']=$tag;//订单号
			$data['uid']=$uid;//当前用户id
			
			$data['create_time']=NOW_TIME;//创建时间
			$data['update_time']=NOW_TIME;//更新时间
			if($paytype==1)	{
				$this->error('暂不支持该支付方式',U('Index/index'));
			}elseif ($paytype==5){//复消币购买
				$hascp = M('member')->where(array('uid'=>$uid,'status'=>1))->getField('hascp');
				if($hascp<$all){
					$this->error('复消币不足',U('Index/index'));
				}
				//扣除该用户的复消币
				$res = M('member')->where(array('uid'=>$uid,'status'=>1))->setDec('hascp',$all);
				if($res){
					//生成购物记录
					$this->createMoneyChange($uid, 5, $all, 'hascp');
					//更新商品shop总的销售数量
					$this->updateShop($del);
					//判断购物币是否大于商品数额
					$data['ispay']=5; //支付方式：复消币
					$data['status']= 1;//支付状态：已支付
					//根据订单id保存对应的费用数据
					$orderid=$order->add($data);
					//更新商品清单中的订单号
					$Shoplist->where(array('tag'=>$tag))->setField('orderid',$orderid);
				}else{
					$this->error('支付失败',U('Index/index'));
				}
			
			}elseif($paytype==2){//奖金币购买
				
				$shopUser  = M('member')->where(array('uid'=>$uid,'status'=>1))->find();

				$hasshop = $shopUser['hasmoney'];
				if($hasshop<$all){
					$this->error('奖金币不足',U('Index/index'));
				}
				
				//扣除该用户的购物币
				$res = M('member')->where(array('uid'=>$uid,'status'=>1))->setDec('hasmoney',$all);
				if($res){
// 					$res = M('member')->where(array('uid'=>$uid,'status'=>1))->setInc('hasbill',$all);
// 					$res = M('member')->where(array('uid'=>$uid,'status'=>1))->setInc('allbill',$all);
					$hasbill = $shopUser['hasshop']+$all; 
					
					$type=array('recordtype'=>1,'changetype'=>32,'moneytype'=>2);
					$money_arr=array('money'=>$all,'hasmoney'=> $hasbill,'taxmoney'=>0);
					money_change($type, $shopUser, get_com(), $money_arr);
					
					//生成购物记录
					$this->createMoneyChange($uid, 2, $all, 'hasmoney');
					//更新商品shop总的销售数量
					$this->updateShop($del);
					//判断购物币是否大于商品数额
					$data['ispay']=2; //支付方式：购物币
					$data['status']= 1;//支付状态：已支付
					//根据订单id保存对应的费用数据
					$orderid=$order->add($data);
					//更新商品清单中的订单号
					$Shoplist->where(array('tag'=>$tag))->setField('orderid',$orderid);
				}else{
					$this->error('支付失败',U('Index/index'));
				}
			}
			
			$this->meta_title = '完成支付';
			$this->success('支付成功',U('tobeshipped'));
		}
	 }
	 
	 /**
	  * 清空购物车
	  * @param array $list
	  */
	 public function delShopCart($list){
	 	$uid = is_login();
	 	$shopcart = M("shopcart");
	 	$map['uid'] = $uid;
	 	foreach($list as $val){
	 		$map['goodid'] = $val["goodid"];
	 		$shopcart->where($map)->delete();
	 		unset($map['goodid']);
	 	}
	 }
	 
	 /**
	  * 更新商品销售状态
	  * @param array $list
	  */
	 private function updateShop($list){
	 	$shop = M('Shop');
	 	foreach($list as $k=>$val){
	 		//获取购物清单数据表产品id，字段goodid
	 		$goodid=$val["goodid"];
	 		//更新销售数量
	 		$shop->where(array('id'=>$goodid))->setInc('sell_num',$val['num']);
	 		//减少商品剩余个数
	 		$shop->where(array('id'=>$goodid))->setDec('goods_num',$val['num']);
	 		unset($goodid);
	 	}
	 	
	 }
	 
	 /**
	  * 创建购物流水
	  * @param int $uid
	  * @param int $moneytype
	  * @param double $money
	  * @param string $field
	  */
	 private function createMoneyChange($uid,$moneytype,$money,$field){
	 	$targetinfo = M('Member')->where(array('uid'=>$uid))->find();
	 	$userinfo = get_com();
	 	$type=array('recordtype'=>0,'changetype'=>26,'moneytype'=>$moneytype);
		$money=array('money'=>$money,'hasmoney'=>$targetinfo[$field],'taxmoney'=>0);
	 	money_change($type, $targetinfo, $userinfo, $money);
	 }
	 
	 /**
	  * 我的所有订单
	  */
	 public  function allorder(){
	 
	 	$map['total'] = array('exp','is not null');
	 	$list = $this->orderList($map);
	 	$this->assign('order',$list);// 赋值数据集
	 	$this->meta_title = '我的所有订单';
	 	$this->display('orderList');
	 }
	 
	 /* 待发货订单*/
	 public  function tobeshipped(){
	 	$map['status'] = 1;//已支付
	 	$list = $this->orderList($map);
	 	$this->assign('order',$list);// 赋值数据集
	 	$this->meta_title = '待发货订单';
	 	$this->display('orderList');
	 }
	 
	 
	 
	 /* 待确认订单*/
	 public  function tobeconfirmed(){
	 	$map['status'] = 2;//已发货
	 	$map['ispay'] = array('gt',1);
	 	$list = $this->orderList($map);
	 	$this->assign('order',$list);// 赋值数据集
	 	$this->meta_title = '待发确认货订单';
	 	$this->display('orderList');
	 }
	 
	 /* 已完成订单*/
	 public  function finishOrder(){
	 	$map['status'] = array('egt',3);//已发货
	 	$map['ispay'] = array('gt',1);
	 	$list = $this->orderList($map);
	 	$this->assign('order',$list);// 赋值数据集
	 	$this->meta_title = '已完成订单';
	 	$this->display('orderList');
	 }
	 
	 /**
	  * 确认订单
	  * @param unknown $id
	  */
	 public function confirmOrder($id){
	 	if(IS_POST){
	 		$uid = is_login();
	 		$data['status'] = 3;
	 		$data['update_time'] = time();
	 		$res = M('shop_order')->where(array('orderid'=>$id))->save($data);
	 		if($res){
	 			$info = M('shop_order')->where(array('orderid'=>$id))->field('ispay,pricetotal')->find();
	 			if($info['ispay']==2){//奖金币消费返利
	 				$BonusApi = new BonusApi();
	 				
	 				//开始返利
	 				$BonusApi->bonushm($uid,$info['pricetotal']);
	 			}
	 			$this->success('签单成功');
	 		}else{
	 			$this->error('签单失败');
	 		}
	 	}
	 }
	 
	 /**
	  * 显示订单详细信息
	  */
	 public function detail(){
	 	$id=I('id');
	 	$order=M('ShopOrder')->field('orderid,addressid,pricetotal,backinfo,ispay,status')->where(array('orderid'=>$id))->find();
	 	
	 	$detail=M('shoplist')->where(array('tag'=>$id))->select();
	 	
	 	$trans=M("transport")->where(array('id'=>$order['addressid']))->find();
	 	$trans['area'] = $this->areaadd($trans['area']);
	 	$this->assign('trans',$trans);
	 	
	 	$this->assign('detaillist',$detail);
	 	$this->assign('order',$order);
	 
	 	$this->meta_title = '订单详情';
	 	$this->display();
	 }
	 
	 /**
	  * 订单信息
	  * @param array $map
	  * @return array $list;
	  */
	 public function orderList($map){
	 	$uid = is_login();
	 	$order=M("ShopOrder");
	 	$detail=M("shoplist");
	 	$map['uid'] = $uid;
	 	$map['tag'] = array(array('neq',' '),array('exp','is not null'),'and');
	 	$list = $this->lists($order,$map,$map,'id desc');
	 	foreach($list as &$val){
	 		$val['id']=$detail->where(array('orderid'=>$val['id']))->select();
	 
	 	}
	 	return $list;
	 }
}