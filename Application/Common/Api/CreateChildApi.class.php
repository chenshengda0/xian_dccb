<?php
namespace Common\Api;

use User\Api\UserApi;
use Common\Api\BonusApi;
use Common\Api\RelationApi;
/**
 * 生成子点位
 */
final class CreateChildApi 
{
	private static $bonusRule;
	private static $Member;
	private static $Bonus;
	private static $Relation;
	
	
	/**
	 * 初始化
	 */
	public  function __construct()
	{
		self::$Member = M('Member');
		self::$Bonus = new BonusApi();
		self::$Relation = new RelationApi();
		self::$bonusRule = get_bonus_rule();
	}
	
	/**
	 * 生成点位
	 */
	public function createChild()
	{
		$map['hascp'] = array('egt',self::$bonusRule['max_cp_money']);
		$map['status'] = 1;
		
		$count = self::$Member->where($map)->count();//满足条件的记录数
		$prequery = 2000;//每次记录数
		$pre = ceil($count/$prequery);//查询次数
		for($i=0;$i<$pre;$i++){//分批查询
			$start = $i*$prequery;
			$user=self::$Member->where($map)->field('uid,hascp')->limit($start,$prequery)->select();
			if($user){
				foreach($user as $val){
					$cid = self::autoRegister($val['uid']);
					if($cid){
						self::decHascp($val['uid'], $cid);
						self::$Bonus->activation($cid, PROJECTNUMBER);
					}
				}
				self::createChild();
			}
		}
	}
	
	/**
	 * 生成点位后扣除相应复消币
	 * @param unknown $uid
	 * @param unknown $cid
	 */
	private function decHascp($uid,$cid)
	{
		self::$Member->where(array('uid'=>$uid))->setDec('hascp',self::$bonusRule['max_cp_money']);
		$uinfo = self::$Member->where(array('uid'=>$uid))->field('uid,usernumber,hascp,allshop,hasshop')->find();
		$cinfo = self::$Member->where(array('uid'=>$cid))->field('uid,usernumber')->find();
		
		$type=array('recordtype'=>0,'changetype'=>29,'moneytype'=>5);
		$money=array('money'=>self::$bonusRule['max_cp_money'],'hasmoney'=>$uinfo['hascp']);
		money_change($type,$uinfo, $cinfo, $money);
		unset($money,$type);
		
		$data['hasshop'] = $uinfo['hasshop']+self::$bonusRule['max_cp_money'];
		$data['allshop'] = $uinfo['allshop']+self::$bonusRule['max_cp_money'];
		self::$Member->where(array('uid'=>$uid))->save($data);
		
		$type=array('recordtype'=>1,'changetype'=>29,'moneytype'=>4);
		$money=array('money'=>self::$bonusRule['max_cp_money'],'hasmoney'=>$data['hasshop']);
		money_change($type,$uinfo, $cinfo, $money);
	}
	
	/**
	 * 自动注册点位
	 * @param unknown $uid
	 * @return boolean
	 */
	private  function autoRegister($uid)
	{
		$user_tuijian=	self::$Member->where(array('uid'=>$uid))->find();
		$count = self::$Member->where(array('tuijianid'=>$uid,'reg_type'=>1))->count();
		
		$tuijiannumber=$user_tuijian['usernumber'] ;
		$tdata = self::$Relation->tRelation($tuijiannumber);
		
		$parentid = getPosition(1);
		$pdata = self::$Relation->pOneRelation($parentid);
		
		/*报单中心*/
		if($user_tuijian['isbill']==1){
			$billnumber = $tuijiannumber;
		}else{
			if($user_tuijian['billcenterid']==0){
				$billnumber = '公司';
			}else{
				$billnumber = self::$Member->where(array('uid'=>$user_tuijian['billcenterid']))->getField('usernumber');
			}
		}
		$bdata = self::$Relation->billBelong($billnumber);
		

		/**********************用户自动注册  开始*********************************/
		$usernumber = $user_tuijian['usernumber']."-".($count+1);
		$realname = $user_tuijian['realname']."-".($count+1);
    	$userdata = array(
    		'usernumber' => $usernumber,
    		'realname'=>$realname,
    		'reg_type' =>1,
    		'isbill'=>0,
    		'psd1' => '123456',
    		'psd2' =>'123456',
    		'reg_uid' =>$uid,
    		'isadmin'=>false,//是否为管理员。默认无
    					
    	);
    	
    	/*判断是否存在推荐关系*/
    	if(isset($tdata)){
    		if($tdata['code']>0){
    			unset($tdata['code']);
    			$userdata = array_merge($userdata, $tdata);
    		}else{
    			return false;
    		}
    	}
    	
    	/*判断是否存在接点关系*/
    	if(isset($pdata)){
    		if($pdata['code']>0){
    			unset($pdata['code']);
    			$userdata = array_merge($userdata, $pdata);
    			$parentid = $userdata['parentid'];
    			$zone = $userdata['zone'];
    		}else{
    			return false;
    		}
    	}
    	
    	/*判断是否有报单中心*/
    	if(isset($bdata)){
    		if($bdata['code']>0){
    			unset($bdata['code']);
    			$userdata = array_merge($userdata, $bdata);
    		}else{
    			return false;
    		}
    	}
    	
		$User = new UserApi;
		$cid = $User -> register($userdata);
		if($cid>0){
			if(isset($parentid)){
				$pznum = self::$Member->where(array('uid'=>$parentid))->getField('znum');
				$data['znum'] = $pznum+1;
				if(isset($zone)){
					if($zone==1){
						$data['left_zone'] = 1; //左区被占
					}
					if($zone==2){
						$data['right_zone'] = 1;//右区被占
					}
				}
				self::$Member->where(array('uid'=>$parentid))->save($data);
			}
			return $cid;
		} else { //注册失败，显示错误信息
			return false;
		}
	}
}