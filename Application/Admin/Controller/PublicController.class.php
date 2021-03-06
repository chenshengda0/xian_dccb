<?php
namespace Admin\Controller;
use Common\Api\BonusApi;
use Common\Api\ChangeApi;

/**
 * 后台首页控制器
 */
class PublicController extends \Think\Controller {

    /**
     * 后台用户登录
     */
    public function login($username = null, $password = null, $verify = null){
        if(IS_POST){
            /* 检测验证码 TODO: */
        	if(C('DEVELOP_MODE') == 0){
        		if (C('VERIFY_OPEN') == 1 or C('VERIFY_OPEN') == 2) {
        			if (!check_verify($verify)) {
        				$this->error('验证码输入错误。',"",0);
        			}
        		}
        	}

            /* 调用UC登录接口登录 */
            $uid = D('Manager')->login($username, $password);
            if(0 < $uid){ //UC登录成功
                /* 登录用户 */
                $this->success('登录成功！', U('Index/index'));
            } else { //登录失败
                switch($uid) {
                    case -100: $error = '用户不存在或被禁用！'; break; //系统级别禁用
                    case -101: $error = '密码错误！'; break;
                    default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
                }
                $this->error($error,"",0);
            }
        } else {
            if(is_admin_login()){
                $this->redirect('Index/index');
            }else{
                /* 读取数据库中的配置 */
                $config	=	S('DB_CONFIG_DATA');
                if(!$config){
                    $config	=	D('Config')->lists();
                    S('DB_CONFIG_DATA',$config);
                }
                C($config); //添加配置
                
                $this->display();
            }
        }
    }
    

    /* 退出登录 */
    public function logout(){
        if(is_admin_login()){
            D('Manager')->logout();
            session('[destroy]');
            $this->success('退出成功！', U('login'),1);
        } else {
            $this->redirect('login',0);
        }
    }

	public function verify(){
       if(C('VERIFY_TYPE')==4){
    		$zhset = C('ZH_FONT');
    		$zhset = empty($zhset)?'们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这':$zhset;
    		$config = array(
    				'useZh'   =>true,
    				'zhSet'   =>$zhset,
    				'length'      =>   6,     // 验证码位数
    				'useNoise'    =>    false, // 关闭验证码杂点
    				'useCurve'  => false,
    		);
    	}else{
    		if(C('VERIFY_TYPE')==1){
    			$codeSet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    		}elseif(C('VERIFY_TYPE')==2){
    			$codeSet = '0123456789';
    		}else{
    			$codeSet = '0123456789ABCDEFGHGKLMNPQRST';
    		}
    		$config =  array(
    				'codeSet'  =>  $codeSet,
    				'length'      =>   6,     // 验证码位数
    				'fontttf' =>'4.ttf',
    				'useNoise'    =>    false, // 关闭验证码杂点
    				'useCurve'  => false,
    				 
    		);
    	}
	}



   //矿机每小时收益
    public function ceshi(){
     
       
	  //  $bonus = new ChangeApi();
	  //  $bonus->fh();
       // $bonus->tuijianBonus(15,100);
     	// $bonus->zzz();
		 $this->success("无访问权限");
    }
  public function daishu(){
    //  $bonus = new ChangeApi();
     // $bonus->managementAward(14);         //团队管理奖 2018-9-1
     // $bonus->memberDayAward(14);      //个人每日所得
     // $bonus->leadershipAward(14);      //团队领导奖：
  	//	$bonus->team();
   //   	$bonus->daishu();
     $this->success("无访问权限");
  }
  
  public function qk(){
   	//	$bonus = new ChangeApi();
	    $bonus->qd();		//清空签到
    	$bonus->fhsf();		//查看孵化仓
    	$bonus->fhc();		//孵化仓升息
        $bonus->moneyup();     //币价升值
     $this->success("孵化仓生息成功");
  }
  
  public function time(){
  	$bonus=get_bonus_rule();
   	$hous=date("H");
    if($hous<$bonus["level_money"]||$hous>=$bonus["leader_money"]){
    	$map["value"]=0;
      	D("config")->where(array("id"=>4))->save($map);
    }else{
    	$map["value"]=1;
      	D("config")->where(array("id"=>4))->save($map);
    }
  }
  
  //
  public function chu(){
  	$member=D("moneyChange")->where(array("tuserid"=>"69","recordtype"=>0,"money"=>array("gt",0)))->select();
 	$map=array();
    foreach($member as $a){
      if($a["changetype"]==16){
      	$type="转出";
      }else{
      	$type=$a["changetype"];
      }
    	$map[]="出款用户:".ee($a["userid"])."-->收入账户:".ee($a["targetuserid"])."--出账金额:".$a['money']."--出账类型:".$type."--时间:".date("Y-m-d H:i:s",$a["createtime"]);
    	$money+=$a["money"];
    }
    
    dump($map);dump($money);
  }
  
    public function ru(){
  	$member=D("moneyChange")->where(array("targetuserid"=>"69","recordtype"=>1,"money"=>array("gt",0)))->select();
 	$map=array();
    foreach($member as $a){
      if($a["changetype"]==16){
      	$type="转出";
      }else{
      	$type=$a["changetype"];
      }
    	$map[]="出款用户:".ee($a["userid"])."-->收入账户:".ee($a["targetuserid"])."--出账金额:".$a['money']."--出账类型:".$type."--时间:".date("Y-m-d H:i:s",$a["createtime"]);
    	$money+=$a["money"];
    }
    
    dump($map);dump($money);
  }
  
  public function del(){
	//$user=D("member")->where(array("usernumber"=>"18009266780"))->delete();
  	//D("member")->where(array("tuijianids"=>array("like","%,".$user['uid'].",%")))->delete();
    // $bonus = new ChangeApi();
  	//$bonus->team();
      //	$bonus->daishu2();
    $map["changetype"]=6;
    $map["moneytype"]=3;
    $map["createtime"]=array(array("egt",strtotime(date('Y-m-d'))),array("elt", strtotime(date('Y-m-d',strtotime('+1 day')))));
    $money=D("moneyChange")->where($map)->select();
    
    foreach($money as $a){
    	$b[]=$a["targetuserid"];
    }
    //dump($b);die;
    $c=array_unique($b); //去重
 	//dump($c);die;
    
    $yx=D("member")->where(array("hasbill"=>array("gt",0)))->select();
   
   foreach($yx as $d){
   		$e[]=$d["uid"];
   }
   
    
    $cz=array_diff($e,$c);
   	//dump($cz);die;
    $bonus = new ChangeApi();
  	$bonus->daishu2($cz);
  }
  
  
  
  public function fot(){
  	$map["changetype"]=7;
   // $map["targetuserid"]=58;
    $map["createtime"]=array(array("egt",strtotime(date('Y-m-d'.'00:00:00',time()))),array("elt", strtotime(date('Y-m-d',strtotime('+1 day')))));
    $money=D("moneyChange")->where($map)->select();
 
    foreach($money as $a){
    	$b[]=$a["targetuserid"];
    }

     $yx=D("member")->where(array("hasbill"=>array("gt",0)))->select();

    foreach($yx as $c){
    	$d[]=$c["uid"];
    }
		/*dump($b);
    dump($d);die;*/
    
    
      $cz=array_diff($d,$b);
       $bonus = new ChangeApi();
       $bonus->fh2($cz);
  	
  }
  
  public function hd(){
  	$member=D("ucenter_member")->select();

    foreach($member as $a){
    	$uinfo=D("member")->where(array("uid"=>$a["id"]))->find();
    	
      if(empty($uinfo)){
    //  	D("ucenter_member")->where(array("id"=>$a["id"]))->delete();
      }
     
    }
  }

    /**
     *  每日定时释放任务 2018-9-5
     */
  public function timedTask(){

      $key = I('key');
      if($key != '481F85D988BE72F4'){

            die(json_encode(array('status'=>'NO','msg'=>'校验错误')));
      }

     // http://ciex.cn/index.php?a=/admin/public/timedTask/key/481F85D988BE72F4.html

      //判断是否进行分红   暂时放着
      $sttime= strtotime(date('Y-m-d'),time());
      $endtime = $sttime + 86400;

      $maptiem['time'] =array('BETWEEN',array($sttime,$endtime));
      $fhtime = M('fh')->where($maptiem)->find();
      if($fhtime){

          //如果没有，则进行一次分红
          $fh= new ChangeApi;
          $fh->fh();     //今天静态分红

          $fh->moneyup();  //币价升值
          //并在数据库插入当天的时间
          $data['time'] = time();
          $result = M('fh')->add($data);

      }
      die(json_encode(array('status'=>'OK','msg'=>'无访问权限')));
      $this->success("无访问权限",'/home/index/index');

  }
  
 
  
  
  
}