<?php
namespace Home\Controller;
class BonusController extends HomeController {
	
	/**
	 * 用户奖金生成列表
	 */
	public function bonusCount(){
	
		$uid = is_login () ;
		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-3 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d H:i:s", time()));
    	
    	$starttime = strtotime($starttime);
    	$endtime = strtotime($endtime);
			
		$map['count_date'] = array(array('gt',$starttime),array('lt',$endtime)) ;
		$map['touserid'] = $uid;
	
		/*查询结果*/
		$bonus_list = $this->lists('bonus_count',$map,$maps);
		$list = $this->listFile($bonus_list);
		$this->assign('_list',$list);
	
		$this->searchCondition($maps);
	
		$this->title = '奖金明细';
		$this->display();
	}

	/**
	 * 奖金生成明细
	 */
	public function  showMore(){
	
		$uid = is_login();//当前登陆用户id
	
		//保存查询条件
		$maps['usernumber'] = $usernumber= I('usernumber');
		$maps['createtime'] = $createtime = I('createtime');
		$maps['changetype'] = $changetype = I('changetype',0);
		//查询类型
		if(!empty($usernumber)){
			$map['usernumber'] = $usernumber;
		}
	
		$cstarttime = date("Y-m-d 00:00:00", $createtime);
		$cendtime = date("Y-m-d 23:59:59", $createtime);
		$cstarttime = strtotime($cstarttime);
		$cendtime = strtotime($cendtime);
	
		$map['targetuserid'] =$uid ;
		$map['createtime'] = array(array('gt',$cstarttime),array('lt',$cendtime)) ;
		$map['status'] = 1 ;
		
		if($changetype){
			$map['changetype'] = $changetype;
		}else{
			$map['changetype'] = array('in','1,3,4,5,6');
		}
	
		$list = $this->lists('MoneyChange',$map,$maps);
		$this->assign ( '_list', $list );
		
		$opt = array(
				array(
						'type'=>'select',
						'name'=>'changetype',
						'option'=>array(
								'0'=>'全部',
								'1'=>getChangeType(1),
								'3'=>getChangeType(3),
								'4'=>getChangeType(4),
								'5'=>getChangeType(5),
								'6'=>getChangeType(6),
		
						),
						'value'=>$maps['changetype'],
				),
				array(
						'type'=>'input',
						'ipttype'=>'hidden',
						'name'=>'targetuserid',
						'value'=>$maps['targetuserid'],
				),
				array(
						'type'=>'input',
						'ipttype'=>'hidden',
						'name'=>'createtime',
						'value'=>$maps['createtime'],
				)
		);
		
		$this->searchCondition($maps,$opt);
	
		$this->title = '奖金明细列表';
		$this->display ();
	
	}
	
	/**
	 * 用户支出列表
	 */
	public function bonusPay(){
		
		$uid = is_login () ;//当前登陆用户id
		
		/*接受查询条件*/
		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d H:i:s", time()));
    	$maps['usernumber'] = $usernumber = I('usernumber');
    	
		$starttime = strtotime("$starttime 00:00:00");
		$endtime = strtotime("$endtime 23:59:59");
		//查询类型
		if(!empty($usernumber)){
			$map['usernumber'] = $usernumber;
		}
	
		$map['createtime'] = array(array('egt',$starttime),array('elt',$endtime)) ;
		$map['status'] = 1 ;		
		$map['targetuserid'] = $uid;
		$map['recordtype'] = 0;
		//实现分页出
	
		$list = $this->lists('MoneyChange',$map,$maps,'moneytype,id desc');
		$this->assign ( '_list', $list );

		$this->searchCondition($maps);
		
		$this->title = '用户支出列表';
		$this->display ();	
	}

	/**
	 * 充值明细
	 */
	public function rechargeList(){
		$uid = is_login () ;
		/*接受查询条件*/
		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d H:i:s", time()));
    	
		$starttime = strtotime("$starttime 00:00:00");
		$endtime = strtotime("$endtime 23:59:59");
		
		$map['status'] = 1 ;		
		$map['targetuserid'] = $uid;
		$map['changetype'] = 21;
		$map['createtime'] = array(array('egt',$starttime),array('elt',$endtime)) ;

		/*查询结果*/
		$list = $this->lists('MoneyChange',$map,$maps,'moneytype,id desc');
		$this->assign ( '_list', $list );
		
		$this->searchCondition($maps);
		
		$this->title = '用户充值明细';
		$this->display ();	
	}
	


	/**
	 * 财务流水
	 */
	public function financialFlow1(){
		$uid = is_login () ;//当前登陆用户id
		
		/*接受查询条件*/
		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
    	$maps['usernumber'] = $usernumber = I('usernumber');
    	
		$starttime = strtotime("$starttime 00:00:00");
		$endtime   = strtotime("$endtime 23:59:59");
		//查询类型
		if(!empty($usernumber)){
			$map['usernumber'] = $usernumber;
		}
		
		$map['createtime'] = array(array('egt',$starttime),array('elt',$endtime)) ;
		$map['status'] = 1 ;
		$map['targetuserid'] = $uid;
		//实现分页出
		
		$list = $this->lists('MoneyChange',$map,$maps);
		$this->assign ( '_list', $list );
		
		$this->searchCondition($maps);
		
		$this->title = '财务流水';
		$this->display ();
	}
  
	public function financialFlow(){
	    $uid = is_login () ;//当前登陆用户id
	    $map['status'] = 1 ;
	    $map['targetuserid'] = $uid;
	   $map['changetype'] =array("in","3,4,7,8,9,10,11,14,16,21");
	    $shouru = M('MoneyChange')->where($map)->order('createtime desc')->sum("money");

	    //转入
     
       $mao['changetype'] =16;
      	$mao["targetuserid"]=$uid;
      	$mao['recordtype'] = 1;
	   $zhuanru= M('MoneyChange')->where($mao)->order('createtime desc')->limit(7)->select();
	 //   $map['recordtype'] = 0;
      
      //转出
        $mad['userid'] = $uid;
        $mad['changetype'] =16;
      	$mad['recordtype'] = 0;
        $zhuanchu = M('MoneyChange')->where($mad)->order('createtime desc')->limit(7)->select();
      
	  //挂售
       $mat['targetuserid'] = $uid;
	   $mat['changetype'] =array("in","3,4,9,10,11,14");
	   $guashou = M('MoneyChange')->where($mat)->order('createtime desc')->limit(7)->select();
	    
	  //手续费
       $mam['targetuserid'] = $uid;
	   $mam['changetype'] =8;
	   $shouxu = M('MoneyChange')->where($mam)->order('createtime desc')->limit(7)->select();
     
      
      $this->assign("zhuanru",$zhuanru);
      $this->assign("zhuanchu",$zhuanchu);
      $this->assign("guashou",$guashou);
      $this->assign("shouxu",$shouxu);
      
      
      
 	  //  $this->assign('tt_shouru',sprintf("%.2f",$tt_shouru));
 	 //   $this->assign('tt_zhichu',sprintf("%.2f",$tt_zhichu));
	  //
	    $this->assign('all',$shouru);
	   // $this->assign('zhichu',$zhichu);
	    $this->display();
	}
}