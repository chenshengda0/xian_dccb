<?php

namespace Shop\Controller;

/**
 * Class IndexController
 * 
 * @package Shop\Controller
 */
class SearchController extends HomeController {
	/**
	 * 商城初始化
	 */
	public function _initialize(){
		parent::_initialize();
	}
	public function index() {
		$key = I ( 'order' );
		$sort = I ( 'sort' );
		$keyword= I('searchtext');//获取分类的英文名称
		if (isset ( $key )) {
			if ($key == "1") {
				$listsort = "view" . " " . $sort;
			}
			if ($key == "2") {
				$listsort = "id" . " " . $sort;
			}
			if ($key == "3") {
				$listsort = "ms_price" . " " . $sort;
			}
			if ($key == "4") {
				$listsort = "sell_num" . " " . $sort;
			}
		}
		if (empty ( $key )) {
			$key = "1";
			$see = "asc";
			$order = "view";
			$sort = "asc";
			$listsort = $order . " " . $sort;
		}		
    	if ($sort == "asc") {
			$see = "desc";
		}
		if ($sort == "desc") {
			$see = "asc";
		}		
		$this->assign ( 'see', $see );
		$this->assign ( 'order', $key );
		$this->assign ( 'value', $sort );
		$this->assign ( 'keyword', $keyword );
		$map ['goods_name|goods_introduct|goods_detail'] = array ('like','%' . $keyword . '%' );
		$map ['status'] = 1;
		$goods_list = $this->lists('Shop',$map,$map,$listsort);
		$this->assign ( 'goodList', $goods_list ); // 赋值数据集
		$this->meta_title=$keyword.'搜索';	
        $this->display();
	}
}