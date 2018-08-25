<?php

namespace Shop\Controller;


/**
 * Class IndexController
 * 
 * @package Shop\Controller
 */
class UserController extends HomeController {
	public function _initialize() {
		parent::_initialize();
	}
	public function index() {
		$uid=is_login();
		
		//获取该用户购物车中商品数量
		$shopnum=D("Shopcart")->getNumByuid();
		$this->assign('shopnum', $shopnum);
		
		//最后一次登录时间
		$time=D("ucenter_member")->where("status=1 and id=$uid")->getField('last_login_time');
		
		//获取当前用户的购物币和通过购物消费的积分
		$uinfo=M('member')->where(array('status'=>1,'uid'=>$uid))->field('realname,hascp,hasshop')->find();
		$this->assign('uinfo',$uinfo);	
		
	
		
		$map['status'] = 2;//已发货
		$map['ispay'] = array('gt',1);
		$list = A('Order')->orderList($map);
		$this->assign('order',$list);
	
		$this->assign('time', $time);
		$this->meta_title = $uinfo['realname'].'的个人中心';
        $this->display();
	}
	
	public function order(){
		$uid=is_login();
		$order=M("order");
		if(IS_POST){
			$list=$order->where("uid='$uid' and total!='' and tag='".I('code')."'")->select();		
			$this->meta_title = '订单'.I('code');
		}else{
			/* 数据分页*/			
			
			$count=$order->where(" uid='$uid'  and total!=''")->count();
			$Page= new \Think\Page($count,5);
			$Page->setConfig('prev','上一页');
			$Page->setConfig('next','下一页');
			$Page->setConfig('first','第一页');
			$Page->setConfig('last','尾页');
			$Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
			$show= $Page->show();
			$list=$order->where("uid='$uid'  and total!=''")->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();			
			$this->meta_title = '我的所有订单';
		}
		foreach($list as $n=> $val){
			$list[$n]['id']=M("shoplist")->where('orderid=\''.$val['id'].'\'')->select();
		}
		
		$this->assign('allorder',$list);// 赋值数据集
		$this->assign('page',$show);
		
		$this->display();
	}
	
	/**
	 * 收藏商品
	 */
	public function favor(){
		if(IS_AJAX){
			$id=$_POST["id"];
			$data["id"] = $id;
			$uid=is_login();			
			$fav=M("favortable");
            $exsit=$fav->where("goodid='$id' and uid='$uid'")->getField("id");
		    if(isset($exsit)){
		     	$data["status"] = 1;
              	$data["msg"] = "已收藏过";
             	$this->ajaxReturn($data); 
		   	}
		   	else{
			   	$fav->goodid=$id;
			   	$fav->uid=$uid;
			   	$fav->add();
		   		$data["status"] = 1;
              	$data["msg"] = "收藏成功";
             	$this->ajaxReturn($data); 
		   	}		   
		}
	}
	
	/**
	 * 收藏列表
	 */
	public function favorList(){
		$table=D("favortable");
		$uid=is_login();
		$count=$table->where(" uid='$uid' ")->count();
		$Page= new \Think\Page($count,6);
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('first','第一页');
		$Page->setConfig('last','尾页');
		$Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
		$show= $Page->show();
		$favorlist=$table->where("uid='$uid' ")->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('favorlist', $favorlist);
		$this->assign('favor_count', $count);
		$this->assign('page',  $show);
		$this->meta_title = '我的收藏';	
		$this->display();
	}
	/**
	 * 取消收藏
	 */
	public function cancelfavor(){
		$goodid=I('id');
		if(empty($goodid)||!is_numeric($goodid))
			return ;		
		$uid=is_login();
		if(M('favortable')->where(array('uid'=>$uid,'goodid'=>$goodid))->delete()>0){
			$this->success('取消成功');
		}else {
			$this->error('操作失败','',1);
		}		
	}
	
	/**
	 * 显示地址管理
	 */
	public function address(){
		$uid=is_login();
		$address=M("transport"); 
		$list=$address->where("uid='$uid'")->select();
		$this->assign('address_count', count($list));
		$this->assign('address_list', $list);
		$this->meta_title = get_nickname(session ( 'user_auth.uid' )).'的地址管理'; 
		$this->display();
	}
	/**
	 * 删除一个收货地址
	 * @return [type] [description]
	 */
	public  function deleteAddress() {
		if(IS_AJAX){
			$Transport = M("transport"); // 实例化transport对象
			$uid=is_login();
			$id=$_POST["id"];
			if($Transport->where("uid='$uid' and id='$id'")->delete()){
				$data['msg'] = "删除成功";
				$data['status'] = 1;
				$this->ajaxreturn($data);
			}
			else{
				$data['msg'] = "删除失败";
				$data['status'] = 0;
				$this->ajaxreturn($data);
			}
		}
	}
	/**
	 * 设置某个地址为默认地址
	 * @return [type] [description]
	 */
	public  function shezhi() {
		if(IS_AJAX){
			$uid=is_login();
			$Transport = M("transport"); // 实例化transport对象
			$data['status'] = 0;
			$Transport->where("uid='$uid'")->save($data);
			$id=$_POST["id"];
			$result=$Transport->where("uid='$uid' and id='$id'")->setField("status",1);
			if($result){
				$data['msg'] =  "设置成功";
				$data['status'] = 1;				
			}
			else{
				$data['msg'] = "设置失败";
				$data['status'] = 0;
				
			}
			$this->ajaxreturn($data);
		}
	}
	
	/**
	 * 显示订单详细信息
	 */
	public function detail(){
		$id=I('id');
		$order=M('order')->field('addressid,pricetotal,backinfo,ispay')->where("orderid='$id'")->find();	
		$detail=M('shoplist')->where(array('tag'=>$id))->select();	
		$trans=M("transport")->field('cellphone,address,realname')->where(array('id'=>$order['addressid']))->find();
		$this->assign('trans',$trans);	
		$this->assign('detaillist',$detail);		
		$this->assign('totalprice',$order['pricetotal']);
		$this->assign('backinfo',$order['backinfo']);
		$this->assign('ispay',$order['ispay']);
		$this->meta_title = '订单详情';
		$this->display();
	}
	
	/**
	 * 取消订单
	 * @return [type] [description]
	 */
	public function cancel(){
		if(IS_POST){			
			$id= I('id');//获取orderid
			$order=M("order");
			$status=$order->where("orderid='$id'")->getField("status");
			$ispay=$order->where("orderid='$id'")->getField("ispay");
			//订单已提交或未支付直接取消
			if(($ispay==-1&&$status==1)||($ispay==1&&$status==-1)){						
				$data = array('status'=>'6','backinfo'=>'订单已关闭');
				//更新订单列表订单状态为已取消，清空取消订单操作
				if($order->where("orderid='$id'")->setField($data)) {					
					$this->ajaxReturn(array('status'=>true));					
				}
				else{
					$this->ajaxReturn(array('status'=>false));
				}
			}else{//已支付......
	
			}
		}		
	}
	
	/**
	 * 删除订单
	 * @return [type] [description]
	 */
	public function delete(){	
		if(IS_POST){
			$tag=I('id');
			$map["tag"]=$tag;
			$map["uid"]=is_login();
			if(M("order")->where($map)->delete()){
				$data=M("shoplist")->where($map)->delete();
				$pay=M('pay')->where(array('out_trade_no'=>$tag))->delete();
				if($data){
					$this->ajaxReturn(array('status'=>true));		
				}
				else{
					$this->ajaxReturn(array('status'=>false));
				}
			}	
			$this->ajaxReturn(array('status'=>false));			
		}
	}

	
}