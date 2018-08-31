<?php
namespace Common\Api;
use User\Api\UserApi;
//use Common\Api\UpdateApi;

final class Fh
{
    private static $bonusRule;
    private static $Member;

    /**
     * 初始化
     */
    public function __construct()
    {
        self::$Member = M('Member');
        self::$bonusRule = get_bonus_rule();
    }

    //每日产币
    public function mrcb(){
        //符合的会员
        $member=D("member")->where(array("status"=>1,"shenpi"=>3,"hasbill"=>array("gt",0)))->select();
        $bonusRule=get_bonus_rule();

    }
  




}