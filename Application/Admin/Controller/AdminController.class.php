<?php
namespace Admin\Controller;

use Admin\Model\AuthRuleModel;
use Admin\Model\AuthGroupModel;
use Common\Controller\CommonController;
/**
 * 后台首页控制器
 */
class AdminController extends CommonController {
	
	private $cate_id        =   null; //文档分类id

    /**
     * 后台控制器初始化
     */
    protected function _initialize(){
     
    	parent::_initialize();
    
        // 获取当前用户ID
        define('UID',is_admin_login());
        if( !UID ){// 还没登录 跳转到登录页面
            $this->redirect('Public/login');
        }

        // 是否是超级管理员
        define('IS_ROOT',   is_administrator(UID));
        if(!IS_ROOT && C('ADMIN_ALLOW_IP')){
            // 检查IP地址访问
            if(!in_array(get_client_ip(),explode(',',C('ADMIN_ALLOW_IP')))){
                $this->error('403:禁止访问');
            }
        }
        // 检测访问权限
        $access =   $this->accessControl();
        if ( $access === false ) {
            $this->error('403:禁止访问');
        }elseif( $access === null ){
            $dynamic        =   $this->checkDynamic();//检测分类栏目有关的各项动态权限
            if( $dynamic === null ){
                //检测非动态权限
                $rule  = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
                if ( !$this->checkRule($rule,array('in','1,2')) ){
                    $this->error('未授权访问!');
                }
            }elseif( $dynamic === false ){
                $this->error('未授权访问!');
            }
        }
        
        if(C('DEFAULT_THEME')=='default'){
        	$this->assign('__MENU__', $this->getMenus());
        	//$id = I('get.menuid');
        	//$this->assign('_nav',array_reverse($this->_nav($id)));
        }else{
        	$liu = M('liuyan')->where(array('status'=>0,'touser'=>0))->count();
        	$this->assign('liu',$liu);
        	$this->assign('__MENU__', $this->getNewMenus());
        	$this->getMenu();
        }
      
        
    }
    
    private function _nav($id){
    	$menu = M('Menu');
    	static $nav = array();
    	$info = $menu->where(array('id'=>$id))->field('url,pid,title')->find();
    	$nav[] = array('id'=>$id,'title'=>$info['title'],'url'=>$info['url']);
    	if($info['pid']){
    		$this->_nav($info['pid']);
    	}
    	return $nav;
    }

    /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function checkRule($rule, $type=AuthRuleModel::RULE_URL, $mode='url'){
        if(IS_ROOT){
            return true;//管理员允许访问任何页面
        }
        static $Auth    =   null;
        if (!$Auth) {
            $Auth       =   new \Think\Auth();
        }
        if(!$Auth->check($rule,UID,$type,$mode)){
            return false;
        }
        return true;
    }

    /**
     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则会进入checkRule根据节点授权判断权限
     *
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    protected function checkDynamic(){
        if(IS_ROOT){
            return true;//管理员允许访问任何页面
        }
        return null;//不明,需checkRule
    }


    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function accessControl(){
        if(IS_ROOT){
            return true;//管理员允许访问任何页面
        }
		$allow = C('ALLOW_VISIT');
		$deny  = C('DENY_VISIT');
		$check = strtolower(CONTROLLER_NAME.'/'.ACTION_NAME);
        if ( !empty($deny)  && in_array_case($check,$deny) ) {
            return false;//非超管禁止访问deny中的方法
        }
        if ( !empty($allow) && in_array_case($check,$allow) ) {
            return true;
        }
        return null;//需要检测节点权限
    }

    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     *
     * @param string $model 模型名称,供M函数使用的参数
     * @param array  $data  修改的数据
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    final protected function editRow ( $model ,$data, $where , $msg ){
        $id    = array_unique((array)I('id',0));
        $id    = is_array($id) ? implode(',',$id) : $id;
       	if($model!='Manager'){
       		$where = array_merge( array('id' => array('in', $id )) ,(array)$where );
       	}
        $msg   = array_merge( array( 'success'=>'操作成功！', 'error'=>'操作失败！', 'url'=>'' ,'ajax'=>IS_AJAX) , (array)$msg );
        if( M($model)->where($where)->save($data)!==false ) {
            $this->success($msg['success'],$msg['url'],$msg['ajax']);
        }else{
            $this->error($msg['error'],$msg['url'],$msg['ajax']);
        }
    }

    /**
     * 禁用条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的 where()方法的参数
     * @param array  $msg   执行正确和错误的消息,可以设置四个元素 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    protected function forbid ( $model , $where = array() , $msg = array( 'success'=>'状态禁用成功！', 'error'=>'状态禁用失败！')){
        $data    =  array('status' => 0);
        $this->editRow( $model , $data, $where, $msg);
    }

    /**
     * 恢复条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    protected function resume (  $model , $where = array() , $msg = array( 'success'=>'状态恢复成功！', 'error'=>'状态恢复失败！')){
        $data    =  array('status' => 1);
        $this->editRow(   $model , $data, $where, $msg);
    }

    /**
     * 还原条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * @author huajie  <banhuajie@163.com>
     */
    protected function restore (  $model , $where = array() , $msg = array( 'success'=>'状态还原成功！', 'error'=>'状态还原失败！')){
        $data    = array('status' => 1);
        $where   = array_merge(array('status' => -1),$where);
        $this->editRow(   $model , $data, $where, $msg);
    }

    /**
     * 条目假删除
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    protected function delete ( $model , $where = array() , $msg = array( 'success'=>'删除成功！', 'error'=>'删除失败！')) {
        $data['status']         =   -1;
        $data['update_time']    =   NOW_TIME;
        $this->editRow(   $model , $data, $where, $msg);
    }

    /**
     * 设置一条或者多条数据的状态
     */
    public function setStatus($Model=CONTROLLER_NAME){

        $ids    =   I('request.ids');
        $status =   I('request.status');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }

        $map['id'] = array('in',$ids);
        switch ($status){
            case -1 :
                $this->delete($Model, $map, array('success'=>'删除成功','error'=>'删除失败'));
                break;
            case 0  :
                $this->forbid($Model, $map, array('success'=>'禁用成功','error'=>'禁用失败'));
                break;
            case 1  :
                $this->resume($Model, $map, array('success'=>'启用成功','error'=>'启用失败'));
                break;
            default :
                $this->error('参数错误');
                break;
        }
    }
    
    protected function writeMessage($data){
    	$data['fromuserid'] = 0;
    	$data['create_time'] = time();
    	$data['status'] = 0;
    	M('liuyan')->add($data);
    }

    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final public function getMenus($controller=CONTROLLER_NAME){
        // $menus  =   session('ADMIN_MENU_LIST'.$controller);
        if(empty($menus)){
            // 获取主菜单
            $where['pid']   =   0;
            $where['hide']  =   0;
            if(!C('DEVELOP_MODE')){ // 是否开发者模式
                $where['is_dev']    =   0;
            }
            $menus['main']  =   M('Menu')->where($where)->order('sort asc')->select();

            $menus['child'] = array(); //设置子节点

            //高亮主菜单
            $current = M('Menu')->where("url like '%{$controller}/".ACTION_NAME."%'")->field('id')->find();
            if($current){
                $nav = D('Menu')->getPath($current['id']);
                $nav_first_title = $nav[0]['title'];

                foreach ($menus['main'] as $key => $item) {
                    if (!is_array($item) || empty($item['title']) || empty($item['url']) ) {
                        $this->error('控制器基类$menus属性元素配置有误');
                    }
                    if( stripos($item['url'],MODULE_NAME)!==0 ){
                        $item['url'] = MODULE_NAME.'/'.$item['url'];
                    }
                    // 判断主菜单权限
                    if ( !IS_ROOT && !$this->checkRule($item['url'],AuthRuleModel::RULE_MAIN,null) ) {
                        unset($menus['main'][$key]);
                        continue;//继续循环
                    }

                    // 获取当前主菜单的子菜单项
                    if($item['title'] == $nav_first_title){
                        $menus['main'][$key]['class']='current';
                        //生成child树
                        $groups = M('Menu')->where("pid = {$item['id']}")->distinct(true)->field("`group`")->select();
                        if($groups){
                            $groups = array_column($groups, 'group');
                        }else{
                            $groups =   array();
                        }

                        //获取二级分类的合法url
                        $where          =   array();
                        $where['pid']   =   $item['id'];
                        $where['hide']  =   0;
                        if(!C('DEVELOP_MODE')){ // 是否开发者模式
                            $where['is_dev']    =   0;
                        }
                        $second_urls = M('Menu')->where($where)->getField('id,url');

                        if(!IS_ROOT){
                            // 检测菜单权限
                            $to_check_urls = array();
                            foreach ($second_urls as $key=>$to_check_url) {
                                if( stripos($to_check_url,MODULE_NAME)!==0 ){
                                    $rule = MODULE_NAME.'/'.$to_check_url;
                                }else{
                                    $rule = $to_check_url;
                                }
                                if($this->checkRule($rule, AuthRuleModel::RULE_URL,null))
                                    $to_check_urls[] = $to_check_url;
                            }
                        }
                        // 按照分组生成子菜单树
                        foreach ($groups as $g) {
                            $map = array('group'=>$g);
                            if(isset($to_check_urls)){
                                if(empty($to_check_urls)){
                                    // 没有任何权限
                                    continue;
                                }else{
                                    $map['url'] = array('in', $to_check_urls);
                                }
                            }
                            $map['pid'] =   $item['id'];
                            $map['hide']    =   0;
                            if(!C('DEVELOP_MODE')){ // 是否开发者模式
                                $map['is_dev']  =   0;
                            }
                            $menuList = M('Menu')->where($map)->field('id,pid,title,url,tip')->order('sort asc')->select();
                            $menus['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
                        }
                        if($menus['child'] === array()){
                            //$this->error('主菜单下缺少子菜单，请去系统=》后台菜单管理里添加');
                        }
                    }
                }
            }
            // session('ADMIN_MENU_LIST'.$controller,$menus);
        }
        return $menus;
    }
    
    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final public function getNewMenus($controller=CONTROLLER_NAME){
    	// $menus  =   session('ADMIN_MENU_LIST'.$controller);
    	if(empty($menus)){
    		// 获取主菜单
    		$where['pid']   =   0;
    		$where['hide']  =   0;
    		if(!C('DEVELOP_MODE')){ // 是否开发者模式
    			$where['is_dev']    =   0;
    		}
    		$menus['main']  =   M('Menu')->where($where)->order('sort asc')->select();
    
    		//高亮主菜单
    		$current = M('Menu')->where("url like '%{$controller}/".ACTION_NAME."%'")->field('id')->find();
    		if($current){
    			$nav = D('Menu')->getPath($current['id']);
    			$nav_first_title = $nav[0]['title'];
    
    			foreach ($menus['main'] as $key => &$item) {
    				if (!is_array($item) || empty($item['title']) || empty($item['url']) ) {
    					$this->error('控制器基类$menus属性元素配置有误');
    				}
    				if( stripos($item['url'],MODULE_NAME)!==0 ){
    					$item['url'] = MODULE_NAME.'/'.$item['url'];
    				}
    				// 判断主菜单权限
    				if ( !IS_ROOT && !$this->checkRule($item['url'],AuthRuleModel::RULE_MAIN,null) ) {
    					unset($menus['main'][$key]);
    					continue;//继续循环
    				}
    
    				// 获取当前主菜单的子菜单项
    				//  if($item['title'] == $nav_first_title){
    				$menus['main'][$key]['class']='current';
    
    				//获取二级分类的合法url
    				$where          =   array();
    				$where['pid']   =   $item['id'];
    				$where['hide']  =   0;
    				if(!C('DEVELOP_MODE')){ // 是否开发者模式
    					$where['is_dev']    =   0;
    				}
    				$second_urls = M('Menu')->where($where)->getField('id,url');
    
    				if(!IS_ROOT){
    					// 检测菜单权限
    					$to_check_urls = array();
    					foreach ($second_urls as $key=>$to_check_url) {
    						if( stripos($to_check_url,MODULE_NAME)!==0 ){
    							$rule = MODULE_NAME.'/'.$to_check_url;
    						}else{
    							$rule = $to_check_url;
    						}
    						if($this->checkRule($rule, AuthRuleModel::RULE_URL,null))
    							$to_check_urls[] = $to_check_url;
    					}
    				}
    				// 按照分组生成子菜单树
    				if(isset($to_check_urls)){
    					if(empty($to_check_urls)){
    						// 没有任何权限
    						continue;
    					}else{
    						$map['url'] = array('in', $to_check_urls);
    					}
    				}
    				$map['pid'] =   $item['id'];
    				$map['hide']    =   0;
    				if(!C('DEVELOP_MODE')){ // 是否开发者模式
    					$map['is_dev']  =   0;
    				}
    				$menuList = M('Menu')->where($map)->field('id,pid,title,url,tip,icon,is_shortcut,shortcut')->order('sort asc')->select();
    				$item['children'] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
    				//  dump($item);
    			}
    			//}
    		}
    		// session('ADMIN_MENU_LIST'.$controller,$menus);
    	}
    	return $menus;
    }
    
    /**
     * 显示左边菜单，进行权限控制
     * @author huajie <banhuajie@163.com>
     */
    protected function getMenu(){
    	//获取动态分类
    	$cate_auth  =   AuthGroupModel::getAuthCategories(UID);	//获取当前用户所有的内容权限节点
    	$cate_auth  =   $cate_auth == null ? array() : $cate_auth;
    	$cate       =   M('Category')->where(array('status'=>1))->field('id,title,pid,allow_publish')->order('pid,sort')->select();
    	 
    
    	//没有权限的分类则不显示
    	if(!IS_ROOT){
    		foreach ($cate as $key=>$value){
    			if(!in_array($value['id'], $cate_auth)){
    				unset($cate[$key]);
    			}
    		}
    	}
    
    	$cate           =   list_to_tree($cate);	//生成分类树
    
    	//获取分类id
    	$cate_id        =   I('param.cate_id');
    	$this->cate_id  =   $cate_id;
    
    	//是否展开分类
    	$hide_cate = false;
    	if(ACTION_NAME != 'recycle' && ACTION_NAME != 'draftbox' && ACTION_NAME != 'mydocument'){
    		$hide_cate  =   true;
    	}
    
    	//生成每个分类的url
    	foreach ($cate as $key=>&$value){
    		$value['url']   =   'Article/index?cate_id='.$value['id'];
    		if($cate_id == $value['id'] && $hide_cate){
    			$value['current'] = true;
    		}else{
    			$value['current'] = false;
    		}
    		if(!empty($value['_child'])){
    			$is_child = false;
    			foreach ($value['_child'] as $ka=>&$va){
    				$va['url']      =   'Article/index?cate_id='.$va['id'];
    				if(!empty($va['_child'])){
    					foreach ($va['_child'] as $k=>&$v){
    						$v['url']   =   'Article/index?cate_id='.$v['id'];
    						$v['pid']   =   $va['id'];
    						$is_child = $v['id'] == $cate_id ? true : false;
    					}
    				}
    				//展开子分类的父分类
    				if($va['id'] == $cate_id || $is_child){
    					$is_child = false;
    					if($hide_cate){
    						$value['current']   =   true;
    						$va['current']      =   true;
    					}else{
    						$value['current'] 	= 	false;
    						$va['current']      =   false;
    					}
    				}else{
    					$va['current']      =   false;
    				}
    			}
    		}
    	}
    
    	 
    	$this->assign('nodes',      $cate);
    	$this->assign('cate_id',    $this->cate_id);
    	//获取面包屑信息
    	//  $nav = get_parent_category($cate_id);
    	//   $this->assign('rightNav',   $nav);
    
    
    	//获取回收站权限
    	//  $show_recycle = $this->checkRule('Admin/article/recycle');
    	// $this->assign('show_recycle', IS_ROOT || $show_recycle);
    	//获取草稿箱权限
    	// $this->assign('show_draftbox', C('OPEN_DRAFTBOX'));
    }

    /**
     * 返回后台节点数据
     * @param boolean $tree    是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @retrun array
     *
     * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     *
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    final protected function returnNodes($tree = true){
        static $tree_nodes = array();
        if ( $tree && !empty($tree_nodes[(int)$tree]) ) {
            return $tree_nodes[$tree];
        }
        if((int)$tree){
            $list = M('Menu')->field('id,pid,title,url,tip,hide')->order('sort asc')->select();
            foreach ($list as $key => $value) {
                if( stripos($value['url'],MODULE_NAME)!==0 ){
                    $list[$key]['url'] = MODULE_NAME.'/'.$value['url'];
                }
            }
            $nodes = list_to_tree($list,$pk='id',$pid='pid',$child='operator',$root=0);
            foreach ($nodes as $key => $value) {
                if(!empty($value['operator'])){
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }
        }else{
            $nodes = M('Menu')->field('title,url,tip,pid')->order('sort asc')->select();
            foreach ($nodes as $key => $value) {
                if( stripos($value['url'],MODULE_NAME)!==0 ){
                    $nodes[$key]['url'] = MODULE_NAME.'/'.$value['url'];
                }
            }
        }
        $tree_nodes[(int)$tree]   = $nodes;
        return $nodes;
    }
    
    /* 空操作，用于输出404页面 */
    public function _empty(){
    	$this->redirect('Empty/noPage');
    }

}
