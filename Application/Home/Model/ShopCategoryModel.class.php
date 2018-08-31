<?php

namespace Home\Model;
use Think\Model;

/**
 * Class Shop_categoryModel
 * @package Shop\Model
 * @郑钟良
 */
class ShopCategoryModel extends Model {

    protected $tableName='shop_category';
    protected $_validate = array(
        array('url','require','url必须填写'), //默认情况下用正则进行验证
    );
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_UPDATE),
        array('status', '1', self::MODEL_INSERT),
    );

    /**
     * 获取分类详细信息
     * @param $id
     * @param bool $field
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function info($id, $field = true){
        /* 获取分类信息 */
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['id'] = $id;
        } else { //通过标识查询
            $map['name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }
    
    public function getCategoryById($id){
    	if(empty($id)||!is_numeric($id))
    		return false;
    	$pid=$this->where(array('status'=>1,'id'=>$id))->getField('pid');
    	if(!empty($pid))
    		$categoryInfo=$this->getTree($pid);
    	else 
    		$categoryInfo=$this->getTree($id);
    	if(!empty($categoryInfo)){
    		return $categoryInfo['_'];
    	}
    	return false;
    }
    
    
    /**
     * 获取分类下没有商品的分类
     */
    public function noHasGoods($map=array()){
    	$map['status'] = 1;
    	$list = $this->where($map)->select();
    	$cates = array();
    	foreach ($list as $cate){
    		$id = M('Shop')->where(array('category_id'=>$cate['id']))->getField('id');
    		if(!$id){
    			$cates[] = $cate;
    		}
    	}
    	return $cates;
    }
    
    /**
     * 查询分类的所有叶子节点
     */
    public function getLeaf($map=array()){
    	if(empty($map)){
    		$map['status'] = 1;
    	}
    	$list = $this->where($map)->select();
    	$leaf = array();
    	foreach ($list as $cate){
    		$id = $this->where(array('pid'=>$cate['id']))->where($map)->getField('id');
    		if(!$id){
    			$leaf[] = $cate;
    		}
    	}
    	return $leaf;
    }

}