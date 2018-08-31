<?php
namespace Common\Controller;

use Think\Controller;
use User\Api\UserApi;
use Common\Api\RelationApi;
use Common\Api\BonusApi;
/**
 * 后台首页控制器
 */
class CommonController extends Controller {
	
	protected static $Member;
	protected static $modeType;
   
    /**
     * 后台控制器初始化
     */
    protected function _initialize(){
      
       /* 读取数据库中的配置 */
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config =   api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置
        self::$Member = M('Member');
        self::$modeType  = C('SELECT_MODE');//系统制度
				
    }
     
    protected function areaadd($areas){
    	$areaid = array_filter(explode(',', $areas));
    	$len = count($areaid);
    	for ($i=0;$i<$len;$i++){
    		$area[] = M('District')->where(array('id'=>$areaid[$i]))->getField('name');
    	}
    	$address = $area[0].$area[1].$area[2];
    		
    	return $address;
    }
    
    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  userList.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: userList.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: userList.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param array        $base    基本的查询条件
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @author 朱亚杰 <xcoolcc@gmail.com>
     *
     * @return array|false
     * 返回数据集
     */
    protected function lists ($model,$where=array(),$maps=array(),$order='',$field=true){
    	$options    =   array();
    	$REQUEST    =   (array)I('request.');
    	if(is_string($model)){
    		$model  =   M($model);
    	}
    
    	$OPT        =   new \ReflectionProperty($model,'options');
    	$OPT->setAccessible(true);
    
    	$pk         =   $model->getPk();
    	if($order===null){
    		//order置空
    	}else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
    		$options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
    	}elseif( $order==='' && empty($options['order']) && !empty($pk) ){
    		$options['order'] = $pk.' desc';
    	}elseif($order){
    		$options['order'] = $order;
    	}
    	unset($REQUEST['_order'],$REQUEST['_field']);
    
    	$options['where'] = array_filter((array)$where,function($val){
    		if($val===''||$val===null){
    			return false;
    		}else{
    			return true;
    		}
    	});
    	if( empty($options['where'])){
    		unset($options['where']);
    	}
    	$options      =   array_merge( (array)$OPT->getValue($model), $options );
    	$total        =   $model->where($options['where'])->count();
    
    	if( isset($REQUEST['r']) ){
    		$listRows = (int)$REQUEST['r'];
    	}else{
    		$module =  strtoupper(MODULE_NAME);
    		$pagecount = C($module.'_LIST_ROWS');
    		$listRows = (isset($pagecount)&&($pagecount>0))?$pagecount:15;
    	}
    	$page = new \Think\Page($total, $listRows, $REQUEST);
    	$page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% %GO_PAGE%');
    	$page->parameter=array_map('urlencode',$maps);
    
    	$p =$page->show();
    	$this->assign('_page', $p? $p: '');
    	$this->assign('_total',$total);
    	$options['limit'] = $page->firstRow.','.$page->listRows;
    
    	$model->setProperty('options',$options);
    
    	return $model->field($field)->select();
    }
    
    protected function createImg($uid,$deep){
    
    	$static = c('TMPL_PARSE_STRING.__STATIC__');
    	$imgleft= $static."/tree/t_tree_bottom_l.gif";
    	$imgline = $static."/tree/t_tree_line.gif";
    	$imgmid = $static."/tree/t_tree_mid.gif";
    	$imgright = $static."/tree/t_tree_bottom_r.gif";

    	
    	$imghtml ="<tr><td style='text-align: center;' colspan='4'>";
    	
    	$imghtml.="<div class='img' style='position: relative;'>";
    	$imghtml.="<img style='left:0' src='{$imgleft}'/>";
    	$imghtml.="<img style='margin-left:2px;left:0;height:30px;' width='50%' src='{$imgline}'/>";
    	$imghtml.="<img style='top:0;' src='{$imgmid}'/>";
    	$imghtml.="<img style='margin-right:2px;right:0;height: 30px;width: 50%;' src='{$imgline}'/>";
    	$imghtml.="<img style='right:0;' src='{$imgright}'/>";
    	

    	

    	$imghtml.="</div></td></tr>";
    	
    	
    
    
    	$Member = M('Member');
    	$field = 'uid,usernumber,userrank,reg_type,parentid,zone,pdeep,status,a_bill, b_bill, c_bill, d_bill, a_bill_all, b_bill_all, c_bill_all, d_bill_all';
    	$info = $Member->field($field)->find($uid);
    
    	$map['parentid']=$uid;
    	$map['zone']=1;
    	$linfo = $Member->where($map)->field($field)->find();
    	$map['zone']=2;
    	$rinfo = $Member->where($map)->field($field)->find();
    	$map['zone']=3;
    	$cinfo = $Member->where($map)->field($field)->find();
    	$map['zone']=4;
    	$dinfo = $Member->where($map)->field($field)->find();
    
    
    	$html='';
    	$html.="<table class='layer'><tr><td style='text-align: center;' colspan='4'>";
    	$html.=$this->createHtml($info);;
    	$html.="</td></tr>";
    	$html.=$imghtml;
    
    	//A区
    	$html.="<tr><td class='left' style='width: 50%;padding:0 10px;'>";
    	if($linfo){
    		if($linfo['status']==0){
    			$html.=$this->createHtml($linfo);
    		}else{
    			if($linfo['pdeep']<$deep){
    				$html.=$this->createImg($linfo['uid'],$deep);
    			}else{
    				$html.=$this->createHtml($linfo);
    			}
    		}
    	}else{
    		$html .= self::createReg($uid, 1);
    	}
    	//B区
    	$html.="</td><td style='width: 50%;padding:0 10px;'>";
    	if($rinfo){
    		if($rinfo['status']==0){
    			$html.=$this->createHtml($rinfo);
    		}else{
    			if($rinfo['pdeep']<$deep){
    				$html.=$this->createImg($rinfo['uid'],$deep);
    			}else{
    				$html.=$this->createHtml($rinfo);
    			}
    		}
    	}else{
    		$html .= self::createReg($uid, 2);
    	}
    	
    	//C区
    	$html.="</td><td style='width: 50%;padding:0 10px;'>";
    	if($cinfo){
    		if($cinfo['status']==0){
    			$html.=$this->createHtml($cinfo);
    		}else{
    			if($cinfo['pdeep']<$deep){
    				$html.=$this->createImg($cinfo['uid'],$deep);
    			}else{
    				$html.=$this->createHtml($cinfo);
    			}
    		}
    	}else{
    		$html .= self::createReg($uid, 3);
    	}
    	//D区
    	$html.="</td><td style='width: 50%;padding:0 10px;'>";
    	if($dinfo){
    		if($dinfo['status']==0){
    			$html.=$this->createHtml($dinfo);
    		}else{
    			if($dinfo['pdeep']<$deep){
    				$html.=$this->createImg($dinfo['uid'],$deep);
    			}else{
    				$html.=$this->createHtml($dinfo);
    			}
    		}
    	}else{
    		$html .= self::createReg($uid, 4);
    	}
    	
    	
    	$html.="</td></tr></table>";
    
    	return $html;
    }
    
    private function createReg($uid,$zone){
    	$href = U('registuser',array('puid'=>$uid,'zone'=>$zone));
    	$reghtml = "<table class='info'><tr><td style='min-width:80px;background:#03AE87;'>";
    	$reghtml.= "<a style='color:#fff;' href='{$href}'>注册会员</a>";
    	$reghtml.= "</td></tr></table>";
    	
    	return $reghtml;
    }
    
    private function createHtml($info){
    	$urank = '级别：'.get_userrank($info['userrank']);
    	$user = $info['usernumber'];
    	$status = $info['status'];
    	
    	$ucolor = M('RankColor')->where(array('rank'=>$info['userrank'],'types'=>0))->getField('bgcolor');
    	
    	$bill = $this->filter_bill($info);
    	if($status==0){
    		$href = U('activeUser',array('id'=>$info['uid']));
    		$infohtml.= "<table class='info' style='background:gainsboro;'><tr><td>{$user}</td></tr>";
    		$infohtml.= "<tr><td>{$urank}</td></tr>";
    		$infohtml.="<tr><td style='min-width:80px;'>";
    		$infohtml.= "<a class='ajax-get' href='{$href}' title='点击激活'>点击激活</a>";
    		$infohtml.= "</td></tr>";
    	}else{
    		$href = U('userImg',array('uid'=>$info['uid']));
    		$user = "<a href='{$href}'>{$user}</a>";
    		$total=$bill['a_bill_all']+$bill['b_bill_all']+$bill['c_bill_all']+$bill['d_bill_all'];
    		$infohtml.="<table class='info' style='margin: 0 auto;background:{$ucolor};'>";
    		$infohtml.="<tr><td style='text-align: center;' colspan='4'>".$user."</td></tr>";
    		$infohtml.="<tr><td colspan='4'>".$urank."</td></tr>";
     		$infohtml.="<tr><td>".$bill['a_bill_all']."</td><td>AB</td><td>".$bill['b_bill_all']."</td></tr>";
    		//$infohtml.="<tr><td>".$bill['a_bill']."</td><td>余单</td><td>".$bill['b_bill']."</td></tr>";
    		$infohtml.="<tr><td>".$bill['c_bill_all']."</td><td>CD</td><td>".$bill['d_bill_all']."</td></tr>";
    		//$infohtml.="<tr><td>".$bill['c_bill']."</td><td>余单</td><td>".$bill['d_bill']."</td></tr>";
    		$infohtml.="<tr><td colspan='3'>总业绩:".$total."</td></tr>";
    	}
    	$infohtml.="</table>";
    	return $infohtml;
    }
    
    private function filter_bill($allbill){
    	return $allbill;
    	if(C("IS_MORE_LEVEL")){
    		$prebill = get_user_level_moeny(1,get_bonus_rule(), 'level_money');
    	}else{
    		$prebill = get_bonus_rule('money');
    	}
    	/* $allbill['right_bill'] = floor($allbill['right_bill']/$prebill);
    	$allbill['left_bill'] = floor($allbill['left_bill']/$prebill);
    	$allbill['right_bill_all'] = floor($allbill['right_bill_all']/$prebill);
    	$allbill['left_bill_all'] = floor($allbill['left_bill_all']/$prebill);
    	  */
    	$allbill['a_bill_all'] = floor($allbill['a_bill_all']);
    	$allbill['b_bill_all'] = floor($allbill['b_bill_all']);
    	$allbill['c_bill_all'] = floor($allbill['c_bill_all']);
    	$allbill['d_bill_all'] = floor($allbill['d_bill_all']);
    }
    
    /**
     * 会员注册
     */
    public function register(){
    	if(IS_POST){
    		if(MODULE_NAME=='Admin'){
    			$loginid=0;
    		}else{
    		    
    			$loginid = is_login();
    		}
    		
    		/*用户基本信息*/
    		$usernumber   =  trim(I('mobile'));		//会员编号
    		if(preg_match("/-/",$usernumber)){
    			$this->error("会员编号中不允许出现'-'");
    		}
    		
    		$realname = I('realname');        			//会员姓名
    		$mobile =  trim(I('mobile'));            	//手机号

            if (!preg_match("/^1[34578]\d{9}$/", $mobile)) {
                 //这里有无限想象
                  $this->error("会员手机号格式错误");
            }

    		$IDcard = trim(I('IDcard')); 				//身份证号
    		/* if(empty($realname)){
    		    $this->error('真实姓名不能为空');
    		}
    		if(empty($mobile)){
    		    $this->error('手机号不能为空');
    		}
    		if(empty($IDcard)){
    		    $this->error('身份证号不能为空');
    		}
    		 */
    		
    		$sex = I('sex');							//性别
    		$borth = I('borth');						//年龄
    		$email= trim(I('email'));            		//邮箱
    		$qq = trim(I('qq'));			         	//qq号
    		$bankname = trim(I('bankname')); 			//银行名称
    		$banknumber = trim(I('banknumber')); 		//银行卡号
    		$bankholder = $realname; 		//开户人姓名
    		 
    		
    		/* 检测用户编号是否重复 */
    		$where['usernumber']=$usernumber;
    		if(self::$Member->where($where)->getField('uid')){
    			$this -> error('用户编号已存在！');
    		}
    		$userrank = trim(I('userrank',0));		//会员级别
    		$reg_type = trim(I('reg_type'));		//会员类型
    		$isbill= trim(I('isbill',0));           //是否设为报单中心
    		

    		
    		
    		$rule =  get_bonus_rule();
    		$arr_rate = user_level_bonus_format($rule['level_bill']);
    		$level_bill = $arr_rate[$userrank][2];
    		
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
    			
    			if(self::$modeType>1){
    				/*接点关系*/
    				$pnumber = trim(I('parentnumber'));
    				if(empty($pnumber)){
    					$this->error('请输入接点人');
    				}
    				$pdata = $relationApi->pRelation($pnumber);
    			}
    			/*报单中心*/
    			$billnumber = trim(I('billnumber'));
    			$bdata = $relationApi->billBelong($billnumber);
    			//点位
//     			$max_bid=M('member')->max('bid');
//     			$bid=$max_bid+1;
//     			$max_oid=M('member')->max('oid');
//     			$oid=$max_oid+1;
    
    		}else{
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
    			/*$isbill = 1;
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
    				'realname'=>$realname,
    				'userrank' => $userrank,
    				'oldrank' => $userrank,
    				'reg_type' =>$reg_type,
    				'isbill'=>$isbill,
    				'borth' => $borth,
    				'sex' => $sex,
    				'level_bill' => $level_bill,
    
    				'qq'=>$qq ,
    				'mobile'=>$mobile ,
    				'email' => $email,
    				'bankname' =>$bankname,
    				'IDcard' =>$IDcard,
    				'banknumber' =>$banknumber,
    				'bankholder' =>$bankholder,
    
    				'psd1' => $password1,
    				'psd2' => $password2,
    				'status'=>1,
    		        'active_time'=>time(),
    				'bid'=>$bid,
    				'oid'=>$oid,
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
    		/*判断是否存在接点关系*/
    	/*	if(isset($pdata)){
    			if($pdata['code']>0){
    				unset($pdata['code']);
    				$userdata = array_merge($userdata, $pdata);
    				$parentid = $userdata['parentid'];
    			}else{
    				$this->error($this->regError($pdata['code']));
    			}
    		}*/
    		
    		/*判断是否有报单中心*/
    	/*	if(isset($bdata)){
    			if($bdata['code']>0){
    				unset($bdata['code']);
    				$userdata = array_merge($userdata, $bdata);
    			}else{
    				$this->error($this->regError($bdata['code']));
    			}
    		}
    		*/
    		$User = new UserApi;
    	
    		$uid = $User-> register($userdata);


    	    if($uid>0){
    	        
    	       
    	        $info=self::$Member->where(array("uid"=>$uid))->find();
    	        self::$Member->where(array('uid'=>$info['tuijianid']))->setInc('recom_num',1);
              	$where=array();
                $where['uid'] = array('in',$info['tuijianids']);
              	$where['status'] = 1;
              	
              	self::$Member->where($where)->setInc('team',1);
    	        $bonus = new BonusApi();
    	        $bonus->add_suanli($uid,0);
    	        
    			$this->success('注册成功', U('userindex'));
    		} else { //注册失败，显示错误信息
    			$this->error($this->regError($uid));
    		}
    	}
    	 
    }
    
    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    protected function regError($code = 0){
    	switch ($code) {
    		case -1:  $error = '用户名长度必须在16个字符以内！'; break;
    		case -2:  $error = '用户名被禁止注册！'; break;
    		case -3:  $error = '用户名被占用！'; break;
    		case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
    		case -5:  $error = '邮箱格式不正确！'; break;
    		case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
    		case -7:  $error = '邮箱被禁止注册！'; break;
    		case -8:  $error = '邮箱被占用！'; break;
    		case -9:  $error = '手机格式不正确！'; break;
    		case -10: $error = '手机被禁止注册！'; break;
    		case -11: $error = '手机号被占用！'; break;
    		case -12:$error='用户名必须以中文或字母开始，只能包含拼音数字，字母，汉字！';break;
    		
    		
    		case -21:$error='请输入正确的身份证号！';break;
    		case -22:$error='请输入正确的身份证号！';break;
    		case -23:$error='该身份证号已经注册过！';break;
    
    
    		case -30:$error='用户名不存在或已被删除！';break;
    		case -31:$error='用户名已经被激活！';break;
    		case -32:$error='查询奖金规则失败！';break;
    		case -33:$error='当前用户电子币不足，请充电子币或把奖金转为电子币后在充值！';break;
    		case -34:$error='生成推荐奖失败！';break;
    		case -35:$error='生成奖金变更表失败！';break;
    
    		case -40:$error = '查询奖金规则失败';break;
    		case -41:$error = '激活会员不存在';break;
    		case -50:$error = '用户不存在';break;
    		case -51:$error = '用户已激活';break;
    		case -52:$error = '用户已退出平台';break;
    		case -100:$error = '报单电子币不足';break;
            case -200:$error = '报单注册币不足';break;
    		case -101:$error = '扣费失败';break;
    		case -102:$error = '激活失败';break;
    
    		case -1000:$error = '接点人未激活或不存在';break;
    		case -1001:$error = '已无接点位';break;
    		
    		case -2000:$error = '推荐人不存在或被冻结';break;
    		case -2001:$error = '该账户推荐人数已达上限';break;
    		case -2002:$error = '该会员无权推荐会员';break;
    		
    		case -3000:$error = '所选报单中心不存在';break;
    
    		default:  $error = $code;
    	}
    	return $error;
    }
    
    /**
     * 动态加载子节点推荐关系
     */
    public function getTree(){
    	$tuijianid = I('uid',0);
    	$rootid = I('rootid');
    	$member = M('member');
    	if($tuijianid  ==0){
    		$myself = $member->where("uid = $rootid")->find();
    		$count = $member->where("tuijianid = $rootid")->count();
    		if($count!=0){
    			$value['isParent']= true;
    		}  else {
    			$value['isParent']= false;
    		}
    
    		$value['uid'] = $rootid;
    		$value['usernumber'] = $myself['usernumber'];
    		$value['status'] = $myself['status'];
    		$value['userrank'] = get_userrank($myself['userrank']);
    		$value['realname'] = $myself['realname'];
    		$value['tdeep'] = $myself['tdeep'];
    		$list[] = $value;
    	}else{
    		$map['tuijianid'] = $tuijianid;
    		$map['status'] = array('egt',-1);
    		$info = $member->where($map)->field('uid,usernumber,realname,userrank,status,tdeep')->select();
    		foreach ($info as $value){
    			$count = $member->where("tuijianid = {$value['uid']}")->count();
    			$value['userrank'] = get_userrank($value['userrank']);
    			if($count!=0){
    				$value['isParent']= true;
    			}  else {
    				$value['isParent']= false;
    			}
    			$list[] = $value;
    		}
    	}
    
    	$this->ajaxReturn($list,'JSON');
    }
    
    /**
     * 动态查询子节点接点关系
     */
    public function getPtree(){
    	$tuijianid = I('uid',0);
    	$rootid = I('rootid');
    	$member = M('member');
    	if($tuijianid  ==0){
    		$myself = $member->where("uid = $rootid")->find();
    		$count = $member->where("uid = $rootid")->count();
    		if($count!=0){
    			$value['isParent']= true;
    		}  else {
    			$value['isParent']= false;
    		}
    		$value['uid'] = $myself['uid'];
    		$value['usernumber'] = $myself['usernumber'];
    		$value['realname'] = $myself['realname'];
    		$value['status'] = $myself['status'];
    		$value['userrank'] = get_userrank($myself['userrank']);
    		$value['reg_type'] = get_regtype($myself['reg_type']);
    		$value['znum'] = $myself['znum'];
    		$list[] = $value;
    	}else{
    		$map['parentid'] = $tuijianid;
    		$map['status'] = array('egt',-1);
    		$info = $member->where($map)->field('uid,usernumber,realname,status,reg_type,znum,userrank')->select();
    		foreach ($info as $value){
    			$value['userrank'] = get_userrank($value['userrank']);
    			$value['reg_type'] = get_regtype($value['reg_type']);
    			$count = $member->where("parentid = {$value['uid']}")->count();
    			if($count!=0){
    				$value['isParent']= true;
    			}  else {
    				$value['isParent']= false;
    			}
    			$list[] = $value;
    		}
    	}
    	$this->ajaxReturn($list,'JSON');
    }
    
    
    /**
     * 获取报单中心
     */
    public function getServercenter($usernumber){
    	$userinfo = M('member')->where(array('usernumber'=>$usernumber,'status'=>1))->find();
    	if(empty($userinfo)){
    		if(IS_AJAX){
    			$this->ajaxReturn(-1);
    		}else{
    			return false;
    		}
    	}else{
    		/*用户所属报单中心*/
    		$parentids = explode(",",$userinfo['tuijianids']);
    		$parentids[]=$userinfo['uid'];
    		$map['uid']  = array(' in ',$parentids);
    		//$map['isbill'] = array('neq',0) ;
    		$map['isbill'] = array('in','1,2') ;
    		$serverCenterid = M('Member')->where($map)->max('uid');//该用户所属报单中心id
    		if(empty($serverCenterid)){
    			if(IS_AJAX){
    				$this->ajaxReturn('公司');
    			}else{
    				return '公司';
    			}
    		}else{
    			$serverCenter = query_user('usernumber',$serverCenterid);
    			if(IS_AJAX){
    				$this->ajaxReturn($serverCenter);
    			}else{
    				return $serverCenter;
    			}
    		}
    		 
    	}
    }
    
    /**
     * 列表过滤
     */
    public function listFile($list){
    	foreach ($list as &$v){
    		$v['allmoney'] = sprintf('%0.2f',$v['bonus1']+$v['bonus3']+$v['bonus2']);
    	}
    	unset($v);
    	return $list ;
    }
    
    
    /**
     * 验证用户编号是否已经存在
     */
    
    public function checkusernum() {
    	$usernumber = I('post.usernum');
    	if(empty($usernumber)){
    		$this->ajaxReturn(-2);
    	}
    	$where['usernumber']=$usernumber;
    	$res = self::$Member->where($where)->getField('uid');
    	
    	$denyname = C('USER_NAME_BAOLIU');
    	$denyname = str2arr($denyname);
    	
    	if($res||in_array($usernumber, $denyname)){
    		$this->ajaxReturn(-1);
    	}else{
    		$this->ajaxReturn(0);
    	}
    }
    
    /**
     * 随机生成会员编号
     * @return number
     */
    public function getUsernumber(){
    	$res = rand(10000000, 99999999);
    	$usernumber = $res;
    	if(IS_AJAX){
    		$this->ajaxReturn($usernumber);
    	}else{
    		return $usernumber;
    	}
    }
    
    public function getRealname(){
    	$tnumber=I('tnumber');
    	$bnumber=I('bnumber');
    	$pnumber=I('pnumber');
    	$data['tuijianname']=M('member')->where(array('usernumber'=>$tnumber))->getField('realname');
    	$data['billname']=M('member')->where(array('usernumber'=>$bnumber))->getField('realname');
    	$data['parentname']=M('member')->where(array('usernumber'=>$pnumber))->getField('realname');
    	$this->ajaxReturn($data);
    }
    
    
    /**
     * 检测验证码
     * @param  integer $id 验证码ID
     * @return boolean     检测结果
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function check_verify($code, $id = 1){
    	$verify = new \Think\Verify();
    	return $verify->check($code, $id);
    }
    
    /**
     * 查询添加
     * @param unknown $arr
     * @param unknown $defalut
     */
    public function searchCondition($defalut,$arr=array()){
    	/*选择条件*/
    	$opt = array(
    			array(
    					'type'=>'input',
    					'name'=>'begintime',
    					'title'=>'',
    					'attr'=>array(
    								'id'=>'start',
    								'class'=>'laydate-icon',
    								'readonly'=>'readonly',
    							),
    					'value'=>$defalut['begintime'],
    			),
    			array(
    					'type'=>'input',
    					'name'=>'endtime',
    					'title'=>'',
    					'attr'=>array(
    							'id'=>'end',
    							'class'=>'laydate-icon',
    							'readonly'=>'readonly',
    					),
    					'value'=>$defalut['endtime'],
    			),
    			array(
    					'type'=>'input',
    					'name'=>'usernumber',
    					'title'=>'请输入会员编号',
    					'value'=>$defalut['usernumber'],
    			),
          		array(
    					'type'=>'input',
    					'name'=>'mobile',
    					'title'=>'请输入会员手机号',
    					'value'=>$defalut['mobile'],
    			),
          		array(
    					'type'=>'select',
    					'name'=>'userrank',
    					'option'=>array(
    							'1'=>'矿工',
    							'2'=>'一星矿主',
    							'3'=>'二星矿主',
        					    '4'=>'三星矿主',
        					    '5'=>'钻石矿主',
        					    
    					),
    					'value'=>$defalut['userrank'],
    			),
          		array(
    					'type'=>'select',
    					'name'=>'status',
    					'option'=>array(
    							'0'=>'会员状态',
    							'-1'=>'冻结会员',
    							
        					    
    					),
    					'value'=>$defalut['status'],
    			),
    			array(
    					'type'=>'select',
    					'name'=>'active_type',
    					'option'=>array(
    							'-2'=>'全部',
    							'0'=>'空单',
    							'1'=>'实单',
    					),
    					'value'=>$defalut['active_type'],
    			),
    			array(
    					'type'=>'input',
    					'ipttype'=>'hidden',
    					'name'=>'createtime',
    					'value'=>$defalut['createtime'],
    			)
    	);
//     	if(!empty($arr)){
//     		foreach ($arr as $v){
//     			if(deep_in_array($v['name'], $opt)){
//     				$this->error('查询条件中的name不能相同');
//     			}
//     		}
//     		$opt = array_merge($opt,$arr);
//     	}
    	/* foreach ($defalut as $k=>$v){
    		if(!array_key_exists($v['name'],$defalut)){
    			unset($opt[$k]);
    		}
    	} */
    	foreach ($opt as $k=>$v){
    		if(!array_key_exists($v['name'],$defalut)){
    			unset($opt[$k]);
    		}
    	}
    	$this->assign('opt',$opt);
    }
    
    /**
     * 重置密码扣除费用
     * @param unknown $uid
     * @return boolean
     */
    protected function decMoneyForResetPwd($uid){
    	$money = C('RESET_PWD');
    	if($money<=0){
    		return true;
    	}
    	
    	$relnumber = C('PWD_UNUMBER');
    	$relid = self::$Member->where(array('usernumber'=>$relnumber))->getField('uid');//关联用户
    	if(!$relid){
    		$relation = get_com();
    		$res = self::$Member->where(array('uid'=>$uid))->setDec('hasmoney',$money);
    		if($res){
    			$taruserinfo = self::$Member->where(array('uid'=>$uid))->find();
    			$type_arr = array('recordtype'=>0,'moneytype'=>2,'changetype'=>29);
    			$money_arr = array('money'=>$money,'hasmoney'=>$taruserinfo['hasmoney']);
    			money_change($type_arr, $relation,$taruserinfo, $money_arr);
    			return true;
    		}
    	}else{
    		$res = self::$Member->where(array('uid'=>$relid))->setInc('hasmoney',$money);
    		$res = self::$Member->where(array('uid'=>$uid))->setDec('hasmoney',$money);
    		if($res){
    			$relation = self::$Member->where(array('uid'=>$relid))->find();
    			$taruserinfo = self::$Member->where(array('uid'=>$uid))->find();
    			
    			$op = md5($uid.$relid.time());
    			$type_arr = array('recordtype'=>0,'moneytype'=>2,'changetype'=>29);
    			$money_arr = array('money'=>$money,'hasmoney'=>$taruserinfo['hasmoney']);
    			money_change($type_arr, $relation,$taruserinfo, $money_arr,$op);
    			unset($type_arr,$money_arr);
    			
    			$type_arr = array('recordtype'=>1,'moneytype'=>2,'changetype'=>29);
    			$money_arr = array('money'=>$money,'hasmoney'=>$relation['hasmoney']);
    			money_change($type_arr,$taruserinfo, $relation, $money_arr,$op);
    			return true;
    		}
    	}
    	
    	return false;
    	
    }

    /**
    **/
    /*
  * 利率折线图*/
    function zxt($strtime,$endtime){

        $e['createtime']=array(array('egt',$endtime),array('elt',$strtime));     //时间段
        //dump($e);

        $jl=D('jiaoyi')->where($e)->select();


        $aa=array();
        $bb=array();
        $cc=array();
        $dd=array();
        foreach ($jl as $o){
            if ($o['createtime']<=$strtime&&$o['createtime']>=$strtime-86400){
                $aa[0]+=$o['danjia'];
                if($o['mm']==1){
                    $bb[0]+=$o['danjia'];
                    $dd[0]++;
                }
                $cc[0]++;


            }elseif($o['createtime']<=$strtime-86400&&$o['createtime']>=$strtime-172800){
                $aa[1]+=$o['danjia'];
                if($o['mm']==1) {
                    $bb[1]+= $o['danjia'];
                    $dd[1]++;
                }
                $cc[1]++;

            }elseif($o['createtime']<=$strtime-172800&&$o['createtime']>=$strtime-259200){
                $aa[2]+=$o['danjia'];
                if($o['mm']==1) {
                    $bb[2]+= $o['danjia'];
                    $dd[2]++;
                }
                $cc[2]++;
            }elseif($o['createtime']<=$strtime-259200&&$o['createtime']>=$strtime-345600){
                $aa[3]+=$o['danjia'];
                if($o['mm']==1) {
                    $bb[3]+= $o['danjia'];
                    $dd[3]++;
                }
                $cc[3]++;
            }elseif ($o['createtime']<=$strtime-345600&&$o['createtime']>=$strtime-432000){
                $aa[4]+=$o['danjia'];
                if($o['mm']==1) {
                    $bb[4]+= $o['danjia'];
                    $dd[4]++;
                }
                $cc[4]++;
            }elseif ($o['createtime']<=$strtime-432000&&$o['createtime']>=$strtime-518400){
                $aa[5]+=$o['danjia'];
                if($o['mm']==1) {
                    $bb[5]+= $o['danjia'];
                    $dd[5]++;
                }
                $cc[5]++;
            }else{
                $aa[6]+=$o['danjia'];
                if($o['mm']==1) {
                    $bb[6]+= $o['danjia'];
                    $dd[6]++;
                }
                $cc[6]++;
            }
        }
        $aa[0]=round($aa[0]/$cc[0],2);
        $aa[1]=round($aa[1]/$cc[1],2);
        $aa[2]=round($aa[2]/$cc[2],2);
        $aa[3]=round($aa[3]/$cc[3],2);
        $aa[4]=round($aa[4]/$cc[4],2);
        $aa[5]=round($aa[5]/$cc[5],2);
        $aa[6]=round($aa[6]/$cc[6],2);

        $bb[0]=round($bb[0]/$dd[0],2);
        $bb[1]=round($bb[1]/$dd[1],2);
        $bb[2]=round($bb[2]/$dd[2],2);
        $bb[3]=round($bb[3]/$dd[3],2);
        $bb[4]=round($bb[4]/$dd[4],2);
        $bb[5]=round($bb[5]/$dd[5],2);
        $bb[6]=round($bb[6]/$dd[6],2);


      /*  $cc[0]=date("m/d",$strtime-86400);
        $cc[1]=date("m/d",$strtime-172800);
        $cc[2]=date("m/d",$strtime-259200);
        $cc[3]=date("m/d",$strtime-345600);
        $cc[4]=date("m/d",$strtime-432000);
        $cc[5]=date("m/d",$strtime-518400);
        $cc[6]=date("m/d",$strtime-604800);*/
        if ($aa[0]==''){
            $aa[0]=0.00;
        }if ($aa[1]==''){
            $aa[1]=0.00;
        }if ($aa[2]==''){
            $aa[2]=0.00;
        }if ($aa[3]==''){
            $aa[3]=0.00;
        }if ($aa[4]==''){
            $aa[4]=0.00;
        }if ($aa[5]==''){
            $aa[5]=0.00;
        }if ($aa[6]==''){
            $aa[6]=0.00;
        }

        if ($bb[0]==''){
            $bb[0]=0.00;
        }if ($bb[1]==''){
            $bb[1]=0.00;
        }if ($bb[2]==''){
            $bb[2]=0.00;
        }if ($bb[3]==''){
            $bb[3]=0.00;
        }if ($bb[4]==''){
            $bb[4]=0.00;
        }if ($bb[5]==''){
            $bb[5]=0.00;
        }if ($bb[6]==''){
            $bb[6]=0.00;
        }
      //  dump($aa);dump($bb);;die;


        $sdk="[$aa[6],$aa[5],$aa[4],$aa[3],$aa[2],$aa[1],$aa[0]]";
        $ost="[$bb[6],$bb[5],$bb[4],$bb[3],$bb[2],$bb[1],$bb[0]]";
        //$sot="["."'".$cc[6]."'".','."'".$cc[5]."'".','."'".$cc[4]."'".','."'".$cc[3]."'".','."'".$cc[2]."'".','."'".$cc[1]."'".','."'".$cc[0]."'"."]";
        $all=array($sdk,$ost,$sot);
        return $all;
    }
   
}
