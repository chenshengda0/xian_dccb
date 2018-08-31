<?php
namespace Common\Api;
use User\Api\UserApi;
//use Common\Api\UpdateApi;

final class ChangeApi
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

    /**
     * 奖金测试
     */
    public  function test()
    {
        echo PROJECTNUMBER;
    }

    /**
     * 每日分红(定时)
     */
    public function fh(){
        $member=D("member")->where(array("status"=>1,"shenpi"=>3,"hasbill"=>array("gt",0)))->select();
        $bonusRule=get_bonus_rule();

        foreach($member as $a){

            if ($a["hasbill"]>0&&$a["hasbill"]<$bonusRule["chia"]){

                //分红
                $jj=$a["hasbill"]*$bonusRule["chiaa"]*0.01;
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("hasjifen",$jj);
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("alljifen",$jj);
                self::bonusCount(7, $a, $jj, $a['hascp'], 0, 0);
                $type = array('recordtype' => 1, 'changetype' => 7, 'moneytype' => 4);
                $money = array('money' => $jj, 'hasmoney' => $a['hascp'], 'taxmoney' => 0);
                money_change($type, $a, get_com(), $money);



            }elseif($a["hasbill"]>=$bonusRule["chia"]&&$a["hasbill"]<$bonusRule["chib"]){

                $jj=$a["hasbill"]*$bonusRule["chiab"]*0.01;

                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("hasjifen",$jj);
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("alljifen",$jj);
                self::bonusCount(7, $a, $jj, $a['hascp'], 0, 0);
                $type = array('recordtype' => 1, 'changetype' => 7, 'moneytype' => 4);
                $money = array('money' => $jj, 'hasmoney' => $a['hascp'], 'taxmoney' => 0);
                money_change($type, $a, get_com(), $money);

            }elseif($a["hasbill"]>=$bonusRule["chib"]&&$a["hasbill"]<$bonusRule["chic"]){

                $jj=$a["hasbill"]*$bonusRule["chiac"]*0.01;

                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("hasjifen",$jj);
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("alljifen",$jj);
                self::bonusCount(7, $a, $jj, $a['hascp'], 0, 0);
                $type = array('recordtype' => 1, 'changetype' => 7, 'moneytype' => 4);
                $money = array('money' => $jj, 'hasmoney' => $a['hascp'], 'taxmoney' => 0);
                money_change($type, $a, get_com(), $money);




            }elseif($a["hasbill"]>=$bonusRule["chic"]&&$a["hasbill"]<$bonusRule["chid"]){

                $jj=$a["hasbill"]*$bonusRule["chiad"]*0.01;
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("hasjifen",$jj);
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("alljifen",$jj);
                self::bonusCount(7, $a, $jj, $a['hascp'], 0, 0);
                $type = array('recordtype' => 1, 'changetype' => 7, 'moneytype' => 4);
                $money = array('money' => $jj, 'hasmoney' => $a['hascp'], 'taxmoney' => 0);
                money_change($type, $a, get_com(), $money);


            }elseif($a["hasbill"]>=$bonusRule["chid"]){

              if($a["hasbill"]>$bonusRule["chie"]){
              		$a["hasbill"]=$bonusRule["chie"];
              }else{
              		$a["hasbill"]=$a["hasbill"];
              }
              
                $jj=$a["hasbill"]*$bonusRule["chiae"]*0.01;
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("hasjifen",$jj);
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("alljifen",$jj);
                self::bonusCount(7, $a, $jj, $a['hascp'], 0, 0);

                //计算生成的孵化仓数量


                $type = array('recordtype' => 1, 'changetype' => 7, 'moneytype' => 4);
                $money = array('money' => $jj, 'hasmoney' => $a['hascp'], 'taxmoney' => 0);
                money_change($type, $a, get_com(), $money);



            }



        }

    }
    //孵化仓生息
    public function fhc(){
        $member=D("member")->where(array("status"=>1,"shenpi"=>3,"hasfh"=>array("gt",0)))->select();

        $bonusRule=get_bonus_rule();

        foreach($member as $a){

            $jj=$a["hasfh"]*$bonusRule["flx"]*0.01;
          	$jj=round($jj,3);
            D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("hassf",$jj);
            D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("allsf",$jj);
            self::bonusCount(6, $a, $jj, $a['hassf'], 0, 0);
            $type = array('recordtype' => 1, 'changetype' => 17, 'moneytype' => 6);
            $money = array('money' => $jj, 'hasmoney' => $a['hassf'], 'taxmoney' => 0);
            money_change($type, $a, get_com(), $money);

        }
    }
  
  
    //孵化仓释放
    public function fhsf(){
        $member=D("moneyChange")->where(array("moneytype"=>5,"cp_money"=>0))->select();
     
        foreach($member as $a){
          $strtime=$a["createtime"];
          $endtime=time();
          $sc=($strtime-$endtime)/60/60/24;//时差 
          if($sc>=100){
          	$user=D("member")->where(array("uid"=>$a["targetuserid"]))->find();
           	
            D("member")->where(array("uid"=>$a["targetuserid"]))->setInc("hassf",$a["money"]);
           
            self::bonusCount(6, $user, $a["money"], $user['hassf'], 0, 0);
            $type = array('recordtype' => 1, 'changetype' => 18, 'moneytype' =>6);
            $money = array('money' =>$a["money"], 'hasmoney' => $user['hassf'], 'taxmoney' => 0);
            money_change($type, $user, get_com(), $money);
          }
        }
    }                                  
  


    //代数奖
    //奖金
    public function daishu(){

        $uinfo=D("member")->where(array("status"=>1,"shenpi"=>3,"hasbill"=>array("gt",0)))->select();
	
        foreach($uinfo as $user){

            $bonusRule=get_bonus_rule();
            $userrank=$this->level($user); //最下级用户等级

           		$map["createtime"]=array(array("egt",strtotime(date('Y-m-d'))),array("elt",strtotime(date('Y-m-d',strtotime('+1 day')))));
                $map["targetuserid"]=$user["uid"];
                $map["changetype"]=7;
                $moenyold=D("moneyChange")->where($map)->getField("money");

            $num=$user["tdeep"]+$userrank[2];
            $member=D("member")->where(array("status"=>1,"shenpi"=>3,"tuijianids"=>array("like","%,".$user['uid'].",%"),"tdeep"=>array(array("gt",$user["tdeep"]),array("elt",$num)),"hasbill"=>array("egt",0)))->select();		//
		
            foreach($member as $a){

                $map["createtime"]=array(array("egt",strtotime(date('Y-m-d'))),array("elt",strtotime(date('Y-m-d',strtotime('+1 day')))));
                $map["targetuserid"]=$a["uid"];
                $map["changetype"]=7;
                $moeny=D("moneyChange")->where($map)->getField("money");

					if($moeny>=$moenyold){
                    	$mon=$moenyold;
                    }else{
                    	$mon=$moeny["money"];
                    }
                	$fhc=$mon*$userrank[3]*$bonusRule["flv"]*0.01;
                    $jj=$mon*$userrank[3]*(1-$bonusRule["flv"]*0.01);

                    $ob["hascp"]=$user["hascp"]+$jj;
                    $ob["allcp"]=$user["allcp"]+$jj;
                    $ob["hasfh"]=$user["hasfh"]+$fhc;
                    $ob["allfh"]=$user["allfh"]+$fhc;

                   D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$user["uid"]))->save($ob);

                    self::bonusCount(6, $user, $jj, $user['hascp'], 0, 0);
            		self::bonusCount(6, $user, $fhc, $user['hasfh'], 0, 0);

                    unset($money);

                    $type = array('recordtype' => 1, 'changetype' => 6, 'moneytype' => 3);
                    $money = array('money' => $jj, 'hasmoney' => $user['hascp'], 'taxmoney' => 0);

                    money_change($type, $user, $a, $money);

            		unset($money,$type);

                    $type = array('recordtype' => 1, 'changetype' => 6, 'moneytype' => 5);
                    $money = array('money' => $fhc, 'hasmoney' => $user['hasfh'], 'taxmoney' => 0);

                    money_change($type, $user, $a, $money);


            }

        }
    }

    /**
     * 团队奖(日)
     */
    public function team(){
        $member=D("member")->where(array("status"=>1,"shenpi"=>3,"hasbill"=>array("gt",0)))->select();
        $bonusRule=get_bonus_rule();
        foreach ($member as $a){

            $count=D("member")->where(array("tuijianid"=>$a["uid"],"status"=>1,"shenpi"=>3))->count();
            $count4=$this->DQ($a);
            if($count>0&&$count4[0]>0){
                $money=$count4[0];

                if ($count>=$bonusRule["zta"]&&$money>$bonusRule["ztaa"]){
					
                    $ll=$bonusRule["ztba"];
                }elseif ($count>=$bonusRule["ztb"]&&$money>$bonusRule["ztab"]){

                    $ll=$bonusRule["ztbb"];
                }elseif ($count>=$bonusRule["ztc"]&&$money>$bonusRule["ztac"]){

                    $ll=$bonusRule["ztbc"];
                }elseif ($count>=$bonusRule["ztd"]&&$money>$bonusRule["ztad"]){

                    $ll=$bonusRule["ztbd"];
                }elseif ($count>=$bonusRule["zte"]&&$money>$bonusRule["ztae"]){

                    $ll=$bonusRule["ztbe"];
                }elseif ($count>=$bonusRule["ztf"]&&$money>$bonusRule["ztaf"]){

                    $ll=$bonusRule["ztbf"];
                }else{

                    $ll=0;
                }
				
                if( $ll>0){
                    $fhc=$count4[1]*$ll*0.01*$bonusRule["flv"]*0.01;//孵化仓
                    $jj=$count4[1]*$ll*0.01*(1-$bonusRule["flv"]*0.01);//所得奖金

                    $ob["hascp"]=$a["hascp"]+$jj;
                    $ob["allcp"]=$a["allcp"]+$jj;
                    $ob["hasfh"]=$a["hasfh"]+$fhc;
                    $ob["allfh"]=$a["allfh"]+$fhc;


                    D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->save($ob);

                    self::bonusCount(6, $a, $jj, $a['hascp'], 0, 0);
                    self::bonusCount(6, $a, $fhc, $a['hasfh'], 0, 0);
                    unset($money);
                    $type = array('recordtype' => 1, 'changetype' => 1, 'moneytype' => 3);
                    $money = array('money' => $jj, 'hasmoney' => $a['hascp'], 'taxmoney' => 0);
                    money_change($type, $a, get_com(), $money);
                    unset($money,$type);
                    $type = array('recordtype' => 1, 'changetype' => 1, 'moneytype' => 5);
                    $money = array('money' => $fhc, 'hasmoney' => $a['hasfh'], 'taxmoney' => 0);
                    money_change($type, $a, get_com(), $money);
                }

            }

        }
    }


    //去掉最大区
    public function DQ($id){
        $userinfo=D("member")->where(array("tuijianids"=>array("like","%,".$id["uid"].",%"),"tdeep"=>$id["tdeep"]+1))->select();
        $o="";
        foreach($userinfo as $a){
            $hasbill=D("member")->where(array("tuijianids"=>array("like","%,".$a["uid"].",%")))->sum("hasbill");
            $o[$a['uid']]=$hasbill+$a["hasbill"];
        }
        $ben=array_pop($o);//去掉最大值
     
        $sum=array_sum($o);//求和
      	$all=$sum+$ben;
      	//dump($sum);dump($all);die;
      	$ao=array("0"=>$sum,"1"=>$all);
        return $ao;


    }




    /**
     * 懒人奖(日)
     */
    public function zzz(){
        $member=D("member")->where(array("status"=>1,"shenpi"=>3,"hasbill"=>array("gt",0)))->select();
        $bonusRule=get_bonus_rule();
        foreach ($member as $a){
            $userinfo=D("member")->where(array("uid"=>$a["tuijianid"],"status"=>1,"shenpi"=>3))->find();
            $some=D("member")->where(array("tuijianid"=>$userinfo["uid"],"hasbill"=>array("gt",0)))->count();
          //上代
            $strtime=strtotime(date('Y-m-d',time()));
            $endtime=strtotime(date('Y-m-d',strtotime('+1 day')));
            $map["createtime"]=array(array("egt",$strtime),array("elt",$endtime));
            $map["changetype"]=7;
            $map["targetuserid"]=$userinfo["uid"];
            $money=D("moneyChange")->where($map)->find();

            $mad["createtime"]=array(array("egt",$strtime),array("elt",$endtime));
            $mad["changetype"]=7;
            $mad["targetuserid"]=$a["uid"];
            $mon=D("moneyChange")->where($mad)->find();

            if($money!=""){
              if($mon["money"]>$money["money"]){
              	$jq=$money["money"];
              }else{
                $jq=$mon["money"];
              }
              
              
                $fhc=$jq/$some*$bonusRule["zzz"]*0.01*$bonusRule["flv"]*0.01;//孵化仓
                $jj=$jq/$some*$bonusRule["zzz"]*0.01*(1-$bonusRule["flv"]*0.01);//所得奖金

                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"],"hasbill"=>array("gt",0)))->setInc("hascp",$jj);
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"],"hasbill"=>array("gt",0)))->setInc("allcp",$jj);

                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"],"hasbill"=>array("gt",0)))->setInc("hasfh",$fhc);
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"],"hasbill"=>array("gt",0)))->setInc("allfh",$fhc);
                self::bonusCount(5, $a, $jj, $a['hascp'], 0, 0);
                self::bonusCount(5, $a, $fhc, $a['hasjifen'], 0, 0);
                unset($money);
                $type = array('recordtype' => 1, 'changetype' => 5, 'moneytype' => 3);
                $money = array('money' => $jj, 'hasmoney' => $a['hascp'], 'taxmoney' => 0);
                money_change($type, $a, $userinfo, $money);
                unset($money);
                $type = array('recordtype' => 1, 'changetype' => 5, 'moneytype' => 5);
                $money = array('money' => $fhc, 'hasmoney' => $a['hasfh'], 'taxmoney' => 0);
                money_change($type, $a, $userinfo, $money);
            }

        }
    }


    /**
     *	币价升值
     *
     */
    public function moneyup(){
        $yj=get_bonus_rule("eve_price");	//原价
        $zz=get_bonus_rule("zizeng");		//自增
        if($zz>0){
            $map["eve_price"]=$yj+$zz;
            D("bonusRule")->where(array("id"=>1))->save($map);
        }
    }

    /**
     *	清空每日签到
     *
     */
    public function qd(){
        $userinfo=D("member")->where(array("status"=>1))->select();
        $map["is_qd"]=0;
        foreach($userinfo as $a){
           D("member")->where(array("uid"=>$a["uid"]))->save($map);

        }

    }


    /**
     * 判断用户等级
     */
    public function level($a){
        $bonusRule=get_bonus_rule();
      
            if ($a["hasbill"]>0&&$a["hasbill"]<=$bonusRule["chia"]){
                $uplevel=array(1,$bonusRule["chiaa"]*0.01,$bonusRule["chiba"],$bonusRule["chica"]*0.01);

            }elseif($a["hasbill"]>$bonusRule["chia"]&&$a["hasbill"]<=$bonusRule["chib"]){
                $uplevel=array(2,$bonusRule["chiab"]*0.01,$bonusRule["chibb"],$bonusRule["chicb"]*0.01);

            }elseif($a["hasbill"]>$bonusRule["chib"]&&$a["hasbill"]<=$bonusRule["chic"]){
                $uplevel=array(3,$bonusRule["chiac"]*0.01,$bonusRule["chibc"],$bonusRule["chicc"]*0.01);

            }elseif($a["hasbill"]>$bonusRule["chic"]&&$a["hasbill"]<=$bonusRule["chid"]){
                $uplevel=array(4,$bonusRule["chiad"]*0.01,$bonusRule["chibd"],$bonusRule["chicd"]*0.01);

            }elseif($a["hasbill"]>$bonusRule["chid"]&&$a["hasbill"]<=$bonusRule["chie"]){
                $uplevel=array(5,$bonusRule["chiae"]*0.01,$bonusRule["chibe"],$bonusRule["chice"]*0.01);
            }
     
        return $uplevel;
    }


    /**
     * 奖金统计
     * @param int $bonustype 奖金类型
     * @param array $touser  获得奖金的用户信息
     * @param double $money 应得奖金金额
     * @param double $hasmoney 应得奖金金额
     * @param double $taxmoney 奖金税额
     */
    public  function bonusCount($bonustype,$touser,$money,$hasmoney,$cp_money,$tax_money)
    {

        $BCount = M("BonusCount");
        $count_date=strtotime('today');

        $data=array(
            'touserid'=>$touser['uid'],
            'tousernumber'=>$touser['usernumber'],
            'count_date'=>$count_date,
        );

        $map['touserid'] = $touser['uid'];
        $map['count_date'] = $count_date;

        $count=$BCount->where($map)->find();

        $field = 'bonus'.$bonustype;
        if(!empty($count)){
            $data[$field] = $count[$field]+$money ;
            $data['cp_money']=$count['cp_money']+$cp_money;
            $data['tax_money']=$count['tax_money']+$tax_money;
            $data['total']=$count['total']+$hasmoney;
            $BCount->where($map)->save($data);
        }else{
            $data[$field]=$money;
            $data['cp_money']=$cp_money;
            $data['tax_money']=$tax_money;
            $data['total']=$hasmoney;
            $BCount->add($data);
        }

        self::bonusTotal($bonustype, $touser, $money,$hasmoney,$cp_money,$tax_money);
    }

    /**
     * 会员奖金累计
     * @param int $bonustype 奖金类型
     * @param array $touser  获得奖金的用户信息
     * @param double $money 奖金金额
     * @param double $hasmoney 应得奖金金额
     * @param double $taxmoney 奖金税额
     */
    private function bonusTotal($bonustype,$touser,$money,$hasmoney,$cp_money,$tax_money)
    {

        $BCount = M("BonusTotal");
        $data=array(
            'touserid'=>$touser['uid'],
            'tousernumber'=>$touser['usernumber'],
            'count_date'=>time(),//更新时间
        );
        $map['touserid'] = $touser['uid'];
        $count=$BCount->where($map)->find();
        $field = 'bonus'.$bonustype;
        if(!empty($count)){
            $data[$field] = $count[$field]+$money ;
            $data['cp_money']=$count['cp_money']+$cp_money;
            $data['tax_money']=$count['tax_money']+$tax_money;
            $data['total']=$count['total']+$hasmoney;
            $BCount->where($map)->save($data);
        }else{
            $data[$field]=$money;
            $data['total']=$hasmoney;
            $data['cp_money']=$cp_money;
            $data['tax_money']=$tax_money;
            $BCount->add($data);
        }
    }
   //代数奖
    //奖金
    public function daishu2($id){
	
        $uinfo=D("member")->where(array("uid"=>array("in",$id)))->select();
      
       //$uinfo=D("member")->where(array("uid"=>890))->select();
	  //dump($uinfo);die;
       foreach($uinfo as $user){

            $bonusRule=get_bonus_rule();
            // $dongtai = explode(',', trim($bouns['dt_money'], ','));                //动态奖励规则
            $pids_arr = array_reverse(explode(',', trim($user['tuijianids'], ',')));    //发奖人数

            $userrank=$this->level($user); //当前用户等级
            	//dump($userrank);die;
           		$map["createtime"]=array(array("egt",strtotime(date('Y-m-d'))),array("elt",strtotime(date('Y-m-d',strtotime('+1 day')))));
                $map["targetuserid"]=$user["uid"];
                $map["changetype"]=7;
                $moenyold=D("moneyChange")->where($map)->find();

            $num=$user["tdeep"]+$userrank[2];
            //$lv=array($bonusRule["chica"],$bonusRule["chicb"],$bonusRule["chicc"],$bonusRule["chicd"],$bonusRule["chice"],$bonusRule["chicf"]);

            //dump($num);
            ///dump($user["tdeep"]);
            $member=D("member")->where(array("status"=>1,"shenpi"=>3,"tuijianids"=>array("like","%,".$user['uid'].",%"),"tdeep"=>array(array("gt",$user["tdeep"]),array("elt",$num)),"hasbill"=>array("egt",0)))->select();		//
			
            foreach($member as $a){

                $map["createtime"]=array(array("egt",strtotime(date('Y-m-d'))),array("elt",strtotime(date('Y-m-d',strtotime('+1 day')))));
                $map["targetuserid"]=$a["uid"];
                $map["changetype"]=7;
                $moeny=D("moneyChange")->where($map)->find();
            	//dump($moeny);dump($moenyold);die;
					if($moeny["money"]>=$moenyold["money"]){
                    	$mon=$moenyold["money"];
                    }else{
                    	$mon=$moeny["money"];
                    }
             // dump($user);dump($a);
					
                	$fhc=$mon*$userrank[3]*$bonusRule["flv"]*0.01;
                    $jj=$mon*$userrank[3]*(1-$bonusRule["flv"]*0.01);
					//dump($fhc);dump($jj);die;
                 	D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$user["uid"]))->setInc("hascp",$jj);
                    D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$user["uid"]))->setInc("allcp",$jj);
             		D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$user["uid"]))->setInc("hasfh",$fhc);
                    D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$user["uid"]))->setInc("allfh",$fhc);
                    self::bonusCount(6, $user, $jj, $user['hascp'], 0, 0);
            		self::bonusCount(6, $user, $fhc, $user['hasfh'], 0, 0);

                    unset($money);

                    $type = array('recordtype' => 1, 'changetype' => 6, 'moneytype' => 3);
                    $money = array('money' => $jj, 'hasmoney' => $user['hascp'], 'taxmoney' => 0);

                    money_change($type, $user, $a, $money);

            		unset($money);

                    $type = array('recordtype' => 1, 'changetype' => 6, 'moneytype' => 5);
                    $money = array('money' => $fhc, 'hasmoney' => $user['hasfh'], 'taxmoney' => 0);

                    money_change($type, $user, $a, $money);


            }

        }
    }
  
  
    public function fh2($uid){
        $member=D("member")->where(array("uid"=>array("in",$uid)))->select();
     
        $bonusRule=get_bonus_rule();


        foreach($member as $a){

            if ($a["hasbill"]>0&&$a["hasbill"]<$bonusRule["chia"]){

                //分红
                $jj=$a["hasbill"]*$bonusRule["chiaa"]*0.01;
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("hasjifen",$jj);
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("alljifen",$jj);
                self::bonusCount(7, $a, $jj, $a['hascp'], 0, 0);
                $type = array('recordtype' => 1, 'changetype' => 7, 'moneytype' => 4);
                $money = array('money' => $jj, 'hasmoney' => $a['hascp'], 'taxmoney' => 0);
                money_change($type, $a, get_com(), $money);



            }elseif($a["hasbill"]>=$bonusRule["chia"]&&$a["hasbill"]<$bonusRule["chib"]){

                $jj=$a["hasbill"]*$bonusRule["chiab"]*0.01;

                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("hasjifen",$jj);
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("alljifen",$jj);
                self::bonusCount(7, $a, $jj, $a['hascp'], 0, 0);
                $type = array('recordtype' => 1, 'changetype' => 7, 'moneytype' => 4);
                $money = array('money' => $jj, 'hasmoney' => $a['hascp'], 'taxmoney' => 0);
                money_change($type, $a, get_com(), $money);

            }elseif($a["hasbill"]>=$bonusRule["chib"]&&$a["hasbill"]<$bonusRule["chic"]){

                $jj=$a["hasbill"]*$bonusRule["chiac"]*0.01;

                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("hasjifen",$jj);
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("alljifen",$jj);
                self::bonusCount(7, $a, $jj, $a['hascp'], 0, 0);
                $type = array('recordtype' => 1, 'changetype' => 7, 'moneytype' => 4);
                $money = array('money' => $jj, 'hasmoney' => $a['hascp'], 'taxmoney' => 0);
                money_change($type, $a, get_com(), $money);




            }elseif($a["hasbill"]>=$bonusRule["chic"]&&$a["hasbill"]<$bonusRule["chid"]){

                $jj=$a["hasbill"]*$bonusRule["chiad"]*0.01;
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("hasjifen",$jj);
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("alljifen",$jj);
                self::bonusCount(7, $a, $jj, $a['hascp'], 0, 0);
                $type = array('recordtype' => 1, 'changetype' => 7, 'moneytype' => 4);
                $money = array('money' => $jj, 'hasmoney' => $a['hascp'], 'taxmoney' => 0);
                money_change($type, $a, get_com(), $money);


            }elseif($a["hasbill"]>=$bonusRule["chid"]){

              if($a["hasbill"]>$bonusRule["chie"]){
              		$a["hasbill"]=$bonusRule["chie"];
              }else{
              		$a["hasbill"]=$a["hasbill"];
              }
              
                $jj=$a["hasbill"]*$bonusRule["chiae"]*0.01;
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("hasjifen",$jj);
                D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("alljifen",$jj);
                self::bonusCount(7, $a, $jj, $a['hascp'], 0, 0);
                $type = array('recordtype' => 1, 'changetype' => 7, 'moneytype' => 4);
                $money = array('money' => $jj, 'hasmoney' => $a['hascp'], 'taxmoney' => 0);
                money_change($type, $a, get_com(), $money);



            }else{

                $ote=1;
            }



        }

    }
  
  
  
  
  
  
  
  
  




}