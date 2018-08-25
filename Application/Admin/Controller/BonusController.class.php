<?php
namespace Admin\Controller;
use Think\Model;
class BonusController extends AdminController {

	/**
	 * 财务流水
	 */
	public function  bonusChange(){

		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
		$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
		$maps['usernumber'] = $usernumber =preg_replace('# #','',I('usernumber'));


		$maps['changetype'] = $changetype = I('changetype',-2);
		$recordtype=trim(I("ob"));

		$starttime = strtotime("$starttime 00:00:00");
		$endtime = strtotime("$endtime 23:59:59");


		$map['createtime'] = array(array('gt',$starttime),array('lt',$endtime)) ;
		$map["recordtype"]=I('ob',1);
		if(!empty($usernumber)){
			if($recordtype==1){
				$map['_string'] = "targetusernumber ='$usernumber'";

			}else{
				$map['_string'] = "usernumber ='$usernumber'";
			}
		}
		if($changetype>-2){
			$map['changetype'] = $changetype;

		}

		dump($changetype);
		$maps["ob"]=$recordtype;

		$map["money"]=array("neq",0);
		$model = M('MoneyChange');
		/*$res=D("MoneyChange")->where($map)->select();
      	dump($res);die;*/
		//  dump($map); dump($maps);die;
		//

		$list = $this->lists('MoneyChange',$map,$maps);


		$this->assign ( '_list', $list );
		$this->assign("ob",$recordtype);

		$change_type = C('CHANGETYPE');

		$change_type['-2'] = '全部';
		ksort($change_type);
		$opt = array(
			array(
				'type'=>'select',
				'name'=>'changetype',
				'option'=>$change_type,
				//'value'=>$maps['changetype'],
			),
		);
		//dump($maps);dump($opt);die;
		$this->searchCondition($maps,$opt);

		$more["jiaoyi"]=D("moneyChange")->where(array("changetype"=>4))->sum("money");	//总充值
		$more["jinjiaoyi"]=D("moneyChange")->where(array("changetype"=>4,"createtime"=>array(array("elt",strtotime(date('Y-m-d',strtotime('+1 day')))),array("egt",strtotime(date('Y-m-d'))))))->sum("money");	//今日充值
		$more["shouxu"]=D("moneyChange")->where(array("changetype"=>8))->sum("money");	//总充值
		$more["jinshouxu"]=D("moneyChange")->where(array("changetype"=>8,"createtime"=>array(array("elt",strtotime(date('Y-m-d',strtotime('+1 day')))),array("egt",strtotime(date('Y-m-d'))))))->sum("money");	//今日充值
		$more["zhuanru"]=D("moneyChange")->where(array("changetype"=>16))->sum("money");	//总充值
		$more["jinzhuanru"]=D("moneyChange")->where(array("changetype"=>16,"createtime"=>array(array("elt",strtotime(date('Y-m-d',strtotime('+1 day')))),array("egt",strtotime(date('Y-m-d'))))))->sum("money");	//今日充值

		if($more["jiaoyi"]==""){$more["jiaoyi"]="0.00";}
		if($more["jinjiaoyi"]==""){$more["jinjiaoyi"]="0.00";}
		if($more["shouxu"]==""){$more["shouxu"]="0.00";}
		if($more["jinshouxu"]==""){$more["jinshouxu"]="0.00";}
		if($more["zhuanru"]==""){$more["zhuanru"]="0.00";}
		if($more["jinzhuanru"]==""){$more["jinzhuanru"]="0.00";}


		$this->assign("more",$more);


		$this->meta_title = '交易明细';
		$this->display ();

	}
	//导出财务流水
	public function importbc(){
		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
		$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
		$maps['usernumber'] = $usernumber = I('usernumber');
		$maps['changetype'] = $changetype = I('changetype',-2);
		$ob = I('ob');

		$starttime = strtotime("$starttime 00:00:00");
		$endtime = strtotime("$endtime 23:59:59");

		$map['createtime'] = array(array('gt',$starttime),array('lt',$endtime)) ;

		if(!empty($usernumber)){
			$map['_string'] = "targetusernumber ='$usernumber'";

		}
		if($changetype>-2){
			$map['changetype'] = $changetype;
		}

		$list = M('money_change')->where($map,$maps)->select();
		$title= '交易明细';
		$a =		array(
			array('count_date','时间'),
			//array('nickname','会员昵称'),
			array('usernumber','来源会员'),
			array('targetusernumber','目标会员'),
			//array('money1','变更前金额'),
			array('money','变更金额'),
			array('ob','变更状态'),
			//array('money2','变更后金额'),
			array('changetype','变更类型'),
			array('moneytype','变更币种'),
		);
		foreach ($list as $k => &$v){
			$v['count_date'] = date('Y-m-d H:i:s',$v['createtime']);
			$v['nickname'] = M('member')->where(array('uid'=>$v['targetuserid']))->getField('nickname');
			$v['usernumber'] = get_usernumber($v['usernumber']);
			$v['targetusernumber'] = $v['targetusernumber'];
			switch($v['recordtype']){
				case 0: $v['money1'] = $v['hasmoney']+$v['money'];break;
				case 1: $v['money1'] = $v['hasmoney']-$v['money'];break;
			}
			$v['money'] = $v['money'];
			if($ob == 1){
				$v['ob'] = '入账';
			}else{
				$v['ob'] = '出账';
			}

			$v['money2'] = $v['hasmoney'];
			$v['changetype'] =getChangeType($v['changetype']);
			$v['moneytype'] =getMoneyType1($v['moneytype']);

		}
		$res=exportExcel($title,$a,$list);
	}
	public function import(){
		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
		$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
		$maps['usernumber'] = $usernumber = I('usernumber');

		if(!empty($usernumber)){
			$numwhere['usernumber']= $usernumber ;
			$tid = M('Member')->where($numwhere)->getField('uid');
			if(!$tid){
				$this->error('用户不存在！');
			}else{
				$map['touserid']= $tid ;
			}
		}

		$starttime = strtotime("$starttime 00:00:00");
		$endtime = strtotime("$endtime 23:59:59");



		$map['count_date'] = array(array('gt',$starttime),array('lt',$endtime)) ;


		$list=M("bonus_total")->where($map,$maps)->select();

		$title= '奖金统计';
		$a =		array(
			array('count_date','汇总时间'),

			array('nickname','会员昵称'),
			array('usernumber','会员编号'),

			array('bonus1','推荐奖'),

			array('bonus3','领导奖'),
			array('bonus4','分 红'),
			array('allmoney','应发奖金累计'),
			array('total','实发奖金累计'),
			array('taxmoney','奖金税'),
		);
		foreach ($list as $k => &$v){

			$v['count_date'] = date('Y-m-d H:i:s',$v['count_date']);
			$v['nickname'] = M('member')->where(array('uid'=>$v['touserid']))->getField('nickname');
			$v['usernumber'] = $v['tousernumber'];
			$v['bonus1'] = $v['bonus1'];
			$v['bonus3'] = $v['bonus3'];

			$v['bonus4'] = $v['bonus4'];
			$v['allmoney'] =$v['total']+$v['taxmoney'];
			$v['total'] =$v['total'];
			$v['taxmoney'] = $v['taxmoney'];

		}
		$res=exportExcel($title,$a,$list);
	}
	/**
	 * 奖金统计
	 */
	public function bonusTotal(){

		$maps['usernumber'] = $usernumber =preg_replace('# #','',trim(I('usernumber')));

		if(!empty($usernumber)){
			$numwhere['usernumber']= $usernumber ;
			$tid = M('Member')->where($numwhere)->getField('uid');
			if(!$tid){
				$this->error('用户不存在！');
			}else{
				$map['targetuserid']= $tid ;
			}

		}
		$map["changetype"]=array("in","1,5,6,7");
		$map["money"]=array("neq",0);
		/**
		 *分页
		 */
		// dump($maps); dump($map);die;
		$bonus_list=$this->lists("MoneyChange",$map);
		//dump($bonus_list);die;
		$list = $this->listFile($bonus_list);
		$this->assign('_list',$list);


		$more["jiangjin"]=D("moneyChange")->where(array("changetype"=>array("in","1,5,6")))->sum("money");	//总充值
		$more["jinjiangjin"]=D("moneyChange")->where(array("changetype"=>array("in","1,5,6"),"createtime"=>array(array("elt",strtotime(date('Y-m-d',strtotime(date('Y-m-d'))))),array("egt",strtotime('-1 day')))))->sum("money");	//今日充值

		if($more["jiangjin"]==" "){$more["jiangjin"]="0.00";}
		if(empty($more["jinjiangjin"])){

			$more["jinjiangjin"]="0.00";}



		$this->assign("more",$more);




		$this->meta_title = '奖金累计';
		$this->display();
	}



	public function bonusCollect(){

		$bonusCollect=M()->query("select sum(bonus1) b1,sum(bonus3) b3,sum(bonus4) b4,sum(total) t,sum(taxmoney) tax from zx_bonus_total");
		$this->assign('_list',$bonusCollect[0]);
		$this->assign('all',$bonusCollect[0]['b1']+$bonusCollect[0]['b3']+$bonusCollect[0]['b4']);

		$this->meta_title = '奖金汇总';
		$this->display();
	}

	/**
	 * 奖金统计
	 */
	public function bonusCount(){
		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
		$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
		$maps['usernumber'] = $usernumber = I('usernumber');

		if(!empty($usernumber)){
			$numwhere['usernumber']= $usernumber ;
			$tid = M('Member')->where($numwhere)->getField('uid');
			if(!$tid){
				$this->error('用户不存在！');
			}else{
				$map['touserid']= $tid ;
			}
		}

		$starttime = strtotime("$starttime 00:00:00");
		$endtime = strtotime("$endtime 23:59:59");



		$map['count_date'] = array(array('gt',$starttime),array('lt',$endtime)) ;


		$bonus_list=$this->lists("BonusCount",$map,$maps);
		$list = $this->listFile($bonus_list);
		$this->assign('_list',$list);

		$this->searchCondition($maps);

		$this->meta_title = '奖金统计';
		$this->display();
	}

	/*
	 * 奖金明细
	*/
	public function  showMore(){

		$maps['changetype'] = $changetype = I('changetype',0);

		$targetuserid = I('targetuserid',0);
		$createtime = I('createtime',0);
		$maps['createtime'] = $createtime;

		$starttime = date("Y-m-d 00:00:00", $createtime);
		$endtime = date("Y-m-d 23:59:59", $createtime);
		$starttime = strtotime($starttime);
		$endtime = strtotime($endtime);

		$map['targetuserid']=$maps['targetuserid'] =$targetuserid ;
		$map['createtime'] = array(array('gt',$starttime),array('lt',$endtime)) ;
		$map['status'] = 1 ;
		$map['recordtype'] = 1 ;
		$map['money'] = array('gt',0) ;
		$map['moneytype'] = array('in','1,2,3');
		if($changetype){
			$map['changetype'] = $changetype;
		}else{
			$map['changetype'] = array('in','1,2,3,4,5,6');
		}
		$model = M('MoneyChange');
		$list = $this->lists('MoneyChange',$map,$maps,'createtime desc');

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

		$this->assign ( '_list', $list );

		$this->meta_title = '奖金明细';
		$this->display ();

	}

	/**
	 * 公司财务
	 */
	public function corporateFinance(){
		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
		$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));

		$starttime = strtotime("$starttime 00:00:00");
		$endtime = strtotime("$endtime 23:59:59");


		/*实时更新数据*/
		$Model = new Model();

		//统计
		$sql = "select sum(income) as income,sum(expend) as expend
        		from zx_finance where createtime<={$endtime} and createtime>={$starttime}";
		$alist = $Model->query($sql);
		$alist[0]['id'] = 0;
		$alist[0]['createtime'] = '总计';

		$model = M('Finance');
		$map['createtime'] =$maps['createtime']= array(array('egt',$starttime),array('elt',$endtime)) ;
		$list = $this->lists('Finance',$map,$maps);
		$list = array_merge($alist,$list);
		$list = $this->financeFilter($list);

		$this->assign ( '_list', $list );
		$this->searchCondition($maps);

		$this->meta_title = '公司财务';
		$this->display();
	}

	private  function financeFilter($list){
		foreach ($list as &$v){
			$v['surplus'] = sprintf("%0.2f", $v['income']-$v['expend']);
			if(is_numeric($v['createtime'])){
				$v['createtime'] = date('Y-m-d',$v['createtime']);
			}
			if($v['income'] == 0){
				$v['outrate'] = 100.00;
			}else{
				$v['outrate'] = round($v['expend']/$v['income'],4)*100;
			}

		}
		return $list;
	}

	//搜索交易明细
	//public function search_transaction(){
	//	$model = M('MoneyChange');
	//	$name = I('name',0);
	//	dump($name);
	// }











}