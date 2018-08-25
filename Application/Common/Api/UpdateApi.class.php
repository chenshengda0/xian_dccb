<?php
namespace Common\Api;

final class UpdateApi 
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
	 * 升级入口
	 * @param array $user
	 * @param double $money 奖金基准金额
	 */
	public  function updateIn($uid)
	{
		$user = self::$Member->where(array('uid'=>$uid))->find();
		self::updateIds($user);
	}
	
	
	/**
	 * 会员职务晋升：查询需升级的会员
	 */
	private  function updateIds($user)
	{
		$pids = array_reverse(str2arr($user['parentids']));
		$reg_type_arr = user_level_bonus_format(self::$bonusRule['update_regtype']);
		$len = count($pids);
		for($i=0;$i<$len;++$i){
			self::updateCondition($pids[$i],$reg_type_arr);
		}
	}
	
	/**
	 * 职务晋升：判断是否满足升级条件
	 * @param int $uid 升级会员id
	 * @param array $reg_type_arr 升级条件数组
	 */
	private   function updateCondition($uid,$reg_type_arr)
	{
		$user = self::$Member->where(array('uid'=>$uid))->find();
		$uregtype = $user['reg_type']; //会员当前职务
		$newregtype = $uregtype+1;
		$area = $reg_type_arr[$newregtype][2]; //区域条件
		
		
		$map['status'] = array('neq',0);
		if($uregtype>=count($reg_type_arr)){
			return ;
		}else{
			$map['reg_type'] = $uregtype;
			if(($user['right_bill_all'])>=$area&&($user['left_bill_all']>=$area)){
				$regtype = $uregtype+1;
			}
		}
		if(isset($regtype)){
			self::$Member->where(array('uid'=>$user['uid']))->setField('reg_type',$regtype);
		}

	}
	
	/**
	 * 奖满足条件的会员设为报单中心
	 * @param unknown $user
	 */
	public function setBill($user)
	{
		$map['uid'] = array('in',$user['parentids']);
		$map['status'] = 1;
		$map['isbill'] = 0;
		$map['right_bill_all'] = array('egt',self::$bonusRule['level_bill_condition2']);
		$map['left_bill_all'] = array('egt',self::$bonusRule['level_bill_condition2']);
		self::$Member->where($map)->setField('isbill',1);
	}
}