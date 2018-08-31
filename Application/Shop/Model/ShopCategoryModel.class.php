<?php

namespace Shop\Model;
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
     * 获得分类树
     * @param int $id
     * @param bool $field
     * @return array
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function getTree($id = 0, $field = true,$map=array()){
        /* 获取当前分类信息 */
        if($id){
            $info = $this->info($id);
            $id   = $info['id'];
        }
        /* 获取所有分类 */
        if(empty($map)){
        	$map['status']  = 1;
        }
        $list = $this->field($field)->where($map)->order('sort')->select();
        $list = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_', $root = $id);

        /* 获取返回数据 */
        if(isset($info)){ //指定分类则返回当前分类极其子分类
            $info['_'] = $list;
        } else { //否则返回所有分类
            $info = $list;
        }
        return $info;
    }


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