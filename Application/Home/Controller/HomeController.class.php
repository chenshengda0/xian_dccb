<?php
namespace Home\Controller;
use Common\Controller\CommonController;
require_once APP_PATH . 'User/Conf/config.php';
require_once(APP_PATH . 'User/Common/common.php');
use Common\Api\ChangeApi;
/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends CommonController {

    protected function _initialize(){


    	parent::_initialize();

        if(false && !C('WEB_SITE_CLOSE')){
            $this->error(C('NO_BODY_TLE'));
        }


        //判断是否进行分红   暂时放着
        $sttime= strtotime(date('Y-m-d'),time());
        $endtime = $sttime + 86400;
		$dqtime = time();
		$maptiem['time'] =array('BETWEEN',array($sttime,$endtime));
        $fhtime = M('fh')->where($maptiem)->find();
		if(!$fhtime){
            //如果没有，则进行一次分红
            $fh= new ChangeApi;
             $fh->fh();
             $fh->fhc();
             $fh->daishu2();
             $fh->zzz();
            //并在数据库插入当天的时间
            $data['time'] = time();
             $result = M('fh')->add($data);

        }
        /*加载页面判断是否开启网站*/
        $opwebt = get_bonus_rule('opwebt');
        $clwebt = get_bonus_rule('clwebt');
        $webtime = date('H',time());
        if(false && ($webtime < $opwebt || $webtime > $clwebt)){
            die('hello');
             $this->redirect('Clweb/index');
        }else{
            /*验证是否登录*/
            $this->checkLogin();
            /*验证资料是否完整*/
            
            /*加载页面是读取站内信条数*/
            $this->baseInfo();
        }
    }

	/* 用户登录检测 */
	protected function checkLogin(){
		//判断该操作是否不需要验证
		//不需要验证的动作列表
		 $allow = array(
            //控制器名=>当前控制器中不需要验证的动作名列表
            'Index' => array('yanzheng','index','login','forget_pass','savepass','yanzhengma','logout','answerquestion',
                'doreset','adminlogin','checkadmin','lookpwd','freereg'
            ),
            'Home' => array('verify'),
            'User' => array('checkusernum','getbankname','regist','renzheng','tj_renzheng'),
        );
		//开始判断，得到当前控制器名全局变量$controller和动作名全局变量$action
		if (isset($allow[CONTROLLER_NAME])&& in_array(ACTION_NAME, $allow[CONTROLLER_NAME])){
			//不需要验证
			return ;
		}else{
			/* 用户登录检测 */
			is_login() || $this->error('您还没有登录，请先登录！', U('Index/index'));
			$id = is_login();
			if($id){
			    $where['uid']  = $id;
			    $info = self::$Member->where($where)->find();
			    $xinxi = empty($info['info'])?'您的账号异常':$info['info'];
			    if($info['status']!=1){
			        $this->error($xinxi, U('index/index'));
			    }
// 			    if($info['shenpi']==3){
			        
// 			    }elseif($info['shenpi']==1){
// 			        $this->error('后台审批中', U('User/renzheng'));
			        
// 			    }else{

// 			    }
			}
		}

	}
	

	/*二级密码登陆验证*/
	public function secondpassword(){
		if(IS_POST){
			$uid=is_login();
			$user=M('UcenterMember');
			$spsd=$user->where("id=$uid")->getfield('password2');
			$ppsd=I('password2');
			$ppsd = think_ucenter_md5($ppsd,UC_AUTH_KEY);

			if($ppsd==$spsd){
				session('ifpsd', true);
				$url = I('url');
				
				$this->success('二级密码验证成功',$url);
			}else{
				$this->error('二级密码输入错误');
			}
		}else{
		    
			$this->title="二级密码登录";
			$this->display('Index/secondpassword');

		}
	}

    
    /**
     * 推荐未激活用户
     */
    public function baseInfo(){
        //不需要完善资料的
       
        
    	$uid = is_login();
    	$Member = M('Member');
    	$map['uid'] = $uid;
    	$info= $Member->where($map)->find();
    	$this->assign('logininfo',$info);
    	
    	$liucount = M('Liuyan')->where(array('status'=>0,'touser'=>$uid))->count();
    	$this->assign('liucount',$liucount);
   		$this->assign('wdtime',str2arr(get_bonus_rule('wd_time')));
        
   		if(!empty($info['mobile']) && !empty($info['IDcard'] &&!empty($info['alipay']) && !empty($info['banknumber']))){
            
   		    $this->assign('ziliao',1);
   		}else{
   		    $this->assign('ziliao',1);
   		}
   		$allow = array(
   		    //控制器名=>当前控制器中需要验证的动作名列表
   		    'Kung' => array('gonghui','center','jiaoyi','wodekj'),
   		);
   		
   		if (isset($allow[CONTROLLER_NAME]) && in_array(ACTION_NAME, $allow[CONTROLLER_NAME])){
   		    if(empty($info['IDcard']) || empty($info['banknumber']) || empty($info['alipay']) || empty($info['wechat'])){
   		        $this->error('请先提交真实资料','index.php?a=/home/user/pay_action/method/3');
   		    }
   		}
    }
    /* 空操作，用于输出404页面 */
    public function _empty(){
    	$this->redirect('Empty/noPage');
    }
    
    /* 验证码，用于登录 */
    public function verify(){
    	if(C('VERIFY_TYPE')==4){
    		$zhset = C('ZH_FONT');
    		$zhset = empty($zhset)?'们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这':$zhset;
    		$config = array(
    				'useZh'   =>true,
    				'zhSet'   =>$zhset,
    				'length'      =>   4,     // 验证码位数
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
    				'length'      =>   4,     // 验证码位数
    				'fontttf' =>'4.ttf',
    				'useNoise'    =>    false, // 关闭验证码杂点
    				'useCurve'  => false,
    				 
    		);
    	}
    	
    	$verify = new \Think\Verify($config);
    	$verify->entry(1);
    }
    
    public function param(){
    	$param['class'] = 'form-control m-b-10';
    	$param['style'] = 'display:inline-block;width:33%;min-width:80px;';
    	$this->assign('param',$param);
    }
    //判断网站是否开启或关闭
    public function openweb(){
       $opwebt = get_bonus_rule('opwebt');
       $clwebt = get_bonus_rule('clwebt');
       $webtime = date('H',time());

       dump($opwebt);
       dump($clwebt);
       dump($webtime);
        if($webtime > $opwebt){
             $this->redirect('index/clweb');
        }
       
    }

}
