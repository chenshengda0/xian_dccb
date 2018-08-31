<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model;

/**
 * 分类模型
 */
class ShopModel extends Model{

	/**
	 * 获取所有商品运费
	 */
	public function getFreight(){
		$map ["uid"] = is_login();
		if ($this->getCnt () == 0) {
			// 种数为0，个数也为0
			return 0;
		} else {
			$data = M('shopcart')->where ( $map )->select ();
			$allfreight=0;
			foreach ( $data as $k => $item ) {
				$freight=M('shop')->where(array('status'=>1,'id'=>$item['goodid']))->getField('freight');
				$allfreight+=$freight*$item ['num'];
			}
			return $allfreight;
		}
	}
	
	/**
	 * 获取所有商品运费
	 */
	public function getG(){
		$map ["uid"] = is_login();
		$r1=$r2=0;
		if ($this->getCnt () == 0) {
			// 种数为0，个数也为0
			return 0;
		} else {
			$data = M('shopcart')->where ( $map )->select ();
			$allfreight=0;
			foreach ( $data as $k => $item ) {
				$is_join=M('shop')->where(array('status'=>1,'id'=>$item['goodid']))->getField('is_join');
				if($is_join){
					$r1+=$item['price']*$item ['num'];
				}else{
					$r2+=$item['price']*$item ['num'];
				}
				
			}
			return array('r1'=>$r1,'r2'=>$r2);
		}
	}

	/**
	 * 查询购物车中商品的种类
	 *
	 * @return [type] [description]
	 */
	public function getCnt() {
		$map ["uid"] = is_login();
		return M('shopcart')->where ( $map )->count();
	}
	/**
	 * 查询登录用户购物车中商品的个数 
	 * @return number|unknown
	 */
	public function getNumByuid() {
		$map ["uid"] = is_login ();
		$species=$this->getCnt ();
		if ($species == 0) {
			// 种数为0，个数也为0
			return 0;
		} else {
			$data = M('shopcart')->where ( $map )->select ();
			foreach ( $data as $k => $item ) {
				$sum += $item ['num'];
				$all+=$item['price']*$item ['num'];
			}
		}
		return array('species'=>$species,'num'=>$sum,'totalprice'=>$all);
	}
	/**
	 * 获取商品信息
	 * @param int $id 商品id
	 * @param string $field 所需字段
	 * @return array|boolean
	 */
	public function getGoodsinfo($id,$field=NULL){
		$_data['status']=1;
		if($field){
			if(is_array($field)){
				$field = arr2str($field);
			}
			$goodsinfo = $this->where($_data)->fields($field)->find($id);
		}else{
			$goodsinfo = $this->where($_data)->find($id);
		}
	
		if($goodsinfo){
			if(isset($goodsinfo['pics'])){
				$goodsinfo['pics'] = str2arr($goodsinfo['pics']);
			}			
			$colors=$goodsinfo['color'];
			if(!empty($colors)){
				$colors=explode(',',$colors);
				if(is_array($colors)){
					$goodsinfo['colors']=$colors;
				}
			}
			$attrs=$goodsinfo['attribute'];
			if(!empty($attrs)){
				$attrs=explode(',',$attrs);
				if(is_array($attrs)){
					$goodsinfo['attrs']=$attrs;
				}
			}
			return $goodsinfo;
		}else {
			return false;
		}
	}
	/**
	 * 获取最新或推荐的前4个商品
	 * @param unknown $num
	 * @return unknown
	 */
	public function getIndexRec($num){
		$map['is_recommend']=1;
		$map['status']=1;
		$goods_list_rec=M('shop')->where($map)->order('changetime desc')->limit($num)->select();
		return $goods_list_rec;
	}
	
	/**
	 * 获取热销产品
	 * @return unknown
	 */
	public function getIndexHot($num){
		//获取排列前四位的商品或者直接获取管理员指定的商品		
		$map['is_hot']=1;
		$map['status']=1;
		$goods_list_hot=M('shop')->where($map)->order('changetime desc')->limit($num)->select();		
		return $goods_list_hot;
	}
	/**
	 * 获取最新前4个商品
	 * @param unknown $num
	 * @return unknown
	 */
	public function getIndexNew($num){
		$map['is_new']=1;
		$map['status']=1;
		$goods_list_new=M('shop')->where($map)->order('changetime desc')->limit($num)->select();
		return $goods_list_new;
	}
	
}
