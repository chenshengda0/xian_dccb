<?php
namespace Shop\Controller;

class ShopcartController extends HomeController {
	
	/**
	 * 商城初始化
	 * 
	 * @author 郑钟良<zzl@ourstu.com>
	 */
	public function _initialize() {
		parent::_initialize();
	}
	/**
	 * 初始化购物车页面
	 * @return [type] [description]
	 */
	public function index() {
		//假如用户登录		
		$cart=D("Shopcart");
		$sum= $cart->getNumByuid();// 查询购物车中商品的个数
		$price=$cart->getPriceByuid(); // 购物车中商品的总金额	
		$count=$cart->getCnt(); //查询购物车中商品的种类 
				
     	$result= $cart->getcart();//获取购物车数据  
     	$this->assign('sqlcart',$result);
     	
     	$this->assign('sum',$sum);
     	$this->assign('totalprice',$price);
     	$this->assign('count',$count); 			
		$this->meta_title = '我的购物车';
		$this->display ();
	}

	/**
	 * ajax处理减少商品
	 * @return [type] [description]
	 */
	public function decNumByuid(){
		if(IS_POST){
			$cart=D("shopcart");
			$sort=$_POST['sort'];
			$result= $cart->dec($sort);
			$count=$cart->getCnt(); /*查询购物车中商品的种类 */
			$sum= $cart->getNumByuid();/* 查询购物车中商品的个数*/
			$price=$cart->getPriceByuid(); /* 购物车中商品的总金额*/
			if($result){
				$data['new'] ='新个数'.$result;
				$data['count'] = $count;
				$data['status'] = 1;
				$data['price'] =$price;
				$data['sum'] = $sum;
				$data['msg'] = '处理成功';
				$this->ajaxReturn($data);
			}
		}	
	}

	/**
	 * ajax处理增加商品
	 * @return [type] [description]
	 */
	public function incNumByuid(){
		if(IS_POST){
			$sort=$_POST['sort'];
			$cart=D("Shopcart");
	    	$result= $cart->inc($sort);
		    $count=$cart->getCnt(); /*查询购物车中商品的种类 */
		    $sum= $cart->getNumByuid();/* 查询购物车中商品的个数*/
		    $price=$cart->getPriceByuid(); /* 购物车中商品的总金额*/
			if($result){
				$data['new'] ='新个数'.$result;
				$data['count'] = $count;
				$data['status'] = 1;
				$data['price'] =$price;
			 	$data['sum'] = $sum;
		        $data['msg'] = '处理成功';
				$this->ajaxReturn($data);
			}
		}
	}

	/**
	 * 删除购物车中某个商品
	 * @return [type] [description]
	 */
	public function delItemByuid(){
		if(IS_POST){
			$cart=D("shopcart");
			$sort=I('id');
			$result= $cart->deleteid($sort);
			if($result){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}
		}
		
	}

	/**
	 * 向购物车中添加一个商品
	 * @param [type] $id [description]
	 */
	public function addItem()
	{

			//添加新东东到session中
			$goodid=I('id');//获取商品id
			$uid=is_login();//获取用户id
			$gnum=I('num');//获取商品数量
			$gprice=I('price');//获取商品价格
			$gname=I('name');
			$createTime=NOW_TIME;//获取当前时间			

			//组织数据结构
			$shopcart=M("shopcart");
			$data['goodid']=$goodid;
			$data['uid']=$uid;			
			$data['sort']=$goodid;
			$data['parameters']='';
			$data['price']=$gprice;
			$data['create_time']=NOW_TIME;
		
			//读取数据库此用户此商品的个数（这个也可以用session，就是当加载的时候把购物车当到session当中）
			$datanum=M("shopcart")->where("goodid='$goodid'and uid='$uid'")->getField("num");
			if($datanum){
				$data['num']=$gnum+$datanum;	
				//更新此商品
				$lastId=$shopcart->where("goodid='$goodid'and uid='$uid' ")->save($data);
				
			}else{
				$data['num']=$gnum;	
				$lastId=$shopcart->add($data);	
			}						
			if($lastId>0){
				$res = D('shopcart')->getCartAll();
				$this->ajaxReturn($res);
			}else{
				$this->error('添加失败');			
			}
				
	}

	/**
	 * 添加地址
	 */
	public function addAddress()
	{		
		$uid=D('Shopcart')->uid();//获取对象的uid
		$Transport = M("transport"); // 实例化transport对象
		//获取要保存的值
		$data['realname']=I('name');
		$data['cellphone']=I('phone');
		$data['address']=I('dizhi');
		$data['uid'] = $uid;
		$data['create_time']=NOW_TIME;
		if(I('msg')=='yes'){
			if($Transport->where("uid='$uid' and status='1'")->getField("id")){
				$single['status'] = 0;
				$Transport->where("uid='$uid'")->setField($single); 
			}
			$data['status'] = 1;			
			$data['addressid']=$Transport->add($data); 
			$data['value'] = "default";
			//$data['addressid']=$Transport->where("uid='$uid' and status='1'")->getField("id");
	  		$data['msg'] = 'yes';
		}else{
			$data['status'] = 0;			
	    	//$data['orderid'] = $id;
        	$data['addressid']=$Transport->add($data); // 根据条件保存修改的数据
	   		//$data['addressid'] = M("transport")->where("orderid='$id'")->getField("id");
			// 返回新增标识
	 		$data['msg'] = 'no';
	 	}
   		$this->ajaxReturn($data);
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
}

?>