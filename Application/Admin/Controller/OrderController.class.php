<?php
namespace Admin\Controller;
use Common\Api\BonusApi;
/**
 * 后台订单控制器
 */
class OrderController extends AdminController {

    /**
     * 会员新下订单
     */
    public function index(){
        /* 查询条件初始化 */
       $map  = array('status' => 1);
        // 记录当前列表页的cookie
       Cookie('__forward__',$_SERVER['REQUEST_URI']);
       $this->orderList($map);
       $this->assign('action','index');
       $this->assign('status',1);
       $this->meta_title = '新下订单';
       $this->display('order');
    }

    /**
     * 已发货订单
     */
    public function  deliveryOrder(){

        /* 查询条件初始化 */
        $map  = array('status' => 2);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->orderList($map);
        $this->assign('action','deliveryOrder');
        $this->meta_title = '已发货订单';
        $this->display('order');

    }

    public function  importShop1(){
    
    	/* 查询条件初始化 */
    	$map  = array('status' => 1);
    	// 记录当前列表页的cookie
    	$list=M('shop_order')->where($map)->select();
    	$title = '待发货订单';
    	$a =		array(
    			array('orderid','订单号'),
    			array('nickname','会员昵称'),
    			array('usernumber','会员编号'),
    			array('total','金额'),
    			array('ispay','支付方式'),
    			array('createtime','时间'),
    	);
    	foreach ($list as $k => &$v){
    		$v['orderid'] = $v['orderid'];
    		$v['nickname'] = M('member')->where(array('uid'=>$v['uid']))->getField('nickname');
    		$v['usernumber'] = M('member')->where(array('uid'=>$v['uid']))->getField('usernumber');
    		$v['total'] = $v['total'];
    		$v['ispay'] = '购物币';
    		$v['createtime'] = date('Y-m-d H:i:s',$v['create_time']); 
    	}
    	$res=exportExcel($title,$a,$list);
    
    }
    public function  importShop2(){
    
    	/* 查询条件初始化 */
    	$map  = array('status' => 2);
    	// 记录当前列表页的cookie
    	$list=M('shop_order')->where($map)->select();
    	$title = '已发货订单';
    	$a =		array(
    			array('orderid','订单号'),
    			array('nickname','会员昵称'),
    			array('usernumber','会员编号'),
    			array('total','金额'),
    			array('ispay','支付方式'),
    			array('createtime','时间'),
    	);
    	foreach ($list as $k => &$v){
    		$v['orderid'] = $v['orderid'];
    		$v['nickname'] = M('member')->where(array('uid'=>$v['uid']))->getField('nickname');
    		$v['usernumber'] = M('member')->where(array('uid'=>$v['uid']))->getField('usernumber');
    		$v['total'] = $v['total'];
    		$v['ispay'] = '购物币';
    		$v['createtime'] = date('Y-m-d H:i:s',$v['create_time']);
    	}
    	$res=exportExcel($title,$a,$list);
    
    }
    public function  importShop3(){
    
    	/* 查询条件初始化 */
    	$map  = array('status' => 3);
    	// 记录当前列表页的cookie
    	$list=M('shop_order')->where($map)->select();
    	$title = '已签收订单';
    	$a =		array(
    			array('orderid','订单号'),
    			array('nickname','会员昵称'),
    			array('usernumber','会员编号'),
    			array('total','金额'),
    			array('ispay','支付方式'),
    			array('createtime','时间'),
    	);
    	foreach ($list as $k => &$v){
    		$v['orderid'] = $v['orderid'];
    		$v['nickname'] = M('member')->where(array('uid'=>$v['uid']))->getField('nickname');
    		$v['usernumber'] = M('member')->where(array('uid'=>$v['uid']))->getField('usernumber');
    		$v['total'] = $v['total'];
    		$v['ispay'] = '购物币';
    		$v['createtime'] = date('Y-m-d H:i:s',$v['create_time']);
    	}
    	$res=exportExcel($title,$a,$list);
    
    }
    /**
     * 已签收订单
     */
    public function  signforOrder(){

        /* 查询条件初始化 */
        $map  = array('status' => 3);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->orderList($map);
        $this->assign('action','signforOrder');
        $this->meta_title = '已签收订单';
        $this->display('order');

    }

    /**
     * 订单列表
     * @param array $map
     */

    public function orderList($map){
        $list = $this->lists('ShopOrder', $map,$map,'id desc');
       
        $this->assign('status',$map['status']);
        $this->assign('list', $list);
    }


    /**
     * 订单详情
     */
    public function details($id = 0){
            $action = I('action','index');
            /* 获取数据 */
            $info = M('Shop_order')->find($id);
			$list=M('shoplist')->where("orderid='$id'")->select();
			$addressid=M('Shop_order')->where(array('id'=>$id))->getField("addressid");
			$address=M('transport')->where(array('id'=>$addressid))->find();
			
 			$this->assign('address', $address);
			$this->assign('list', $list);
			$this->assign('info', $info);
			$this->assign('action',$action);
            $this->meta_title = '订单详情';
            $this->display();
    }
  /**
     * 订单发货
     * @author 烟消云散 <1010422715@qq.com>
     */
    public function send($id = 0){
        if(IS_POST){
            $Form = M('ShopOrder');
          	$uid=is_admin_login();
          	$mname = M('Manager')->where(array('mid'=>$uid))->getField('mname');

			$id=I('post.id');
			$data['tool'] = I('post.tool'); //快递公司名称
			$data['toolid'] = I('post.toolid'); //快递单号
			$data['send_name'] = I('post.send_name'); //发货人
			$data['send_contact'] = I('post.send_contact'); //发货人联系方式
			$data['send_address'] = I('post.send_address'); //发货地址
			$data['assistant'] = I('post.assistant',0); //操作员
			/* if(!$data['assistant']){
				$this->error('签名认证失败，请确保您是该公司员工');
			} */
			$data['update_time'] = NOW_TIME;
			$data['status'] = 2;
			$result=$Form->where(array('id'=>$id))->save($data);
            if($result){
                //记录行为
                $this->success('更新成功', Cookie('__forward__'));
            } else {
                $this->error('更新失败'.$id);
            }

        } else {
            /* 获取数据 */
            $info = M('ShopOrder')->find($id);
			$this->assign('info', $info);
            $this->meta_title = '订单发货';
            $this->display();
        }
    }


   /**
     * 删除订单
     */
    public function del(){
       if(IS_POST){
            $ids = I('post.id');
           
            $order = M("shop_order");
            if(is_array($ids)){
                foreach($ids as $id){
                    $order->where("id='$id'")->delete();
					$shop=M("shoplist");
					$shop->where("orderid='$id'")->delete();
                }
            }
           $this->success("删除成功！");
        }else{
            $id = I('get.id');
            $db = M("shop_order");
            $status = $db->where(array('id'=>$id))->delete();
            if ($status){
                $this->success("删除成功！");
            }else{
                $this->error("删除失败！");
            }
        }
    }
}