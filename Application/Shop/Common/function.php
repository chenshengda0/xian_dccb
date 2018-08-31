<?php 

//  /* 商品名称调用 */
// function get_good_name($id){
// 	  return M('shop')->where("id=$id")->getField('goods_name');
// }

// /**
//  * 根据用户id获取收件地址id
//  * @param  [type] $uid [description]
//  * @return [type]      [description]
//  */
// function get_addressid($uid){
// 	return M('Transport')->where("status='1' and uid='$uid'")->find();
// }

// /**
//  * 根据商品id获取商品图片
//  * @param  [type] $goodid [description]
//  * @return [type]         [description]
//  */
// function getGoodPic($goodid){
// 	$picid=M('shop')->where("id=$goodid")->getField('goods_ico');
// 	$arr=array(
// 			'id'=>$picid,
// 			'status'=>1
// 	);
// 	return __ROOT__.M('picture')->where($arr)->getField('path');
// }

// /* 商品价格调用 */
// function get_good_price($id){
// 	//$row = M('document')->getbyId($id);
// 	$row=M('shop')->where(array('id'=>$id))->getField('ms_price');
// 	return $row;
// }
// /**
//  * 根据类别id判断首页是否有推荐的类别广告 *
//  * @param unknown $adid 一级栏目ID
//  * @return string
//  */
// function getAd($adid) {
// 	$ad = M ( 'ad' )->where ( array (
// 			'status' => 1,//启用状态
// 			'ypid' => $adid,
// 			'place'=>1,//放置的位置
// 			'mark'=>-1
// 	) )->order ( 'update_time desc' )->find ();
// 	if (! empty ( $ad )) {
// 		$html = "<div class='index_adv01 mar_auto mar30'>
// 				<a href='" . get_nav_url ( $ad ['url'] ) . "' target='_blank'>
// 				<img src='" . get_cover ( $ad ['icon'], 'path' ) . "' width='1200' height='118'>
// 				</a></div>";
// 	}
// 	return $html;
// }

// /**
//  * 根据不同区域获取广告值------wpf------20150604
//  * 首先判断是否设置了url，若设置了就判断是否是全路径，若没有设置url就走到搜索页，（后期再加上活动商品id）
//  * @param unknown $mark
//  */
// function getAd_02($mark){
// 	$ad = M ( 'ad' )->where ( array (
// 			'status' => 1,//启用状态			
// 			'place'=>1,//放置的位置
// 			'mark'=>$mark
// 	) )->order ( 'update_time desc' )->find ();		
// 	if(!empty($ad['url'])){
// 		//if(is_http_url($ad['url'])){
// 			return $html="<a href='".get_nav_url ( $ad ['url'] )."' target='_blank'><img src='".get_cover ( $ad ['icon'], 'path' )."' width='130' height='130'/></a>";
// 		//}
// 	}	
// 	return $html="<a href='".U('Search/index',array('searchtext'=>$ad['keywords'] ))."' target='_blank'><img src='".get_cover ( $ad ['icon'], 'path' )."' width='130' height='130'/></a>";
// } 

// function get_nav_active_02($url) {
// 	if(stripos( $_SERVER ['QUERY_STRING'],$url)!==false){
// 		return 1;
// 	}
// 	return 0;
// }



// /**
//  * 判断是否为http或https或#
//  * @param unknown $url
//  * @return boolean
//  */
// function is_http_url($url) {
// 	switch ($url) {
// 		case 'http://' === substr ( $url, 0, 7 ) :
// 		case 'https://' === substr ( $url, 0, 8 ) :
// 		case '#' === substr ( $url, 0, 1 ) :
// 			return true;		
// 	}
// 	return false;	
// }

// function getcolor($k) {
// 	$colorArr = Array (
// 			'index_firstfloor',
// 			'index_secondfloor',
// 			'index_thirdfloor',
// 			'index_fourthfloor',
// 			'index_fifthfloor',
// 			'index_sixthfloor',
// 			'index_seventhfloor',
// 			'index_eighthfloor'
// 	);
// 	return $colorArr [$k];
// }
// function get_location_name($id) {
// 	return get_location ( $id, 'title' );
// }