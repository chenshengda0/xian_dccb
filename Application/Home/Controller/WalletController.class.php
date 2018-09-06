<?php
namespace Home\Controller;

/**
 * 用户控制器
 */
class WalletController extends HomeController{
	
	
	public function _initialize(){
		parent::_initialize();
	}
   
   //用户充值操作
    public function recharge(){
		
		//系统配置参数
		$bonusRule=get_bonus_rule();
		
		if(IS_POST){
			$num = I('num');
			$psd = I('pay');
			$uid=is_login();
			$userinfo=M('member')->field('psd2,mobile,realname')->where(array('uid'=>$uid))->find();
			
			if($num<0||$num==""){
                $this->error("转入数量必须大于0且不为空");
            }
          
           if($psd!=$userinfo["psd2"]||$psd==""){
                $this->error("支付密码为空或错误");
            }
			$wait = M('ciex_recharge')->where(array('uid'=>$uid,'status'=>0))->count();
			if($wait >= 3){
				$this->error("您至少还有三个充值订单未支付，请勿重复申请");
			}
			

			$price = $bonusRule['eve_price'] / $bonusRule['usdt_price'];
			$usdt_num = $price * $num;
			$usdt_num = round($usdt_num, 4); 
			
			$data['ciex_num'] = $num;
			$data['uid'] = $uid;
			$data['ciex_price'] = $bonusRule['eve_price'];
			$data['usdt_num'] = $usdt_num;
			$data['usdt_price'] = $bonusRule['usdt_price'];
			$data['status'] = 0;
			$data['add_time'] = time();
			$data['mobile'] = $userinfo['mobile'];
			$data['realname'] = $userinfo['realname'];
			
			$res = M('ciex_recharge')->add($data);
			if($res){
				$this->success("充值已申请，请及时支付!", U('Wallet/recharge_pay',array('id'=>$res)),3);
				exit;
			}else{
				
				$this->error("充值申请失败");
			}
				
			
				
			
		}
		
		
		
        $this->assign("bonusRule",$bonusRule);
        $this->display();
    }
	
	//为上一步充值做个支付
	public function recharge_pay(){
		
		$id = I('id');
		$uid=is_login();
		$order = M('ciex_recharge')->where(array('uid'=>$uid,'id'=>$id))->find();
		if($order){
			
			if(IS_POST){
				
				$file = $_FILES;
				$upload = new \Think\Upload();// 实例化上传类
				$upload->maxSize   =     3145728 ;// 设置附件上传大小
				$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
				$upload->savePath  =      './Home/jyrz/'; // 设置附件上传目录
				// 上传文件
				$info   =   $upload->upload();
				 
				if(!$info) {
					// 上传错误提示错误信息
					$this->error($upload->getError());
				}else{
					// 上传成功 获取上传文件信息
					foreach($info as $file){
						
					}
					$idcard =  './Uploads/'.ltrim($file['savepath'].$file['savename'],'./');
				}
				$url = $idcard;
				
				$data['payment_img'] = $url;
				$data['pay_time'] = time();
				$data['status'] = 1;
				$res = M('ciex_recharge')->where(array('uid'=>$uid,'id'=>$id))->save($data);
				
				if($res){
					$this->success("支付成功，请等待平台确认!", U('User/index'),3);
						exit;
				}else{
					
					$this->error("支付失败");
				}
					
				
			}
			
			
			
		}else{
			
			$this->error("订单不存在");
		}
		
		$list  =   M("wallet")->field(true)->order('id asc')->select();

        $this->assign('list',$list);
		$this->assign("info",$order);
        $this->display();
		
		
		
	}
	
	//充币列表
	public function recharge_log(){
		$uid = is_login () ;
		
		$list  =   M("ciex_recharge")->where(array('uid'=>$uid))->field(true)->order('id desc')->limit(30)->select();
		$status = array('-1'=>'无效','0'=>'未支付','1'=>'已支付','2'=>'已完成');
		if($list) {
			
			 foreach($list as $key=>$vo){
                $list[$key]['type'] = $status[$vo['status']];
				
            }
           
        }
		
        $this->assign('list',$list);
		$this->display();
		
	}
   

}