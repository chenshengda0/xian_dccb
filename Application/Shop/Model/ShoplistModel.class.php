<?php
namespace Shop\Model;

use Think\Model;

/**
 * 购买商品列表
 * @author Administrator
 */
class ShoplistModel extends Model {
	
	public function getGoodsPrice($tag){
		$map['uid'] = is_login();
		$map['tag'] = $tag;
		$list = $this->where($map)->select();
		$total = 0;
		foreach ($list as $v){
			$total += $v['price']*$v['num'];
		}
		return $total;
	}

}
