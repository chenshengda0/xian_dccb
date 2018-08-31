<?php
namespace Admin\Controller;

/**
 * 空模块，主要用于显示404页面，请不要删除
 */
class EmptyController extends AdminController{
	public function noPage(){
		$this->title='页面错误';
		$this->display('Public/empty');
	}
}
