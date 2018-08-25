<?php
namespace Home\Controller;

use User\Api\UserApi;
use Common\Api\BonusApi;
use Common\Api\RelationApi;
use Common\Api\ChangeApi;
use Common\Api\Fh;
use Think\IDCard;
/**
 * 用户控制器
 */
class UserController extends HomeController{
	
	
	public function _initialize(){
		parent::_initialize();
	}
    /* 用户中心首页 */
    public function index(){
    	
    	$uid=is_login();
    	$info=M('member')->where(array('uid'=>$uid))->find();
      
      //奖金
      $strtime=strtotime(date('Y-m-d',time()));
            $endtime=strtotime(date('Y-m-d',strtotime('+1 day')));
            $map["createtime"]=array(array("egt",strtotime(date('Y-m-d'.'00:00:00',time()))),array("elt", strtotime(date('Y-m-d',strtotime('+1 day')))));
            $map["changetype"]=7;
            $map["targetuserid"]=$uid;
      
            $jj=D("moneyChange")->where($map)->getField("money");
      		
      if($jj==""){
      $jj="0.000";
      }
      $this->assign('fh',$jj);
      
      //团队持币
      
      unset($map);
      $map["tuijianids"]=array("like","%,".$uid.",%");
      $map["shenpi"]=3;
      $map["status"]=1;
      $chibi=D("member")->where(array($map))->sum("hasbill");
      
      if($chibi==""){
      $chibi="0.000";
      }
      
      
      $this->assign("cb",$chibi);
      
    	//判断签到
//     	dump($info['qiandao']);
//     	dump(strtotime('today'));
//     	die;
    	if($info['qiandao']<strtotime('today')){
    	    $this->assign('qiandao',1);//已经签到
    	}else{
    	    $this->assign('qiandao',0);
    	}
    	/*新闻公告*/
    	$category = M('category');
    	unset($map);
    	$map['name'] = 'news';
    	$map['status'] = 1;
    	$id = $category->where($map)->getField('id');
    	$s1=$category->where(array('status'=>1,'pid'=>$id))->select();
    	foreach($s1 as $v){
	    	$ids1.=$v['id'].',';
    	}
    	unset($map);
    	$map['category_id']=array('in',$ids1);
    	$map['status']=1;
    	$document = M('document');
    	$newsinfo = $document->where($map)->order('create_time desc')->limit(5)->select();
    	$this->assign('newsinfo',$newsinfo);
    	/*文档资料*/
    	unset($map);
    	$map['name'] = 'instroduce';
    	$map['status'] = 1;
    	$id = $category->where($map)->getField('id');
    	$s2=$category->where(array('status'=>1,'pid'=>$id))->select();
    	foreach($s2 as $v){
    		$ids2.=$v['id'].',';
    	}
    	unset($map);
    	
    	
    	$map['category_id']=array('in',$ids2);
    	$map['status']=1;
    	
    	//查看已经冻结的eve币
    	$renshu = get_bonus_rule('renshu');
      	$where=array();
      	$where['myid']=$uid;
      	$where['status']=array('in','1,2,3');
      	
    	$shuiliang = M('jiaoyi')->where($where)->sum('shuiliang');
    	$shouxu = M('jiaoyi')->where($where)->sum('shouxu');
    	$dongjie = $shuiliang+$shouxu;
    	//查询未读的文件
      	$where=array();
      	$where['yidu']=0;
      	$where['fromuserid']=$uid;
      	$where['status']=1;
      	$count = M('liuyan')->where($where)->count();
    	$instro = $document->where($map)->order('create_time desc')->limit(5)->select();
    	//判断资料是否完善
    	//签到结束在审批15天之后
      	$qd_end = $info['sp_time']+15*24*3600;
      	$nowtime = time();
      	if($nowtime>=$qd_end){
        	$this->assign('end',1);
        }else{
            $this->assign('end',0);
        }
        $this->assign('info',$info);
      	$this->assign('weidu',$count);
    	$this->assign('instro',$instro);
    	$this->assign('renshu',$renshu);
    	$this->assign('dongjie',$dongjie);
		$this->title="首页";
    	$this->display();
    }
    //申请商家
    public function shangjia(){
        $this->error('该功能暂未开放');
    }
    //申请代理商
    public function daili(){
        $uid=is_login();
        if(IS_POST){
            //1.直接推荐 10名有效矿工，算力达到0.1GH/s
            $user = self::$Member->where(array('uid'=>$uid))->find();
            $dl_tuijian = get_bonus_rule('dl_tuijian');
            $dl_suanli = get_bonus_rule('dl_suanli');
            if($user['recom_num']<$dl_tuijian){
                $this->error('您的直推矿工不足');
            }
            if($user['suanli']<$dl_suanli){
                $this->error('您的算力不足');
            }
            $province = I('province');
            $city = I('city');
            $content = trim(I('content'));
            if(strlen($content)>=200){
                $this->error('内容太多无法提交');
            }
            //四个直辖市
            $arr = ['1','2','22','9'];
            if(in_array($province,$arr)){ 
                $city = $province;
                $province = 0;
            }
            if(empty($city)){
                $this->error('请选择你想要代理的市');
            }
            $data['province'] = $province;
            $data['userid'] = $uid;
            $data['area'] = $city;
            $data['userid'] = $uid;
            $data['content'] = $content;
            $data['createtime'] = time();
            $result = M('dl_shenqing')->add($data);
            if($result){
                $this->success('申请成功,等待审批',U('index'));
            }else{
                $this->error('申请失败');
            }
        }else{
            $result = M('dl_shenqing')->where(array('userid'=>$uid,'status'=>1))->find();
            if($result){
                $this->error('后台审批中');
            }
            $param['style']='width:100px;';
            $this->assign("param",$param);
            $this->assign('result',$result);
            $this->display();
           
            
        }
       
    }
    //代理商收益
    public function dl_shouyi(){
        $uid=is_login();
        $where['targetuserid'] = $uid;
        $where['changetype'] = 15;
        $result = M('money_change')->where($where)->select();
        //累计收益
        $leiji = M('money_change')->where($where)->sum('money');
        $leiji = trim($leiji,'0');
        $this->assign('leiji',$leiji);
        $this->assign('result',$result);
        $this->display();
    }
    //会员签到
    public function qd_shouyi(){
        $uid=is_login();
        $info=M('member')->where(array('uid'=>$uid))->find();
        //签到结束在审批15天之后
      	$qd_end = $info['sp_time']+15*24*3600;
      	$nowtime = time();
      	if($nowtime>=$qd_end){
        	echo 0;
        }
        //随机AGL币
      	$min = get_bonus_rule('q_min');
      	$max = get_bonus_rule('q_max');
        $rand = rand($min,$max);
      	$rand = $rand/100;
        $data['qiandao'] = strtotime('today');
        $data['hasmoney'] = $info['hasmoney']+$rand;
        if($info['qiandao']<$data['qiandao']){
            $result = self::$Member->where(array('uid'=>$uid))->save($data);
            if($result){
                moneyChange(1,13,$info,get_com(),$rand,$data['hasmoney'],0,2);
                echo $rand;
            }else{
                echo 0;
            }
             
        }
    }
    //个人信息
    public function geren(){
        $uid=is_login();
        $info=M('member')->where(array('uid'=>$uid))->find();
        //判断是否申请代理商
        $daili = M('daili')->where(array('userid'=>$uid))->find();
        $result = M('dl_shenqing')->where(array('userid'=>$uid,'status'=>1))->find();
        
        $this->assign('result',$result);
        $this->assign('daili',$daili);
        
        $this->assign('info',$info);
        $this->display();
    }
    //申请vip
    public function vip(){
        $uid=is_login();
        $info=M('member')->where(array('uid'=>$uid))->find();
        $vip_pay = get_bonus_rule('vip_pay');
        if(IS_POST){
            $password = I('password');
            if(empty($password)){
                $this->error('请输入密码');
            }
            if($info['psd2']!=$password){
                $this->error('交易密码不正确');
            }
            if($info['hasmoney']<$vip_pay){
                $this->error('您的AGL币不足无法购买vip');
            }
           
            //扣币写流水
            $data['hasmoney'] = $info['hasmoney']-$vip_pay;
          	if($info['is_vip']==1){
            	 $data['vip_end'] = $info['vip_end']+30*24*3600;
            }else{
            	 $data['is_vip'] = 1;
              	 $data['vip_end'] = time()+30*24*3600;
            }
           
          	
            $result = self::$Member->where(array('uid'=>$uid))->save($data);
            if($result){
                moneyChange(0,12,$info,get_com(),$vip_pay,$data['hasmoney'],0,2);
                $this->success('vip购买成功');
            }else{
                $this->error('vip购买失败');
            }
            
        }else{
            $this->assign('vip_pay',$vip_pay);
          	$this->assign('info',$info);
            if($info['is_vip']==1){
                $this->display('vvip');
            }else{
                $this->display('vip');
            }
           
        }
    }
    //签名
    public function qianming(){
        $uid = is_login();
        if(IS_POST){
            $qianming = trim(I('qianming',''));
            if(strlen($qianming)>=60){
                $this->error('签名过长无法通过');
            }
            if(empty($qianming)){
                $this->error('请输入您的个性签名');
            }
            $result = self::$Member->where(array('uid'=>$uid))->setField('qianming',$qianming);
            if($result){
                $this->success('签名成功');
            }else{
                $this->error('签名失败');
            }
        }else{
            $qianming = self::$Member->where(array('uid'=>$uid))->getField('qianming');
            $this->assign('qianming',$qianming);
            $this->display();
        }
       
    }
    //实名认证
    public function renzheng(){
        $uid=is_login();
        $info=M('member')->where(array('uid'=>$uid))->find();
       
       
        $this->assign('info',$info);
        $this->display();
    }
    
    //设置
    public function set(){
        $uid = is_login();
        $info = self::$Member->where(array('uid'=>$uid,'status'=>1))->find();
        $this->assign('uinfo',$info);
        $this->display();
    }
    //合作商
    public function hezuo(){
        $shangjia = M('shangjia')->select();
        $this->assign('result',$shangjia);
        $this->display();
    }
    //修改登录
    public function edit_pass(){ 
      
       $msgoff=get_bonus_rule("gs_name");
          
          if($msgoff==1){
                $uid=is_login();
                $method = trim($_GET['method']);

                if(IS_POST){

                   $code = trim(I('code'));
                    if($code!=session('editpass')){ 
                        $this->error('验证码不正确');
                    }
                     if($method==1){

                         $data['password'] = I('newpwd1');
                         empty($data['password']) && $this->error('请输入新密码');
                         $repassword = I('rnewpwd1');
                         empty($repassword) && $this->error('请输入确认密码');
                         if($data['password'] !== $repassword){
                             $this->error('您输入的新密码与确认密码不一致');
                         }
                         $Api  =   new UserApi();
                         $res    =   $Api->updateMemberpsd($uid, $data);
                         if($res){

                             session('user_auth',null);
                             session('editpass',null);
                             $this->success('修改密码成功！',U('Index/index'));
                         }else{
                             $this->error('修改失败');
                         }
                     }else{

                         $data['password2'] = I('newpwd1');
                         empty($data['password2']) && $this->error('请输入新密码');
                         $repassword = I('rnewpwd1');
                         empty($repassword) && $this->error('请输入确认密码');
                         if($data['password2'] !== $repassword){
                             $this->error('您输入的新密码与确认密码不一致');
                         }
                         $Api    =   new UserApi();
                         $res    =   $Api->updateMemberpsd($uid, $data);
                         if($res){
                             session('ifpsd', null);
                             session('editpass',null);
                             $this->success('修改密码成功',U('User/updatePwd'));
                         }else{
                             $this->error('修改失败');
                         }
                     }
                }else{
                    $phone=M('member')->where(array('uid'=>$uid))->getField('mobile');

                    if($method==1){
                        $this->assign('title','登录');
                    }else if($method==2){
                        $this->assign('title','交易');
                    }
                    $this->assign('method',$method);
                    $this->assign('phone',$phone);
                    $this->display();
                }
          }else{
          $this->error("短信验证关闭,暂无法修改");
         }
        
    }
    //支付方式1支付宝2微信3银行卡4手机号5身份证号
    public function pay_action(){
        $uid=is_login();
        $method = I('method',3);
        if(IS_POST){
           if(get_bonus_rule('kaiguan')==0){
                $this->error('暂时无法修改资料');
            }
            $zhanghao = trim(I('zhanghao'));
            //dump($method);die;
            if($method==1){
                if(empty($zhanghao)){
                    $this->error('支付宝不能为空');
                }
                //$data['alipay'] = $zhanghao;
                $result = self::$Member->where(array('uid'=>$uid))->setField('alipay',$zhanghao);
                if($result){
                    $this->success('支付宝设置成功');
                }else{
                    $this->error('设置失败');
                }
            }else if($method==2){
                //$data['wechat'] = $zhanghao;
                if(empty($zhanghao)){
                    $this->error('微信号不能为空');
                }
                $result = self::$Member->where(array('uid'=>$uid))->setField('wechat',$zhanghao);
                if($result){
                    $this->success('微信设置成功');
                }else{
                    $this->error('设置失败');
                }
            }else if($method==3){
                $bankname = trim(I('bankname'));
              $IDcard = trim(I('IDcard'));
              $wechat = trim(I('wechat'));
              $alipaye = trim(I('alipay'));
            $realname=trim(I("realname"));

                $data['banknumber'] = $zhanghao;
                $data['bankname'] = $bankname;
              $data['IDcard'] = $IDcard;
              $data['wechat'] = $wechat;
              $data['alipay'] = $alipaye;
                $data['realname'] = $realname;

                if(empty($bankname)){
                    $this->error('请输入开户行');
                }
                if(empty($realname)){
                    $this->error('请输入用户姓名');
                }

              if(empty($IDcard)){
                    $this->error('请输入身份证');
                }



                $icard = get_bonus_rule('icard');

                if( $icard != '0'){
                    $realname = urlencode($realname);
                        $url = 'http://apis.juhe.cn/idcard/index?idcard='.$$IDcard.'&realname='.$realname.'&key=';
                        $dataa = json_decode(file_get_contents($url), true);
                        if(!is_array($dataa['result'])){
                            $dataa['result'] = json_decode($dataa['result']);
                        }
                        if($dataa['result']['res'] == 2){
                            $this->error('身份验证失败');
                        }
                }
             /* if(empty($wechat)){
                    $this->error('请输入微信号');
                }

               if(empty($alipaye)){
                    $this->error('请输入支付宝帐号');
                }*/



                $result = self::$Member->where(array('uid'=>$uid))->save($data);
                if($result){
                    $this->success('银行卡设置成功',U("kung/center"));
                }else{
                    $this->error('设置失败');
                }
            }else if($method==4){
                if(empty($zhanghao)){
                    $this->error('手机号不能为空');
                }
                $result = self::$Member->where(array('uid'=>$uid))->setField('mobile',$zhanghao);
                if($result){
                    $this->success('手机号设置成功',U("kung/center"));
                }else{
                    $this->error('设置失败');
                }
            }else if($method==5){
                if(empty($zhanghao)){
                    $this->error('身份证号不能为空');
                }
              	$IDcard = self::$Member->where(array('uid'=>$uid))->getField('IDcard');
              	if($IDcard){
                   $this->error('身份证已经审批无法修改');
                }
                $result = self::$Member->where(array('uid'=>$uid))->setField('IDcard',$zhanghao);
                if($result){
                    $this->success('身份证号设置成功',U("kung/center"));
                }else{
                    $this->error('设置失败');
                }
            }
          
            
        }else{
            if($method==1){
               $zhanghao = self::$Member->where(array('uid'=>$uid))->getField('alipay');
               $this->assign('title','支付宝'); 
            }else if($method==2){
               $zhanghao = self::$Member->where(array('uid'=>$uid))->getField('wechat');
               $this->assign('title','微信');
            }else if($method==3){
                $zhanghao = self::$Member->where(array('uid'=>$uid))->getField('banknumber');
                $bankname = self::$Member->where(array('uid'=>$uid))->getField('bankname');
                $this->assign('title','银行卡');
            }else if($method==4){
                $zhanghao = self::$Member->where(array('uid'=>$uid))->getField('mobile');
              	$this->assign('title','手机号');
            }else if($method==5){
                $zhanghao = self::$Member->where(array('uid'=>$uid))->getField('IDcard');
              	$this->assign('title','身份证号');
            }
          
          $user=D("member")->where(array("uid"=>$uid))->find();
          
          $this->assign('user',$user);
            $this->assign('bankname',$bankname);
            $this->assign('zhanghao',$zhanghao);
            $this->assign('method',$method);
            $this->display();
        }
    }
    //实名认证步骤1
    public function renzheng_one(){
        $uid = is_login();
        if(IS_POST){
            $data['realname'] = trim(I('realname'));
            $data['IDcard'] = trim(I('card'));
            $data['shenpi'] = 3;
            if(empty($data['realname'])){
                $this->error('姓名不能为空');
            }
           /* $result = IDCard::isCard($data['IDcard']);
            if(!$result){
                $this->error('请输入有效身份证');
            }
          	$user = self::$Member->where(array('IDcard'=>$data['IDcard']))->find();
            if($user){
            	$this->error('该身份证已经注册');
            }*/
            if(self::$Member->where(array('uid'=>$uid))->save($data)){
                $this->success('提交成功,耐心等待');
            }else{
                $this->error('提交失败');
            }
           
        }else{
            $user = self::$Member->field('realname,IDcard')->where(array('uid'=>$uid))->find();
            $this->assign('user',$user);
            $this->display();
        }
        
       
    }
    //检查第一步骤是否完成
    public function check_one(){
        $uid = is_login();
        $user = self::$Member->field('realname,IDcard')->where(array('uid'=>$uid))->find();
        if(empty($user['realname']) || empty($user['IDcard'])){
            $this->error('请先填写姓名和身份证号');
        }else{
            $this->success('正在跳转...',U('renzheng_two'));
        }
    }
    //实名认证步骤2
    public function renzheng_two(){
       
        $uid = is_login();
        
        if(IS_POST){
            $file = $_FILES;
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath  = './Home/idcard/'; // 设置附件上传目录
            $info   =   $upload->upload();
            if(!$info) {
                // 上传错误提示错误信息
                $this->error($upload->getError());
            }else{
                // 上传成功 获取上传文件信息
                foreach($info as $file){
                    $idcard[] =  './Uploads/'.ltrim($file['savepath'].$file['savename'],'./');
                }
            }
            $IDcard_z = $idcard[0];
            $IDcard_f = $idcard[1];
            $data['IDcard_z'] = $IDcard_z;
            $data['IDcard_f'] = $IDcard_f;
            $data['shenpi'] = 1;
            if(empty($IDcard_z) || empty($IDcard_f)){
                unlink($idcard[0]);
                unlink($idcard[1]);
                $this->error('身份证正反面都必须上传');
            }
            $result = self::$Member->where(array('uid'=>$uid,'status'=>1))->save($data);
            if($result){
                $this->success('提交成功,等待审核');
            }else{
                $this->error('提交失败');
            }
        }else{
            $result = self::$Member->field('IDcard_z,IDcard_f')->where(array('uid'=>$uid))->find();
            $this->assign('result',$result);
            $this->display();
            
        } 
    }
    //实名认证三
    public  function renzheng_three(){
        $uid = is_login();
        if(IS_POST){
            $file = $_FILES;
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath  = './Home/zheng/'; // 设置附件上传目录
            $info   =   $upload->upload();
            if(!$info) {
                // 上传错误提示错误信息
                $this->error($upload->getError());
            }else{
                // 上传成功 获取上传文件信息
                foreach($info as $file){
                    $idcard[] =  './Uploads/'.ltrim($file['savepath'].$file['savename'],'./');
                }
            }
            $IDcard_z = $idcard[0];
            $IDcard_f = $idcard[1];
            $data['zheng_z'] = $IDcard_z;
            $data['zheng_f'] = $IDcard_f;
            $data['shenpi'] = 1;
            if(empty($IDcard_z) || empty($IDcard_f)){
                unlink($idcard[0]);
                unlink($idcard[1]);
                $this->error('身份证正反面都必须上传');
            }
           
            $result = self::$Member->where(array('uid'=>$uid,'status'=>1))->save($data);
            if($result){
                $this->success('提交成功,等待审核');
            }else{
                $this->error('提交失败');
            }
        }else{
            $result = self::$Member->field('zheng_z,zheng_f')->where(array('uid'=>$uid))->find();
           
            $this->assign('result',$result);
            $this->display();
        }
       
    }
    //发送短信
    public function duanxin(){
        $mobile=trim(I("mobile"));
        if (empty($mobile)){
           $this->error('您的手机号为空,无法发送短信');
        }

		 $msgoff=get_bonus_rule("gs_name");
          
          if($msgoff==1){
            
                $strtime=time();
                $endtime=$strtime-60;//5分钟有效期
                $ms["create_time"]=array(array("egt",$endtime),array("elt",$strtime));
                $ms["mobile"]=$mobile;
                // dump($ms);die;
                $jl=D("msg")->where($ms)->find();
                if ($jl){
                    $this->error("短信已发送，请耐心等待");
                }

                $bonusRule=D("bonusRule")->where(array("id"=>1))->find();
                $yzm=rand(100000, 999999); //生成验证码;
                session('editpass',$yzm);
                $type=0;
                $form=$bonusRule["gs_name"];     //公司名称
                $appid=$bonusRule["appid"];       //短信ID
                $msg=msg(0,$yzm,$form,$mobile,$type,$appid);
                if ($msg==1){ 
                    M('bonus_rule')->where(array('id'=>1))->setInc('duanxin',1);
                    $count =   M('bonus_rule')->where(array('id'=>1))->getField('duanxin');
                    if($count>=10000){
                        $this->error('短信条数不足请联系管理员');
                    }
                    $this->success('发送成功');
                }else{
                    $this->error('发送失败');
                }
          }else{
          	$this->error("当前短信验证已关闭");
          }
        
    }
    public function  tj_renzheng(){
        $uid=is_login();
        $uinfo=M('member')->where(array('uid'=>$uid))->find();
        if(IS_POST){
            $file = $_FILES;
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath  = './Home/Uploads/'; // 设置附件上传目录
            $info   =   $upload->upload();
            if(!$info) {
                // 上传错误提示错误信息
                $this->error($upload->getError());
            }else{
                // 上传成功 获取上传文件信息
                foreach($info as $file){
                    $idcard[] =  './Uploads/'.ltrim($file['savepath'].$file['savename'],'./');
                }
            }
            $IDcard_z = $idcard[0];
            $IDcard_f = $idcard[1];
             
            $bankname = trim(I('bankname'));
            $banknumber =  trim(I('banknumber'));
            $alipay =  trim(I('alipay'));
            $bite =  trim(I('bite'));
            $IDcard =   trim(I('IDcard'));
            $skype =  trim(I('skype'));
          	$realname =  trim(I('realname'));
          	$ytf =  trim(I('ytf'));
          	$facebook =  trim(I('facebook'));
          	
          
          	if(empty($realname)){
               
                $this->error('请填写真实姓名');
            }
          
            if(empty($IDcard_z) || empty($IDcard_f)){
                unlink($idcard[0]);
                unlink($idcard[1]);
                $this->error('身份证正反面都必须上传');
            }
             
            if(empty($bankname)){
                $this->error('开户银行不能为空');
            }
            if(empty($banknumber)){
                $this->error('银行卡号不能为空');
            }
            if(empty($alipay)){
                $this->error('支付宝号不能为空');
            }
        
           
            $data['bankname'] = $bankname;
            $data['banknumber'] = $banknumber;
            $data['alipay'] = $alipay;
            $data['bite'] = $bite;
            $data['IDcard'] = $IDcard;
            $data['IDcard_z'] = $IDcard_z;
            $data['IDcard_f'] = $IDcard_f;
            $data['shenpi'] = 1;
          	$data['skype'] = $skype;
          	$data['realname'] = $realname;
           $data['ytf'] = $ytf;
           $data['facebook'] = $facebook;
            $result = self::$Member->where(array('uid'=>$uid,'status'=>1))->save($data);
           if($result){
                $this->success('认证成功',U('User/index'));
            }else{
                $this->error('认证失败',U('User/index'));
            }
             
        }
    }
    public function demo(){
        $this->display();
    }
    /**
     * 会员拓扑图
     */
    public function tTree(){
    	
    		$loginid = $rootid =$uid = is_login();
    	
    		$id = I('id');
    		$member = M('Member');
    		$userinfo = $member->where(array('uid'=>$rootid))->find();
//     		$parentids = $userinfo['tuijianids'].','.$uid;
    		$parentids = ','.$uid.',';
    		$map['tuijianids'] = array('like','%'.$parentids.'%');
    		$maxdeep = $member->where($map)->max('tdeep');
    		$level = $maxdeep-$userinfo['tdeep'];
    		if(empty($maxdeep)){
    			$level = 0;
    		}
    		if($id){
    			$uid = $id;
    		}
    				
    		$position = I('position',0);
    		$this->assign('pst',$position);
    		$this->assign('uid',0);
    		$this->assign('rootid',$uid);
    		$this->assign('loginid',$loginid);
    		$this->assign('level',$level);
    		$this->assign('tdeep',$userinfo['tdeep']);

	    	$this->title = "会员拓扑图";
	    	$this->display();
    }
    //会员升级
    public function updateLevel(){
    	$uid=is_login();
    	$userinfo=M('Member')->where(array('uid'=>$uid,'status'=>1))->find();
    	if(!$userinfo){
    		$this->error('所要升级的会员不存在或已冻结！');
    	}
    	$uinfo=M('update_level')->where(array('uid'=>$uid,'status'=>0))->find();
    	if($uinfo){
    		$this->error('您上次升级申请尚未审核，暂时不可再次申请！');
    	}
    	$level_money = get_bonus_rule('level_money');
    	$userrank = user_level_bonus_format($level_money);
    	foreach ($userrank as $k =>&$v){
    		$v[3] = get_userrank($v[1]);
    	}
    	if(IS_POST){
    		$rank=trim(I('userrank'));
    		$old_money=$userrank[$userinfo['userrank']][2];
    		$new_money=$userrank[$rank][2];
    		if($userinfo['active_type']==1){
    			$money=$new_money-$old_money;
	    		if($userinfo['hasmoney']<$money){
	    			$this->error('对不起，该会员的奖金币余额不足，不能升级！');
	    		}
    		}else{
    			$money=0;
    		}
    		$data=array(
    				'usernumber'=>$userinfo['usernumber'],
    				'uid'=>$userinfo['uid'],
    				'oldrank'=>$userinfo['userrank'],
    				'userrank'=>$rank,
    				'money'=>$money,
    				'createtime'=>time(),
    		);
    		$id=M('update_level')->add($data);
    		if($id){
    			$t=60*60*24*7;
    			$current_time=time()+$t;
    			M('member')->where(array('uid'=>$userinfo['uid']))->save(array('back_time'=>$current_time));
    			if($userinfo['active_type']==1){
    				$touserinfo =  M('Member')->where(array('uid'=>1))->find();
    				$money = M('update_level')->where(array('id'=>$id))->getField('money');
    				M('member')->where(array('usernumber'=>$userinfo['usernumber']))->setDec('hasmoney',$money);
    				$hasmoney =M('Member')->where(array('usernumber'=>$userinfo['usernumber']))->getField('hasmoney');
    				moneyChange(0, 25, $userinfo, $touserinfo, $money, $hasmoney, 0,2);
    			}
    			$this->success('升级申请成功！',U('updateLevelRecord'));
    		}else{
    			$this->error('升级申请失败！');
    		}
    	}
    	array_pop($userrank);
    	$end=end($userrank);
    	if($userinfo['userrank']==$end[1]){
    		$this->error('该会员已是最高级别，无法再升级！');
    	}
    	if($userinfo['userrank']<$end[1]){
	    	$userrank=array_slice($userrank, $userinfo['userrank']);
    	}
    	$this->title = '会员升级';
    	$this->assign('userinfo',$userinfo);
    	$this->assign('rank',$userrank);
    	$this->display();
    }
    /**
     * 升级申请记录
     */
    public function  updateLevelRecord(){
    	$uid=is_login();
    	$map['uid']=$uid;
    	$list = $this->lists('update_level',$map);
    	$this->assign ( '_list', $list );
    	$this->title="升级记录";
    	$this->display();
    }
    /**
     * 组织结构图
     */
    public function orgChart() {
    	$uid=is_login();
    	//第一个人信息
    	$first_userinfo=M('member')->where(array('uid'=>$uid))->find();
    	//搜索人信息
    	$usernumber=I('usernumber');
    	if(!empty($usernumber)){
    		$sea_userinfo=M('member')->where(array('usernumber'=>$usernumber,'status'=>1))->find();
    		$userinfo=$sea_userinfo;
    	}else{
    		$id=I('uid');
    		$id_userinfo=M('member')->where(array('uid'=>$id))->find();
    		$userinfo=$id_userinfo;
    	}
    	if(empty($userinfo)){
    		$userinfo=$first_userinfo;
    	}else{
    		$upinfo=M('member')->where(array('uid'=>$userinfo['parentid']))->find();
    		if(empty($upinfo)){
    			$userinfo=$first_userinfo;
    		}
    	}
    	$userinfo['uid']=$userinfo['uid'];
    	$usersons=M('member')->where(array('parentid'=>$userinfo['uid']))->order('uid ASC')->select();
    	foreach ($usersons as $k=>$v){
    		$usersons[$k]['_child']=M('member')->where(array('parentid'=>$v['uid']))->order('uid ASC')->select();
    	}
    	$userinfo['_child']=$usersons;
    	$this -> assign('userinfo', $userinfo);
    	$this->meta_title = '接点关系图';
    	$this->display();
    }
    
    
    /**
     * 用户接点结构树查询
     */
    public function pTree(){
    	$uid = is_login();
    	if(IS_POST){
    		$usernumber = I('usernumber');
    		$member = M('Member');
    		$map['usernumber'] = $usernumber;
    		$map['status'] = array('egt',0);
    		$userinfo = $member->where($map)->find();
    		if(empty($userinfo)){
    			$this->error('用户不存在！');
    		}
    		$uid = $userinfo['uid'];
    	}
    	$this->assign('uid',0);
    	$this->assign('rootid',$uid);
    	$this->title="接点拓扑图";
    	$this->display();
    }
    
    public function userImg(){
    	$uid = I('uid',is_login());
    	$p = I('p',0);
    	$usernumber = I('usernumber');
    	$roodinfo = M('Member')->where(array('uid'=>$uid))->find();
    	if(empty($roodinfo['parentids'])){
    		$parents = $uid;//接点人id组
    	}else{
//     		$parents= $roodinfo['parentids'].','.$uid;//接点人id组
    		
    		$parents=$uid;//接点人id组
    		
    	}
    	if($usernumber){
    		$map['parentids'] = array('like','%,'.$parents.',%');
    		$map['usernumber'] = $usernumber;
    		$suid = M('Member')->where($map)->getField('uid');
    		if($suid){
    			$uid = $suid;
    		}
    	}
    	
    	if($p&&$uid != is_login()){
    		$suid = M('Member')->where(array('uid'=>$uid))->getField('parentid');
    	}
    	
    	if($suid){
    		$uid = $suid;
    	}
    	$deep = 3;
    	$pdeep = M('Member')->field('pdeep')->find($uid);
    	if(!isset($pdeep)){
    		$this->error('会员信息不存在');
    	}
    	$maxdeep = $pdeep['pdeep']+$deep;
    	$html = $this->createImg($uid,$maxdeep);
    	$this->assign('html',$html);
    	$this->assign('uid',$uid);
    	$this->title="网络图谱";
    	$this->display();
    
    }


    /**
     * 会员免费注册
     */
    public function  regist() {
    	if(IS_POST){
    		/*用户基本信息*/
    		$usernumber  = trim(I('usernumber'));		//会员编号
    		$realname = I('realname');        			//会员姓名
    		$mobile =  trim(I('mobile'));            	//手机号
//    		$IDcard = trim(I('IDcard')); 				//身份证号
    		$bankname = trim(I('bankname')); 			//银行名称
    		$banknumber = trim(I('banknumber')); 		//银行卡号
    		$bankholder = $realname; 		//开户人姓名

    		/* 检测用户编号是否重复 */
    		$where['usernumber']=$usernumber;
    		if(self::$Member->where($where)->getField('uid')){
    			$this -> error('用户编号已存在！');
    		}
    		$userrank = trim(I('userrank',1));		//会员级别
    		$reg_type = trim(I('reg_type',0));		//会员类型
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
    		$table = self::$Member->find();
    		if(!empty($table)){
    			/*推荐关系*/
    			$relationApi = new RelationApi();
    			$tnumber = trim(I('tuijiannumber'));
    			if(empty($tnumber)){
    				$this->error('请选择推荐人');
    			}
    			$tdata = $relationApi->tRelation($tnumber);
    			/*报单中心*/
    			$billnumber = trim(I('billnumber'));
    			$bdata = $relationApi->billBelong($billnumber);
    		}else{
    			/*推荐人信息*/
    			$tuijiannumber = 0;
    			$tuijianid = 0;
    			$tdeep = 0;
    			/*报单中心*/
    			$isbill = 1;
    			$arruid['uid'] = 1;
    			$arruid['reg_time'] = time();
    			$arruid['billcenterid'] = 0;
    		}
    		//注册成功
    		$userdata = array(
    				'usernumber' => $usernumber,
    				'realname'=>$realname,
    				'userrank' => $userrank,
    				'oldrank' => $userrank,
    				'reg_type' =>$reg_type,
    				'isbill'=>$isbill,
    				'mobile'=>$mobile ,
    				'bankname' =>$bankname,
//    				'IDcard' =>$IDcard,
    				'banknumber' =>$banknumber,
    				'bankholder' =>$bankholder,
    				'level_bill' => 0,
    				'psd1' => $password1,
    				'psd2' => $password2,
    				'isadmin'=>false,//是否为管理员。默认无
    				'status' => 0 ,
    				'reg_time' => time(),
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
//     			$user=M('member')->where(array('uid'=>$uid))->find();
//     			if($user['tuijianid']){
//     				M('member')->where(array('uid'=>$user['tuijianid']))->setInc('recom_num',1);
//     				M('member')->where(array('uid'=>$user['tuijianid']))->setField('last_rec_time',time());
//     			}
//     			$auth = array(
//     					'uid' => $uid,
//     					'username' => get_username($uid),
//     					'last_login_time' => $user['last_login_time'],
//     			);
//     			session('ifpsd', true);
//     			session('user_auth', $auth);
//     			session('user_auth_sign', data_auth_sign($auth));
    			$this->success('注册成功,等待激活',U('Index/index'));
    		} else { //注册失败，显示错误信息
    			$this->error($this->regError($uid));
    		}
    	}
    	if (!C('USER_ALLOW_REGISTER')) {
    		$this -> error('注册已关闭');
    	}
    	$member = M('member')->select();
    	$userinfo=reset($member);
    	$uid  = trim(I('uid'));
    	if($uid){
    		$userinfo=M('member')->where(array('uid'=>$uid))->find();
    	}
    	$userinfo['billcenternumber']= $this->getServercenter($userinfo['usernumber']);
    	$this->userinfo=$userinfo;
    	$this->assign('allow_bank',C('ALLOW_BANK'));
    	$this->title = '会员免费注册';
    	$this->display();
    }
    /**
     * 会员注册
     */
    public function  registuser() {
   		$loginid = is_login();
    	if (!C('USER_ALLOW_REGISTER')) {
    		$this -> error('注册已关闭');
    	}
    	$secondpassword=is_sndpsd();
    	if(!$secondpassword){
    		$this->assign('url',U('registuser'));
    		$this->secondpassword();
    	}else{
    		$user=M('member')->where(array('uid'=>$loginid))->find();
//     		if($user['bid'] == 0){
//     			$this->error('您无权注册会员');
//     		}
    		$this->param();
//    		$puid = getPosition();
//    		if($puid){//系统有会员
//    			$puid = I('puid',$puid);
//    			$pinfo = M('Member')->where(array('uid'=>$puid))->find();
//    			if($pinfo['status']<=0){
//    				$this->error('接点人尚未激活，暂时不可注册！',U('User/userindex'));
//    			}
//    		}
            $puid = $loginid;
    		$userinfo['tuijiannumber']= query_user('usernumber',$loginid);
    		$userinfo['parentnumber']= query_user('usernumber',$puid);
    		$userinfo['billcenternumber']= $this->getServercenter($userinfo['tuijiannumber']);
			
			if(C('IS_MORE_LEVEL')){
				/*会员级别*/
				$level_money = get_bonus_rule('level_money');
				$userrank = user_level_bonus_format($level_money);
				foreach ($userrank as $k =>&$v){
					$v[3] = get_userrank($v[1]);
				}
				$this->assign('userrank',$userrank);
			}
			
			$this->assign('allow_bank',C('ALLOW_BANK'));
			$this->assign('userinfo',$userinfo);
			$this->title = '会员注册';
			$this->display();
    	}
    }
    
    public function getbankname(){
    	$card = I('post.card');
    	echo bankInfo($card);
						
    }
 	/**
     * 查询已经开通用户列表
     */
    public function userIndex(){
    	$uid = is_login();
	    	/*接受查询条件*/
	    	$starttime =I('begintime');
	    	$endtime =  I('endtime');
	    	$usernumber = I('usernumber');
	    	$regtype = I('regtype');

	    	/*保存查询条件*/
	    	$maps['begintime'] = $starttime;
	    	$maps['endtime'] = $endtime;
	    	$maps['usernumber'] = $usernumber;
	    	$maps['regtype'] = $regtype;

	    	/*查询条件处理*/
    		if(($starttime=='')||($endtime=='')){
    			$starttime=strtotime('-12 month');
    			$endtime=time();
    		}else{
    			$starttime = strtotime("$starttime 00:00:00");
    			$endtime = strtotime("$endtime 23:59:59");
    		}
    		
	    	if(!empty($usernumber)){
	    		$map['usernumber'] = trim($usernumber);
	    	}
	    	/*查询条件*/
	    	$map['tuijianid'] = $uid;
	    	$map['active_time'] = array(array('gt',$starttime),array('lt',$endtime)) ;
			if(!empty($regtype)){
				$map['reg_type'] = $regtype-1;
			}
	    	$map['status'] =array('in','-1,1');
	    	
	    	/*分页查询*/
	    	$list = $this->lists('Member',$map,$maps);
	    	$this->assign ( '_list', $list );
	    	
	    	$this->searchCondition($maps);
	    	
	    	/*查询数据返回*/
	    	$starttime = date("Y-m-d", $starttime);
	    	$endtime = date("Y-m-d", $endtime);
	    	$this->assign ( 'usernumber', $usernumber );
	    	$this->assign ( 'begintime', $starttime );
	    	$this->assign ( 'endtime', $endtime );
	    	$this->assign ( 'regtype', $regtype );

			$this->title="推荐会员列表";
    		$this->display ();

    }

    /**
     * 修改用户信息
     */
  	public function updateMemberMsg(){
    		$uid = is_login();
      		$user=M('Member');
	    	$userinfo = $user->where(array('uid'=>$uid))->find();
      		$kaiguan = get_bonus_rule('kaiguan');
      		$this->assign('kaiguan',$kaiguan);
      		$this->assign('userinfo',$userinfo);
      		$this->display();
    }
    public function updateMemberMsg11(){
    	$uid = is_login();
    	$user=self::$Member->where(array('uid'=>$uid))->find();
//     	$map1['realname']=$user['realname'];
//     	$map1['mobile']=$user['mobile'];
//     	$map1['IDcard']=$user['IDcard'];
//     	$minuid=self::$Member->where($map1)->min('uid');
//     	if($uid!=$minuid){
//     		$this->error('您无权修改该信息！');
//     	}
    	$secondpassword=is_sndpsd();
    	if(!$secondpassword){
    		$this->assign('url',U('updateMemberMsg'));
    		$this->secondpassword();
    	}else{
	    	if(IS_POST){
	    			$mobile	=I('mobile');
//                    $bankidcard = trim(I('IDcard')); //身份证
	    			$bankname = trim(I('bankname')); //银行名称
	    			$banknumber = trim(I('banknumber')); //银行卡号
	    			$bankaddress = trim(I('bankaddress')); //开户行地址
		    		$userdata = array(
		    			'mobile'=>trim($mobile),
//                        'IDcard'=>trim($bankidcard),
		    			'bankname' =>$bankname,
		    			'banknumber' =>$banknumber,
		    			'bankaddress' =>$bankaddress,
		    		);
		    	$rows = self::$Member->where($map1)->select();
		    	if($rows){
		    		$res = 1;
		    		foreach($rows as $v){
		    			$flag = self::$Member->where(array('uid'=>$v['uid']))->save($userdata);
		    			if(!$flag){
		    				$res = 0;
		    			}
		    		}
		    	}
	    		
	    		if (!$res) {
	    			$this->error('用户更新失败！');
	    		}else{
	    			$this->success('用户更新成功！',U('index'));
	    		}
	    	}else{
	    		$user=M('Member');
	    		$userinfo = $user->where(array('uid'=>$uid))->find();
	    		$area = $userinfo['area'];
	    		$param = param($area);
	    		$this->assign('param',$param);
	    		$this->assign('userinfo',$userinfo);
	    		$this->param();
	
	    		$this->assign('allow_bank',C('ALLOW_BANK'));
	    		$this->title = '修改用户信息';
	    		$this->display();
	    	}
    	}

    }

    /**
     * 未激活子会员列表
     */
    public function  noActiveUserList(){
    	$uid = is_login();
    	$secondpassword=is_sndpsd();
    	if(!$secondpassword){
    		$this->assign('usname',query_user('usernumber',$uid));
    		$this->assign('url',U('noActiveUserList'));
    		$this->secondpassword();
    	}else{
    		
	    	/*接受查询条件*/
	    	$starttime =I('begintime');
	    	$endtime =  I('endtime');
	    	$usernumber = I('usernumber');
	    	/*保存查询条件*/
	    	$maps['begintime'] = $starttime;
	    	$maps['endtime'] = $endtime;
	    	$maps['usernumber'] = $usernumber;

	    	/*查询条件处理*/
    		if(($starttime=='')||($endtime=='')){
    			$starttime=strtotime('-12 month');
    			$endtime=time();
    		}else{
    			$starttime = strtotime("$starttime 00:00:00");
    			$endtime = strtotime("$endtime 23:59:59");
    		}
	    	
	    	if(!empty($usernumber)){
	    		$map['usernumber'] = $usernumber;
	    	}
	    	/*查询条件*/
	    	$map['_string'] = "billcenterid= {$uid}  or tuijianid = {$uid} or reg_uid = {$uid}";
	    	$map['status']  =   array('eq',0);
	    	$map['reg_time'] = array(array('gt',$starttime),array('lt',$endtime)) ;
	    	
	    	$active_type =  C('ACTIVE_TYPE');

	    	/*分页查询*/
	    	$list = $this->lists('Member',$map,$maps);
	    	$this->assign ('_list', $list );
	    	
	    	$this->searchCondition($maps);
	    	
	    	
	    	$this->assign('uid',$uid);
	    	$this->assign('authority',$active_type);

	    	$this->assign('title', "未激活用户列表");
	    	$this->display();
    	}

    }


    /**
     * 激活用户
     */
    public function  activeUser() {
    	$secondpassword=is_sndpsd();
    	if(!$secondpassword){
    		$this->assign('url',U('activeUser'));
    		$this->secondpassword();
    	}else{
    		if(IS_POST){
    			$uid  = is_login() ;
    			$noAtiveUid = I('id',0);//未激活用户id
    			
    			$tobe_active_user = M("Member")->where("uid=$noAtiveUid")->find();//被激活用户
    			
    			if(empty($tobe_active_user)){
    				$this->error("用户不存在！");
    			}
    			/**
    			 * 判断用户是否已经激活
    			 */
    			if($tobe_active_user['status']==1){
    				$this->error("用户已经激活！");
    			}
    				
    			$active_type =  C('ACTIVE_TYPE');
    			
    			if($active_type == 0){
    				$this->error('你无权激活该会员');
    			}
    			if($active_type == 1&&$tobe_active_user['billcenterid'] !=  $uid){
    				$this->error('你无权激活该会员');
    			}
    			if($active_type == 2&&$tobe_active_user['tuijianid'] !=  $uid){
    				$this->error('你无权激活该会员');
    			}
    			if($active_type == 3&&$tobe_active_user['parentid'] !=  $uid){
    				$this->error('你无权激活该会员');
    			}
    			
    			if($active_type == 4&&$tobe_active_user['reg_uid'] !=  $uid){
    				$this->error('你无权激活该会员');
    			}
    			$BonusApi = new BonusApi();
    			$res = $BonusApi->activation($noAtiveUid, $uid);
    			if($res>0){
    				$map['tuijianid'] = $uid;
    				$map['status'] = 0;
    				$noActive = M("Member")->where($map)->select();
    				if(empty($noActive)){
    					$this->success('用户激活成功', U('userIndex'));
    				}else {
    					$this->success('用户激活成功', U('noActiveUserList'));
    				}
    			}else{
    				$this->error($this->regError($res));
    			}
    		}else{
    			$this->error('请不要非法操作');
    		}
	    	
    	}
    }

    /**
     * 修改密码页面
     */
    public function updatePwd(){
    	$secondpassword=is_sndpsd();
    	
    	if(!$secondpassword){
    		$this->assign('url',U('User/index'));
    		
    		
    		$this->secondpassword();
    	}else{
        	$uid = is_login();
        	$this->assign('uid',$uid);
        	$this->title = '修改密码';
        	$this->display();
    	}
    	//session('ifpsd', null);
    }
    /**
     * 修改一级密码
     */
    public function updatePwd1(){
    	if(IS_POST){
	        $uid = trim(I('uid',is_login()));
	
	        $oldpwd1 = trim(I('oldpwd1'));
	        empty($oldpwd1) && $this->error('请输入原密码');
	        $data['password'] = I('newpwd1');
	        empty($data['password']) && $this->error('请输入新密码');
	        $repassword = I('rnewpwd1');
	        empty($repassword) && $this->error('请输入确认密码');
	
	        if($data['password'] !== $repassword){
	            $this->error('您输入的新密码与确认密码不一致');
	        }
	
	        $psd1 = M('Member')->where(array('uid'=>$uid))->getField('psd1');
	        if($oldpwd1!=$psd1){
	        	$this->error('原密码输入错误');
	        }
	        $Api  =   new UserApi();
	        $uid    =   $Api->login($uid, $oldpwd1, 4);
	        $res    =   $Api->updateMemberpsd($uid, $data);
	        if($res){
// 	        	$this->decMoneyForResetPwd($uid);
	        	session('user_auth',null);
	            $this->success('修改密码成功！',U('Index/index'));
	        }else{
	             $this->error('修改失败');
	        }
    	}
    }
    /**
     * 修改二级密码
     */
    public function updatePwd2(){
    	if(IS_POST){
    		$uid = trim(I('uid',is_login()));
    		$oldpwd2 = trim(I('oldpwd2'));
    		empty($oldpwd2) && $this->error('请输入原密码');
    		$data['password2'] = I('newpwd2');
    		empty($data['password2']) && $this->error('请输入新密码');
    		$repassword = I('rnewpwd2');
    		empty($repassword) && $this->error('请输入确认密码');
    		
    		if($data['password2'] !== $repassword){
    			$this->error('您输入的新密码与确认密码不一致');
    		}
    		
    		$psd2 = M('Member')->where(array('uid'=>$uid))->getField('psd2');
    		if($oldpwd2!=$psd2){
    			$this->error('原密码输入错误');
    		}
    		
    		$Api    =   new UserApi();
    		$res    =   $Api->updateMemberpsd($uid, $data);
    		if($res){
//     			$this->decMoneyForResetPwd($uid);
    			session('ifpsd', null);
    			$this->success('修改密码成功',U('User/updatePwd'));
    		}else{
    			$this->error('修改失败');
    		}
    	}
      
    }
  
    
    /**
     * 删除未激活会员
     */
    public function delMember(){
    	$id = I('id');
    	$map['uid'] = $id ;
    	$model = M('Member') ;
    	$member =$model->where($map)->find();
    	$status  = $member['status'];
    	$othermap['id'] = $id;
    	if($status==0){
    		if($model->where($map)->delete()){
    			$uid = M('ucenter_member')->where($othermap)->delete();
    			$this->success('删除成功！', U('User/noActiveUserList'));
    		}else{
    			$this->error('删除失败！');
    		}
    	}else{
    		$this->error('该会员已激活！');
    	}
    }
    
//     /**
//      * 会员升级
//      */
//     public function updateLevel($id){
//     	$uid = is_login();
    	
//     	$UCenter= self::$Member->where(array('uid'=>$uid,'status'=>1,'isbill'=>1))->find();
    	
//     	if(C('IS_BILL')){
//     		$billtype = 1;
//     		$billfield = 'hasbill';
//     	}else{
//     		$billtype = 2;
//     		$billfield = 'hasmoney';
//     	}
    	
//     	if(empty($UCenter)){
//     		$this->error('对不起，您无权进行此操作！');
//     	}
    	 
    	
//     	if(IS_POST){
//     		$uinfo= self::$Member->where(array('uid'=>$uid,'status'=>1))->find();
//     		if(!$uinfo){
//     			$this->error('所要升级的会员不存在或已冻结');
//     		}
    		
//     		$val = trim(I('userrank',1));
//     		$field = 'userrank';
//     		if($val>$uinfo['userrank']){//扣币升级
//     			$new_bill_money = get_user_level_moeny($val, get_bonus_rule(), 'level_money');//新级别所需报单费
//     			$need_money = $new_bill_money-$uinfo['bill_money'];//升级需要补的差价
//     			if($need_money>$uinfo[$billfield]){
//     				$this->error('该会员账户余额不足，无法升级');
//     			}else{
//     				$data[$billfield] = $uinfo[$billfield] - $need_money;
//     				$data['bill_money'] = $new_bill_money;
//     			}
    		
// 	    		$data[$field] = $val;
// 	    		$res = self::$Member->where(array('uid'=>$uid))->save($data);
// 	    		if($res){
// 	    			$type=array('recordtype'=>0,'changetype'=>25,'moneytype'=>$billtype);
// 	    			$money_arr=array('money'=>$need_money,'hasmoney'=>$data[$billfield],'taxmoney'=>0);
// 	    			money_change($type, $uinfo, get_com(), $money_arr);
// 	    			$this->success('升级成功',U('index'));
// 	    		}else{
// 	    			$this->success('升级失败');
// 	    		}
//     		}else{
//     			$this->error('系统不支持降级功能');
//     		}
    		 
//     	}else{
    
//     		$uinfo = self::$Member->where(array('uid'=>$id))->find();
//     		$this->assign('uinfo',$uinfo);
    		 
//     		/*会员级别*/
//     		$level_money = get_bonus_rule('level_money');
//     		$urank = user_level_bonus_format($level_money);
//     		$userrank = del_array($urank, $uinfo['userrank'],'elt');
    		
    		
//     		$up_money_arr = array();//升到不同级别对应差额
//     		$up_rank = array();//可以升的级别
//     		foreach ($userrank as $k => $val){
//     			$up_money = $val[2]-$uinfo['bill_money'];
//     			if($up_money<=$uinfo[$billfield]){
//     				$up_money_arr[$k] = $up_money;
//     				$up_rank[] = $k;
//     			}
//     			unset($up_money);
//     		}
    		
//     		$this->assign('userrank',$up_rank);
//     		$this->assign('max_level',count($urank));
    
//     		$this->title = '会员升级';
//     		$this->display();
//     	}
//     }

//     public function updateLevel(){
    	
//     	$id = trim(I('id'));
//     	$uid = is_login();
//     	$ucenter = self::$Member->where(array('uid'=>$uid,'isbill'=>1))->find();
//     	$uinfo = self::$Member->where(array('uid'=>$id,'status'=>1,'billcenterid'=>$uid))->find();
    	
//     	if(!$ucenter){
//     		$this->error('您无权进行该操作！');
//     	}
    	
//     	if(!$uinfo){
//     		$this->error('所要升级的会员不存在或已冻结或者您不是该用户报单中心！');
//     	}
    	
//     	if(IS_POST){

//     			$val = trim(I('userrank',1));
//     			$field = 'userrank';
//     			$new_bill_money = get_user_level_moeny($val, get_bonus_rule(), 'level_money');//新级别所需报单费
//     			if(($val>$uinfo['userrank'])){//扣币升级
//     				if($new_bill_money>$uinfo['bill_money']){
//     					$need_money = $new_bill_money-$uinfo['bill_money'];//升级需要补的差价
//     					if(C('IS_BILL')){
//     						$billtype = 1;
//     						$billfield = 'hasbill';
//     					}else{
//     						$billtype = 2;
//     						$billfield = 'hasmoney';
//     					}
//     					if($need_money>$ucenter[$billfield]){
//     						$this->error('您的账户余额不足，请另选升级方式');
//     					}else{
//     						$data[$billfield] = $ucenter[$billfield] - $need_money;
//     						$type=array('recordtype'=>0,'changetype'=>25,'moneytype'=>$billtype);
//     						$money_arr=array('money'=>$need_money,'hasmoney'=>$data[$billfield],'taxmoney'=>0);
//     						$res = self::$Member->where(array('uid'=>$uid))->save($data);
    						
//     						if($res){
//     							money_change($type, $ucenter, get_com(), $money_arr);
    							
//     							$data_1[$field] = $val;
//     							$data_1['bill_money'] = $new_bill_money;//新级别报单费
//     							$res = self::$Member->where(array('uid'=>$id))->save($data_1);
    							
//     							if($res){
//     								$this->success('级别修改成功',U('updatelevellist'));
//     							}else{
//     								$this->error('扣费成功，升级失败');
//     							}
    							
//     						}else{
//     							$this->error('级别修改失败');
//     						}
//     					}
//     				}else{
//     					$this->error('用户级别发生错误！请联系管理员！');
//     				}
//     			}else{
//     				$this->error('你无权降低用户级别！');
//     			}
//     	}else{
    		
    		
//     		$this->assign('uinfo',$uinfo);
//     		$this->assign('ucenter',$ucenter);
//     		/*会员级别*/
//     		$level_money = get_bonus_rule('level_money');
//     		$userrank = arr_level($level_money);
//     		$userrank = del_array_key($userrank, $uinfo['userrank']);
//     		$this->assign('userrank',$userrank);
    
//     		/*会员职务*/
//     		$r_type = get_bonus_rule('update_regtype');
//     		$reg_type = arr_level($r_type);
//     		$arr = array('0');
//     		$reg_type = array_merge($arr,$reg_type);
//     		$reg_type = del_array_key($reg_type, $uinfo['reg_type']);
//     		$this->assign('reg_type',$reg_type);
    
//     		$this->meta_title = '修改会员级别';
//     		$this->display();
//     	}
//     }
    
    
    
    /**
     * 报单中心升级会员  保单中心仅能升级以及报过单的会员
     */
    
    /**
     * 报单中心升级会员  查询保单中心激活过的用户
     */
    public function updateLevelList(){
	    	$uid = is_login();
	  
    		/*接受查询条件*/
    		$starttime =I('begintime');
    		$endtime =  I('endtime');
    		$usernumber = I('usernumber');
    		/*保存查询条件*/
    		$maps['begintime'] = $starttime;
    		$maps['endtime'] = $endtime;
    		$maps['usernumber'] = $usernumber;
    		
    		/*查询条件处理*/
    		if(($starttime=='')||($endtime=='')){
    			$starttime=strtotime('-12 month');
    			$endtime=time();
    		}else{
    			$starttime = strtotime("$starttime 00:00:00");
    			$endtime = strtotime("$endtime 23:59:59");
    		}
    		
    		if(!empty($usernumber)){
    			$map['usernumber'] = $usernumber;
    		}
    		/*查询条件*/
    		$map['_string'] = "billcenterid= {$uid} ";
    		$map['status']  =   array('eq',1);
    		$map['reg_time'] = array(array('gt',$starttime),array('lt',$endtime)) ;
    		
    		$active_type =  C('ACTIVE_TYPE');
    		
    		/*分页查询*/
    		$list = $this->lists('Member',$map,$maps);
    		$this->assign ('_list', $list );
    		
    		$this->searchCondition($maps);
    		
    		/*查询数据返回*/
//     		$starttime = date("Y-m-d", $starttime);
//     		$endtime = date("Y-m-d", $endtime);
//     		$this->assign ( 'usernumber', $usernumber );
//     		$this->assign ( 'begintime', $starttime );
//     		$this->assign ( 'endtime', $endtime );
    		
    		$this->assign('uid',$uid);
    		$this->assign('authority',$active_type);
    		
    		$this->assign('title', "会员升级");
    		$this->display();
    		 
    }
    
    
    public function setBill(){
    	$this->title = '申请成为报单中心';
    	$this->display();
    }
    
    /**
     * 修改会员图像
     */
    public function updatePic(){
    	$uid = is_login();
    	if(IS_POST){
    		$face = I('face');
    		if($face){
    			M('Member')->where(array('uid'=>$uid))->setField('face',$face);
    			$this->success('修改成功');
    		}
    	}else{
    		$face = M('Member')->where(array('uid'=>$uid))->getField('face');
    		$this->assign('face',$face);
    		$this->title='修改图像';
    		$this->display();
    	}
    }
    
    /**
     * 个性化网站背景
     */
    public function changeBg(){
    	$uid = is_login();
    	$bg = I('bg');
    	$res = M('Member')->where(array('uid'=>$uid))->setField('bg',$bg);
    	cookie('bg',$bg);
    	echo $res;
    }
    
    /**
     * 获奖公示信息
     */
    public function prize(){
    	    	//$uid = is_login();
	    	/*接受查询条件*/
	    	$starttime =I('begintime');
	    	$endtime =  I('endtime');
	    	$usernumber = I('usernumber');
	    	$regtype = I('regtype');

	    	/*保存查询条件*/
	    	$maps['begintime'] = $starttime;
	    	$maps['endtime'] = $endtime;
	    	$maps['usernumber'] = $usernumber;
	    	//$maps['regtype'] = $regtype;

	    	/*查询条件处理*/
    		if(($starttime=='')||($endtime=='')){
    			$starttime=strtotime('-12 month');
    			$endtime=time();
    		}else{
    			$starttime = strtotime("$starttime 00:00:00");
    			$endtime = strtotime("$endtime 23:59:59");
    		}
    		
	    	if(!empty($usernumber)){
	    		$map['usernumber'] = trim($usernumber);
	    	}
	    	/*查询条件*/
	    	//$map['tuijianid'] = $uid;
	    	$map['time'] = array(array('gt',$starttime),array('lt',$endtime)) ;

	    	
	    	
	    	/*分页查询*/
	    	$list = $this->lists('prize',$map,$maps);
	    	$this->assign ( '_list', $list );
	    	
	    	$this->searchCondition($maps);
	    	
	    	/*查询数据返回*/
	    	$starttime = date("Y-m-d", $starttime);
	    	$endtime = date("Y-m-d", $endtime);
	    	$this->assign ( 'usernumber', $usernumber );
	    	$this->assign ( 'begintime', $starttime );
	    	$this->assign ( 'endtime', $endtime );
	    	
			$this->title="获奖公示栏";
    		$this->display ();
    }
  	public function shangchuan(){
      	if(IS_POST){
        	$uid=is_login();
        	$uinfo=M('member')->where(array('uid'=>$uid))->find();
          	$file = $_FILES;
        
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath  =      './Home/photo/'; // 设置附件上传目录
            // 上传文件
            $info   =   $upload->upload();
          	if(!$info) {
                // 上传错误提示错误信息
                $this->error($upload->getError());
            }else{
                // 上传成功 获取上传文件信息
                foreach($info as $file){
                    $touxiang =  './Uploads/'.ltrim($file['savepath'].$file['savename'],'./');
                }
            }
          	if(empty($touxiang)){
            	$this->error('上传不能为空');
            }
          	$data['photo'] = $touxiang;
          	$result = self::$Member->where(array('uid'=>$uid,'status'=>1))->save($data);
          	if($result){
            	$this->success('上传成功',U('index'));
            }else{
            	$this->error('上传失败');
            }
          	
        }
    	$this->display();
    }
  	public function ziliao_xg(){
    	    $uid=is_login();
        	$uinfo=M('member')->where(array('uid'=>$uid))->find();
      		$this->assign('uinfo',$uinfo);
      		$this->display();
    }
  	public function xg_ziliao(){
      	 $uid=is_login();
    	$data['banknumber'] = trim(I('banknumber'));
      	$data['mobile'] = trim(I('mobile'));
      	$data['realname'] = trim(I('realname'));
      	$data['bankname'] = trim(I('bankname'));
      	$data['alipay'] = trim(I('alipay'));
      	$data['realname'] = trim(I('realname'));
      	$data['bite'] = trim(I('bite'));
      	$data['ytf'] = trim(I('ytf'));
      	$data['skype'] = trim(I('skype'));
      	$data['facebook'] = trim(I('facebook'));
      	$result = M('member')->where(array('uid'=>$uid))->save($data);
      	if($result){
            $this->success('修改成功',U('renzheng'));  
        }else{
        	$this->error('修改失败');
        }
    }
    public function uploadPicture(){
        //TODO: 用户登录检测
        
        /* 返回标准数据 */
        $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
        
        /* 调用文件上传组件上传文件 */
        $Picture = D('Picture');
        $pic_driver = C('PICTURE_UPLOAD_DRIVER');
        $info = $Picture->upload(
            $_FILES,
            C('PICTURE_UPLOAD'),
            C('PICTURE_UPLOAD_DRIVER'),
            C("UPLOAD_{$pic_driver}_CONFIG")
            ); //TODO:上传到远程服务器
        
            /* 记录图片信息 */
            if($info){
                $return['status'] = 1;
                $return = array_merge($info['download'], $return);
            } else {
                $return['status'] = 0;
                $return['info']   = $Picture->getError();
            }
           
            $id = $info['file']['id'];
            $uid = is_login();
            $user = self::$Member->where(array('uid'=>$uid))->getField('photo');
            $data['photo']=$id;
            $res = M('member')->where("uid={$uid}")->save($data);
            if($res){
                $path = M('picture')->where(array('id'=>$user))->getField('path');
                $path = '.'.$path;
                unlink($path);
                
            }
    }

    public function moneybag(){

        $uid=is_login();
        //查看已经冻结的eve币
        $renshu = get_bonus_rule('renshu');
        $where=array();
        $where['myid']=$uid;
        $where['status']=array('in','1,2,3');

        $shuiliang = M('jiaoyi')->where($where)->sum('shuiliang');
        $shouxu = M('jiaoyi')->where($where)->sum('shouxu');
        $dongjie = $shuiliang+$shouxu;

        $this->assign("dongjie",$dongjie);


        $this->display();
    }

// 推荐分享
    public function tj_fx(){


        $category = M('category');
        $document = M('document');
        $map['name'] = 'pnews';
        $map['status'] = 1;
        $id = $category->where($map)->getField('id');
        unset($map);

        $map['category_id']=47;
        $map['status']=1;

        $instro = $document->join('zx_document_article ON zx_document_article.id = zx_document.id')->where($map)->find();

        $this->assign('instro',$instro["content"]);



        $this->display();
    }
    //我的奖励
    public function  money_jl(){
      
      //个人算力
        $map["changetype"]=7;
        $map["moneytype"]=3;
      	$map["money"]=array("gt",0);
        $map["targetuserid"]=is_login();
        $geren=D("moneyChange")->where($map)->select();
      //个人总算力
      	$geren2=D("moneyChange")->where($map)->sum("money");
        
      //分享算力
      	$map["changetype"]=6;
       $fenxiang=D("moneyChange")->where($map)->select();
      //分享总算力
       $fenxiang2=D("moneyChange")->where($map)->sum("money");
      //共享算力
       $map["changetype"]=5;
       $gongxiang=D("moneyChange")->where($map)->select();
      //共享总算力
       $gongxiang2=D("moneyChange")->where($map)->sum("money");
      //团队算力
       $map["changetype"]=1;
       $tuandui=D("moneyChange")->where($map)->select();
      //团队总算力
       $tuandui2=D("moneyChange")->where($map)->sum("money");
      
      if($geren2==""){$geren2="0.00";}
      if($fenxiang2==""){$fenxiang2="0.00";}
      if($gongxiang2==""){$gongxiang2="0.00";}
      if($tuandui2==""){$tuandui2="0.00";}
      
      
      
      	$this->assign("geren",$geren);
      	$this->assign("geren2",$geren2);
      
      	$this->assign("fenxiang",$fenxiang);
      	$this->assign("fenxiang2",$fenxiang2);
      
      	$this->assign("gongxiang",$gongxiang);
      	$this->assign("gongxiang2",$gongxiang2);
      
      	$this->assign("tuandui",$tuandui);
      	$this->assign("tuandui2",$tuandui2);


        $this->display();

    }

    public function mail()
    {
        $uid=is_login();
        // $info=M('member')->where(array('uid'=>$uid))->find();
        $info=M('liuyan')->where(array('touser'=>$uid))->select();
        // dump($info);
        $this->assign("mail",$info);
        $this->display();
    }
    public function mail_cply(){
        $id = $_GET['id'];
        M('liuyan')->where(array('id'=>$id))->setField('yidu',1);

        $info=M('liuyan')->where(array('id'=>$id))->find();
        $this->assign("mail",$info);
        $this->display();
    }
    public function cply()
    {
        $id = $_GET['id'];
        $reply = $_GET['reply'];
        $res = M('liuyan')->where(array('id'=>$id))->setField('reply',$reply);
        if($res){
            $ress = M('liuyan')->where(array('id'=>$id))->setField('reply_time',time());
            if($ress){
                M('liuyan')->where(array('id'=>$id))->setField('status',1);
                echo json_encode(array('status'=>1 ,'con'=>'ok'));
            }else{
                echo json_encode(array('status'=>0 ,'con'=>'no'));
            }
        }else{
            echo json_encode(array('status'=>0 ,'con'=>'网络错误!请重试'));
        }

    }

    public function fh(){
      
    $fh= new Fh;

     $fh->mrcb();
//     $fh->fhc();
//     $fh->daishu2();
//     $fh->zzz();
    }

}