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
     * 后台钱包管理
     * @return none
     */
    public function wallet_list(){


        $list  =   M("wallet")->field(true)->order('id asc')->select();

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
			$data['add_time'] = time();
            $id = $Menu->add($data);
               
                if($id){
                    // S('DB_CONFIG_DATA',null);
                    //记录行为
                    action_log('update_wallet', 'wallet', $id, UID);
                    $this->success('新增成功',Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            
        } else {
            $this->assign('info',array('pid'=>I('pid')));
            $menus = M('Menu')->field(true)->select();
           
            $this->assign('Menus', $menus);

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
                    $this->success('更新成功',Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('wallet')->field(true)->find($id);
           
            $this->assign('info', $info);
            $this->meta_title = '编辑支付钱包设置';
            $this->display();
        }
    }

    /**
     * 删除支付钱包
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('wallet')->where($map)->delete()){
            // S('DB_CONFIG_DATA',null);
            //记录行为
            action_log('delete_wallet', 'wallet', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
	
	
	 /**
     * 后台钱包管理
     * @return none
     */
    public function recharge_list(){


       
		$model= M('ciex_recharge');
		$map ='';
		$maps = '';
		$order= 'id desc';
		$field = '*';
		$list = $this->lists($model,$map,$maps,$order,$field);

		$status = array('-1'=>'无效','0'=>'未支付','1'=>'已支付','2'=>'已完成');
		
		 if($list) {
			
			 foreach($list as $key=>$vo){
                $list[$key]['status'] = $status[$vo['status']];
				$list[$key]['add_time'] = date('Y-m-d H:i',$vo['add_time']);
				$list[$key]['pay_time'] = $vo['pay_time'] ? date('Y-m-d H:i',$vo['pay_time']) : '未支付';
              
            }
           
        }
		
        $this->assign('list',$list);
		

        $this->display();
    }
	
	
	 /**
     * 编辑配置
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function recharge($id = 0){
        if(IS_POST){
			$id = I('id');
            $Menu = M('ciex_recharge');
			$data['note'] = I('note');
			$data['status'] = I('status');
			$data['recharge_time'] = time();
            $id = $Menu->where(['id'=>$id])->save($data);
           
                if($id){
                    // S('DB_CONFIG_DATA',null);
                    //记录行为
                    action_log('update_ciex_recharge', 'ciex_recharge', $data['id'], UID);
                    $this->success('更新成功',U('/Admin/wallet/recharge_list'));
                } else {
                    $this->error('更新失败');
                }
            
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('ciex_recharge')->field(true)->find($id);
           
            $this->assign('info', $info);
            $this->meta_title = '用户充币';
            $this->display();
        }
    }
	
	
	  /**
     * 删除支付钱包
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function rechargeDel(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('ciex_recharge')->where($map)->delete()){
            // S('DB_CONFIG_DATA',null);
            //记录行为
            action_log('del_ciex_recharge', 'ciex_recharge', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }


   
}
