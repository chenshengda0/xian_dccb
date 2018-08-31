<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: wpf <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

use Common\Api\BonusApi;
class ShopController extends HomeController{
	function _initialize(){
		$uid=is_login();
		//获取该用户的购物币
		$shopmoney=M('member')->where(array('status'=>1,'uid'=>$uid))->getField('hasshop');
		$this->assign('shopmoney',$shopmoney);
		$freezemoney=M('member')->where(array('status'=>1,'uid'=>$uid))->getField('cachemoney');
		$this->assign('freezemoney',$freezemoney);
		//获取二级菜单
		$secmenus=M('shop_category')->where(array('status'=>1,'pid'=>array('neq',0)))->select();
		$this->assign('secmenus',$secmenus);		
		parent::_initialize();
	}
	function index(){	
		$map['status'] = 1;	
		if(IS_POST){
			$keyword=I('keyWord');
			$map['goods_name|goods_introduct|goods_detail'] = array (
					'like',
					'%' . $keyword . '%'
			);
		}
		if(IS_GET){
			$pid=I('pid');
			$map['category_id']=$pid;
		}
		$uid=is_login();
        
		//获取商品菜单
// 		$tree = D('ShopCategory')->where(array('status'=>1,'pid'=>0))->select();
		
// 		$categoryList=array();
// 		foreach ($tree as $k=>$v){
// 			//查询出该大类的最新前八条数据
// 			$categoryList[$k]=$v;
// 			$categoryList[$k]['shop']=M('shop')->where(array('status'=>1,'category_id'=>$v['id']))->limit(4)->select();
// 		}
// 		$this->assign('categoryList',$categoryList);
	    
		$list = $this->lists('shop',$map,$map,'id asc');
		$youhui = get_bonus_rule('youhui');
		foreach($list as $k=>$v){
		    $list[$k]['youhui'] = $v['ms_price']*$youhui*0.1;
		}
		
		$this->assign('goodlist',$list);
		$this->assign('title', "商品列表");
		$this->display();
	}
	//商品
	function getDetail_mobile($id){
		$goodinfo=M('shop')->where(array('id'=>$id))->find();
		$goodinfo['pics']=explode(',', $goodinfo['pics']);
		$this->goodinfo=$goodinfo;
		$this->display();
	}
	/**
	 * 显示商品详情页
	 */
	function goods(){
		$category_id=I('category_id');
		if(is_numeric($category_id)){
			$goodslist = D('shop')->where(array('status'=>1,'category_id'=>$category_id))->select();
			$this->assign('goodslist',$goodslist);
			if($goodslist){
				//更新浏览次数
				M('shop')->where(array('id' => $category_id))->setInc('view');
			}
			$this->category_name=M('shop_category')->where(array('id'=>$category_id))->getField('title');
			$this->display();
		}else{
			$this->error('禁止非法操作！');
		}
	}
	/**
	 * 显示购物车商品
	 */
	/**
	 * 显示购物车商品
	 */
	public function cart(){
		if(IS_POST){
			$goodid=I('id');//获取商品id
			$shop=M('shop')->where(array('id'=>$goodid,'status'=>1))->find();
			$uid=is_login();//获取用户id
			$gnum=I('num');//获取商品数量
			$youhui = get_bonus_rule('youhui');
			$gprice=$shop['ms_price']*$youhui*0.1;//获取商品价格
			//$gpv=I('pv');//获取商品价格
            
			//组织数据结构

			$shopcart=M("shopcart");
			$data['goodid']=$goodid;
			$data['uid']=$uid;
			$data['sort']=$goodid;
			$parameters=trim($_POST['attr'].' '.$_POST['color']);
			$data['parameters']=$parameters;
			$data['price']=$gprice;
			//$data['pv']=$gpv;

			$data['create_time']=NOW_TIME;
			
			//读取数据库此用户此商品的个数（这个也可以用session，就是当加载的时候把购物车当到session当中）
			$datanum=M("shopcart")->where("goodid='$goodid'and uid='$uid' and parameters='$parameters'")->getField("num");
			if($datanum){
				$data['num']=$gnum+$datanum;
				//更新此商品
				$lastId=$shopcart->where("goodid='$goodid'and uid='$uid' and parameters='$parameters'")->save($data);
					
			}else{
				$data['num']=$gnum;
				$lastId=$shopcart->add($data);
			}
			if($lastId>0){
				$info = array('info' =>true,'msg'=>'成功加入购物车！');
			}else{
				$info = array('info' =>false ,'msg'=>'加入购物车失败' );
			}
			
			$this->ajaxReturn($info);
		}else{
			//获取该用户购物车中的数据
			$cartlist = M('shopcart')->where (array('uid'=>is_login()))->select ();
			
			$sum_goods=0;//商品总额
			$sum_freight=0;//商品总额
			$youhui = get_bonus_rule('youhui');
			$gprice=$youhui*0.1;//获取商品价格
			foreach($cartlist as $k=>$val){
				//$shop=M("shop")->field('color,ms_price,goods_name,brand,category_id')->where(array('id'=>$val['goodid'],'status'=>1))->find();
				$shop=M("shop")->where(array('id'=>$val['goodid'],'status'=>1))->find();
				$shop['ms_price'] = $shop['ms_price']*$gprice;
				$cartlist[$k]['shop']=$shop;
				$sum_goods+=$val['num']*$val['price'];
				$sum_freight+=$val['num']*$shop['freight'];
			}
			
			$uid=is_login();
			$selfinfo=M('member')->where(array('uid'=>$uid))->find();
			$this->assign('hasmoney',$selfinfo['hasmoney']);
			$this->assign('hascp',$selfinfo['hascp']);
			//
			$jiangjin = get_bonus_rule('youhui_jjb')*0.01;
			$jifen = get_bonus_rule('youhui_jifen')*0.01;
			$this->assign('jiangjin',$jiangjin);
			$this->assign('jifen',$jifen);
			//获取购物车中商品总额和运费总额
			$this->assign('sum_goods',$sum_goods);
			$this->assign('youhui',$youhui);
			$this->assign('sum_freight',$sum_freight);
			
			$this->assign('cartlist',$cartlist);
			$this->assign('title', "我的购物车");
			$this->display();
		}
	}
	
	/**
	 * 地址管理
	 */
	public function address(){
		$uid=is_login();
		if(IS_POST){
			$tran=M('transport');
			$post=$_POST;
			//----验证----
			$realname=I('realname');
			if(!$realname){
				$this->error('收件人姓名不能为空！');
			}
			$address=trim(I('address'));
			if(!$address){
				$this->error('详细地址不能为空！');
			}
			//------------
						
			$data=$tran->create();
			$data['area']=$_POST['province'].','.$_POST['city'].','.$_POST['district'];
			if(!empty($data)){				
				$data['create_time']=NOW_TIME;
				$data['uid'] = $uid;
				if($_POST['default']=='on'){
					if($tran->where("uid='$uid' and status='1'")->getField("id")){
						$single['status'] = 0;
						$tran->where("uid='$uid'")->setField($single);
					}
					$data['status'] = 1;					
				}else{
					$data['status'] = 0;				
				}
				$result=$tran->add($data);
				$this->success('地址添加成功',U('cart'));
			}			
		}else{			
			$address=M("transport");
			$list=$address->where("uid='$uid'")->select();		
			$this->assign('address_count', count($list));
			$this->assign('address_list', $list);
			$param['class']='width:100px';
			$this->assign('param',$param);
			$this->meta_title = get_nickname(session ( 'user_auth.uid' )).'的地址管理';
			$this->assign('title','地址管理');
			$this->display();		
		}
	}
	/**
	 * 设置某个地址为默认地址
	 * @return [type] [description]
	 */
	public  function shezhi() {
			$id=I('id');
			$uid=is_login();
			$Transport = M("transport"); // 实例化transport对象
			$data['status'] = 0;
			$Transport->where("uid='$uid'")->save($data);
			//$id=$_POST["id"];
			$result=$Transport->where("uid='$uid' and id='$id'")->setField("status",1);
/* 			if($result){
				$data['msg'] =  "设置成功";
				$data['status'] = 1;
			}
			else{
				$data['msg'] = "设置失败";
				$data['status'] = 0;
	
			}
			$this->ajaxreturn($data); */
			if($result){
				$this->success('设置成功');
			}else{
				$this->error('设置失败');
			}
		
	}
	/**
	 * 删除一个收货地址
	 * @return [type] [description]
	 */
/* 	public  function deleteAddress() {
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
	} */
	public  function deleteAddress() {
		
			$Transport = M("transport"); // 实例化transport对象
			$uid=is_login();
			$id=I('id');
			$res=$Transport->where("uid='$uid' and id='$id'")->delete();
			if($res){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}
		
	}

	/**
	 * 显示订单列表
	 */
	public function order() {
		$uid=is_login();
		$bonusApi=new BonusApi();
		/* 创建订单*/
		if(IS_POST){		
			$orderid=I('ordernumber');
			if($orderid){
				$orderinfo=M('shop_order')->where(array('orderid'=>$orderid))->find();
				if($orderinfo){
					if($orderinfo['status']==2){
						$diffday=$bonusApi->blackoutdate($orderinfo);
					}elseif($orderinfo['status']==3){
						$diffday=$bonusApi->verifydate($orderinfo);
					}
					$orderlist[0]=$orderinfo;
					$orderlist[0]['diffday']=$diffday;					
				}						
			}else{
				$orderstatus=I('orderstatus');
				if($orderstatus){
					$data['status']=$orderstatus;
				}				
				$data['uid']=$uid;
				$orderlist=$this->lists('shop_order',$data,array(),'create_time desc');
				$orderlist=$this->searchOrder($orderlist, $bonusApi);
				$this->assign('status',$orderstatus);
			}
			$this->assign('orderlist',$orderlist);
		}else{
			$data['status']=array('gt',0);
			$data['uid']=$uid;		
			$orderlist=$this->lists('shop_order',$data,array(),'create_time desc');	//获取所有订单
			//$orderlist=$this->searchOrder($orderlist, $bonusApi);	
			$this->assign('title', "订单管理");
			$this->assign('orderlist',$orderlist);
			
		}
		$this->display();
	}
	
 	private function searchOrder($orderlist,$bonusApi){
		$ordercount=count($orderlist);
		for($i=0;$i<$ordercount;$i++){
			if($orderlist[$i]['status']==2){
				$orderinfo=$bonusApi->blackoutdate($orderlist[$i]);
				$orderlist[$i]=$orderinfo;
				/* if(isset($orderinfo['diffday'])){
					
				}  */
			}elseif($orderlist[$i]['status']==3){
				$diffday=$bonusApi->verifydate($orderlist[$i]);
				$orderlist[$i]['diffday']=$diffday;
			}
		}
		return $orderlist;
	} 
	

	
	/**
	 * 生成订单号
	 */
	public function createorder(){
		if(IS_POST){
			$selltype=I('selltype',2);	
			$uid=is_login();
			$userdata['uid']=$uid;
			$tag=$this->ordersn(); //标识号
			$uinfo=M('member')->where(array('uid'=>$uid,'status'=>1))->find();
			//获取该用户购物车中的数据
			$cartlist = M('shopcart')->where (array('uid'=>$uid))->select ();
				
				
			if(empty($cartlist)){
				$this->error('请先购买商品！',U('Shop/index'));
			}
			//判断用户是否没有选择收货地址
			$address=M('transport')->where("status='1' and uid='$uid'")->getField('id');
			if(empty($address)){
				//$this->error('请选择一个收货地址',U('Shop/address'));
				$data['msg']="请选择一个收货地址！";
				$data['url']=U('Shop/address');
				$this->ajaxReturn($data);
			}
	        
			//计算提交的订单的商品运费,累加运费
			//$trans=D('Shop')->getFreight();
			//$total=D('Shop')->getG();
			//获取总费用，（商品总额+运费总额）
			//$all=$total['r2'];
				
			//获取当前用户的购物币是否大于商品总额
// 			switch($selltype){
// 				case 2:
// 				//获取当前用户的购物币是否大于商品总额
// 				$hasmoney=M('member')->where($userdata)->getField('hasmoney');
// 				if($hasmoney<$all){
// 					$data['msg']="您的奖金币不足，无法购买！";
// 					$data['url']=U('cart');
// 					$this->ajaxReturn($data);
// 				}
// 				break;
// 				case 3:
// 				//获取当前用户的购物币是否大于商品总额
// 				$hascp=M('member')->where($userdata)->getField('hascp');
// 				if($hascp<$all){
// 					$data['msg']="您的复销币不足，无法购买！";
// 					$data['url']=U('cart');
// 					$this->ajaxReturn($data);
// 				}
// 				break;
// 			}
           
           
           foreach($cartlist as $k=>$v){
               $all = $all+$v['num']*$v['price'];
           }
           
           //判断用的积分或者奖金币是否够
           $jiangjin = get_bonus_rule('youhui_jjb')*0.01;
           $jifen = get_bonus_rule('youhui_jifen')*0.01;
           if($uinfo['hasmoney']<$all*$jiangjin){
               $data['msg']="您的奖金币不足，无法购买！";
               $data['url']=U('cart');
               $this->ajaxReturn($data);
           }
           if($uinfo['hascp']<$all*$jifen){
               $data['msg']="您的积分不足，无法购买！";
               $data['url']=U('cart');
               $this->ajaxReturn($data);
           }
           
			$data['addressid']=$address;//收货人地址
			
			$data['total']=$all;//总金额
			
			$data['orderid']=$tag;//订单号
			$data['tag']=$tag;//标签号
			$data['uid']=$uid;//当前用户id
			$data['backinfo']='已购买';
			$data['ispay']='4';//标示支付类型
			$data['status']='1';//等待发货
			$data['update_time']=NOW_TIME;//订单完成时间
			$data['create_time']=NOW_TIME;//订单完成时间
	       
			//根据订单id保存对应的费用数据
			$orderid=M('shop_order')->add($data);
			
			//扣除该用户的奖金币
// 			if($selltype==2){
// 				M('member')->where($userdata)->setDec('hasmoney',$all);
// 			}else{
// 				M('member')->where($userdata)->setDec('hascp',$all);
// 			}
			//扣钱
			M('member')->where($userdata)->setDec('hasmoney',$all*$jiangjin);
			M('member')->where($userdata)->setDec('hascp',$all*$jifen);
			
			
			$goodlist=M("shoplist");
			$cartcount=count($cartlist);
			$comment .= "购买商品：用户".get_username(is_login()).'购买了';
			foreach ($cartlist as $val){
				//$good=M("shop")->field('brand,goods_name,unit,freight,ms_price,score')->where(array('id'=>$val['goodid'],'status'=>1))->find();
				$goodlist->goodid = $val['goodid'];
				$goodlist->status = 1;
				$goodlist->orderid =$orderid;
				$goodlist->parameters =$val["parameters"];
				$goodlist->sort =$val['sort'];
				$goodlist->num = $val['num'];
				$goodlist->uid=$uid;
				$goodlist->tag=$tag;//标识号必须相同
				$goodlist->create_time= NOW_TIME;
				$goodlist->price= $val['price'];
				$goodtotal=$val['num']*$val['price'];
				$goodlist->total =$goodtotal;
				$comment.=$val['num']."个".get_good_name($val['goodid']).' ';
				$goodlist->add();
			}
			$comment.=',共消费'.$all;
	
			//清空购物车
			M('shopcart')->where(array('uid'=>$uid))->delete();
	
			//记录账户金额变更操作记录
			$relevance_user=M('member')->where(array('status'=>1))->find(is_login());
			$activation_user_info=get_com();
			
// 			if($selltype==2){
// 				$result = moneyChange(0,26,$relevance_user,$activation_user_info,$all,$hasmoney-$all,0,2);
// 			}else{
// 				$result = moneyChange(0,26,$relevance_user,$activation_user_info,$all,$hascp-$all,0,3);
// 			}	
			moneyChange(0,26,$relevance_user,$activation_user_info,$all*$jiangjin,$uinfo['hasmoney']-$all*$jiangjin,0,2);
			moneyChange(0,26,$relevance_user,$activation_user_info,$all*$jifen,$uinfo['hascp']-$all*$jiangjin,0,3);
			
			
			$data['msg']="订单提交成功！";
			$data['url']=U('Shop/order');
			$this->ajaxReturn($data);
		}
	}
	
	
	
	
	
	/**
	 * 删除购物车中某个商品
	 * @return [type] [description]
	 */
	public function delItemByuid(){
		if(IS_POST){
			$cart=M("shopcart");
			$sort=$_POST['id'];
			$uid=is_login();
			$result= $cart->where("sort='$sort'and uid='$uid'")->delete();		
			if($result){
				$data['status'] = 1;
				//$data['price'] =$price;
				//$data['count'] = $count;
				//$data['sum'] =  $sum;
				$data['msg'] = '处理成功';
				$this->ajaxReturn($data);
			}
		}
	}
	
	/**
	 * 查看订单详情
	 */
	public function detail(){
		$uid=is_login();
		//获取订单号
		$orderid=I('id');
		$orderinfo=M('shop_order')->where(array('orderid'=>$orderid,'uid'=>$uid))->find();
		$shoplist=M('shoplist')->where(array('status'=>1,'tag'=>$orderid,'uid'=>$uid))->select();
		$address=M('transport')->where(array('id'=>$orderinfo['addressid']))->find();
		/* foreach($shoplist as $n=>$val){
			$shoplist[$n]['shop']=M('shop')->where(array('status'=>1,'id'=>$val['goodid']))->find();
		} */
		$this->assign('info',$orderinfo);
		$this->assign('list',$shoplist);	
		$this->assign('title','订单详情');
		
		
 		$this->assign('address', $address);
		$this->display();
	}
	/**
	 * 确认订单
	 * @param unknown $id
	 */
	public function confirmOrder($id){
			$uid = is_login();
			$data['status'] = 3;
			$data['update_time'] = time();
			$res = M('shop_order')->where(array('orderid'=>$id))->save($data);
			if($res){
				$this->success('签单成功',U('order'));
			}else{
				$this->error('签单失败');
			}
	}
	 
	 public function getCnt(){	 	
	 	$this->ajaxReturn(D('shop')->getCnt());
	 }
	 

	/**
	 * 生成一个随机订单号
	 * @return [type] [description]
	 */
	function ordersn(){
		$yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
		$orderSn = $yCode[intval(date('Y')) - 2015] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5);
		return $orderSn;
	}
	
	public function getDetail(){		
		$id=I('id');
		$goodinfo=M('shop')->field('goods_name,goods_ico,goods_introduct,goods_detail')->where(array('id'=>$id))->find();
		$goodinfo['goods_ico']= __ROOT__.M('picture')->where(array('id'=>$goodinfo['goods_ico']))->getField('path');
		$this->ajaxReturn($goodinfo);
	}
	
	/**
	 * 收藏商品
	 */
	public function favor(){
		if(IS_AJAX){
			$id=I('id');
			$data["id"] = $id;
			$data["uid"]=is_login();			
			$fav=M("favortable");
			$exsit=$fav->where($data)->getField("id");
			if(!empty($exsit)){
				$fav->delete($exsit);
				$this->error('收藏取消！');
			}
			else{				
				$fav->add($data);
				$this->error('收藏成功！');
			}
		}
	}
        
        //商品详情
        public function xiangqing(){
            $id = I("id");
            $uid=is_login();
            $map["status"] = 1;
            $map["id"] = $id;
            $list = M('shop')->where($map)->find();
          			
            $this->assign('goodlist',$list);
            $this->assign('id',$id);
            $this->display();
        }
        
        public function kuangji_by() {
           
            $id = I("id");
            $uid=is_login();
            //商品
            $map["status"] = 1;
            $map["id"] = $id;    
            $list = M('shop')->where($map)->find();
          
          	//统计当前矿机类型个数
          	$allkg=M("kuangji")->where(array("kuangjiid"=>$list["id"],"userid"=>$uid,"sstatus"=>array("in","1,2")))->count();
          	if($allkg>=$list["tox_money_need"]){
            	$this->error("您已达到购买限制");
            }
          
          
          
            //个人信息
            $where['uid']=$uid;
            $where['status']=1;
            $userinfo = self::$Member->where($where)->find();
             
            if ($userinfo['hasmoney'] < $list['ms_price']) {
                $this->error("您的余额不足");
            }
            //执行业务
            $date['hasmoney'] = $userinfo['hasmoney'] - $list['ms_price']; 
            $result = self::$Member->where($where)->save($date);
            if ($result) {
                //写流水
                finance(5, $list['ms_price']);
                $type=array('recordtype'=>0,'changetype'=>5,'moneytype'=>2);
                $money=array('money'=>$list['ms_price'],'hasmoney'=>$date['hasmoney'],'taxmoney'=>0);
                money_change($type, $userinfo, get_com(), $money);
                $tag=$this->ordersn(); //标识号
                $data['userid'] = $uid;
                $data['kuangjiid'] = $list['id'];
                $data['kuangjicode'] = $tag;
                $data['ttime'] = 0;//当前运行时间
                $data['alltime'] = 0;//运行总时间
                $data['ccreatetime'] = time();
                $data['pprevtime'] = 0;
                $data['sstatus'] = 2;
                $data['danwei'] = $list['chanliang'];//每小时产量
                $data['ssuanli'] =  $list['suanli'];//算力
                $res = M('kuangji')->add($data);
                if($res){
                 
                  	$bonus= new BonusApi();
                  	$bonus->add_suanli($uid,$data['ssuanli']);
                  //	$bonus->dl_shouyi($userinfo,$list['ms_price']);                  
                    $this->success('购买成功',U('user/index'));
                }
            }
        }
        
        public function showkj() {
            $uid=is_login();
          	$id = I('id');
          	$where['iid'] = $id;
          	$where['sstatus'] = array('lt',3);
            $res =  M('kuangji')->join('zx_shop ON zx_kuangji.kuangjiid = zx_shop.id')->where($where)->find();
          
          	
          	if($res){
            	$this->assign('res',$res);
            }else{
            	$this->error('该矿机已经报废');
            }
            //我的算例
            $where=array();
            $where['sstatus'] = array('lt',3);
            $where['userid'] = $uid;
          	
            $res1 = M('kuangji')->where($where)->sum('ssuanli');
         	
            //累计算例
            $map['changetype'] = 2;
          	$map['targetuserid'] = $uid;
            $res2 = M('money_change')->where($map)->sum('money');
            
            //全网算例
          	$allsl = get_bonus_rule('allsl');
          	if(empty($allsl)){
            	 $map1['sstatus'] = array('lt',3);
                 $res3 = M('kuangji')->where($map1)->sum('ssuanli');
            }else{
            	$res3 = $allsl;
            }
           
           
            $this->assign('res1',$res1);
            $this->assign('res2',$res2);
            $this->assign('res3',$res3);
           
            $this->display();
        }
        
      
}
