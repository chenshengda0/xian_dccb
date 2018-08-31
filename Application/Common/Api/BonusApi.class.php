<?php
namespace Common\Api;
use User\Api\UserApi;
//use Common\Api\UpdateApi;

final class BonusApi
{
	private static $bonusRule;
	private static $Member;

	/**
	 * 初始化
	 */
	public  function __construct()
	{
		self::$Member = M('Member');
		self::$bonusRule = get_bonus_rule();
	}

	/**
	 * 奖金测试
	 */
	public  function test()
	{
		echo PROJECTNUMBER;
	}


	/**
	 * 会员激活
	 * @param int $uid 被激活会员id
	 * @param int $active_id 激活会员id
	 */
	public  function activation($uid,$active_id)
	{

		if($active_id==PROJECTNUMBER){
			$res =  self::adminActive($uid);
		}else{
			$res = self::memberActive($uid, $active_id);
		}

		if($res>0){
			$user = self::$Member->find($uid);
			if($user['status']==1){




			}
		}

		return $res;
	}
	/**
	 * 获取会员报单金额
	 */
	public function billMoney($user)
	{

		if(C("IS_MORE_LEVEL")){
			$bill_money = get_user_level_moeny($user['userrank'], self::$bonusRule, 'level_money');
		}else{
			$bill_money = self::$bonusRule['money'];
		}

		return $bill_money;
	}
	//月报表
	public function month($money){
	    $year = date('Y');
	    $month = date('n');
	    $time = mktime(0,0,0,$month,1,$year);
	    $result = M('month')->where(array('time'=>$time))->find();
	    if($result){
	        M('month')->where(array('id'=>$result['id']))->setInc('money',$money);
	    }else{
	        M('month')->add(array('money'=>$money,'time'=>$time));
	    }


	}
	//$id  城市id

	public function dl_month($id){

	    $result = M('daili')->where(array('areaid'=>$id))->find();
	    if($result){
	        M('daili')->where(array('iid'=>$result['iid']))->setInc('renshu',1);
	    }
	}
    /**
 * 获取会员报单电子金额
 */
    public function ElectronMoney($user)
    {
        if(C("IS_MORE_LEVEL")){
            $electron_money = get_user_level_moeny($user['userrank'], self::$bonusRule, 'electron_money');
        }else{
            $electron_money = self::$bonusRule['money'];
        }

        return $electron_money;
    }
    /**
     * 获取会员报单注册金额
     */
    public function RegisterMoney($user)
    {
        if(C("IS_MORE_LEVEL")){
            $register_money = get_user_level_moeny($user['userrank'], self::$bonusRule, 'register_money');
        }else{
            $register_money = self::$bonusRule['register_money'];
        }

        return $register_money;
    }
	/**
	 * 会员激活
	 * @param int $uid
	 * @param int $active_id
	 */
	private  function memberActive($uid,$active_id){

		$billtype = 2;//电子币
		$billtype2 = 1;//注册币
		$billfield = 'hasbill';//报单币余额
        $hasmoney = 'hasmoney';//奖金币余额
		$rule  = self::$bonusRule;

		$info = self::$Member->find($uid);

		$active_info = self::$Member->find($active_id);
        $register_register_money = self::RegisterMoney($info);//获取报单所需要的注册币金额
		$register_electron_money = self::ElectronMoney($info);//获取报单所需要的电子币金额
		$user_hasbill = $active_info[$billfield];//当前用户报单币余额
        $user_hasmoney = $active_info[$hasmoney];//当前用户奖金币余额
		if($register_electron_money>$user_hasmoney){
			return -100;//报单电子币不足
		}
        if($register_register_money>$user_hasbill){
            return -200;//报单注册币不足
        }

		// 激活用户需要更新字段，激活字段=1;
		$data['status'] = 1 ;
		$data['active_uid'] = $active_id;
		$data['active_time'] = time();
		//$data['bill_money'] = $register_money;
       // $data['bill_money'] = $register_money;
        $data['bill_money'] = $register_electron_money+$register_register_money;
		//激活用户
		$res = self::$Member->where(array('uid'=>$uid))->save($data);

		if($res){
			$res1 = self::$Member->where(array('uid'=>$active_id))->setDec($billfield,$register_register_money);
            $res2 = self::$Member->where(array('uid'=>$active_id))->setDec($hasmoney,$register_electron_money);
			if($res1 && $res2 ){
				$hasmoney =self::$Member->where(array('uid'=>$active_id))->getField($hasmoney);
                $billfield =self::$Member->where(array('uid'=>$active_id))->getField($billfield);
				$type=array('recordtype'=>0,'changetype'=>20,'moneytype'=>$billtype);
				$type2=array('recordtype'=>0,'changetype'=>20,'moneytype'=>$billtype2);
				$money=array('money'=>$register_electron_money,'register_money'=>$register_register_money,'hasmoney'=>$hasmoney,'taxmoney'=>0);
				$money2=array('money'=>$register_register_money,'hasmoney'=>$billfield,'taxmoney'=>0);
				money_change($type, $active_info, $info, $money);
				money_change($type2,$active_info,$info,$money2);
			}else{
				self::$Member->where(array('uid'=>$uid))->setField('status',0);
				return  -101;//报单费扣除失败
			}

			self::$Member->where(array('uid'=>$info['tuijianid']))->setInc('recom_num',1);
			self::$Member->where(array('uid'=>$info['tuijianid']))->setField('last_rec_time',time());

			return $res;
		}else{
			return -102;//状态更改失败
		}
	}
	//代理商收益
	/*public function dl_shouyi($user,$qian){
	    //查到他属于哪个市
	    $city = $user['city'];
	    //查这个市的代理
	    $dl_id = M('daili')->where(array('areaid'=>$city))->getField('userid');
	    $daili = self::$Member->where(array('uid'=>$dl_id))->find();
	    $dl_money = get_bonus_rule('dl_money');
	    if($dl_id){
	        //给这个人
	        $bonus = $qian*$dl_money*0.01;
	        $data['hasmoney'] = $daili['hasmoney']+bonus;
	        $result = self::$Member->where(array('uid'=>$daili['uid']))->save($data);
	        moneyChange(1,15,$daili,$user,$bonus,$data['hasmoney'],0,2);
	    }
	}*/
	//会员定星
	public function add_suanli($uid,$suanli){

	    $user = self::$Member->where(array('uid'=>$uid,'status'=>1))->find();
	    $tuijianids = $user['tuijianids'].$uid;
	    $where['uid'] = array('in',$tuijianids);
	    $where['status'] = 1;
	    $result = self::$Member->where($where)->order('uid')->select();

	    foreach ($result as $k=>$v){
	        $where = array();
	        $where['uid'] = $v['uid'];
	        $where['status'] = 1;
          	if($suanli){
            	$result1 = self::$Member->where($where)->setInc('suanli',$suanli);
            }else{
            	$result1 = true;
            }
	        if($user['tuijianid']==$v['uid']){
	           	if($suanli){
                	$result2 = self::$Member->where($where)->setInc('zt_suanli',$suanli);
                }else{
                	$result2 = true;
                }
	            if($result1 && $result2){
	                self::dingxing($v['uid']);
	            }
	        }else{
	            if($result1){

	                self::dingxing($v['uid']);
	            }
	        }

	    }
	}
	//判断定星条件
	public function dingxing($uid){
      	$user = self::$Member->where(array('uid'=>$uid))->find();
      	$count=self::$Member->where(array("tuijianid"=>$uid,"shenpi"=>1))->count(); //直推人数
        $hasman=self::$Member->where(array("tuijianids"=>array("like","%,".$uid.",%"),"shenpi"=>1))->count(); //团队人数
        $ones=self::$Member->where(array("tuijianids"=>array("like","%,".$uid.",%"),"shenpi"=>1,"userrank"=>1))->count(); //团队人数
        $twos=self::$Member->where(array("tuijianids"=>array("like","%,".$uid.",%"),"shenpi"=>1,"userrank"=>2))->count(); //团队人数
        $threes=self::$Member->where(array("tuijianids"=>array("like","%,".$uid.",%"),"shenpi"=>1,"userrank"=>3))->count(); //团队人数
	    //获取定星条件
	    $xing[1] = explode(',',self::$bonusRule['yixing']);//一星条件
	    $xing[2] = explode(',',self::$bonusRule['erxing']);
	    $xing[3] = explode(',',self::$bonusRule['sanxing']);
	   // $xing[4] = explode(',',self::$bonusRule['sixing']);

	    if($count>=$xing[$user['userrank']][0] &&$hasman>=$xing[$user['userrank']][1]&& $user['suanli']>=$xing[$user['userrank']][2]){

	        if($user['userrank']>=2){//2代表一星会员
	             //如果是一星会员以上需要判断网体下是否有其他星级会员
	             $where['tuijianid'] = $uid;
	             $where['status'] = 1;

	             $zhitui = self::$Member->where($where)->select();
	             $num=array();
                 $rank = $user['userrank'];
	             foreach($zhitui as $k=>$v){

	                 $where=array();
	                 $where['tuijianids'] = array('like','%,'.$v['uid'].',%');
	                 $where['status'] = 1;
	                 if($v['userrank']>=$rank){
	                     $num[$k]++;
	                 }
	                 if($user['userrank']==2){//如果是二星会员
	                     $where['userrank'] = 2;
	                     $count = self::$Member->where($where)->count();//一星矿主人数
	                     if($count){
	                         $num[$k]+=$count;
	                     }
	                     $a = 2;

	                 }elseif($user['userrank']==3){//如果是三星会员
	                     $where['userrank'] = 3;

	                     $count = self::$Member->where($where)->count();//二星矿主人数

	                     if($count){
	                         $num[$k]+=$count;
	                     }
	                     $a = 3;

	                 }
	             }
                  $total = array_sum($num);

	             if($total>=$xing[$user['userrank']][$a] &&  count($num)>=$xing[$user['userrank']][7]){//如果星级会员打到三个以上

                     $data['userrank'] = $user['userrank']+1;
	                 $result = self::$Member->where(array('uid'=>$uid,'status'=>1))->save($data);

	                 if($result){
                         unset($num);
	                     self::fa_kuangji($uid);
	                 }

	             }
	        }else{
	             $result = self::$Member->where(array('uid'=>$user['uid'],'status'=>1))->setField('userrank',2);
              	 if($result){
                   	 unset($num);
	                 self::fa_kuangji($user['uid']);
	             }
	        }
	    }
	}

    /**
     * 发矿机
     * @param $uid 升级用户
     */
  		public function  fa_kuangji($uid){
            $user = self::$Member->where(array('uid'=>$uid,'status'=>1))->find();
            switch ($user['userrank']){
                case 2:
                    $id=self::$bonusRule['xiao'];break;
                case 3:
                    $id=self::$bonusRule['zhong'];break;
                case 4:
                    $id=self::$bonusRule['da'];break;

            }
            $list = M('shop')->where(array('id'=>$id,'status'=>1))->find();
            $data['kuangjiid']= $id;
            $data['userid'] = $uid;
            $data['ttime'] = 0;
            $data['ccreatetime'] = time();
            $data['pprevtime'] = 0;
            $data['ssuanli'] =  $list['suanli'];
            $data['danwei'] = $list['chanliang'];
            if(M('kuangji')->add($data)){

                self::add_suanli($uid,$data['ssuanli']);
            }

	}
	//动态拿三级
	//$user  $money用户收取的eve
	public function dongtai($user,$chanliang){
        $bouns = get_bonus_rule();
        $dongtai = explode(',', trim($bouns['dt_money'], ','));                //动态奖励规则
        $pids_arr = array_reverse(explode(',', trim($user['tuijianids'], ',')));    //发奖人数
		
        for ($i=0;$i<count($dongtai);$i++) {//推荐人数走
          
            $tjr = M("member")->where(array("uid" => $pids_arr[$i], "status" => 1,"ytf"=>array("gt",0)))->find();

            $tjzr= M("member")->where(array("tuijianid" => $pids_arr[$i],"ytf"=>array("gt",0)))->count();
      

       		$op=$i+1;
          	
            if($tjzr>=$op){
           
                $bonus = $dongtai[$i] * $chanliang * 0.01;
                $data['hasmoney'] = $tjr['hasmoney'] + $bonus;
                $data['allmoney'] = $tjr['allmoney'] + $bonus;
                $data['allbonus'] = $tjr['allbonus'] + $bonus;
              	$res = self::$Member->where(array('uid' => $tjr['uid'], 'status' => 1))->save($data);
           
                if ($res) {
                    self::bonusCount(6, $tjr, $bonus, $data['hasmoney'], 0, 0);
                    $type = array('recordtype' => 1, 'changetype' => 6, 'moneytype' => 2);
                    $money = array('money' => $bonus, 'hasmoney' => $data['hasmoney'], 'taxmoney' => 0);
                    money_change($type, $tjr, $user, $money);

                }

            }

        }
	}
	//矿机每小时结算一次收益
	public function  jiesuan(){
	    $time = 1*60*60;
	    $model = new \Think\Model();
	    $sql = "update zx_kuangji set cchanliang=cchanliang+danwei,allcchanliang=allcchanliang+danwei,ttime=ttime+$time,allttime=allttime+$time where sstatus=1";

	    $result = $model->execute($sql);

	    //判断报废
	    $endtime = 720*60*60;
	    $shijian = 2*60*60;
	    $kuangji = M('kuangji')->where(array('sstatus'=>1))->select();

	    foreach($kuangji as $k=>$v){

	        if($v['allttime']>=$endtime){
	            //这台矿机报废
	            $res = M('kuangji')->where(array('iid'=>$v['iid'],'sstatus'=>1))->setField('sstatus',3);
	        }else if($v['ttime']==$shijian){

	            //vip自动收益
	            $uinfo = self::$Member->where(array('uid'=>$v['userid']))->find();

	            if($uinfo['is_vip']){
	                //改变该矿机的收益为0运行时间为0
	                //$data['cchanliang']=0;
	                $data['ttime'] = 0;
	                $data['stoptime'] = time();
	                M('kuangji')->where(array('iid'=>$v['iid']))->save($data);
	                //给用户加产量
	               /* $data1['hasmoney'] = $uinfo['hasmoney']+$v['cchanliang'];
	                $data1['allhasmoney'] = $uinfo['allhasmoney']+$v['cchanliang'];
	                self::$Member->where(array('uid'=>$v['userid']))->save($data1);*/
	                //流水
	               /* $bonus = new BonusApi();
	                $bonus->dongtai($uinfo,$v['cchanliang']);
	                $bonus->bonusCount(2, $uinfo,$v['cchanliang'],$data1['hasmoney'],0,0);
	                $type=array('recordtype'=>1,'changetype'=>2,'moneytype'=>2);
	                $money=array('money'=>$v['cchanliang'],'hasmoney'=>$data1['hasmoney'],'taxmoney'=>0);
	                money_change($type, $uinfo, get_com(), $money);*/
	            }
	        }
	    }
	}
	//二十分钟查看一下所有交易中的订单  如果是交易中判断买家12小时内是否打款,如果是打款了判断卖家是否在12小时内确认收款
	public function dingdan(){
	    $model = new \Think\Model();
	    $sql = 'select * from zx_jiaoyi where status=2 or status=3';
	    $result = $model->query($sql);
	    $newtime = time();
	    //12小时未打款
	    $pp = get_bonus_rule('pp_time')*60*60;
	    //12小时未收款
	    $dk = get_bonus_rule('dk_time')*60*60;
	    foreach ($result as $K=>$v){
	        $myinfo = self::$Member->where(array('uid'=>$v['myid']))->find();
	        $youinfo = self::$Member->where(array('uid'=>$v['youid']))->find();
	        if($v['status']==2){//如果是交易中的订单
	            $jiange = $newtime-$v['time'];//匹配多久了
	            if($jiange>=$pp){//如果大于12小时冻结买家   钱返给卖家  订单显示超时未打款
	                //(1)冻结买家
	                $info['status'] = -1;
	                $info['info'] = '作为买家未及时付款['.$v['myname'].']';
	                self::$Member->where(array('uid'=>$v['youid']))->save($info);
	                //(2) 钱返给卖家
	                $total = $v['shuiliang']+$v['shouxu'];
	                $res2 = self::$Member->where(array('uid'=>$v['myid']))->setInc('hasmoney',$total);
	                if($res2){
	                    //写流水
	                    moneyChange(1,14,$myinfo,$youinfo,$total,$v['hasmoney']+$total,0,2);
	                    //(3)订单显示超时未打款
	                    $data['status'] = 8;//交易中买家未付款
	                    $data['youtime'] = time();
	                    M('jiaoyi')->where(array('id'=>$v['id']))->save($data);
	                }
	            }
	        }else if($v['status']==3){//如果是已经打款的订单
	            $jiange = $newtime-$v['dk_time'];//打款多久了
	            if($jiange>=$dk){//如果大于12小时未确认收款 订单进入异常交易
	                if($v['status']==3){//如果状态还是付款中则冻结订单
	                      $where['id'] = $v['id'];
	                      $where['status'] = 3;
	                      $data['status']=6;//冻结订单
	                      $data['youtime'] = time();
	                      M('jiaoyi')->where($where)->save($data);

	                }
	            }
	        }
	    }
	}
   //检查vip是否过期
	public function vip_day(){
	    $where['is_vip'] = 1;
	    $result = self::$Member->where($where)->select();
	    $time = time();
	    foreach($result as $k=>$v){
             if($v['vip_end']<=$time){
                 self::$Member->where(array('uid'=>$v['uid']))->setField('is_vip',0);
             }
	    }
	}
	/**
	 * 管理员激活
	 * @param int $uid
	 */
	private  function adminActive($uid){

		$info = self::$Member->field('usernumber,userrank,tuijianid')->find($uid);
		// 激活用户需要更新字段，激活字段=1;
		$data['status'] = 1 ;
		$data['active_time'] = time();
		$data['bill_money'] = self::billMoney($info);
		//激活用户
		$map['uid']  = $uid;
		$res = self::$Member->where($map)->save($data);
		if($res){
			self::$Member->where(array('uid'=>$info['tuijianid']))->setInc('recom_num',1);
			self::$Member->where(array('uid'=>$info['tuijianid']))->save(array('last_rec_time'=>time(),));
			return $res;
		}else{
			return -102;//状态更改失败
		}
	}
	/**
	 * 推荐几人反几层推荐奖：类型为:1
	 * 币种：奖金币:2
	 * @param array $user
	 */
	private  function divid($user)
	{
        $level_recom_rate=user_level_bonus_format(self::$bonusRule['recom_money']);
      //  $tuijianids_arr=array_reverse(explode(',', trim($user['parentids'],',')));
        $tinfo=self::$Member->where(array('uid'=>$user['tuijianid'],'status'=>1))->find();

        if ($tinfo)
        {
            $recom_money=$level_recom_rate;
            self::bonusShare($recom_money, 1, $user['tuijianid'], $user);
        }
        unset($tinfo,$recom_money);

	}
    /**
     * 推荐奖：类型为:1
     * 币种：奖金币:2
     *
     */
    private function bonusRecom($user)
    {
        //$level_recom_rate=user_level_bonus_format(self::$bonusRule['recom_money']);
        $level_recom_rate=self::$bonusRule['recom_money'];
        $tinfo=self::$Member->where(array('uid'=>$user['tuijianid'],'status'=>1))->find();
        if($tinfo){
           // $recom_money=$level_recom_rate[$tinfo['userrank']][2];
            $recom_money=$level_recom_rate;
            self::bonusShare($recom_money, 1, $user['tuijianid'], $user);
        }
        unset($tinfo,$recom_money);
    }
    /*多级直推奖*/
    public  function bonusRecom1($user,$billmoney)
    {
        $level_recom_rate=user_level_bonus_format(self::$bonusRule['recom_money']);
        $tuijianids = $user['tuijianids'];
        $where['uid'] = array('in',$tuijianids);
        $where['status'] =1;
        $where['tdeep'] = array('egt',$user['tdeep']-3);
        $fa_jiang = self::$Member->where($where)->order('uid desc')->select();

        foreach($fa_jiang as $k=>$v){
            $recom_money = $billmoney*$level_recom_rate[$k+1][2];
            self::bonusShare($recom_money, 1, $v['uid'], $user);
        }

    }
    /*见点奖*/
    public function jd_money($user,$billmoney){
        $jd_yi = self::$bonusRule['jd_yi'];
        $jd_er = self::$bonusRule['jd_er'];
        $yi_ceng = self::$bonusRule['yi_ceng'];
        $er_ceng = self::$bonusRule['er_ceng'];
        $rules[$jd_yi] = $yi_ceng;
        $rules[$jd_er] = $er_ceng;
        $jd_money = self::$bonusRule['jd_money'];
        $tuijianids = $user['tuijianids'];
        $tdeep = $user['tdeep'];
        $where['uid'] = array('in',$tuijianids);
        $where['status'] =1;
        $fa_jiang = self::$Member->where($where)->order('uid desc')->select();
        foreach($fa_jiang as $k=>$v){
            if($v['tdeep']>=$tdeep-$rules[$v['recom_num']]){
                $bonus = $billmoney*$jd_money*0.01;

                self::bonusShare($bonus , 2, $v['uid'], $user);
            }
        }
    }
    //每天返积分
    public function divid_jifen(){
        $jifen = get_bonus_rule('bill_money');
        $where['status'] = 1;
        $result = self::$Member->where($where)->select();
        foreach($result as $k=>$v){

            self::bonusShare($jifen , 4, $v['uid'], get_com(),3);

        }
    }
    //报单中心 极差奖
    public function jc_money($user,$billmoney){
        $level_recom_rate=user_level_bonus_format(self::$bonusRule['bd_money']);
        $billcenterid = $user['billcenterid'];
        //报单中心信息
        $billcenter = self::$Member->where(array('uid'=>$billcenterid))->find();
        //要发极差奖的id
        $billids = $billcenter['tuijianids'].$billcenterid;
        //组件条件

        $where['uid'] = array('in',$billids);
        $where['isbill'] = array('egt',1);
        $result = self::$Member->where($where)->order('uid desc')->select();
        $prevbill=10;
        $prev = 0;

        foreach($result as $k=>$v){
            if($v['isbill']<$prevbill){
                $bonus = ($level_recom_rate[$v['isbill']][2]-$prev)*$billmoney;
                self::bonusShare($bonus, 3, $v['uid'], $user);

                $prevbill = $v['isbill'];
                $prev = $level_recom_rate[$v['isbill']][2];
            }
        }

    }
    /**
     * 增加所有父节点成就
     * @param array $user
     * @param double $money
     */
    private function achievement($user,$money)
    {
        if($money<=0){
            return ;
        }
        $pidareas = parentids_format($user['parentareas']);
        if(isset($pidareas['array_1'])){
            $map['uid'] = array('in',$pidareas['array_1']);
            self::$Member->where($map)->setInc('left_bill',$money);
            self::$Member->where($map)->setInc('left_bill_all',$money);
        }
        if(isset($pidareas['array_2'])){
            $map['uid'] = array('in',$pidareas['array_2']);
            self::$Member->where($map)->setInc('right_bill',$money);
            self::$Member->where($map)->setInc('right_bill_all',$money);
        }
    }

    /*
     * 领导奖 类型为:3
         * 币种：奖金币:2
     *
     */
    private function leader($user)
    {
        $tuijianids_arr=array_reverse(explode(',', trim($user['parentids'],',')));
        $tuijianids_arr=array_slice($tuijianids_arr,1,2);
        $leve1=$tuijianids_arr[0];
        $leve2=$tuijianids_arr[1];
        $level1_amount_rate=self::$bonusRule['leader_money'];//见点奖(领导将)//一代
        $level2_amount_rate=self::$bonusRule['leader_money2'];//见点奖(领导将)//一代
        foreach ($tuijianids_arr as $tuijian)
        {
            $map['uid']=$tuijian;
            $map['status']=1;
            $tuijian_pinfo = self::$Member->where($map)->find();
            if ($tuijian_pinfo) {
                if($tuijian_pinfo['uid']==$leve1)
                {
                  // $level_amount_rate = $level1_amount_rate;
                    $level_amount_rate = $level1_amount_rate;
                }else
                {
                    $level_amount_rate = $level2_amount_rate;
                }
                $money_sum = $level_amount_rate;

                self::bonusShare($money_sum, 3, $tuijian, $user);
            }
        }
    }

	/**
	 * 推荐几人反几层推荐奖：类型为:1
	 * 币种：奖金币:2
	 * @param array $user
	 */
	private  function reinvest($user,$money)
	{
		$tuser=self::$Member->where(array('uid'=>$user['tuijianid'],'status'=>1))->find();
		if($tuser){
			$puser=$user;
			$usernumber=$tuser['usernumber'].'-'.$tuser['recom_num'];
			if($tuser['tuijianids']){
				$tuijianids=$tuser['tuijianids'].$tuser['uid'].',';
			}else{
				$tuijianids=','.$tuser['uid'].',';
			}
			if($puser['parentids']){
				$parentids=$puser['parentids'].$puser['uid'].',';
			}else{
				$parentids=','.$puser['uid'].',';
			}
			$max_oid=M('member')->max('oid');
			$oid=$max_oid+1;
			$userdata=array(
				'usernumber' => $usernumber,
				'realname'=>$tuser['realname'],
				'userrank' => $tuser['userrank'],
				'oldrank' => $tuser['userrank'],
				'reg_type' =>$tuser['userrank'],
				'isbill'=>0,
				'borth' => $tuser['borth'],

				'tuijianid' => $tuser['uid'],
				'tuijiannumber' => $tuser['usernumber'],
				'tuijianids' => $tuijianids,
				'tdeep' => ($tuser['tdeep']+1),
				'parentid' => $puser['uid'],
				'parentnumber' => $puser['usernumber'],
				'parentids' => $parentids,
				'pdeep' => ($puser['pdeep']+1),
				'isbill' => 0,
				'oid' => $oid,

				'bill_money'=>$money,
				'psd1' => $tuser['psd1'],
				'psd2' => $tuser['psd2'],
				'active_time'=>time(),
				'reg_uid' =>$tuser['uid'],
				'proxy_state'=>1,
				'isadmin'=>false,//是否为管理员。默认无
			);
			$User = new UserApi();
			$uid = $User-> register($userdata);
			if($uid){
				$data=array(
						'mobile'=>$tuser['mobile'] ,
						'email' => $tuser['email'],
						'bankname' =>$tuser['bankname'],
						'IDcard' =>$tuser['IDcard'],
						'banknumber' =>$tuser['banknumber'],
						'bankholder' =>$tuser['bankholder'],
						'status' =>1,
				);
				self::$Member->where(array('uid'=>$uid))->save($data);
			}
		}

		unset($user,$tuser);
	}
	/**
	 * 推荐几人反几层推荐奖：类型为:1
	 * 币种：奖金币:2
	 * @param array $user
	 */
	private  function out($user){
		$map['bid']=array('egt',1);//复投的不参与推人，参与拿出局奖
		$n=self::$Member->where($map)->count()-1;
		$m=$n%2;
		if($m==0){//出局
			$k=$n/2;
			$outUser=self::$Member->where(array('oid'=>$k,'status'=>1))->find();
			if($outUser){
				$map['status']=1;
				$map['realname']=$outUser['realname'];
				$map['mobile']=$outUser['mobile'];
				$map['IDcard']=$outUser['IDcard'];
				$minuid=self::$Member->where($map)->min('uid');
				if($outUser['uid']!=$minuid){
					$outUser=self::$Member->where(array('uid'=>$minuid))->find();
				}
				$recom_money=self::$bonusRule['recom_money'];
				self::bonusShare($recom_money, 1, $outUser['uid'], $user);
			}
		}
	}
	/**
	 * 推荐几人反几层推荐奖：类型为:1
	 * 币种：奖金币:2
	 * @param array $user
	 */
//	private  function bonusBill($user){
//		$binfo=self::$Member->where(array('uid'=>$user['billcenterid'],'status'=>1))->find();
//		if($binfo){
//			$bill_money=self::$bonusRule['bill_money'];
//			if($bill_money){
//				self::bonusShare($bill_money, 3, $binfo['uid'], $user);
//			}
//		}
//	}
    /**
     * 股东分红
     * 币种：分红：4
     */
    public function members($user)
    {
        $map['uid']=$user['uid'];
        $info=self::$Member->where($map)->find();  //根据激活人uid查出parentids
        $tuijianids_arr=explode(',', trim($info['parentids'],','));
        foreach ($tuijianids_arr as $v)
        {
            $map1['parentids']=array("like","%,$v,%");
            $map2['tuijianid']=$v;
            $sel=self::$Member->where($map1)->select();
            $num1=self::$Member->where($map2)->count(); //直推人数
            $num2=count($sel)+1;  //团队人数
            /*echo $num1."<br>";
            echo $num2."<br>";*/
            if ($num1>=30  && $num2>=200)
            {
                $lev=2;
                $map3['uid']=$v;   //团队领头人id
                $this->updateLevel($map3,$lev);

            }
            elseif ($num1>=100  && $num2>=1000 )
            {
                $lev=3;
                $map3['uid']=$v;
                $this->updateLevel($map3,$lev);

            }
            elseif ($num1>=150 && $num2>=2000 ) {
                $lev = 4;
                $map3['uid'] = $v;
                $this->updateLevel($map3, $lev);

            }
        }
    }
    //变更等级
    private function updateLevel($map,$lev)
    {
        $data['update_time']=time();
        $data['userrank']=$lev;
        $data['proxy_state']=2;
        $result=self::$Member->where($map)->find();
        if ($result['userrank']==$lev) {
            return false;
        }else{
            $res=self::$Member->where($map)->save($data);
        }
        if ($res) {
            return true;
        }else{
            return false;
        }
    }
    public function fenhong()
    {
        $tree=array();
        $person=array();
        $b=array();
        $proxy['proxy_state']=2;
        $result=self::$Member->group('userrank')->where($proxy)->select(); //查询所有有分红标示 的会员
        //查询上个月的业绩量
        $starttime=strtotime(date('Y-m-01', strtotime('-1 month')));
        $endtime=strtotime(date('Y-m-t', strtotime('-1 month')));
        //$starttime = strtotime(date('Y-m-01'));
        //$endtime = strtotime(date("Y-m-t"));
        $time['createtime'] = array(array('gt', $starttime), array('lt', $endtime));
        $income = M('finance')->where($time)->sum('income');
        $expend = M('finance')->where($time)->sum('expend');
        $bouns = $income - $expend;

        $day_roof_arr = user_level_bonus_format(self::$bonusRule['divid_money']);

        foreach ($result as $k=>$v)
        {
            $id['uid']=$tree=$v['uid'];
            $user=self::$Member->where($id)->find();
            $userrank['userrank']=$user['userrank']; //顺序为2,3,4
            $result1=self::$Member->where($userrank)->select();  //统计各等级人数
            foreach ($result1 as $val)
            {
                $person[]=$val['uid'];  //各个等级的id
            }
            foreach ($person as $value)
            {
                $a['uid']=$value;
                $res=self::$Member->where($a)->find();
                $update_time=$res['update_time']; //晋升时间
                $today_time=time();
                $D=date('m',$today_time);
                $d=date('m',$update_time);
                if (($D-$d)<0) {
                    $d=$d-12;
                }
                if (($D-$d)>1) {
                    $b[]=$a['uid'];
                }
            }
            $num=count($b);
            $day_roof_money = $day_roof_arr[$user['userrank']][2] * 0.01;
            $bonusmoney = ($bouns * $day_roof_money) / $num;
            foreach ($b as $key=>$valu) {
                $c = $valu;
                if ($bonusmoney) {
                    self::bonusShare($bonusmoney, 4, $c, 1);
                }
            }
    unset($b);    }
    }

    /**
	 * 奖金生成
	 * @param double $bonusmoney 奖金金额
	 * @param int $changetype 奖金类型
	 * @param int $uid 获得奖金的会员编号
	 * @param array $fromuser 奖金来源会员编号或因该会员而产生奖金
	 * @return boolean    $res
	 */
	public  function bonusShare($bonusmoney,$changetype,$uid,$fromuser,$moneytype=2)
	{

		$map['uid'] = $uid;
		$user=self::$Member->where($map)->find();
		if($changetype==4){
		    $data = array();
		    $data['hascp'] = $user['hascp']+$bonusmoney;
		    $data['allcp']=$user['allcp']+$bonusmoney;
		    $res = self::$Member->where($map)->save($data);
		    if($res){

		        $type=array('recordtype'=>1,'changetype'=>$changetype,'moneytype'=>$moneytype);
		        $money=array('money'=>$bonusmoney,'hasmoney'=>$data['hascp'],'taxmoney'=>0);
		        money_change($type, $user, $fromuser, $money);
		        unset($type,$money);
		    }
		}else{
		    $data = array();
		    $tax_rate = self::$bonusRule['tax_rate'];
		    if($tax_rate>100||$tax_rate<=0){
		        $tax = 0;
		    }else{
		        $tax = $tax_rate*0.01;
		    }
		    //		$cp_rate = self::$bonusRule['cp_rate'];
		    // 		if($cp_rate>100||$cp_rate<=0){
		    // 			$cp = 0;
		    // 		}else{
		    // 			$cp = $cp_rate*0.01;
		    // 		}
		    $tax_money = $bonusmoney*$tax ;  //奖金税
		    $cp_money = 0 ;  //复销
		    $hasmoney = $bonusmoney - $tax_money;
		    $data['hasmoney'] = $user['hasmoney']+$hasmoney;
		    $data['allmoney']=$user['allmoney']+$hasmoney;
		    // 		$data['hascp'] = $user['hascp']+$cp_money;
		    // 		$data['allcp']=$user['allcp']+$hasmoney;
		    $data['allbonus']=$user['allbonus']+$bonusmoney;
		    $res = self::$Member->where($map)->save($data);
		    if($res){
		        self::bonusCount($changetype, $user, $bonusmoney, $hasmoney,$cp_money,$tax_money);
		        $type=array('recordtype'=>1,'changetype'=>$changetype,'moneytype'=>$moneytype);
		        $money=array('money'=>$hasmoney,'hasmoney'=>$data['hasmoney'],'taxmoney'=>$tax_money);
		        money_change($type, $user, $fromuser, $money);
		        unset($type,$money);

// 		        if(isset($cp_money)){
// 		            $type=array('recordtype'=>1,'changetype'=>$changetype,'moneytype'=>3);
// 		            $money=array('money'=>$cp_money,'hasmoney'=>$data['hascp'],'taxmoney'=>0);
// 		            money_change($type, $user, $fromuser, $money);
// 		        }

		        finance($changetype, $bonusmoney, $tax_money); //公司财务统计
		    }
		}

	}

	/**
	 * 奖金统计
	 * @param int $bonustype 奖金类型
	 * @param array $touser  获得奖金的用户信息
	 * @param double $money 应得奖金金额
	 * @param double $hasmoney 应得奖金金额
	 * @param double $taxmoney 奖金税额
	 */
	public  function bonusCount($bonustype,$touser,$money,$hasmoney,$cp_money,$tax_money)
	{

		$BCount = M("BonusCount");
		$count_date=strtotime('today');

		$data=array(
				'touserid'=>$touser['uid'],
				'tousernumber'=>$touser['usernumber'],
				'count_date'=>$count_date,
		);

		$map['touserid'] = $touser['uid'];
		$map['count_date'] = $count_date;

		$count=$BCount->where($map)->find();

		$field = 'bonus'.$bonustype;
		if(!empty($count)){
			$data[$field] = $count[$field]+$money ;
			$data['cp_money']=$count['cp_money']+$cp_money;
			$data['tax_money']=$count['tax_money']+$tax_money;
			$data['total']=$count['total']+$hasmoney;
			$BCount->where($map)->save($data);
		}else{
			$data[$field]=$money;
			$data['cp_money']=$cp_money;
			$data['tax_money']=$tax_money;
			$data['total']=$hasmoney;
			$BCount->add($data);
		}

		self::bonusTotal($bonustype, $touser, $money,$hasmoney,$cp_money,$tax_money);
	}

	/**
	 * 会员奖金累计
	 * @param int $bonustype 奖金类型
	 * @param array $touser  获得奖金的用户信息
	 * @param double $money 奖金金额
	 * @param double $hasmoney 应得奖金金额
	 * @param double $taxmoney 奖金税额
	 */
	private function bonusTotal($bonustype,$touser,$money,$hasmoney,$cp_money,$tax_money)
	{

		$BCount = M("BonusTotal");
		$data=array(
				'touserid'=>$touser['uid'],
				'tousernumber'=>$touser['usernumber'],
				'count_date'=>time(),//更新时间
		);
		$map['touserid'] = $touser['uid'];
		$count=$BCount->where($map)->find();
		$field = 'bonus'.$bonustype;
		if(!empty($count)){
			$data[$field] = $count[$field]+$money ;
			$data['cp_money']=$count['cp_money']+$cp_money;
			$data['tax_money']=$count['tax_money']+$tax_money;
			$data['total']=$count['total']+$hasmoney;
			$BCount->where($map)->save($data);
		}else{
			$data[$field]=$money;
			$data['total']=$hasmoney;
			$data['cp_money']=$cp_money;
			$data['tax_money']=$tax_money;
			$BCount->add($data);
		}
	}

}