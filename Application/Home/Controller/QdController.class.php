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
        $uid =  is_login();
        $bonusRule=get_bonus_rule("is_qd");
        $userinfo=D('member')->where(array("uid"=>$id))->find();
        //今日签到记录
        $sttime= strtotime(date('Y-m-d'),time());
        $endtime = $sttime + 86400;

        $maptiem['sign_time'] =array('BETWEEN',array($sttime,$endtime));
        $maptiem['uid'] =$uid;
        $sign = M('sign')->where($maptiem)->find();

        if($sign){

            $this->error("您已签到!");

        }else{

            $signdata['uid']=$uid;
            $signdata['sign_time']=time();
            $signdata['status']=1;
            $res=  M('sign')->add($signdata);

            if($res){
                //首次连续三天送币 2018-9-4
                $signwhere['uid'] =
                $signwhere['status'] =
                $sign = M('sign')->where(array('uid'=>$uid,'status'=>2))->find();
                if(!$sign){

                    $sttime= strtotime(date('Y-m-d'),time());
                    $sttimes = $sttime - 172800;
                    $endtime = $sttime + 86400;


                    $where['sign_time'] =array('BETWEEN',array($sttimes,$endtime));
                    $where['uid'] =$uid;
                    $day = M('sign')->where($where)->count();
                    //连续三天
                    if($day == 3){

                        M('sign')->where($signdata)->save(array('status'=>2));
                        $map["hascp"]=$userinfo["hascp"]+$bonusRule;
                        D('member')->where(array("uid"=>$id))->save($map);


                        $type = array('recordtype' => 1, 'changetype' => 13, 'moneytype' => 3);
                        $money = array('money' => $bonusRule, 'hasmoney' => $userinfo['hasbill'], 'taxmoney' => 0);
                        money_change($type,  $userinfo,get_com(), $money);


                    }


                }


                //签到分红
                $fh= new ChangeApi;
                $fh->fh($signdata['uid']);     //今天静态分红
                $fh->managementAward($signdata['uid']);         //团队管理奖
                $fh->leadershipAward($signdata['uid']);         //团队领导奖


                $this->success("签到成功");
            }else{
                $this->error("签到失败");
            }
        }



    }

}