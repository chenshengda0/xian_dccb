<?php
class db
{
    public $host = "127.0.0.1";//定义默认连接方式
    public $zhang = "root";//定义默认用户名
    public $mi = "231510622abc";//定义默认的密码
    public $dbname = "ceshi";//定义默认的数据库名

//成员方法   是用来执行sql语句的方法
    public function Query($sql,$type=1)
//两个参数：sql语句，判断返回1查询或是增删改的返回
    {
//造一个连接对象，参数是上面的那四个
        $db = new mysqli($this->host,$this->zhang,$this->mi,$this->dbname);
        $r = $db->query($sql);
        if($type == "1")
        {
            return $r->fetch_all();//查询语句，返回数组.执行sql的返回方式是all，也可以换成row
        }
        else
        {
            return $r;
        }
    }

}
    $rule = "select * from zx_member where status = 1 and shenpi = 3 and hasbill >0";

  $db = new db();
    //每日分红
    $sql = "select * from zx_member where status = 1 and shenpi = 3 and hasbill >0";
    $arr = $db->Query($sql);
    foreach($member as $a){

			$jj=$a["hasbill"]*$bonusRule["chiaa"]*0.01;
          //  D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("hasjifen",$jj);
            //D("member")->where(array("status"=>1,"shenpi"=>3,"uid"=>$a["uid"]))->setInc("alljifen",$jj);
            self::bonusCount(7, $a, $jj, $a['hascp'], 0, 0);
            $type = array('recordtype' => 1, 'changetype' => 7, 'moneytype' => 4);
            $money = array('money' => $jj, 'hasmoney' => $a['hascp'], 'taxmoney' => 0);
            money_change($type, $a, get_com(), $money);

        }

 ?>
