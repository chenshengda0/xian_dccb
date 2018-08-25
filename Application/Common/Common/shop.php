<?php
function get_store_title($usernumber){
	$uid = M('Member')->where(array('usernumber'=>$usernumber))->getField('uid');
	return M('Stores')->where(array('uid'=>$uid))->getField('shopname');
}
/**
 * 获取商品信息
 * @param unknown $goodsid
 * @param string $field
 * @return unknown
 */
function get_goods($goodsid,$field=null){
	if($field){
		$res = M('Shop')->where(array('id'=>$goodsid))->getField($field);
	}else{
		$res = M('Shop')->where(array('id'=>$goodsid))->find();
	}
	return $res;
}

/**
 * 获取收货地址信息
 * @param int $id
 * @return string
 */
/* function address($id){
	$map['id'] = $id;
	$row = M('ShopTransport')->where($map)->field('area,address')->find();
	$areaid = array_filter(explode(',', $row['area']));
	$len = count($areaid);
	for ($i=0;$i<$len;$i++){
		$area[] = M('District')->where(array('id'=>$areaid[$i]))->getField('name');
	}
	$address = $area[0].$area[1].$area[2].$row['address'];
	return $address;
} */
/**
 * 面包屑导航
 * @param unknown $id
 * @param string $field
 * @return string|Ambigous <>
 */
function get_shop_location($id, $field = null){
	static $list;
	//非法分类ID
	if (empty ( $id ) || ! is_numeric ( $id )) {
		return;
	}
	//读取缓存数据
	if (empty($list)) {
		$list = S( 'sys_shop_category_list' );
	}
	//获取分类名称
	if (! isset ( $list [$id] )) {
		$cate = M ( 'shop_category' )->find ( $id );
		if (! $cate || 1 != $cate ['status']) { // 不存在分类，或分类被禁用
			return '';
		}
		$list [$id] = $cate;
		S('sys_shop_category_list', $list ); // 更新缓存
	}
	if (is_null ( $field )) {
		return $list [$id];
	} else {
		$a = $list [$id] [$field];
		$NameOne = $list [$id] ['id'];
		$UrlOne = U('Index/goods', array('id' =>$NameOne));
		$LeverOne = '<a href="' . $UrlOne . '">' . $a . '</a>';
		if (! empty ( $list [$id] ['pid'] )) { // 2级分类，第2级
			$pid = $list [$id] ['pid'];
			$cat = M ('shop_category');
			if (0 !== $pid) {
				// 根据pid获取上一级category的标题和标识
				$TitleTWO = $cat->where ( "id='$pid'" )->getField ( 'title' );
				// 设置链接
				$UrlTwo = U ('Index/goods', array ('id' => $pid));
				$LeverTWO = '<a href="' . $UrlTwo . '">' . $TitleTWO . '</a>';
				// 获取当前分类的上级分类主键id
				$Id = $cat->where ( "id='$pid'" )->getField ( 'pid' );
				if (! empty ( $Id )) { // 判断是否是一级分类,获取标题和标识
					$TitleThree = $cat->where ( "id='$Id'" )->getField ( 'title' );
					$NameThree = $cat->where ( "id='$Id'" )->getField ( 'name' );
					// 设置链接
					$UrlThree = "index.php?s=/Home/Article/index/category/" . $NameThree;
					$LeverThree = '<a href="' . $UrlThree . '">' . $TitleThree . '</a>';

					return $LeverThree . ">" . $LeverTWO . ">" . $LeverOne;
				} else {
					// 只有2级的分类
					return $LeverTWO . ">" . $LeverOne;
				}
			} else {
				// 只有1级的分类
				return $LeverOne;
			}
		} else {
			return $LeverOne;
		}
	}
}

function get_location_name($id) {
	return get_shop_location( $id, 'title' );
}


/* 商品名称调用 */
function get_good_name($id){
	return M('shop')->where("id=$id")->getField('goods_name');
}

/**
 * 根据用户id获取收件地址id
 * @param  [type] $uid [description]
 * @return [type]      [description]
 */
function get_addressid($uid){
	return M('Transport')->where("status='1' and uid='$uid'")->find();
}

/**
 * 根据商品id获取商品图片
 * @param  [type] $goodid [description]
 * @return [type]         [description]
 */
function getGoodPic($goodid){
	$picid=M('shop')->where("id=$goodid")->getField('goods_ico');
	$arr=array(
			'id'=>$picid,
			'status'=>1
	);
	return __ROOT__.M('picture')->where($arr)->getField('path');
}

/* 商品价格调用 */
function get_good_price($id){
	//$row = M('document')->getbyId($id);
	$row=M('shop')->where(array('id'=>$id))->getField('ms_price');
	return $row;
}
/**
 * 根据类别id判断首页是否有推荐的类别广告 *
 * @param unknown $adid 一级栏目ID
 * @return string
 */
function getAd($adid,$sy=0) {
	$ad = M ( 'ad' )->where ( array (
			'status' => 1,//启用状态
			'ypid' => $adid,
			'place'=>1,//放置的位置
			'shop_type' => $sy,
	) )->order ( 'update_time desc' )->find ();
	if (! empty ( $ad )) {
		$html = "<div class='index_adv01 mar_auto mar30'>
				<a href='" . get_nav_url ( $ad ['url'] ) . "' target='_blank'>
				<img src='" . get_cover ( $ad ['icon'], 'path' ) . "' width='1200' height='118'>
				</a></div>";
	}
	return $html;
}

/**
 * 根据不同区域获取广告值
 * 首先判断是否设置了url，若设置了就判断是否是全路径，若没有设置url就走到搜索页，（后期再加上活动商品id）
 * @param unknown $mark
 */
function getAd_02($mark,$sy=0){
	$ad = M ( 'ad' )->where ( array (
			'status' => 1,//启用状态
			'place'=>1,//放置的位置
			'ypid' => $mark,
			'shop_type' => $sy,
	) )->order ( 'update_time desc' )->find ();
	if(!empty($ad['url'])){
		//if(is_http_url($ad['url'])){
		return $html="<a href='".get_nav_url ( $ad ['url'] )."' target='_blank'><img src='".get_cover ( $ad ['icon'], 'path' )."' width='130' height='130'/></a>";
		//}
	}
	return $html="<a href='".U('Search/index',array('searchtext'=>$ad['keywords'] ))."' target='_blank'><img src='".get_cover ( $ad ['icon'], 'path' )."' width='130' height='130'/></a>";
}

function get_nav_active_02($url) {
	if(stripos( $_SERVER ['QUERY_STRING'],$url)!==false){
		return 1;
	}
	return 0;
}

/**
 * 判断是否为http或https或#
 * @param unknown $url
 * @return boolean
 */
function is_http_url($url) {
	switch ($url) {
		case 'http://' === substr ( $url, 0, 7 ) :
		case 'https://' === substr ( $url, 0, 8 ) :
		case '#' === substr ( $url, 0, 1 ) :
			return true;
	}
	return false;
}
function getcolor($k) {
	$colorArr = Array (
			'index_firstfloor',
			'index_secondfloor',
			'index_thirdfloor',
			'index_fourthfloor',
			'index_fifthfloor',
			'index_sixthfloor',
			'index_seventhfloor',
			'index_eighthfloor'
	);
	return $colorArr [$k];
}

/**
 * 代理商模块公共函数
 */
function get_all_cate_name($cid){
	static $ids = array();
	$ids = get_parent_category_id($cid);
	$name = '';
	$len = count($ids);
	for ($i=0;$i<$len;$i++){
		if($i==$len-1){
			$name .=get_shop_category($ids[$i],'title');
		}else{
			$name .= get_shop_category($ids[$i],'title').'>';
		}
	}

	return $name;
}

/**
 * 获取分类信息并缓存分类
 * @param  integer $id 分类ID
 * @param  string  $field 要获取的字段名
 * @return string         分类信息
 */
function get_shop_category($id, $field = null)
{
    static $list;

    /* 非法分类ID */
    if (empty($id) || !is_numeric($id)) {
        return '';
    }

    /* 读取缓存数据 */
    if (empty($list)) {
        $list = S('sys_shop_category_list');
    }

    /* 获取分类名称 */
    if (!isset($list[$id])) {
        $cate = M('ShopCategory')->find($id);
        if (!$cate || 1 != $cate['status']) { //不存在分类，或分类被禁用
            return '';
        }
        $list[$id] = $cate;
        S('sys_shop_category_list', $list); //更新缓存
    }
    return is_null($field) ? $list[$id] : $list[$id][$field];
}
/**
 * 获取参数的所有父级分类
 * @param int $cid 分类id
 * @return array 参数分类和父类的信息集合
 * @author huajie <banhuajie@163.com>
 */
function get_parent_category_id($cid){
	if(empty($cid)){
		return false;
	}
	$ids  =   M('ShopCategory')->where(array('status'=>1))->getField('id',true);
	$pid  =   get_shop_category($cid,'pid');	//获取参数分类的信息

	$res[]  =   $cid;
	while(true){
		foreach ($ids as $id){
			if($id == $pid){
				array_unshift($res, $id);	//将父分类插入到数组第一个元素前
				$pid = get_shop_category($id,'pid');
			}
		}
		if($pid == 0){
			break;
		}
	}
	return $res;
}

/**
 * 获取参数的所有父级分类导航
 * @param int $cid 分类id
 * @return array 参数分类和父类的信息集合
 * @author huajie <banhuajie@163.com>
 */
function get_parent_category_nav($cid){
	if(empty($cid)){
		return false;
	}
	$info = M('ShopCategory')->where(array('id'=>$cid))->field('title,pid')->find();
	$list = M('ShopCategory')->where(array('status'=>1))->select();
	$url = U ('Index/goods', array ('id' => $cid));
	$href = "<a href='{$url}'>{$info['title']}</a>";
	$pid = $info['pid'];
	while(true){
		foreach ($list as $cate){
			if($cate['id'] == $pid){
				$url = U ('Index/goods', array ('id' => $cate['id']));
				$href = "<a href='{$url}'>{$cate['title']}</a>".'&nbsp;&nbsp;>&nbsp;&nbsp;'.$href;
				$pid = $cate['pid'];
			}
		}
		if($pid == 0){
			break;
		}
	}
	return $href;
}

/**
 * 获取参数分类下的所有字分类
 * @param int $cid 分类id
 * @return array 参数分类和父类的信息集合
 * @author huajie <banhuajie@163.com>
 */
function get_children_category($pid=0){
	
	$list = M('ShopCategory')->field('id,pid')->select();
	if($pid!=0){
		$res = get_all_children($list,$pid);
		
		array_unshift($res, $pid);
	}else{
		$res =  M('ShopCategory')->getField('id',true);
	}
	return $res;
}

function is_has_goods($id){
	$res = M('Shop')->where(array('category_id'=>$id))->getField('id');
	if($res){
		return $res;
	}else{
		return 0;
	}

}
