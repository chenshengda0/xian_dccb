<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Shop\Model;

use Think\Model;
use Think\Page;

/**
 * 文档基础模型
 */
class ShopcartModel extends Model {
	/* 查询登录用户购物车中商品的个数 */
	public function getNumByuid() {
		$map ["uid"] = $this->uid ();
		if ($this->getCnt () == 0) {
			// 种数为0，个数也为0
			return 0;
		} else {
			$data = $this->where ( $map )->select ();
			foreach ( $data as $k => $item ) {
				$sum += $item ['num'];
			}
		}
		return $sum;
	}
	
	/**
	 * 查询购物车中的数据
	 * @return Ambigous <\Think\mixed, boolean, string, NULL, mixed, multitype:, unknown, object>
	 */
	public  function getcart() {
	    $uid = is_login();
		$map["uid"]= $uid;
	    $cartlist=$this->where($map)->select();
	    foreach ($cartlist as &$v){
	    	$v['goods_name'] = get_good_name($v['goodid']);
	    	$v['goods_ico'] = $this->getGoodPic($v['goodid']);
	    	$v['subtotal'] = sprintf ( "%01.2f", $v['num']*$v['price']);
	    }
		return $cartlist; 
	}
	
	/**
	 * 查询登录用户购物车中商品的总金额
	 * 
	 * @return [type] [description]
	 */
	public function getPriceByuid() {
		$map ["uid"] = $this->uid ();
		// 数量为0，价钱为0
		if ($this->getCnt () == 0) {
			return 0;
		} else {
			$total = 0.00;
			$data = $this->where ( $map )->select ();
			foreach ( $data as $k => $val ) {
				$price = $val ['price'];
				$total += $val ['num'] * $price;
			}
		}
		return sprintf ( "%01.2f", $total );
	}

	public function getCntByuid() {
		$uid = is_login();
		$map ["uid"] = $uid;
		$cartlist = $this->where ( $map )->select ();
		return count ( $cartlist );
	}
	
	/**
	 * 查询购物车中商品的种类
	 * 
	 * @return [type] [description]
	 */
	public function getCnt() {
		$uid = is_login();
		$map ["uid"] = $uid;
		return $this->where ($map)->count ();
	}
	
	/* 登录用户增加购物车中商品的个数 */
	public function inc($sort) {
		$uid = $this->uid ();
		$cart = D ( "shopcart" );		
		$cart->where("sort='$sort'and uid='$uid'" )->setInc('num');
		return $cart->where("sort='$sort'and uid='$uid'" )->getField('num');		
	}

	/* 登录用户减少购物车中商品的个数*/
	public function dec($sort){
		$uid = $this->uid ();
		$cart = D ( "shopcart" );		
		$cart->where("sort='$sort'and uid='$uid'" )->setDec('num');
	    return $cart->where("sort='$sort'and uid='$uid'" )->getField('num');		

	}

	/* 登录用户删除购物车中商品的个数*/
	public function deleteid($sort){
		$uid = is_login();
		$cart = D ( "shopcart" );
		$result= $cart->where("sort='$sort'and uid='$uid'")->delete();
		return $result;
	}
	
	/**
	 * 获取当前登录用户的id
	 * 
	 * @return [type] [description]
	 */
	public function uid() {
		$user = session ( "user_auth" );
		$uid = $user ["uid"];
		return $uid;
	}

	 public function getPricetotal($tag) { 
	        $data = M("shoplist")->where("tag='$tag'")->select();
	        foreach ($data as $k=>$val) {
		// $price=$val['price'];
	 //           $total += $val['num'] * $price;
	 	    $total+=	$val['total'] ;	           
	        }
	        return sprintf("%01.2f", $total);
	}
	
	public function getCartAll(){
		$list = $this->getcart();
		$cart_num = $this->getNumByuid();
		$total = $this->getPriceByuid();
		$res['list'] = $list;
		$res['other'] = array('cart_num'=>$cart_num,'total'=>$total);
		$res['status'] = 1;
		
		return $res;
	}

	/**
	 * 根据商品id获取商品图片
	 * @param  [type] $goodid [description]
	 * @return [type]         [description]
	 */
	public function getGoodPic($goodid){
		$picid=M('shop')->where("id=$goodid")->getField('goods_ico');
		$arr=array(
			'id'=>$picid,
			'status'=>1
			);
		return __ROOT__.M('picture')->where($arr)->getField('path');
	}
}
