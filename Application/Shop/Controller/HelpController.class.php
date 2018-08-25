<?php

namespace Shop\Controller;

/**
 * Class IndexController
 * 
 * @package Shop\Controller
 */
class HelpController extends HomeController {
	public function _initialize() {				
		parent::initialize();
	}
	public function index() {
		$id=I('id');		
		if(empty($id)||!is_numeric($id)){
			$this->error('输入的格式不正确！');
		}
		$title=M('document')->where(array('id'=>$id))->getField('title');
		$content=M('document_article')->where(array('id'=>$id))->getField('content');
		if(empty($title)){
			$this->error('查找的内容不存在！');
		}
		$this->assign('title',$title);
		$this->assign('content',$content);
		$this->meta_title=$title;
		$this->display();
	}
}