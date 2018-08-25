<?php

namespace Admin\Controller;

/**
 * 后台订单控制器
 * 
 * @author 烟消云散 <1010422715@qq.com>
 */
class AdController extends AdminController {
	
	/**
	 * 广告管理
	 * author 烟消云散 <1010422715@qq.com>
	 */
	public function index() {
		/* 查询条件初始化 */
		if (IS_GET) {
			$title = trim ( I ( 'get.title' ) );
			$map ['title'] = array (
					'like',
					"%{$title}%" 
			);
			$list = M ( "Ad" )->where ( $map )->field ( true )->order ( 'update_time desc' )->select ();
		} else {
			//------wpf------这里都没啥用
			$list = $this->lists ( 'Ad', $map, 'update_time desc' );
		}
	
		$this->assign ( 'list', $list );
		// 记录当前列表页的cookie
		Cookie ( '__forward__', $_SERVER ['REQUEST_URI'] );		
		$this->meta_title = '首页分类推广管理';
		$this->display ();
	}
	
	/* 编辑分类 */
	public function edit($id = null, $pid = 0) {
		$ad = D ( 'ad' );
		// 获取一级分类
		$list = M ( 'shop_category' )->field ( 'id,title' )->where ( array (
				'status' => 1,
				pid => 0 
		) )->select ();
		$this->assign ( 'list', $list );
		
		if (IS_POST) { // 提交表单
			if($_POST['icon']){
				M('shop_category')->where(array('id'=>$_POST['ypid']))->setField('icon',$_POST['icon']);
			}
			if (false !== $ad->update ()) {
				$this->success ( '编辑成功！', U ( 'index' ) );
			} else {
				$error = $ad->getError ();
				$this->error ( empty ( $error ) ? '未知错误！' : $error );
			}
		} else {
			$cate = '';
			if ($pid) {
				
				$cate = $ad->info ( $pid, 'id,name,title,status' );
				if (! ($cate && 1 == $cate ['status'])) {
					$this->error ( '指定的上级分类不存在或被禁用！' );
				}
			}
			
			/* 获取分类信息 */
			$info = $id ? $ad->info ( $id ) : '';
			
			$this->assign ( 'info', $info );
			$this->assign ( 'category', $cate );
			$this->meta_title = '编辑推广内容';
			$this->display ();
		}
	}
	
	/* 新增分类 */
	public function add() {
		$ad = D ( 'ad' );
		// 获取一级分类
		$list = M ( 'shop_category' )->field ( 'id,title' )->where ( array (
				'status' => 1,
				pid => 0 
		) )->select ();
		$this->assign ( 'list', $list );
		// 提交表单
		if (IS_POST) {
			if (false !== $ad->update ()) {
				$this->success ( '新增成功！', U ( 'index' ) );
			} else {
				$error = $ad->getError ();
				$this->error ( empty ( $error ) ? '未知错误！' : $error );
			}
		} else {
			$cate = array ();
			if ($pid) {				
				$cate = $ad->info ( $pid, 'id,title,status' );
				if (! ($cate && 1 == $cate ['status'])) {
					$this->error ( '指定的上级分类不存在或被禁用！' );
				}
			}
			
			/* 获取优惠券信息 */
			$this->assign ( 'info', null );
			$this->assign ( 'category', $cate );
			$this->meta_title = '新增推广内容';
			$this->display ();
		}
	}
	public function del() {
		if (IS_POST) {
			$ids = I ( 'post.id' );
			$order = M ( "ad" );
			if (is_array ( $ids )) {
				foreach ( $ids as $id ) {
					$order->where ( "id='$id'" )->delete ();
				}
			}
			$this->success ( "删除成功！" );
		} else {
			$id = I ( 'get.id' );
			$db = M ( "ad" );
			$status = $db->where ( "id='$id'" )->delete ();
			if ($status) {
				$this->success ( "删除成功！" );
			} else {
				$this->error ( "删除失败！" );
			}
		}
	}
}