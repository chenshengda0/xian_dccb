<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\CommonController;
use Common\Api\RelationApi;
use User\Api\UserApi;
use Common\Api\BonusApi;
class RegisterController extends CommonController{
    public function index(){
		        $id = I('id',0);
                if(empty($id)){//前台注册
                    
                }else{//招募
                    $userinfo = self::$Member->where(array('uid'=>$id))->find();
                    $this->assign('userinfo',$userinfo);
                }
               	
                $param['class'] = '';
                $param['style'] = 'background-color:transparent;width:45%;text-align:left';
                $this->assign('param',$param);
                
                $this->title = '会员注册';
                $this->display();
            }

    public function param(){
        $param['class'] = 'form-control m-b-10';
        $param['style'] = 'display:inline-block;width:15%;min-width:80px;height:33px;';
        $this->assign('param',$param);
    }
  
  
    /**
     * 会员注册
     */
public function register(){
    //$this->success('注册成功', U('index/index'));
    	if(IS_POST){
    		if(MODULE_NAME=='Admin'){
    			$loginid=0;
    		}else{
    		    
    			$loginid = is_login();
    		}
    		/*用户基本信息*/
    		$usernumber  =  trim(I('mobile'));		//会员编号
    		$yzm = I('yzm');
    		$mobile =  trim(I('mobile'));
    		//手机号
    		if(empty($mobile)){
    		    $this->error('手机号不能为空');
    		}
          
          $msgoff=get_bonus_rule("gs_name");
          
          if($msgoff==1){
            $ccode = I('ccode');

            $strtime=time();
            $endtime=$strtime-60;//5分钟有效期
            $ms["create_time"]=array(array("egt",$endtime),array("elt",$strtime));
            $ms["mobile"]=$mobile;
            $ms["msg"]=$ccode;
            // dump($ms);die;
            $jl=D("msg")->where($ms)->find()?true:true;
            if (empty($jl)){
                $this->error("验证码错误");
            }
          
          }
    		//地址
    		/*$province = I('province');
    		$city = I('city');
    		$arr = ['1','2','22','9'];
    		if(in_array($province,$arr)){
    		    $city = $province;
    		}
    		if(empty($city)){
    		    $this->error('请选择你所在市');
    		}*/
    		/* 检测用户编号是否重复 */
    		$where['usernumber']=$usernumber;
    		if(self::$Member->where($where)->getField('uid')){
    			$this -> error('手机号已存在！');
    		}
          	$map['mobile'] = $mobile;
            if(self::$Member->where($map)->getField('uid')){
    			$this -> error('该手机号已经注册！');
    		}
    		$userrank = trim(I('userrank',0));		//会员级别
    		$reg_type = trim(I('reg_type'));		//会员类型
    		/*用户密码*/
    		$password1= I('password1');
    		$password2 ="123456";
    		$table = self::$Member->find();
    		if(!empty($table)){
    			/*推荐关系*/
    			$relationApi = new RelationApi();
    			$tnumber = trim(I('tuijiannumber'));
    			
    			if(empty($tnumber)){
    				$this->error('请选择推荐人');
    			}
    			$tdata = $relationApi->tRelation($tnumber);
    		}else{
    			/*推荐人信息*/
    			$tuijiannumber = 0;
    			$tuijianid = 0;
    			$tdeep = 0;
    			/*接点人信息*/
    		/*	$parentnumber = 0;
    			$parentid = 0;
    			$zone = 1; //左区
    			$pdeep = 0;*/
    			/*报单中心*/
    		/*	$isbill = 1;
    			$arruid['uid'] = 1;
    			$arruid['reg_time'] = time();
    			$arruid['billcenterid'] = 0;
    			//点位
    			$bid=1;
    			$oid=1;*/
    		}
    		//注册成功
    		$userdata = array(
    				'usernumber' => $usernumber,
    				'userrank' => $userrank,
             	 'realname' => trim(I("usernumber")),
    		        /*'province' => $province,
    		        'city' => $city,*/
    				'oldrank' => $userrank,
    				'reg_type' =>$reg_type,
    				//'isbill'=>$isbill,
    				'mobile'=>$mobile ,
    				'psd1' => $password1,
    				'psd2' => $password2,
    				'status'=>1,
    		        'active_time'=>time(),
    				//'bid'=>$bid,
    				//'oid'=>$oid,
    				'reg_uid' =>$loginid,
    				'proxy_state'=>1,
    				'isadmin'=>false,//是否为管理员。默认无
    					
    		);
    		if(isset($arruid)){
    			$userdata = array_merge($userdata, $arruid);
    		}
    		/*判断是否存在推荐关系*/
    		if(isset($tdata)){
    			if($tdata['code']>0){
    				$userdata = array_merge($userdata, $tdata);
    			}else{
    				$this->error($this->regError($tdata['code']));
    			}
    		}
    		$User = new UserApi;
    		$uid = $User-> register($userdata);
    	    if($uid>0){
    	        //直推加1
    	        $info=self::$Member->where(array("uid"=>$uid))->find();
    	        self::$Member->where(array('uid'=>$info['tuijianid']))->setInc('recom_num',1);
              	$where=array();
              	//团队加1
              	$where['uid'] = array('in',$info['tuijianids']);
              	$where['status'] = 1;
              	self::$Member->where($where)->setInc('team',1);
              	//算力
    	        $bonus = new BonusApi();
    	        $bonus->add_suanli($uid,0);
    			$this->success('注册成功', U('index/index'));
    		} else { //注册失败，显示错误信息
    			$this->error($this->regError($uid));
    		}
    	}
    	 
    }
    public function  yanzhengma(){
//        $this->error(11);
//        die;
      
       $msgoff=get_bonus_rule("gs_name");
          
          if($msgoff==1){
                $mobil=trim(I("mobile"));

               // $code = trim(I("code"));
              //dump($mobil);dump($code);die;
                if (empty($mobil)){
                    $this->error('手机号不能为空');
                }
                 $strtime=time();
                $endtime=$strtime-60;//5分钟有效期
                $ms["create_time"]=array(array("egt",$endtime),array("elt",$strtime));
                $ms["mobile"]=$mobil;
                $jl=D("msg")->where($ms)->find();
              //dump($jl);die;
              if ($jl){
                    $this->error("您的短信已发送,请耐心等待");
                }

                //判断验证码是否正确
              /* if (!$this->check_verify($code)) {
                    $this->error('验证码输入错误。');
                }*/
                $cx=D("member")->field('uid')->where(array("mobile"=>$mobil))->find();
                if ($cx){
                   $this->error('该手机号已经被注册');
                }
                $bonusRule=D("bonusRule")->where(array("id"=>1))->find();
                $yzm=rand(100000, 999999); //生成验证码;
                $type=0;
                $form=$bonusRule["gs_name"];     //公司名称
                $appid=$bonusRule["appid"];       //短信ID
                $count =   M('msg')->where(array("status"=>array("gt",0)))->count();

                if($count>=10000){
                    $this->error('短信不足,请联系管理员');
                }else{
                    session('yzm',$yzm);
                    M('bonus_rule')->where(array('id'=>1))->setInc('duanxin',1);
                    $msg=msg(0,$yzm,$form,$mobil,0,$appid);

                    $this->success('发送成功,注意查收');
                }     
            }else{
          
          	$this->error("当前短信验证已关闭");
          }
    

}
}