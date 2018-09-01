<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\CommonController;
use Common\Api\ChangeApi;
class QdController extends CommonController{
    public function index(){
        $userinfo=D('member')->where(array("uid"=>is_login()))->find();

        $sttime= strtotime(date('Y-m-d'),time());
        $endtime = $sttime + 86400;

        $maptiem['sign_time'] =array('BETWEEN',array($sttime,$endtime));
        $maptiem['uid'] = is_login();
        $sign = M('sign')->where($maptiem)->find();
        if($sign)
            $is_sign = 0;
        else
            $is_sign =1;

        $this->assign("is_sign",$is_sign);
        $this->assign("logininfo",$userinfo);
        $this->display();
    }
    public function sent(){
        $id=trim(I('userID'));
        $bonusRule=get_bonus_rule("is_qd");
        $userinfo=D('member')->where(array("uid"=>$id))->find();
        //今日签到记录
        $sttime= strtotime(date('Y-m-d'),time());
        $endtime = $sttime + 86400;

        $maptiem['sign_time'] =array('BETWEEN',array($sttime,$endtime));
        $maptiem['uid'] = is_login();
        $sign = M('sign')->where($maptiem)->find();

        if($sign){

            $this->error("您已签到!");

        }else{
            $map["hascp"]=$userinfo["hascp"]+$bonusRule;
            $map["hasmoney"]=$userinfo["hasmoney"]+$bonusRule;
           // $map["is_qd"]=1;
            $res=D('member')->where(array("uid"=>$id))->save($map);

            $signdata['uid']=is_login();
            $signdata['sign_time']=time();
            $signdata['status']=1;
            M('sign')->add($signdata);

            if($res){
                //签到分红
                $fh= new ChangeApi;
                $fh->fh($signdata['uid']);     //今天静态分红
                $fh->managementAward($signdata['uid']);         //团队管理奖
                $fh->leadershipAward($signdata['uid']);         //团队领导奖

                $type = array('recordtype' => 1, 'changetype' => 13, 'moneytype' => 3);
                $money = array('money' => $bonusRule, 'hasmoney' => $userinfo['hasbill'], 'taxmoney' => 0);
                money_change($type,  $userinfo,get_com(), $money);
                $this->success("签到成功");
            }else{
                $this->error("签到失败");
            }
        }



    }

}