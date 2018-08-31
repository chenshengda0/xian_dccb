<?php
namespace Admin\Controller;

use Think\Model;

/**
 * 后台首页控制器
 */
class IndexController extends AdminController {

    /**
     * 后台首页
     */
    public function index(){
        $map['status'] = 0;
        $info['newuser'] = self::$Member->where(array('status'=>0))->count();//新增会员
        $info['userall'] = self::$Member->count();//总会员数
        $info['wd']	= M('Withdrawal')->where($map)->count();//未处理提现申请
        $info['order']	= M('ShopOrder')->where($map)->count();//新增订单
        $info['rg']	= M('Recharge')->where($map)->count();//新增订单
        
      	$info['sx']=D("moneyChange")->where(array("changetype"=>8))->sum("money");
      
        /*实时更新数据*/
        $Model = new Model();
        
        //统计
        $sql = "select sum(income) as income,sum(expend) as expend from zx_finance ";
        $alist = $Model->query($sql);
       	$list = $this->financeFilter($alist);
       	$this->assign('list',$list);
      
      	$more["chongzhi"]=D("moneyChange")->where(array("changetype"=>21))->sum("money");	//总充值
      	$more["jinchongzhi"]=D("moneyChange")->where(array("changetype"=>21,"createtime"=>array(array("elt",strtotime(date('Y-m-d',strtotime('+1 day')))),array("egt",strtotime(date('Y-m-d'))))))->sum("money");	//今日充值
      	$more["changbi"]=D("moneyChange")->where(array("changetype"=>array("in","7,6,5,1")))->sum("money");	//总产币
      	$more["jinchangbi"]=D("moneyChange")->where(array("changetype"=>array("in","7,6,5,1"),"createtime"=>array(array("elt",strtotime(date('Y-m-d',strtotime('+1 day')))),array("egt",strtotime(date('Y-m-d'))))))->sum("money");	//今日充值
		
      if($more["chongzhi"]==""){$more["chongzhi"]="0.00";}
      if($more["jinchongzhi"]==""){$more["jinchongzhi"]="0.00";}
      if($more["changbi"]==""){$more["changbi"]="0.00";}
      if($more["jinchangbi"]==""){$more["jinchangbi"]="0.00";}
      
       	
		//系统总共的币
       	$sum = self::$Member->sum('hasmoney');
     
       	 $endtime= time();
       	$starttime = strtotime('-15 day') ;
       	$map['createtime'] = array(array('egt',$starttime),array('elt',$endtime)) ;
       	$alist = $this->lists('Finance',$map);
       	$alist = $this->financeFilter($alist);
       	$this->assign('alist',$alist);
        $this->assign('sum',$sum);
        $this->assign('info',$info);
      	$this->assign('more',$more);
        $this->assign('bonusRule',get_bonus_rule());
        $this->meta_title = '管理首页';
        $this->display();
    }
    
    private  function financeFilter($list){
    	foreach ($list as &$v){
    		$v['surplus'] = sprintf("%0.2f", $v['income']-$v['expend']);
    		if($v['income'] == 0){
    			$v['outrate'] = 100.00;
    		}else{
    			$v['outrate'] = round($v['expend']/$v['income'],4)*100;
    		}
    	}
    	return $list;
    }

}
