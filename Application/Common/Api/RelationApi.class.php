<?php
namespace Common\Api;

/**
 * 会员关系APi
 */

final class RelationApi 
{
	private static $bonusRule;
	private static $Member;
	private  static $modeType;
	
	
	/**
	 * 初始化
	 */
	Public function __construct()
	{
		self::$Member = M('Member');
		self::$bonusRule = get_bonus_rule();
		self::$modeType  = C('SELECT_MODE');//系统制度
	}
	
	/**
	 * 测试接口
	 */
	public function test($uid)
	{
		$user = self::$Member->where(array('uid'=>$uid))->find();
	}
	
	public function pRelation($pnumber)
	{
		$data = array();
		$parent_info = self::$Member->where(array('usernumber'=>$pnumber,'status'=>1))->find();
		if(empty($parent_info)){
			$data['code']=-1000;
			return $data;
		}
		$parentid = $parent_info['uid'];
		if(empty($parent_info['parentids'])){
			$parents = ','.$parentid.',';//接点人id组
		}else{
			$parents= $parent_info['parentids'].$parentid.',';//接点人id组
		}
		
		$pdeep = $parent_info['pdeep']+1;
			
		$data['parentid'] = $parentid;
		$data['parentnumber'] = $parent_info['usernumber'];
		$data['parentids'] = $parents;
		$data['pdeep'] = $pdeep;
		$data['code'] = 1;
			
		return $data;
	}
	
	/**
	 * 推荐关系
	 * @param string $tnumber 接点人
	 */
	public  function tRelation($tnumber)
	{
	    
		$tuijian_info = self::$Member->where(array('usernumber'=>$tnumber,'status'=>1))->find();
		
		if(empty($tuijian_info)){
			$data['code']=-2000;
			return $data;//推荐人不存在
		}
		
		$tuijianid = $tuijian_info['uid'];
		if(empty($tuijian_info['tuijianids'])){
			$tuijianids = ','.$tuijianid.',';//推荐人id组
		}else{
			$tuijianids= $tuijian_info['tuijianids'].$tuijianid.',';//推荐人id组
		}
		$tdeep = $tuijian_info['tdeep']+1;
		
		$data['tuijianid'] = $tuijianid;
		$data['tuijiannumber'] = $tnumber;
		$data['tuijianids'] = $tuijianids;
		$data['tdeep'] = $tdeep;
		$data['code'] = 1;
      	$data['gonghuiid'] =$tuijian_info['gonghuiid']; 
		return $data;
	}
	
	/**
	 * 所属报单中心
	 * @param string $billnumber
	 */
	public function billBelong($billnumber)
	{
		if($billnumber !='公司'){
    		$map['usernumber'] = $billnumber;
    		$map['isbill'] = array('neq',0);
    		$bill_info = M('Member')->where($map)->find();
    		if(empty($bill_info)){
    			$data['code'] = -3000;
    		}else{
    			$billid = $bill_info['uid'];
    			$data['billcenterid'] = $billid;
    			$data['code'] = 1;
    		}
    		
	    }else{
	    	$data['billcenterid'] = 0;
	    	$data['code'] = 1;
	    }
	    $data['isbill'] = 0;
	    return $data;
	}

}