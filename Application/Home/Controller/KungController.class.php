<?php
namespace Home\Controller;

use User\Api\UserApi;
use Common\Api\BonusApi;
use Common\Api\RelationApi;
use Common\Api\ChangeApi;
/**
 * 用户控制器
 */
class KungController extends HomeController{
	
	
	public function _initialize(){
		parent::_initialize();
      
	}
	public function index(){
	    $category = M('category');
      	$document = M('document');
    	$map['name'] = 'instroduce';
    	$map['status'] = 1;
    	$id = $category->where($map)->getField('id');
    	$s2=$category->where(array('status'=>1,'pid'=>$id))->select();
    	$ids2="";
    	foreach($s2 as $v){
    		$ids2.=$v['id'].',';
    	}
    	unset($map);
      	//日线和分线
    	$rixian = explode(',',get_bonus_rule('rixian'));
    	$fenxian = explode(',',get_bonus_rule('fenxian'));
    	$arr['rixian'] = json_encode($rixian);
    	$arr['fenxian'] = json_encode($fenxian);
        //汇率
        $huilv = get_bonus_rule('huilv');
        $arr['eve_price'] = rtrim(get_bonus_rule('eve_price'),'0');
        $arr['rmb'] = $arr['eve_price']*$huilv;
    	$map['category_id']=49;
    	$map['status']=1;
    	
    	$instro = $document->field('id,title,update_time')->where($map)->limit(5)->select();
        unset($map);
    	//公告标题
    	
    	$map['category_id']=46;
    	$map['status']=1;
      	
    	$gonggao = $document->field('id,title,update_time')->where($map)->order('create_time desc')->limit(5)->select();
    	//dump($instro);dump($gonggao);die;
      	//轮播图
      	
        $lunbo = M('slide')->where(array('status'=>1))->limit(3)->select();
      	
      	$this->assign('lunbo',$lunbo);
        $this->assign('gonggao',$gonggao);
    	$this->assign('arr',$arr);
      	$this->assign('instro',$instro);
      	 $this->display();
	}
	//公告详情
	public function gg_detail(){
	     $id = I('id');
	     $title = I('title');
	     $time = I('time');
	     $document = M('document_article')->where(array('id'=>$id))->getField('content');
	    
	     $this->assign('document',$document);
	     $this->assign('title',$title);
	     $this->assign('time',$time);
	     $this->display();
	}
	//更多新闻
	public function newlist(){
	    $category = M('category');
	    $document = M('document');
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
	    
	    $instro = $document->field('id,title,update_time')->where($map)->select();
	    
	    $this->assign('instro',$instro);
	    $this->display();
	}
	//新闻详情
	public function new_xiangqing(){
	    $id = I('id');
	    $title = I('title');
	    $time = I('time');


	    $document = M('document_article')->where(array('id'=>$id))->getField('content');
        $document1 = M('document')->where(array('id'=>$id))->find();
	    
	    $this->assign('document',$document);
	    $this->assign('title',$document1['title']);
	    $this->assign('time',$document1["create_time"]);

	    $this->display();
	}
    public function shouyi(){
        $uid=is_login();
        
        $Model=M('kuangji');
        $where1['userid'] = $uid;
        $where1['zx_kuangji.sstatus'] = 1;
        $list1 = $Model->join('zx_shop ON zx_kuangji.kuangjiid = zx_shop.id')->where($where1)->select();
        
        $where2['userid'] = $uid;
        $where2['zx_kuangji.sstatus'] = 3;
        $list2 = $Model->join('zx_shop ON zx_kuangji.kuangjiid = zx_shop.id')->where($where2)->select();

       
       
       
        $this->assign('list1',$list1);
        $this->assign('list2',$list2);
        $this->display();
    }
    //获取收益
    public function huoqu(){
        $id = I('id');
        $kuangji= M('kuangji')->where(array('iid'=>$id))->find();
        //必须间隔24小时领取
      /*  $jiange = 24*60*60;
        $nowtime = time();
        $cha = $nowtime-$kuangji['stoptime'];
        if($cha<$jiange && $kuangji['sstatus']==1){
            $this->error('必须间隔24小时才能获取收益');
        }*/
        //用户信息和矿机信息
        $where['uid'] = $kuangji['userid'];
        $where['status'] = 1;
        $uinfo = self::$Member->where($where)->find();
        $shop = M('shop')->where(array('id'=>$kuangji['kuangjiid']))->find();
        if($kuangji['cchanliang']<=0){
           $this->error('暂无产量可收益');
        }
        //给账号加钱
        $data['hasmoney'] = $uinfo['hasmoney']+$kuangji['cchanliang'];
        $data['allmoney'] = $uinfo['allmoney']+$kuangji['cchanliang'];
        $result = self::$Member->where(array('uid'=>$uinfo['uid']))->save($data);
        //改变该矿机状态
        $data=array();
        $data['cchanliang'] = 0;
        $data['stoptime'] = time();
        $data['ttime'] = 0;
     $result1 = M('kuangji')->where(array("iid"=>$id))->save($data);
        
        $fromuser['uid'] = $kuangji['iid'];
        $fromuser['usernumber'] = $shop['goods_name'];
       
     
      
        if($result && $result1){
            $bonus = new BonusApi();
            $bonus->dongtai($uinfo,$kuangji['cchanliang']);
	
            moneyChange(1,2,$uinfo,$fromuser,$kuangji['cchanliang'],$data['hasmoney'],0,2);
            $this->success('收益成功');
        }else{
            $this->error('收益失败');
        }
       
        
        
        
    }
    //交易中心
    public function center(){
      



        $uid=is_login();
        $uinfo = M('member')->where(array('uid'=>$uid,'status'=>1))->find();
        $bonusRule=get_bonus_rule();
        //资料控制
      	if(empty($uinfo['mobile']) || empty($uinfo['realname'])){
        	$this->error('请先完成您的资料才可进入交易大厅');
        }


        /* 每日折线图数据*/
        $strtime=strtotime("today");        //今日凌晨
        $endtime=strtotime("today")-604800; //7天前
        $a=$this->zxt($strtime,$endtime,$uinfo);

        //$a  1.红色  2.绿色 3.时间

        if ($bonusRule["rixian"]!=""){
            $di= explode(",",$bonusRule["rixian"]);

            $a[0]="[$di[0],$di[1],$di[2],$di[3],$di[4],$di[5],$di[6]]";
        }
        if ($bonusRule["fenxian"]!=""){
            $di= explode(",",$bonusRule["fenxian"]);
            $a[1]="[$di[0],$di[1],$di[2],$di[3],$di[4],$di[5],$di[6]]";
        }

        // dump($a);die;
        $mairu=$a[0];
        $maichu=$a[1];



        //  dump($tm);die;

        $this->assign('mr',$mairu);
        $this->assign('mc',$maichu);
        /* end (折线图)*/






        //时间控制
      	$start = get_bonus_rule('start');
      	$end = get_bonus_rule('end');
        $hour = date('G',time());
      	if($hour<$start ||  $hour>=$end){
        	$this->error('交易已经关闭');
        }
        
       $secondpassword=is_sndpsd();
       $arr['fu'] = get_bonus_rule('fu');
       $shuliang = get_bonus_rule('liang');
       $arr['low'] = get_bonus_rule('low');
       $arr['hight'] = get_bonus_rule('hight');
       $arr['renshu'] = get_bonus_rule('renshu');
       $arr['eve_price'] = rtrim(get_bonus_rule('eve_price'),'0');

       
        if(!$secondpassword){
    		$this->assign('url',U('Kung/center'));
    		$this->secondpassword();
    	}else{
             $huilv = get_bonus_rule('huilv');
             $arr1['eve_price'] = rtrim(get_bonus_rule('eve_price'),'0');
             $arr1['rmb'] = $arr['eve_price']*$huilv;
            $liang = M('jiaoyi')->where(array('status'=>5))->sum('shuiliang');
           	if($shuliang){
            	$arr['liang']=(int)$shuliang;
            }else{
            	$arr['liang']=(int)$liang;
            }
            if ($bonusRule["mairu"]==0){
                //买入
                $where=array();
                $where['status'] =1;
                $where['youid'] = array('gt',0);
                $where['youname'] = array('neq','');
                $where['createtime']=array(array('egt',strtotime(date('Y-m-d')),array('elt',strtotime('+1 day'))));     //时间段
                $mairu = M('jiaoyi')->where($where)->avg("danjia");
                if ($mairu==""){

                    $mairu=0;
                }
                unset($where);
            }else{
                $mairu= $bonusRule["mairu"];
            }

            if ($bonusRule["maichu"]==0) {
                //卖出
                $where = array();
                $where['status'] = 1;
                $where['myid'] = array('gt', 0);
                $where['myname'] = array('neq', '');
                $where['createtime'] = array(array('egt', strtotime(date('Y-m-d')), array('elt', strtotime('+1 day'))));     //时间段
                $maichu = M('jiaoyi')->where($where)->avg("danjia");
                if ($maichu == "") {

                    $maichu = 0;
                }
            }else{
                $maichu= $bonusRule["maichu"];
            }
            if ($bonusRule["cjl"]==0) {
                //成交量
                $where = array();
                $where['status'] = 5;
                $where['myid'] = array('gt', 0);
                $where['myname'] = array('neq', '');
                $where['createtime'] = array(array('egt', strtotime(date('Y-m-d')), array('elt', strtotime('+1 day'))));     //时间段
                $cjl = M('jiaoyi')->where($where)->sum("shuiliang");
                if ($cjl == "") {

                    $cjl = 0;
                }
            }else{
                $cjl= $bonusRule["cjl"];

            }

            if ($bonusRule["cje"]==0) {
                //成交额
                $where = array();
                $where['status'] = 5;
                $where['myid'] = array('gt', 0);
                $where['myname'] = array('neq', '');
                $where['createtime'] = array(array('egt', strtotime(date('Y-m-d')), array('elt', strtotime('+1 day'))));     //时间段
                $cje = M('jiaoyi')->where($where)->avg("price");
                if ($cje == "") {

                    $cje = 0;
                }
            }else{
                $cje= $bonusRule["cje"];

            }

          	$this->assign('arr',$arr);
          	$this->assign('arr1',$arr1);
            $this->assign('mairu',$mairu);
            $this->assign('maichu',$maichu);
            $this->assign('uid',$uid);
            $this->assign('cjl',$cjl);
            $this->assign('cje',$cje);
            $this->display(); 
        }
      
         
    }
    public function mairu(){
        $uid=is_login();
        if(IS_POST){
        $m = new \Think\Model();
      	
        $uinfo = self::$Member->where(array('uid'=>$uid,'status'=>1))->find();
            $psd=trim(I("password"));
      	if ($uinfo["psd2"]!=$psd){
      	    $this->error("支付密码错误!");
        }
		$bonusRule=get_bonus_rule("jy_money");
        $rate = get_bonus_rule('byj');//买入的保证金
        $shuliang = trim(I('shuliang',0));
        $danjia = trim(I('danjia',0));
        $hight = get_bonus_rule('hight');
        $low = get_bonus_rule('low');
        
           $num=$shuliang*$danjia;
          	if((int)$shuliang!=$shuliang || $shuliang<100){
            	$this->error('提交数量必须是大于100的整数');
            }
            $qy = get_bonus_rule('qy');
           
          	if( $shuliang%$qy !=0){
            	$this->error('提交数量必须是大于100的整倍数');
            }
   
          	if($danjia<$low){
            	$this->error('提交单价不能低于今日最低币价');
            }
			if($num<$bonusRule){
            	$this->error('提交总金额不能低于'.$bonusRule);
            }
            if(empty($shuliang) || $shuliang<0){
                $this->error('提交数量不能为空');
            }
            if(empty($danjia) || $danjia<0){
                $this->error('提交价格不能为空');
            }

            //判断保证金是否足够
            if( $rate <= $uinfo['hasmoney']){
                $kouchu = self::$Member->where(array('uid'=>$uid))->setDec('hasmoney',$rate);
            }else{
                $this->error('保证金不足，无法买入');
            }

        	$price = $shuliang*$danjia;
            $data['youid'] = $uid;
            $data['youname'] = $uinfo['usernumber'];
            $data['shuiliang'] = $shuliang;
            $data['danjia'] = $danjia;
            $data['price'] = $price;
          	$data['shouxu'] = $rate;	//手续费
            $data['createtime'] = time();//挂出时间
            $data['tag'] = $this->ordersn();
            //买入的保证金记录、手续费
            moneyChange(0,8,$uinfo,get_com(),$rate,$price,0,2);
            $result = M('jiaoyi')->add($data);
            if($result){
                $this->success('挂出成功',U('jiaoyi'));
            }
        }else{
            $BonusRule=get_bonus_rule();
            $danjia=$BonusRule["eve_price"];
            if ($BonusRule["mairu"]!=0){
                $pj_mr=$BonusRule["mairu"];
            }else{
                $strtime=strtotime("today");        //今日凌晨
                $endtime=strtotime("today")+60*60*24; //明日凌晨
                $mad["createtime"]=array(array("egt",$strtime),array("elt",$endtime));
                $pj_mr=D("jiaoyi")->where($mad)->avg("danjia");
            }
            if ($BonusRule["maichu"]!=0){
                $pj_mc=$BonusRule["maichu"];
            }else{
                $strtime=strtotime("today");        //今日凌晨
                $endtime=strtotime("today")+60*60*24; //明日凌晨
                $mad["createtime"]=array(array("egt",$strtime),array("elt",$endtime));
                $pj_mc=D("jiaoyi")->where($mad)->avg("danjia");
            }
			$this->assign("bonus",get_bonus_rule());
            $this->assign("sj",$danjia);
            $this->assign("pj_mr",$pj_mr);
            $this->assign("pj_mc",$pj_mc);
          
            $mr=D("jiaoyi")->where(array("myid"=>array("neq",$uid),"status"=>1,"youid"=>array("eq",0)))->select();
            $this->assign("mr",$mr);


            $this->display();
        }
        
       
    }
    public function maichu(){
        $uid=is_login();

        if (IS_POST){


            //只能挂出一笔订单
            $m = new \Think\Model();
          	$dingdan=D("jiaoyi")->where(array("myid"=>$uid,"status"=>array("in","2,3")))->find();

         
          if($dingdan){
          	$this->error("请先完成上一定单");
          }

            $uinfo = self::$Member->where(array('uid'=>$uid,'status'=>1))->find();

            $psd=trim(I("password"));
            if ($uinfo["psd2"]!=$psd){
                $this->error("支付密码错误!");
            }

			$zq=get_bonus_rule("zq");	//转账次数
          	$jt=get_bonus_rule("jt");//转账累计金额

          $strtime=strtotime(date('Y-m-d'));
          $endtime=strtotime(date('Y-m-d',strtotime('+1 day')));
          $mat["createtime"]=array(array("egt",$strtime),array("elt",$endtime));	//时间段
          $mat["myid"]=$uid;
          $count=D("jiaoyi")->where($mat)->count(); //次数
         
          $all=D("jiaoyi")->where($mat)->sum("price");  //查询

          if($count>=$zq){
          	$this->error("您已达到今日转账次数限制");
          }

           if($all>=$jt){
          	$this->error("您已达到今日转账金额限制");
          }
          
          
          
		

            if($uinfo['is_vip']==1){
                $rate = get_bonus_rule('jy_rate');//交易手续费
            }else{
                $rate = get_bonus_rule('jy_rate');//交易手续费
            }

            $shuliang = trim(I('shuliang',0));
            $danjia = trim(I('danjia',0));
            $outbl = $uinfo['hasmoney'] * get_bonus_rule('jy_rate');
            if($danjia > $outbl){
                $danjia = $outbl;
            }
           $uuu = get_bonus_rule();

          if($danjia>get_bonus_rule('tm')){
          	$this->error("单价超出限制金额");
          }
          
           if($danjia<get_bonus_rule('lm')){
          	$this->error("单价低于限制金额");
          }
          
          
          	$num=$shuliang*$danjia;
            $hight = get_bonus_rule('hight');
            $low = get_bonus_rule('low');
            //提交控制
                if($danjia<$low){
                    $this->error('提交单价不能低于今日最低币价');
                }
                $qy = get_bonus_rule('qy');
                if( $shuliang%$qy!==0){
                    $this->error('提交数量必须是大于100的整倍数');
                }
				if($num<$bonusRule){
                 $this->error('提交总金额不能低于'.$bonus);
                }
                if(empty($shuliang) || $shuliang<0){
                    $this->error('提交数量不能为空');
                }
                if(empty($danjia) || $danjia<0){
                    $this->error('提交价格不能为空');
                }

                $price1 = $shuliang*$danjia;

            //判断eve币余额是否满足
            $jy_rate = $rate*0.01;
            $cha = 1-$jy_rate;
            /*if($jy_rate<0){
                $shouxu = 0;
                $total = $shuliang;
            }else if($jy_rate>1){

            }else{*/
                //需要卖出的总币
              
                $shouxu = $shuliang*$jy_rate;
            	$total= $shuliang+$shouxu;
            //}
				
            if($uinfo['hasmoney']<$total){
                $this->error('对不起您的余额不足');
            }

            $kouchu = self::$Member->where(array('uid'=>$uid,'status'=>1))->setDec('hasmoney',$total);
            if($kouchu){
                moneyChange(0,9,$uinfo,get_com(),$total,$uinfo['hasmoney']-$total,0,2);
               	moneyChange(0,8,$uinfo,get_com(),$shouxu,$price1,0,2);
                $data['myid'] = $uid;
                $data['myname'] = $uinfo['usernumber'];
                $data['shouxu'] = $num*$rate*0.01;
                $data['shuiliang'] = $shuliang;
                $data['danjia'] = $danjia;
                $data['price'] = $price1;
                $data['createtime'] = time();//挂出时间
                $data['tag'] = $this->ordersn();//买
                $result = M('jiaoyi')->add($data);
                if($result){
                    $this->success('挂出成功',U('jiaoyi'));
                }

            }
        }else{
            $BonusRule=get_bonus_rule();
            $danjia=$BonusRule["eve_price"];
            if ($BonusRule["mairu"]!=0){
                $pj_mr=$BonusRule["mairu"];
            }else{
                $strtime=strtotime("today");        //今日凌晨
                $endtime=strtotime("today")+60*60*24; //明日凌晨
                $mad["createtime"]=array(array("egt",$strtime),array("elt",$endtime));
                $mad["mm"]=2;
                $pj_mr=D("jiaoyi")->where($mad)->avg("danjia");
            }
            if ($BonusRule["maichu"]!=0){
                $pj_mc=$BonusRule["maichu"];
            }else{
                $strtime=strtotime("today");        //今日凌晨
                $endtime=strtotime("today")+60*60*24; //明日凌晨
                $mad["createtime"]=array(array("egt",$strtime),array("elt",$endtime));
                $mad["mm"]=1;
                $pj_mc=D("jiaoyi")->where($mad)->avg("danjia");
            }
			$this->assign("bonus",get_bonus_rule());
            $this->assign("sj",$danjia);
            $this->assign("pj_mr",$pj_mr);
            $this->assign("pj_mc",$pj_mc);
          
           $mr=D("jiaoyi")->where(array("youid"=>array("neq",$uid),"status"=>1,"myid"=>array("eq",0)))->select();
            $this->assign("mr",$mr);
          
          
          
          
            $this->display();
        }
        
    }
    //匹配
    public function pipei(){
        if(IS_POST){
            $id=I('id',0);
            $mai = I('mai',0);
            $uid=is_login();
            $uinfo = self::$Member->where(array('uid'=>$uid,'status'=>1))->find();
            $where['id'] = $id;
            $where['status'] = 1;
            $result = M('jiaoyi')->where($where)->find();
            if(!$result){
                $this->error('订单不存在');
            }
            //如果是自己的不能交易
            if($result['myid']==$uid || $result['youid']==$uid){
                $this->error('不能交易自己的订单');
            }
            if($mai==1){//如果是买
                $data['youid'] = $uid;
                $data['youname'] = $uinfo['usernumber'];//买家姓名
                $data['time'] = time();//匹配时间
                $data['status'] = 2;//匹配中
                $result1 = M('jiaoyi')->where($where)->save($data);
                if($result1){
                    //短信通知
                  	
                    $this->duanxian($uid, $result['myid']);
                    $this->success('匹配成功');
                }else{
                    $this->error('匹配失败');
                }
            }elseif($mai==2){//如果是卖
                $fromuser['uid'] = $result['youid'];
                $fromuser['usernumber'] = $result['youname'];
                //判断eve币余额是否满足
              /*  if($uinfo['is_vip']==1){
                   $jy_rate = get_bonus_rule('vip_shouxu')*0.01;//交易手续费
                }else{*/
                    $jy_rate =get_bonus_rule('jy_rate')*0.01;//交易手续费
              /*  } 
                if($jy_rate<0){
                    $shouxu = 0;
                    $total = $result['shuiliang'];
                }else if($jy_rate>1){
                    $shouxu = 0;
                    $total = $result['shuiliang'];
                }else{
                    //需要卖出的总币
                    $total= $result['shuiliang'];
                    
                }*/
             $sx= $result['shuiliang']*$jy_rate;
              $total=$result['shuiliang']+$sx;
                if($uinfo['hasmoney']<$total){
                   $this->error('您的余额不足无法匹配');
                }
                //扣钱
             $kouchu = self::$Member->where(array('uid'=>$uid,'status'=>1))->setDec('hasmoney',$total);
            if($kouchu){
                //流水
                moneyChange(0,3,$uinfo,$fromuser,$total,$uinfo['hasmoney']-$result['shuiliang'],0,2);
                moneyChange(0,8,$uinfo,$fromuser,$sx,$result['shuiliang'],0,2);
                    $data=array();
                    $data['myid'] = $uid;
                    $data['myname'] = $uinfo['usernumber'];//买家姓名
                    $data['shouxu'] = $shouxu;//匹配时间
                    $data['time'] = time();//匹配时间
                    $data['status'] = 2;//匹配中
                    $result2 = M('jiaoyi')->where($where)->save($data);
                    if($result2){
                       //短信通知
                       $this->duanxian($result['youid'],$uid);
                       $this->success('卖出成功');
                    }else{
                       $this->error('卖出失败');
                    }
                }
            }
        }
    }
    //短信通知买家id  and  卖家id
    public function duanxian($id,$otherid){
      
        $myinfo = self::$Member->field('is_vip,mobile')->where(array('uid'=>$otherid))->find();
        $youinfo = self::$Member->field('is_vip,mobile')->where(array('uid'=>$id))->find();
        $bonusRule=D("bonusRule")->where(array("id"=>1))->find();
        $form=$bonusRule["gs_name"];     //公司名称
        $appid=$bonusRule["appid"];       //短信ID
      	
        if($myinfo['is_vip']==1){
            $msg=msg(0,'您的订单已经匹配成功',$form,$myinfo['mobile'],1,$appid);
            if ($msg==1){
                M('bonus_rule')->where(array('id'=>1))->setInc('duanxin',1);
            }
        }
        if($youinfo['is_vip']==1){
             $msg1=msg(0,'您的订单已经匹配成功',$form,$youinfo['mobile'],1,$appid);
             if ($msg1==1){
                 M('bonus_rule')->where(array('id'=>1))->setInc('duanxin',1);
             }
        }  
    }
    //匹配
    public function pipei1(){
      	
        $id=I('id');
        $uid=is_login();
        $uinfo = self::$Member->where(array('uid'=>$uid,'status'=>1))->find();
        if($id){
            $where['id'] = $id;
            $where['status'] = 1;
            $result = M('jiaoyi')->where($where)->find();
            if(!$result){
            	$this->error('订单不存在');
            }
            if($result['mm']==1){
                $data['youid'] = $uid;
                $data['youname'] = $uinfo['usernumber'];//买家姓名
                $data['time'] = time();//匹配时间
                $data['status'] = 2;//匹配中
                $result1 = M('jiaoyi')->where($where)->save($data);
                if($result1){
                  	 $msg['k']= '匹配成功';
           			 $msg['info']= '1';
           			 $this->ajaxReturn($msg);//启用失败
                   
                }else{
                  	 $msg['k']= '匹配失败';
           			 $msg['info']= '2';
           			 $this->ajaxReturn($msg);//启用失败
                   
                }
            }else if($result['mm']==2){
                $fromuser['uid'] = $result['youid'];
                $fromuser['usernumber'] = $result['youn'];
                //判断eve币余额是否满足
                $jy_rate = get_bonus_rule('jy_rate')*0.01;
               
               /* if($jy_rate<0){
                    $shouxu = 0;
                    $total = $result['shuiliang'];
                }else if($jy_rate>1){
                    
                }else{
                    //需要卖出的总币
                   $total= $result['shuiliang']+$result['shuiliang']*$jy_rate;
                    $shouxu = $result['shuiliang']*$jy_rate;
                }*/
                $total=$result['shuiliang']+$result["shouxu"];
                if($uinfo['hasmoney']<$total){
                  	 $msg['k']= '对不起您的余额不足,无法匹配';
           			 $msg['info']= '3';
           			 $this->ajaxReturn($msg);//启用失败
                    
                }
                //扣钱
                $kouchu = self::$Member->where(array('uid'=>$uid,'status'=>1))->setDec('hasmoney',$total);
                if($kouchu){
                 moneyChange(0,3,$uinfo,$fromuser,$result['shuiliang'],$uinfo['hasmoney']-$result['shuiliang'],0,2);
                moneyChange(0,8,$uinfo,$fromuser,$result["shouxu"],$price1,0,2);
                    /* $bonus = new BonusApi();
                    
                    $type=array('recordtype'=>0,'changetype'=>3,'moneytype'=>2);
                    $money=array('money'=>$total,'hasmoney'=>$uinfo['hasmoney']-$total,'taxmoney'=>0);
                     
                    money_change($type, $uinfo, $fromuser,  $money); */
                    $data=array();
                    $data['myid'] = $uid;
                    $data['myname'] = $uinfo['usernumber'];//买家姓名
                    $data['shouxu'] = $shouxu;//匹配时间
                    $data['time'] = time();//匹配时间
                    $data['status'] = 2;//匹配中
                    $result2 = M('jiaoyi')->where($where)->save($data);
                    if($result2){
                     $msg['k']= '匹配成功';
           			 $msg['info']= '3';
           			 $this->ajaxReturn($msg);//启用失败
                      
                    }else{
                     $msg['k']= '匹配失败';
           			 $msg['info']= '3';
           			 $this->ajaxReturn($msg);//启用失败
                       
                    }
                }
                
            }
        }
    }
  //买家未付款卖家取消交易
   public function qxjy(){
   	  $id = I('id');
      $uid = is_login();
     
      $data['youname']='';
      $data['youid'] = 0;
      $data['createtime'] = time();
      $data['time'] = '';
      $data['mm'] = 1;
      $data['status'] =1;
     
      
      $result = M('jiaoyi')->where(array('id'=>$id,'status'=>2))->save($data);
      if($result){
       $this->success('取消成功',U('user/index'));
      }else{
       $this->error('取消失败');
      }
   }
    //我的交易页面
    public function jiaoyi(){
        $m = new \Think\Model();
        $uid=is_login();
        //我求购的交易
        $res1 = M('jiaoyi')->where(array('youid'=>$uid,'status'=>1))->order('createtime desc')->select();
        //我出售的
        $res2 = M('jiaoyi')->where(array('myid'=>$uid,'status'=>1))->order('createtime desc')->select();
        //交易中的
        $sql3="select * from zx_jiaoyi where (status=2 or status=3) and (myid=$uid or youid=$uid)";
        $res3 = $m->query($sql3);
      
        if($res3[0]['status']==2){
            //最晚打款时间
            $time = $res3[0]['time']+get_bonus_rule('pp_time')*60*60;

        }else if($res3[0]['status']==3){
            //最晚收款时间
            $time = $res3[0]['dk_time']+get_bonus_rule('dk_time')*60*60;
        }else{
            $time=0;
        }
        $myinfo = self::$Member->field('usernumber,mobile,wechat,alipay,realname,banknumber,bankname')->where(array('uid'=>$res3[0]['myid']))->find();
        $youinfo = self::$Member->field('usernumber,mobile,wechat,alipay,realname,banknumber,bankname')->where(array('uid'=>$res3[0]['youid']))->find();
        
        //已完成的
        $sql4="select * from zx_jiaoyi where (status=4 or status=5 or status=6 or status=7) and (myid=$uid or youid=$uid) order by id desc";
        $res4 = $m->query($sql4);
        
        $this->assign('time',$time);
        $this->assign('myinfo',$myinfo);
        $this->assign('youinfo',$youinfo);
        $this->assign('uid',$uid);
        $this->assign('res1',$res1);
        $this->assign('res2',$res2);
        $this->assign('res3',$res3);
        $this->assign('res4',$res4);

      	$this->assign("bonus",get_bonus_rule());
        $this->display();
    }
  //交易详情
  public function sent(){
  	$date=I("");
    $type=$date["type"];
    $id=$date["id"];
    $uid=is_login();
 $m = new \Think\Model();
     $sql3="select * from zx_jiaoyi where id=$id";
        $res3 = $m->query($sql3);
  
        if($res3[0]['status']==2){
            //最晚打款时间
            $time = $res3[0]['time']+get_bonus_rule('pp_time')*60*60;
           
            
        }else if($res3[0]['status']==3){
            //最晚收款时间
            $time = $res3[0]['dk_time']+get_bonus_rule('dk_time')*60*60;
        }else{
            $time=0;
        }
        $myinfo = self::$Member->field('usernumber,mobile,wechat,alipay,realname,banknumber,bankname')->where(array('uid'=>$res3[0]['myid']))->find();
        $youinfo = self::$Member->field('usernumber,mobile,wechat,alipay,realname,banknumber,bankname')->where(array('uid'=>$res3[0]['youid']))->find();
    
    
     $this->assign('time',$time);
     $this->assign('res3',$res3[0]);
     $this->assign('myinfo',$myinfo);
     $this->assign('uid',$uid);
     $this->assign('youinfo',$youinfo);
		$this->assign("bonus",get_bonus_rule());
    $this->display();

  }
  
    //取消交易
    public function quxiao(){
        $id=I('id');
        $uid=is_login();
        $uinfo = self::$Member->where(array('uid'=>$uid,'status'=>1))->find();//该用户
        $where['id'] = $id;
        $where['status'] = 1;
        $result = M('jiaoyi')->where($where)->find();//改订单
        if($result){
            if($result['myid']==$uid){//如果是卖出
                $total = $result['shuiliang'];
                $res2 = self::$Member->where(array('uid'=>$uid,'status'=>1))->setInc('hasmoney',$total);
                if($res2){
                    //写流水
                    moneyChange(1,10,$uinfo,get_com(),$total,$uinfo['hasmoney']+$total,0,2);
                    $data['status']=4;
                    $data['youtime']=time();
                    $res3 = M('jiaoyi')->where($where)->save($data);
                    if($res3){
                        $this->success('取消成功');
                    }else{
                        $this->error('取消失败');
                    }
                }else{
                    $this->error('取消失败');
                }
            }else if($result['youid']==$uid){//如果是买入
                $data['status']=4;
                $data['youtime']=time();
                $res1 = M('jiaoyi')->where($where)->save($data);
                if($res1){
                   $this->success('取消成功');
                }else{
                   $this->error('取消失败');
                }
            }
        }
    }
    //交易中取消订单
    public function quxiao_dd(){
        $id = I('id');
        $dingdan = M('jiaoyi')->where(array('id'=>$id,'status'=>2))->find();
        $myinfo = self::$Member->where(array('uid'=>$dingdan['myid']))->find();
        $youinfo = self::$Member->where(array('uid'=>$dingdan['youid']))->find();
        if($dingdan){
            //退卖家的钱
            $total = $dingdan['shuiliang']+$dingdan['shouxu'];
            $res2 = self::$Member->where(array('uid'=>$dingdan['myid'],'status'=>1))->setInc('hasmoney',$total);
            if($res2){
                //写流水
                moneyChange(1,11,$myinfo,$youinfo,$total,$myinfo['hasmoney']+$total,0,2);
                //改订单状态
                $data['status'] = 7;
                $data['youtime'] = time();
                M('jiaoyi')->where(array('id'=>$id))->save($data);
                $this->success('取消成功');
            }
        }else{
            $this->error('订单不存在或');
        }
        
    }
    //取消交易
    public function  quxiao1(){
        $id=I('id');
        $uid=is_login();
        $uinfo = self::$Member->where(array('uid'=>$uid,'status'=>1))->find();//该用户
        $result = M('jiaoyi')->where($where)->find();//改订单
        
        if($id){
            $where['id'] = $id;
            $where['status'] = 1;
           
            if($result['mm']==1){//如果是卖出的需要把eve币返还到账号然后改状态
                $total = $result['shuiliang']+$result['shouxu'];
              
                $res2 = self::$Member->where(array('uid'=>$uid,'status'=>1))->setInc('hasmoney',$total);
             
                if($res2){
                    $data['status']=4;
                    $data['youtime']=time();
                    $res3 = M('jiaoyi')->where($where)->save($data);
                    if($res3){
                        echo 1;//取消成功
                    }else{
                        echo 2;//取消失败
                    }
                }
            }else if($result['mm']==2){//如果是收购的直接改状态
                $data['status']=4;
                $data['youtime']=time();
                $res1 = M('jiaoyi')->where($where)->save($data);
                if($res1){
                    echo 1;//取消成功
                }else{
                    echo 2;//取消失败
                }
            }
        }
    }
    //我的矿机
    public function  wodekj(){
        $uid=is_login();
        
        $where['userid'] = $uid;
        $where['sstatus'] = array('lt',3);
        //运行中矿机
        $yunxing = M('kuangji')->join('zx_shop ON zx_kuangji.kuangjiid = zx_shop.id')->where($where)->select();
        //报废的矿机
        $where['sstatus'] = 3;
        $baofei =  M('kuangji')->join('zx_shop ON zx_kuangji.kuangjiid = zx_shop.id')->where($where)->select();
        $this->assign('yunxing',$yunxing);
        $this->assign('baofei',$baofei);
        $this->display();
    }
    //运行中的矿机
    public function yunxingkj(){
            $iid = I("iid");
            $uid=is_login();
            //$map["zx_kuangji.sstatus"] = 1;
            $map["zx_kuangji.iid"] = $iid;
            $list = M('kuangji')->field('zx_kuangji.*,zx_shop.goods_ico')->join('zx_shop ON zx_kuangji.kuangjiid = zx_shop.id')->where($map)->find();
          
            $this->assign('goodlist',$list);
            $this->display(); 
    }
    //启用矿机
    public function qiyong(){
        $id=I('iid');
        $where['iid'] = $id;
        $data['sstatus'] = 1;
       $uid=is_login();
        $result = M('kuangji')->where($where)->save($data);
        if($result){
          D("member")->where(array("uid"=>$uid))->setInc("ytf",1);
            $this->success('启用成功');
        }else{
            $this->error('启用失败');
        }
        
    }
    //交易详情
    public function  xiangqing(){
        $uid=is_login();
        $id = I('id');
       
       
        $result = M('jiaoyi')->where(array('id'=>$id))->find();
        
        if($result['myid']==$uid){
            $info = self::$Member->where(array('uid'=>$result['youid']))->find();//买家信息
            $mai=1;//我是卖家
        }elseif($result['youid']==$uid){
            $info = self::$Member->where(array('uid'=>$result['myid']))->find();//卖家信息
            $mai=2;//我是买家
        }
        
        $this->assign('result',$result);
        $this->assign('info',$info);
        $this->assign('mai',$mai);
        $this->assign('id',$id);
        $this->display();
    }
    //买家提交
    public function mj_tj(){
        if(IS_POST){
            $id=I('id');
            $file = $_FILES;
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath  =      './Home/jyrz/'; // 设置附件上传目录
            // 上传文件
            $info   =   $upload->upload();
             
            if(!$info) {
                // 上传错误提示错误信息
                $this->error($upload->getError());
            }else{
                // 上传成功 获取上传文件信息
                foreach($info as $file){
                    
                }
                $idcard =  './Uploads/'.ltrim($file['savepath'].$file['savename'],'./');
            }
            $url = $idcard;
            $data['pinzheng'] = $url;
            $data['status'] = 3;
            $data['dk_time'] = time();

            $result = M('jiaoyi')->where(array('id'=>$id))->save($data);
            if($result){
                $this->success('交易成功,等待对方确认',U('user/index'));
            }else{
                $this->error('交易失败');
            }
            
        }
        
    }
    //卖家确认收款
    public function mj1_tj(){
        $id=I('id');
      	$bonus=get_bonus_rule();
        $result = M('jiaoyi')->where(array('id'=>$id,'status'=>3))->find();
        if(!$result){
            $this->error('订单不存在');
        }
        $uinfo = self::$Member->where(array('uid'=>$result['myid'],'status'=>1))->find();//卖家
        $fromuser = self::$Member->where(array('uid'=>$result['youid'],'status'=>1))->find();//买家 
        //改状态
        $data['status']=5;
        $data['youtime']=time();
      	$data["huilv"]=$bonus["huilv"];
        $res= M('jiaoyi')->where(array('id'=>$id,'status'=>3))->save($data);
        //写流水
        if($res){
           //把手续费加给公司
           $bonus = new BonusApi();
           $bonus->month($result['shouxu']);
           finance(8, $result['shouxu']); //公司财务统计
           //把钱币加给买家
          $bonus=get_bonus_rule("jy_rate");
          $num=$result['shuiliang'];
            //给购买的数量加上奖励
            $num = $num + $num * get_bonus_rule('buybl')*0.01;
//          $sxf=$result['shuiliang']*$bonus*0.01;
           $result1 = self::$Member->where(array('uid'=>$result['youid'],'status'=>1))->setInc('hasmoney',$num);
           if($result1){
               $type1=array('recordtype'=>1,'changetype'=>4,'moneytype'=>2);
               $money1=array('money'=>$num,'hasmoney'=>$fromuser['hasmoney']+$num,'taxmoney'=>0);
               money_change($type1, $fromuser, $uinfo, $money1);
             
              /*$type1=array('recordtype'=>0,'changetype'=>8,'moneytype'=>2);
               $money1=array('money'=>$sxf,'hasmoney'=>$result['shuiliang'],'taxmoney'=>0);
               money_change($type1,  $uinfo,get_com(), $money1);*/
           }
                //删除交易凭证
                unlink($result['pinzheng']);
                $this->success('交易成功',U('jiaoyi'));
           }else{
                $this->error('交易失败',U('jiaoyi'));
           }          
    }
    //我的公会
    public function gonghui(){
        $uid=is_login();
        $uinfo = self::$Member->where(array('uid'=>$uid,'status'=>1))->find();
        //显示的都是自己的直推
        $where['tuijianid'] = $uid;
        $where['status'] = 1;
        $people = self::$Member->field('mobile,photo,usernumber,uid,team')->where($where)->select();
        foreach ($people as $k=>$v){
            $ids.=$v['uid'].',';
        }
        $where = array();
        $where['userid'] = array('in',$ids);
        $result = M('kuangji')->field('userid,count(iid)')->where($where)->group('userid')->select();
        foreach ($result as $k=>$v){
            $kuangji[$v['userid']] = $v['count(iid)'];
        }
       
        //显示自己的团队总算力
        $map['tuijianids'] = array('like','%,'.$uid.',%');
        $map['status'] = 1;
        $tuandui = self::$Member->field('uid')->where($map)->select();
        foreach ($tuandui as $k=>$v){
            $uids.=$v['uid'].',';
        }
        //查询
        $where = array();
        $where['userid'] = array('in',$uids);
        $total =  M('kuangji')->where($where)->sum('ssuanli');
        //我所属的团队
        $gonghui = M('gonghui')->where(array('id'=>$uinfo['gonghuiid']))->find();
       
        if($gonghui){
            $this->assign('gonghui',$gonghui);
        }else{
            $this->assign('gonghui','');
        }
        
        $this->assign('kuangji',$kuangji);
        $this->assign('total',$total);
        $this->assign('uinfo',$uinfo);
        $this->assign('people',$people);
        $this->display();
    }
    public function  gonghui1(){
        $model = M('gonghui');
        $uid=is_login();
        $uinfo = self::$Member->where(array('uid'=>$uid,'status'=>1))->find();
        if(empty($uinfo['is_gonghui'])){
          	if(empty($uinfo['gonghuiid'])){
            	//该会员网体下的人
                $where['tuijianid'] = $uid;
                $where['status'] = 1;
                $people = self::$Member->where($where)->select();
            }else{
              	$where['tuijianid'] = $uid;
                $where['status'] = 1;
                $people = self::$Member->where($where)->select();
            	$gonghui = $model->where(array('id'=>$uinfo['gonghuiid']))->find();
            }
        }else{
            $gonghui = $model->where(array('id'=>$uinfo['gonghuiid']))->find();
            if($uinfo['is_gonghui']!=1){
                $uinfo = self::$Member->where(array('uid'=>$gonghui['userid']))->find();
            }
          	$where['tuijianid'] = $uid;
            $where['status'] = 1;
          	$people = self::$Member->where($where)->select();
            $map['tuijianids'] = array('like','%,'.$uid.',%');
            $peoplee = self::$Member->field('uid')->where($map)->select();
            foreach($peoplee as $k=>$v){
            	$str.=$v['uid'].',';
            }
            $where=array();
            $where['userid'] = array('in',$str);
            $total =  M('kuangji')->where($where)->sum('ssuanli');
         
        }
      	    //所有人的算力
            $suanli = M('kuangji')->field("userid,sum(ssuanli)")->where(array('sstatus'=>1))->group('userid')->order('userid')->select();
            foreach($people as $k=>$v){
                foreach($suanli as $key=>$val){
                    if($v['uid']==$val['userid']){
                        $people[$k]['ttsuanli'] = $val['sum(ssuanli)'];
                       
                    }
                }
            }
      	 	
    
      	
      	$this->assign('total',$total);
      	$this->assign('uinfo',$uinfo);
        $this->assign('gonghui',$gonghui);
        $this->assign('people',$people);
        $this->display();
    }
    public function create_gh(){
        $uid=is_login();
        $uinfo = self::$Member->where(array('uid'=>$uid))->find();
        if(IS_POST){
          
            if($uinfo['userrank']<2 && $uinfo['is_gonghui']==0){
                $this->error('您的级别不够无法创建公会');
            }
            $iid = I('id',0);
            $gh_xuanyan = I('gh_xuanyan','');
            $gh_qq = I('gh_qq',0);
            $hz_phone = I('hz_phone',0);
            if(empty($gh_xuanyan)){
                $this->error('请输入公会宣言');
            }
            if(empty($gh_qq)){
                $this->error('请输入公会qq群');
            }
            
            $data['userid'] = $uid;
            $data['gh_xuanyan'] = $gh_xuanyan;
            $data['gh_qq'] = $gh_qq;
            $data['hz_phone'] = $hz_phone;
            $data['gh_name'] = $uinfo['usernumber'];
            if($iid>0){//修改
                if(M('gonghui')->where(array('id'=>$iid))->save($data)){
                    $this->success('修改成功');
                }else{
                    $this->error('修改资料失败');
                }
            }else{//创建
                $id = M('gonghui')->add($data);
            }
            
            if($id){
                $data1['gonghuiid'] = $id;
                $data1['is_gonghui']=1;
                $result1 = self::$Member->where(array('uid'=>$uid,'status'=>1))->save($data1);
                //先更改网体下的公会id
                $where=array();
                $where['tuijianids'] = array('like','%,'.$uid.',%');
                $where['status'] = 1;
                $where['is_gonghui']=0;
                $result2 = self::$Member->where($where)->setField('gonghuiid',$id);
                //查找他的网体下是否已经有人创建了公会
                $where['tuijianids'] = array('like','%,'.$uid.',%');
                $where['is_gonghui'] = 1;
                $wangti = self::$Member->field('uid,gonghuiid')->where($where)->order('uid')->select();
                if(!empty($wangti)){
                    //再更改 创建了公会的人的网体id
                    foreach($wangti as $k=>$v){
                        $where=array();
                        $where['tuijianids'] = array('like','%,'.$v['uid'].',%');
                        $where['status'] = 1;
                        $where['is_gonghui']=0;
                        $result3 = self::$Member->where($where)->setField('gonghuiid',$v['gonghuiid']);
                    }
                    if($result3){
                        $this->success('创建成功');
                    }else{
                        $this->error('创建失败');
                    }
                }else{
                    if($result2){
                        $this->success('创建成功');
                    }else{
                        $this->error('创建失败');
                    }
                }
            }
        }else{
            $id = I('id',0);
            $gonghui = M('gonghui')->where(array('id'=>$id))->find();
            if($gonghui){
                $this->assign('gonghui',$gonghui);
                $this->assign('isgh',1);
                $this->assign('title','修改资料');
            }else{
                $this->assign('isgh',0);
                $this->assign('title','创建公会');
            }
            
            $this->display();
        }
       
//         if($uinfo['userrank']>=2 && $uinfo['is_gonghui']==0){
            
//         }else{
//             $this->error('您的级别不够无法创建公会');
//         }
    }
    public function chuangjian(){
        $uid=is_login();
        $model = M('gonghui');
        if(IS_POST){
            $_POST['userid'] = $uid;
            $id = $model->add($_POST);
            if($id){
                $data['gonghuiid'] = $id;
                $data['is_gonghui']=1;
                $result1 = self::$Member->where(array('uid'=>$uid,'status'=>1))->save($data);
                //查找他的网体下是否已经有人创建了公会
                $where['tuijianids'] = array('like','%,'.$uid.',%');
                $where['is_gonghui'] = 1;
        
                $wangti = self::$Member->field('uid,gonghuiid')->where($where)->select();
        
                if(!empty($wangti)){
                    //先更改网体下的公会id
                    $where=array();
                    $where['tuijianids'] = array('like','%,'.$uid.',%');
                    $where['status'] = 1;
                    $where['is_gonghui']=0;
        
                    $result2 = self::$Member->where($where)->setField('gonghuiid',$id);
        
                    //再更改 创建了公会的人的网体id
                    foreach($wangti as $k=>$v){
                        $where=array();
                        $where['tuijianids'] = array('like','%,'.$v['uid'].',%');
                        $where['status'] = 1;
                        $where['is_gonghui']=0;
                        $result3 = self::$Member->where($where)->setField('gonghuiid',$v['gonghuiid']);
        
                    }
                    if($result1 && $result2 && $result3){
                        $this->success('创建成功',U('user/index'));
                    }else{
                        $this->error('创建失败1');
                    }
                }else{
                    //直接更改网体下的公会id
                    $where=array();
                    $where['tuijianids'] = array('like','%,'.$uid.',%');
                    $where['status'] = 1;
                    $where['is_gonghui']=0;
                    $result2 = self::$Member->where($where)->setField('gonghuiid',$id);
                    if($result1 && $result2){
                        $this->success('创建成功',U('user/index'));
                    }else{
                        $this->error('创建失败2');
                    }
                }
        
            }else{
                $this->error('创建失败1');
            }
             
        }
    }
    //修改公会信息
    public function xiugai(){
        $id= I('id');
        $data['gh_name']= I('gh_name');
        $data['gh_xuanyan']= I('gh_xuanyan');
        $data['gh_qq']= I('gh_qq');
        $data['gh_wx']= I('gh_wx');
        $data['hz_qq']= I('hz_qq');
        $data['hz_phone']= I('hz_phone');
        
        $result = M('gonghui')->where(array('id'=>$id))->save($data);
        $this->success('修改成功');
    }
    //公会收益
    public function gh_shouyi(){
        $uid=is_login();
        $where['targetuserid'] = $uid;
        $where['changetype'] = 6;
      	$result = M('money_change')->where($where)->select();
        
        $this->assign('result',$result);
        $this->display();
    }
   public function gh_shouyiiii(){
        $uid=is_login();
        $uinfo = self::$Member->where(array('uid'=>$uid,'status'=>1))->find();
      	//显示我的下三代
      	$where['tuijianids'] = array('like','%,'.$uid.',%');
      	$tdeep = $uinfo['tdeep']+3;
      	if($tdeep<0){
        	$tdeep=0;
        }else{
        	$tdeep = $tdeep;
        }
      	$where['tdeep'] = array('elt',$tdeep);
      	$where['status']=1;
      	
      	$result = self::$Member->field('uid')->where($where)->select();
      	      foreach($result as $k=>$v){
                  $str.=$v['uid'].',';
              }
              $where['targetuserid'] = array('in',$str);
              $where['changetype'] =2;
              $result = M('money_change')->where($where)->select();
      	
        
        $this->assign('result',$result);
        $this->display();
    }
    //推广链接
    public function tuiguang(){
        $uid=is_login();
        $this->assign('id',$uid);
        $this->display();
       
    }
  //定向交易
    public function dxjy(){
        if(IS_POST){
         $qianbao = trim(I('qianbao'));
         if(empty($qianbao)){
         	$this->error('请输入钱包地址');
         }
         $result = self::$Member->where(array('qianbao'=>$qianbao))->find();
         if($result){
            $this->success('正在跳转...',U('dingxiang',array('uid'=>$result['uid'])));
         }else{
         	$this->error('钱包地址不存在');
         }
       }
    }
  	public function dingxiang(){
       $otherid = is_login();
       if(IS_POST){
          $uid = I('uid');
          $password =trim(I('password'));
          $shuliang = trim(I('shuliang'));
            if((int)$shuliang!=$shuliang || $shuliang<1){
            	$this->error('交易数量必须是大于1的整数');
            }
          //对方信息
          $uinfo = self::$Member->where(array('uid'=>$uid))->find();
         //我得信息
          $myinfo = self::$Member->where(array('uid'=>$otherid))->find();
          //判断二级密码
          if($password!=$myinfo['psd2']){
            $this->error('交易密码输入有误');
          }
          if($myinfo['is_vip']==1){
              $rate = get_bonus_rule('vip_shouxu')*0.01;//交易手续费
          }else{
              $rate = get_bonus_rule('jy_rate')*0.01;//交易手续费
          }
          $total = $shuliang+$shuliang*$rate;
          //判断自己的余额是否够
          
          if($myinfo['hasmoney']<$total){
             $this->error('您的余额不足');
          }
         	
         //扣自己的钱 然后把钱加到对方 然后写流水
         $data['hasmoney'] = $myinfo['hasmoney']-$total;
         $myresult = self::$Member->where(array('uid'=>$otherid))->save($data);
         //给对方加钱写流水
         $data1['hasmoney'] = $uinfo['hasmoney']+$shuliang;
         $result = self::$Member->where(array('uid'=>$uid))->save($data1);
         if($myresult && $result){
            //自己流水
            moneyChange(0,16,$myinfo,$uinfo,$total,$data['hasmoney'],0,2);
            //对方流水
            moneyChange(1,16,$uinfo,$myinfo,$shuliang,$data1['hasmoney'],0,2);
            $this->success('交易成功',U('center'));
         }else{
           $this->error('交易失败');
         }
        
       }else{
          $uid = I('uid');
          $uinfo = self::$Member->where(array('uid'=>$uid))->find();
          if($uinfo){
            $this->assign('uinfo',$uinfo);
            $this->display();
          }else{
          	$this->error('钱包地址不存在');
          }
       }
    }
    //手机号搜索
    public function search(){
        $mobile = I('mobile');
        $k = I('k');//1卖出2买入
        $where['status'] = 1;
        $where['shenpi'] = 3;
        $where['mobile'] = array('like','%'.$mobile.'%');
        $result = self::$Member->field('uid')->where($where)->select();
        foreach ($result as $key=>$v){
            $uids .=$v['uid'].','; 
        }
        $uids = rtrim($uids,',');
        $data=array();
        if(empty($uids)){
            $data['str'] = '';
            $data['k'] = $k;
            $this->ajaxReturn($data);
        }
        if($k==1){
            $map['myid']=array('in',$uids);
            
            $map['status'] = 1;
           
            $chu = M('jiaoyi')->where($map)->select();
            
            foreach($chu as $k=>$vo){
                $str .= '<li class="mui-table-view-cell mui-media"><img class="mui-media-object mui-pull-left" src="/Public/Home/a/imgs/new.jpg"><div class="mui-media-body">'.$vo["myname"].'<div class="num"><p>'.$vo['shuiliang'].'<span>个</span></p><p>'.$vo['danjia'].'$/AGL</p></div><p style="color:#30b73a">总计：<span>'.$vo['price'].'$</span></p><p class="but g1" onclick="sure1('.$vo['id'].')">买入TA</p></div></li>';
            }
            $data['str'] = $str;
            $data['k'] = 1;
            $this->ajaxReturn($data);
        }else if($k==2){
            $map['youid']=array('in',$uids);
            $map['status'] = 1;
            $ru = M('jiaoyi')->where($map)->select();
            
            foreach($ru as $k=>$vo){
                $str .= '<li class="mui-table-view-cell mui-media"><img class="mui-media-object mui-pull-left" src="/Public/Home/a/imgs/new.jpg"><div class="mui-media-body">'.$vo["youname"].'<div class="num"><p>'.$vo['shuiliang'].'<span>个</span></p><p>'.$vo['danjia'].'$/AGL</p></div><p style="color:#30b73a">总计：<span>'.$vo['price'].'$</span></p><p class="but" onclick="sure('.$vo['id'].')">卖给TA</p></div></li>';
            }
            $data['str'] = $str;
            $data['k'] = 2;
            $this->ajaxReturn($data);
        }
    }
    //订单号
   function ordersn(){
		$yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
		$orderSn = $yCode[intval(date('Y')) - 2015] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%04d%02d', rand(1000, 9999),rand(0,99));
		return $orderSn;
	}
    //我的团队
    public function team(){

        $uid=is_login();

        $userinfo=D("member")->where(array("uid"=>$uid))->find();

        //一代
        $one=D("member")->where(array("tuijianid"=>is_login(),"status"=>1))->select();

        //二代
   
     
        $two=D("member")->where(array("tuijianids"=>array("like","%,".$uid.",%"),"status"=>1,"tdeep"=>$userinfo["tdeep"]+2))->select();
     

        //三代
        $three=D("member")->where(array("tuijianids"=>array("like","%,".$uid.",%"),"status"=>1,"tdeep"=>$userinfo["tdeep"]+3))->select();


        $this->assign("one",$one);
        $this->assign("two",$two);
        $this->assign("three",$three);

        $this->display();
    }

    //转出
    public  function moneyout(){
	    if (IS_POST){
            $otherid = is_login();
            $uid = trim(I('usernumber'));
             // 判断当天有没有转账
             $starttime =strtotime(date('Y-m-d', time()));  
             $endtime = $starttime + 86399;
             $where['createtime'] = array('between',array($starttime,$endtime));
             $where['userid']=$otherid;
             $con = D('money_change')->where($where)->count();
             $zq = get_bonus_rule('zq');

             if($con >= $zq){
                $this->error('今日转账已达上限');
             }
          if(empty($uid)){
          	$this->error("账户不可为空");
          }
            $password =trim(I('pay'));
            $shuliang = trim(I('num'));
            if((int)$shuliang<100 || (int)$shuliang!=$shuliang){
                $this->error('交易数量必须是大于100的整数');
            }

           $qy = get_bonus_rule('qy');
                if( $shuliang%$qy!==0){
                    $this->error('提交数量必须是大于100的整倍数');
                }
           $myinfo = self::$Member->where(array('uid'=>$otherid))->find();
          if($uid==$myinfo["mobile"]){
            $this->error("不能给自己转账");
        }
         /* $yzm= trim(I('msg'["mobile"];
          
           
          $strtime=time();
          $endtime=$strtime-120;//5分钟有效期
          $ms["create_time"]=array(array("egt",$endtime),array("elt",$strtime));
          $ms["mobile"]=$myinfo["mobile"];
          $ms["msg"]=$yzm;
         //dump($ms);die;
          $jl=D("msg")->where($ms)->find();
        if (empty($jl)){
            $this->error("验证码错误");
        }*/
          
          
          
            //对方信息
            $uinfo = self::$Member->where(array('usernumber'=>$uid))->find();
          if(empty($uinfo)){
          	$this->error("没有该用户");
          }
            //我得信息
   
            //判断二级密码
            if($password!=$myinfo['psd2']){
                $this->error('交易密码输入有误');
            }
            if($myinfo['is_vip']==1){
                $rate = $shuliang*get_bonus_rule('hz')*0.01;//交易手续费
            }else{
                $rate = $shuliang*get_bonus_rule('hz')*0.01;//交易手续费
            }
            $total = $shuliang+$rate;
            //判断自己的余额是否够

            if($myinfo['hasmoney']<$total){
                $this->error('您的余额不足');
            }

            //扣自己的钱 然后把钱加到对方 然后写流水
            $data['hasmoney'] = $myinfo['hasmoney']-$shuliang-$rate;
            $myresult = self::$Member->where(array('uid'=>$otherid))->save($data);
            //给对方加钱写流水
            $data1['hasmoney'] = $uinfo['hasmoney']+$shuliang;
            $result = self::$Member->where(array('uid'=>$uinfo["uid"]))->save($data1);
            if($myresult && $result){
                //自己流水
                // moneyChange(0,16,$myinfo,$uinfo,$total,$data['hasmoney'],0,2);
                moneyChange(0,16,$uinfo,$myinfo,$total,$data['hasmoney'],0,2);
              if($rate>0){
                // moneyChange(0,8,$myinfo,$uinfo,$rate,$data['hasmoney'],0,2);
             	moneyChange(0,8,$uinfo,$myinfo,$rate,$data['hasmoney'],0,2);
              }
                //对方流水
               // moneyChange(1,16,$myinfo,$uinfo,$shuliang,$data1['hasmoney'],0,2);
               moneyChange(1,16,$uinfo,$myinfo,$shuliang,$data1['hasmoney'],0,2);
                $this->success('交易成功',U("bonus/financialflow"));
            }else{
                $this->error('交易失败');
            }

        }else{
          $this->display();
        }
	    

    }

    //转出记录
    public  function moneyout_set(){
	    $map["changtype"]=24;
	    $map["userid"]=is_login();
	    $map["moneytype"]=2;
      $map["recordtype"]=0;
	    $info=D("moneyChange")->where($map)->select();

	    $this->assign("info",$info);

	        $this->display();
    }

    //转入
    public function moneyin(){
	    if (IS_POST){
            $moneytypein=trim(I("moneytype"));
            $moneytypeout=trim(I("moneytypeout"));
            $num=trim(I("num"));
            $psd=trim(I("pay"));
			$bonusRule=get_bonus_rule();
          /*	if($bonusRule["fon"]==0&&$moneytypeout==5){
              $this->error("孵化仓转换暂时关闭");
            }*/
          

          $qy = get_bonus_rule('qy');
            if( $num%$qy !=0){
                $this->error('提交数量必须是大于100的整倍数');
            }
          
          
            $userinfo=D('member')->where(array("uid"=>is_login(),"status"=>1,"shenpi"=>3))->find();
			
          	if($userinfo["qb1"]==1&&$moneytypeout==1){
              $this->error("在线钱包转换暂时关闭");
            }
          
          
          	if($userinfo["qb3"]==1&&$moneytypeout==3){
              $this->error("动态钱包转换暂时关闭");
            }
          	if($userinfo["qb2"]==1&&$moneytypeout==4){
              $this->error("静态钱包转换暂时关闭");
            }
          if($userinfo["qb4"]==1&&$moneytypeout==4){
              $this->error("孵化钱包转换暂时关闭");
            }
          
          
          
            if ($moneytypein==$moneytypeout){
                $this->error("转入类型相同");
            }

            if($num<0||$num==""){
                $this->error("转入金额必须大于0且不为空");
            }
          
           if($psd!=$userinfo["psd2"]||$psd==""){
                $this->error("支付密码为空或错误");
            }
          
          $fee=$bonusRule["hzje"]*0.01*$num;	//扣手续费
         
          if($moneytypeout==1){
          	$fielout="hasbill";
            if($userinfo["hasbill"]<$num){
            	$this->error("在线钱包余额不足");
            }
            
            
          }elseif($moneytypeout==2){
          	$fielout="hasmoney";
              if($userinfo["hasmoney"]<$num){
            	$this->error("总钱包余额不足");
            }
          }elseif($moneytypeout==3){
          	$fielout="hascp";
          
      
                if($userinfo["hascp"]<$num){
            	$this->error("动态钱包余额不足");
            }
          }elseif($moneytypeout==4){
          	$fielout="hasjifen";
                if($userinfo["hasjifen"]<$num){
            	$this->error("静态钱包余额不足");
            }
          }elseif($moneytypeout==6){
          	$fielout="hassf";
                if($userinfo["hassf"]<$num){
            	$this->error("静态钱包余额不足");
            }
          }/*elseif($moneytypeout==5){
          	$fielout="hasfh";
                if($userinfo["hasfh"]<$num){
            	$this->error("孵化仓余额不足");
            }
          }*/
          
          if($moneytypein==1){
          	$fielin="hasbill";
          }elseif($moneytypein==2){
          	$fielin="hasmoney";
          }elseif($moneytypein==3){
          	$fielin="hascp";
          }elseif($moneytypein==4){
          	$fielin="hasjifen";
          }elseif($moneytypein==6){
          	$fielin="hassf";
          }/*elseif($moneytypein==5){
          	$fielin="hasfh";
          }*/
           
          
          if($moneytypeout==1&&$moneytypein==2){
        	$je=$num+$fee;
            if($userinfo[$fielout]<$je){
            	$this->error("钱包余额不足");
            }
            
            D('member')->where(array("uid"=>is_login(),"status"=>1,"shenpi"=>3))->setInc($fielin,$num);
            D('member')->where(array("uid"=>is_login(),"status"=>1,"shenpi"=>3))->setDec($fielout,$je);
            
      
                //自己流水
                moneyChange(0,24,$userinfo,$userinfo,$num,$userinfo[$fielout],0,2);
            	moneyChange(0,8,$userinfo,get_com(),$fee,$num,0,2);
                //对方流水
                moneyChange(1,24,$userinfo,$userinfo,$je,$data1[$fielin],0,2);
            
            	$this->success("转换成功!");
            
          }elseif($moneytypeout==4&&$moneytypein==2){
        	$je=$num+$fee;
            if($userinfo[$fielout]<$je){
            	$this->error("钱包余额不足");
            }
            
            D('member')->where(array("uid"=>is_login(),"status"=>1,"shenpi"=>3))->setInc($fielin,$num);
            D('member')->where(array("uid"=>is_login(),"status"=>1,"shenpi"=>3))->setDec($fielout,$je);
            
      
                //自己流水
                moneyChange(0,24,$userinfo,$userinfo,$num,$userinfo[$fielout],0,4);
            	moneyChange(0,8,$userinfo,get_com(),$fee,$num,0,4);
                //对方流水
                moneyChange(1,24,$userinfo,$userinfo,$je,$data1[$fielin],0,4);
            
            	$this->success("转换成功!");
            
          }/*elseif($moneytypeout==5&&$moneytypein==2){
        	$je=$num-$fee;
          /*  if($userinfo[$fielout]<$je){
            	$this->error("钱包余额不足");
            }*/
            
          /*  D('member')->where(array("uid"=>is_login(),"status"=>1,"shenpi"=>3))->setInc($fielin,$je);
            D('member')->where(array("uid"=>is_login(),"status"=>1,"shenpi"=>3))->setDec($fielout,$num);
            
      
                //自己流水
                moneyChange(0,24,$userinfo,$userinfo,$num,$userinfo[$fielout],0,5);
            	moneyChange(0,8,$userinfo,get_com(),$fee,$num,0,5);
                //对方流水
                moneyChange(1,24,$userinfo,$userinfo,$je,$data1[$fielin],0,5);
            
            	$this->success("转换成功!");
            
          }*/elseif($moneytypeout==3&&$moneytypein==2){
            $je=$num+$fee;
             if($userinfo[$fielout]<$je){
            	$this->error("钱包余额不足");
            }
            D('member')->where(array("uid"=>is_login(),"status"=>1,"shenpi"=>3))->setInc($fielin,$num);
            D('member')->where(array("uid"=>is_login(),"status"=>1,"shenpi"=>3))->setDec($fielout,$je);
            
      
                //自己流水
                moneyChange(0,24,$userinfo,$userinfo,$num,$userinfo[$fielout],0,3);
            	moneyChange(0,8,$userinfo,get_com(),$fee,$num,0,3);
                //对方流水
                moneyChange(1,24,$userinfo,$userinfo,$je,$data1[$fielin],0,3);
            
            	$this->success("转换成功!");
            
            
          }elseif($moneytypeout==6&&$moneytypein==2){
            $je=$num+$fee;
             if($userinfo[$fielout]<$je){
            	$this->error("钱包余额不足");
            }
            D('member')->where(array("uid"=>is_login(),"status"=>1,"shenpi"=>3))->setInc($fielin,$num);
            D('member')->where(array("uid"=>is_login(),"status"=>1,"shenpi"=>3))->setDec($fielout,$je);
            
      
                //自己流水
                moneyChange(0,24,$userinfo,$userinfo,$num,$userinfo[$fielout],0,6);
            	moneyChange(0,8,$userinfo,get_com(),$fee,$num,0,6);
                //对方流水
                moneyChange(1,24,$userinfo,$userinfo,$je,$data1[$fielin],0,6);
            
            	$this->success("转换成功!");
            
            
          }else{
         
            D('member')->where(array("uid"=>is_login(),"status"=>1,"shenpi"=>3))->setInc($fielin,$num);
            D('member')->where(array("uid"=>is_login(),"status"=>1,"shenpi"=>3))->setDec($fielout,$num);
                // 2018-8-31 add
              $bonus = new ChangeApi();
              $bonus->tuijianBonus($userinfo['uid'],$num);
                //自己流水
                moneyChange(0,24,$userinfo,$userinfo,$num,$data[$fielout],0,2);
                //对方流水
                moneyChange(1,24,$userinfo,$userinfo,$num,$data1[$fielin],0,2);
                $this->success("转换成功!");
            
          }
          
           
           
          
          






        }else{
          $bonus=get_bonus_rule();
          $this->assign("bonus",$bonus);
            $this->display();
        }

    }
    //转入记录
    public function moneyin_set(){
        $map["changtype"]=16;
        $map["targetuserid"]=is_login();
        $map["moneytype"]=2;
        $info=D("moneyChange")->where($map)->select();

        $this->assign("info",$info);
        $this->display();
    }
    //我的挂单
    public function guadan(){

	    $info=D("jiaoyi")->where(array("myid"=>is_login()))->select();
        $this->assign("info",$info);
	    $this->display();
    }
    public function  yanzheng(){
       $msgoff=get_bonus_rule("gs_name");
          
          if($msgoff==1){
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
                  $endtime=$strtime-60;//5分钟有效期
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
                $count =   M('bonus_rule')->where(array('id'=>1))->getField('duanxin');

                if($count>=10000){
                    $this->error('短信不足,请联系管理员');
                }else{
                    session('denglu',$denglu);
                    M('bonus_rule')->where(array('id'=>1))->setInc('duanxin',1);
                    msg(0,$denglu,$form,$mobile,0,$appid);
                    $this->success('发送成功,注意查收');
                }     
            }else{
          	$this->error("当前短信验证已关闭");
          }



	}
}