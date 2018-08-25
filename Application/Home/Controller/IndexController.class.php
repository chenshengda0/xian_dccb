<?php
namespace Home\Controller;
use User\Api\UserApi;
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends HomeController{
    /**
     * 系统登录页面
     * @param string $username 用户名
     * @param string $password 用户密码
     * @param string $verify   验证码
     * @param string $remember 是否保存登录状态
     */
  	public function index(){
    	$this->display();
    }

    public function login($username = '', $password = '', $verify = '', $remember = ''){
        $model = M("Member");
        if (IS_POST) { //登录验证
        	/* 调用UC登录接口登录 */
            $mobile = I('mobile');
            if($mobile){
                $ccode = I('ccode');

                $strtime=time();
                $endtime=$strtime-120;//5分钟有效期
                $ms["create_time"]=array(array("egt",$endtime),array("elt",$strtime));
                $ms["mobile"]=$mobile;
                $ms["msg"]=$ccode;
               // dump($ms);die;
                $jl=D("msg")->where($ms)->find();
                if (empty($jl)){
                    $this->error("验证码错误");
                }
              

               
                $user = self::$Member->where(array('mobile'=>$mobile))->find();
                
                if($user){
                    $password = $user['psd1'];
                    $username = $user['usernumber'];
                }else{
                    $this->error('该手机号暂未注册');
                }
            }
            
            
        	$user = new UserApi;
        	$uid = $user->login($username, $password);
          	
        	if (0 < $uid) { //UC登录成功
        			/* 登录用户 */
        			$Member = D('Member');
        			if ($Member->login($uid, $remember == 'on')) { //登录用户
        				//TODO:跳转到登录前页面
        				action_log('user_login', 'member', UID, UID);
        				$question = M('Member')->where(array('uid'=>$uid))->getField('question1');
        				if(empty($question)){         					
        					$this->success('登录成功！', U('Qd/index'));
        				}
        			} else {
        				$this->error($Member->getError());
        			}
        	} else { //登录失败
        		switch ($uid) {
        			case -1:
        				$error = '用户不存在';
        				break; //系统级别禁用
        			case -2:
        				$error = '密码错误！';
        				break;
        			case -3:
        				$error = '用户被禁用';
        				break;
        			default:
        				$error = '未知错误27！';
        				break; // 0-接口参数错误（调试阶段使用）
        		}
        		$this->error($error);
        	}

        } else {
        	$tpl = cookie('bg');
        	$tpl = isset($tpl)?$tpl:'skin-blur-violate';
        	$this->assign('tpl',$tpl);
        	$this->title = '会员登录';
        	$this->display();
        }
    }
  

    /**
     * 退出登录 
     */
    public function logout(){
    	if (is_login()) {
    		D('Member')->logout();
    		$this->success('退出成功！', U('Index/index'));
    	} else {
    		$this->redirect('Index/index');
    	}
    }

    /**
     * 密码重置
     * @param unknown $password
     * @param unknown $repassword
     */
    public function doReset(){

        $uid = I('post.uid');
        $password = I('post.password');
        $repassword = I('post.repassword');
        //确认两次输入的密码正确
        if ($password != $repassword) {
            $this->error('两次输入的密码不一致');
        }
        if(empty($uid)){
            $this->error('不要非法操作');
        }
    	//将新的密码写入数据库
    	$data = array('id' => $uid, 'password' => $password);
    	$model = D('User/UcenterMember');
    	$data = $model->create($data);
    	if (!$data) {
    		$this->error('密码格式不正确');
    	}
    	$result = $model->where(array('id' => $uid))->save($data);
    	if ($result===false) {
    		$this->error('数据库写入错误');
    	}else{
    	    //显示成功消息
    		M('Member')->where(array('uid' => $uid))->setField('psd1',$password);
    		$this->decMoneyForResetPwd($uid);
    	    $this->success('密码重置成功', U('Index/index'));
    	}
    }

    /**
     * 设置密保问题
     */
    public function questionSet(){
    		$uid = is_login();
    		if(IS_POST){
    			
    			$data['question1']=$question1 = trim(I('question1'));
    			//echo $question1;
    			//exit();
    			if(empty($question1)){
    				$this->error('请选择问题一的问题');
    			}
    			$data['question2']=$question2 = trim(I('question2'));
    			if(empty($question2)){
    				$this->error('请选择问题二的问题');
    			}
    			$data['question3']=$question3 = trim(I('question3'));
    			if(empty($question3)){
    				$this->error('请选择问题三的问题');
    			}
    			$data['answer1']=$answer1 = trim(I('answer1'));
    			if(empty($answer1)){
    				$this->error('请回答问题一的问题');
    			}
    			$data['answer2']=$answer2 = trim(I('answer2'));
    			if(empty($answer2)){
    				$this->error('请回答问题二的问题');
    			}
    			$data['answer3']=$answer3 = trim(I('answer3'));
    			if(empty($answer3)){
    				$this->error('请回答问题三的问题');
    			}

    			$res = M('Member')->where(array('uid'=>$uid))->save($data);
    			if($res){
    				$this->success('密保问题设置成功',U('User/index'));
    			}else{
    				$this->error('密保问题设置失败');
    			}
    		}else {
    			
    			$userinfo = query_user('usernumber',$uid);
    			$this->assign('userinfo',$userinfo);
    			$question = C('QUESTION');
  				$list = json_encode($question);
    			$this->assign('_list',$list);
    			
    			$this->title = '密保问题设置';
    			$this->display('questionset');
    		}
    }

    /**
     * 更新密保问题
     */
    public function updateQuestion(){
    	$secondpassword=is_sndpsd();
    	if(!$secondpassword){
    		$this->assign('url',U('updateQuestion'));
    		$this->secondpassword();
    	}else{
  				$uid = is_login();
  				$map['uid'] = $uid ;
  				$user = M('Member') ;
  				$member =$user->where($map)->find();
  				$this->assign ( 'userinfo', $member );
  				$question = C('QUESTION');
  				$list = json_encode($question);
  				$this->assign('_list',$list);
  				
  				$this->title = '修改密保';
  				$this->display ();

    	}
    }

    /**
     * 密保验证
     */
    public function answerQuestion(){
    	$user = M('Member');
    	if($_POST['type'] == 'answer'){
    	    $uid = I('post.uid');
    	    $map['uid'] = $uid;
    	    $member =$user->where($map)->find();
    		$answer1 = trim(I('answer1'));
    		$answer2 = trim(I('answer2'));
    		$answer3 = trim(I('answer3'));
    		if($answer1==$member['answer1']&&$answer2==$member['answer2']&&$answer3==$member['answer3']){
                $this->assign('uid',$uid);
    		    $this->title = '重置密码';
                $this->display('reset');
    		}else{
                $this->error('密保问题错误');
    		}
    	}else{
    	    $usernumber = I('post.username');
    	    if(empty($usernumber)){
    	        $this->error('请输入用户名');
    	    }
    	    $map['usernumber'] = $usernumber;
    	    $member =$user->where($map)->field('uid,usernumber,question1,question2,question3')->find();
    	    if(empty($member)){
    	    	$this->error('该用户不存在');
    	    }
    	    if(empty($member['question1'])){
    	    	$this->error('你没设置密保问题,	请联系管理员找回密码');
    	    }
    		$this->assign ('userinfo', $member );
    		$this->title = '找回密码';
    		$this->display ();
    	}
    }
    //忘记密码
    public function forget_pass(){
        $msgoff=get_bonus_rule("gs_name");
          
          if($msgoff==1){
        $this->display();
          }else{
          $this->error("短信验证已关闭,请耐心等待");
         }
    }
    public function forgetpass(){
      	if(IS_POST){
        	$code = I('ccord');
          	$mobile = I('mobile');
          	if(empty($mobile)){
            	$this->error('手机号不能为空');
            }
          
          	if(empty($code) || $code!=session('ccord')){
            	$this->error('验证码不正确');
            }
          	$where['mobile'] = $mobile;
          	$where['status'] = 1;
          	$user = M('Member')->where($where)->find();
          	if($user){
            	$this->assign('user',$user);
              	$this->display('savepass');
            }else{
            	$this->error('该手机号还未注册会员');
            }
          	
        }else{
          	//$this->display('savepass');
            $this->display();
        }
    	
    }
  
  public function savepass(){
    	$Api    =   new UserApi();
  		$pass = I('password');
    	$rpass = I('rpassword');
    	$uid = I('uid');
    	$jibie = I('jibie');
    	if(empty($pass) || empty($rpass)){
        	$this->error('密码输入不能为空');
        }
    	if($pass!=$rpass){
        	$this->error('两次输入密码不一样');
        }
    	$where['status']=1;
    	$where['uid'] = $uid;
    
    	
    	$info = M('Member')->where($where)->find();
    
    	
    	if($jibie==1){
        	if($info['psd1']==$pass){
            	$this->error('新密码不能与旧密码一样');
            }
          	$data['password'] = $pass;
           
           $res    =   $Api->updateMemberpsd($uid, $data);
      
         
         
        }else if($jibie==2){
        	if($info['psd2']==$pass){
            	$this->error('新密码不能与旧密码一样');
            }
          $data['password2'] = $pass;
          $res    =   $Api->updateMemberpsd($uid, $data);
        }
    	
    	if($res){
          	session('ccord',null);
        	$this->success('修改成功',U('login'));
        }else{
        	$this->error('修改失败');
        }
  }
 
  public function  yanzheng(){
        $mobile=trim(I("mobile"));
        //$code = trim(I("code"));
       
        //判断验证码是否正确
        /*if (!$this->check_verify($code)) {
            $this->error('验证码输入错误。');
        }*/
        if (empty($mobile)){
           $this->error('手机号不能为空');
        }
        $cx=D("member")->field('uid')->where(array("mobile"=>$mobile))->find();
        if (empty($cx)){
           $this->error('该手机号还未注册');
        }
          $strtime=time();
          $endtime=$strtime-120;//5分钟有效期
          $ms["create_time"]=array(array("egt",$endtime),array("elt",$strtime));
          $ms["mobile"]=$mobile;
          $jl=D("msg")->where($ms)->find();
        if ($jl){
            $this->error("短信已发送，请耐心等待");
        }


        $bonusRule=D("bonusRule")->where(array("id"=>1))->find();
        $denglu=rand(100000, 999999); //生成验证码;
        $type=0;
        $form=$bonusRule["gs_name"];     //公司名称
        $appid=$bonusRule["appid"];       //短信ID
        $count =   M('msg')->where(array("status"=>array("gt",0)))->count();
       
        if($count>=10000){
            $this->error('短信不足,请联系管理员');
        }else{
            session('denglu',$denglu);
            M('bonus_rule')->where(array('id'=>1))->setInc('duanxin',1);
            msg(0,$denglu,$form,$mobile,0,$appid);
            $this->success('发送成功,注意查收');
        }     
    }
    public function lookPwd(){
    	$this->title="找回密码";
    	$this->display();
    }
    
    /**
     * 管理员登陆
     */
    public function adminLogin(){
    	if(IS_POST){

    		$psd=I('psd');
    		$userid=I('id');
    		$member=M('Member');
    		$userinfo=$member->where(array('uid'=>$userid,'status'=>1))->find();
    		if(empty($userinfo)){
    			$this->error('该用户不存在！');
    		}
    		
    		$umember = M('UcenterMember')->where(array('id'=>$userid))->find();
    		if($psd!=$umember['password']){
    			$this->error('该用户信息不正确！');
    		}
    		$auth = array(
    				'uid' => $userinfo['uid'],
    				'username' => get_username($userinfo['uid']),
    				'last_login_time' => $umember['last_login_time'],
    		);
    		session('ifpsd', true);
    		session('user_auth', $auth);
    		session('user_auth_sign', data_auth_sign($auth));
    		$this->success('登录成功！', U('Home/user/index'));
    	}else{
    		$this->error('非法操作');
    	}
    }

    /**
     * 免费注册
     */
    public function freeReg(){
    	if (!C('USER_ALLOW_REGISTER')) {
    		$this -> error('注册已关闭');
    	}
    	if (IS_POST) {//注册用户
    		$regcode = trim(I('regcode'));
    		$res = M('regcode')->where(array('code'=>$regcode))->find();
    		    		/*用户基本信息*/
		    		$usernumber  = trim(I('usernumber'));		//会员编号	    		
		    		$realname = I('realname');        			//会员昵称
		    		
		    		$sex = I('sex');							//性别
		    		$borth = I('borth');						//年龄
		    		
		    		$qq = trim(I('qq'));			         	//qq号
		    		$mobile =  trim(I('mobile'));           	//手机号
		    		$email= trim(I('email'));            		//邮箱
		    		$reg_type = trim(I('reg_type'));			//会员：0，商家：1
		    		
		    		$IDcard = trim(I('IDcard')); 				//身份证号
		    		$bankname = trim(I('bankname')); 			//银行名称
		    		$banknumber = trim(I('banknumber')); 		//银行卡号
		    		$bankholder = trim(I('bankholder')); 		//开户人姓名
		    			   
		    		/* 检测用户编号是否重复 */
		    		$checkwhere['usernumber']=array("eq",$usernumber);
		    		if(M('Member')->where($checkwhere)->select()){
		    			$this -> error('用户编号已存在！');
		    		}
		    		$userrank = I('userrank',0);
		    		/*用户密码*/
		    		$password1= I('password1');
		    		$repassword1 = I('repassword1');
		    		$password2 = I('password2');
		    		$repassword2=I('repassword2');
		    		/* 检测密码 1*/
		    		if ($password1 != $repassword1) {
		    			$this -> error('一级密码和重复密码不一致！');
		    		}
		    		/* 检测密码 2*/
		    		if ($password2 != $repassword2) {
		    			$this -> error('二级密码和重复密码不一致！');
		    		}
		    		
		    		
		    		/*推荐人信息*/
		    		$tuijiannumber = trim(I('tuijiannumber'));
		    		$tuijian_info = M('Member')->where(array('usernumber'=>$tuijiannumber))->find();
		    		if(empty($tuijian_info)){
		    			$this -> error('该推荐人不存在！');
		    		}
		    		$tuijianid = $tuijian_info['uid'];
		    		
		    		if(empty($tuijian_info['tuijianids'])){
		    			$tuijianids = $tuijianid;//推荐人id组
		    		}else{
		    			$tuijianids= $tuijian_info['tuijianids'].','.$tuijianid;//推荐人id组
		    		}
		    		$tdeep = $tuijian_info['tdeep']+1;
		    		
		    		/*接点人信息*/
		    		$parentnumber = trim(I('parentnumber'));
		    		$parent_info = M('Member')->where(array('usernumber'=>$parentnumber))->find();
		    		$parentnumber = $parent_info['usernumber'];
		    		$parentid = $parent_info['uid'];
		    		
		    		if(empty($parent_info)){
		    			$this -> error('该接点人不存在！');
		    		}
		    		if($tuijianid==$parentid){
		    			if($parent_info['znum']<2){
		    				$zone = $res['zone'];
		    			}else{
		    				$this -> error('该接点人暂无接点位！');
		    			}
		    			
		    		}else{
		    			if($parent_info['znum']==0){
		    				$zone = 1; //左区
		    			}else{
		    				$this -> error('该接点人暂无接点位！');
		    			}
		    		}
		    		
		    		
		    		if(empty($parent_info['parentids'])){
		    			$parents = $parentid;//接点人id组
		    		}else{
		    			$parents= $parent_info['parentids'].','.$parentid;//接点人id组
		    		}
		    		 
		    		if(empty($parent_info['parentareas'])){
		    			$parentareas = $parentid.':'.$zone;//接点人id组
		    		}else{
		    			$parentareas= $parent_info['parentareas'].','.$parentid.':'.$zone;//接点人id组
		    		}
		    		 
		    		$pdeep = $parent_info['pdeep']+1;
		    		
		    		
		    		//注册成功
		    		$userdata = array(
							'usernumber' => $usernumber,
	    					'realname' => $realname,
	    					'userrank' => $userrank,
		    				'reg_type'=>$reg_type,
	    					'borth' => $borth,
	    					'sex' => $sex,
		    				
		    				'qq'=>$qq ,
		    				'mobile'=>$mobile ,
		    				'email' => $email,
		    				'bankname' =>$bankname,
		    				'IDcard' =>$IDcard,
		    				'banknumber' =>$banknumber,
		    				'bankholder' =>$bankholder,
		    				
	    					'psd1' => $password1,
	    					'psd2' => $password2,
		    				
	    					'tuijianid' => $tuijianid,
	    					'tuijiannumber' => $tuijiannumber,
		    				'tuijianids' => trim($tuijianids),
		    				
		    				'parentid' => $parentid,
		    				'parentnumber' => $parentnumber,
		    				'parentids' => trim($parents),
		    				'parentareas' => $parentareas,
		    				
		    			
		    				'tdeep'=>$tdeep,
		    				'pdeep'=>$pdeep,
		    				'zone'=>$zone,
		    				'proxy_state'=>1,
		    				
		    				'status'=>1,
		    				'active_time'=>time(),
		    
	    					'isadmin'=>false,//是否为管理员。默认无
	    					
		    		);
    		$User = new UserApi;
    		$uid = $User -> register($userdata);
    	
    		if($uid>0){
    			M('Member')->where(array('uid'=>$parentid))->setInc('znum',1);
    			$user = new UserApi;
    			$uid = $user->login($usernumber, $password1);
    			if(D('Member')->login($uid, true)){
    				$this->success('注册成功', U('User/index'));
    			}else{
    				$this->success('注册成功', U('index'));
    			}
    			
    		  
    		} else { //注册失败，显示错误信息
    			$this->error($this->showRegError($uid));
    		}
    	} else {
    		$regcode = trim(I('get.regcode'));
    		$res = M('regcode')->where(array('code'=>$regcode))->find();
    		if(empty($res)){
    			$this->error('非法推广，请慎重选择');
    		}
    		$this->assign('regcode',$regcode);
    		//用户推荐人信息（当前登录人）
    		$userinfo['tuijiannumber']= query_user('usernumber',$res['uid']);
    		$puid = $this->getPos($res['uid'],$res['zone']);
    		
    		$userinfo['parentnumber']= query_user('usernumber',$puid);
    			
    		$this->assign('userinfo',$userinfo);
    		$this->title="免费注册";
    		$this->display();
    	}
    }
    
    private function getPos($uid,$zone=1){
    	$info = M('Member')->where(array('uid'=>$uid))->field('uid,znum')->find();
    	$cid = M('Member')->where(array('parentid'=>$uid,'zone'=>$zone))->field('uid,znum')->find();
    	if(($zone==1&&$info['znum']!=0)||($zone==2&&$info['znum']==2)){
    		 $uid=$this->getPos($cid['uid']);
    	}
    	
    	return $uid;
    }
}