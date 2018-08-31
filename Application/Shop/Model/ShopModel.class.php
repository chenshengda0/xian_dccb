<?php

namespace Shop\Model;
use Think\Model;

/**
 * 文档基础模型
 */
class ShopModel extends Model{
	protected $_validate = array(
			array('goods_name', 'require', '商品名称不能为空 ', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
			array('tox_money_need', 'require', '请输入该商品单价 ', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
			array('category_id', 'require', '请选择商品分类 ', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		//	array('goods_name', '', '该商品名称已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_INSERT),
			
	);
	
	protected $_auto = array(
			array('createtime', NOW_TIME, self::MODEL_INSERT),
			array('changetime', NOW_TIME, self::MODEL_UPDATE),
			array('status', '1', self::MODEL_BOTH),
	);
	
	
	/**
	 * 获取商品信息
	 * @param int $id 商品id
	 * @param string $field 所需字段
	 * @return array|boolean
	 */
	public function getGoodsinfo($id,$field=NULL){
		if($field){
			if(is_array($field)){
				$field = arr2str($field);
			}
			$goodsinfo = $this->fields($field)->find($id);
		}else{
			$goodsinfo = $this->find($id);
		}
		
		if($goodsinfo){
			if(isset($goodsinfo['pics'])){
				$goodsinfo['pics'] = str2arr($goodsinfo['pics']);
			}
			return $goodsinfo;
		}else {
			return false;
		}
	}
	
	/**
	 * 删除商品
	 * @param unknown $id
	 */
	public function delGoods($ids){
		$pcinfo = $this->where(array('id'=>array('in',$ids)))->select();
		foreach ($pcinfo as $info){
			$picid = $info['goods_ico'];
			if(!empty($info['pics'])){
				$picid = str2arr($info['pics'].','.$info['goods_ico']);
			}
			if(is_array($picid)){
				$len = count($picid);
				for ($i=0;$i<$len;$i++){
					del_picture($picid[$i]);
				}
			}else{
				del_picture($picid);
			}
		}
		return $this->where(array('id'=>array('in',$ids)))->delete();
		
	}

}
