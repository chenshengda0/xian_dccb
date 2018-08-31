<?php
namespace Home\Controller;

class ArticleController extends HomeController{	
	/**
	 * 新闻公告
	 */
	public function news(){
		$name =array('pnews','cnews');
		$this->newsList($name);
		$this->assign('title', "新闻公告");
		$this->assign('name','news');
		$this->display('newsList');
	}
	
	/**
	 * 学习资料
	 */
	public function study(){
		$name =array('study');
		$this->newsList($name);
		$this->assign('title', "学习资料");
		$this->assign('name','study');
		$this->display('newsList');
	}
	
	/**
	 * 新闻列表
	 */
	private function newsList($name){
		$category = M('category');
		$map['name'] = array('in',$name);
		$id = $category->where($map)->getField('id',true);
		unset($map);
		$map['category_id'] = array('in',$id);
		$map['status'] = 1;
		$list = $this->lists('Document',$map,$map,'');
		$this->assign('document_list',$list);
	}
	/**
	 * 新闻信息展示
	 */
	public function showNews(){
		
      
      	$category = M('category');
      	$document = M('document');
    	$map['name'] = 'pnews';
    	$map['status'] = 1;
    	$id = $category->where($map)->getField('id');
    	unset($map);
      
    	$map['category_id']=$id;
    	$map['status']=1;
       
    	$instro = $document->join('zx_document_article ON zx_document_article.id = zx_document.id')->where($map)->select();
      	
      	$this->assign('instro',$instro);
      	$this->display();
		
	}
}

