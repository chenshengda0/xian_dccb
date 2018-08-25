<?php
namespace Home\Controller;
use Think\Model;
/**
 * 平台交易控制器
 */
class TradingHallController extends HomeController{
    
    /**
     * 提现申请
     */
    public function wdReply(){
    
    	$uid = is_login();
    	$secondpassword=is_sndpsd();
    	if(!$secondpassword){
    		$this->secondpassword();
    	}else{
    		$userinfo = self::$Member->where(array('uid'=>$uid))->find();
    		$Wd =  M('Withdrawal');
    		$rule = get_bonus_rule();//交易规则  		
	    	$wdinfo = $Wd->where(array('userid'=>$uid,'status'=>0))->find();
	    	
	    	if (IS_POST) {
	    		if(!empty($wdinfo)){
	    			$this->error('已申请过提现，等申请批准后方可再次申请!');
	    		}
				$money = trim(I('money'));         							//提取金额
				$data['mobile']=$mobile = $userinfo['mobile'];					//预留手机号
				$data['banknumber']=$banknumber = $userinfo['banknumber'];		//银行卡号
				$data['bankname']=$bankname = $userinfo['bankname'];			//银行名称
				$data['bankaddress']=$bankaddress = $userinfo['bankaddress'];
				$data['bankholder']=$bankholder = $userinfo['realname']  ;		//开户人姓名
				$data['message'] = trim(I('message'));						//申请留言
				$data['createtime'] = time();							//申请时间
				$data['userid'] = $uid;									//申请人id
				$data['usernumber'] = $userinfo['usernumber'];			//申请人编号
				
				
				if(empty($bankholder)){
					$this->error('请联系管理员修改开户行姓名');
				}
				
				
				if($userinfo['hasmoney']<$money){
					$this->error('提现金额大于在账户余额');
				}
				
				if($rule['min_wd']>$money){
					$this->error('提现金额小于最小提现金额');
				}
				
				if($money%$rule['multiple_wd']){
					$this->error("提现金额必须是{$rule['multiple_wd']}的整数倍");
				}
				$fee = $money*$rule['fee_wd']*0.01;//手续费
				if($fee>$rule['max_wd_fee']){
					$fee = $rule['max_wd_fee'];
				}
				$data['fee']=$fee;	//手续费
				$data['money']=$money-$fee;//实得金额
				
				$flag = false;
				
				$flag = $Wd->add($data);
				if($flag){
					$flag =self::$Member->where(array('uid'=>$uid))->setDec('hasmoney',$money);
					if($flag){
						$map['banknumber']=$banknumber;		//银行卡号
						$map['bankname']=$bankname;			//银行名称
						$map['bankaddress']=$bankaddress;
						self::$Member->where(array('uid'=>$uid))->save($map);
						unset($map);
						$hasmoney = self::$Member->where(array('uid'=>$uid))->getField('hasmoney');
						$type_arr = array('recordtype'=>0,'moneytype'=>2,'changetype'=>22);
						$money_arr = array('money'=>$money,'hasmoney'=>$hasmoney,'taxmoney'=>$fee);
						money_change($type_arr, $userinfo,get_com(), $money_arr);
						$this->success('提现申请成功',U('wdRecord'));
						exit();
					}else{
						$Wd->where(array('id'=>$flag))->delete();
					}
				}
				$this->error('申请失败');
				
	    	}else{
	    		if(!empty($wdinfo)){
	    			$userinfo['mobile'] = $wdinfo['mobile'];
	    			$userinfo['banknumber'] = $wdinfo['banknumber'];
	    			$userinfo['bankname'] = $wdinfo['bankname'];
	    			$userinfo['bankholder'] = $wdinfo['bankholder'];
	    			$this->assign('wdinfo',$wdinfo);
	    		}
	    		$this->assign('userinfo',$userinfo);
	    		$this->assign('wdfee',$rule['fee_wd']);
	    		$this->assign('wdleast',$rule['min_wd']);
	    		$this->assign('wdmultiple',$rule['multiple_wd']);
	    		$this->assign('allow_bank',C('ALLOW_BANK'));
	    		
	    		$this->assign('nowday',date('j'));
	    		$this->assign('allowday',$rule['wd_time']);
	    		
	    		$this->assign('title','提现申请');
	    		$this->display();
	    	}
    	}
    }
    
    /**
     * 提现记录
     */
    public function  wdRecord(){
    	$uid = is_login();
    	/*接受查询条件*/
		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
    	
		$starttime = strtotime("$starttime 00:00:00");
		$endtime = strtotime("$endtime 23:59:59");
		
    	$map['createtime'] = array(array('egt',$starttime),array('elt',$endtime));
    	$map['userid'] = $uid;
    	
    	$list = $this->lists('Withdrawal',$map,$maps,'id desc');
    	$this->assign ( '_list', $list );
    
    	$this->searchCondition($maps);
    	
    	$this->assign('title','提现记录');
    	$this->display ();
    }
    
    /**
     * 电子币转报单币
     */
    public function tfReply(){
        	$uid = is_login();
    	$secondpassword=is_sndpsd();
    	if(!$secondpassword){
    		$this->secondpassword();
    	}else{
    		$bonusRole = M('BonusRule')->where(array('id'=>1))->find();//交易规则
    		$Member=D('Member');//用户表
    		$userinfo = $Member->where("uid=$uid")->find();
    		if (IS_POST) {
    			$money = I('money',0);         							//转币金额
    			$taruser = I('usernumber');         						//转入用户
    			$moneytype = I('moneytype',1);  //转币类型
    			$tftype =   I('tftype',1);
    			$taruserinfo =  $Member->where(array('usernumber'=>$taruser,'status'=>1))->find(); //转入用户信息 ;
    			$taruser = $taruserinfo['usernumber'] ;
    
    			if(empty($taruser)){
    				$this->error('请输入转入用户编号');
    			}
    
    			if(empty($taruserinfo)){
    				$this->error('转入用户不存在或未激活');
    			}
    
    			if($taruserinfo['uid']==$uid){
    				if(($tftype==1)||($tftype==2)){
    					$this->error('不能给自己转账');
    				}
    			}else{
    			    if($tftype==3){
    			        $this->error('转账类型错误');
    			    }
    			}
    
    			if($tftype==1){//卓越币转卓越币
    			    $outfiled = 'hasbill';
    			    $infiled = 'hasbill';
    			    $outmoneytype = 1;
    			    $inmoneytype = 1;
    			}elseif($tftype==2){//卓越币转卓越币
    				$outfiled = 'hasmoney';
    				$infiled = 'hasmoney';
    				$outmoneytype = 2;
    				$inmoneytype = 2;
    			}elseif($tftype==3){ //复消币转复消币
    				$outfiled = 'hasmoney';
    				$infiled = 'hasbill';
    				$outmoneytype = 2;
    				$inmoneytype = 1;
    			}
    
    			if($userinfo[$outfiled]<$money){
    				$this->error('转币金额大于账户余额');
    			}
    
    			if($bonusRole['min_tf']>$money){
    				$this->error('转币金额小于最小转币金额');
    			}
    			if($money%$bonusRole['multiple_wd']){
    				$this->error("转币金额必须是{$bonusRole['multiple_wd']}的整数倍");
    			}
    			$prefix = C('DB_PREFIX');
    			$model = new Model();
    			$model->startTrans();
    			$flag = true;
    			//数据操作
    			switch($inmoneytype){
    			    case 1:$sql1 = "update {$prefix}member  set hasbill = hasbill+{$money},allbill = allbill+{$money} where uid={$taruserinfo['uid']}";break;
    			    case 2:$sql1 = "update {$prefix}member  set hasmoney = hasmoney+{$money},allmoney = allmoney+{$money} where uid={$taruserinfo['uid']}";break;
    			}
    			$sql2 = "update {$prefix}member  set {$outfiled} = {$outfiled}-{$money} where uid={$uid}";
    
    			$res1 = $model->execute($sql1);
    			$res2 = $model->execute($sql2);
    				
    			if(!res1){$flag = false;}
    			if(!res2){$flag = false;}
    			if($flag){
    				$model->commit();
    				$taruserinfo = $Member->where(array('usernumber'=>$taruser,'status'=>1))->find(); //转入用户
    				$userinfo = $Member->where(array('uid'=>$uid))->find(); //转出用户
    				$op = md5($uid.$taruserinfo['uid'].time());
    					
    				$type_arr = array('recordtype'=>1,'moneytype'=>$inmoneytype,'changetype'=>24);
    				$money_arr = array('money'=>$money,'hasmoney'=>$taruserinfo[$infiled]);
    				money_change($type_arr, $taruserinfo, $userinfo, $money_arr,$op);
    				unset($type_arr,$money_arr);
    					
    				$type_arr = array('recordtype'=>0,'moneytype'=>$outmoneytype,'changetype'=>24);
    				$money_arr = array('money'=>$money,'hasmoney'=>$userinfo[$outfiled]);
    				money_change($type_arr, $userinfo,$taruserinfo, $money_arr,$op);
    					
    				$this->success('转币成功',U('tfRecord'));
    			}else{
    				$model->rollback();
    				$this->error('交易失败');
    			}
    
    		}else{
    			$this->assign('wdmultiple',$bonusRole['multiple_tf']);
    			$this->assign('userinfo',$userinfo);
    			$this->assign('tfleast',$bonusRole['min_tf']);
    			$this->title="会员间转币";
    			$this->display();
    		}
    	}
    	 
    	
    }
    
    
    /**
     * 充值申请
     */
    public function rechargeReply(){
    	$uid = is_login();
    	$uinfo = M('Member')->where(array('uid'=>$uid))->find();
    	if(IS_POST){
    		$data['userid'] = $uid;
    		$data['usernumber'] = $uinfo['usernumber'];
    		$data['money'] = trim(I('money'));
    		$data['bankholder'] = C('BANK_USER');
    		$data['bankname'] = C('BANK_NAME');
    		$data['banknumber'] = C('BANK_NUMBER');
    		$data['mobile'] = $uinfo['mobile'];
    		$data['out_banknumber'] = trim(I('out_banknumber'));
    		$data['out_bankholder'] = trim(I('out_bankholder'));
    		$data['outtime'] = trim(I('outtime'));
    		$data['out_bankname'] = trim(I('out_bankname'));
    		$data['message'] = trim(I('message'));
    		$data['type'] = trim(I('moneytype',2));
            $data["outtime"]=time();
    		$res = D('Recharge')->create($data);
    		if($res){
    			D('Recharge')->add();
    			$this->success('充值申请成功',U('rechargeRecord'));
    			exit();
    		}else{
    			$this->error(D('Recharge')->getError());
    		}
    	}else{
    		
    		$this->assign('uinfo',$uinfo);
    		$this->assign('allow_bank',C('ALLOW_BANK'));
    		$this->title="充值申请";
    		$this->display();
    	}
    	
    }
    
    /**
     * 充值申请记录
     */
    public function rechargeRecord(){
    	$uid = is_login();
    	
    	/*接受查询条件*/
		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
    	$this->searchCondition($maps);
    	
		$starttime = strtotime("$starttime 00:00:00");
		$endtime = strtotime("$endtime 23:59:59");
    	
    	//联合查询出类型和交易记录
    	$map['userid'] =$uid;
    	$map['createtime'] =array(array('egt',$starttime),array('elt',$endtime));;
    	$list =$this->lists('Recharge',$map,$maps,'createtime desc,status asc');
    	$this->assign ( '_list', $list );
    
    	$this->title="充值申请记录";
    	$this->display();
    }
    
    /**
     * 转币记录
     */
    public function tfRecord(){
    	
    	$uid = is_login();
    	/*接受查询条件*/
		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
    	$this->searchCondition($maps);
    	
		$starttime = strtotime("$starttime 00:00:00");
		$endtime = strtotime("$endtime 23:59:59");
    	
    	$map['createtime'] = array(array('egt',$starttime),array('elt',$endtime));
    	$map['changetype'] = 24;
    	$map['targetuserid'] = $uid;
    	 
    	$list = $this->lists('MoneyChange',$map,$maps,'id desc');
    	$this->assign ( '_list', $list );

    	
    	$this->title="转币记录";
    	$this->display();
    }
    
    /**
     * 申请成为报单中心
     */
    public function billReplay(){
    	$uid = is_login();
    	$info = self::$Member->where(array('uid'=>$uid))->field('isbill')->find();
    	if(IS_POST){
    		
    	}else{
			$this->assign('info',$info);
    		$this->title="升级申请";
    		$this->display();
    	}
    }
    
    /**
     *话费充值申请
     */
    public function mobileRecharge(){
    	$uid = is_login();
    	$uinfo = self::$Member->field('uid,usernumber,realname,hasshop,mobile')->find($uid);
    	$money_arr = array_reverse(str2arr(C('RG_MONEY')));
    	if(IS_POST){
    		$data['mobile'] = $mobile = I('post.mobile');
    		$data['mobiletype'] = $mobiletype = I('post.mobiletype');
    		$data['money'] = $money = I('post.money');
    		if($money>$uinfo['hasshop']){
    			$this->error('话费余额不足，无法充值');
    		}
    		$decmoney = $uinfo['hasshop']-$money;
    		$data['status'] = 0;
    		$data['createtime'] = time();
    		$data['userid'] = $uid;
    		$data['hasmoney'] = $decmoney;
    		$res = self::$Member->where(array('uid'=>$uid))->setDec('hasshop',$money);
    		if($res){
    			$res = M('RechargeMobile')->add($data);
    			$type=array('recordtype'=>0,'changetype'=>30,'moneytype'=>4);
				$money=array('money'=>$money,'hasmoney'=>$decmoney);
				money_change($type, $uinfo, get_com(), $money);
				
				$this->success('申请充值成功，敬请等待回复',U('mobileRecord'));
    		}
    	}else{
    		$this->assign('uinfo',$uinfo);
    		$this->assign('money_arr',$money_arr);
    		$this->title="话费充值申请";
    		$this->display();
    	}
    }
    /**
     * 消费转币记录
     */
    public function mobileRecord(){
    	$uid = is_login();
    
    	/*接受查询条件*/
		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
    	$this->searchCondition($maps);
    	
		$starttime = strtotime("$starttime 00:00:00");
		$endtime = strtotime("$endtime 23:59:59");
    
    	$map['createtime'] = array(array('egt',$starttime),array('elt',$endtime));
    	$map['userid'] = $uid;
    	 
    	$list = $this->lists('RechargeMobile',$map,$maps,'id desc');
    	$this->assign ( '_list', $list );

    	 
    	$this->title="消费记录";
    	$this->display();
    }
}