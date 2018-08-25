<?php
namespace Admin\Controller;
use Common;
use Common\Api\CommonApi;
/**
 * 平台交易控制器
 */
class TradingHallController extends AdminController{
    
    /**
     * 提现申请
     */
    public function withdrawal($id){//转账
	    $wdinfo = M('Withdrawal')->where(array('id'=>$id))->find();
	    $this->assign('wdinfo',$wdinfo);
	    $this->assign('id',$id);	    		
	    $this->assign('meta_title','提现申批');
	    $this->display();
    }
    public function importwd(){
    	$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
    	$maps['usernumber'] = $usernumber = I('usernumber');
    	$maps['status'] = $status = I('status',2);
    	
    	/*查询条件*/
    	$starttime = strtotime("$starttime 00:00:00");
    	$endtime = strtotime("$endtime 23:59:59");
    	$map['createtime'] = array(array('gt',$starttime),array('lt',$endtime));
    	
    	if(!empty($usernumber)){
    		$numwhere['usernumber']= $usernumber ;
    		$user = M('Member')->where($numwhere)->find();
    		if(!$user){
    			$this->error('用户不存在！');
    		}else{
    			$map['userid']= $user['uid'] ;
    		}
    	}
    	if($status!=2){
    		$map['status'] = $status;
    	}else{
    		$map['status'] = array('in','-1,1');
    	}
    	 
    	
    	$list = M('withdrawal')->where($map,$maps)->select();
    	$title = "提现记录";
    	$a =		array(
    	
    			array('createtime','申请时间'),
    			array('nickname','会员昵称'),
    			array('usernumber','会员编号'),
    	
    			array('total','提现金额'),
    	
    			array('money','实得金额'),
    			array('fee','手续费'),
    			array('message','提现留言'),
    			array('status','申请状态'),
    			array('handtime','处理时间'),
    	);
    	foreach ($list as $k => &$v){
    		 
    		$v['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
    		$v['nickname'] = M('member')->where(array('uid'=>$v['userid']))->getField('nickname');
    		$v['usernumber'] = $v['usernumber'];
    		$v['total'] = $v['money']+$v['fee'];
    		 
    		$v['money'] = $v['money'];
    		$v['fee'] =$v['fee'];
    		$v['message'] = $v['message'];
    		if($v['status']==1){
    			$v['status'] ='申请获批';
    		}else{
    			$v['status'] ='申请拒批';
    		}
    		$v['handtime'] = date('Y-m-d H:i:s',$v['handtime']);
    		 
    	}
    	$res=exportExcel($title,$a,$list);
    }
    //充值记录导出
    /**
     * 提现审批-同意
     */
    public function agree($id,$replyms=''){
    	$wdinfo = M('Withdrawal')->where(array('id'=>$id))->find();
    	if($wdinfo['status']!=0){
    		$this->error('该申请已审批');
    	}
    	
    	$user = M('Member')->where(array('uid'=>$wdinfo['userid']))->find();
    	if(empty($user)){
    		$this->error('该申请人已不存在');
    	}
    	
    	$data['status']=1;
    	$data['replyms'] = $replyms;
    	$data['handtime'] = time();
    	M('Withdrawal')->where(array('id'=>$id))->save($data);
    	$this->success('申批成功',U('wdOldRecord'));
    }
    /**
     * 提现审批-拒批
     */
    public function refuse($id,$replyms=''){
    	$wdinfo = M('Withdrawal')->where(array('id'=>$id))->find();
    	$data['status']=-1;
    	$data['replyms'] = $replyms;
    	$data['handtime'] = time();

    	$res = M('Withdrawal')->where(array('id'=>$id))->save($data);
    	if($res){
    		$userinfo =M('Member')->where(array('uid'=>$wdinfo['userid']))->find();
    		$touserinfo =  M('Member')->where(array('uid'=>1))->find();
    		$money = $wdinfo['money']+$wdinfo['fee'];
    		$res =M('Member')->where(array('uid'=>$wdinfo['userid']))->setInc('hasmoney',$money);
    		$hasmoney =M('Member')->where(array('uid'=>$wdinfo['userid']))->getField('hasmoney');
    		moneyChange(1, 23, $userinfo, $touserinfo, $money, $hasmoney, 0);
    		$this->success('拒批成功',U('wdOldRecord'));
    	}else{
    		$this->error('拒批失败');
    	}
    	
    }
    /**
     * 提现申请记录
     */
    public function  wdNewRecord(){
    	$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
    	$maps['usernumber'] = $usernumber = I('usernumber');
    	
    	/*查询条件*/
    	$starttime = strtotime("$starttime 00:00:00");
    	$endtime = strtotime("$endtime 23:59:59");
    	
    	$map['createtime'] = array(array('gt',$starttime),array('lt',$endtime));
    	
    	if(!empty($usernumber)){
    		$numwhere['usernumber']= $usernumber ;
    		$user = M('Member')->where($numwhere)->find();
    		if(!$user){
    			$this->error('用户不存在！');
    		}else{
    			$map['userid']= $user['uid'] ;
    		}
    	}
    	$map['status'] = 0;
    	$list = $this->lists('Withdrawal',$map,$maps);
    	$this->assign ( '_list', $list );
    	$this->searchCondition($maps);
    	
    	$this->meta_title="提现申请";
    	$this->display ('withdrawalRecord');
    }
    
    /**
     * 已处理提现申请
     */
    public function  wdOldRecord(){
    	 
    	$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
    	$maps['usernumber'] = $usernumber = I('usernumber');
    	$maps['status'] = $status = I('status',2);
    	 
    	/*查询条件*/
    	$starttime = strtotime("$starttime 00:00:00");
    	$endtime = strtotime("$endtime 23:59:59");
    	$map['createtime'] = array(array('gt',$starttime),array('lt',$endtime));
    	 
    	if(!empty($usernumber)){
    		$numwhere['usernumber']= $usernumber ;
    		$user = M('Member')->where($numwhere)->find();
    		if(!$user){
    			$this->error('用户不存在！');
    		}else{
    			$map['userid']= $user['uid'] ;
    		}
    	}
    	 if($status!=2){
    	 	$map['status'] = $status;
    	 }else{
    	 	$map['status'] = array('in','-1,1');
    	 }
    	
    
    	$list = $this->lists('Withdrawal',$map,$maps);
    	$this->assign ( '_list', $list );
    	
    	/*追加查询条件*/
    	$arr = array(
    			array(
    					'type'=>'select',
    					'name'=>'status',
    					'option'=>array(
    							'2'=>'全部',
    							'1'=>'批准',
    							'-1'=>'拒批',
    					),
    					'value'=>$maps['type'],
    			)
    	);
    
    	$this->searchCondition($maps,$arr);
    	 
    	$this->meta_title="提现记录";
    	$this->display('withdrawalRecord');
    }
    //提现汇总
    public function  wdCollect(){
    	$list = M()->query("select sum(money) m,sum(fee) f from zx_withdrawal where status=1");
    	$this->assign ( '_list', $list[0] );
    	$this->assign ( 'total', $list[0]['m']+$list[0]['f'] );
    	 
    	$this->meta_title="提现汇总";
    	$this->display();
    }
    
    /**
     * 转币记录
     */
    public function tfRecord(){
    	/*接受查询条件*/
    	$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
    	$maps['usernumber'] = $usernumber = I('usernumber');
    	 
    	/*查询条件*/
    	if(($starttime=='')||($endtime=='')){
    		$starttime=strtotime('-12 month');
    		$endtime=time();
    	}else{
    		$starttime = strtotime("$starttime 00:00:00");
    		$endtime = strtotime("$endtime 23:59:59");
    	}
    	$map['createtime'] = array(array('gt',$starttime),array('lt',$endtime));
    	 
    	if(!empty($usernumber)){
    		$numwhere['usernumber']= $usernumber ;
    		$user = M('Member')->where($numwhere)->find();
    		if(!$user){
    			$this->error('用户不存在！');
    		}else{
    			$map['userid']= $user['uid'] ;
    		}
    	}
    	$map['recordtype']= 1;
    	$map['changetype']= 24;

    	$list = $this->lists('MoneyChange',$map,$maps,'id desc');
    	$list = $this->tfFiter($list);
    	$this->assign ( '_list', $list );
    	
    	$this->searchCondition($maps);
    	 
    	$this->assign('meta_title','转币记录');
    	$this->display ();
    }
    
    private function tfFiter($list){
    	$map['recordtype']= 0;
    	$map['changetype']= 24;
    	foreach ($list as &$v){
    		$map['op'] = $v['op'];
    		$v['outtype'] = M('MoneyChange')->where($map)->getField('moneytype');
    	}
    	unset($v);
    	return $list;
    }
    
    /**
     * 奖金币充值
     */
    public function moneyInc(){
    	$loginid = is_login();
			if(IS_POST){
				$money = I('money') ;
				$moneytype = I('moneytype',2);
				/* if(($moneytype==2)&&(C('IS_BILL'))){
					$moneytype=1;
				} */
				if($money<=0){
					$this->error('充值金额不能小于0');
				}
				$usernumber = I('usernumber');
				$usernumber = trim($usernumber);
				//$touserid = M('Member')->where(array('usernumber'=>$usernumber,'status'=>1,'isbill'=>1))->getField('uid');
				$touserid = M('Member')->where(array('usernumber'=>$usernumber,'status'=>1))->getField('uid');
				if(!$touserid){
					$this->error('用户不存在或未激活');
				}
				
				$touser   = M('Member')->where(array('uid'=>$touserid))->find();
				if($moneytype==1){
					$billfield1 = 'hasbill';
					$billfield2 = 'allbill';
					$fieldname = "在线钱包";
				}elseif($moneytype==2){
					$billfield1 = 'hasmoney';
					$billfield2 = 'allmoney';
					$fieldname = "总钱包";
				}elseif($moneytype==3){
					$billfield1 = 'hascp';
					$billfield2 = 'allcp';
					$fieldname = "动态钱包";
				}

				$user_data[$billfield1] = $touser[$billfield1]+$money;
				$user_data[$billfield2] = $touser[$billfield2]+$money;
				$user_data['recharge'] = $touser['recharge']+$money;
				
				/* if($touser['userrank']>=get_bonus_rule('level_bill_condition3')){
					if(($money>=get_bonus_rule('level_bill_condition1'))&&($touser['isbill']==0)){
						$user_data['isbill'] = 1;
					}
				} */
				$res = M('Member')->where(array('uid'=>$touserid))->save($user_data);
				if($res){
					
					$type=array('recordtype'=>1,'changetype'=>21,'moneytype'=>$moneytype);
					$money_arr=array('money'=>$money,'hasmoney'=>$user_data[$billfield1],'taxmoney'=>0);
					money_change($type, $touser, get_com(), $money_arr);
					
					$title='充值成功';
					$content = '公司已经给你充值'.$money.$fieldname;
					$res = CommonApi::sendMessage($touser['uid'],$title,$content);
					$this->success('充值成功',U('moneyIncRecord'));
				}else{
					$this->error('充值失败');
				}
			}else{
				$uid = I('id');
				$userinfo = M('Member')->where(array('uid'=>$uid,'status'=>1))->find();
				$this->assign('userinfo',$userinfo);
				$this->assign('is_bill',C('IS_BILL'));
				$this->meta_title="会员充值";
				$this->display ();
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
    	$maps['usernumber'] = $usernumber = I('usernumber');
    
    	/*查询条件*/
    	$starttime = strtotime("$starttime 00:00:00");
    	$endtime = strtotime("$endtime 23:59:59");
    	 
    	if(!empty($usernumber)){
    		$numwhere['usernumber']= $usernumber ;
    		$user = M('Member')->where($numwhere)->find();
    		if(!$user){
    			$this->error('用户不存在！');
    		}else{
    			$map['userid']= $user['uid'] ;
    		}
    	}
    	 
    	//联合查询出类型和交易记录
    	$map['createtime'] =array(array('gt',$starttime),array('lt',$endtime));
    	$map['status'] = 0;
    	$list =$this->lists('Recharge',$map,$maps,'createtime desc,status asc');
    	$this->assign ( '_list', $list );
    	 
    	$this->searchCondition($maps);
    
    	$this->meta_title="充值申请";
    	$this->display();
    }
    
    /**
     * 充值申请详情
     * @param unknown $id
     */
    public function rechargeInfo($id){
    	$reinfo = M('Recharge')->where(array('id'=>$id))->find();
    	$this->assign('info',$reinfo);
    	$this->meta_title='充值申请详情';
    	$this->display();
    }
    
    
    /**
     * 充值申请-同意
     */
    public function bagree($id){
    	$reinfo = M('Recharge')->where(array('id'=>$id))->find();
    	if($reinfo['status']!=0){
    		$this->error('该申请已审批');
    	}
    	$user = M('Member')->where(array('uid'=>$reinfo['userid']))->find();
    	$userinfo = M('Member')->where(array('uid'=>1))->find();
    	if(empty($user)){
    		$this->error('该申请人已不存在');
    	}
    
    	if($reinfo['type'] == 1){
    		$billtype = 1;
    		$billfield1 = 'hasbill';
    		$billfield2 = 'allbill';
    		$fieldname = "在线钱包";
    	}elseif($reinfo['type'] == 2){
    		$billtype = 2;
    		$billfield1 = 'hasmoney';
    		$billfield2 = 'allmoney';
    		$fieldname = "总钱包";
    	}else{
    		$billtype = 4;
    		$billfield1 = 'hasshop';
    		$billfield2 = 'allshop';
    		$fieldname = "动态钱包";
    	}
    	$user_data[$billfield1] = $user[$billfield1]+$reinfo['money'];
    	$user_data[$billfield2] = $user[$billfield2]+$reinfo['money'];
    	$user_data['recharge'] = $user['recharge']+$reinfo['money'];
//     	if($user['userrank']>=get_bonus_rule('level_bill_condition3')){
//     		if(($reinfo['money']>=get_bonus_rule('level_bill_condition1'))&&($user['isbill']==0)){
//     			$user_data['isbill'] = 1;
//     		}
//     	}
    	
    	$res = M('Member')->where(array('uid'=>$reinfo['userid']))->save($user_data);
    	if($res>0){
    		$data['status']=1;
    		$data['handtime'] = time();
    		M('Recharge')->where(array('id'=>$id))->save($data);
    
    		$type = array('recordtype'=>1,'moneytype'=>$billtype,'changetype'=>21);
    		$money = array('money'=>$reinfo['money'],'hasmoney'=>$user_data[$billfield1]);
    		money_change($type, $user, get_com(), $money);
    		$title='充值成功';
    		$content = '公司已经给你充值'.$reinfo['money'].$fieldname.'请注意查收！'.date('Y-m-d H:i:s');
    		$res = CommonApi::sendMessage($user['uid'],$title,$content);
    		$this->success('充值成功',U('moneyIncRecord'));
    		 
    	}else{
    		$this->error('充值失败');
    	}
    }
    
    /**
     * 充值申请-拒绝
     */
    public function brefuse($id,$message=''){
    	$reinfo = M('Recharge')->where(array('id'=>$id))->find();
    	if($reinfo['status']!=0){
    		$this->error('该申请已审批');
    	}
    
    	$user = M('Member')->where(array('uid'=>$reinfo['userid']))->find();
    	$userinfo = M('Member')->where(array('uid'=>1))->find();
    	if(empty($user)){
    		$this->error('该申请人已不存在');
    	}
    
    	$data['status']=-1;
    	$data['handtime'] = time();
    	$data['cause'] = $message;
    	M('Recharge')->where(array('id'=>$id))->save($data);
    	$this->success('申批成功',U('rechargeRecord'));
    }
    
    
    /**
     * 会员充值明细
     */
    public function  moneyIncRecord(){
    		
    	/*接受查询条件*/
    	$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
    	$maps['usernumber'] = $usernumber = preg_replace('# #','',I('usernumber')) ;
    		
    	$starttime = strtotime("$starttime 00:00:00");
    	$endtime = strtotime("$endtime 23:59:59");
    	
    	if(!empty($usernumber)){
    		$numwhere['usernumber']= $usernumber ;
    		$user = M('Member')->where($numwhere)->find();
    		if(!$user){
    			$this->error('用户不存在！');
    		}else{
    			$map['targetuserid']= $user['uid'] ;
    		}
    	}
    	$map['createtime'] = array(array('gt',$starttime),array('lt',$endtime)) ;
    	$map['changetype'] = 21 ;
    	$list = $this->lists('MoneyChange',$map,$maps);
    	$this->assign('_list',$list);
    	
    	$this->searchCondition($maps);
    	
    	
    	$this->meta_title = '充值记录';
    	$this->display ();
    }
    public function importmi(){
    	$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
    	$maps['usernumber'] = $usernumber = I('usernumber');
    		
    	$starttime = strtotime("$starttime 00:00:00");
    	$endtime = strtotime("$endtime 23:59:59");
    	
    	if(!empty($usernumber)){
    		$numwhere['usernumber']= $usernumber ;
    		$user = M('Member')->where($numwhere)->find();
    		if(!$user){
    			$this->error('用户不存在！');
    		}else{
    			$map['targetuserid']= $user['uid'] ;
    		}
    	}
    	$map['createtime'] = array(array('gt',$starttime),array('lt',$endtime)) ;
    	$map['changetype'] = 21 ;
    	$list = M('money_change')->where($map,$maps)->select();
    	$title = '充值记录';
    	$a =		array(
    			array('nickname','用户昵称'),
    			array('usernumber','用户编号'),
    			array('moneytype','充值币种'),
    			array('money','充值金额'),
    			array('handtime','充值时间'),
    	);
    	foreach ($list as $k => &$v){
    		$v['nickname'] = M('member')->where(array('uid'=>$v['targetuserid']))->getField('realname');
    		$v['usernumber'] = $v['targetusernumber'];
    		$v['moneytype'] = '购物币';
    		$v['money'] = $v['money'];
    		$v['handtime'] = date('Y-m-d H:i:s',$v['createtime']); 
    	}
    	$res=exportExcel($title,$a,$list);
    }
    public function  incRecord(){
    
    	$list = M()->query("select sum(money) as money from zx_money_change where changetype=21");
    	$this->assign('money',$list[0]['money']);
    	 
    	$this->meta_title = '充值汇总';
    	$this->display ();
    }
    
    
    /**
     * 系统扣币
     */
    public function moneyDec(){
    	$loginid = is_login();
    	if(IS_POST){
    		$money = I('money') ;
    		if($money<=0){
    			$this->error('请输入合理的金额');
    		}
    		$usernumber = I('usernumber');
    		$usernumber = trim($usernumber);
    		$hasmoney = M('Member')->where(array('usernumber'=>$usernumber,'status'=>1))->find();
    		if(empty($hasmoney)){
    			$this->error('用户不存在或未激活');
    		}
    		$touserid = $hasmoney['uid'] ;
    		$moneytype = trim(I('moneytype',2));
    		if($moneytype == 1){
    			$field = 'hasbill';
    		}elseif($moneytype == 2){
    			$field = 'hasmoney';
    		}elseif($moneytype == 3){
    			$field = 'hascp';
    		}
    			
    		$touser    = M('Member')->where("uid = $touserid")->find();
    			
    		$user_data[$field] = $touser[$field]-$money;
    		$res = M('Member')->where("uid = $touserid")->save($user_data);
    		if($res){
    			moneyChange(0, 28, $touser, get_com(), $money, $user_data[$field], 0,$moneytype);
    			$this->success('扣币成功',U('Bonus/bonusChange'));
    		}else{
    			$this->error('扣币失败');
    		}
    	}else{
    		$uid = I('id');
    		$userinfo = M('Member')->where(array('uid'=>$uid,'status'=>1))->find();
    		$this->assign('userinfo',$userinfo);
    			
    		$this->meta_title="公司扣币";
    		$this->display ();
    	}
    }
    
    public function mobileRecord(){
    	/*接受查询条件*/
    	$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
    	$maps['usernumber'] = $usernumber = I('usernumber');
    	$maps['type']=$type = I('type',2);
    	$maps['mobile']=$mobile = I('mobile','');
    	
    	/*处理查询条件*/
    	$starttime = strtotime("$starttime 00:00:00");
    	$endtime = strtotime("$endtime 23:59:59");
    	
    	$map['createtime'] = array(array('gt',$starttime),array('lt',$endtime));
    	if(!empty($usernumber)){
    		$numwhere['usernumber']= $usernumber ;
    		$user = M('Member')->where($numwhere)->find();
    		if($user){
    			$map['userid']= $user['uid'] ;
    		}
    	}
    	
    	if($type<2){
    		$map['status'] = $type;
    	}
    	if(!empty($mobile)){
    		$map['mobile'] = $mobile;
    	}
    	
    	$list = $this->lists('RechargeMobile',$map,$maps,'id desc');
    	$this->assign ( '_list', $list );
    	
    	/*追加查询条件*/
    	$arr = array(
    			array(
    					'type'=>'input',
    					'name'=>'mobile',
    					'title'=>'请输入手机号',
    					'value'=>$maps['mobile'],
    			),
    			array(
    					'type'=>'select',
    					'name'=>'type',
    					'option'=>array(
    							'2'=>'全部',
    							'1'=>'已处理',
    							'0'=>'未处理',
    							'-1'=>'拒充',
    					),
    					'value'=>$maps['type'],
    			)
    	);
    	$this->searchCondition($maps,$arr);
    	
    	$this->meta_title=$usernumber."话费充值记录";
    	$this->display();
    }
    
    /**
     * 充值申请-同意
     */
    public function rAgree($id){
    	$reinfo = M('RechargeMobile')->where(array('id'=>$id))->find();
    	if($reinfo['status']!=0){
    		$this->error('该申请已审批');
    	}
    	$user = M('Member')->where(array('uid'=>$reinfo['userid']))->find();
    	$userinfo = M('Member')->where(array('uid'=>1))->find();
    	if(empty($user)){
    		$this->error('该申请人已不存在');
    	}
    	$data['status'] = 1;
    	$data['handtime'] = time();
    	$res = M('RechargeMobile')->where(array('id'=>$id))->save($data);
    	if($res>0){
    		$title='充值成功';
    		$content = '公司已经向'.$reinfo['mobile'].'手机号充值'.$reinfo['money'].'话费请注意查收！'.date('Y-m-d H:i:s');
    		$res = CommonApi::sendMessage($user['uid'],$title,$content);
    		$this->success('话费充值成功',U('mobileRecord'));
    		 
    	}else{
    		$this->error('充值失败');
    	}
    }
    
    /**
     * 充值申请-同意
     */
    public function rRefuse($id){
    	$reinfo = M('RechargeMobile')->where(array('id'=>$id))->find();
    	if($reinfo['status']!=0){
    		$this->error('该申请已审批');
    	}
    	$user = M('Member')->where(array('uid'=>$reinfo['userid']))->find();
    	$userinfo = M('Member')->where(array('uid'=>1))->find();
    	if(empty($user)){
    		$this->error('该申请人已不存在');
    	}
    	$after_money = $reinfo['money']+$user['hasshop'];
    	$res = self::$Member->where(array('uid'=>$reinfo['userid']))->setInc('hasshop',$reinfo['money']);
    	if($res>0){
    		
    		$data['status'] = -1;
    		$data['handtime'] = time();
    		$res = M('RechargeMobile')->where(array('id'=>$id))->save($data);
    		
    		$type=array('recordtype'=>1,'changetype'=>31,'moneytype'=>4);
    		$money=array('money'=>$reinfo['money'],'hasmoney'=>$after_money);
    		money_change($type, $user, get_com(), $money);
    		
    		$title='拒充留言';
    		$content = '由于您充值的手机号本月充值过于频繁，为了你的账号安全，不予充值，望谅解'.date('Y-m-d H:i:s');
    		$res = CommonApi::sendMessage($user['uid'],$title,$content);
    		$this->success('拒批成功',U('mobileRecord'));
    		 
    	}else{
    		$this->error('拒批失败');
    	}
    }
    
    public function delMobile($id){
    	
    }
    
    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0){
    	switch ($code) {
    		case -1:  $error = '该用户已达最高级别！'; break;
    		case -2:  $error = '已过升级 时间！'; break;
    		case -3:  $error = '充值金额不足！'; break;
    		default:  $error = '未知错误';
    	}
    	return $error;
    }
    public function jiaoyi(){
       //$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	//$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
    	//$maps['usernumber'] = $usernumber = I('usernumber');
    	//$maps['changetype'] = $changetype = I('changetype',-2);

		$starttime = strtotime("$starttime 00:00:00");
		$endtime = strtotime("$endtime 23:59:59");
		
		//$map['createtime'] = array(array('gt',$starttime),array('lt',$endtime)) ;
		$map['status'] = 6;
		if(!empty($usernumber)){
			$map['_string'] = "targetusernumber ='$usernumber'";
			 
		}
		
	
		$model = M('jiaoyi');
		
		$list = $this->lists('jiaoyi',$map,$maps);
		$this->assign ( '_list', $list );
		
		
		
		
		
// 		$opt = array(
// 				array(
// 						'type'=>'select',
// 						'name'=>'changetype',
// 						'option'=>$change_type,
// 						'value'=>$maps['changetype'],
// 				),
// 		);
		
		//$this->searchCondition($maps,$opt);
	
		
		$this->meta_title = '异常交易';
		$this->display ();
    }
    public function chuli(){
        $id = I('id');
        $dingdan = M('jiaoyi')->where(array('id'=>$id,'status'=>6))->find();
        //找到订单  给买家加币 冻结卖家
        $youinfo = self::$Member->where(array('uid'=>$dingdan['youid']))->find();//买家信息
        $myinfo = self::$Member->where(array('uid'=>$dingdan['myid']))->find();//卖家信息
        $data['hasmoney'] = $youinfo['hasmoney']+$dingdan['shuiliang'];
        $result = self::$Member->where(array('uid'=>$youinfo['uid']))->save($data);
        if($result){
          	//改变订单状态
          	M('jiaoyi')->where(array('id'=>$id,'status'=>6))->setField('status',5);
            //写流水冻结卖家
            moneyChange(1,4,$youinfo,$myinfo,$dingdan['shuiliang'],$data['hasmoney'],0,2);
            //冻结卖家
            $data1['status'] = -1;
            $data1['info'] = '作为卖家未及时收款['.$youinfo['usernumber'].']';
            $result1 = self::$Member->where(array('uid'=>$dingdan['myid']))->save($data1);
            if($result1){
                $this->success('处理成功');
            }else{
                $this->error('处理失败');
            }
        }else{
            $this->error('处理失败');
        }
    }
}