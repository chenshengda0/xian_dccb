<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\CommonController;
use Common\Api\RelationApi;
use User\Api\UserApi;
use Common\Api\BonusApi;
class QdController extends CommonController{
    public function index(){
        $userinfo=D('member')->where(array("uid"=>is_login()))->find();
        $this->assign("logininfo",$userinfo);
        $this->display();
    }
    public function sent(){
        $id=trim(I('userID'));
        $bonusRule=get_bonus_rule("is_qd");
        $userinfo=D('member')->where(array("uid"=>$id))->find();
        if($userinfo["is_qd"]==1){

            $this->error("您已签到!");

        }else{
            $map["hascp"]=$userinfo["hasbill"]+$bonusRule;
            $map["is_qd"]=1;
            $res=D('member')->where(array("uid"=>$id))->save($map);

            if($res){
                $type = array('recordtype' => 1, 'changetype' => 13, 'moneytype' => 1);
                $money = array('money' => $bonusRule, 'hasmoney' => $userinfo['hasbill'], 'taxmoney' => 0);
                money_change($type,  $userinfo,get_com(), $money);
                $this->success("签到成功");
            }else{
                $this->error("签到失败");
            }
        }



    }

}