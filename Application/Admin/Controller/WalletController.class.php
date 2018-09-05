<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台配置控制器
 * @author yangweijie <yangweijiester@gmail.com>
 */
class WalletController extends AdminController {

    /**
     * 后台菜单首页
     * @return none
     */
    public function wallet_list(){


        $list  =   M("wallet")->where($map)->field(true)->order('id asc')->select();

        $this->assign('list',$list);

        $this->display();
    }

    /**
     * 新增菜单
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function add(){
        if(IS_POST){
            $Menu = M('wallet');
			$data['name'] = I('name');
			$data['address'] = I('address');
            $id = $Menu->add($data);
               
                if($id){
                    // S('DB_CONFIG_DATA',null);
                    //记录行为
                    action_log('update_wallet', 'wallet', $id, UID);
                    $this->success('新增成功');
                } else {
                    $this->error('新增失败');
                }
            
        } else {
            $this->assign('info',array('pid'=>I('pid')));
            $menus = M('Menu')->field(true)->select();
            $menus = D('Common/Tree')->toFormatTree($menus);
            $menus = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $menus);
            $this->assign('Menus', $menus);
            $this->meta_title = '新增菜单';
            $this->display('edit');
        }
    }

    /**
     * 编辑配置
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function edit($id = 0){
        if(IS_POST){
			$id = I('id');
            $Menu = M('wallet');
			$data['name'] = I('name');
			$data['address'] = I('address');
            $id = $Menu->where(['id'=>$id])->save($data);
           
                if($id){
                    // S('DB_CONFIG_DATA',null);
                    //记录行为
                    action_log('update_wallet', 'wallet', $data['id'], UID);
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('wallet')->field(true)->find($id);
           
            $this->assign('info', $info);
            $this->meta_title = '编辑后台菜单';
            $this->display();
        }
    }

    /**
     * 删除后台菜单
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Menu')->where($map)->delete()){
            // S('DB_CONFIG_DATA',null);
            //记录行为
            action_log('update_menu', 'Menu', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

   
}
