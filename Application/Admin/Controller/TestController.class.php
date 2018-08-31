<?php
namespace Admin\Controller;

use Think\Model;
use User\Api\UserApi;
use Common\Api\BonusApi;
use Common\Api\RelationApi;
use Common\Api\CreateChildApi;



class TestController extends AdminController{
	
	
	public function test(){
		$BonusApi = new CreateChildApi;
		$result = $BonusApi->createChild();
	}
	
	public function  index(){
		
		$this->meta_title = "数据初始化";
		$this->display();
	}

    /**
     * 清空所有数据
     */
    public function deleteData () { 
    	
    	$Model = new Model();// 实例化一个model对象 没有对应任何数据表
    	$prefix = C('DB_PREFIX');//表前缀
    	 
    	$sql = " truncate {$prefix}money_change";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}seed_change";
    	$Model->execute($sql);

    	 
    	$sql = " truncate {$prefix}bonus_count ";
    	$Model->execute($sql);
    	 
    	$sql = " truncate {$prefix}trademsg";
    	$Model->execute($sql);
    	 
    	$sql = " truncate {$prefix}traderelease";
    	$Model->execute($sql);
    	 
    	$sql = " truncate {$prefix}liuyan";
    	$Model->execute($sql);
    	 
    	$sql = " truncate {$prefix}member";
    	$Model->execute($sql);
    	 
    	$sql = " truncate {$prefix}ucenter_member";
    	$Model->execute($sql);

    	
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}shop_buy";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}shop_see";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}shop_log";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}shop_order";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}shoplist";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}shopcart";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}transport";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}withdrawal";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}recharge";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}finance";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}regcode";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}bonus_total";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}prize";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}layer_bill";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}update_level";
    	$Model->execute($sql);
    	
       $sql = " truncate {$prefix}kuangji";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}jiaoyi";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}gonghui";
    	$Model->execute($sql);
      
    	clean_all_cache();
    	$this->success('数据表清空成功！',U('User/registuser'));
    }
    
    /**
     * 删除奖金
     */
    function deleteBonus(){
    	$Model = new Model();// 实例化一个model对象 没有对应任何数据表
    	
    	$prefix = C('DB_PREFIX');//表前缀
    	$sql = " truncate {$prefix}money_change";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}bonus_count ";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}bonus_total";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}finance";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}trademsg";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}traderelease";
    	$Model->execute($sql);
    	
    	clean_all_cache();
    	$this->success('数据表清空成功！');
    }
    
    public function deleteShop(){
    	$Model = new Model();// 实例化一个model对象 没有对应任何数据表
    	$prefix = C('DB_PREFIX');//表前缀
    	$sql = " truncate {$prefix}shop";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}shop_category";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}shop_buy";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}shop_see";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}shop_log";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}shop_order";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}shoplist";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}shopcart";
    	$Model->execute($sql);
    	
    	$sql = " truncate {$prefix}shop_transport";
    	$Model->execute($sql);
    	
    	$this->success('数据表清空成功！');
    }
    
    public function  reg(){
    	$this->meta_title = "批量注册";
    	$this->display();
    }
    
    public function auto_reguser(){
    	$num = 2; //每层注册会员数
    	$floor = I('floor',5);//注册层数
    	if($floor>10){
    		$floor = 10;
    	}
    	$pre = 0; //参与推荐下层会员数
    	$map["status"]=1;
    	$table=M("Member")->find();
    	if(empty($table)){
    		$this->reg_one($num,$floor,$pre);
    	}else{
    		$maxtdeep = M("Member")->max('tdeep');
    		for($i=0;$i<$floor;$i++){
    			$map['tdeep'] = array('egt',$maxtdeep+$i);
    			$member=M("Member")->where($map)->limit($pre)->select();
    			foreach ($member as $val){
    				$this->auto($val['uid'],$num);
    			}
    		}
    	}
    	$this->success('注册成功！', U('User/userindex'));
    }
    
    private function reg_one($num,$floor,$pre){
    	/*推荐人信息*/
    	$tuijiannumber = 0;
    	$tuijianid = 0;
    	$tdeep = 0;
    	/*接点人信息*/
    	$parentnumber = 0;
    	$parentid = 0;
    	$zone = 1; //左区
    	$pdeep = 0;
    	/*报单中心*/
    	$isbill = 1;
    	$arruid['uid'] = 1;
    	$arruid['reg_time'] = time();
    	$arruid['billcenterid'] = 0;
    		
    	//注册成功
    	$userdata = array(
    			'usernumber' => rand(10000000, 99999999),
    			'realname'=>'好聚点',
    			'userrank' => 1,
    			'oldrank' => 1,
    			'isbill'=>1,
    			'psd1' => rand(100000, 999999),
    			'psd2' => rand(100000, 999999),
    			'reg_uid' =>0,
    			'isadmin'=>false,//是否为管理员。默认无
    	);
    
    	if(isset($arruid)){
    		$userdata = array_merge($userdata, $arruid);
    	}
    
    	$User = new UserApi;
    	$uid = $User-> register($userdata);
    	if($uid>0){
    		$BonusApi = new BonusApi();
    		$result = $BonusApi->activation($uid,PROJECTNUMBER);
    		for($i=0;$i<$floor;$i++){
    			$map['tdeep'] = $i;
    			$member=M("Member")->where($map)->select();
    			foreach ($member as $val){
    				$this->auto($val['uid'],$num);
    			}
    			 
    		}
    	} else { //注册失败，显示错误信息
    		$this->error($this->regError($uid));
    	}
    }
    
    public function auto($uid,$num){
    	for($i=0;$i<$num;$i++){
    		$this->aut_reg($uid);
    	}
    }
    /**
     * 自动注册测试会员
     */
    public function aut_reg($uid){
    		/*推荐关系*/
    		$relationApi = new RelationApi();
    		$tuijiannumber=	M('Member')->where(array('uid'=>$uid))->getField('usernumber');
    		$tdata = $relationApi->tRelation($tuijiannumber);
    
    	
    		/*接点关系*/
    		$parentid=getPosition();
    		$parentnumber=M('Member')->where(array('uid'=>$parentid))->getField('usernumber');
    		$zone = 1;
    		$pdata = $relationApi->pRelation($parentnumber, $zone);
    		 
    		/*报单中心*/
    		$billnumber=$this->getServercenter($tuijiannumber);
    		$bdata = $relationApi->billBelong($billnumber);
    		
    		$xm = xm2arr();
    		$xs = xs2arr();
    		$name = $xs[rand(0, 437)].$xm[rand(0, 37)];
	    	//$rank = rand(1, 3);
    		$rank = 1;
	    	$rule =  get_bonus_rule();
	    	$arr_rate = user_level_bonus_format($rule['level_bill']);
	    	$level_bill = $arr_rate[$rank][2];
	    	
	    	$userdata = array(
	    			'usernumber' => rand(10000000, 99999999),
	    			'realname'=>$name,
	    			'userrank' => $rank,
	    			'oldrank' => $rank,
	    			'level_bill' => $level_bill,
	    			'isbill'=>0,
	    			'reg_type'=>0,
	    			'psd1' => rand(100000, 999999),
	    			'psd2' => rand(100000, 999999),
	    			'reg_uid' =>0,
	    			'isadmin'=>false,//是否为管理员。默认无
	    	);
    
    		/*判断是否存在推荐关系*/
    		if(isset($tdata)){
    			if($tdata['code']>0){
    				unset($tdata['code']);
    				$userdata = array_merge($userdata, $tdata);
    			}else{
    				$this->error($this->regError($tdata['code']));
    			}
    		}
    		/*判断是否存在接点关系*/
    		if(isset($pdata)){
    			if($pdata['code']>0){
    				unset($pdata['code']);
    				$userdata = array_merge($userdata, $pdata);
    				$parentid = $pdata['parentid'];
    			}else{
    				$this->error($this->regError($pdata['code']));
    			}
    		}
    		/*判断是否有报单中心*/
    		if(isset($bdata)){
    			if($bdata['code']>0){
    				unset($bdata['code']);
    				$userdata = array_merge($userdata, $bdata);
    			}else{
    				$this->error($this->regError($bdata['code']));
    			}
    		}
    		 
    		$User = new UserApi;
    		$uid = $User -> register($userdata);
    		if($uid>0){
    			if(isset($pdata)){
    				$pznum = self::$Member->where(array('uid'=>$pdata['parentid']))->getField('znum');
    				$data['znum'] = $pznum+1;
    				if(isset($pdata['zone'])){
    					if($zone==1){
    						$data['left_zone'] = 1; //左区被占
    					}
    					if($zone==2){
    						$data['right_zone'] = 1;//右区被占
    					}
    				}
    				self::$Member->where(array('uid'=>$pdata['parentid']))->save($data);
    			}
    			$BonusApi = new BonusApi();
    			$result = $BonusApi->activation($uid,PROJECTNUMBER);
    		} else { //注册失败，显示错误信息
    			$this->error($this->regError($uid));
    		}
    }
 
}
