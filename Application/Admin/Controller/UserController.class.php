<?php                                                                                                                                                      
namespace Admin\Controller;

use User\Api\UserApi;
use Common\Api\BonusApi;
/**
 * 后台用户控制器
 */
class UserController extends AdminController {
	
	
	public function _initialize(){
      
		parent::_initialize();
      
	}
    public function shuju(){
	   	 
	    $user = self::$Member->field('uid')->where(array('shenpi'=>3))->limit(0,10)->select();
     
	    foreach($user as $k=>$v){
	        $qb_address = $this->qb_address();
	        $start = rand(1,12);
	        $dizhi = substr(md5($qb_address), $start,20);
         
	        self::$Member->where(array('uid'=>$v['uid']))->setField('qianbao',$dizhi);  
	    }  
	}
	
    public function demo(){
        $user = M('member')->where(array('uid'=>4))->find();
        $bonus = new BonusApi();
        $bonus->add_suanli($user['uid'],0);
    }
    /**
     * 查询 已经注册但未激活
     */
    public function noActiveUserList(){
    	$uid = is_login();   	 
    	$map['status'] = 0 ;
    	
    	$usernumber = I('usernumber');
    	if(!empty($usernumber)){
    		$map['usernumber'] = $usernumber ;
    	}
    	$list = $this->lists(self::$Member,$map,$map,'reg_time'); 
    
    	$this->assign ('_list', $list );
    	$this->meta_title = '未激活会员';
    	$this->display ();
    	 
    }
    
    /**
     * 激活用户
     */
    public function  activeUser() {   
    	$uid = is_admin_login();
    	$id = I('id',0);
    	if($id==0){
    		$this->error('要激活用户不存在！', U('noActiveUserList'));
    	}else{
    		$map['uid'] = $id ;
    		$user = M('Member') ;
    		$member =$user->where($map)->find();
    		unset($map);
    		$status  = $member['status'];
    		if($status>0){
    			$this->error('用户已经激活！', U('noActiveUserList'));
    		}else{
    			$BonusApi = new BonusApi();
    			$result = $BonusApi->activation($id,PROJECTNUMBER);
	   			if($result>0){
	   				$map['status'] = 0;
		    		$noActive = M("Member")->where($map)->select();
		    		if(empty($noActive)){
		    			$this->success('用户激活成功', U('userIndex'));
		    		}else {
		    			$this->success('用户激活成功', U('noActiveUserList'));
		    		}
	    		}else{    				
	    			
	    			$this->error($this->showRegError($result));
	   			}
    		}
    	}
    }
    public function import1(){
    	$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
    	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
    	$maps['usernumber'] = $usernumber = I('usernumber');
    	$maps['type']=$type = I('type',1);
    	$maps['idorder'] = $sort['idorder'] = I('idorder',2);
    	$maps['field'] = $sort['field'] = I('field','uid');
    	if($sort['idorder'] == 1){
    		$order = "{$sort['field']} asc";
    	}else{
    		$order = "{$sort['field']} desc";
    	}
    	if(($starttime=='')||($endtime=='')){
    		$starttime=strtotime('-12 month');
    		$endtime=time();
    	}else{
    		$starttime = strtotime("$starttime 00:00:00");
    		$endtime = strtotime("$endtime 23:59:59");
    	}
    	
    	 
    	$map['active_time'] = array(array('gt',$starttime),array('lt',$endtime)) ;
    	if($type==3){
    		$map['isbill']= 1 ;
    	}
    	 
    	if(!empty($usernumber)){
    		if($type==2){
    			$map['realname']= $usernumber ;
    		}else{
    			$map['usernumber']= $usernumber ;
    		}
    	
    	}
    	 
    	$prefix = C('DB_PREFIX');
    	$member = $prefix.'member';
    	$umember = $prefix.'ucenter_member';
    	$map['m.status'] =array('neq',0);
    	$field = 'm.*,u.password';
    	$model = M('Member')->join('as m LEFT JOIN  '.$umember.' as u ON u.id = m.uid ');
    	//$list = $this->lists($model,$map,$maps,$order,$field);
    	$list = self::$Member->where($map,$maps)->select();
    	$this->assign ( '_list', $list );
    	$title = "会员列表";
    	$a =		array(
    			
    			array('usernumber','会员编号'),
    			array('realname','会员姓名'),
    			 
    			
    			 
    			
    			array('hasmoney','总钱包'),
    			array('hasbill','在线钱包'),
          		array('hascp','动态钱包'),
    			
    			array('pwd','密码'),
    			array('tuijiannumber','推荐人'),
    			
    			array('active_time','注册时间'),
    			array('status','会员状态'),
    	);
    	foreach ($list as $k => &$v){
    		$v['nickname'] = $v['nickname'];
    		$v['usernumber'] = $v['usernumber'];
    		$v['realname'] = $v['realname'];
    		if($v['isbill'] == 1){
	    		$v['isbill'] = '是';
    		}else{
    			$v['isbill'] = '否';
    		}
    		$v['userrank'] = strip_tags(get_userrank($v['userrank']));
    	
    		$v['hasmoney'] = $v['hasmoney'];
    		$v['hasbill'] = $v['hasbill'];
    		if($v['reg_type'] == 'ATB'){
    			$v['reg_type'] = '院线';
    		}else{
    			$v['reg_type'] = '非院线';
    		}
    		$v['pwd'] ="{$v['psd1']}/{$v['psd2']}";
    		$v['tuijiannumber'] = $v['tuijiannumber'];
    		$v['parentnumber'] = $v['parentnumber'];
    		$v['billcenterid'] = get_usernumber($v['billcenterid']);
    	
    		$v['active_time'] = date('Y-m-d H:i:s',$v['active_time']);
    		$v['status'] = get_user_status($v['status']);
    	
    	}
    	$res=exportExcel($title,$a,$list);
    }


	public function import2(){
		$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
		$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
		$maps['usernumber'] = $usernumber = I('usernumber');
		//$maps['mobile'] = $mobile= I('mobile');
		$maps['type']=$type = I('type',1);
		$maps['idorder'] = $sort['idorder'] = I('idorder',2);
		$maps['field'] = $sort['field'] = I('field','uid');
		$map["hasmoney"]=array("gt",0.000);
		if($sort['idorder'] == 1){
			$order = "{$sort['field']} asc";
		}else{
			$order = "{$sort['field']} desc";
		}
		if(($starttime=='')||($endtime=='')){
			$starttime=strtotime('-12 month');
			$endtime=time();
		}else{
			$starttime = strtotime("$starttime 00:00:00");
			$endtime = strtotime("$endtime 23:59:59");
		}

		$map['active_time'] = array(array('gt',$starttime),array('lt',$endtime)) ;
		if($type==3){
			$map['isbill']= 1 ;
		}

		if(!empty($usernumber)){
			if($type==2){
				$map['realname']= $usernumber ;
			}else{
				$map['usernumber']= $usernumber ;
			}
		}
		$prefix = C('DB_PREFIX');
		$member = $prefix.'member';
		$umember = $prefix.'ucenter_member';
		$map['m.status'] =array('neq',0);
		$field = 'm.*,u.password';
		$model = M('Member')->join('as m LEFT JOIN  '.$umember.' as u ON u.id = m.uid ');
		//$list = $this->lists($model,$map,$maps,$order,$field);
		$list = self::$Member->where($map,$maps)->select();

		$this->assign ( '_list', $list );
		$title = "持币会员列表";

		$a =array(

			array('usernumber','会员编号'),
			array('realname','会员姓名'),
			array('mobile','会员手机号'),
			array('hasmoney','总钱包'),
			array('hasbill','在线钱包'),
			array('hascp','动态钱包'),
			array('hasjifen','静态钱包'),

			array('hasfh','孵化仓'),
			array('hassf','孵化钱包'),


			array('tuijiannumber','推荐人'),

			array('active_time','注册时间'),
			array('status','会员状态'),
		);
		foreach ($list as $k => &$v){

			$v['nickname'] = $v['nickname'];
			$v['usernumber'] = $v['usernumber'];
			$v['realname'] = $v['realname'];
			if($v['isbill'] == 1){
				$v['isbill'] = '是';
			}else{
				$v['isbill'] = '否';
			}
			$v['userrank'] = strip_tags(get_userrank($v['userrank']));

			$v['hasmoney'] = $v['hasmoney'];
			$v['hasbill'] = $v['hasbill'];
			if($v['reg_type'] == 'ATB'){
				$v['reg_type'] = '院线';
			}else{
				$v['reg_type'] = '非院线';
			}
			$v['pwd'] ="{$v['psd1']}/{$v['psd2']}";

			$v['hascp'] = $v['hascp'];
			$v['hasjifen'] = $v['hasjifen'];
			$v['hasfh'] = $v['hasfh'];
			$v['hassf'] = $v['hassf'];


			$v['tuijiannumber'] = $v['tuijiannumber'];
			$v['parentnumber'] = $v['parentnumber'];
			$v['billcenterid'] = get_usernumber($v['billcenterid']);

			$v['active_time'] = date('Y-m-d H:i:s',$v['active_time']);
			$v['status'] = get_user_status($v['status']);



		}
		$res=exportExcel($title,$a,$list);
	}

    //会员升级
    public function updateLevel(){
    	$map['status']=0;
    	$list = $this->lists('update_level',$map);
    	$this->assign ( '_list', $list );
    	$this->meta_title = '会员升级申请';
    	$this->display();
    }
    //同意升级
    public function agree(){
    	$id=trim(I('id'));
    	$update=M('update_level')->where(array('uid'=>$id,'status'=>0))->find();
    	$current_time=time();
    	$back_time=$current_time+7*24*60*60;
    	$result=M('update_level')->where(array('uid'=>$id,'status'=>0))->save(array('status'=>1,'handtime'=>$current_time));
    	if($result){
	    	$row=M('member')->where(array('uid'=>$id,'status'=>1))->save(array('userrank'=>$update['userrank']));
	    	if($row){
	    		$last_hand_time=M('member')->where(array('uid'=>$id,'status'=>1))->getField('hand_time');
	    		if($last_hand_time<$current_time){
	    			M('member')->where(array('uid'=>$id,'status'=>1))->setField('lastrank',$update['oldrank']);
	    		}
	    		M('member')->where(array('uid'=>$id,'status'=>1))->setField('back_time',$back_time);
	    		 
	    		$tax = get_bonus_rule('tax');
	    		if($tax>100||$tax<=0){
	    			$tax = 0;
	    		}else{
	    			$tax = $tax*0.01;
	    		}
	    		$taxall = $update['money']*$tax ;  //税金总额
	    		finance(25, $update['money'], $taxall);
	    		
	    		$this->success('同意升级成功',U('updateLevelRecord'));
	    	}else{
	    		$this->error('同意升级失败');
	    	}
    	}
    }
    //拒绝升级
    public function refuse(){
    	$id=trim(I('id'));
    	$money=M('update_level')->where(array('uid'=>$id,'status'=>0))->getField('money');//重点
    	$result=M('update_level')->where(array('uid'=>$id,'status'=>0))->save(array('status'=>-1,'handtime'=>time()));
    	if($result){
    		M('member')->where(array('uid'=>$id,'status'=>1))->setInc('hasmoney',$money);
    		$hasmoney =M('Member')->where(array('uid'=>$id,'status'=>1))->getField('hasmoney');
    		$touserinfo =  M('Member')->where(array('uid'=>1,'status'=>1))->find();
    		$userinfo =  M('Member')->where(array('uid'=>$id,'status'=>1))->find();
    		moneyChange(1, 25, $userinfo, $touserinfo, $money, $hasmoney, 0,2);
    		$this->success('拒绝升级成功',U('updateLevelRecord'));
    	}else{
    		$this->error('拒绝升级失败');
    	}
    }
    //升级记录
    public function updateLevelRecord(){
    	$map['status']!=0;
    	$list = $this->lists('update_level',$map);
    	$this->assign ( '_list', $list );
    	$this->meta_title = '升级记录';
    	$this->display();
    }
    /**
     * 空单激活会员
     */
    public function emptyActive(){
    	$id = array_unique((array)I('id', 0));
    	$id = is_array($id) ? implode(',', $id) : $id;
    	if (empty($id)) {
    		$this->error('请选择要操作的数据!');
    	}
    	$map['uid'] = array('in', $id);
    	$data['active_type'] = 0;
    	$data['active_time'] = time();
    	$data['status'] = 1;
    	$res = self::$Member->where($map)->save($data);
    	if($res){
    		$this->success('激活成功');
    	}else{
    		$this->error('激活失败');
    	}
    }
    
    /**
     * 查询已经开通用户列表
     */
	public function userIndex(){

//     	$maps['begintime'] = $starttime = I('begintime',date("Y-m-d", strtotime('-12 month')));
//     	$maps['endtime'] = $endtime =  I('endtime',date("Y-m-d", time()));
		$maps['usernumber'] = $usernumber = preg_replace('# #','',I('usernumber')) ;
		$maps['type']=$type = I('type',1);
		$maps['idorder'] = $sort['idorder'] = I('idorder',2);
		$maps['field'] = $sort['field'] = I('field','uid');
		//$maps['userrank'] =$userrank = I('userrank');
		$maps['status'] = $status = I('status');
		$maps['mobile'] = $mobile = preg_replace('# #','',I('mobile'));


		/*	if(!empty($userrank)){
              $map['userrank'] = $userrank;
          }
        */
		if(!empty($mobile)){
			$map['mobile'] = $mobile;
		}

		if($sort['idorder'] == 1){
			$order = "{$sort['field']} asc";
		}else{
			$order = "{$sort['field']} desc";
		}
		if(($starttime=='')||($endtime=='')){
			$starttime=strtotime('-12 month');
			$endtime=time();
		}else{
			$starttime = strtotime("$starttime 00:00:00");
			$endtime = strtotime("$endtime 23:59:59");
		}


		//$map['active_time'] = array(array('gt',$starttime),array('lt',$endtime)) ;
		if($type==3){
			$map['isbill']= 1 ;
		}

		if(!empty($usernumber)){
			if($type==2){
				$map['realname']= $usernumber ;
			}else{
				$map['usernumber']= $usernumber ;

			}

		}

		$prefix = C('DB_PREFIX');
		$member = $prefix.'member';
		$umember = $prefix.'ucenter_member';
		$map['m.status'] =array('neq',0);
		if($status){
			$map['m.status']=$status;
		}

		$field = 'm.*,u.password';

		$model = M('Member')->join('as m LEFT JOIN  '.$umember.' as u ON u.id = m.uid ');
		$model =M('Member');

		$list = $this->lists($model,$map,$maps,$order,$field);

		$this->assign ( '_list', $list );

		/*追加查询条件*/
		$arr = array(
			array(
				'type'=>'select',
				'name'=>'type',
				'option'=>array(
					'1'=>'会员编号',
					'2'=>'会员姓名',
					'3'=>'报单中心',
				),
				'value'=>$maps['type'],
			)
		);
		$this->searchCondition($maps,$arr);
	//	dump($this->searchCondition($maps,$arr));
		$this->assign('sort',$sort);

		//出局
		$n=M('member')->where(array('bid'=>array('egt',1)))->count()-1;
		$m=floor($n/2);
		$this->assign('m',$m);

		$this->meta_title = '会员列表';
		$this->display ();
	}
	public function norenzheng(){
		$maps['usernumber'] = $usernumber = preg_replace('# #','',I('usernumber')) ;
		$maps['idorder'] = $sort['idorder'] = I('idorder',2);
		$maps['field'] = $sort['field'] = I('field','uid');
		$maps['mobile'] = $mobile = preg_replace('# #','',I('mobile')) ;
		$map["hasmoney"]=array("gt",0.000);


		if($sort['idorder'] == 1){
			$order = "{$sort['field']} asc";
		}else{
			$order = "{$sort['field']} desc";
		}
		if(!empty($usernumber)){
			$map['usernumber']= $usernumber ;
		}

		if(!empty($mobile)){
			$map['mobile'] = $mobile;
		}
		$prefix = C('DB_PREFIX');
		$member = $prefix.'member';
		$umember = $prefix.'ucenter_member';
		$map['m.status'] =array('neq',0);

		$field = 'm.*,u.password';
		//dump($map);dump($maps);die;
		$model = M('Member')->join('as m LEFT JOIN  '.$umember.' as u ON u.id = m.uid ');
		//dump($map['m.status']);
		$model =M('Member');

		$list = $this->lists($model,$map,$maps,$order,$field);


		$this->assign ( '_list', $list );

		/*追加查询条件*/
		$arr = array(
			array(
				'type'=>'select',
				'name'=>'type',
				'option'=>array(
					'1'=>'会员编号',
					'2'=>'会员姓名',
					'3'=>'报单中心',
				),
				'value'=>$maps['type'],
			)
		);
		$this->searchCondition($maps,$arr);
		$this->assign('sort',$sort);

		//出局
		$n=M('member')->where(array('bid'=>array('egt',1)))->count()-1;
		$m=floor($n/2);
		$this->assign('m',$m);

		$this->meta_title = '会员列表';
		$this->display ("norenzheng");
	}
    /**
     * 修改用户信息
     */
    public function   updateMemberMsg(){
    	$id = I('id',0);
    	 
    	if(IS_POST){
    		$uid = I('uid',0);
    		$qq	=I('qq');
		    		$mobile	=I('mobile');
		    		$email	=I('email');
		    		$nickname = I('nickname');
		    		$realname = I('realname');
		    		$sex = I('sex');							//性别
		    		$borth = I('borth');						//年龄
		    		$province = I('province');
		    		$city = I('city');
		    		$district = I('district');
		    		$address = I('address');
		    		$area = $province.','.$city.','.$district;
		    		$IDcard = trim(I('IDcard')); //身份证号
	    			$bankname = trim(I('bankname')); //银行名称
	    			$banknumber = trim(I('banknumber')); //银行卡号
	    			$bankholder = trim(I('bankholder')); //开户人姓名
	    			$wechat = trim(I('wechat')); 				//微信号
	    			$alipay = trim(I('alipay')); 				//支付宝账号
          			$bite = trim(I('bite'));   //比特币
          			$ytf = trim(I('ytf'));  //ytf
          			$skype = trim(I('skype'));
          			$facebook = trim(I('facebook'));

                    if(empty($realname) || empty($mobile) || empty($banknumber) || empty($bankname) || empty($alipay) || empty($wechat) || empty($IDcard)){
                        $this->error('请填写必填项');
                    }

		    		$userdata = array(
		    			'qq'=>trim($qq),
		    			'email'=>trim($email),
		    			'mobile'=>trim($mobile) ,
		    			'nickname'=>$nickname ,
		    			'realname'=>$realname ,
		    			'borth' => $borth,
		    			'sex' => $sex,
		    			'area' => $area,
		    			'address' => $address,
		    			'bankname' =>$bankname,
		    			'IDcard' =>$IDcard,
		    			'banknumber' =>$banknumber,
		    			'bankholder' =>$bankholder,
		    			'wechat' =>$wechat,
		    			'alipay' =>$alipay,
                      	'bite' =>$bite,
                      	'ytf'=>$ytf,
                      	'skype'=>$skype,
                      	'facebook'=>$facebook,
                      	
		    		);



    		$User = new UserApi;

    		$state = $User -> updateMember($uid,$userdata);
    		
    		if (!$state) {
    			$this->error('用户更新失败！');
    		}else{
    			$this->success('用户更新成功！',U('Admin/User/userIndex'));
    		}
    	}else{
    		if($id!=0){
    			$map['uid'] = $id ;
    			$user = M('Member') ;
    			$member =$user->where($map)->find();
    			$area = $member['area'];
    			$area = explode(',', $area);
    			$this->assign('area',$area);
    			$this->assign ( 'userinfo', $member );
    			$this->meta_title = '修改用户信息';
    			$this->display ();
    		}else{
    			$this->error('用户不存在！');
    		}
    	}
    
    }
    
    
    /**
     * 修改普通用户密码
     */
    public function   updateMemberPsd(){
    	$id = I('id',0);
    	if(IS_POST){
    	    $uid = I('uid',0);
    		$password1= I('password1');
    		$repassword1 = I('repassword1');
    		$password2 = I('password2');
    		$repassword2=I('repassword2');
    		/* 检测密码 */
    		if ($password1 != $repassword1) {
    		    $this -> error('一级密码和重复密码不一致！');
    		}
    		/* 检测密码 */
    		if ($password2 != $repassword2) {
    		   	$this -> error('二级密码和重复密码不一致！');
    		 }
    
    		$userdata = array(
    		    'password' => trim($password1),
    		    'password2' => trim($password2)
    		);
    
    		$User = new UserApi;
    		$state = $User -> updateMemberpsd($uid,$userdata);
    
    		if (!$state) {
    			$this->error('用户更新失败！');
    		}else{
//     			$this->decMoneyForResetPwd($uid);
    			$this->success('用户更新成功！',U('User/userIndex'));
    		}
    	}else{
    		if($id!=0){
    			$map['uid'] = $id ;
    			$user = M('Member') ;
    			$member =$user->where($map)->find();
    			$this->assign ( 'userinfo', $member );
    			$this->meta_title = '重置会员密码';
    			$this->display ();
    		}else{
    			$this->error('用户不存在！');
    		}
    	}
    
    }
    
    /**
     * 用户推荐结构树查询
    */
    public function tTree(){
    	$uid = 1;
    	if(IS_POST){
    		$usernumber = I('usernumber');
          if(empty($usernumber)){
          $uid = 1;
          }else{
          $member = M('Member');
    		$map['mobile'] = $usernumber;
    		$map['status'] = array('egt',0);
    		$userinfo = $member->where($map)->find();
    		if(empty($userinfo)){
    			$this->error('用户不存在！');
    		}
    		$uid = $userinfo['uid'];
          }
    	}
    	$this->assign('uid',0);
    	$this->assign('rootid',$uid);
		$this->meta_title="推荐拓扑图";
        $this->display();      
    }

    /**
     * 用户接点结构树查询
     */
    public function pTree(){
    	$uid = 1;
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
    	$this->meta_title="接点拓扑图";
    	$this->display();
    }

    /**
     * 会员注册
     */
    public function  registuser() {
	   		$loginid = 1;
	    	if (!C('USER_ALLOW_REGISTER')) {
	    		$this -> error('注册已关闭');
	    	}else {//显示注册表单
//    			$puid = getPosition();
//    			if($puid){//系统有会员
//	    			$puid = I('puid',$puid);
//			    	$pinfo = M('Member')->where(array('uid'=>$puid))->find();
//			    	if($pinfo['status']<=0){
//			    		$this->error('接点人尚未激活，暂时不可注册！',U('User/userindex'));
//			    	}
//    			}
	    		$puid = $loginid ;
	    		
				$userinfo['tuijiannumber']= query_user('usernumber',1);
				$userinfo['parentnumber']= query_user('usernumber',$puid);
				$userinfo['billcenternumber']= $this->getServercenter($userinfo['tuijiannumber']);
			     
				/*会员级别*/
				if(C('IS_MORE_LEVEL')){//是否是多级别
					$level_money = get_bonus_rule('level_money');
					//var_dump($level_money);
					$userrank = user_level_bonus_format($level_money);
					foreach ($userrank as $k =>&$v){
						$v[3] = get_userrank($v[1]);
					}
					//$userrank = strip_tags($userrank);
					$this->assign('userrank',$userrank);
				}
				$xm = xm2arr();
				$xs = xs2arr();

				$name = $xs[rand(0, 437)].$xm[rand(0, 37)];
				$userinfo['realname']= $name;
				
				$this->assign('table',self::$Member->find());

				$this->assign('userinfo',$userinfo);

				$this->meta_title = '会员注册';					
				$this->display();
	    	}
    }

    /**
     * 删除用户信息
     */
    public  function  deleteMember(){
    	$id = I('id',0);
    	if($id){
    		$status  = self::$Member->where(array('uid'=>$id))->getField('status');
    		if($status==0){
    			$res = $this->delNoActive($id);
    			if($res){
    				$this->success('删除成功！', U('User/noActiveUserList'));
    			}else{
    				$this->error('删除失败！', U('User/noActiveUserList'));
    			}
    		}else{
    			$tcount = self::$Member->where(array('tuijianid'=>$id))->find();
    			$pcount = self::$Member->where(array('parentid'=>$id))->find();
    			if($tcount||$pcount){
    				$this->error('该用户存在下级会员暂不允许删除！', U('User/userIndex'));
    			}else{
    				$res = $this->delLastUser($id);
    				if($res){
    					$this->success('删除成功！', U('User/userIndex'));
    				}else{
    					$this->error('删除失败！', U('User/userIndex'));
    				}
    			}
    		}
    	}else{
    		$this->error('请选择要删除的会员');
    	}
    	
    }
    /**
     * 删除未激活会员
     */
    private function delNoActive($uid){
    	$user = self::$Member->where(array('uid'=>$uid))->find();
    	if(self::$Member->where(array('uid'=>$uid))->delete()){
    		M('ucenter_member')->where(array('id'=>$uid))->delete();
    		return true;
    	}else{
    		return false;
    	}
    }
    
    /**
     * 删除末单会员
     */
    private function delLastUser($uid){
    	$user = self::$Member->where(array('uid'=>$uid))->find();
    	if(self::$Member->where(array('uid'=>$uid))->delete()){
    		$uid = M('ucenter_member')->where(array('id'=>$uid))->delete();
    		self::$Member->where(array('uid'=>$user['tuijianid']))->setDec('recom_num',1);
    		return true;
    	}else{
    		return false;
    	}
    }
    
	/**
     * 冻结/解冻账户
     */
    public function freezeStatus($method){
    	$id = array_unique((array)I('id', 0));
    	$id = is_array($id) ? implode(',', $id) : $id;
    	if (empty($id)) {
    		$this->error('请选择要操作的数据!');
    	}
        $content = I('content');
    	$map['uid'] = array('in', $id);
    	switch (strtolower($method)) {
    		case 'freeze':
    		    $data['status'] = -1;
    		    $data['info'] = $content;
    			$res = self::$Member->where($map)->save($data);
    			if($res){
    				$this->success('会员冻结成功');
    			}else{
    				$this->error('会员冻结失败');
    			}
    			break;
    		case 'unfreeze':
    			$res = self::$Member->where($map)->save(array('status'=>1,'times'=>0));
    			if($res){
    				$this->success('会员解冻成功');
    			}else{
    				$this->error('会员解冻失败');
    			}
    			break;
    		default:
    			$this->error('参数非法');
    	}
    }
    
//     public function updateLevel(){
//     	if(IS_POST){
//     		$usernumber = trim(I('usernumber')); //升级会员
//     		$lvtype = trim(I('lvtype'));//升级类型
//     		$uptype = trim(I('uptype')); //升级方式
//     		$uinfo= self::$Member->where(array('usernumber'=>$usernumber,'status'=>1))->find();
//     		if(!$uinfo){
//     			$this->error('所要升级的会员不存在或已冻结');
//     		}
    	
//     		if($lvtype == 2){
//     			$val = trim(I('userrank',1));
//     			$field = 'userrank';
//     			$new_bill_money = get_user_level_moeny($val, get_bonus_rule(), 'level_money');//新级别所需报单费
//     			if(($uptype==1)&&($val>$uinfo['userrank'])){//扣币升级
//     				if($new_bill_money>$uinfo['bill_money']){
//     					$need_money = $new_bill_money-$uinfo['bill_money'];//升级需要补的差价
//     					if(C('IS_BILL')){
//     						$billtype = 1;
//     						$billfield = 'hasbill';
//     					}else{
//     						$billtype = 2;
//     						$billfield = 'hasmoney';
//     					}
//     					if($need_money>$uinfo[$billfield]){
//     						$this->error('该会员账户余额不足，请另选升级方式');
//     					}else{
//     						$data[$billfield] = $uinfo[$billfield] - $need_money;
//     						$type=array('recordtype'=>0,'changetype'=>25,'moneytype'=>$billtype);
//     						$money_arr=array('money'=>$need_money,'hasmoney'=>$data[$billfield],'taxmoney'=>0);
//     						money_change($type, $uinfo, get_com(), $money_arr);
//     					}
//     				}
//     			}
//     		}
    		
//     		if($lvtype == 3){
//     			$val = trim(I('reg_type',0));
//     			$field = 'reg_type';
//     		}
    		
//     		$data[$field] = $val;
//     		$data['bill_money'] = $new_bill_money;//新级别报单费
//     		$res = self::$Member->where(array('usernumber'=>$usernumber))->save($data);
//     		if($res){
//     			$this->success('级别修改成功',U('userIndex'));	
//     		}else{
//     			$this->success('级别修改失败');
//     		}
    	
//     	}else{
//     		$uid = trim(I('id'));
//     		$uinfo = self::$Member->where(array('uid'=>$uid))->find();
//     		$this->assign('uinfo',$uinfo);
    		 
//     		/*会员级别*/
//     		$level_money = get_bonus_rule('level_money');
//     		$userrank = arr_level($level_money);
//     		$userrank = del_array_key($userrank, $uinfo['userrank']);
//     		$this->assign('userrank',$userrank);
    		
//     		/*会员职务*/
// 			$r_type = get_bonus_rule('update_regtype');
// 			$reg_type = arr_level($r_type);
// 			$arr = array('0');
// 			$reg_type = array_merge($arr,$reg_type);
// 			$reg_type = del_array_key($reg_type, $uinfo['reg_type']);
//     		$this->assign('reg_type',$reg_type);
    		
//     		$this->meta_title = '修改会员级别';
//     		$this->display();
//     	}
//     }
    
    /**
     * 设置报单中心
     */
    public function setBill(){
    	$id = I('id',0);
    	$type=I('type');
    	
    	if($id==0){
    		$this->error('用户不存在！', U('User/userindex'));
    	}else{
    	    switch($type){
    			case 0: $msg="取消报单中心";break;
    			case 1: $msg="设为报单中心";break;
    		}
    	    $res=M('member')->where(array('uid'=>$id))->setField('isbill',$type);
    		if($res){
		    	$this->success($msg.'成功', U('User/userindex'));
    		}else{
    			$this->success($msg.'失败', U('User/userindex'));
    		}
    	}
    }
    
    public function userImg(){
    	$uid = I('uid',1);
    	$p = I('p',0);
    	$usernumber = I('usernumber');
    	if($usernumber){
    		$uid = M('Member')->where(array('usernumber'=>$usernumber))->getField('uid');
    	}
    	if($p){
    		$uid = M('Member')->where(array('uid'=>$uid))->getField('parentid');
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
    	$this->meta_title="网络图谱";
    	$this->display();
    
    }
    
    
    /**
     * 组织结构图
     */
    public function orgChart() {
    	$uid=1;
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
    	$this->meta_title = '组织结构图';
    	$this->display();
    }
    
    /**
     * 会员详细信息
     * @param unknown $uid
     */
    public function uinfo($id){
    	$uinfo = self::$Member->find($id);
    	$n=M('member')->where(array('bid'=>array('egt',1)))->count()-1;
    	$m=floor($n/2)-1;
    	$this->assign('m',$m);
    	$this->assign('uinfo',$uinfo);
    	$this->meta_title = $uinfo['realname']."的资料";
    	htmlspecialchars();
    	$this->display();
    }
    public function out(){
    	$uid = I('uid');
    	$User=self::$Member->where(array('uid'=>$uid))->find();
    	if($User){
    		$map['status']=1;
    		$map['realname']=$User['realname'];
    		$map['mobile']=$User['mobile'];
    		$map['IDcard']=$User['IDcard'];
    		$minuid=self::$Member->where($map)->min('uid');
    		if($User['uid']!=$minuid){
    			$User=self::$Member->where(array('uid'=>$minuid))->find();
    		}
    		$recom_money=get_bonus_rule('recom_money');
    		$bonusApi = new BonusApi();
    		$bonusApi -> bonusShare($recom_money, 1, $User['uid'], get_com());
    		self::$Member -> where(array('uid'=>$uid)) -> setField('oid',0);
    		$rows = self::$Member -> where(array('oid'=>array('egt',1),'status'=>1))->select();
    		foreach($rows as $k => $v){
    			self::$Member -> where(array('uid'=>$v['uid'])) -> setField('oid',($k+1));
    		}
    		$this->ajaxReturn('会员出局成功！');
    	}
    	unset($User,$recom_money);
    }
    
    /**
     * 修改会员的推荐人
     * @param int $uid 
     * @param string $tnumber
     */
    public function updateTnumber($uid,$tnumber){
    	if(IS_POST){
    		
    	}else{
    		$this->error('请安正规流程操作');
    	}
    }
    
    /**
     * 修改会员报单中心
     * @param int $uid
     * @param string  $bnumber
     */
    public function updateBnumber($uid,$bnumber){
    	if(IS_POST){
    		$bid = self::$Member->where(array('usernumber'=>$bnumber,'isbill'=>1))->getField('uid');
    		if($bid){
    			self::$Member->where(array('uid'=>$uid))->setField('billcenterid',$bid);
    			$this->success('报单中心修改成功');
    		}else{
    			$this->error('所选报单中心不存在');
    		}
    	}else{
    		$this->error('请安正规流程操作');
    	}
    }
    
    public function prize(){
    				
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
	
		
		$this->meta_title = '获奖信息';
		$this->display ();
    }
    public function shenpi(){
       
    	$maps['usernumber'] = $usernumber = I('usernumber');
    	$maps['type']=$type = I('type',1);
    	$maps['idorder'] = $sort['idorder'] = I('idorder',2);
    	$maps['field'] = $sort['field'] = I('field','uid');
    	if($sort['idorder'] == 1){
    		$order = "{$sort['field']} asc";
    	}else{
    		$order = "{$sort['field']} desc";
    	}
    	
    		
    	
    	$map['shenpi']= 1 ;
    	if($type==3){
    		$map['isbill']= 1 ;
    	}
    	
    	if(!empty($usernumber)){
    		if($type==2){
    			$map['realname']= $usernumber ;
    		}else{
    			$map['usernumber']= $usernumber ;
    		}
    		
    	}
      
    	
    	$prefix = C('DB_PREFIX');
    	$member = $prefix.'member';
    	$umember = $prefix.'ucenter_member';
    	$map['m.status'] =array('neq',0); 
    	$field = 'm.*,u.password';
     //dump($map);dump($maps);die;
    	$model = M('Member')->join('as m LEFT JOIN  '.$umember.' as u ON u.id = m.uid ');
    	$list = $this->lists($model,$map,$maps,$order,$field);
     // dump($list);die;
    	
    	$this->assign ( '_list', $list );

    	/*追加查询条件*/
    	$arr = array(
    			array(
    					'type'=>'select',
    					'name'=>'type',
    					'option'=>array(
    							'1'=>'会员编号',
    							'2'=>'会员姓名',
    							'3'=>'报单中心',
    					),
    					'value'=>$maps['type'],
    			)
    	);
    	$this->searchCondition($maps,$arr);
    	$this->assign('sort',$sort);
    	
    	//出局
    	//$n=M('member')->where(array('bid'=>array('egt',1)))->count()-1;
    	//$m=floor($n/2);
    	//$this->assign('m',$m);
    	
    	$this->meta_title = '审批认证';
    	$this->display ();
    }
    //代理商申请
    public function daili(){
        
        
        
        $maps = array();
        $map['zx_dl_shenqing.status'] = 1;
        $order = "id asc";
        $field = 'zx_member.usernumber,zx_member.realname,zx_dl_shenqing.*';
        $model =  M('dl_shenqing')->join('zx_member ON zx_member.uid = zx_dl_shenqing.userid');
        $model =  M('dl_shenqing');
         
        $result = $this->lists($model,$map,$maps,$order,$field);
        $this->assign('result',$result);
        $this->display();
    }
    //通过
    public function dl_yunxu(){
        $id = I('id');
        $tag=$this->ordersn(); //标识号
        //改变 申请状态  添加到代理商列表
        $shenqing = M('dl_shenqing')->where(array('id'=>$id,'status'=>1))->find();
        $data['userid'] = $shenqing['userid'];
        $data['areaid'] = $shenqing['area'];
        //查询该用户是否是代理
        $is_daili = M('daili')->where(array('userid'=>$shenqing['userid']))->find();
        if($is_daili){
            $this->error('该用户已经是代理');
        }
        //查询该地区是否已经有代理商
        $daili = M('daili')->where(array('areaid'=>$shenqing['area']))->find();
        if($daili){
           if($daili['userid']){
              if($daili['userid']==$shenqing['userid']){
                    $this->error('改用户已经是该地区代理');
              }else{
                    $this->error('该地区已经有代理商');
              }
                   
           }else{
              $result = M('dl_shenqing')->where(array('id'=>$id,'status'=>1))->setField('status',2);
              $res  = M('daili')->where(array('id'=>$daili['id']))->save($data);
                 if($res && $result){
                     $list = M('shop')->where(array('id'=>7))->find();
                     $data['kuangjiid']= $list['id'];
                     $data['userid'] = $shenqing['userid'];
                     $data['ttime'] = 0;
                     $data['ccreatetime'] = time();
                     $data['kuangjicode'] = $tag;
                     $data['ssuanli'] =  $list['suanli'];
                     $data['danwei'] = $list['chanliang'];
                     $data['sstatus'] = 2;
                     if(M('kuangji')->add($data)){
                         $bonus = new BonusApi();
                         $bonus->add_suanli($shenqing['userid'],$data['ssuanli']);
                         $this->success('审批通过',U('dl_list'));
                     }else{
                         $this->error('审批失败');
                     }
                    
                 }else{
                    $this->error('审批失败');
                 }
           }
       }else{
              $res = M('daili')->add($data);
              $result = M('dl_shenqing')->where(array('id'=>$id,'status'=>1))->setField('status',2);
              if($res && $result){
              $list = M('shop')->where(array('id'=>7))->find();
                     $data['kuangjiid']= $list['id'];
                     $data['userid'] = $shenqing['userid'];
                     $data['ttime'] = 0;
                     $data['ccreatetime'] = time();
                     $data['kuangjicode'] = $tag;
                     $data['ssuanli'] =  $list['suanli'];
                     $data['danwei'] = $list['chanliang'];
                     $data['sstatus'] = 2;
                     if(M('kuangji')->add($data)){
                         $bonus = new BonusApi();
                         $bonus->add_suanli($shenqing['userid'],$data['ssuanli']);
                         $this->success('审批通过',U('dl_list'));
                     }else{
                         $this->error('审批失败');
                     }
              }else{
                   $this->error('审批失败');
              }
      }    
    }
    //冻结用户
//     public function dongjie(){
//         $id = I('id');
//         $method = I('method');
//         $content = I('content');
        
//     }
    //代理商拒绝
    public function dl_jujue(){
        $id = I('id');
        $result= M('dl_shenqing')->where(array('id'=>$id,'status'=>1))->setField('status',0);
        if($result){
            $this->success('拒绝成功');
        }else{
            $this->error('拒绝失败');
        }
    }
    //代理商列表
    public function dl_list(){
       
        
        $maps['mobile'] = $mobile = I('mobile');
        if(!empty($mobile)){
            $map['mobile'] = $mobile;
        }
        $map['userid'] =array('neq',0);
        $order = "iid desc";
        $field = 'zx_daili.areaid,zx_daili.iid,zx_daili.renshu,zx_member.*';
        $model =  M('daili')->join('zx_member ON zx_member.uid = zx_daili.userid');
        $this->searchCondition($maps);
         
        $result = $this->lists($model,$map,$maps,$order,$field);
        $this->assign('result',$result);
        $this->display();
    }
    //取消代理
    public function dl_quxiao(){
        $iid = I('iid');
        $where['iid'] = $iid;
        $result = M('daili')->where(array('iid'=>$iid))->setField('userid',0);
        if($result){
            $this->success('取消代理成功');
        }else{
            $this->error('取消代理失败');
        }
    }
    //设置vip
    public function is_vip(){
       $uid = I('id');
        $info = self::$Member->where(array('uid'=>$uid))->find();
        if($info['is_vip']==1){
            $data['vip_end'] = $info['vip_end']+30*24*3600;
        }else{
            $data['vip_end'] = time()+30*24*3600;
            $data['is_vip'] = 1;
        }
      	
        $result = self::$Member->where(array('uid'=>$uid))->save($data);
        if($result){
            $this->success('充值一个月成功');
        }else{
            $this->error('充值失败');
        }
    }
    //审批通过
    public function yunxu(){
      	$id = array_unique((array)I('id', 0));
    	$id = is_array($id) ? implode(',', $id) : $id;
      	
        if (empty($id)) {
    		$this->error('请选择要操作的数据!');
    	}
      	
      	$map['uid'] = array('in', $id);
      	$map['status'] = 1;
      	$map['shenpi'] = 1;
        if($id){
            $ttime = 15*24*60*60;
            $nowtime = time();
            $map['uid'] = array('in', $id);
            $map['status'] = 1;
            $map['shenpi'] = 1;
            $people = self::$Member->where($map)->select();
           
            foreach($people as $k=>$v){
               
                
                unlink($v['IDcard_z']);
                unlink($v['IDcard_f']);
                
                $time = $nowtime-$v['active_time'];
              	$qb_address = $this->qb_address();
                $data['shenpi'] = 3;
              	$data['sp_time'] = time();
              	$start = rand(1,12);
              	$data['qianbao'] = substr(md5($qb_address), $start,20);
                $result = self::$Member->where(array('uid'=>$v['uid']))->save($data);
                $bonus = new BonusApi();
                $bonus->dl_month($v['city']);
                if($time<=$ttime){
                    $data=array();
                    $id= get_bonus_rule('song');
                    $list = M('shop')->where(array('id'=>$id,'status'=>1))->find();
                  	$have = M('kuangji')->where(array('userid'=>$v['uid']))->find();
                  	if($have){
                    	
                    }else{
                          $tag=$this->ordersn(); //标识号
                      	  $data['kuangjicode'] = $tag;
                    	  $data['kuangjiid']= $id;
                          $data['userid'] = $v['uid'];
                          $data['ttime'] = 0;
                      	  $data['sstatus'] = 2;
                          $data['ccreatetime'] = time();
                          $data['pprevtime'] = 0;
                          $data['ssuanli'] =  $list['suanli'];
                          $data['danwei'] = $list['chanliang'];
                          
                          if(M('kuangji')->add($data)){

                              $bonus->add_suanli($v['uid'],$data['ssuanli']);
                          }
                    }
                   
                
                }
            }
            
           
            
            if($result){
                $this->success('审批通过');
            }else{
                $this->error('审批失败');
            }
        }
    }
   
    //审批拒绝
    public function jujue(){
        $id = I('id');
        $user = self::$Member->where(array('uid'=>$id))->find();
        if($user){
            unlink($user['IDcard_z']);
            unlink($user['IDcard_f']);
            $data['shenpi'] = 0;
            $data['IDcard_z'] = '';
            $data['IDcard_f'] = '';
            $data['zheng_z'] = '';
            $data['zheng_f'] = '';
            $result = self::$Member->where(array('uid'=>$id,'status'=>1,'shenpi'=>1))->save($data);
            if($result){
                $this->success('审批拒绝');
            }else{
                $this->error('审批失败');
            }
        }
    }
    //添加商家
    public function sj_add(){
        if(IS_POST){
            $sj_name = I('name');
            $sj_address = I('address');
            $id  =I('id');
            if(empty($sj_name)){
                $this->error('商家名不能为空');
            }
            if(empty($sj_address)){
                $this->error('商家地址不能为空');
            }
            $data['sj_name'] = $sj_name;
            $data['sj_address'] = $sj_address;
            $data['createtime'] = time();
            if(empty($id)){
                $result = M('shangjia')->add($data);
            }else{
                $result = M('shangjia')->where(array('id'=>$id))->save($data);
            }
            
            if($result){
                $this->success('提交成功',U('sj_list'));
            }else{
                $this->error('提交失败');
            }
        }else{
            $id = I('id');
            $sj = M('shangjia')->where(array('id'=>$id))->find();
            if($sj){
                $this->assign('sj',$sj);
            }
            $this->display();
        }
       
    }
    //商家列表
    public function sj_list(){
        $field='';
        $order ='id desc';
        $maps = array();
        $map=array();
        $sj = M('shangjia');
        $result = $this->lists($sj,$map,$maps,$order,$field);
        $this->assign('sj',$result);
        $this->display();
        
    }
    //删除商家
    public function sj_del(){
        $id = I('id');
        $result = M('shangjia')->where(array('id'=>$id))->delete();
        if($result){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
    //删除会员
    public function del(){
        $id = trim(I('id'));
      
        $result = M('member')->where(array('uid'=>$id))->delete();
       M('ucenter_member')->where(array('id'=>$id))->delete();
        if($result){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
  //修改推荐关系
  public function xg_tj(){
    $date=I("");
    
  	  $id = trim(I('uid'));
      $mobile=trim(I("mobile"));
   
      $uinfo = M('member')->where(array('uid'=>$id,"status"=>1))->find();
      $rinfo = M('member')->where(array('mobile'=>$mobile,"status"=>1))->find();

    
    //当前会员
    $map["tuijianid"]=$rinfo["uid"];
    
    if($rinfo["tuijianids"]==""){
    	$rinfo["tuijianids"]=",";
    }
    
   
    
    
    $set=count(explode(",",trim($uinfo["tuijianids"],",")));
    
 
    if($set==1){
  
     $map["tuijianids"]=",".$rinfo["uid"].",";
     $ids=",".$rinfo["uid"];
    }else{
     
      $map["tuijianids"]=$rinfo["tuijianids"].$rinfo["uid"].",";
        $ids=$rinfo["tuijianids"].$rinfo["uid"];
    }
	$map["tdeep"]=$rinfo["tdeep"]+1;
    $map["tuijiannumber"]=$rinfo["usernumber"];
  
    
    $res=M("member")->where(array("uid"=>$id))->save($map);
    
    //团队会员
    $member=D("member")->where(array("tuijianids"=>array("like","%,".$id.",%")))->select();
    
    foreach($member as $a){
      
    	$tids=strstr($a["tuijianids"],",".$id.",");
      
      	
      
     	$num2=count(explode(",",trim($tids,",")));
      	$mad["tuijianids"]=$ids.$tids;
      $mad["tdeep"]=$map["tdeep"]+$num2;
     	
      D("member")->where(array("uid"=>$a["uid"]))->save($mad);
    }
    
    $this->success("修改成功");
    
    
    
    
  }
  
    //分红
    public function fenhong(){
        $id = I('id');
        $info = self::$Member->where(array('uid'=>$id,'status'=>1))->find();
        $xing[2] = explode(',',get_bonus_rule('yixing'));//一星条件
        $xing[3] = explode(',',get_bonus_rule('erxing'));
        $xing[4] = explode(',',get_bonus_rule('sanxing'));
        $xing[5] = explode(',',get_bonus_rule('sixing'));
        
        //本月的分红
        $year=date('Y');
        $month = date('m');
        $time = mktime(0,0,0,$month,1,$year);
      	
        $result = M('month')->where(array('time'=>$time))->find();
        
        if($info){
            $bonus = $xing[$info['userrank']][5]*$result['money'];
         
            if($bonus){
              	finance(7, $bonus); //公司财务统计
                //发钱
                $data['hasmoney'] = $info['hasmoney']+$bonus;
                $data['allmoney'] = $info['allmoney']+$bonus;
                $res = self::$Member->where(array('uid'=>$id,'status'=>1))->save($data);
                if($res){
                    $type=array('recordtype'=>1,'changetype'=>7,'moneytype'=>2);
                    $money=array('money'=>$bonus,'hasmoney'=>$data['hasmoney'],'taxmoney'=>0);
                    money_change($type, $info, get_com(), $money);
                    $this->success('分红成功');
                }
            }
            
        }
    }
    function qb_address(){
		$yCode = array('K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T');
		$orderSn = $yCode[intval(date('Y')) - 2015] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%04d%02d', rand(1000, 9999),rand(0,99));
		return $orderSn;
	}
    function ordersn(){
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2015] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5);
        return $orderSn;
    }
      //会员升级
    public function updateuserrank(){
        $date=I("");
        if(is_numeric($date["bnumber"])){
            $map["uid"]=$date["uid"];
            $getuid=D("member")->where(array("uid"=>$date["uid"]))->find();
            if($date["bnumber"]==$getuid["userrank"]){
                $this->error("等级无变化");
            }
          if($date["bnumber"]>5){
                $this->error("只有1-5级");
            }

            $map["userrank"]=$date["bnumber"];
            $res=D("member")->where(array("uid"=>$date["uid"]))->save($map);
            $this->success("更新成功");
        }else{
            $this->error("请输入数字");
        }

    }
  
  
  //修改钱包状态
  public function updateNew(){
  	$date=I("");
    $user=D("member")->where(array("uid"=>$date["uid"]))->find();
    $num=$date["bnumber"];
    if($user["qb".$num]==1){
    		$map["qb".$num]=0;
    }else{
    	$map["qb".$num]=1;
     
    }
     $res=D("member")->where(array("uid"=>$date["uid"]))->save($map);
      
      if($res){
      	$this->success("更改成功");
      }
  
  }
   //修改钱包状态
  public function updateNew2(){
  	$date=I("");
    $user=D("member")->where(array("uid"=>$date["uid"]))->find();
    $num=$date["bnumber"];
    if($user["qb".$num]==1){
    		$map["qb".$num]=0;
    }else{
    	$map["qb".$num]=1;
     
    }
     $res=D("member")->where(array("uid"=>$date["uid"]))->save($map);
      
      if($res){
      	$this->success("更改成功");
      }
  
  }
  //修改钱包状态
  public function updateNew3(){
  	$date=I("");
    $user=D("member")->where(array("uid"=>$date["uid"]))->find();
    $num=$date["bnumber"];
    if($user["qb".$num]==1){
    		$map["qb".$num]=0;
    }else{
    	$map["qb".$num]=1;
     
    }
 
     $res=D("member")->where(array("uid"=>$date["uid"]))->save($map);
      
      if($res){
      	$this->success("更改成功");
      }
  
  }
  
}
