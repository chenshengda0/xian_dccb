<?php
namespace Shop\Controller;

use Common\Controller\CommonController;

class HomeController extends CommonController {
		
 	public function _initialize(){
    	parent::_initialize();
    	
        if(false && !C('WEB_SITE_CLOSE')){
            $this->error(C('NO_BODY_TLE'));
        }
        $this->checkLogin();
        $this->pulicInfo();
        $this->footerMenu();
        $this->shopSub();
        $this->usercart();
        
        $param['class'] = 'selt selt1';
        $this->assign('param',$param);
       
    }

	/* 用户登录检测 */
	protected  function checkLogin(){
		//判断该操作是否不需要验证
		
		//不需验证的控制器列表
		$allow_controller = array();
		
		//不需要验证的动作列表
		$allow_action = array();
		
		//开始判断，得到当前控制器名全局变量$controller和动作名全局变量$action
		if(isset($allow_controller[CONTROLLER_NAME])){
			return ;
		}elseif(isset($allow_action[CONTROLLER_NAME])&& in_array(ACTION_NAME, $allow_action[CONTROLLER_NAME])){
			//不需要验证
			return ;
		}else{
			/* 用户登录检测 */
			is_login() || $this->error('您还没有登录，请先登录！', U('Home/Index/index'));
		}

	}
	
	/**
	 * 公共信息
	 */
	public function pulicInfo(){
		
		//获取商城配置
		$webconfig=M('webconfig')->find(1);
		if(!empty($webconfig)){
			$this->assign('webtitle',$webconfig['web_name']);
			$this->assign('keywords',$webconfig['web_keys']);
			$this->assign('description',$webconfig['web_description']);
			$this->assign('weblogo',get_cover($webconfig['web_logo'],'path'));
			$this->assign('copyright',$webconfig['web_copyright']);
		}
		//获取qq客服配置
		$qqlist=C('SERVICE_QQ');
		if(!empty($qqlist)){
			$this->assign('qqlist',$qqlist);
		}
		
		//获取客服
		$phlist=C('SERVICE_PHONE');
		if(!empty($phlist)){
			$this->assign('phlist',$phlist);
		}
			
		//获取导航菜单
		$tree = D('shopCategory')->where(array('status'=>1,'pid'=>0))->select();

		foreach ($tree as $vo) {
			
			$menu = array('id'=>$vo['id'], 'title' => $vo['title'], 'href' => U('shop/index/goods', array('id' => $vo['id'])));
			
			$channelList[] = $menu;
		}
		
		$this->assign('channelList',$channelList);
		//获取关于我们内容页
		$map['name'] = 'about';
		$map['status'] = 1;
		$id = M('category')->where($map)->getField('id');
		$guanyu=M('document')->field('id,title')->where(array('status'=>1,'category_id'=>$id))->select();
		$this->assign('guanyu',$guanyu);
	}
	
	public function getCate($pid){
		$channelList  = S('channelList'.$pid);
		if(!$channelList){
			//获取导航菜单
			$tree = D('shopCategory')->where(array('status'=>1,'pid'=>$pid))->select();
			if(!empty($tree)){
				foreach ($tree as $vo) {
					$menu = array('id'=>$vo['id'], 'title' => $vo['title'], 'href' => U('shop/index/goods', array('id' => $vo['id'])));
					$channelList[] = $menu;
				}
				S('channelList'.$pid,$channelList);
			}
		}
		$this->ajaxReturn($channelList);
	}
	
	/**
	 * 商城底部菜单
	 */
	public function footerMenu(){
		$map['name']='help';
		$id = M('category')->where($map)->getField('id');
		unset($map['name']);
		$map['pid']=$id;
		$ids=M('category')->field('id,title')->where($map)->select();
		
		foreach($ids as $n=> $v){
			$v['children']=M('document')->field('id,title')->where(array('category_id'=>$v['id'],'status'=>1))->limit(4)->select();
			$ids[$n]=$v;
		}
		if(!empty($ids)){
			$this->assign('helplink',$ids[0]['children'][0]['id']);
		}
		
		$this->assign('ids',$ids);
	}
	
	/**
	 * 获取商品分类导航
	 */
	public function shopSub(){
		//获取商品菜单
		$tree = D('shopCategory')->getTree();
		$this->assign('tree', $tree);
		foreach ($tree as $category) {
			$menu = array('tab' => 'category_' . $category['id'],'id'=>$category['id'], 'title' => $category['title'], 'href' => U('shop/index/goods', array('id' => $category['id'])));
			if ($category['_']) {
				//$menu['children'][] = array( 'title' => '全部', 'href' => U('shop/index/goods', array('category_id' => $category['id'])));
				foreach ($category['_'] as $child)
					$menu['children'][] = array( 'title' => $child['title'], 'id'=>$child['id'],'href' => U('shop/index/goods', array('id' => $child['id'])));
			}
			$menu_list[] = $menu;
		}
		$this->assign('sub_menu', $menu_list);
	}
	
	/**
	 * 购物车
	 * @return unknown
	 */
	protected function usercart(){
		$cart=D("shopcart");
		$result= $cart->getcart();
		$total = $cart->getPriceByuid();
		$cart_num = $cart->getNumByuid();
		$this->assign('total',$total);
		$this->assign('cart_num',$cart_num);
		$this->assign('cartlist',$result);
	}
}