<?php
namespace Shop\Model;
use Think\Model;

class IndexModel extends Model{
	
	
	/**
	 * 获取热销产品
	 * @return unknown
	 */
	public function getIndexHot($num){
		//获取排列前四位的商品或者直接获取管理员指定的商品
		//首先获取用户指定的热销产品
		$map['is_hot']=1;
		$map['status']=1;
		$goods_list_hot=M('shop')->where($map)->order('changetime desc')->limit($num)->select();
		//若上述为空，则获取超过阀值的商品
		if(empty($goods_list_hot)){
			$hot_num = C('HOT_BUY');
			$map_hot['sell_num'] = array('egt', $hot_num);
			$map_hot['status'] = 1;
			$goods_list_hot = D('shop')->where($map_hot)->order('sell_num desc')->limit(8)->field($this->goods_info)->select();
		}
		return $goods_list_hot;
	}
	/**
	 * 获取最新或推荐的前4个商品
	 * @param unknown $num
	 * @return unknown
	 */
	public function getIndexNew($num){		
		$map['is_new']=1;
		$map['status']=1;
		$goods_list_new=M('shop')->where($map)->order('changetime desc')->limit($num)->select();		
		return $goods_list_new;
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
}